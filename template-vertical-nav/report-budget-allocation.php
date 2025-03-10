<style>
    #reportTable {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Arial', sans-serif;
        font-size: 16px;

    }

    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th,
    #reportTable td {
        text-align: left;
        vertical-align: top;
        white-space: nowrap;
        padding: 8px;

    }

    /* ป้องกัน Head Table หายไปเวลา Scroll */
    .table-responsive {
        max-height: 70vh;
        overflow-y: auto;

    }

    /* Sticky Header */
    #reportTable thead th {
        position: sticky;
        top: 0;
        background: #f4f4f4;
        z-index: 100;
    }
</style>
<?php
include('../component/header.php');
include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();
$faculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
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
function fetchScenarioData($conn, $faculty = null, $scenarioColumnValue, $selectedScenario)
{
    $query = "WITH RECURSIVE account_hierarchy AS (
    -- Anchor member: เริ่มจาก account ทุกตัว
    SELECT 
        a1.account,
        a1.account AS account1, -- account สำหรับ level1
        a1.alias_default AS level1,
        a1.parent,
        CAST(NULL AS CHAR(255)) AS account2, -- account สำหรับ level2
        CAST(NULL AS CHAR(255)) AS level2,
        CAST(NULL AS CHAR(255)) AS account3, -- account สำหรับ level3
        CAST(NULL AS CHAR(255)) AS level3,
        CAST(NULL AS CHAR(255)) AS account4, -- account สำหรับ level4
        CAST(NULL AS CHAR(255)) AS level4,
        CAST(NULL AS CHAR(255)) AS account5, -- account สำหรับ level5
        CAST(NULL AS CHAR(255)) AS level5,
        1 AS depth
    FROM account a1
    WHERE a1.parent IS NOT NULL
    UNION ALL
    -- Recursive member: หา parent ต่อไปเรื่อยๆ
    SELECT 
        ah.account,
        ah.account1,
        ah.level1,
        a2.parent,
        CASE WHEN ah.depth = 1 THEN a2.account ELSE ah.account2 END AS account2,
        CASE WHEN ah.depth = 1 THEN a2.alias_default ELSE ah.level2 END AS level2,
        CASE WHEN ah.depth = 2 THEN a2.account ELSE ah.account3 END AS account3,
        CASE WHEN ah.depth = 2 THEN a2.alias_default ELSE ah.level3 END AS level3,
        CASE WHEN ah.depth = 3 THEN a2.account ELSE ah.account4 END AS account4,
        CASE WHEN ah.depth = 3 THEN a2.alias_default ELSE ah.level4 END AS level4,
        CASE WHEN ah.depth = 4 THEN a2.account ELSE ah.account5 END AS account5,
        CASE WHEN ah.depth = 4 THEN a2.alias_default ELSE ah.level5 END AS level5,
        ah.depth + 1 AS depth
    FROM account_hierarchy ah
    JOIN account a2 
        ON ah.parent = a2.account COLLATE UTF8MB4_GENERAL_CI
    WHERE ah.parent IS NOT NULL
    AND ah.depth < 5 -- จำกัดระดับสูงสุดที่ 5
),
-- หาความลึกสูงสุดสำหรับแต่ละ account
hierarchy_with_max AS (
    SELECT 
        account,
        account1 AS CurrentAccount,
        level1 AS Current,
        account2 AS ParentAccount,
        level2 AS Parent,
        account3 AS GrandparentAccount,
        level3 AS Grandparent,
        account4 AS GreatGrandparentAccount,
        level4 AS GreatGrandparent,
        account5 AS GreatGreatGrandparentAccount,
        level5 AS GreatGreatGrandparent,
        depth,
        MAX(depth) OVER (PARTITION BY account) AS max_depth
    FROM account_hierarchy
)
-- เลือกเฉพาะแถวที่ depth = max_depth สำหรับแต่ละ account
,main AS (
    SELECT 
        CurrentAccount,
        Current,
        ParentAccount,
        Parent,
        GrandparentAccount,
        Grandparent,
        GreatGrandparentAccount,
        GreatGrandparent,
        GreatGreatGrandparentAccount,
        GreatGreatGrandparent,
        depth AS TotalLevels
    FROM hierarchy_with_max
    WHERE depth = max_depth
    ORDER BY account
),t1 AS(SELECT 
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
        bap.Reason,    CASE 
    WHEN m.TotalLevels = 5 THEN m.GreatGrandparentAccount
    WHEN m.TotalLevels = 4 THEN m.GrandparentAccount
    WHEN m.TotalLevels = 3 THEN m.ParentAccount
END AS a1,

CASE 
    WHEN m.TotalLevels = 5 THEN m.GrandparentAccount
    WHEN m.TotalLevels = 4 THEN m.ParentAccount
    WHEN m.TotalLevels = 3 THEN m.CurrentAccount
END AS a2,

COALESCE(
    CASE  
        WHEN m.TotalLevels = 5 THEN m.ParentAccount
        WHEN m.TotalLevels = 4 THEN m.CurrentAccount
        WHEN m.TotalLevels = 3 THEN NULL
    END,
    bap.Account -- หากผลลัพธ์เป็น NULL ให้ใช้ค่า bap.Account
) AS a3
,

COALESCE(
    CASE  
        WHEN m.TotalLevels = 5 THEN m.CurrentAccount
        WHEN m.TotalLevels = 4 THEN NULL
        WHEN m.TotalLevels = 3 THEN NULL
    END,
    bap.Account -- หากผลลัพธ์เป็น NULL ให้ใช้ค่า bap.Account
) AS a4
,
        CASE  
    WHEN m.TotalLevels = 5 THEN COALESCE(m.GreatGrandparent, bap.KKU_Item_Name)
    WHEN m.TotalLevels = 4 THEN COALESCE(m.Grandparent, bap.KKU_Item_Name)
    WHEN m.TotalLevels = 3 THEN COALESCE(m.Parent, bap.KKU_Item_Name)
END AS Name_a1,

CASE 
    WHEN (m.TotalLevels = 5 AND COALESCE(m.GreatGrandparent, bap.KKU_Item_Name) = bap.KKU_Item_Name) 
         OR (m.TotalLevels = 4 AND COALESCE(m.Grandparent, bap.KKU_Item_Name) = bap.KKU_Item_Name) 
         OR (m.TotalLevels = 3 AND COALESCE(m.Parent, bap.KKU_Item_Name) = bap.KKU_Item_Name)
    THEN NULL
    WHEN m.TotalLevels = 5 THEN COALESCE(m.Grandparent, bap.KKU_Item_Name)
    WHEN m.TotalLevels = 4 THEN COALESCE(m.Parent, bap.KKU_Item_Name)
    WHEN m.TotalLevels = 3 THEN COALESCE(m.Current, bap.KKU_Item_Name)
END AS Name_a2,

COALESCE(
    CASE  
        WHEN (m.TotalLevels = 5 AND COALESCE(m.Grandparent, bap.KKU_Item_Name) = bap.KKU_Item_Name)
             OR (m.TotalLevels = 4 AND COALESCE(m.Parent, bap.KKU_Item_Name) = bap.KKU_Item_Name)
             OR (m.TotalLevels = 3 AND COALESCE(m.Current, bap.KKU_Item_Name) = bap.KKU_Item_Name)
        THEN bap.KKU_Item_Name  -- เปลี่ยนจาก NULL เป็น bap.KKU_Item_Name
        WHEN m.TotalLevels = 5 THEN COALESCE(m.Parent, bap.KKU_Item_Name)
        WHEN m.TotalLevels = 4 THEN COALESCE(m.Current, bap.KKU_Item_Name)
    END,
    bap.KKU_Item_Name -- หากผลลัพธ์เป็น NULL ให้ใช้ค่า bap.KKU_Item_Name
) AS Name_a3,


CASE
    WHEN (
        COALESCE(
            CASE  
                WHEN (m.TotalLevels = 5 AND COALESCE(m.Grandparent, bap.KKU_Item_Name) = bap.KKU_Item_Name)
                     OR (m.TotalLevels = 4 AND COALESCE(m.Parent, bap.KKU_Item_Name) = bap.KKU_Item_Name)
                     OR (m.TotalLevels = 3 AND COALESCE(m.Current, bap.KKU_Item_Name) = bap.KKU_Item_Name)
                THEN bap.KKU_Item_Name  
                WHEN m.TotalLevels = 5 THEN COALESCE(m.Parent, bap.KKU_Item_Name)
                WHEN m.TotalLevels = 4 THEN COALESCE(m.Current, bap.KKU_Item_Name)
            END,
            bap.KKU_Item_Name
        ) = bap.KKU_Item_Name
    )
    THEN NULL
    ELSE COALESCE(
        CASE  
            WHEN (m.TotalLevels = 5 AND COALESCE(m.Parent, bap.KKU_Item_Name) = bap.KKU_Item_Name)
                 OR (m.TotalLevels = 4 AND COALESCE(m.Current, bap.KKU_Item_Name) = bap.KKU_Item_Name)
            THEN NULL
            WHEN m.TotalLevels = 5 THEN COALESCE(m.Current, bap.KKU_Item_Name)
        END,
        bap.KKU_Item_Name
    )
END AS Name_a4

    FROM 
        budget_planning_allocated_annual_budget_plan bap
        INNER JOIN Faculty ft ON bap.Faculty = ft.Faculty AND ft.parent LIKE 'Faculty%'
        LEFT JOIN plan p ON bap.Plan = p.plan_id
        LEFT JOIN sub_plan sp ON bap.Sub_Plan = sp.sub_plan_id
        LEFT JOIN project pj ON bap.Project = pj.project_id
        LEFT JOIN main m ON bap.`Account` = m.CurrentAccount
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
    if ($faculty) {
        $query .= " AND bpd.Faculty = :faculty"; // กรองตาม Faculty ที่เลือก
    }
    $query .= " AND ac.id > (SELECT MAX(id) FROM account WHERE parent = 'Expenses')
       
     ORDER BY bap.Faculty ASC, bap.Plan ASC, bap.Sub_Plan ASC, bap.Project ASC, 
    ac.sub_type ASC,bap.`Account` ASC)
SELECT * FROM t1";

    $stmt = $conn->prepare($query);

    // ตรวจสอบว่า $faculty มีค่าหรือไม่
    if (!empty($faculty)) {
        $stmt->bindParam(':faculty', $faculty, PDO::PARAM_STR);
    }

    // ใช้ execute แบบ array ต้องแน่ใจว่าใส่ทุกค่าที่ใช้ใน $query
    $stmt->execute([
        ':selectedScenario' => $selectedScenario,
        ':scenarioColumnValue' => $scenarioColumnValue,
        ':faculty' => $faculty // เพิ่ม faculty เข้าไปด้วย
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}



// รับค่าจาก dropdown ถ้าไม่มีให้ใช้ค่าเริ่มต้นเป็น Scenario1
$selectedScenario = isset($_GET['scenario']) ? $_GET['scenario'] : 'Scenario1';

// ดึงข้อมูลตาม Scenario ที่เลือก
$results = fetchScenarioData($conn, $faculty, scenarioColumnValue: $scenarioValue, selectedScenario: $selectedScenario);

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

function fetchYearsData($conn)
{
    $query = "SELECT DISTINCT Budget_Management_Year 
              FROM budget_planning_annual_budget_plan 
              ORDER BY Budget_Management_Year DESC"; // ดึงปีจากฐานข้อมูล และเรียงลำดับจากปีล่าสุด
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

                                <?php
                                // ดึงข้อมูลจากฐานข้อมูล
                                $faculties = fetchFacultyData($conn);  // ดึงข้อมูล Faculty
                                $years = fetchYearsData($conn);  // ดึงข้อมูลปีจากฐานข้อมูล
                                $scenarios = fetchScenariosData($conn);  // ดึงข้อมูล Scenario
                                
                                // ตรวจสอบค่าที่ส่งมาจากฟอร์ม
                                $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';
                                $selectedScenario = isset($_GET['scenario']) ? $_GET['scenario'] : '';
                                $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
                                $selectedAllocation = isset($_GET['allocation']) ? $_GET['allocation'] : '';
                                ?>

                                <!-- ฟอร์มค้นหา -->
                                <form method="GET" action="">
                                    <!-- Dropdown สำหรับเลือกส่วนงาน/หน่วยงาน -->
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="faculty" class="label-faculty"
                                            style="margin-right: 10px;">เลือกส่วนงาน/หน่วยงาน</label>
                                        <select name="faculty" id="faculty" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือกทุกส่วนงาน</option>
                                            <?php
                                            foreach ($faculties as $faculty) {
                                                $facultyName = htmlspecialchars($faculty['Faculty_Name']);
                                                $facultyCode = htmlspecialchars($faculty['Faculty']);
                                                $selected = ($selectedFaculty == $facultyCode) ? 'selected' : '';
                                                echo "<option value=\"$facultyCode\" $selected>$facultyName</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Dropdown สำหรับเลือกประเภทงบประมาณ -->
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="scenario" class="label-scenario"
                                            style="margin-right: 10px;">เลือกประเภทงบประมาณ</label>
                                        <select name="scenario" id="scenario" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือกทุกประเภทงบประมาณ</option>
                                            <?php
                                            foreach ($scenarios as $scenario) {
                                                $scenarioName = htmlspecialchars($scenario['Scenario']);
                                                $scenarioCode = htmlspecialchars($scenario['Scenario']);
                                                $selected = ($selectedScenario == $scenarioCode) ? 'selected' : '';
                                                echo "<option value=\"$scenarioCode\" $selected>$scenarioName</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Dropdown สำหรับเลือกปีงบประมาณ -->
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="year" class="label-year"
                                            style="margin-right: 10px;">เลือกปีงบประมาณ</label>
                                        <select name="year" id="year" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือกปีงบประมาณ</option>
                                            <?php
                                            foreach ($years as $year) {
                                                $yearValue = htmlspecialchars($year['Budget_Management_Year']);
                                                $selected = ($selectedYear == $yearValue) ? 'selected' : '';
                                                echo "<option value=\"$yearValue\" $selected>$yearValue</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Dropdown สำหรับเลือกจัดสรรงวดที่ -->
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="allocation" class="label-allocation"
                                            style="margin-right: 10px;">เลือกจัดสรรงวดที่:</label>
                                        <select name="allocation" id="allocation" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="Scenario1" <?php echo ($selectedAllocation == 'Scenario1') ? 'selected' : ''; ?>>จัดสรรงวดที่ 1</option>
                                            <option value="Scenario2" <?php echo ($selectedAllocation == 'Scenario2') ? 'selected' : ''; ?>>จัดสรรงวดที่ 2</option>
                                            <option value="Scenario3" <?php echo ($selectedAllocation == 'Scenario3') ? 'selected' : ''; ?>>จัดสรรงวดที่ 3</option>
                                            <option value="Scenario4" <?php echo ($selectedAllocation == 'Scenario4') ? 'selected' : ''; ?>>จัดสรรงวดที่ 4</option>
                                        </select>
                                    </div>

                                    <!-- ปุ่มค้นหา -->
                                    <div class="form-group" style="display: flex; justify-content: center;">
                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                    </div>
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
                                                <th colspan="7" style='text-align: left;'>
                                                    รายงานการจัดสรรเงินรายงวด


                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="7" style='text-align: left;'>
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
                                                </th>
                                            </tr>

                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th rowspan="2">งบประมาณรายจ่ายทั้งสิ้น</th>
                                                <th colspan="3" style="text-align: center; vertical-align: middle;">
                                                    เงินงวด</th>
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
                                            $previousName_a1 = "";
                                            $previousName_a2 = "";
                                            $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            // Fetch data from the database
                                            $results = fetchScenarioData($conn, $selectedFaculty, scenarioColumnValue: $scenarioValue, selectedScenario: $selectedScenario);

                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                $summary = [];

                                                foreach ($results as $row) {

                                                    $Faculty = $row["Faculty_Id"];
                                                    $plan = $row['Plan'];
                                                    $subPlan = $row['Sub_Plan'];
                                                    $project = $row['project_name'];
                                                    $Name_a1 = $row['Name_a1'];
                                                    $Name_a2 = $row['Name_a2'];
                                                    $Name_a3 = $row['Name_a3'];
                                                    $Name_a4 = $row['Name_a4'];


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
                                                            'Name_a1' => [], // เก็บข้อมูลของ Name_a2
                                                        ];
                                                    }

                                                    $ItemName_a1 = (!empty($row['Name_a1']))
                                                        ? "" . htmlspecialchars($row['a1']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a1']))
                                                        : "" . htmlspecialchars($row['a1']) . "";

                                                    // เก็บข้อมูลของ Name_a1
                                                    if (!isset($summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1])) {
                                                        $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1] = [
                                                            'name' => $ItemName_a1,
                                                            'a1' => $row['a1'],

                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'Reason' => $row['Reason'],
                                                            'Name_a2' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }


                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a4
                                                    if (!empty($row['a2']) && !empty($row['Name_a2'])) {
                                                        $ItemName_a2 = htmlspecialchars($row['a2']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']));
                                                    } elseif (empty($row['a2']) && !empty($row['Name_a2'])) {
                                                        $ItemName_a2 = htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']));
                                                    } else {
                                                        $ItemName_a2 = htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }

                                                    // เก็บข้อมูลของ Name_a2
                                                    if (!isset($summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2])) {
                                                        $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2] = [
                                                            'name' => $ItemName_a2,
                                                            'test' => $row['Name_a2'],
                                                            'test2' => $row['Name_a3'],
                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'Reason' => $row['Reason'],
                                                            'Name_a3' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }
                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a3
                                                    if (!empty($row['a3']) && !empty($row['Name_a3'])) {
                                                        $ItemName_a3 = htmlspecialchars($row['a3']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a3']));
                                                    } elseif (empty($row['a3']) && !empty($row['Name_a3'])) {
                                                        $ItemName_a3 = htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a3']));
                                                    } else {
                                                        $ItemName_a3 = htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    // เก็บข้อมูลของ Name_a3
                                                    if (!isset($summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3])) {
                                                        $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3] = [
                                                            'name' => $ItemName_a3,
                                                            'test' => $row['Name_a3'],
                                                            'test2' => $row['Name_a4'],

                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'Reason' => $row['Reason'],
                                                            'Name_a4' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }

                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a4
                                                    if (!empty($row['a4']) && !empty($row['Name_a4'])) {
                                                        $ItemName_a4 = htmlspecialchars($row['a4']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a4']));
                                                    } elseif (empty($row['a4']) && !empty($row['Name_a4'])) {
                                                        $ItemName_a4 = htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a4']));
                                                    } else {
                                                        $ItemName_a4 = htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    // เก็บข้อมูลของ Name_a4
                                                    if (!isset($summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4])) {
                                                        $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4] = [
                                                            'name' => $ItemName_a4,
                                                            'test' => $row['Name_a4'],
                                                            'test2' => $row['KKU_Item_Name'],
                                                            'Allocated_Total_Amount_Quantity' => 0,
                                                            'Release_Amount' => 0,
                                                            'Pre_Release_Amount' => 0,
                                                            'Scenario1' => 0,
                                                            'Scenario2' => 0,
                                                            'Scenario3' => 0,
                                                            'Scenario4' => 0,
                                                            'Reason' => $row['Reason'],
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

                                                    // รวมข้อมูลของ Name_a1
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Scenario4'] += $row['Scenario4'];

                                                    // รวมข้อมูลของ Name_a2
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Scenario4'] += $row['Scenario4'];

                                                    // รวมข้อมูลของ Name_a3
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Scenario4'] += $row['Scenario4'];


                                                    // รวมข้อมูลของ Name_a3
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Release_Amount'] += $row['Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Pre_Release_Amount'] += $row['Pre_Release_Amount'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Scenario1'] += $row['Scenario1'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Scenario2'] += $row['Scenario2'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Scenario3'] += $row['Scenario3'];
                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Scenario4'] += $row['Scenario4'];



                                                    // เก็บข้อมูลของ KKU_Item_Name
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "" . htmlspecialchars($row['Account'] ?? '') . " : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                        : "" . htmlspecialchars($row['Account'] ?? '') . "";

                                                    $summary[$Faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['kku_items'][] = [
                                                        'name' => $kkuItemName,
                                                        'test' => $row['KKU_Item_Name'],
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
                                                    echo "<td style='text-align: left;'>รวมทั้งสิ้น</td>";
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

                                                    // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                    $facultyData = str_replace('-', ':', $FacultyData['FacultyName']);
                                                    echo "<td style='text-align: left;'>" . htmlspecialchars($facultyData) . "</td>";
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
                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 8) . htmlspecialchars($planData['PlanName'] ?? '') . "</td>";
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
                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 16) . htmlspecialchars($cleanedSubPlan) . " : " . htmlspecialchars($subPlanData['SubPlanName'] ?? '') . "</td>";
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
                                                                echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 24) . htmlspecialchars($project) . "</td>";
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

                                                                foreach ($projectData['Name_a1'] as $Name_a1 => $Name_a1Data) {

                                                                    // ดึงค่า Pre_Release_Amount และ Scenario ที่รวมไว้
                                                                    $preReleaseAmount = $Name_a1Data['Pre_Release_Amount'] ?? 0;
                                                                    $selectedScenario = $_GET['scenario'] ?? 'Scenario1';
                                                                    // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                                    $finalPreReleaseAmount = $preReleaseAmount;
                                                                    switch ($selectedScenario) {
                                                                        case 'Scenario1':
                                                                            $finalPreReleaseAmount += $Name_a1Data['Scenario1'] ?? 0;
                                                                            break;
                                                                        case 'Scenario2':
                                                                            $finalPreReleaseAmount += $Name_a1Data['Scenario2'] ?? 0;
                                                                            break;
                                                                        case 'Scenario3':
                                                                            $finalPreReleaseAmount += $Name_a1Data['Scenario3'] ?? 0;
                                                                            break;
                                                                        case 'Scenario4':
                                                                            $finalPreReleaseAmount += $Name_a1Data['Scenario4'] ?? 0;
                                                                            break;
                                                                        default:
                                                                            break;
                                                                    }
                                                                    // แสดงข้อมูลของ Name_a2
                                                                    echo "<tr>";
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . htmlspecialchars($Name_a1Data['name'] ?? '') . "</td>";
                                                                    // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                                    echo "<td>" . formatNumber($Name_a1Data['Allocated_Total_Amount_Quantity']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Pre_Release_Amount']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data[$selectedScenario] ?? 0) . "</td>";
                                                                    echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                                    // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                                    $subtractedAmount = ($Name_a1Data['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                                    echo "<td>" . formatNumber($subtractedAmount) . "</td>";
                                                                    echo "<td>" . "</td>";
                                                                    echo "</tr>";

                                                                    foreach ($Name_a1Data['Name_a2'] as $Name_a2 => $Name_a2Data) {

                                                                        if ($Name_a2Data['test'] == null || $Name_a2Data['test'] == '' || $Name_a1Data['name'] == $Name_a2Data['name']) {
                                                                            continue;
                                                                        }

                                                                        // ดึงค่า Pre_Release_Amount และ Scenario ที่รวมไว้
                                                                        $preReleaseAmount = $Name_a2Data['Pre_Release_Amount'] ?? 0;
                                                                        $selectedScenario = $_GET['scenario'] ?? 'Scenario1';
                                                                        // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                                        $finalPreReleaseAmount = $preReleaseAmount;
                                                                        switch ($selectedScenario) {
                                                                            case 'Scenario1':
                                                                                $finalPreReleaseAmount += $Name_a2Data['Scenario1'] ?? 0;
                                                                                break;
                                                                            case 'Scenario2':
                                                                                $finalPreReleaseAmount += $Name_a2Data['Scenario2'] ?? 0;
                                                                                break;
                                                                            case 'Scenario3':
                                                                                $finalPreReleaseAmount += $Name_a2Data['Scenario3'] ?? 0;
                                                                                break;
                                                                            case 'Scenario4':
                                                                                $finalPreReleaseAmount += $Name_a2Data['Scenario4'] ?? 0;
                                                                                break;
                                                                            default:
                                                                                break;
                                                                        }

                                                                        // แสดงข้อมูลของ Name_a2
                                                                        echo "<tr>";
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 40) . htmlspecialchars($Name_a2Data['name'] ?? '') . "</td>";
                                                                        // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                                        echo "<td>" . formatNumber($Name_a2Data['Allocated_Total_Amount_Quantity']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Pre_Release_Amount']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data[$selectedScenario] ?? 0) . "</td>";
                                                                        echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                                        // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                                        $subtractedAmount = ($Name_a2Data['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                                        echo "<td>" . formatNumber($subtractedAmount) . "</td>";

                                                                        if ($Name_a2Data['test2'] == null || $Name_a2Data['test2'] == '') {
                                                                            echo "<td>" . (isset($Name_a2Data['Reason']) && !empty($Name_a2Data['Reason']) ? htmlspecialchars($Name_a2Data['Reason']) : "") . "</td>";
                                                                        } else {
                                                                            echo "<td>" . "</td>";
                                                                        }
                                                                        echo "</tr>";

                                                                        foreach ($Name_a2Data['Name_a3'] as $Name_a3 => $Name_a3Data) {
                                                                            if ($Name_a3Data['test'] == null || $Name_a3Data['test'] == '' || $Name_a2Data['name'] == $Name_a3Data['name']) {
                                                                                continue;
                                                                            }


                                                                            // ดึงค่า Pre_Release_Amount และ Scenario ที่รวมไว้
                                                                            $preReleaseAmount = $Name_a3Data['Pre_Release_Amount'] ?? 0;
                                                                            $selectedScenario = $_GET['scenario'] ?? 'Scenario1';
                                                                            // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                                            $finalPreReleaseAmount = $preReleaseAmount;
                                                                            switch ($selectedScenario) {
                                                                                case 'Scenario1':
                                                                                    $finalPreReleaseAmount += $Name_a3Data['Scenario1'] ?? 0;
                                                                                    break;
                                                                                case 'Scenario2':
                                                                                    $finalPreReleaseAmount += $Name_a3Data['Scenario2'] ?? 0;
                                                                                    break;
                                                                                case 'Scenario3':
                                                                                    $finalPreReleaseAmount += $Name_a3Data['Scenario3'] ?? 0;
                                                                                    break;
                                                                                case 'Scenario4':
                                                                                    $finalPreReleaseAmount += $Name_a3Data['Scenario4'] ?? 0;
                                                                                    break;
                                                                                default:
                                                                                    break;
                                                                            }

                                                                            // แสดงข้อมูลของ Name_a3
                                                                            echo "<tr>";
                                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 48) . htmlspecialchars($Name_a3Data['name'] ?? '') . "</td>";
                                                                            // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                                            echo "<td>" . formatNumber($Name_a3Data['Allocated_Total_Amount_Quantity']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Pre_Release_Amount']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data[$selectedScenario] ?? 0) . "</td>";
                                                                            echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                                            // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                                            $subtractedAmount = ($Name_a3Data['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                                            echo "<td>" . formatNumber($subtractedAmount) . "</td>";

                                                                            if ($Name_a3Data['test2'] == null || $Name_a3Data['test2'] == '') {
                                                                                echo "<td>" . (isset($Name_a3Data['Reason']) && !empty($Name_a3Data['Reason']) ? htmlspecialchars($Name_a3Data['Reason']) : "") . "</td>";
                                                                            } else {
                                                                                echo "<td>" . "</td>";
                                                                            }
                                                                            echo "</tr>";
                                                                            foreach ($Name_a3Data['Name_a4'] as $Name_a4 => $Name_a4Data) {
                                                                                if ($Name_a4Data['test'] == null || $Name_a4Data['test'] == '' || $Name_a3Data['name'] == $Name_a4Data['name']) {
                                                                                    continue;
                                                                                }


                                                                                // ดึงค่า Pre_Release_Amount และ Scenario ที่รวมไว้
                                                                                $preReleaseAmount = $Name_a4Data['Pre_Release_Amount'] ?? 0;
                                                                                $selectedScenario = $_GET['scenario'] ?? 'Scenario1';
                                                                                // คำนวณ finalPreReleaseAmount โดยบวกค่า Scenario ที่เลือก
                                                                                $finalPreReleaseAmount = $preReleaseAmount;
                                                                                switch ($selectedScenario) {
                                                                                    case 'Scenario1':
                                                                                        $finalPreReleaseAmount += $Name_a4Data['Scenario1'] ?? 0;
                                                                                        break;
                                                                                    case 'Scenario2':
                                                                                        $finalPreReleaseAmount += $Name_a4Data['Scenario2'] ?? 0;
                                                                                        break;
                                                                                    case 'Scenario3':
                                                                                        $finalPreReleaseAmount += $Name_a4Data['Scenario3'] ?? 0;
                                                                                        break;
                                                                                    case 'Scenario4':
                                                                                        $finalPreReleaseAmount += $Name_a4Data['Scenario4'] ?? 0;
                                                                                        break;
                                                                                    default:
                                                                                        break;
                                                                                }

                                                                                // แสดงข้อมูลของ Name_a4
                                                                                echo "<tr>";
                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 56) . htmlspecialchars($Name_a4Data['name'] ?? '') . "</td>";
                                                                                // แสดงข้อมูลในคอลัมน์ที่เหลือ
                                                                                echo "<td>" . formatNumber($Name_a4Data['Allocated_Total_Amount_Quantity']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Pre_Release_Amount']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data[$selectedScenario] ?? 0) . "</td>";
                                                                                echo "<td>" . formatNumber($finalPreReleaseAmount) . "</td>";
                                                                                // คำนวณผลลัพธ์การลบระหว่าง 'Allocated_Total_Amount_Quantity' และ 'finalPreReleaseAmount'
                                                                                $subtractedAmount = ($Name_a4Data['Allocated_Total_Amount_Quantity'] ?? 0) - $finalPreReleaseAmount;
                                                                                echo "<td>" . formatNumber($subtractedAmount) . "</td>";

                                                                                if ($Name_a4Data['test2'] == null || $Name_a4Data['test2'] == '') {
                                                                                    echo "<td>" . (isset($Name_a4Data['Reason']) && !empty($Name_a4Data['Reason']) ? htmlspecialchars($Name_a4Data['Reason']) : "") . "</td>";
                                                                                } else {
                                                                                    echo "<td>" . "</td>";
                                                                                }
                                                                                echo "</tr>";

                                                                                foreach ($Name_a4Data['kku_items'] as $kkuItem) {
                                                                                    if ($kkuItem['test'] == null || $kkuItem['test'] == '' || $Name_a4Data['name'] == $kkuItem['name']) {
                                                                                        continue;
                                                                                    }

                                                                                    echo "<tr>";
                                                                                    echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 72) . $kkuItem['name'] . "</td>";

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

            // ฟังก์ชันช่วยเติมค่าซ้ำ
            const repeatValue = (value, count) => Array(count).fill(value).join(',');

            // เพิ่มชื่อรายงาน
            csvRows.push(["รายงานการจัดสรรเงินรายงวด", "", "", "", "", "", ""]);

            // เพิ่มส่วนของงวดที่เลือก
            const selectedScenario = <?php echo json_encode($selectedScenario); ?>;
            let scenarioText;
            switch (selectedScenario) {
                case 'Scenario1':
                    scenarioText = 'จัดสรรงวดที่ 1';
                    break;
                case 'Scenario2':
                    scenarioText = 'จัดสรรงวดที่ 2';
                    break;
                case 'Scenario3':
                    scenarioText = 'จัดสรรงวดที่ 3';
                    break;
                case 'Scenario4':
                    scenarioText = 'จัดสรรงวดที่ 4';
                    break;
                default:
                    scenarioText = 'จัดสรรงวดที่ 1'; // ค่าเริ่มต้น
            }
            csvRows.push([scenarioText, "", "", "", "", "", ""]);

            // เพิ่มส่วนหัวของตาราง
            csvRows.push([
                "รายการ",
                "งบประมาณรายจ่ายทั้งสิ้น",
                "เงินจัดสรรกำหนดให้แล้ว",
                "<?php echo $headerText; ?>",
                "รวมเงินจัดสรรทั้งสิ้น",
                "งบประมาณรายจ่ายคงเหลือ",
                "หมายเหตุ"
            ]);

            // วนลูปเฉพาะ <tbody>
            const tbody = table.querySelector("tbody");
            for (const row of tbody.rows) {
                const cellLines = [];
                let maxSubLine = 1;

                // วนลูปแต่ละเซลล์
                for (const cell of row.cells) {
                    let html = cell.innerHTML;

                    // แปลง &nbsp; เป็น non-breaking space (\u00A0)
                    html = html.replace(/(&nbsp;)+/g, (match) => {
                        const count = match.match(/&nbsp;/g).length;
                        return '\u00A0'.repeat(count);
                    });

                    // ลบแท็ก HTML ออก
                    html = html.replace(/<\/?[^>]+>/g, '');

                    // แยกข้อความเป็นบรรทัด
                    const lines = html.split('\n').map(x => x.trimEnd());

                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }

                    cellLines.push(lines);
                }

                // เพิ่ม sub-row ตามจำนวนบรรทัดย่อยที่มากที่สุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];

                    for (const lines of cellLines) {
                        let text = lines[i] || '';
                        text = text.replace(/"/g, '""'); // Escape double quotes
                        text = `"${text}"`;
                        rowData.push(text);
                    }

                    csvRows.push(rowData.join(','));
                }
            }

            // รวมเป็น CSV + BOM
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

            console.log(allRows);  // ตรวจสอบข้อมูล

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead ลงใน sheet (ถ้ามี)
            if (theadMerges.length > 0) {
                ws['!merges'] = theadMerges;
            }

            // ตั้งค่า vertical-align: bottom ให้ทุกเซลล์
            applyCellStyles(ws, "bottom");

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์เป็น .xlsx
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xlsx',  // เลือกชนิดของไฟล์
                type: 'array'      // ส่งออกเป็น array
            });

            // สร้าง Blob + ดาวน์โหลด
            const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานการจัดสรรเงินรายงวด.xlsx';
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