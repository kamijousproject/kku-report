<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th,
    #reportTable td {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: middle;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }

    .wide-column {
        min-width: 250px;
        /* ปรับขนาด column ให้กว้างขึ้น */
        word-break: break-word;
        /* ทำให้ข้อความขึ้นบรรทัดใหม่ได้ */
        white-space: pre-line;
        /* รักษารูปแบบการขึ้นบรรทัด */
        vertical-align: top;
        /* ทำให้ข้อความอยู่ด้านบนของเซลล์ */
        padding: 10px;
        /* เพิ่มช่องว่างด้านใน */
    }

    .wide-column div {
        margin-bottom: 5px;
        /* เพิ่มระยะห่างระหว่างแต่ละรายการ */
    }

    /* กำหนดให้ตารางขยายขนาดเต็มหน้าจอ */
    table {
        width: 100%;
        border-collapse: collapse;
        /* ลบช่องว่างระหว่างเซลล์ */
    }

    /* ทำให้หัวตารางติดอยู่กับด้านบน */
    th {
        position: sticky;
        /* ทำให้ header ติดอยู่กับด้านบน */
        top: 0;
        /* กำหนดให้หัวตารางอยู่ที่ตำแหน่งด้านบน */
        background-color: #fff;
        /* กำหนดพื้นหลังให้กับหัวตาราง */
        z-index: 2;
        /* กำหนด z-index ให้สูงกว่าแถวอื่น ๆ */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        /* เพิ่มเงาให้หัวตาราง */
        padding: 8px;
    }

    /* เพิ่มเงาให้กับแถวหัวตาราง */
    th,
    td {
        height: auto;
        /* ให้ความสูงของเซลล์ปรับอัตโนมัติตามเนื้อหา */
        vertical-align: top;
        /* จัดตำแหน่งเนื้อหาของเซลล์ให้เริ่มต้นจากด้านบน */
        word-wrap: break-word;
        /* หากข้อความยาวเกินจะทำการห่อคำ */
        white-space: normal;
        /* ป้องกันไม่ให้ข้อความยาวในแถวตัดข้าม */
    }


    /* ทำให้ข้อมูลในตารางเลื่อนได้ */
    .table-responsive {
        max-height: 60vh;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        display: block;
    }
</style>
<?php
include('../component/header.php');
include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();

// ฟังก์ชันตัดตัวอักษรออกให้เหลือแค่ตัวเลข
function extractNumericPart($string)
{
    return preg_replace('/\D/', '', $string);
}

// ฟังก์ชันเปรียบเทียบ Fund และ Sub_Plan โดยตัดตัวอักษรออกให้เหลือแค่ตัวเลข
function compareSubPlanAndFund($subPlan1, $subPlan2, $fund1, $fund2)
{
    $subPlan1 = extractNumericPart($subPlan1);
    $subPlan2 = extractNumericPart($subPlan2);
    $fund1 = extractNumericPart($fund1);
    $fund2 = extractNumericPart($fund2);

    return $subPlan1 === $subPlan2 && $fund1 === $fund2;
}

// ฟังก์ชันดึงข้อมูล
function fetchBudgetData($conn, $fund)
{
    $query = "SELECT DISTINCT
    ksp.ksp_id AS Ksp_id,
    ksp.ksp_name AS Ksp_Name,
    
    acc.type,
    acc.sub_type,
    project.project_name,
    bpanbp.Account,
    bpanbp.Fund,  -- คืนค่าเดิม
    CONCAT('FN', bpa.FUND) AS FUND,  -- เพิ่ม 'FN' นำหน้า
    bpanbp.Faculty,
    bpanbp.Plan,
    bpanbp.Sub_Plan,  -- คืนค่าเดิม
    CONCAT('SP_', bpa.SUBPLAN) AS SUBPLAN,  -- เพิ่ม 'SP_' นำหน้า
    bpanbp.Reason AS Reason,
    bpanbp.Project,
    bpanbp.KKU_Item_Name,
    bpanbp.Allocated_Total_Amount_Quantity,
    bpa.FISCAL_YEAR,
    bpa.TOTAL_BUDGET,
    bpa.TOTAL_CONSUMPTION,
    bpa.EXPENDITURES,
    bpa.COMMITMENTS,
    bpa.OBLIGATIONS,
    f.Alias_Default AS Faculty_Name,
    p.plan_name AS Plan_Name,
    sp.sub_plan_name AS Sub_Plan_Name,
    pr.project_name AS Project_Name
FROM
    budget_planning_allocated_annual_budget_plan bpanbp
    LEFT JOIN budget_planning_actual bpa ON 
        bpanbp.Fund = CONCAT('FN', bpa.FUND)  -- เปรียบเทียบแบบเพิ่ม 'FN' ให้ bpa.FUND
        AND bpanbp.Faculty = bpa.FACULTY
        AND bpanbp.Plan = bpa.PLAN
        AND bpanbp.Sub_Plan = CONCAT('SP_', bpa.SUBPLAN)  -- เปรียบเทียบแบบเพิ่ม 'SP_' ให้ bpa.SUBPLAN
        AND bpanbp.Project = bpa.PROJECT
    LEFT JOIN budget_planning_annual_budget_plan bpabp ON 
        bpanbp.Faculty = bpabp.Faculty
        AND bpanbp.Plan = bpabp.Plan
        AND bpanbp.Sub_Plan = bpabp.Sub_Plan
        AND bpanbp.Project = bpabp.Project
        AND bpanbp.Fund = bpabp.Fund
    LEFT JOIN budget_planning_project_kpi bppk ON bpanbp.Project = bppk.Project
    LEFT JOIN project ON bpanbp.Project = project.project_id
    LEFT JOIN ksp ON bppk.KKU_Strategic_Plan_LOV = ksp.ksp_id
    LEFT JOIN account acc ON bpanbp.Account = acc.account
    LEFT JOIN Faculty AS f ON bpanbp.Faculty = f.Faculty
    LEFT JOIN plan AS p ON bpanbp.Plan = p.plan_id
    LEFT JOIN sub_plan AS sp ON bpanbp.Sub_Plan = sp.sub_plan_id
    LEFT JOIN project AS pr ON bpanbp.Project = pr.project_id;

    WHERE bpanbp.Fund = :fund";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':fund', $fund);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch results for FN02, FN06, and FN08
$resultsFN02 = fetchBudgetData($conn, 'FN02');
$resultsFN06 = fetchBudgetData($conn, 'FN06');
$resultsFN08 = fetchBudgetData($conn, 'FN08');

$mergedData = [];

foreach ($resultsFN06 as $fn06) {
    // Matching FN02 and FN08 with FN06
    $fn02Match = array_filter($resultsFN02, function ($fn02) use ($fn06) {
        return compareSubPlanAndFund($fn06['Sub_Plan'], $fn02['Sub_Plan'], $fn06['Fund'], $fn02['Fund']) &&
            (string) ($fn06['Plan'] ?? '') === (string) ($fn02['Plan'] ?? '') &&
            (string) ($fn06['Project'] ?? '') === (string) ($fn02['Project'] ?? '');
    });
    $fn08Match = array_filter($resultsFN08, function ($fn08) use ($fn06) {
        return compareSubPlanAndFund($fn06['Sub_Plan'], $fn08['Sub_Plan'], $fn06['Fund'], $fn08['Fund']) &&
            (string) ($fn06['Plan'] ?? '') === (string) ($fn08['Plan'] ?? '') &&
            (string) ($fn06['Project'] ?? '') === (string) ($fn08['Project'] ?? '');
    });

    $fn02 = reset($fn02Match);
    $fn08 = reset($fn08Match);

    // Handle Commitments and Expenditures
    $commitment_FN06 = ($fn06['COMMITMENTS'] ?? 0) + ($fn06['OBLIGATIONS'] ?? 0);
    $commitment_FN02 = ($fn02['COMMITMENTS'] ?? 0) + ($fn02['OBLIGATIONS'] ?? 0);
    $commitment_FN08 = ($fn08['COMMITMENTS'] ?? 0) + ($fn08['OBLIGATIONS'] ?? 0);

    // Calculate Total
    $Total_Allocated = ($fn06['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn08['Allocated_Total_Amount_Quantity'] ?? 0);
    $Total_Commitments = $commitment_FN06 + $commitment_FN02 + $commitment_FN08;

    $mergedData[] = [
        'Account' => $fn06['Account'] ?? '-',
        'Ksp_id' => $fn06['Ksp_id'] ?? '-',
        'Ksp_Name' => $fn06['Ksp_Name'] ?? '-',
        'Plan' => $fn06['Plan'] ?? '',
        'Sub_Plan' => $fn06['Sub_Plan'] ?? '',
        'Reason' => $fn06['Reason'] ?? '',
        'Plan_Name' => $fn06['Plan_Name'] ?? '',
        'Sub_Plan_Name' => $fn06['Sub_Plan_Name'] ?? '',
        'Type' => $fn06['type'] ?? '',
        'Sub_Type' => $fn06['sub_type'] ?? '',
        'Project_Name' => $fn06['Project_Name'] ?? '',
        'KKU_Item_Name' => $fn06['KKU_Item_Name'] ?? '',
        'Allocated_FN06' => $fn06['Allocated_Total_Amount_Quantity'] ?? 0,
        'Commitments_FN06' => $commitment_FN06,
        'Expenditures_FN06' => $fn06['EXPENDITURES'] ?? 0,
        'Allocated_FN02' => $fn02['Allocated_Total_Amount_Quantity'] ?? 0,
        'Commitments_FN02' => $commitment_FN02,
        'Expenditures_FN02' => $fn02['EXPENDITURES'] ?? 0,
        'Allocated_FN08' => $fn08['Allocated_Total_Amount_Quantity'] ?? 0,
        'Commitments_FN08' => $commitment_FN08,
        'Expenditures_FN08' => $fn08['EXPENDITURES'] ?? 0,
        'Total_Allocated' => $Total_Allocated,
        'Total_Commitments' => $Total_Commitments,
    ];
}

// สร้างตัวแปรเก็บจำนวนแถวที่ต้อง merge
$rowspanData = [];

foreach ($mergedData as $row) {
    $type = $row['Type'] ?? '';
    $subType = $row['Sub_Type'] ?? '';

    if (!isset($rowspanData[$type][$subType])) {
        $rowspanData[$type][$subType] = 1;
    } else {
        $rowspanData[$type][$subType]++;
    }
}

// ใช้ตัวแปรนี้เพื่อติดตามแถวที่ถูก merge ไปแล้ว
$usedRowspan = [];

// เริ่มสร้างตาราง HTML
?>



<!DOCTYPE html>
<html lang="en">

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
                        <h4>รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">
                                รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว
                                        ประเภทที่ยังไม่มีหนี้</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="4">ปี 2568 (ปีที่ขอตั้ง)</th>
                                                <th colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                                <th>รวม</th>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                                <th>รวม</th>
                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            // ตัวแปรเก็บค่าของแถวก่อนหน้า
                                            $previousAccount = "";
                                            $previousType = "";
                                            $previousSubType = "";
                                            $previousKKU_Item_Name = "";  // เก็บค่าของ KKU_Item_Name
                                            
                                            // วนลูปแสดงข้อมูลที่รวมกัน
                                            foreach ($mergedData as $row) {
                                                // คำนวณ Total_Allocated สำหรับแต่ละแถว
                                                $Allocated_FN06 = $row['Allocated_FN06'] ?? 0;
                                                $Allocated_FN02 = $row['Allocated_FN02'] ?? 0;
                                                $Allocated_FN08 = $row['Allocated_FN08'] ?? 0;
                                                $Total_Allocated = $Allocated_FN06 + $Allocated_FN02 + $Allocated_FN08;  // เพิ่ม Allocated_FN08 ในการคำนวณ Total_Allocated
                                                $Allocated_FN08Yead00 = 0;
                                                $Total = $Total_Allocated - $Allocated_FN08Yead00;
                                                // คำนวณ Percentage Change (เพิ่มหรือลด)
                                                $Percentage_Change = 0;

                                                // ตรวจสอบว่าค่า Allocated_FN02 เป็น 0 หรือไม่
                                            
                                                // คำนวณ Percentage Change (เพิ่มหรือลด)
                                                if ($Allocated_FN08Yead00 == 0) {
                                                    $Percentage_Change = 100; // กรณีที่ค่าเป็น 0 ให้ถือว่าเป็น 100%
                                                } else {
                                                    $Percentage_Change = (($Allocated_FN06 - $Allocated_FN08Yead00) / $Allocated_FN08Yead00) * 100;
                                                }

                                                echo "<tr>";
                                                echo "<td style='text-align: left;'>";  // เริ่มต้น <td> สำหรับแสดงข้อมูล
                                            
                                                // เช็คว่า Account ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่
                                                if ($row['Account'] != $previousAccount) {
                                                    $previousAccount = $row['Account'];  // เก็บค่าปัจจุบันของ Account
                                                }

                                                // เช็คว่า Type ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่
                                                if ($row['Type'] != $previousType) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 5) . "{$row['Type']}</strong><br>";
                                                    $previousType = $row['Type'];  // เก็บค่าปัจจุบันของ Type
                                                }

                                                // เช็คว่า Sub_Type ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่
                                                if ($row['Sub_Type'] != $previousSubType) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 10) . "{$row['Sub_Type']}</strong><br>";
                                                    $previousSubType = $row['Sub_Type'];  // เก็บค่าปัจจุบันของ Sub_Type
                                                }

                                                // เช็คว่า KKU_Item_Name ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่

                                                    echo "<strong>" . str_repeat('&nbsp;', 15) . implode(' ', array_slice(explode(' ', $row['KKU_Item_Name']), 0, 1)) . "</strong>";



                                                // แสดงข้อมูลเพิ่มเติมในแต่ละช่อง
                                                echo "<td>0</td>";  // ปี 2567 เงินอุดหนุนจากรัฐ
                                                echo "<td>0</td>";  // ปี 2567 เงินนอกงบประมาณ
                                                echo "<td>0</td>";  // ปี 2567 เงินรายได้
                                                echo "<td>0</td>";  // ปี 2567 รวม
                                                echo "</td>";  // ปิด <td>
                                            
                                                // แสดง Allocated_FN06, Allocated_FN08, Allocated_FN02
                                                echo "<td>" . (isset($row['Allocated_FN06']) ? $row['Allocated_FN06'] : '-') . "</td>";
                                                echo "<td>" . (isset($row['Allocated_FN08']) ? $row['Allocated_FN08'] : '-') . "</td>";
                                                echo "<td>" . (isset($row['Allocated_FN02']) ? $row['Allocated_FN02'] : '-') . "</td>";

                                                // แสดง Total_Allocated
                                                echo "<td>" . $Total_Allocated . "</td>";  // ปี 2568 รวม
                                            
                                                // แสดงเพิ่ม/ลด จำนวน
                                                echo "<td>" . $Total . "</td>";
                                                // แสดงเพิ่ม/ลด ร้อยละ
                                                echo "<td>" . sprintf("%.2f", $Percentage_Change) . "%</td>";
                                                // เพิ่ม/ลด ร้อยละ
                                                echo "</tr>";  // ปิด <tr>
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
            const rows = [];
            const table = document.getElementById('reportTable');

            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const row = table.rows[rowIndex];
                const cells = Array.from(row.cells).map((cell, index) => {
                    let text = cell.innerText.trim();

                    // เช็คว่าเป็นตัวเลข float (ไม่มี , ในหน้าเว็บ)
                    if (!isNaN(text) && text !== "") {
                        text = `"${parseFloat(text).toLocaleString("en-US", { minimumFractionDigits: 2 })}"`;
                    }

                    return text;
                });

                // ขยับเฉพาะแถวที่ 2 (rowIndex === 1) ไป 1 คอลัมน์
                if (rowIndex === 1) {  // rowIndex === 1 คือแถวที่ 2
                    cells.splice(0, 0, "");  // เพิ่มช่องว่างในคอลัมน์แรก
                }

                // ขยับแค่แถวที่ 2 เท่านั้น
                rows.push(cells.join(",")); // ใช้ , เป็นตัวคั่น CSV
            }

            const csvContent = "\uFEFF" + rows.join("\n"); // ป้องกัน Encoding เพี้ยน
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }



        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape');

            // เพิ่มฟอนต์ภาษาไทย
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal); // ใช้ตัวแปรที่ได้จากไฟล์
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");

            // ตั้งค่าฟอนต์และข้อความ
            doc.setFontSize(12);
            doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

            // ใช้ autoTable สำหรับสร้างตาราง
            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                styles: {
                    font: "THSarabun", // ใช้ฟอนต์ที่รองรับภาษาไทย
                    fontSize: 10,
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
                bodyStyles: {
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
                headStyles: {
                    fillColor: [102, 153, 225], // สีพื้นหลังของหัวตาราง
                    textColor: [0, 0, 0], // สีข้อความในหัวตาราง
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
            });

            // บันทึกไฟล์ PDF
            doc.save('รายงาน.pdf');
        }

        function exportXLSX() {
            const table = document.getElementById('reportTable');
            const rows = [];
            const merges = [];
            const rowSpans = {}; // เก็บค่า rowspan
            const colSpans = {}; // เก็บค่า colspan

            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const row = table.rows[rowIndex];
                const cells = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < row.cells.length; cellIndex++) {
                    let cell = row.cells[cellIndex];
                    let cellText = cell.innerText.trim();

                    // ตรวจสอบว่ามี rowspan หรือ colspan หรือไม่
                    let rowspan = cell.rowSpan || 1;
                    let colspan = cell.colSpan || 1;

                    // หากเป็นเซลล์ที่เคยถูก Merge ข้ามมา ให้ข้ามไป
                    while (rowSpans[`${rowIndex},${colIndex}`]) {
                        cells.push(""); // ใส่ค่าว่างแทน Merge
                        colIndex++;
                    }

                    // เพิ่มค่าลงไปในแถว
                    cells.push(cellText);

                    // ถ้ามี colspan หรือ rowspan
                    if (rowspan > 1 || colspan > 1) {
                        merges.push({
                            s: {
                                r: rowIndex,
                                c: colIndex
                            }, // จุดเริ่มต้นของ Merge
                            e: {
                                r: rowIndex + rowspan - 1,
                                c: colIndex + colspan - 1
                            } // จุดสิ้นสุดของ Merge
                        });

                        // บันทึกตำแหน่งเซลล์ที่ถูก Merge เพื่อกันการซ้ำ
                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (r !== 0 || c !== 0) {
                                    rowSpans[`${rowIndex + r},${colIndex + c}`] = true;
                                }
                            }
                        }
                    }

                    colIndex++;
                }
                rows.push(cells);
            }

            // สร้างไฟล์ Excel
            const XLSX = window.XLSX;
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(rows);

            // ✅ เพิ่ม Merge Cells
            ws['!merges'] = merges;

            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // ✅ ดาวน์โหลดไฟล์ Excel
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'array'
            });
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงาน.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
    <!-- โหลดไลบรารี xlsx จาก CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


</body>

</html>