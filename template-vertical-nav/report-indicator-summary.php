<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
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
$selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
$year = isset($_GET['year']) ? (int) $_GET['year'] : 2568; // Default to 2568 if not provided
$budget_year1 = $year;
$budget_year2 = $year - 1;
$scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
function fetchBudgetData($conn, $selectedFaculty = null, $budget_year1 = null, $scenario = null)
{
    $budget_year1 = $budget_year1 ?? 2568;

    $query = "WITH Annual AS (
        SELECT DISTINCT bap.Faculty, ft.Alias_Default, bap.Plan, p.plan_name, 
                        bap.Sub_Plan, sp.sub_plan_name, bap.Project, pj.project_name, bap.Scenario
        FROM budget_planning_annual_budget_plan bap
        LEFT JOIN Faculty ft ON ft.Faculty = bap.Faculty AND ft.parent LIKE 'Faculty%' 
        LEFT JOIN plan p ON p.plan_id = bap.Plan
        LEFT JOIN sub_plan sp ON sp.sub_plan_id = bap.Sub_Plan
        LEFT JOIN project pj ON pj.project_id = bap.Project
        ORDER BY bap.Faculty ASC, bap.Plan ASC , bap.Sub_Plan ASC , bap.Project ASC 
    )
    SELECT * FROM Annual";

    // Initialize an array to store conditions
    $conditions = [];
    $params = [];

    // Add conditions based on provided parameters
    if ($selectedFaculty) {
        $conditions[] = "Faculty = :selectedFaculty";
        $params[':selectedFaculty'] = $selectedFaculty;
    }
    if ($scenario) {
        $conditions[] = "Scenario = :scenario";
        $params[':scenario'] = $scenario;
    }

    // Append conditions to the query if any exist
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($query);

    // Bind parameters if they exist
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$query2 = "
WITH kpiProgress AS (SELECT DISTINCT 
    ppp.Faculty,
    ppp.Sub_Plan,
    ppp.KPI,
    SUM(case when ppp.YEAR = $budget_year1 THEN ppp.Prog_Q1 ELSE 0 END)AS Prog_Q1_1,
    SUM(case when ppp.YEAR = $budget_year1 THEN ppp.Prog_Q2 ELSE 0 END)AS Prog_Q2_1,
    SUM(case when ppp.YEAR = $budget_year1 THEN ppp.Prog_Q3 ELSE 0 END)AS Prog_Q3_1,
    SUM(case when ppp.YEAR = $budget_year1 THEN ppp.Prog_Q4 ELSE 0 END)AS Prog_Q4_1,
    SUM(case when ppp.YEAR = $budget_year2 THEN ppp.Prog_Q1 ELSE 0 END)AS Prog_Q1_2,
    SUM(case when ppp.YEAR = $budget_year2 THEN ppp.Prog_Q2 ELSE 0 END)AS Prog_Q2_2,
    SUM(case when ppp.YEAR = $budget_year2 THEN ppp.Prog_Q3 ELSE 0 END)AS Prog_Q3_2,
    SUM(case when ppp.YEAR = $budget_year2 THEN ppp.Prog_Q4 ELSE 0 END)AS Prog_Q4_2
FROM budget_planning_sub_plan_kpi_progress ppp
GROUP BY ppp.Faculty,
    ppp.Sub_Plan,
    ppp.KPI
),kpiSubplan AS (SELECT DISTINCT spi.Faculty,spi.Plan,spi.Sub_Plan,spi.Sub_plan_KPI_Name,spi.KPI,spi.UoM_for_Sub_plan_KPI,
SUM(case when spi.`YEAR` = $budget_year1 then spi.Sub_plan_KPI_Target ELSE 0 END) AS Sub_plan_KPI_Target_1,
SUM(case when spi.`YEAR` = $budget_year2 then spi.Sub_plan_KPI_Target ELSE 0 END) AS Sub_plan_KPI_Target_2
FROM budget_planning_subplan_kpi spi
GROUP BY spi.Faculty,spi.Plan,spi.Sub_Plan,spi.Sub_plan_KPI_Name,spi.UoM_for_Sub_plan_KPI,spi.KPI)
,t1 AS (SELECT spi.Faculty,spi.Plan,spi.Sub_Plan,spi.Sub_plan_KPI_Name,spi.KPI,spi.UoM_for_Sub_plan_KPI,spi.Sub_plan_KPI_Target_1,spi.Sub_plan_KPI_Target_2
,ppp.Prog_Q1_1
,ppp.Prog_Q2_1
,ppp.Prog_Q3_1
,ppp.Prog_Q4_1
,ppp.Prog_Q1_2
,ppp.Prog_Q2_2
,ppp.Prog_Q3_2
,ppp.Prog_Q4_2 FROM kpiSubplan spi 
LEFT JOIN kpiProgress ppp
ON ppp.Faculty = spi.Faculty
AND ppp.Sub_Plan = spi.Sub_Plan
AND ppp.KPI = spi.KPI
)
SELECT * FROM t1
";

// เตรียม Query 2
$stmt2 = $conn->prepare($query2);
$stmt2->execute();
$results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$query3 = "
   WITH kpiProject AS (
       SELECT DISTINCT 
           pki.Faculty,
           pki.Project,
           pki.Proj_KPI_Name,
           pki.UoM_for_Proj_KPI,
           pki.KPI,
           SUM(CASE WHEN pki.YEAR = $budget_year1 THEN pki.Proj_KPI_Target ELSE 0 END) AS Proj_KPI_Target_1,
           SUM(CASE WHEN pki.YEAR = $budget_year2 THEN pki.Proj_KPI_Target ELSE 0 END) AS Proj_KPI_Target_2
       FROM budget_planning_project_kpi pki
       GROUP BY pki.Faculty, pki.Project, pki.Proj_KPI_Name, pki.UoM_for_Proj_KPI, pki.KPI
   ),
   kpiProgress AS (
       SELECT DISTINCT 
           ppp.Faculty,
           ppp.Project,
           ppp.KPI,
           SUM(CASE WHEN ppp.YEAR = $budget_year1 THEN ppp.Prog_Q1 ELSE 0 END) AS Prog_Q1_1,
           SUM(CASE WHEN ppp.YEAR = $budget_year1 THEN ppp.Prog_Q2 ELSE 0 END) AS Prog_Q2_1,
           SUM(CASE WHEN ppp.YEAR = $budget_year1 THEN ppp.Prog_Q3 ELSE 0 END) AS Prog_Q3_1,
           SUM(CASE WHEN ppp.YEAR = $budget_year1 THEN ppp.Prog_Q4 ELSE 0 END) AS Prog_Q4_1,
           SUM(CASE WHEN ppp.YEAR = $budget_year2 THEN ppp.Prog_Q1 ELSE 0 END) AS Prog_Q1_2,
           SUM(CASE WHEN ppp.YEAR = $budget_year2 THEN ppp.Prog_Q2 ELSE 0 END) AS Prog_Q2_2,
           SUM(CASE WHEN ppp.YEAR = $budget_year2 THEN ppp.Prog_Q3 ELSE 0 END) AS Prog_Q3_2,
           SUM(CASE WHEN ppp.YEAR = $budget_year2 THEN ppp.Prog_Q4 ELSE 0 END) AS Prog_Q4_2
       FROM budget_planning_project_kpi_progress ppp
       GROUP BY ppp.Faculty, ppp.Project, ppp.KPI
   ),
   t1 AS (
       SELECT 
           pki.Faculty,
           pki.Project,
           pki.Proj_KPI_Name,
           pki.KPI,
           pki.UoM_for_Proj_KPI,
           pki.Proj_KPI_Target_1,
           ppp.Prog_Q1_1,
           ppp.Prog_Q2_1,
           ppp.Prog_Q3_1,
           ppp.Prog_Q4_1,
           pki.Proj_KPI_Target_2,
           ppp.Prog_Q1_2,
           ppp.Prog_Q2_2,
           ppp.Prog_Q3_2,
           ppp.Prog_Q4_2
       FROM kpiProject pki
       LEFT JOIN kpiProgress ppp 
       ON ppp.Faculty = pki.Faculty
       AND ppp.Project = pki.Project
       AND ppp.KPI = pki.KPI
   )
   SELECT * FROM t1
";

$stmt3 = $conn->prepare($query3);
$stmt3->execute();
$results3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

$results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $scenario);

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
function fetchScenariosData($conn)
{
    $query = "SELECT DISTINCT Scenario FROM budget_planning_annual_budget_plan";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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
                        <h4>รายงานสรุปรายการตัวชี้วัดแผน/ผลของแผนงานย่อย</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปรายการตัวชี้วัดแผน/ผลของแผนงานย่อย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย</h4>
                                </div>
                                <div class="table-responsive">
                                    <?php
                                    $faculties = fetchFacultyData($conn);
                                    $years = fetchYearsData($conn);  // ดึงข้อมูลปีจากฐานข้อมูล\
                                    $scenarios = fetchScenariosData($conn); // ดึงข้อมูล Scenario จากฐานข้อมูล
                                    ?>
                                    <form method="GET" action="" onsubmit="validateForm(event)">

                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <label for="year" class="label-year"
                                                style="margin-right: 10px;">เลือกปีงบประมาณ</label>
                                            <select name="year" id="year" class="form-control"
                                                style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                                <option value="">เลือก ปีงบประมาณ</option>
                                                <?php
                                                foreach ($years as $year) {
                                                    $yearValue = htmlspecialchars($year['Budget_Management_Year']);
                                                    $selected = (isset($_GET['year']) && $_GET['year'] == $yearValue) ? 'selected' : '';
                                                    echo "<option value=\"$yearValue\" $selected>$yearValue</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <label for="scenario" class="label-scenario"
                                                style="margin-right: 10px;">เลือก
                                                ประเภทงบประมาณ</label>
                                            <select name="scenario" id="scenario" class="form-control"
                                                style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                                <option value="">เลือก ทุก ประเภทงบประมาณ</option>
                                                <?php
                                                foreach ($scenarios as $scenario) {
                                                    $scenarioName = htmlspecialchars($scenario['Scenario']);
                                                    $scenarioCode = htmlspecialchars($scenario['Scenario']);
                                                    $selected = (isset($_GET['scenario']) && $_GET['scenario'] == $scenarioCode) ? 'selected' : '';
                                                    echo "<option value=\"$scenarioCode\" $selected>$scenarioName</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <label for="faculty" class="label-faculty" style="margin-right: 10px;">เลือก
                                                ส่วนงาน/หน่วยงาน</label>
                                            <select name="faculty" id="faculty" class="form-control"
                                                style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                                <option value="">เลือก ส่วนงาน/หน่วยงาน</option>
                                                <?php
                                                foreach ($faculties as $faculty) {
                                                    $facultyName = htmlspecialchars($faculty['Faculty_Name']);
                                                    $facultyCode = htmlspecialchars($faculty['Faculty']);
                                                    $selected = (isset($_GET['faculty']) && $_GET['faculty'] == $facultyCode) ? 'selected' : '';
                                                    echo "<option value=\"$facultyCode\" $selected>$facultyName</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="display: flex; justify-content: center;">
                                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                                        </div>
                                    </form>

                                    <script>
                                        function validateForm(event) {
                                            event.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ

                                            var faculty = document.getElementById('faculty').value;
                                            var year = document.getElementById('year').value;
                                            var scenario = document.getElementById('scenario').value;

                                            var baseUrl = "http://202.28.118.192:8081/template-vertical-nav/report-indicator-summary.php";
                                            var params = [];

                                            // เพิ่ม Faculty หากเลือก
                                            if (faculty) {
                                                params.push("faculty=" + encodeURIComponent(faculty));
                                            }
                                            // เพิ่ม Year หากเลือกและไม่เป็นค่าว่าง
                                            if (year && year !== "") {
                                                params.push("year=" + encodeURIComponent(year));
                                            }
                                            // เพิ่ม Scenario หากเลือกและไม่เป็นค่าว่าง
                                            if (scenario && scenario !== "") {
                                                params.push("scenario=" + encodeURIComponent(scenario));
                                            }

                                            // ตรวจสอบพารามิเตอร์ที่สร้าง
                                            console.log("Params:", params);

                                            // ถ้าไม่มีการเลือกอะไรเลย
                                            if (params.length === 0) {
                                                window.location.href = baseUrl; // ถ้าไม่มีการเลือกใดๆ จะเปลี่ยน URL ไปที่ base URL
                                            } else {
                                                // ถ้ามีการเลือกค่า จะเพิ่มพารามิเตอร์ที่เลือกไปใน URL
                                                window.location.href = baseUrl + "?" + params.join("&");
                                            }
                                        }
                                    </script>
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="15" style='text-align: left;'>
                                                    รายงานสรุปรายการตัวชี้วัดแผน/ผลของแผนงานย่อย</th>
                                            </tr>

                                            <?php


                                            // ตรวจสอบและกำหนดค่า $selectedFacultyName
                                            $selectedFacultyCode = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $selectedYear = isset($_GET['year']) && $_GET['year'] != '' ? (int) $_GET['year'] : '2568';
                                            $scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
                                            $selectedFacultyName = 'แสดงทุกหน่วยงาน';

                                            if ($selectedFacultyCode) {
                                                // ค้นหาชื่อคณะจากรหัสคณะที่เลือก
                                                foreach ($faculties as $faculty) {
                                                    if ($faculty['Faculty'] === $selectedFacultyCode) {
                                                        $selectedFacultyName = htmlspecialchars($faculty['Faculty_Name']);
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <th colspan="15" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        if ($selectedYear) {
                                                            echo "ปีงบที่ต้องการเปรียบเทียบ " . ($selectedYear - 1) . " ถึง " . $selectedYear;
                                                        } else {
                                                            echo "ปีงบที่ต้องการเปรียบเทียบ: ไม่ได้เลือกปีงบประมาณ";
                                                        }
                                                        ?> </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="15" style='text-align: left;'>
                                                    <span style="font-size: 16px;">


                                                        <?php
                                                        $facultyData = str_replace('-', ':', $selectedFacultyName);

                                                        echo "ส่วนงาน / หน่วยงาน: " . $facultyData; ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="15" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        echo "ประเภทงบประมาณ: " . (!empty($scenario) ? $scenario : "แสดงทุกประเภทงบประมาณ");
                                                        ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="7">ปี <?= ($selectedYear - 1) ?></th>
                                                <th colspan="7">ปี <?= $selectedYear ?></th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2">หน่วยนับของตัวชี้วัด</th>
                                                <th rowspan="2">ค่าเป้าหมาย</th>
                                                <th colspan="4">ผลของตัวชี้วัด</th>
                                                <th rowspan="2">ผลรวมของตัวชี้วัด</th>
                                                <th rowspan="2">หน่วยนับของตัวชี้วัด</th>
                                                <th rowspan="2">ค่าเป้าหมาย</th>
                                                <th colspan="4">ผลของตัวชี้วัด</th>
                                                <th rowspan="2">ผลรวมของตัวชี้วัด</th>
                                            </tr>
                                            <tr>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
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

                                            $previousType = "";
                                            $previousSubType = "";
                                            $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;
                                            $budget_year2 = isset($_GET['year']) ? $_GET['year'] - 1 : null;
                                            $scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
                                            $results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $scenario);
                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                $summary = [];
                                                $shownSubPlans = [];
                                                foreach ($results as $row) {
                                                    $Faculty = $row['Faculty'];
                                                    $Plan = $row['Plan'];
                                                    $Sub_Plan = $row['Sub_Plan'];
                                                    $Project = $row['Project'];
                                                    if (!isset($summary[$Faculty])) {
                                                        $summary[$Faculty] = [
                                                            'name' => str_replace('-', ':', $row['Alias_Default'] ?? ''),
                                                            'plan' => [],
                                                        ];
                                                    }
                                                    if (!isset($summary[$Faculty]['plan'][$Plan])) {
                                                        $summary[$Faculty]['plan'][$Plan] = [
                                                            'name' => $row['plan_name'],
                                                            'subPlan' => [],
                                                        ];
                                                    }
                                                    if (!isset($summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan])) {
                                                        $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan] = [
                                                            'name' => $row['Sub_Plan'],
                                                            'name1' => $row['sub_plan_name'],
                                                            'Faculty' => $row['Faculty'],
                                                            'Plan' => $row['Plan'],
                                                            'Sub_Plan' => $row['Sub_Plan'],
                                                            'ProjectID' => $row['Project'],
                                                            'project' => [],
                                                        ];
                                                    }
                                                    if (!isset($summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project])) {
                                                        $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project] = [
                                                            'name' => $row['project_name'],
                                                            'Faculty' => $row['Faculty'],
                                                            'Plan' => $row['Plan'],
                                                            'Sub_Plan' => $row['Sub_Plan'],
                                                            'ProjectID' => $row['Project'],
                                                            'kku' => [],
                                                        ];
                                                    }
                                                }
                                                foreach ($summary as $Faculty => $data1) {
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left;'>" . htmlspecialchars($data1['name'] ?? '') . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                    echo "</tr>";
                                                    foreach ($data1['plan'] as $Plan => $data2) {
                                                        echo "<tr>";
                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 8) . htmlspecialchars($data2['name'] ?? '') . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "-" . "</td>";

                                                        echo "</tr>";

                                                        foreach ($data2['subPlan'] as $Sub_Plan => $data3) {
                                                            // ใช้ข้อมูลจาก $summary ไปดึงข้อมูลจาก $results2 ที่ตรงกัน
                                                            $matchingResults2 = array_filter($results2, function ($result2) use ($data3) {
                                                                return $result2['Faculty'] === $data3['Faculty'] && $result2['Plan'] === $data3['Plan'] && $result2['Sub_Plan'] === $data3['Sub_Plan'];
                                                            });

                                                            // นำข้อมูลที่ตรงกันไปเก็บใน $data3
                                                            $data3['kpi_data1'] = $matchingResults2;

                                                            // สร้างตัวแปรเพื่อเก็บผลรวมสำหรับฟิลด์ต่างๆ
                                                            $Sub_plan_KPI_Target_1 = 0;
                                                            $Sub_plan_KPI_Target_2 = 0;
                                                            $total_Prog_Q1_2 = 0;
                                                            $total_Prog_Q2_2 = 0;
                                                            $total_Prog_Q3_2 = 0;
                                                            $total_Prog_Q4_2 = 0;
                                                            $total_Prog_Q1_1 = 0;
                                                            $total_Prog_Q2_1 = 0;
                                                            $total_Prog_Q3_1 = 0;
                                                            $total_Prog_Q4_1 = 0;

                                                            // คำนวณผลรวมจากข้อมูลที่ตรงกัน
                                                            foreach ($data3['kpi_data1'] as $row2) {
                                                                $Sub_plan_KPI_Target_1 += $row2['Sub_plan_KPI_Target_1'];
                                                                $Sub_plan_KPI_Target_2 += $row2['Sub_plan_KPI_Target_2'];
                                                                $total_Prog_Q1_2 += $row2['Prog_Q1_2'];
                                                                $total_Prog_Q2_2 += $row2['Prog_Q2_2'];
                                                                $total_Prog_Q3_2 += $row2['Prog_Q3_2'];
                                                                $total_Prog_Q4_2 += $row2['Prog_Q4_2'];
                                                                $total_Prog_Q1_1 += $row2['Prog_Q1_1'];
                                                                $total_Prog_Q2_1 += $row2['Prog_Q2_1'];
                                                                $total_Prog_Q3_1 += $row2['Prog_Q3_1'];
                                                                $total_Prog_Q4_1 += $row2['Prog_Q4_1'];
                                                            }

                                                            // คำนวณผลรวมสำหรับ `total1` และ `total2`
                                                            $total2 = $total_Prog_Q1_2 + $total_Prog_Q2_2 + $total_Prog_Q3_2 + $total_Prog_Q4_2;
                                                            $total1 = $total_Prog_Q1_1 + $total_Prog_Q2_1 + $total_Prog_Q3_1 + $total_Prog_Q4_1;
                                                            $subPlanName = str_replace('SP_', '', $data3['name'] ?? '');
                                                            // แสดงข้อมูล Sub Plan
                                                            echo "<tr>";
                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 16) . htmlspecialchars($subPlanName) . " : " . htmlspecialchars($data3['name1'] ?? '') . "</td>";
                                                            echo "<td>" . "</td>";
                                                            echo "<td>" . htmlspecialchars($Sub_plan_KPI_Target_2) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total_Prog_Q1_2) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total_Prog_Q2_2) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total_Prog_Q3_2) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total_Prog_Q4_2) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total2) . "</td>";
                                                            echo "<td>" . "</td>";
                                                            echo "<td>" . htmlspecialchars($Sub_plan_KPI_Target_1) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total_Prog_Q1_1) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total_Prog_Q2_1) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total_Prog_Q3_1) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total_Prog_Q4_1) . "</td>";
                                                            echo "<td>" . htmlspecialchars($total1) . "</td>";
                                                            echo "</tr>";

                                                            // แสดงข้อมูล KPI ของ Sub Plan
                                                            if (!empty($data3['kpi_data1'])) {
                                                                foreach ($data3['kpi_data1'] as $row2) {
                                                                    $total2 = $row2['Prog_Q1_2'] + $row2['Prog_Q2_2'] + $row2['Prog_Q3_2'] + $row2['Prog_Q4_2'];
                                                                    $total1 = $row2['Prog_Q1_1'] + $row2['Prog_Q2_1'] + $row2['Prog_Q3_1'] + $row2['Prog_Q4_1'];
                                                                    echo "<tr>";
                                                                    echo "<td>" . str_repeat("&nbsp;", 16) . htmlspecialchars($row2['Sub_plan_KPI_Name']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['UoM_for_Sub_plan_KPI']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Sub_plan_KPI_Target_1']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Prog_Q1_2']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Prog_Q2_2']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Prog_Q3_2']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Prog_Q4_2']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($total2) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['UoM_for_Sub_plan_KPI']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Sub_plan_KPI_Target_2']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Prog_Q1_1']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Prog_Q2_1']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Prog_Q3_1']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($row2['Prog_Q4_1']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($total1) . "</td>";
                                                                    echo "</tr>";
                                                                }
                                                            } else {
                                                                echo "<td  style='text-align:center;'>ไม่มีข้อมูล KPI</td>";
                                                                echo "<td colspan='14' style='text-align:center;'> -</td>";
                                                            }

                                                            // แสดงข้อมูล Project และ KPI ของ Project
                                                            foreach ($data3['project'] as $Project => &$data4) {
                                                                // ค้นหาข้อมูลที่ตรงกันจาก $results3
                                                                $matchingResults3 = array_filter($results3, function ($result3) use ($data4) {
                                                                    return $result3['Faculty'] === $data4['Faculty'] && $result3['Project'] === $data4['ProjectID'];
                                                                });

                                                                // นำข้อมูลที่ตรงกันไปเก็บใน $data4
                                                                $data4['kpi_data2'] = $matchingResults3;

                                                                // สร้างตัวแปรเพื่อเก็บผลรวมสำหรับฟิลด์ต่างๆ
                                                                $total_Proj_KPI_Target_1 = 0;
                                                                $total_Proj_KPI_Target_2 = 0;
                                                                $total_Prog_Q1_2 = 0;
                                                                $total_Prog_Q2_2 = 0;
                                                                $total_Prog_Q3_2 = 0;
                                                                $total_Prog_Q4_2 = 0;
                                                                $total_Prog_Q1_1 = 0;
                                                                $total_Prog_Q2_1 = 0;
                                                                $total_Prog_Q3_1 = 0;
                                                                $total_Prog_Q4_1 = 0;

                                                                // คำนวณผลรวมจากข้อมูลที่ตรงกัน
                                                                foreach ($data4['kpi_data2'] as $row3) {
                                                                    $total_Proj_KPI_Target_1 += $row3['Proj_KPI_Target_1'];
                                                                    $total_Proj_KPI_Target_2 += $row3['Proj_KPI_Target_2'];
                                                                    $total_Prog_Q1_2 += $row3['Prog_Q1_2'];
                                                                    $total_Prog_Q2_2 += $row3['Prog_Q2_2'];
                                                                    $total_Prog_Q3_2 += $row3['Prog_Q3_2'];
                                                                    $total_Prog_Q4_2 += $row3['Prog_Q4_2'];
                                                                    $total_Prog_Q1_1 += $row3['Prog_Q1_1'];
                                                                    $total_Prog_Q2_1 += $row3['Prog_Q2_1'];
                                                                    $total_Prog_Q3_1 += $row3['Prog_Q3_1'];
                                                                    $total_Prog_Q4_1 += $row3['Prog_Q4_1'];
                                                                }

                                                                // คำนวณผลรวมสำหรับ `total1` และ `total2`
                                                                $total2 = $total_Prog_Q1_2 + $total_Prog_Q2_2 + $total_Prog_Q3_2 + $total_Prog_Q4_2;
                                                                $total1 = $total_Prog_Q1_1 + $total_Prog_Q2_1 + $total_Prog_Q3_1 + $total_Prog_Q4_1;

                                                                // แสดงข้อมูล Project
                                                                echo "<tr>";
                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 24) . htmlspecialchars($data4['name'] ?? '') . "</td>";
                                                                echo "<td>" . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Proj_KPI_Target_2) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Prog_Q1_2) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Prog_Q2_2) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Prog_Q3_2) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Prog_Q4_2) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total2) . "</td>";
                                                                echo "<td>" . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Proj_KPI_Target_1) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Prog_Q1_1) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Prog_Q2_1) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Prog_Q3_1) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total_Prog_Q4_1) . "</td>";
                                                                echo "<td>" . htmlspecialchars($total1) . "</td>";
                                                                echo "</tr>";

                                                                // แสดงข้อมูล KPI ของ Project
                                                                if (!empty($data4['kpi_data2'])) {
                                                                    foreach ($data4['kpi_data2'] as $row3) {
                                                                        $total2 = $row3['Prog_Q1_2'] + $row3['Prog_Q2_2'] + $row3['Prog_Q3_2'] + $row3['Prog_Q4_2'];
                                                                        $total1 = $row3['Prog_Q1_1'] + $row3['Prog_Q2_1'] + $row3['Prog_Q3_1'] + $row3['Prog_Q4_1'];
                                                                        echo "<tr>";
                                                                        echo "<td>" . str_repeat("&nbsp;", 24) . htmlspecialchars($row3['Proj_KPI_Name']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['UoM_for_Proj_KPI']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Proj_KPI_Target_1']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Prog_Q1_2']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Prog_Q2_2']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Prog_Q3_2']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Prog_Q4_2']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($total2) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['UoM_for_Proj_KPI']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Proj_KPI_Target_1']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Prog_Q1_1']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Prog_Q2_1']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Prog_Q3_1']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($row3['Prog_Q4_1']) . "</td>";
                                                                        echo "<td>" . htmlspecialchars($total1) . "</td>";
                                                                        echo "</tr>";
                                                                    }
                                                                } else {
                                                                    echo "<tr>";
                                                                    echo "<td  style='text-align:center;'>ไม่มีข้อมูล KPI</td>";
                                                                    echo "<td colspan='14' style='text-align:center;'>-</td>";
                                                                    echo "</tr>";
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                echo "<tr><td colspan='11' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                            } ?>
                                        </tbody>

                                    </table>

                                    <?php $conn = null; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        function exportCSV() {
            const table = document.getElementById('reportTable').cloneNode(true);
            const newRow = table.insertRow(0);
            const cell = newRow.insertCell(0);
            cell.colSpan = table.rows[1].cells.length;
            cell.style.textAlign = "center";
            cell.style.fontWeight = "bold";
            cell.innerText = "รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย";

            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน",
                raw: true
            });
            XLSX.writeFile(wb, 'รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.csv', {
                bookType: 'csv',
                type: 'array'
            });
        }

        function exportXLS() {
            const table = document.getElementById('reportTable').cloneNode(true);
            const newRow = table.insertRow(0);
            const cell = newRow.insertCell(0);
            cell.colSpan = table.rows[1].cells.length;
            cell.style.textAlign = "center";
            cell.style.fontWeight = "bold";
            cell.innerText = "รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย";

            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน"
            });
            XLSX.writeFile(wb, 'รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.xlsx');
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
            doc.save('รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.pdf');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>