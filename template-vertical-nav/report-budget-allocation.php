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
    'Scenario2' => 'ANL-RELEASE-1',
    'ANL-RELEASE-2',
    'Scenario3' => 'ANL-RELEASE-1',
    'ANL-RELEASE-2',
    'ANL-RELEASE-3',
    'Scenario4' => 'ANL-RELEASE-1',
    'ANL-RELEASE-2',
    'ANL-RELEASE-3',
    'ANL-RELEASE-4'
);
$scenarioValue = isset($scenarioMap[$selectedScenario]) ? $scenarioMap[$selectedScenario] : 'ANL-RELEASE-1';

// ฟังก์ชันในการดึงข้อมูลจากฐานข้อมูล
function fetchScenarioData($conn, $scenarioColumnValue, $selectedScenario)
{
    $query = "SELECT 
        bap.Service,
        bap.Plan,
        p.plan_name,
        bap.Faculty AS Faculty_Id,
        ft.Faculty,
        ft.Alias_Default AS Faculty_name,
        bap.Project,
        pj.project_id,
        pj.project_name,
        bap.Sub_Plan,
        sp.sub_plan_id,
        sp.sub_plan_name,
        bap.`Account`,
        CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)) AS a1,
        CONCAT(LEFT(bap.`Account`, 4), REPEAT('0', 6)) AS a2,
        ac.`account`,
        ac.`type`,
        ac.sub_type,
        bap.Fund,
        bap.KKU_Item_Name,
        COALESCE(bap.Allocated_Total_Amount_Quantity, 0) AS Allocated_Total_Amount_Quantity,
        COALESCE(bpd.Release_Amount, 0) AS Release_Amount,
        
        -- คำนวณ Pre_Release_Amount ตาม Scenario
CASE 
    WHEN :selectedScenario = 'Scenario1' THEN 0
    WHEN :selectedScenario = 'Scenario2' THEN 
        CASE 
            WHEN bpd.Scenario = 'ANL-RELEASE-1' THEN COALESCE(bpd.Release_Amount, 0) 
            ELSE 0 
        END
    WHEN :selectedScenario = 'Scenario3' THEN 
        CASE 
            WHEN bpd.Scenario IN ('ANL-RELEASE-1', 'ANL-RELEASE-2', 'ANL-RELEASE-3') THEN 
                (
                    SELECT COALESCE(SUM(Release_Amount), 0) 
                    FROM budget_planning_disbursement_budget_plan_anl_release AS bpd_sub
                    WHERE bpd_sub.Scenario IN ('ANL-RELEASE-1', 'ANL-RELEASE-2') 
                      AND bpd_sub.Service = bpd.Service 
                      AND bpd_sub.Faculty = bpd.Faculty 
                      AND bpd_sub.Project = bpd.Project 
                      AND bpd_sub.Plan = bpd.Plan 
                      AND bpd_sub.Sub_Plan = bpd.Sub_Plan 
                      AND bpd_sub.Account = bpd.Account
                )
            ELSE 0 
        END
    WHEN :selectedScenario = 'Scenario4' THEN 
        CASE 
            WHEN bpd.Scenario IN ('ANL-RELEASE-1', 'ANL-RELEASE-2', 'ANL-RELEASE-3','ANL-RELEASE-4') THEN 
                (
                    SELECT COALESCE(SUM(Release_Amount), 0) 
                    FROM budget_planning_disbursement_budget_plan_anl_release AS bpd_sub
                    WHERE bpd_sub.Scenario IN ('ANL-RELEASE-1', 'ANL-RELEASE-2', 'ANL-RELEASE-3') 
                      AND bpd_sub.Service = bpd.Service 
                      AND bpd_sub.Faculty = bpd.Faculty 
                      AND bpd_sub.Project = bpd.Project 
                      AND bpd_sub.Plan = bpd.Plan 
                      AND bpd_sub.Sub_Plan = bpd.Sub_Plan 
                      AND bpd_sub.Account = bpd.Account
                )
            ELSE 0 
        END
END AS Pre_Release_Amount,


        -- แยก Release_Amount ตาม Scenario
        CASE 
            WHEN bpd.Scenario = 'ANL-RELEASE-1' THEN 
                (SELECT COALESCE(Release_Amount, 0) FROM budget_planning_disbursement_budget_plan_anl_release WHERE Scenario = 'ANL-RELEASE-1' AND bpd.Service = Service AND bpd.Faculty = Faculty AND bpd.Project = Project AND bpd.Plan = Plan AND bpd.Sub_Plan = Sub_Plan AND bpd.Account = Account)
            ELSE 0 
        END AS Scenario1,

        CASE 
            WHEN bpd.Scenario IN ('ANL-RELEASE-1', 'ANL-RELEASE-2') THEN 
                (SELECT COALESCE(Release_Amount, 0) FROM budget_planning_disbursement_budget_plan_anl_release WHERE Scenario = 'ANL-RELEASE-2' AND bpd.Service = Service AND bpd.Faculty = Faculty AND bpd.Project = Project AND bpd.Plan = Plan AND bpd.Sub_Plan = Sub_Plan AND bpd.Account = Account)
            ELSE 0 
        END AS Scenario2,

        CASE 
            WHEN bpd.Scenario IN ('ANL-RELEASE-1', 'ANL-RELEASE-2', 'ANL-RELEASE-3') THEN 
                (SELECT COALESCE(Release_Amount, 0) FROM budget_planning_disbursement_budget_plan_anl_release WHERE Scenario = 'ANL-RELEASE-3' AND bpd.Service = Service AND bpd.Faculty = Faculty AND bpd.Project = Project AND bpd.Plan = Plan AND bpd.Sub_Plan = Sub_Plan AND bpd.Account = Account)
            ELSE 0 
        END AS Scenario3,

        CASE 
            WHEN bpd.Scenario IN ('ANL-RELEASE-1', 'ANL-RELEASE-2', 'ANL-RELEASE-3', 'ANL-RELEASE-4') THEN 
                (SELECT COALESCE(Release_Amount, 0) FROM budget_planning_disbursement_budget_plan_anl_release WHERE Scenario = 'ANL-RELEASE-4' AND bpd.Service = Service AND bpd.Faculty = Faculty AND bpd.Project = Project AND bpd.Plan = Plan AND bpd.Sub_Plan = Sub_Plan AND bpd.Account = Account)
            ELSE 0 
        END AS Scenario4,
        bap.Reason

    FROM 
        budget_planning_allocated_annual_budget_plan bap
        INNER JOIN Faculty ft ON bap.Faculty = ft.Faculty AND ft.parent LIKE 'Faculty%'
        LEFT JOIN plan p ON bap.Plan = p.plan_id
        LEFT JOIN sub_plan sp ON bap.Sub_Plan = sp.sub_plan_id
        LEFT JOIN project pj ON bap.Project = pj.project_id
        INNER JOIN account ac ON bap.`Account` = ac.`account`
        LEFT JOIN budget_planning_disbursement_budget_plan_anl_release bpd 
            ON  bap.Service = bpd.Service
            AND bap.Faculty = bpd.Faculty
            AND bap.Project = bpd.Project
            AND bap.Plan = bpd.Plan
            AND bap.Sub_Plan = bpd.Sub_Plan
            AND bap.`Account` = bpd.`Account`";

    if ($scenarioColumnValue) {
        $query .= " WHERE bpd.Scenario = :scenarioColumnValue";
    }

    $query .= " AND ac.id > (SELECT MAX(id) FROM account WHERE account = 'Expenses')";

    $query .= " ORDER BY bap.Faculty ASC, bap.Plan ASC, bap.Sub_Plan ASC, bap.Project ASC, 
ac.sub_type ASC, 
                bap.`Account` ASC ";

    $stmt = $conn->prepare($query);

    $stmt->execute([
        ':selectedScenario' => $selectedScenario,
        ':scenarioColumnValue' => $scenarioColumnValue
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// รับค่าจาก dropdown ถ้าไม่มีให้ใช้ค่าเริ่มต้นเป็น Scenario1
$selectedScenario = isset($_GET['scenario']) ? $_GET['scenario'] : 'Scenario1';

// ดึงข้อมูลตาม Scenario ที่เลือก
$results = fetchScenarioData($conn, scenarioColumnValue: $scenarioValue, selectedScenario: $selectedScenario);
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
                                            $previousSubPlanId = "";
                                            $previousProject = "";
                                            $previousType = "";
                                            $previousSubType = "";

                                            // Fetch data from the database
                                            $results = fetchScenarioData($conn, scenarioColumnValue: $scenarioValue, selectedScenario: $selectedScenario);

                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                $summary = [];

                                                foreach ($results as $row) {

                                                    $Faculty = $row["Faculty_Id"];
                                                    $plan = $row['Plan'];
                                                    $subPlan = $row['Sub_Plan'];
                                                    $project = $row['project_name'];
                                                    $Type = $row['type'];
                                                    $subType = $row['sub_type'];



                                                    // เก็บข้อมูลของ Faculty
                                                    if (!isset($summary[$Faculty])) {
                                                        $summary[$Faculty] = [
                                                            'Faculty_name' => $row['Faculty'] ?? '',
                                                            'FacultyName' => $row['Faculty_name'] ?? '',
                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'plan' => [], // เก็บข้อมูลของ Plan
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Plan
                                                    if (!isset($summary[$Faculty]['plan'][$plan])) {
                                                        $summary[$Faculty]['plan'][$plan] = [
                                                            'PlanName' => $row['plan_name'] ?? '',
                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'sub_plan' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Sub_Plan
                                                    if (!isset($summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan])) {
                                                        $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan] = [
                                                            'Sub_Plan' => $row['Sub_Plan'] ?? '',
                                                            'SubPlanName' => $row['sub_plan_name'] ?? '',
                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'project' => [], // เก็บข้อมูลของ Project
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Project
                                                    if (!isset($summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project])) {
                                                        $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project] = [
                                                            'a2' => $row['a2'] ?? '',
                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'type' => [], // เก็บข้อมูลของ Sub_Type
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Type
                                                    if (!isset($summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type])) {
                                                        $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type] = [
                                                            'a1' => $row['a1'] ?? '',
                                                            'Type' => $row['type'] ?? '',
                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'sub_type' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Type
                                                    if (!isset($summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType])) {
                                                        $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType] = [
                                                            'a2' => $row['a2'] ?? '',
                                                            'SubType' => $row['sub_type'] ?? '',
                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'kku_items' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }



                                                    // รวมข้อมูลของ Faculty
                                                    $summary[$Faculty]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['Scenario4'] += $row['Scenario4'];

                                                    // รวมข้อมูลของ plans
                                                    $summary[$Faculty]['plan'][$plan]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['Scenario4'] += $row['Scenario4'];

                                                    // รวมข้อมูลของ Sub_Plan
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['Scenario4'] += $row['Scenario4'];

                                                    // รวมข้อมูลของ Project
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Scenario4'] += $row['Scenario4'];

                                                    // รวมข้อมูลของ Type
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['Scenario4'] += $row['Scenario4'];

                                                    // รวมข้อมูลของ Sub_Type
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType]['Scenario4'] += $row['Scenario4'];





                                                    // เก็บข้อมูลของ KKU_Item_Name
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "<strong>" . htmlspecialchars($row['Account'] ?? '') . "</strong> : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                        : "<strong>" . htmlspecialchars($row['Account'] ?? '') . "</strong>";

                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['type'][$Type]['sub_type'][$subType]['kku_items'][] = [
                                                        'name' => $kkuItemName,
                                                        'Allocated_Total_Amount_Quantity' => $row['Allocated_Total_Amount_Quantity'],
                                                        'Release_Amount' => $row['Release_Amount'],
                                                        'Pre_Release_Amount' => $row['Pre_Release_Amount'],
                                                        'Scenario1' => $row['Scenario1'],
                                                        'Scenario2' => $row['Scenario2'],
                                                        'Scenario3' => $row['Scenario3'],
                                                        'Scenario4' => $row['Scenario4'],
                                                        'Reason' => $row['Reason'],

                                                    ];
                                                    $rows = $summary;
                                                    // ตัวแปรสำหรับเก็บผลรวมทั้งหมด
                                                    $total_summary = [
                                                        'Allocated_Total_Amount_Quantity' => 0,
                                                        'Release_Amount' => 0,
                                                        'Pre_Release_Amount' => 0,
                                                        'Scenario1' => 0,
                                                        'Scenario2' => 0,
                                                        'Scenario3' => 0,
                                                        'Scenario4' => 0,
                                                    ];
                                                    // แสดงผลรวมทั้งหมด
                                                    //print_r($total_summary);
                                                    // Assuming this is inside a loop where $row is updated (e.g., from a database query)
                                                    foreach ($rows as $row) { // Replace $rows with your actual data source
                                                        // รวมผลรวมทั้งหมดโดยไม่สนใจ Faculty
                                                        $total_summary['Allocated_Total_Amount_Quantity'] += (float) ($row['Allocated_Total_Amount_Quantity'] ?? 0);
                                                        $total_summary['Release_Amount'] += (float) ($row['Release_Amount'] ?? 0);
                                                        $total_summary['Pre_Release_Amount'] += (float) ($row['Pre_Release_Amount'] ?? 0);
                                                        $total_summary['Scenario1'] += (float) ($row['Scenario1'] ?? 0);
                                                        $total_summary['Scenario2'] += (float) ($row['Scenario2'] ?? 0);
                                                        $total_summary['Scenario3'] += (float) ($row['Scenario3'] ?? 0);
                                                        $total_summary['Scenario4'] += (float) ($row['Scenario4'] ?? 0);
                                                    }
                                                }


                                                // ตรวจสอบว่ามีข้อมูลใน $summary หรือไม่
                                                if (isset($summary) && is_array($summary)) {
                                                    // คำนวณค่า Pre_Release_Amount
                                                    $preReleaseAmount = $total_summary['Pre_Release_Amount'] ?? 0;

                                                    // รับค่า Scenario ที่ผู้ใช้เลือก (ถ้ามี)
                                                    $selectedScenario = $_GET['scenario'] ?? 'Scenario1';

                                                    // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                    $finalPreReleaseAmount = $preReleaseAmount;
                                                    switch ($selectedScenario) {
                                                        case 'Scenario1':
                                                            $finalPreReleaseAmount += $total_summary['Scenario1'] ?? 0;
                                                            break;
                                                        case 'Scenario2':
                                                            $finalPreReleaseAmount += $total_summary['Scenario2'] ?? 0;
                                                            break;
                                                        case 'Scenario3':
                                                            $finalPreReleaseAmount += $total_summary['Scenario3'] ?? 0;
                                                            break;
                                                        case 'Scenario4':
                                                            $finalPreReleaseAmount += $total_summary['Scenario4'] ?? 0;
                                                            break;
                                                        default:
                                                            break;
                                                    }

                                                    // แสดงผลลัพธ์ในรูปแบบตาราง
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left;'><strong>รวมทั้งสิ้น</strong></td>";
                                                    // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                    echo "<td>" . formatNumber($total_summary['Allocated_Total_Amount_Quantity'] ?? 0) . "</td>";
                                                    echo "<td>" . formatNumber($preReleaseAmount) . "</td>";
                                                    echo "<td>" . formatNumber($total_summary[$selectedScenario] ?? 0) . "</td>";
                                                    echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                    // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                    $subtractedAmount = ($total_summary['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                    echo "<td>" . formatNumber($subtractedAmount) . "</td>";
                                                    echo "<td></td>"; // คอลัมน์ว่าง (ถ้ามี)
                                                    echo "</tr>";
                                                } else {
                                                    // แสดงข้อความหากไม่มีข้อมูล
                                                    echo "<tr><td colspan='7' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                                }

                                                // แสดงผลลัพธ์ในรูปแบบตาราง
                                                foreach ($summary as $Faculty => $FacultyData) {


                                                    $preReleaseAmount = $FacultyData['Pre_Release_Amount'] ?? 0;

                                                    $selectedScenario = $_GET['scenario'] ?? 'Scenario1';

                                                    // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                    $finalPreReleaseAmount = $preReleaseAmount;
                                                    switch ($selectedScenario) {
                                                        case 'Scenario1':
                                                            $finalPreReleaseAmount += $FacultyData['Scenario1'] ?? 0;
                                                            break;
                                                        case 'Scenario2':
                                                            $finalPreReleaseAmount += $FacultyData['Scenario2'] ?? 0;
                                                            break;
                                                        case 'Scenario3':
                                                            $finalPreReleaseAmount += $FacultyData['Scenario3'] ?? 0;
                                                            break;
                                                        case 'Scenario4':
                                                            $finalPreReleaseAmount += $FacultyData['Scenario4'] ?? 0;
                                                            break;
                                                        default:
                                                            break;
                                                    }
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left;'><strong>" . htmlspecialchars($FacultyData['FacultyName'] ?? '') . "</strong></td>";
                                                    // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                    echo "<td>" . formatNumber($FacultyData['Allocated_Total_Amount_Quantity']) . "</td>";
                                                    echo "<td>" . formatNumber($FacultyData['Pre_Release_Amount']) . "</td>";
                                                    echo "<td>" . formatNumber($FacultyData[$selectedScenario] ?? 0) . "</td>";
                                                    echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                    // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                    $subtractedAmount = ($FacultyData['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                    echo "<td>" . formatNumber($subtractedAmount) . "</td>";
                                                    echo "<td>" . "</td>";
                                                    echo "</tr>";

                                                    foreach ($FacultyData['plan'] as $plan => $planData) {
                                                        $cleanedSubPlan = preg_replace('/^SP_/', '', $plan);

                                                        $preReleaseAmount = $planData['Pre_Release_Amount'] ?? 0;

                                                        $selectedScenario = $_GET['scenario'] ?? 'Scenario1';

                                                        // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                        $finalPreReleaseAmount = $preReleaseAmount;
                                                        switch ($selectedScenario) {
                                                            case 'Scenario1':
                                                                $finalPreReleaseAmount += $planData['Scenario1'] ?? 0;
                                                                break;
                                                            case 'Scenario2':
                                                                $finalPreReleaseAmount += $planData['Scenario2'] ?? 0;
                                                                break;
                                                            case 'Scenario3':
                                                                $finalPreReleaseAmount += $planData['Scenario3'] ?? 0;
                                                                break;
                                                            case 'Scenario4':
                                                                $finalPreReleaseAmount += $planData['Scenario4'] ?? 0;
                                                                break;
                                                            default:
                                                                break;
                                                        }
                                                        echo "<tr>";
                                                        // แสดงผลข้อมูล
                                                        echo "<td style='text-align: left;'><strong>" . str_repeat("&nbsp;", times: 8) . htmlspecialchars($cleanedSubPlan) . htmlspecialchars($planData['PlanName'] ?? '') . "</strong></td>";
                                                        // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                        echo "<td>" . formatNumber($planData['Allocated_Total_Amount_Quantity']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Pre_Release_Amount']) . "</td>";
                                                        echo "<td>" . formatNumber($planData[$selectedScenario] ?? 0) . "</td>";
                                                        echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                        // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                        $subtractedAmount = ($planData['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                        echo "<td>" . formatNumber($subtractedAmount) . "</td>";
                                                        echo "<td>" . "</td>";
                                                        echo "</tr>";

                                                        foreach ($planData['sub_plan'] as $subPlan => $subPlanData) {
                                                            // ลบ 'SP_' ที่อยู่หน้าสุดของข้อความ
                                                            $cleanedSubPlan = preg_replace('/^SP_/', '', $subPlan);
                                                            // ดึงค่า Pre_Release_Amount และ Scenario ที่รวมไว้
                                                            $preReleaseAmount = $subPlanData['Pre_Release_Amount'] ?? 0;

                                                            $selectedScenario = $_GET['scenario'] ?? 'Scenario1';

                                                            // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                            $finalPreReleaseAmount = $preReleaseAmount;
                                                            switch ($selectedScenario) {
                                                                case 'Scenario1':
                                                                    $finalPreReleaseAmount += $subPlanData['Scenario1'] ?? 0;
                                                                    break;
                                                                case 'Scenario2':
                                                                    $finalPreReleaseAmount += $subPlanData['Scenario2'] ?? 0;
                                                                    break;
                                                                case 'Scenario3':
                                                                    $finalPreReleaseAmount += $subPlanData['Scenario3'] ?? 0;
                                                                    break;
                                                                case 'Scenario4':
                                                                    $finalPreReleaseAmount += $subPlanData['Scenario4'] ?? 0;
                                                                    break;
                                                                default:
                                                                    break;
                                                            }
                                                            echo "<tr>";
                                                            // แสดงผลข้อมูล
                                                            echo "<td style='text-align: left;'><strong>" . str_repeat("&nbsp;", 16) . htmlspecialchars($cleanedSubPlan) . "</strong> : " . htmlspecialchars($subPlanData['SubPlanName'] ?? '') . "<br></td>";
                                                            // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                            echo "<td>" . formatNumber($subPlanData['Allocated_Total_Amount_Quantity']) . "</td>";
                                                            echo "<td>" . formatNumber($subPlanData['Pre_Release_Amount']) . "</td>";
                                                            echo "<td>" . formatNumber($subPlanData[$selectedScenario] ?? 0) . "</td>";
                                                            echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                            // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                            $subtractedAmount = ($subPlanData['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                            echo "<td>" . formatNumber($subtractedAmount) . "</td>";
                                                            echo "<td>" . "</td>";
                                                            echo "</tr>";

                                                            foreach ($subPlanData['project'] as $project => $projectData) {


                                                                // ดึงค่า Pre_Release_Amount และ Scenario ที่รวมไว้
                                                                $preReleaseAmount = $projectData['Pre_Release_Amount'] ?? 0;

                                                                $selectedScenario = $_GET['scenario'] ?? 'Scenario1';

                                                                // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                                $finalPreReleaseAmount = $preReleaseAmount;
                                                                switch ($selectedScenario) {
                                                                    case 'Scenario1':
                                                                        $finalPreReleaseAmount += $projectData['Scenario1'] ?? 0;
                                                                        break;
                                                                    case 'Scenario2':
                                                                        $finalPreReleaseAmount += $projectData['Scenario2'] ?? 0;
                                                                        break;
                                                                    case 'Scenario3':
                                                                        $finalPreReleaseAmount += $projectData['Scenario3'] ?? 0;
                                                                        break;
                                                                    case 'Scenario4':
                                                                        $finalPreReleaseAmount += $projectData['Scenario4'] ?? 0;
                                                                        break;
                                                                    default:
                                                                        break;
                                                                }
                                                                echo "<tr>";
                                                                echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 24) . htmlspecialchars($project) . "</strong></td>";
                                                                // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                                echo "<td>" . formatNumber($projectData['Allocated_Total_Amount_Quantity']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Pre_Release_Amount']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData[$selectedScenario] ?? 0) . "</td>";
                                                                echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                                // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                                $subtractedAmount = ($projectData['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                                echo "<td>" . formatNumber($subtractedAmount) . "</td>";
                                                                echo "<td>" . "</td>";
                                                                echo "</tr>";

                                                                foreach ($projectData['type'] as $Type => $TypeData) {
                                                                    $cleanedSubType = preg_replace('/^[\d.]+\s*/', '', $TypeData['Type']);
                                                                    // ดึงค่า Pre_Release_Amount และ Scenario ที่รวมไว้
                                                                    $preReleaseAmount = $TypeData['Pre_Release_Amount'] ?? 0;
                                                                    $selectedScenario = $_GET['scenario'] ?? 'Scenario1';
                                                                    // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                                    $finalPreReleaseAmount = $preReleaseAmount;
                                                                    switch ($selectedScenario) {
                                                                        case 'Scenario1':
                                                                            $finalPreReleaseAmount += $TypeData['Scenario1'] ?? 0;
                                                                            break;
                                                                        case 'Scenario2':
                                                                            $finalPreReleaseAmount += $TypeData['Scenario2'] ?? 0;
                                                                            break;
                                                                        case 'Scenario3':
                                                                            $finalPreReleaseAmount += $TypeData['Scenario3'] ?? 0;
                                                                            break;
                                                                        case 'Scenario4':
                                                                            $finalPreReleaseAmount += $TypeData['Scenario4'] ?? 0;
                                                                            break;
                                                                        default:
                                                                            break;
                                                                    }
                                                                    // แสดงข้อมูลของ Sub_Type
                                                                    echo "<tr>";
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . htmlspecialchars($TypeData['a1'] ?? '') . ' : ' . htmlspecialchars($cleanedSubType ?? '') . "</td>";
                                                                    // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                                    echo "<td>" . formatNumber($TypeData['Allocated_Total_Amount_Quantity']) . "</td>";
                                                                    echo "<td>" . formatNumber($TypeData['Pre_Release_Amount']) . "</td>";
                                                                    echo "<td>" . formatNumber($TypeData[$selectedScenario] ?? 0) . "</td>";
                                                                    echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                                    // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                                    $subtractedAmount = ($TypeData['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                                    echo "<td>" . formatNumber($subtractedAmount) . "</td>";

                                                                    echo "<td>" . "</td>";
                                                                    echo "</tr>";



                                                                    foreach ($TypeData['sub_type'] as $subType => $subTypeData) {
                                                                        $cleanedSubType = preg_replace('/^[\d.]+\s*/', '', $subTypeData['SubType']);
                                                                        // ดึงค่า Pre_Release_Amount และ Scenario ที่รวมไว้
                                                                        $preReleaseAmount = $subTypeData['Pre_Release_Amount'] ?? 0;
                                                                        $selectedScenario = $_GET['scenario'] ?? 'Scenario1';
                                                                        // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                                        $finalPreReleaseAmount = $preReleaseAmount;
                                                                        switch ($selectedScenario) {
                                                                            case 'Scenario1':
                                                                                $finalPreReleaseAmount += $subTypeData['Scenario1'] ?? 0;
                                                                                break;
                                                                            case 'Scenario2':
                                                                                $finalPreReleaseAmount += $subTypeData['Scenario2'] ?? 0;
                                                                                break;
                                                                            case 'Scenario3':
                                                                                $finalPreReleaseAmount += $subTypeData['Scenario3'] ?? 0;
                                                                                break;
                                                                            case 'Scenario4':
                                                                                $finalPreReleaseAmount += $subTypeData['Scenario4'] ?? 0;
                                                                                break;
                                                                            default:
                                                                                break;
                                                                        }

                                                                        // แสดงข้อมูลของ Sub_Type
                                                                        echo "<tr>";
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 40) . htmlspecialchars($subTypeData['a2'] ?? '') . ' : ' . htmlspecialchars($cleanedSubType ?? '') . "</td>";
                                                                        // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                                        echo "<td>" . formatNumber($subTypeData['Allocated_Total_Amount_Quantity']) . "</td>";
                                                                        echo "<td>" . formatNumber($subTypeData['Pre_Release_Amount']) . "</td>";
                                                                        echo "<td>" . formatNumber($subTypeData[$selectedScenario] ?? 0) . "</td>";
                                                                        echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                                        // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                                        $subtractedAmount = ($subTypeData['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                                        echo "<td>" . formatNumber($subtractedAmount) . "</td>";

                                                                        echo "<td>" . "</td>";
                                                                        echo "</tr>";

                                                                        foreach ($subTypeData['kku_items'] as $kkuItem) {
                                                                            echo "<tr>";
                                                                            echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 48) . $kkuItem['name'] . "</td>";

                                                                            $preReleaseAmount = $kkuItem['Pre_Release_Amount'];

                                                                            $selectedScenario = $_GET['scenario'] ?? 'Scenario1';

                                                                            $finalPreReleaseAmount = $preReleaseAmount;


                                                                            switch ($selectedScenario) {
                                                                                case 'Scenario1':
                                                                                    $finalPreReleaseAmount += $kkuItem['Scenario1'];
                                                                                    break;
                                                                                case 'Scenario2':
                                                                                    $finalPreReleaseAmount += $kkuItem['Scenario2'];
                                                                                    break;
                                                                                case 'Scenario3':
                                                                                    $finalPreReleaseAmount += +$kkuItem['Scenario3'];
                                                                                    break;
                                                                                case 'Scenario4':
                                                                                    $finalPreReleaseAmount += $kkuItem['Scenario4'];
                                                                                    break;
                                                                                default:
                                                                                    break;
                                                                            }

                                                                            // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                                            echo "<td>" . formatNumber($kkuItem['Allocated_Total_Amount_Quantity']) . "</td>";
                                                                            echo "<td>" . formatNumber($kkuItem['Pre_Release_Amount']) . "</td>";
                                                                            echo "<td>" . formatNumber(isset($kkuItem[$selectedScenario]) ? $kkuItem[$selectedScenario] : 0) . "</td>";
                                                                            echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                                            $subtractedAmount = $kkuItem['Allocated_Total_Amount_Quantity'] - $finalPreReleaseAmount;
                                                                            echo "<td>" . formatNumber($subtractedAmount) . "</td>";
                                                                            echo "<td >" .
                                                                                (!empty($kkuItem['Reason']) ? htmlspecialchars($kkuItem['Reason']) : '') .
                                                                                "</td>";

                                                                            echo "</tr>";
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                echo "<tr><td colspan='11' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
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
            const csvRows = [];

            // 1) ดึงข้อมูลจาก <thead>
            const theadRows = table.tHead.rows;
            for (const row of theadRows) {
                const cellLines = [];
                let maxSubLine = 1;

                for (const cell of row.cells) {
                    let html = cell.innerHTML;

                    // แปลง &nbsp; เป็นช่องว่าง
                    html = html.replace(/(&nbsp;)+/g, (match) => {
                        const count = match.match(/&nbsp;/g).length;
                        return '\u00A0'.repeat(count);
                    });

                    // ลบ tag HTML อื่นออก
                    html = html.replace(/<\/?[^>]+>/g, '');

                    // แยกเป็น array บรรทัดย่อย
                    const lines = html.split('\n').map(x => x.trimEnd());

                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }

                    cellLines.push(lines);
                }

                // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];

                    for (const lines of cellLines) {
                        let text = lines[i] || '';
                        text = text.replace(/"/g, '""');
                        text = `"${text}"`;
                        rowData.push(text);
                    }

                    csvRows.push(rowData.join(','));
                }
            }

            // 2) ดึงข้อมูลจาก <tbody>
            const tbodyRows = table.tBodies[0].rows;
            for (const row of tbodyRows) {
                const cellLines = [];
                let maxSubLine = 1;

                for (const cell of row.cells) {
                    let html = cell.innerHTML;

                    // แปลง &nbsp; เป็นช่องว่าง
                    html = html.replace(/(&nbsp;)+/g, (match) => {
                        const count = match.match(/&nbsp;/g).length;
                        return '\u00A0'.repeat(count);
                    });

                    // ลบ tag HTML อื่นออก
                    html = html.replace(/<\/?[^>]+>/g, '');

                    // แยกเป็น array บรรทัดย่อย
                    const lines = html.split('\n').map(x => x.trimEnd());

                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }

                    cellLines.push(lines);
                }

                // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];

                    for (const lines of cellLines) {
                        let text = lines[i] || '';
                        text = text.replace(/"/g, '""');
                        text = `"${text}"`;
                        rowData.push(text);
                    }

                    csvRows.push(rowData.join(','));
                }
            }

            // 3) รวมเป็น CSV + BOM
            const csvContent = "\uFEFF" + csvRows.join("\n");
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานการจัดสรรเงินรายงวด.csv';
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
            doc.text("รายงานการจัดสรรเงินรายงวด", 10, 500);

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
            doc.save('รายงานการจัดสรรเงินรายงวด.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const { theadRows, theadMerges } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br/>, ไม่ merge) ============
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
            const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานการจัดสรรเงินรายงวด.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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
                return { theadRows, theadMerges };
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
                            s: { r: rowIndex, c: colIndex },
                            e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 }
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

            return { theadRows, theadMerges };
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
                    const cell_address = XLSX.utils.encode_cell({ r: R, c: C });
                    if (!ws[cell_address]) continue;

                    if (!ws[cell_address].s) ws[cell_address].s = {};
                    ws[cell_address].s.alignment = { vertical: verticalAlign };
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