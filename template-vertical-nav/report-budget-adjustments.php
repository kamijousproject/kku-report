<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: top;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }

    #reportTable td {
        text-align: left;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: top;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }

    #main-wrapper {
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    .content-body {
        flex-grow: 1;
        overflow: hidden;
        /* Prevent body scrolling */
        display: flex;
        flex-direction: column;
    }

    .container {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }


    .table-responsive {
        flex-grow: 1;
        overflow-y: auto;
        /* Scrollable content only inside table */
        max-height: 60vh;
        /* Set a fixed height */
        border: 1px solid #ccc;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead tr:nth-child(1) th {
        position: sticky;
        top: 0;
        background: #f4f4f4;
        z-index: 1000;
    }

    thead tr:nth-child(2) th {
        position: sticky;
        top: 45px;
        /* Adjust height based on previous row */
        background: #f4f4f4;
        z-index: 999;
    }

    thead tr:nth-child(3) th {
        position: sticky;
        top: 90px;
        /* Adjust height based on previous rows */
        background: #f4f4f4;
        z-index: 998;
    }

    .label-faculty {
        font-size: 16px;
        /* ขนาดตัวหนังสือ */
        font-weight: bold;
        /* ตัวหนา */
        color: #333;
        /* สีของตัวหนังสือ */
        margin-bottom: 10px;
        /* เว้นระยะห่างด้านล่าง */
    }
</style>
<?php

include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();


function fetchBudgetData($conn, $faculty = null)
{
    $query = "SELECT 
    bap.id, bap.Faculty,
    bap.Plan,
    ft.Alias_Default AS Faculty_name,
    MAX(p.plan_name) AS plan_name, -- ใช้ MAX() หรือฟังก์ชันการรวมอื่น ๆ
    (SELECT fc.Alias_Default 
     FROM Faculty fc 
     WHERE fc.Faculty = bap.Faculty 
     LIMIT 1) AS Faculty_Name,
     
    bap.Sub_Plan, sp.sub_plan_name,
    bap.Project, pj.project_name,
    bap.`Account`, ac.sub_type,
    bap.KKU_Item_Name,

    -- แยก Total_Amount_Quantity ตามปีจากคอลัมน์ Budget_Management_Year
    SUM(CASE WHEN bap.Budget_Management_Year = 2568 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568,
    SUM(CASE WHEN bap.Budget_Management_Year = 2567 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567,
    SUM(CASE WHEN bap.Budget_Management_Year = 2566 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2566,

    -- แยก TOTAL_BUDGET ตามปีจาก bpa.FISCAL_YEAR
    SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2568 THEN bpa.TOTAL_BUDGET ELSE 0 END) AS TOTAL_BUDGET_2568,
    SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2567 THEN bpa.TOTAL_BUDGET ELSE 0 END) AS TOTAL_BUDGET_2567,
    SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566 THEN bpa.TOTAL_BUDGET ELSE 0 END) AS TOTAL_BUDGET_2566,

    -- หาผลต่างระหว่าง Total_Amount_2568 และ TOTAL_BUDGET_2567
    SUM(CASE WHEN bap.Budget_Management_Year = 2568 THEN bap.Total_Amount_Quantity ELSE 0 END) - 
    COALESCE(SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2567 THEN bpa.TOTAL_BUDGET ELSE 0 END), 0)
    AS Difference_2568_2567,

    -- คำนวณเปอร์เซ็นต์ผลต่าง
    CASE
        WHEN COALESCE(SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2567 THEN bpa.TOTAL_BUDGET ELSE 0 END), 0) = 0
        THEN 100
        ELSE 
            (
                SUM(CASE WHEN bap.Budget_Management_Year = 2568 THEN bap.Total_Amount_Quantity ELSE 0 END) - 
                COALESCE(SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2567 THEN bpa.TOTAL_BUDGET ELSE 0 END), 0)
            ) / 
            NULLIF(COALESCE(SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2567 THEN bpa.TOTAL_BUDGET ELSE 0 END), 0), 0) * 100
    END AS Percentage_Difference_2568_2567,
    bap.Reason

FROM budget_planning_annual_budget_plan bap
LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') ft
ON ft.Faculty = bap.Faculty
LEFT JOIN sub_plan sp ON sp.sub_plan_id = bap.Sub_Plan
LEFT JOIN project pj ON pj.project_id = bap.Project
LEFT JOIN `account` ac ON ac.`account` = bap.`Account`
LEFT JOIN plan p ON p.plan_id = bap.Plan
LEFT JOIN budget_planning_actual bpa
    ON bpa.FACULTY = bap.Faculty
    AND bpa.`ACCOUNT` = bap.`Account`
    AND bpa.SUBPLAN = CAST(SUBSTRING(bap.Sub_Plan, 4) AS UNSIGNED) -- ใช้ SUBSTRING แทน REPLACE เพื่อแปลงเป็นตัวเลข
    AND bpa.PROJECT = bap.Project
    AND bpa.PLAN = bap.Plan
    AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = bap.Budget_Management_Year
    AND bpa.SERVICE = CAST(REPLACE(bap.Service, 'SR_', '') AS UNSIGNED)
    AND bpa.FUND = CAST(REPLACE(bap.Fund, 'FN', '') AS UNSIGNED)
WHERE bap.`Account` LIKE '4%'  
";

    if ($faculty) {
        $query .= " AND bap.Faculty = :faculty"; // กรองตาม Faculty ที่เลือก
    }

    // Remove duplicate GROUP BY
    $query .= " GROUP BY bap.id, bap.Faculty, bap.Sub_Plan, sp.sub_plan_name, 
    bap.Project, pj.project_name, bap.`Account`, ac.sub_type, 
    bap.KKU_Item_Name, ft.Alias_Default
    ORDER BY CAST(SUBSTRING(bap.Sub_Plan, 4) AS UNSIGNED) ASC, pj.project_name ASC";


    $stmt = $conn->prepare($query);

    if ($faculty) {
        $stmt->bindParam(':faculty', $faculty, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



$results = fetchBudgetData($conn);
function fetchFacultyData($conn)
{
    // ดึงข้อมูล Faculty_Name แทน Faculty จากตาราง Faculty
    $query = "SELECT DISTINCT bap.Faculty, ft.Alias_Default AS Faculty_Name
              FROM budget_planning_annual_budget_plan bap
              LEFT JOIN Faculty ft ON ft.Faculty = bap.Faculty";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



?>

<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>

<body class="v-light vertical-nav fix-header fix-sidebar">
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <div id="main-wrapper">
        <?php include('../component/left-nev.php') ?>
        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ</h4>
                                </div>

                                <?php
                                $faculties = fetchFacultyData($conn);  // ดึงข้อมูล Faculty
                                ?>

                                <form method="GET" action="">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="faculty" class="label-faculty" style="margin-right: 10px;">เลือก
                                            ส่วนงาน/หน่วยงาน.</label>
                                        <select name="faculty" id="faculty" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือก ส่วนงาน/หน่วยงาน.</option>
                                            <?php
                                            // แสดง Faculty ที่ดึงมาจากฟังก์ชัน fetchFacultyData
                                            foreach ($faculties as $faculty) {
                                                $facultyName = htmlspecialchars($faculty['Faculty_Name']); // ใช้ Faculty_Name แทน Faculty
                                                $facultyCode = htmlspecialchars($faculty['Faculty']); // ใช้ Faculty รหัสเพื่อส่งไปใน GET
                                                $selected = (isset($_GET['faculty']) && $_GET['faculty'] == $facultyCode) ? 'selected' : '';
                                                echo "<option value=\"$facultyCode\" $selected>$facultyName</option>";
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th rowspan="2">รายรับจริงปี 66</th>
                                                <th colspan="2">ปี 2567</th>
                                                <th rowspan="2">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th colspan="2">เพิ่ม/ลด</th>
                                                <th rowspan="2">คำชี้แจง</th>
                                            </tr>
                                            <tr>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // ตัวแปรเก็บค่าของแถวก่อนหน้า
                                            $previousPlan = "";
                                            $previousSubPlanId = "";
                                            $previousProject = "";
                                            $previousSubType = "";
                                            $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $results = fetchBudgetData($conn, $selectedFaculty);
                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                foreach ($results as $row) {
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left;'>";

                                                    // เช็คและแสดง Plan ถ้าเปลี่ยนแปลง
                                                    if ($row['Plan'] != $previousPlan) {
                                                        echo "<strong>" . htmlspecialchars($row['Plan']) . "</strong> : " . htmlspecialchars($row['plan_name']) . "<br>";
                                                        $previousPlan = $row['Plan'];
                                                        $previousSubPlanId = ""; // รีเซ็ตค่าเมื่อเปลี่ยน Plan
                                                        $previousProject = "";
                                                        $previousSubType = "";
                                                    }

                                                    // เช็คและแสดง Sub Plan ถ้าเปลี่ยนแปลง
                                                    if ($row['Sub_Plan'] != $previousSubPlanId) {
                                                        echo str_repeat("&nbsp;", 4) . "<strong>" . htmlspecialchars($row['Sub_Plan']) . "</strong> : " . htmlspecialchars($row['sub_plan_name']) . "<br>";
                                                        $previousSubPlanId = $row['Sub_Plan'];
                                                        $previousProject = "";
                                                        $previousSubType = "";
                                                    }

                                                    // เช็คและแสดง Project ถ้าเปลี่ยนแปลง
                                                    if ($row['project_name'] != $previousProject) {
                                                        echo str_repeat("&nbsp;", 8) . htmlspecialchars($row['project_name']) . "<br>";
                                                        $previousProject = $row['project_name'];
                                                        $previousSubType = "";
                                                    }

                                                    // เช็คและแสดง Sub Type ถ้าเปลี่ยนแปลง
                                                    if ($row['sub_type'] != $previousSubType) {
                                                        echo str_repeat("&nbsp;", 24) . htmlspecialchars($row['sub_type']) . "<br>";
                                                        $previousSubType = $row['sub_type'];
                                                    }

                                                    // แสดง KKU Item Name เสมอ
// ตรวจสอบว่า KKU_Item_Name มีค่า และไม่เป็นค่าว่างหรือไม่
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? htmlspecialchars($row['KKU_Item_Name'])
                                                        : "ไม่มี ข้อมูล Item_Name";

                                                    // แสดงผล
                                                    echo str_repeat("&nbsp;", 32) . $kkuItemName;

                                                    // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                    echo "<td style='vertical-align: bottom;'>" . (isset($row['Total_Amount_2566']) ? htmlspecialchars($row['Total_Amount_2566']) : "ไม่มีข้อมูล") . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['Total_Amount_2567']) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['TOTAL_BUDGET_2567']) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['Total_Amount_2568']) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['Difference_2568_2567']) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['Percentage_Difference_2568_2567']) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . (isset($row['Reason']) ? htmlspecialchars($row['Reason']) : "ไม่มีข้อมูล") . "</td>";


                                                    echo "</tr>";

                                                }
                                            } else {
                                                echo "<tr><td colspan='8' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                            }

                                            ?>
                                        </tbody>
                                    </table>


                                </div>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLS</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="copyright">
                <p>Copyright &copy; <a href="#">KKU</a> 2025</p>
            </div>
        </div>
    </div>
    <script>

        function exportCSV() {
            const table = document.getElementById('reportTable');
            if (!table) {
                alert("ไม่พบตารางที่ต้องการ Export");
                return;
            }

            // แยกการประมวลผล thead กับ tbody ออกเป็น 2 ส่วน
            const thead = table.querySelector('thead');
            const tbody = table.querySelector('tbody');

            // สร้าง matrix ของส่วน thead
            let headerMatrix = [];
            if (thead) {
                headerMatrix = parseTableSection(thead);
            }

            // สร้าง matrix ของส่วน tbody
            let bodyMatrix = [];
            if (tbody) {
                bodyMatrix = parseTableSection(tbody);
            }

            // รวมทั้งสองส่วนเข้าเป็น CSV
            const csvRows = [];

            // แปลง headerMatrix -> CSV
            headerMatrix.forEach(rowArr => {
                const line = rowArr
                    .map(cell => `"${cell.replace(/"/g, '""')}"`)
                    .join(",");
                csvRows.push(line);
            });

            // แปลง bodyMatrix -> CSV
            bodyMatrix.forEach(rowArr => {
                const line = rowArr
                    .map(cell => `"${cell.replace(/"/g, '""')}"`)
                    .join(",");
                csvRows.push(line);
            });

            // สร้างไฟล์ CSV
            const csvContent = "\uFEFF" + csvRows.join("\n"); // \uFEFF เพื่อให้ Excel รองรับ UTF-8
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'report.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        /**
         * parseTableSection: สร้าง Matrix ตาม rowspan/colspan ของส่วน thead หรือ tbody
         * @param {HTMLElement} section - <thead> หรือ <tbody>
         * @returns {string[][]} 2D array ที่สะท้อนโครงสร้างของตาราง
         */
        function parseTableSection(section) {
            const rows = Array.from(section.rows);

            // หาจำนวนคอลัมน์สูงสุด (maxCols) จากผลรวม colSpan ของแต่ละแถว
            let maxCols = 0;
            rows.forEach(row => {
                let colCount = 0;
                Array.from(row.cells).forEach(cell => {
                    colCount += cell.colSpan;
                });
                if (colCount > maxCols) {
                    maxCols = colCount;
                }
            });

            // สร้าง 2D array เปล่าตามจำนวนแถว x จำนวนคอลัมน์สูงสุด
            const matrix = [];
            for (let i = 0; i < rows.length; i++) {
                matrix.push(new Array(maxCols).fill(""));
            }

            // skipMap เอาไว้ทำเครื่องหมายช่องที่ถูก "จอง" โดย rowspan/colspan แล้ว
            const skipMap = {};

            // วนทีละแถว
            for (let r = 0; r < rows.length; r++) {
                const row = rows[r];
                let c = 0; // ตำแหน่งคอลัมน์ที่จะใส่ข้อมูล

                // วนทีละเซลล์ในแถว
                for (let cellIndex = 0; cellIndex < row.cells.length; cellIndex++) {
                    // ข้ามคอลัมน์ที่ถูกจองไว้ก่อน
                    while (skipMap[`${r},${c}`]) {
                        c++;
                    }

                    const cell = row.cells[cellIndex];
                    const text = cell.innerText.trim();

                    // ใส่ข้อความลงใน matrix
                    matrix[r][c] = text;

                    // เก็บ rowSpan, colSpan
                    const rowSpan = cell.rowSpan;
                    const colSpan = cell.colSpan;

                    // "จอง" ช่อง skipMap ตาม rowSpan, colSpan
                    for (let rr = r; rr < r + rowSpan; rr++) {
                        for (let cc = c; cc < c + colSpan; cc++) {
                            if (rr === r && cc === c) continue; // ช่องต้นฉบับไม่ต้องจองซ้ำ
                            skipMap[`${rr},${cc}`] = true;
                        }
                    }

                    // ขยับตำแหน่ง c ไปข้างหน้าตาม colSpan
                    c += colSpan;
                }
            }

            return matrix;
        }




        function exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape');

            if (window.thsarabunnew_webfont_normal) {
                doc.addFileToVFS("THSarabun.ttf", window.thsarabunnew_webfont_normal);
                doc.addFont("THSarabun.ttf", "THSarabun", "normal");
                doc.setFont("THSarabun");
            }
            doc.setFontSize(14);
            doc.text("รายงานการจัดสรรเงินรายงวด", 10, 10);

            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                styles: { font: "THSarabun", fontSize: 12, lineColor: [0, 0, 0], lineWidth: 0.5 },
                bodyStyles: { lineColor: [0, 0, 0], lineWidth: 0.5 },
                headStyles: { fillColor: [102, 153, 225], textColor: [0, 0, 0], lineColor: [0, 0, 0], lineWidth: 0.5 },
            });

            doc.save('รายงาน.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');
            if (!table) {
                alert("ไม่พบตารางที่ต้องการ Export");
                return;
            }

            // 1) แยก parse ส่วน thead และ tbody ออกเป็น 2 ส่วน
            const { rowsData: headRows, merges: headMerges } = parseSection(table.tHead, 0);
            const { rowsData: bodyRows, merges: bodyMerges } = parseSection(table.tBodies[0], headRows.length);

            // รวม rows ของ thead + tbody
            const allRows = [...headRows, ...bodyRows];
            // รวม merges ของ thead + tbody
            const allMerges = [...headMerges, ...bodyMerges];

            // 2) สร้าง workbook / worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges เข้าไปใน worksheet
            ws['!merges'] = allMerges;

            // 3) บันทึกเป็นไฟล์ .xls
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
            const excelBuffer = XLSX.write(wb, { bookType: 'xls', type: 'array' });
            const blob = new Blob([excelBuffer], { type: 'application/vnd.ms-excel' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'report.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        /**
         * parseSection: ดึงข้อมูลจาก thead/tbody แล้วสร้างทั้ง AoA (Array of Arrays)
         * และข้อมูล merge (rowSpan/colSpan) สำหรับ XLSX
         *
         * @param {HTMLTableSectionElement} section - <thead> หรือ <tbody>
         * @param {number} startRow - เริ่มนับแถวที่เท่าไหร่ (กรณี thead มาก่อน)
         * @return { rowsData, merges }
         *    rowsData: string[][] (AoA) สำหรับแต่ละแถว/คอลัมน์
         *    merges: { s: {r,c}, e: {r,c} }[] สำหรับใส่ ws['!merges']
         */
        function parseSection(section, startRow = 0) {
            if (!section) return { rowsData: [], merges: [] };

            const rows = Array.from(section.rows);

            // หาจำนวนคอลัมน์สูงสุด (maxCols) จากผลรวม colSpan ของแต่ละแถว
            let maxCols = 0;
            rows.forEach(row => {
                let colCount = 0;
                Array.from(row.cells).forEach(cell => {
                    colCount += cell.colSpan || 1;
                });
                if (colCount > maxCols) {
                    maxCols = colCount;
                }
            });

            // สร้าง 2D array เปล่าตามจำนวนแถว x จำนวนคอลัมน์สูงสุด
            const matrix = [];
            for (let i = 0; i < rows.length; i++) {
                matrix.push(new Array(maxCols).fill(""));
            }

            const merges = [];
            const skipMap = {};

            // วนทีละแถว
            for (let r = 0; r < rows.length; r++) {
                const tr = rows[r];
                let c = 0; // ตำแหน่งคอลัมน์ที่จะใส่ข้อมูล

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    // ข้ามคอลัมน์ที่ถูกจองแล้ว (rowSpan/colSpan ก่อนหน้า)
                    while (skipMap[`${r},${c}`]) {
                        c++;
                    }

                    const cell = tr.cells[cellIndex];
                    let text = cell.innerHTML || "";
                    // ลบแท็ก HTML ออก เหลือแต่ข้อความ + เว้นบรรทัด (ถ้าต้องการ)
                    text = text
                        .replace(/<br\s*\/?>/gi, "\n")
                        .replace(/<\/?[^>]+>/g, "")
                        .replace(/&nbsp;/g, " ")
                        .trim();

                    matrix[r][c] = text;

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    // ถ้ามี rowspan หรือ colspan ให้ใส่ merges
                    if (rowspan > 1 || colspan > 1) {
                        merges.push({
                            s: { r: startRow + r, c: c }, // ตำแหน่งเริ่ม (รวม offset ของ startRow)
                            e: { r: startRow + r + rowspan - 1, c: c + colspan - 1 }
                        });

                        // จองช่อง skipMap
                        for (let rr = r; rr < r + rowspan; rr++) {
                            for (let cc = c; cc < c + colspan; cc++) {
                                if (rr === r && cc === c) continue;
                                skipMap[`${rr},${cc}`] = true;
                            }
                        }
                    }

                    c += colspan;
                }
            }

            return {
                rowsData: matrix,
                merges
            };
        }

    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>


    <!-- โหลดไลบรารีที่จำเป็น -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <script src="../js/custom.min.js"></script>


    <!-- โหลดฟอนต์ THSarabun (ตรวจสอบไม่ให้ประกาศซ้ำ) -->
    <script>
        if (typeof window.thsarabunnew_webfont_normal === 'undefined') {
            window.thsarabunnew_webfont_normal = "data:font/truetype;base64,AAEAAA...";
        }
    </script>
</body>

</html>