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
$budget_year2 = isset($_GET['year']) ? $_GET['year'] - 1 : null;
$budget_year3 = isset($_GET['year']) ? $_GET['year'] - 2 : null;

function fetchBudgetData($conn, $faculty = null, $budget_year1 = null, $budget_year2 = null, $budget_year3 = null)
{
    // ตรวจสอบว่า $budget_year1, $budget_year2, $budget_year3 ถูกตั้งค่าแล้วหรือไม่
    if ($budget_year1 === null) {
        $budget_year1 = 2568;  // ค่าเริ่มต้นถ้าหากไม่ได้รับจาก URL
    }
    if ($budget_year2 === null) {
        $budget_year2 = 2567;  // ค่าเริ่มต้น
    }
    if ($budget_year3 === null) {
        $budget_year3 = 2566;  // ค่าเริ่มต้น
    }

    // สร้างคิวรี
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
,main AS (SELECT 
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
ORDER BY account)
,t1 AS(
SELECT 
    bap.id, 
    bap.Faculty, 
    bap.Plan,
    ft.Alias_Default AS Faculty_name,
    MAX(p.plan_name) AS plan_name,
    (SELECT fc.Alias_Default 
     FROM Faculty fc 
     WHERE CAST(SUBSTRING(fc.Faculty, 2) AS UNSIGNED) = CAST(bap.Faculty AS UNSIGNED)
     LIMIT 1) AS Faculty_Name_Main,
    bap.Sub_Plan, 
    sp.sub_plan_name,
    bap.Project, 
    pj.project_name,
    ac.alias_default, 
    ac.sub_type, 
    ac.type,
    bap.KKU_Item_Name, 
    bap.Account,
    SUM(CASE WHEN bap.Budget_Management_Year = 2568 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568,
    SUM(CASE WHEN bap.Budget_Management_Year = 2567 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567,
    SUM(CASE WHEN bap.Budget_Management_Year = 2566 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2566,
    SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2568 THEN bpa.TOTAL_BUDGET ELSE 0 END) AS TOTAL_BUDGET_2568,
    SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2567 THEN bpa.TOTAL_BUDGET ELSE 0 END) AS TOTAL_BUDGET_2567,
    SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566 THEN bpa.TOTAL_BUDGET ELSE 0 END) AS TOTAL_BUDGET_2566,
    (SUM(CASE WHEN bap.Budget_Management_Year = 2568 THEN bap.Total_Amount_Quantity ELSE 0 END) - 
     COALESCE(SUM(CASE WHEN (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2567 THEN bpa.TOTAL_BUDGET ELSE 0 END), 0)) 
     AS Difference_2568_2567,
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
    bap.Reason,
    m.CurrentAccount,
    m.Current,
    m.ParentAccount,
    m.Parent,
    m.GrandparentAccount,
    m.Grandparent,
    m.GreatGrandparentAccount,
    m.GreatGrandparent,
    m.GreatGreatGrandparentAccount,
    m.GreatGreatGrandparent,
    m.TotalLevels,
    
    -- กำหนดค่า a1, a2, a3, a4 ตามเงื่อนไขของ TotalLevels
    CASE 
        WHEN m.TotalLevels = 5 THEN m.GreatGrandparentAccount
        WHEN m.TotalLevels = 4 THEN m.GrandparentAccount
        WHEN m.TotalLevels = 3 THEN m.ParentAccount
    END AS a1,
    
    CASE 
        WHEN m.TotalLevels IN (4, 5) THEN m.GrandparentAccount
        WHEN m.TotalLevels = 3 THEN m.ParentAccount
    END AS a2,
    
    CASE 
        WHEN m.TotalLevels IN (3, 4, 5) THEN m.ParentAccount
    END AS a3,
    
    CASE 
        WHEN m.TotalLevels IN (2, 3, 4, 5) THEN m.CurrentAccount
    END AS a4

FROM budget_planning_annual_budget_plan bap
INNER JOIN Faculty ft 
    ON CAST(SUBSTRING(ft.Faculty, 2) AS UNSIGNED) = CAST(bap.Faculty AS UNSIGNED)
    AND ft.parent LIKE 'Faculty%' 
LEFT JOIN main m ON bap.Account = m.CurrentAccount
LEFT JOIN sub_plan sp ON sp.sub_plan_id = bap.Sub_Plan
LEFT JOIN project pj ON pj.project_id = bap.Project
LEFT JOIN `account` ac ON ac.account COLLATE utf8mb4_general_ci = bap.Account COLLATE utf8mb4_general_ci
LEFT JOIN plan p ON p.plan_id = bap.Plan
LEFT JOIN budget_planning_actual bpa
    ON bpa.FACULTY = bap.Faculty
    AND bpa.ACCOUNT = bap.Account
    AND bpa.SUBPLAN = CAST(SUBSTRING(bap.Sub_Plan, 4) AS UNSIGNED)
    AND bpa.PROJECT = bap.Project
    AND bpa.PLAN = bap.Plan
    AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = bap.Budget_Management_Year
    AND bpa.SERVICE = CAST(REPLACE(bap.Service, 'SR_', '') AS UNSIGNED)
    AND bpa.FUND = CAST(REPLACE(bap.Fund, 'FN', '') AS UNSIGNED)
WHERE ac.id < (SELECT MAX(id) FROM account WHERE parent = 'Expenses')
";

    // เพิ่มเงื่อนไขสำหรับ Faculty ถ้ามี
    if ($faculty) {
        $query .= " AND bap.Faculty = :faculty"; // กรองตาม Faculty ที่เลือก
    }

    // เพิ่มการจัดกลุ่มข้อมูล
    $query .= " GROUP BY 
    bap.id, bap.Faculty, bap.Sub_Plan, sp.sub_plan_name, 
    bap.Project, pj.project_name, bap.Account, ac.sub_type, 
    bap.KKU_Item_Name, ft.Alias_Default, ac.alias_default, ac.type, p.plan_name,bap.Reason,bap.Reason,
    m.CurrentAccount,
    m.Current,
    m.ParentAccount,
    m.Parent,
    m.GrandparentAccount,
    m.Grandparent,
    m.GreatGrandparentAccount,
    m.GreatGrandparent,
    m.GreatGreatGrandparentAccount,
    m.GreatGreatGrandparent,m.TotalLevels

ORDER BY bap.Faculty ASC, bap.Plan ASC, bap.Sub_Plan ASC, bap.Project ASC, ac.type ASC,
                ac.sub_type ASC, bap.Account ASC

)
SELECT * FROM t1";

    // เตรียมคำสั่ง SQL

    $stmt = $conn->prepare($query);

    // ถ้ามี Faculty ให้ผูกค่าพารามิเตอร์
    if ($faculty) {
        $stmt->bindParam(':faculty', $faculty, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$faculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;



$results = fetchBudgetData($conn, $faculty, $budget_year1, $budget_year2, $budget_year3);

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
                                $years = fetchYearsData($conn);  // ดึงข้อมูลปีจากฐานข้อมูล
                                ?>

                                <form method="GET" action="" onsubmit="return validateForm()">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="faculty" class="label-faculty" style="margin-right: 10px;">เลือก
                                            ส่วนงาน/หน่วยงาน</label>
                                        <select name="faculty" id="faculty" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือก ทุกส่วนงาน</option>
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
                                    </div>

                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="year" class="label-year"
                                            style="margin-right: 10px;">เลือกปีงบประมาณ</label>
                                        <select name="year" id="year" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
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
                                    </div>

                                    <!-- ปุ่มค้นหาที่อยู่ด้านล่างฟอร์ม -->
                                    <div class="form-group" style="display: flex; justify-content: center;">
                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                    </div>
                                </form>

                                <script>
                                    function validateForm() {
                                        var faculty = document.getElementById('faculty').value;
                                        if (faculty == '') {
                                            // ถ้าไม่เลือกหน่วยงาน ให้เปลี่ยนเส้นทางไปที่หน้า report-budget-annual-summary.php
                                            window.location.href = "http://localhost/kku-report/template-vertical-nav/report-budget-adjustments.php";
                                            return false; // ป้องกันการส่งฟอร์ม
                                        }
                                        return true;
                                    }
                                </script>


                                <script>
                                    // ส่งค่าจาก PHP ไปยัง JavaScript
                                    const budgetYear1 = <?php echo json_encode($budget_year1); ?>;
                                    const budgetYear2 = <?php echo json_encode($budget_year2); ?>;
                                    const budgetYear3 = <?php echo json_encode($budget_year3); ?>;

                                    // แสดงค่าของ budget_year ในคอนโซล
                                    console.log('Budget Year 1:', budgetYear1);
                                    console.log('Budget Year 2:', budgetYear2);
                                    console.log('Budget Year 2:', budgetYear3);
                                </script>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">
                                        <thead>
                                            <tr>
                                                <th colspan="8" style='text-align: left;'>
                                                    รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ</th>
                                            </tr>
                                            <tr>
                                                <?php
                                                // ตรวจสอบและกำหนดค่า $selectedYear
                                                $selectedYear = isset($_GET['year']) && $_GET['year'] != '' ? (int) $_GET['year'] : '2568';

                                                // ตรวจสอบและกำหนดค่า $selectedFacultyName
                                                $selectedFacultyCode = isset($_GET['faculty']) ? $_GET['faculty'] : null;
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
                                                <th colspan="8" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        if ($selectedYear) {
                                                            echo "ปีงบที่ต้องการเปรียบเทียบ " . ($selectedYear - 2) . " ถึง " . $selectedYear;
                                                        } else {
                                                            echo "ปีงบที่ต้องการเปรียบเทียบ: ไม่ได้เลือกปีงบประมาณ";
                                                        }
                                                        ?> </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="8" style='text-align: left;'>
                                                    <span style="font-size: 16px;">


                                                        <?php
                                                        $facultyData = str_replace('-', ':', $selectedFacultyName);

                                                        echo "ส่วนงาน / หน่วยงาน: " . $facultyData; ?>
                                                    </span>
                                                </th>
                                            </tr>

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

                                            $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;
                                            $budget_year2 = isset($_GET['year']) ? $_GET['year'] - 1 : null;
                                            $budget_year3 = isset($_GET['year']) ? $_GET['year'] - 2 : null;
                                            $results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $budget_year2, $budget_year3);

                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                // สร้าง associative array เพื่อเก็บผลรวมของแต่ละ Plan, Sub_Plan, Project, และ Sub_Type
                                                $summary = [];
                                                foreach ($results as $row) {
                                                    $plan = $row['Plan'];
                                                    $subPlan = $row['Sub_Plan'];
                                                    $project = $row['project_name'];
                                                    $Type = $row['type'];
                                                    $subType = $row['sub_type'];
                                                    $superSubType = $row['alias_default'];


                                                    // เก็บข้อมูลของ Plan
                                                    if (!isset($summary[$plan])) {
                                                        $summary[$plan] = [
                                                            'plan_name' => $row['plan_name'],
                                                            'Total_Amount_2566' => 0,
                                                            'Total_Amount_2567' => 0,
                                                            'TOTAL_BUDGET_2567' => 0,
                                                            'Total_Amount_2568' => 0,
                                                            'Difference_2568_2567' => 0,
                                                            'Percentage_Difference_2568_2567' => 0,
                                                            'Reason' => '',
                                                            'sub_plans' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Sub_Plan
                                                    if (!isset($summary[$plan]['sub_plans'][$subPlan])) {
                                                        $summary[$plan]['sub_plans'][$subPlan] = [
                                                            'sub_plan_name' => $row['sub_plan_name'],
                                                            'Total_Amount_2566' => 0,
                                                            'Total_Amount_2567' => 0,
                                                            'TOTAL_BUDGET_2567' => 0,
                                                            'Total_Amount_2568' => 0,
                                                            'Difference_2568_2567' => 0,
                                                            'Percentage_Difference_2568_2567' => 0,
                                                            'Reason' => '',
                                                            'projects' => [], // เก็บข้อมูลของ Project
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Project
                                                    if (!isset($summary[$plan]['sub_plans'][$subPlan]['projects'][$project])) {
                                                        $summary[$plan]['sub_plans'][$subPlan]['projects'][$project] = [
                                                            'Total_Amount_2566' => 0,
                                                            'Total_Amount_2567' => 0,
                                                            'TOTAL_BUDGET_2567' => 0,
                                                            'Total_Amount_2568' => 0,
                                                            'Difference_2568_2567' => 0,
                                                            'Percentage_Difference_2568_2567' => 0,
                                                            'Reason' => '',
                                                            'type' => [], // เก็บข้อมูลของ Sub_Type
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ type
                                                    if (!isset($summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type])) {
                                                        $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type] = [
                                                            'a1' => $row['a1'],
                                                            'Total_Amount_2566' => 0,
                                                            'Total_Amount_2567' => 0,
                                                            'TOTAL_BUDGET_2567' => 0,
                                                            'Total_Amount_2568' => 0,
                                                            'Difference_2568_2567' => 0,
                                                            'Percentage_Difference_2568_2567' => 0,
                                                            'Reason' => '',
                                                            'sub_types' => [], // เก็บข้อมูลของ Sub_Type
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Sub_Type
                                                    if (!isset($summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType])) {
                                                        $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType] = [
                                                            'a2' => $row['a2'],
                                                            'Total_Amount_2566' => 0,
                                                            'Total_Amount_2567' => 0,
                                                            'TOTAL_BUDGET_2567' => 0,
                                                            'Total_Amount_2568' => 0,
                                                            'Difference_2568_2567' => 0,
                                                            'Percentage_Difference_2568_2567' => 0,
                                                            'Reason' => '',
                                                            'super_sub_types' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ superSubType
                                                    $superSubTypeName = (!empty($row['alias_default']))
                                                        ? "" . htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['alias_default']))
                                                        : "" . htmlspecialchars($row['Account']) . "";
                                                    // เก็บข้อมูลของ superSubType
                                                    if (!isset($summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['super_sub_types'][$superSubType])) {
                                                        $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['super_sub_types'][$superSubType] = [
                                                            'name' => $superSubTypeName,
                                                            'a3' => $row['Account'],
                                                            'Total_Amount_2566' => 0,
                                                            'Total_Amount_2567' => 0,
                                                            'TOTAL_BUDGET_2567' => 0,
                                                            'Total_Amount_2568' => 0,
                                                            'Difference_2568_2567' => 0,
                                                            'Percentage_Difference_2568_2567' => 0,
                                                            'Reason' => '',
                                                            'kku_items' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }

                                                    // รวมข้อมูลของ Plan
                                                    $summary[$plan]['Total_Amount_2566'] += $row['Total_Amount_2566'];
                                                    $summary[$plan]['Total_Amount_2567'] += $row['Total_Amount_2567'];
                                                    $summary[$plan]['TOTAL_BUDGET_2567'] += $row['TOTAL_BUDGET_2567'];
                                                    $summary[$plan]['Total_Amount_2568'] += $row['Total_Amount_2568'];

                                                    // รวมข้อมูลของ Sub_Plan
                                                    $summary[$plan]['sub_plans'][$subPlan]['Total_Amount_2566'] += $row['Total_Amount_2566'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['Total_Amount_2567'] += $row['Total_Amount_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['TOTAL_BUDGET_2567'] += $row['TOTAL_BUDGET_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['Total_Amount_2568'] += $row['Total_Amount_2568'];

                                                    // รวมข้อมูลของ Project
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_2566'] += $row['Total_Amount_2566'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_2567'] += $row['Total_Amount_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['TOTAL_BUDGET_2567'] += $row['TOTAL_BUDGET_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_2568'] += $row['Total_Amount_2568'];

                                                    // รวมข้อมูลของ Sub_Type
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['Total_Amount_2566'] += $row['Total_Amount_2566'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['Total_Amount_2567'] += $row['Total_Amount_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['TOTAL_BUDGET_2567'] += $row['TOTAL_BUDGET_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['Total_Amount_2568'] += $row['Total_Amount_2568'];

                                                    // รวมข้อมูลของ Sub_Type
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['Total_Amount_2566'] += $row['Total_Amount_2566'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['Total_Amount_2567'] += $row['Total_Amount_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['TOTAL_BUDGET_2567'] += $row['TOTAL_BUDGET_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['Total_Amount_2568'] += $row['Total_Amount_2568'];
                                                    // รวมข้อมูลของ Sub_Type
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['super_sub_types'][$superSubType]['Total_Amount_2566'] += $row['Total_Amount_2566'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['super_sub_types'][$superSubType]['Total_Amount_2567'] += $row['Total_Amount_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['super_sub_types'][$superSubType]['TOTAL_BUDGET_2567'] += $row['TOTAL_BUDGET_2567'];
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['super_sub_types'][$superSubType]['Total_Amount_2568'] += $row['Total_Amount_2568'];
                                                    // เก็บข้อมูลของ KKU_Item_Name
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "" . htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                        : "" . htmlspecialchars($row['Account']) . "";
                                                    $summary[$plan]['sub_plans'][$subPlan]['projects'][$project]['type'][$Type]['sub_types'][$subType]['super_sub_types'][$superSubType]['kku_items'][] = [
                                                        'name' => $kkuItemName,
                                                        'Total_Amount_2566' => $row['Total_Amount_2566'],
                                                        'Total_Amount_2567' => $row['Total_Amount_2567'],
                                                        'TOTAL_BUDGET_2567' => $row['TOTAL_BUDGET_2567'],
                                                        'Total_Amount_2568' => $row['Total_Amount_2568'],
                                                        'Difference_2568_2567' => $row['Difference_2568_2567'],
                                                        'Percentage_Difference_2568_2567' => $row['Percentage_Difference_2568_2567'],
                                                        'Reason' => $row['Reason'],
                                                    ];
                                                }

                                                // แสดงผลลัพธ์
                                                foreach ($summary as $plan => $data) {
                                                    // แสดงผลรวมของ Plan
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left;'>" . htmlspecialchars($data['plan_name']) . "<br></td>";
                                                    echo "<td>" . formatNumber($data['Total_Amount_2566']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_Amount_2567']) . "</td>";
                                                    echo "<td>" . formatNumber($data['TOTAL_BUDGET_2567']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_Amount_2568']) . "</td>";

                                                    // คำนวณผลต่างและเปอร์เซ็นต์สำหรับ Plan
                                                    $Difference = $data['Total_Amount_2568'] - $data['TOTAL_BUDGET_2567'];
                                                    $Percentage_Difference = ($data['TOTAL_BUDGET_2567'] != 0) ? ($Difference / $data['TOTAL_BUDGET_2567']) * 100 : 100;

                                                    echo "<td>" . formatNumber($Difference) . "</td>";
                                                    echo "<td>" . formatNumber($Percentage_Difference) . "%</td>";
                                                    echo "<td>" . "</td>";
                                                    echo "</tr>";

                                                    // แสดงผลรวมของแต่ละ Sub_Plan
                                                    foreach ($data['sub_plans'] as $subPlan => $subData) {
                                                        echo "<tr>";

                                                        // ลบ 'SP_' ที่อยู่หน้าสุดของข้อความ
                                                        $cleanedSubPlan = preg_replace('/^SP_/', '', $subPlan);

                                                        // แสดงผลข้อมูล
                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 8) . htmlspecialchars($cleanedSubPlan) . " : " . htmlspecialchars($subData['sub_plan_name']) . "<br></td>";


                                                        echo "<td>" . formatNumber($subData['Total_Amount_2566']) . "</td>";
                                                        echo "<td>" . formatNumber($subData['Total_Amount_2567']) . "</td>";
                                                        echo "<td>" . formatNumber($subData['TOTAL_BUDGET_2567']) . "</td>";
                                                        echo "<td>" . formatNumber($subData['Total_Amount_2568']) . "</td>";

                                                        // คำนวณผลต่างและเปอร์เซ็นต์สำหรับ Sub_Plan
                                                        $subDifference = $subData['Total_Amount_2568'] - $subData['TOTAL_BUDGET_2567'];
                                                        $subPercentage_Difference = ($subData['TOTAL_BUDGET_2567'] != 0) ? ($subDifference / $subData['TOTAL_BUDGET_2567']) * 100 : 100;

                                                        echo "<td>" . formatNumber($subDifference) . "</td>";
                                                        echo "<td>" . formatNumber($subPercentage_Difference) . "%</td>";
                                                        echo "<td>" . "</td>";
                                                        echo "</tr>";

                                                        // แสดงผลรวมของแต่ละ Project
                                                        foreach ($subData['projects'] as $project => $projectData) {
                                                            echo "<tr>";
                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 16) . htmlspecialchars($project) . "<br></td>";
                                                            echo "<td>" . formatNumber($projectData['Total_Amount_2566']) . "</td>";
                                                            echo "<td>" . formatNumber($projectData['Total_Amount_2567']) . "</td>";
                                                            echo "<td>" . formatNumber($projectData['TOTAL_BUDGET_2567']) . "</td>";
                                                            echo "<td>" . formatNumber($projectData['Total_Amount_2568']) . "</td>";

                                                            // คำนวณผลต่างและเปอร์เซ็นต์สำหรับ Project
                                                            $projectDifference = $projectData['Total_Amount_2568'] - $projectData['TOTAL_BUDGET_2567'];
                                                            $projectPercentage_Difference = ($projectData['TOTAL_BUDGET_2567'] != 0) ? ($projectDifference / $projectData['TOTAL_BUDGET_2567']) * 100 : 100;

                                                            echo "<td>" . formatNumber($projectDifference) . "</td>";
                                                            echo "<td>" . formatNumber($projectPercentage_Difference) . "%</td>";
                                                            echo "<td>" . "</td>";
                                                            echo "</tr>";

                                                            // แสดงผลรวมของแต่ละ Sub_Type
                                                            foreach ($projectData['type'] as $Type => $TypeData) {
                                                                echo "<tr>";
                                                                // ใช้ Regex ลบตัวเลขและจุดข้างหน้า
                                                                $cleanedSubType = preg_replace('/^[\d.]+\s*/', '', $Type);

                                                                // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a2 และ subType
                                                                echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 24) . htmlspecialchars($TypeData['a1']) . " : " . htmlspecialchars($cleanedSubType) . "<br></td>";
                                                                echo "<td>" . formatNumber($TypeData['Total_Amount_2566']) . "</td>";
                                                                echo "<td>" . formatNumber($TypeData['Total_Amount_2567']) . "</td>";
                                                                echo "<td>" . formatNumber($TypeData['TOTAL_BUDGET_2567']) . "</td>";
                                                                echo "<td>" . formatNumber($TypeData['Total_Amount_2568']) . "</td>";

                                                                // คำนวณผลต่างและเปอร์เซ็นต์สำหรับ Sub_Type
                                                                $subTypeDifference = $TypeData['Total_Amount_2568'] - $TypeData['TOTAL_BUDGET_2567'];
                                                                $subTypePercentage_Difference = ($TypeData['TOTAL_BUDGET_2567'] != 0) ? ($subTypeDifference / $subTypeData['TOTAL_BUDGET_2567']) * 100 : 100;

                                                                echo "<td>" . formatNumber($subTypeDifference) . "</td>";
                                                                echo "<td>" . formatNumber($subTypePercentage_Difference) . "%</td>";
                                                                echo "<td>" . "</td>";
                                                                echo "</tr>";


                                                                // แสดงผลรวมของแต่ละ Sub_Type
                                                                foreach ($TypeData['sub_types'] as $subType => $subTypeData) {
                                                                    echo "<tr>";
                                                                    // ใช้ Regex ลบตัวเลขและจุดข้างหน้า
                                                                    $cleanedSubType = preg_replace('/^[\d.]+\s*/', '', $subType);

                                                                    // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a2 และ subType
                                                                    echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 32) . htmlspecialchars($subTypeData['a2']) . " : " . htmlspecialchars($cleanedSubType) . "<br></td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Total_Amount_2566']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Total_Amount_2567']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['TOTAL_BUDGET_2567']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Total_Amount_2568']) . "</td>";

                                                                    // คำนวณผลต่างและเปอร์เซ็นต์สำหรับ Sub_Type
                                                                    $subTypeDifference = $subTypeData['Total_Amount_2568'] - $subTypeData['TOTAL_BUDGET_2567'];
                                                                    $subTypePercentage_Difference = ($subTypeData['TOTAL_BUDGET_2567'] != 0) ? ($subTypeDifference / $subTypeData['TOTAL_BUDGET_2567']) * 100 : 100;

                                                                    echo "<td>" . formatNumber($subTypeDifference) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypePercentage_Difference) . "%</td>";
                                                                    echo "<td>" . "</td>";
                                                                    echo "</tr>";

                                                                    // แสดงข้อมูล KKU_Item_Name
                                                                    foreach ($subTypeData['super_sub_types'] as $superSubType) {
                                                                        echo "<tr>";

                                                                        echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 40) . $superSubType['name'] . "<br></td>";
                                                                        echo "<td>" . formatNumber($superSubType['Total_Amount_2566']) . "</td>";
                                                                        echo "<td>" . formatNumber($superSubType['Total_Amount_2567']) . "</td>";
                                                                        echo "<td>" . formatNumber($superSubType['TOTAL_BUDGET_2567']) . "</td>";
                                                                        echo "<td>" . formatNumber($superSubType['Total_Amount_2568']) . "</td>";

                                                                        // คำนวณผลต่างและเปอร์เซ็นต์สำหรับ Sub_Type
                                                                        $subTypeDifference = $superSubType['Total_Amount_2568'] - $superSubType['TOTAL_BUDGET_2567'];
                                                                        $subTypePercentage_Difference = ($superSubType['TOTAL_BUDGET_2567'] != 0) ? ($subTypeDifference / $superSubType['TOTAL_BUDGET_2567']) * 100 : 100;

                                                                        echo "<td>" . formatNumber($subTypeDifference) . "</td>";
                                                                        echo "<td>" . formatNumber($subTypePercentage_Difference) . "%</td>";
                                                                        echo "<td>" . "</td>";
                                                                        echo "</tr>";
                                                                        // แสดงข้อมูล KKU_Item_Name
                                                                        foreach ($superSubType['kku_items'] as $kkuItem) {
                                                                            echo "<tr>";

                                                                            echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 48) . $kkuItem['name'] . "<br></td>";
                                                                            echo "<td>" . formatNumber($kkuItem['Total_Amount_2566']) . "</td>";
                                                                            echo "<td>" . formatNumber($kkuItem['Total_Amount_2567']) . "</td>";
                                                                            echo "<td>" . formatNumber($kkuItem['TOTAL_BUDGET_2567']) . "</td>";
                                                                            echo "<td>" . formatNumber($kkuItem['Total_Amount_2568']) . "</td>";
                                                                            echo "<td>" . formatNumber($kkuItem['Difference_2568_2567']) . "</td>";
                                                                            echo "<td>" . formatNumber($kkuItem['Percentage_Difference_2568_2567']) . "</td>";
                                                                            echo "<td>" . (isset($kkuItem['Reason']) && !empty($kkuItem['Reason']) ? htmlspecialchars($kkuItem['Reason']) : "") . "</td>";
                                                                            echo "</tr>";
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
                                    <script>
                                        // การส่งค่าของ selectedFaculty ไปยัง JavaScript
                                        var selectedFaculty = "<?php echo isset($selectedFaculty) ? htmlspecialchars($selectedFaculty, ENT_QUOTES, 'UTF-8') : ''; ?>";
                                        console.log('Selected Faculty: ', selectedFaculty);

                                        // การส่งค่าของ selectedYear1, selectedYear2, selectedYear3 ไปยัง JavaScript
                                        var budget_year1 = "<?php echo isset($budget_year1) ? $budget_year1 : ''; ?>";
                                        var budget_year2 = "<?php echo isset($budget_year2) ? $budget_year2 : ''; ?>";
                                        var budget_year3 = "<?php echo isset($budget_year3) ? $budget_year3 : ''; ?>";

                                        console.log('Selected Year 1: ', budget_year1);
                                        console.log('Selected Year 2: ', budget_year2);
                                        console.log('Selected Year 3: ', budget_year3); 
                                    </script>
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
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ.csv';
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
            doc.text("รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ", 10, 500);

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
            doc.save('รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ.pdf');
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
            link.download = 'รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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