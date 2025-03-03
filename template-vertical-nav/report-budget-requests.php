<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: middle;
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
        border: 1px solid #ddd;
        /* เพิ่มขอบให้เซลล์ */
    }

    /* ทำให้ข้อมูลในตารางเลื่อนได้ */
    .table-responsive {
        max-height: 60vh;
        /* กำหนดความสูงของตาราง */
        overflow-y: auto;
        /* ทำให้สามารถเลื่อนข้อมูลในตารางได้ */
    }
</style>

<?php

include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();

$budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;


function fetchBudgetData($conn, $budget_year1 = null)
{
    // ตรวจสอบว่า $budget_year1, $budget_year2, $budget_year3 ถูกตั้งค่าแล้วหรือไม่
    if ($budget_year1 === null) {
        $budget_year1 = 2568;  // ค่าเริ่มต้นถ้าหากไม่ได้รับจาก URL
    }

    // สร้างคิวรี
    $query = "SELECT 
    bap.Faculty,
    fta.Alias_Default AS Default_Faculty,
    ft.Alias_Default,
    bap.Plan,
    p.plan_id,
    p.plan_name,
    bap.Sub_Plan,
    sp.sub_plan_id,
    sp.sub_plan_name,
    bap.Project,
    pj.project_id,
    pj.project_name,
    CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)) AS a1,
    CONCAT(LEFT(bap.`Account`, 4), REPEAT('0', 8)) AS a2,
    ac.`type`,
    ac.sub_type,
    bap.`Account`,
    bap.KKU_Item_Name,
    bap.Total_Amount_Quantity,
    SUM(CASE WHEN bap.Fund = 'FN02' THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_FN02,
    SUM(CASE WHEN bap.Fund = 'FN06' THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_FN06,
    SUM(CASE WHEN bap.Fund = 'FN08' THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_FN08
FROM budget_planning_annual_budget_plan bap
INNER JOIN Faculty ft 
    ON bap.Faculty = ft.Faculty 
    AND ft.parent LIKE 'Faculty%' 
LEFT JOIN Faculty fta
    ON ft.Parent = fta.Faculty
LEFT JOIN sub_plan sp 
    ON sp.sub_plan_id = bap.Sub_Plan
LEFT JOIN project pj 
    ON pj.project_id = bap.Project
LEFT JOIN `account` ac 
    ON ac.`account` = bap.`Account`
LEFT JOIN plan p 
    ON p.plan_id = bap.Plan
    WHERE ac.id > (SELECT MAX(id) FROM account WHERE account = 'Expenses')

";



    // เพิ่มการจัดกลุ่มข้อมูล
    $query .= "  GROUP BY 
    bap.Faculty, fta.Alias_Default, ft.Alias_Default, bap.Plan, p.plan_id, p.plan_name, 
    bap.Sub_Plan, sp.sub_plan_id, sp.sub_plan_name, bap.Project, pj.project_id, pj.project_name, 
    CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)), 
    CONCAT(LEFT(bap.`Account`, 4), REPEAT('0', 8)), 
    ac.`type`, ac.sub_type, bap.`Account`, bap.KKU_Item_Name, bap.Total_Amount_Quantity
ORDER BY 
    bap.Faculty ASC,
    fta.Alias_Default ASC,
    bap.Plan ASC,
    bap.Sub_Plan ASC,
    bap.Project ASC,
    ac.`type` ASC,
    ac.sub_type ASC,
    bap.`Account` ASC";

    // เตรียมคำสั่ง SQL

    $stmt = $conn->prepare($query);


    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



$results = fetchBudgetData($conn, $budget_year1);

function fetchYearsData($conn)
{
    $query = "SELECT DISTINCT Budget_Management_Year 
              FROM budget_planning_annual_budget_plan 
              ORDER BY Budget_Management_Year DESC"; // ดึงปีจากฐานข้อมูล และเรียงลำดับจากปีล่าสุด
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
                        <h4>รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</h4>
                                </div>
                                <div class="row">

                                    <?php
                                    $years = fetchYearsData($conn);  // ดึงข้อมูลปีจากฐานข้อมูล
                                    ?>

                                    <form method="GET" action="" onsubmit="return validateForm()">
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <label for="year" class="label-year"
                                                style="margin-right: 10px;">เลือกปีงบประมาณ</label>
                                            <select name="year" id="year" class="form-control"
                                                style="width: 60%; height: 40px; font-size: 16px; margin-right: 10px;">
                                                <option value="">เลือก ปีงบประมาณ</option>
                                                <?php
                                                // แสดงปีที่ดึงมาจากฟังก์ชัน fetchYearsData
                                                foreach ($years as $year) {
                                                    $yearValue = htmlspecialchars($year['Budget_Management_Year']); // ใช้ Budget_Management_Year เพื่อแสดงปี
                                                    $selected = (isset($_GET['year']) && $_GET['year'] == $yearValue) ? 'selected' : '';
                                                    echo "<option value=\"$yearValue\" $selected>$yearValue</option>";
                                                }
                                                ?>

                                            </select>
                                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                                        </div>


                                    </form>

                                    <script>
                                        function validateForm() {
                                            var year = document.getElementById('year').value;
                                            if (year == '') {
                                                alert('กรุณาเลือกปีงบประมาณ');
                                                return false;  // ป้องกันการส่งฟอร์ม
                                            }
                                            return true;  // ส่งฟอร์มได้
                                        }
                                    </script>

                                    <script>
                                        // ส่งค่าจาก PHP ไปยัง JavaScript
                                        const budgetYear1 = <?php echo json_encode($budget_year1); ?>;
                                        // แสดงค่าของ budget_year ในคอนโซล
                                        console.log('Budget Year 1:', budgetYear1);
                                    </script>


                                </div>
                                <br>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>งบประมาณรายจ่าย</th>
                                                <th value="fn06">เงินอุดหนุนจากรัฐ</th>
                                                <th value="fn08">เงินนอกงบประมาณ</th>
                                                <th value="fn02">เงินรายได้</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            function formatNumber($number)
                                            {
                                                return preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', sprintf("%0.2f", (float) $number));
                                            }

                                            function removeLeadingNumbers($text)
                                            {
                                                // ลบตัวเลขที่อยู่หน้าตัวหนังสือ
                                                return preg_replace('/^[\d.]+\s*/', '', $text);
                                            }

                                            // ตัวแปรเก็บค่าของแถวก่อนหน้า
                                            $previousPlan = "";
                                            $previousSubPlan = "";
                                            $previousProject = "";
                                            $previousSubType = "";
                                            $budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;
                                            $results = fetchBudgetData($conn, $budget_year1);

                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                // สร้าง associative array เพื่อเก็บผลรวมของแต่ละ Plan, Sub_Plan, Project, และ Sub_Type
                                                $summary = [];
                                                foreach ($results as $row) {
                                                    $DefaultFaculty = $row['Default_Faculty'];
                                                    $faculty = $row['Faculty'];
                                                    $plan = $row['Plan'];
                                                    $subPlan = $row['Sub_Plan'];
                                                    $project = $row['project_name'];
                                                    $type = $row['type'];
                                                    $subType = $row['sub_type'];

                                                    // เก็บข้อมูลของ DefaultFaculty
                                                    if (!isset($summary[$DefaultFaculty])) {
                                                        $summary[$DefaultFaculty] = [
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'faculty' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ faculty
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty] = [
                                                            'FacultyName' => $row['Alias_Default'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'plan' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ plan
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan] = [
                                                            'PlanName' => $row['plan_name'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'sub_plan' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ sub plan
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan] = [
                                                            'SubPlanName' => $row['sub_plan_name'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'project' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ project
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project] = [
                                                            'PlanName' => $row['plan_name'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'type' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ type
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type] = [
                                                            'a1' => $row['a1'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'subtype' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ sub type
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['subtype'][$subType])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['subtype'][$subType] = [
                                                            'a2' => $row['a2'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'kkuitemname' => [], // เก็บข้อมูลของ kku
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ DefaultFaculty
                                                    $summary[$DefaultFaculty]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ faculty
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ plan
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ subPlan
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ project
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Total_FN08'] += $row['Total_FN08'];


                                                    // เก็บข้อมูลของ type
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ subtype
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['subtype'][$subType]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['subtype'][$subType]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['subtype'][$subType]['Total_FN08'] += $row['Total_FN08'];


                                                    // เก็บข้อมูลของ KKU_Item_Name
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "" . htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                        : "" . htmlspecialchars($row['Account']) . "";
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$type]['subtype'][$subType]['kku_items'][] = [
                                                        'name' => $kkuItemName,
                                                        'Total_FN06' => $row['Total_FN06'],
                                                        'Total_FN02' => $row['Total_FN02'],
                                                        'Total_FN08' => $row['Total_FN08'],

                                                    ];
                                                    $rows = $summary;
                                                    // ตัวแปรสำหรับเก็บผลรวมทั้งหมด
                                                    $total_summary = [
                                                        'Total_FN06' => 0,
                                                        'Total_FN02' => 0,
                                                        'Total_FN08' => 0,
                                                    ];
                                                    // แสดงผลรวมทั้งหมด
                                                    //print_r($total_summary);
                                                    // Assuming this is inside a loop where $row is updated (e.g., from a database query)
                                                    foreach ($rows as $row) { // Replace $rows with your actual data source
                                                        // รวมผลรวมทั้งหมดโดยไม่สนใจ Faculty
                                                        $total_summary['Total_FN06'] += (float) ($row['Total_FN06'] ?? 0);
                                                        $total_summary['Total_FN02'] += (float) ($row['Total_FN02'] ?? 0);
                                                        $total_summary['Total_FN08'] += (float) ($row['Total_FN08'] ?? 0);
                                                    }

                                                }


                                                foreach ($summary as $DefaultFaculty => $data) {
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 0) . $DefaultFaculty . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_FN06']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_FN08']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_FN02']) . "</td>";
                                                    echo "</tr>";
                                                    foreach ($data['faculty'] as $faculty => $facultyData) {
                                                        $facultyName = str_replace(' - ', ' : ', $facultyData['FacultyName']);
                                                        echo "<tr>";
                                                        echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 8) . htmlspecialchars($facultyName) . "</td>";
                                                        echo "<td>" . formatNumber($facultyData['Total_FN06']) . "</td>";
                                                        echo "<td>" . formatNumber($facultyData['Total_FN08']) . "</td>";
                                                        echo "<td>" . formatNumber($facultyData['Total_FN02']) . "</td>";
                                                        echo "</tr>";
                                                        foreach ($facultyData['plan'] as $plan => $planData) {
                                                            echo "<tr>";
                                                            echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 16) . $plan . ' : ' . htmlspecialchars($planData['PlanName']) . "</td>";
                                                            echo "<td>" . formatNumber($planData['Total_FN06']) . "</td>";
                                                            echo "<td>" . formatNumber($planData['Total_FN08']) . "</td>";
                                                            echo "<td>" . formatNumber($planData['Total_FN02']) . "</td>";
                                                            echo "</tr>";
                                                            foreach ($planData['sub_plan'] as $subPlan => $subPlanData) {
                                                                $cleanedSubPlan = preg_replace('/^SP_/', '', $subPlan);
                                                                echo "<tr>";
                                                                echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 24) . $cleanedSubPlan . ' : ' . htmlspecialchars($subPlanData['SubPlanName']) . "</td>";
                                                                echo "<td>" . formatNumber($subPlanData['Total_FN06']) . "</td>";
                                                                echo "<td>" . formatNumber($subPlanData['Total_FN08']) . "</td>";
                                                                echo "<td>" . formatNumber($subPlanData['Total_FN02']) . "</td>";
                                                                echo "</tr>";
                                                                foreach ($subPlanData['project'] as $project => $projectData) {
                                                                    $projectFormatted = str_replace(":", " : ", $project);
                                                                    echo "<tr>";
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . htmlspecialchars($projectFormatted) . "</td>";
                                                                    echo "<td>" . formatNumber($projectData['Total_FN06']) . "</td>";
                                                                    echo "<td>" . formatNumber($projectData['Total_FN08']) . "</td>";
                                                                    echo "<td>" . formatNumber($projectData['Total_FN02']) . "</td>";
                                                                    echo "</tr>";
                                                                    foreach ($projectData['type'] as $type => $typeData) {
                                                                        $cleanedType = preg_replace('/^[\d.]+\s*/', '', $type);
                                                                        echo "<tr>";
                                                                        echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 40) . htmlspecialchars($typeData['a1']) . ' : ' . $cleanedType . "<br></td>";
                                                                        echo "<td>" . formatNumber($typeData['Total_FN06']) . "</td>";
                                                                        echo "<td>" . formatNumber($typeData['Total_FN08']) . "</td>";
                                                                        echo "<td>" . formatNumber($typeData['Total_FN02']) . "</td>";
                                                                        echo "</tr>";
                                                                        foreach ($typeData['subtype'] as $subType => $subTypeData) {
                                                                            $cleanedSubType = preg_replace('/^[\d.]+\s*/', '', $subType);
                                                                            echo "<tr>";
                                                                            echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 48) . htmlspecialchars($subTypeData['a2']) . ' : ' . $cleanedSubType . "<br></td>";
                                                                            echo "<td>" . formatNumber($subTypeData['Total_FN06']) . "</td>";
                                                                            echo "<td>" . formatNumber($subTypeData['Total_FN08']) . "</td>";
                                                                            echo "<td>" . formatNumber($subTypeData['Total_FN02']) . "</td>";
                                                                            echo "</tr>";
                                                                            foreach ($subTypeData['kku_items'] as $kkuItem) {
                                                                                echo "<tr>";
                                                                                echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 56) . $kkuItem['name'] . "<br></td>";
                                                                                echo "<td>" . formatNumber($kkuItem['Total_FN06']) . "</td>";
                                                                                echo "<td>" . formatNumber($kkuItem['Total_FN08']) . "</td>";
                                                                                echo "<td>" . formatNumber($kkuItem['Total_FN02']) . "</td>";
                                                                                echo "</tr>";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                            } else {
                                                echo "<tr><td colspan='9' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
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
            const csvRows = [];

            // วนลูปทีละ <tr>
            for (const row of table.rows) {
                // เก็บบรรทัดย่อยของแต่ละเซลล์
                const cellLines = [];
                let maxSubLine = 1;

                // วนลูปทีละเซลล์ <td>/<th>
                for (const cell of row.cells) {
                    let html = cell.innerHTML;

                    // 1) แปลง &nbsp; ติดกันให้เป็น non-breaking space (\u00A0) ตามจำนวน
                    html = html.replace(/(&nbsp;)+/g, (match) => {
                        const count = match.match(/&nbsp;/g).length;
                        return '\u00A0'.repeat(count); // ex. 3 &nbsp; → "\u00A0\u00A0\u00A0"
                    });


                    // 3) (ถ้าต้องการ) ลบ tag HTML อื่นออก
                    html = html.replace(/<\/?[^>]+>/g, '');

                    // 4) แยกเป็น array บรรทัดย่อย
                    const lines = html.split('\n').map(x => x.trimEnd());
                    // ใช้ trimEnd() เฉพาะท้าย ไม่ trim ต้นเผื่อบางคนอยากเห็นช่องว่างนำหน้า

                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }

                    cellLines.push(lines);
                }

                // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];

                    // วนลูปแต่ละเซลล์
                    for (const lines of cellLines) {
                        let text = lines[i] || ''; // ถ้าไม่มีบรรทัดที่ i ก็ว่าง
                        // Escape double quotes
                        text = text.replace(/"/g, '""');
                        // ครอบด้วย ""
                        text = `"${text}"`;
                        rowData.push(text);
                    }

                    csvRows.push(rowData.join(','));
                }
            }

            // รวมเป็น CSV + BOM
            const csvContent = "\uFEFF" + csvRows.join("\n");
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
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
            doc.text("รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ", 10, 500);

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
            doc.save('รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const {
                theadRows,
                theadMerges
            } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br />, ไม่ merge) ============
            const tbodyRows = parseTbody(table.tBodies[0]);

            // รวม rows ทั้งหมด: thead + tbody
            const allRows = [...theadRows, ...tbodyRows];

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead ลงใน sheet (ถ้ามี)
            ws['!merges'] = theadMerges;

            // ตั้งค่า vertical-align: bottom ให้ทุกเซลล์
            applyCellStyles(ws, "bottom");

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์เป็น .xlsx (แทน .xls เพื่อรองรับ style)
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'array'
            });

            // สร้าง Blob + ดาวน์โหลด
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        /**
         * -----------------------
         * 1) parseThead: รองรับ merge
         * -----------------------
         */
        function parseThead(thead) {
            const theadRows = [];
            const theadMerges = [];

            if (!thead) {
                return {
                    theadRows,
                    theadMerges
                };
            }

            const skipMap = {};

            for (let rowIndex = 0; rowIndex < thead.rows.length; rowIndex++) {
                const tr = thead.rows[rowIndex];
                const rowData = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    while (skipMap[`${rowIndex},${colIndex}`]) {
                        rowData[colIndex] = "";
                        colIndex++;
                    }

                    const cell = tr.cells[cellIndex];
                    let text = cell.innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length)) // แทนที่ &nbsp; ด้วยช่องว่าง
                        .replace(/<\/?[^>]+>/g, '') // ลบแท็ก HTML ทั้งหมด
                        .trim();

                    rowData[colIndex] = text;

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        theadMerges.push({
                            s: {
                                r: rowIndex,
                                c: colIndex
                            },
                            e: {
                                r: rowIndex + rowspan - 1,
                                c: colIndex + colspan - 1
                            }
                        });

                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (r === 0 && c === 0) continue;
                                skipMap[`${rowIndex + r},${colIndex + c}`] = true;
                            }
                        }
                    }
                    colIndex++;
                }
                theadRows.push(rowData);
            }

            return {
                theadRows,
                theadMerges
            };
        }

        /**
         * -----------------------
         * 2) parseTbody: แตก <br/> เป็นหลาย sub-row
         * -----------------------
         */
        function parseTbody(tbody) {
            const rows = [];

            if (!tbody) return rows;

            for (const tr of tbody.rows) {
                const cellLines = [];
                let maxSubLine = 1;

                for (const cell of tr.cells) {
                    let html = cell.innerHTML
                        .replace(/(&nbsp;)+/g, match => {
                            const count = match.match(/&nbsp;/g).length;
                            return ' '.repeat(count);
                        })
                        .replace(/<\/?[^>]+>/g, ''); // ลบแท็ก HTML ทั้งหมด

                    const lines = html.split('\n').map(x => x.trimEnd());
                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }
                    cellLines.push(lines);
                }

                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];
                    for (const lines of cellLines) {
                        rowData.push(lines[i] || '');
                    }
                    rows.push(rowData);
                }
            }

            return rows;
        }

        /**
         * -----------------------
         * 3) applyCellStyles: ตั้งค่า vertical-align ให้ทุก cell
         * -----------------------
         */
        function applyCellStyles(ws, verticalAlign) {
            if (!ws['!ref']) return;

            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let R = range.s.r; R <= range.e.r; ++R) {
                for (let C = range.s.c; C <= range.e.c; ++C) {
                    const cell_address = XLSX.utils.encode_cell({
                        r: R,
                        c: C
                    });
                    if (!ws[cell_address]) continue;

                    if (!ws[cell_address].s) ws[cell_address].s = {};
                    ws[cell_address].s.alignment = {
                        vertical: verticalAlign
                    };
                }
            }
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