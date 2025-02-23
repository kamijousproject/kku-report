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
        font-family: 'Arial', sans-serif;
        /* เลือกฟอนต์ที่ต้องการ */
        font-size: 16px;
        /* กำหนดขนาดฟอนต์ให้เท่ากัน */
        line-height: 1.5;
        /* กำหนดระยะห่างระหว่างบรรทัด */

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
</style>
<?php
include('../component/header.php');
include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();

// รับค่าจาก dropdown ถ้าไม่มีให้ใช้ค่าเริ่มต้นเป็น Scenario1
$selectedScenario = isset($_GET['scenario']) ? $_GET['scenario'] : 'Scenario1';

// map ค่า dropdown เป็นค่าในฐานข้อมูล
$scenarioMap = array(
    'Scenario1' => 'ANL-RELEASE-1',
    'Scenario2' => 'ANL-RELEASE-2',
    'Scenario3' => 'ANL-RELEASE-3',
    'Scenario4' => 'ANL-RELEASE-4'
);
$scenarioValue = isset($scenarioMap[$selectedScenario]) ? $scenarioMap[$selectedScenario] : 'ANL-RELEASE-1';

// ฟังก์ชันในการดึงข้อมูลจากฐานข้อมูล
function fetchScenarioData($conn, $scenarioColumnValue)
{
    $query = "SELECT 
    bap.Service,
    bap.Plan,
    p.plan_name,
    bap.Faculty,
    ft.Faculty,
    ft.Alias_Default AS FacultyName,
    bap.Project,
    pj.project_id,
    pj.project_name,
    bap.Sub_Plan,
    sp.sub_plan_id,
    sp.sub_plan_name,
    bap.`Account`,
    
    -- a1: แสดงแค่สองเลขแรกแล้วตามด้วย 00000000
    CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)) AS a1,
    
    -- a2: แสดงแค่สองเลขแรกแล้วตามด้วย 00000000
    CONCAT(LEFT(bap.`Account`, 4), REPEAT('0', 6)) AS a2,
    
    ac.`account`,
    ac.`type`,
    ac.sub_type,
    bap.Fund,
    bap.KKU_Item_Name,
    
    -- หาก Allocated_Total_Amount_Quantity เป็น NULL ให้แทนที่ด้วย 0
    COALESCE(bap.Allocated_Total_Amount_Quantity, 0) AS Allocated_Total_Amount_Quantity,
    
    -- Pre_Release_Amount ถ้า NULL ให้เป็น 0 เช่นกัน
    COALESCE(bpd.Pre_Release_Amount, 0) AS Pre_Release_Amount,

    -- Release_Amount ถ้า NULL ให้เป็น 0
    COALESCE(bpd.Release_Amount, 0) AS Release_Amount,

    -- แยก Release_Amount โดยใช้เงื่อนไขจาก Scenario และ COALESCE อีกชั้น
    CASE
        WHEN bpd.Scenario = 'ANL-RELEASE-1' THEN COALESCE(bpd.Release_Amount, 0)
        ELSE 0
    END AS Scenario1,
    
    CASE
        WHEN bpd.Scenario = 'ANL-RELEASE-2' THEN COALESCE(bpd.Release_Amount, 0)
        ELSE 0
    END AS Scenario2,
    
    CASE
        WHEN bpd.Scenario = 'ANL-RELEASE-3' THEN COALESCE(bpd.Release_Amount, 0)
        ELSE 0
    END AS Scenario3,
    
    CASE
        WHEN bpd.Scenario = 'ANL-RELEASE-4' THEN COALESCE(bpd.Release_Amount, 0)
        ELSE 0
    END AS Scenario4,
    
    -- คำนวณ Remaining_Amount โดย Coalesce ทั้ง Pre_Release_Amount และ Release_Amount
    ( COALESCE(bap.Allocated_Total_Amount_Quantity, 0) 
      - ( COALESCE(bpd.Pre_Release_Amount, 0) + COALESCE(bpd.Release_Amount, 0) )
    ) AS Remaining_Amount,
    
    -- คำนวณ Total_Release_Amount โดย Coalesce เช่นกัน
    ( COALESCE(bpd.Pre_Release_Amount, 0) + COALESCE(bpd.Release_Amount, 0) ) AS Total_Release_Amount,
    
    -- กรองข้อมูล Fund ที่มีค่าเป็น FN06 หรือ FN02
    bap.Reason
FROM 
    budget_planning_allocated_annual_budget_plan bap

    -- เชื่อม Faculty โดยเลือกเฉพาะ parent ที่ขึ้นต้นด้วย 'Faculty%'
    INNER JOIN Faculty ft 
        ON bap.Faculty = ft.Faculty 
        AND ft.parent LIKE 'Faculty%' 

    LEFT JOIN plan p 
        ON bap.Plan = p.plan_id 

    LEFT JOIN sub_plan sp 
        ON bap.Sub_Plan = sp.sub_plan_id

    LEFT JOIN project pj 
        ON bap.Project = pj.project_id

    INNER JOIN account ac 
        ON bap.`Account` = ac.`account`
        
    LEFT JOIN budget_planning_disbursement_budget_plan_anl_release bpd 
        ON  bap.Service = bpd.Service
        AND bap.Faculty = bpd.Faculty
        AND bap.Project = bpd.Project
        AND bap.Plan = bpd.Plan
        AND bap.Sub_Plan = bpd.Sub_Plan
        AND bap.`Account` = bpd.`Account`";

    // เพิ่มเงื่อนไขสำหรับ Scenario ที่เลือก
    if ($scenarioColumnValue) {
        $query .= " WHERE bpd.Scenario = :scenarioColumn";
    }

    // เพิ่มเงื่อนไขสำหรับ account id ที่มากกว่า id สูงสุดของบัญชี Expenses
    $query .= " AND ac.id > (SELECT MAX(id) FROM account WHERE account = 'Expenses')";

    $query .= " ORDER BY 
    bap.Plan,
    bap.Sub_Plan,
    bap.Project,           
    bap.Faculty,            
    bap.Account";

    $stmt = $conn->prepare($query);
    if ($scenarioColumnValue) {
        $stmt->bindParam(':scenarioColumn', $scenarioColumnValue);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// ดึงข้อมูลตาม Scenario ที่เลือก (ในตัวอย่างนี้ใช้ ScenarioValue)
$results = fetchScenarioData($conn, $scenarioValue);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>รายงานการจัดสรรเงินรายงวด</title>
    <!-- รวม CSS และ Script ต่าง ๆ ที่ต้องการ -->
</head>

<body class="v-light vertical-nav fix-header fix-sidebar">
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <div id="main-wrapper">
        <?php include('../component/left-nev.php'); ?>
        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานการจัดสรรเงินรายงวด</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">รายงานการจัดสรรเงินรายงวด</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการจัดสรรเงินรายงวด</h4>
                                </div>
                                <!-- Dropdown สำหรับเลือก Scenario -->
                                <form method="GET" action="">
                                    <label for="scenario">เลือก จัดสรรงวดที่:</label>
                                    <select name="scenario" id="scenario">
                                        <option value="Scenario1" <?php if ($selectedScenario == 'Scenario1')
                                            echo 'selected'; ?>>จัดสรรงวดที่ 1</option>
                                        <option value="Scenario2" <?php if ($selectedScenario == 'Scenario2')
                                            echo 'selected'; ?>>จัดสรรงวดที่ 2</option>
                                        <option value="Scenario3" <?php if ($selectedScenario == 'Scenario3')
                                            echo 'selected'; ?>>จัดสรรงวดที่ 3</option>
                                        <option value="Scenario4" <?php if ($selectedScenario == 'Scenario4')
                                            echo 'selected'; ?>>จัดสรรงวดที่ 4</option>
                                    </select>
                                    <button type="submit">แสดงข้อมูล</button>
                                </form>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        console.log("Script Loaded");

                                        // ดึงค่า scenario จาก URL (หลังจากกดแสดงข้อมูล)
                                        const urlParams = new URLSearchParams(window.location.search);
                                        const selectedScenario = urlParams.get("scenario");

                                        if (selectedScenario) {
                                            console.log("ค่าที่เลือก:", selectedScenario);
                                        } else {
                                            console.log("ไม่มีค่า scenario ใน URL");
                                        }
                                    });
                                </script>

                                <div class="card-title" style="margin-top:20px;">
                                    <span>
                                        <?php
                                        // เปลี่ยนชื่อแสดงตาม Scenario ที่เลือก
                                        switch ($selectedScenario) {
                                            case 'Scenario1':
                                                echo 'จัดสรรงวดที่ 1';
                                                break;
                                            case 'Scenario2':
                                                echo 'จัดสรรงวดที่ 2';
                                                break;
                                            case 'Scenario3':
                                                echo 'จัดสรรงวดที่ 3';
                                                break;
                                            case 'Scenario4':
                                                echo 'จัดสรรงวดที่ 4';
                                                break;
                                            default:
                                                echo 'จัดสรรงวดที่ 1'; // ค่าเริ่มต้น
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th rowspan="2">รวมทั้งสิ้น</th>
                                                <th colspan="3">เงินงวด</th>
                                                <th rowspan="2">งบประมาณรายจ่ายคงเหลือ</th>
                                                <th rowspan="2">หมายเหตุ</th>
                                            </tr>
                                            <tr>
                                                <th>เงินจัดสรรกำหนดให้แล้ว</th>
                                                <?php
                                                // กำหนดข้อความสำหรับแต่ละ Scenario
                                                $scenarioHeaders = [
                                                    'Scenario1' => 'เงินจัดสรรอนุมัติงวดที่ 1',
                                                    'Scenario2' => 'เงินจัดสรรอนุมัติงวดที่ 2',
                                                    'Scenario3' => 'เงินจัดสรรอนุมัติงวดที่ 3',
                                                    'Scenario4' => 'เงินจัดสรรอนุมัติงวดที่ 4',
                                                ];

                                                // กำหนดค่าเริ่มต้น
                                                $selectedScenario = $_GET['scenario'] ?? 'Scenario1';
                                                $headerText = $scenarioHeaders[$selectedScenario] ?? 'เงินจัดสรรอนุมัติงวดที่ 1';
                                                ?>
                                                <th><?php echo $headerText; ?></th>
                                                <th>รวมเงินจัดสรรทั้งสิ้น</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // ตัวแปรเก็บค่าของแถวก่อนหน้า
                                            $previousPlan = "";
                                            $previousSubPlanId = "";
                                            $previousProject = "";
                                            $previousType = "";
                                            $previousSubType = "";

                                            if (!empty($results)) {
                                                foreach ($results as $row) {
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left;'>";

                                                    // เช็คและแสดง Plan ถ้าเปลี่ยนแปลง
                                                    if ($row['Plan'] != $previousPlan) {
                                                        echo "<strong>" . htmlspecialchars($row['Plan']) . "</strong> : " . htmlspecialchars($row['plan_name']) . "<br>";
                                                        $previousPlan = $row['Plan'];
                                                        $previousSubPlanId = ""; // รีเซ็ตค่าเมื่อเปลี่ยน Plan
                                                        $previousProject = "";
                                                        $previousType = "";
                                                        $previousSubType = "";
                                                    }

                                                    // เช็คและแสดง Sub Plan ถ้าเปลี่ยนแปลง
                                                    if ($row['sub_plan_id'] != $previousSubPlanId) {
                                                        // ลบคำว่า "SP_" ออกจาก sub_plan_id
                                                        $cleanedSubPlanId = str_replace('SP_', '', $row['sub_plan_id']);
                                                        echo str_repeat("&nbsp;", 8) . "<strong>" . htmlspecialchars($cleanedSubPlanId) . "</strong> : " . htmlspecialchars($row['sub_plan_name']) . "<br>";
                                                        $previousSubPlanId = $row['sub_plan_id'];
                                                        $previousProject = "";
                                                        $previousType = "";
                                                        $previousSubType = "";
                                                    }


                                                    // เช็คและแสดง Project ถ้าเปลี่ยนแปลง
                                                    if ($row['project_name'] != $previousProject) {
                                                        echo str_repeat("&nbsp;", 16) . htmlspecialchars($row['project_name']) . "<br>";
                                                        $previousProject = $row['project_name'];
                                                        $previousType = "";
                                                        $previousSubType = "";
                                                    }

                                                    // เช็คและแสดง Type ถ้าเปลี่ยนแปลง
                                                    if ($row['type'] != $previousType) {
                                                        // ลบตัวเลขและจุดจาก type
                                                        $cleanedType = preg_replace('/[0-9.]/', '', $row['type']);
                                                        echo "<strong>" . htmlspecialchars($row['a1']) . "</strong> : " . htmlspecialchars($cleanedType) . "<br>";
                                                        $previousType = $row['type'];
                                                        $previousSubType = "";
                                                    }

                                                    // เช็คและแสดง Sub Type ถ้าเปลี่ยนแปลง
                                                    if ($row['sub_type'] != $previousSubType) {
                                                        // ลบตัวเลขและจุดจาก sub_type
                                                        $cleanedSubType = preg_replace('/[0-9.]/', '', $row['sub_type']);
                                                        echo str_repeat("&nbsp;", 16) . "<strong>" . htmlspecialchars($row['a2']) . "</strong> : " . htmlspecialchars($cleanedSubType) . "<br>";
                                                        $previousSubType = $row['sub_type'];
                                                    }


                                                    // เช็คและกำหนดค่า kkuItemName
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "<strong>" . htmlspecialchars($row['account']) . "</strong> : " . htmlspecialchars($row['KKU_Item_Name'])
                                                        : "<strong>" . htmlspecialchars($row['account']) . "</strong>";

                                                    // แสดงผล
                                                    echo str_repeat("&nbsp;", 32) . $kkuItemName;

                                                    // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['Allocated_Total_Amount_Quantity']) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>0</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row[$selectedScenario]) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['Total_Release_Amount']) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['Remaining_Amount']) . "</td>";
                                                    echo "<td style='vertical-align: bottom;'>" . htmlspecialchars($row['Reason']) . "</td>";


                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='8'>ไม่พบข้อมูล</td></tr>";
                                            }
                                            ?>

                                        </tbody>
                                    </table>

                                </div>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLSX</button>
                            </div>
                        </div>

                    </div>
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