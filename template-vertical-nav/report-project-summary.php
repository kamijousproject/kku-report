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
$budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;
$scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
$faculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
$Fund = isset($_GET['fund']) ? $_GET['fund'] : null;

function fetchBudgetData($conn, $faculty = null, $budget_year1 = null, $scenario = null, $Fund = null)
{
    // ตรวจสอบว่า $budget_year1, $budget_year2, $budget_year3 ถูกตั้งค่าแล้วหรือไม่
    if ($budget_year1 === null) {
        $budget_year1 = 2568;  // ค่าเริ่มต้นถ้าหากไม่ได้รับจาก URL
    }

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
 
 , pilar_Name AS (
 SELECT  DISTINCT 
	 pki.Faculty,
	 pki.Project,
    REPLACE(pki.KKU_Strategic_Plan_LOV, '_', '') AS Strategic_Plan_Cleaned,
    pilar_name 
FROM budget_planning_project_kpi pki
LEFT JOIN pilar ppp
ON REPLACE(pki.KKU_Strategic_Plan_LOV, '_', '') = ppp.pilar_id)

,totalAmount AS (
SELECT 
    tm.Faculty,  
    tm.Plan, 
    tm.Sub_Plan, 
    tm.Project,      
    tm.Account,
    tm.Fund,
    tm.kku_item_name,
    tm.Reason,
    tm.Service,
    tm.Scenario,
    SUM(CASE WHEN tm.Budget_Management_Year = $budget_year1 THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568
FROM 
    budget_planning_annual_budget_plan tm  
GROUP BY  
    tm.Fund,
    tm.Faculty, 
    tm.Plan, 
    tm.Sub_Plan, 
    tm.Project,      
    tm.Account,
    tm.kku_item_name,
    tm.Reason,
    tm.Service,
    tm.Scenario
),
 
 
t1 AS(
SELECT
 	 fta.Alias_Default AS Default_Faculty,
    tm.Faculty, 
    tm.Plan,
    ft.Alias_Default AS Faculty_name,
    MAX(p.plan_name) AS plan_name,
    (SELECT fc.Alias_Default 
     FROM Faculty fc 
     WHERE CAST(SUBSTRING(fc.Faculty, 2) AS UNSIGNED) = CAST(tm.Faculty AS UNSIGNED)
     LIMIT 1) AS Faculty_Name_Main,
    tm.Sub_Plan, 
    sp.sub_plan_name,
    tm.Project, 
    pj.project_name,
    tm.KKU_Item_Name,
    tm.Account,
    tm.Fund,
    tm.Reason,
    tm.Scenario,
        CASE 
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
        tm.Account -- หากผลลัพธ์เป็น NULL ให้ใช้ค่า tm.Account
    ) AS a3,

    COALESCE(
        CASE  
            WHEN m.TotalLevels = 5 THEN m.CurrentAccount
            WHEN m.TotalLevels = 4 THEN NULL
            WHEN m.TotalLevels = 3 THEN NULL
        END,
        tm.Account -- หากผลลัพธ์เป็น NULL ให้ใช้ค่า tm.Account
    ) AS a4,

    CASE  
        WHEN m.TotalLevels = 5 THEN COALESCE(m.GreatGrandparent, tm.KKU_Item_Name)
        WHEN m.TotalLevels = 4 THEN COALESCE(m.Grandparent, tm.KKU_Item_Name)
        WHEN m.TotalLevels = 3 THEN COALESCE(m.Parent, tm.KKU_Item_Name)
    END AS Name_a1,

    CASE 
        WHEN (m.TotalLevels = 5 AND COALESCE(m.GreatGrandparent, tm.KKU_Item_Name) = tm.KKU_Item_Name) 
             OR (m.TotalLevels = 4 AND COALESCE(m.Grandparent, tm.KKU_Item_Name) = tm.KKU_Item_Name) 
             OR (m.TotalLevels = 3 AND COALESCE(m.Parent, tm.KKU_Item_Name) = tm.KKU_Item_Name)
        THEN NULL
        WHEN m.TotalLevels = 5 THEN COALESCE(m.Grandparent, tm.KKU_Item_Name)
        WHEN m.TotalLevels = 4 THEN COALESCE(m.Parent, tm.KKU_Item_Name)
        WHEN m.TotalLevels = 3 THEN COALESCE(m.Current, tm.KKU_Item_Name)
    END AS Name_a2,

    COALESCE(
        CASE  
            WHEN (m.TotalLevels = 5 AND COALESCE(m.Grandparent, tm.KKU_Item_Name) = tm.KKU_Item_Name)
                 OR (m.TotalLevels = 4 AND COALESCE(m.Parent, tm.KKU_Item_Name) = tm.KKU_Item_Name)
                 OR (m.TotalLevels = 3 AND COALESCE(m.Current, tm.KKU_Item_Name) = tm.KKU_Item_Name)
            THEN tm.KKU_Item_Name  -- เปลี่ยนจาก NULL เป็น tm.KKU_Item_Name
            WHEN m.TotalLevels = 5 THEN COALESCE(m.Parent, tm.KKU_Item_Name)
            WHEN m.TotalLevels = 4 THEN COALESCE(m.Current, tm.KKU_Item_Name)
        END,
        tm.KKU_Item_Name -- หากผลลัพธ์เป็น NULL ให้ใช้ค่า tm.KKU_Item_Name
    ) AS Name_a3,

    CASE
        WHEN (
            COALESCE(
                CASE  
                    WHEN (m.TotalLevels = 5 AND COALESCE(m.Grandparent, tm.KKU_Item_Name) = tm.KKU_Item_Name)
                         OR (m.TotalLevels = 4 AND COALESCE(m.Parent, tm.KKU_Item_Name) = tm.KKU_Item_Name)
                         OR (m.TotalLevels = 3 AND COALESCE(m.Current, tm.KKU_Item_Name) = tm.KKU_Item_Name)
                    THEN tm.KKU_Item_Name  
                    WHEN m.TotalLevels = 5 THEN COALESCE(m.Parent, tm.KKU_Item_Name)
                    WHEN m.TotalLevels = 4 THEN COALESCE(m.Current, tm.KKU_Item_Name)
                END,
                tm.KKU_Item_Name
            ) = tm.KKU_Item_Name
        )
        THEN NULL
        ELSE COALESCE(
            CASE  
                WHEN (m.TotalLevels = 5 AND COALESCE(m.Parent, tm.KKU_Item_Name) = tm.KKU_Item_Name)
                     OR (m.TotalLevels = 4 AND COALESCE(m.Current, tm.KKU_Item_Name) = tm.KKU_Item_Name)
                THEN NULL
                WHEN m.TotalLevels = 5 THEN COALESCE(m.Current, tm.KKU_Item_Name)
            END,
            tm.KKU_Item_Name
        )
    END AS Name_a4
FROM totalAmount tm 
INNER JOIN Faculty ft 
    ON ft.Faculty = tm.Faculty 
    AND ft.parent LIKE 'Faculty%' 
LEFT JOIN main m ON tm.Account = m.CurrentAccount
LEFT JOIN Faculty fta
            ON ft.Parent = fta.Faculty
LEFT JOIN sub_plan sp ON sp.sub_plan_id = tm.Sub_Plan
LEFT JOIN project pj ON pj.project_id = tm.Project
LEFT JOIN `account` ac ON ac.account COLLATE utf8mb4_general_ci = tm.Account COLLATE UTF8MB4_GENERAL_CI
LEFT JOIN plan p ON p.plan_id = tm.Plan

 
WHERE ac.id > (SELECT MAX(id) FROM account WHERE parent = 'Expenses')

GROUP BY 
    
	 fta.Alias_Default,
    tm.Faculty, 
    tm.Plan, 
    ft.Alias_Default,
    tm.Sub_Plan, 
    sp.sub_plan_name,
    tm.Project, 
    pj.project_name, 
    tm.KKU_Item_Name,
    tm.Account,
    tm.Scenario,
    tm.Fund,
    tm.Reason,
    tm.Total_Amount_2568,
    m.TotalLevels,
    m.GreatGrandparentAccount,
    m.GrandparentAccount,
    m.ParentAccount,
    m.GreatGrandparent,
    m.Grandparent,
    m.Parent,
    m.Current
 
ORDER BY fta.Alias_Default asc, tm.Faculty ASC , tm.Plan ASC, tm.Sub_Plan ASC, tm.Project ASC, Name_a1 ASC,Name_a2 ASC,Name_a3 ASC,Name_a4 ASC,tm.Account ASC
 
),t2 AS (
        SELECT DISTINCT 
ppp.Strategic_Plan_Cleaned,
ppp.pilar_name,
 	 t.Default_Faculty,
    t.Faculty, 
    t.Plan,
    t.Faculty_name,
    t.plan_name,
    t.Faculty_Name_Main,
    t.Sub_Plan, 
    t.sub_plan_name,
    t.Project, 
    t.project_name,
    t.KKU_Item_Name,
    t.Account,
    t.Fund,
    t.Reason,
    t.Scenario,
	 t.a1,
	 t.a2,
	 t.a3,
	 t.a4,
	 t.Name_a1,
	 t.Name_a2,
	 t.Name_a3,
	 t.Name_a4,
	 SUM(CASE WHEN t.a2 = '5101010000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_1,
	 SUM(CASE WHEN t.a2 = '5101020000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_2,
	 SUM(CASE WHEN t.a2 = '5101030000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_3,
	 SUM(CASE WHEN t.a2 = '5101040000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_4,
	 SUM(CASE WHEN t.a3 = '5101040100' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_4_1,
	 SUM(CASE WHEN t.a3 = '5101040200' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_4_2,
	 SUM(CASE WHEN t.a3 = '5101040300' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_4_3,
	 SUM(CASE WHEN t.a3 = '5101040400' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_4_4,
	 SUM(CASE WHEN t.a3 = '5101040500' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_4_5,
	 SUM(CASE WHEN t.a3 = '5101040600' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_4_6,
	 SUM(CASE WHEN t.a3 = '5101040700' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_1_4_7,
	 SUM(CASE WHEN t.a2 = '5203010000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_2_1,
	 SUM(CASE WHEN t.a2 = '5203020000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_2_2,
	 SUM(CASE WHEN t.a2 = '1105030000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_2_3,
	 SUM(CASE WHEN t.a2 = '5203040000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_2_4,
	 SUM(CASE WHEN t.a2 = '5201000000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_2_5,
	 SUM(CASE WHEN t.a2 = '5202000000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_2_6,
	 SUM(CASE WHEN t.a2 = '1207000000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_3_1,
	 SUM(CASE WHEN t.a2 = '1206000000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_3_2,
	 SUM(CASE WHEN t.a2 = '1205000000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_3_3,
	 SUM(CASE WHEN t.a1 = '5400000000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_4,
	 SUM(CASE WHEN t.a1 = '5500000000' THEN tt.Total_Amount_2568 ELSE 0 END) AS Total_Amount_5
	 FROM t1 t
	 LEFT JOIN totalAmount tt
	 ON tt.Faculty = t.Faculty
   AND tt.Plan = t.Plan
   AND tt.Sub_Plan = t.Sub_Plan 
   AND tt.Project =t.Project      
   AND tt.Account = t.Account
   AND tt.Fund = t.Fund
   inner JOIN pilar_Name ppp 
   ON t.Faculty = ppp.Faculty
   AND t.Project = ppp.Project
   GROUP BY 
   ppp.Strategic_Plan_Cleaned,
    ppp.pilar_name,
    t.Default_Faculty,
    t.Faculty, 
    t.Plan,
    t.Faculty_name,
    t.plan_name,
    t.Faculty_Name_Main,
    t.Sub_Plan, 
    t.sub_plan_name,
    t.Project, 
    t.Scenario,
    t.project_name,
    t.KKU_Item_Name,
    t.Account,
    t.Fund,
    t.Reason,
	 t.a1,
	 t.a2,
	 t.a3,
	 t.a4,
	 t.Name_a1,
	 t.Name_a2,
	 t.Name_a3,
	 t.Name_a4
    )
    SELECT * FROM t2";

    // เพิ่มเงื่อนไข WHERE และ ORDER BY
    $whereConditions = [];
    $params = [];

    if ($faculty) {
        $whereConditions[] = "Faculty = :faculty";
        $params[':faculty'] = $faculty;
    }
    if ($scenario) {
        $whereConditions[] = "Scenario = :scenario";
        $params[':scenario'] = $scenario;
    }
    if ($Fund) {
        $whereConditions[] = "Fund = :Fund";
        $params[':Fund'] = $Fund;
    }


    // เพิ่มเงื่อนไข WHERE ถ้ามี
    if (!empty($whereConditions)) {
        $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    // เพิ่ม ORDER BY
    $query .= " ORDER BY pilar_name ASC, Default_Faculty ASC, Faculty ASC, Plan ASC, Sub_Plan ASC, Project ASC";

    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($query);

    // ผูกค่าพารามิเตอร์
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    // ประมวลผลคำสั่ง SQL
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



$results = fetchBudgetData($conn, $faculty, $budget_year1, $scenario, $Fund);

function fetchFacultyData($conn)
{
    try {
        $query = "SELECT DISTINCT bap.Faculty, ft.Alias_Default AS Faculty_Name
                  FROM budget_planning_annual_budget_plan bap
                  LEFT JOIN Faculty ft ON ft.Faculty = bap.Faculty";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
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
                        <h4>รายงานสรุปคำขอรายโครงการ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">
                                รายงานสรุปคำขอรายโครงการ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขอรายโครงการ</h4>
                                </div>

                                <?php
                                // ดึงข้อมูลจากฐานข้อมูล
                                $faculties = fetchFacultyData($conn);
                                $years = fetchYearsData($conn);  // ดึงข้อมูลปีจากฐานข้อมูล
                                $scenarios = fetchScenariosData($conn); // ดึงข้อมูล Scenario จากฐานข้อมูล
                                

                                // ข้อมูลแหล่งเงิน
                                $fundOptions = [
                                    'FN02' => 'งบประมาณเงินรายได้',
                                    'FN06' => 'งบประมาณเงินอุดหนุนจากรัฐ',
                                    'FN08' => 'เงินนอกงบประมาณ',
                                ];
                                ?>

                                <form method="GET" action="" onsubmit="validateForm(event)">
                                    <!-- เลือกปีงบประมาณ -->
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

                                    <!-- เลือกประเภทงบประมาณ -->
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="scenario" class="label-scenario"
                                            style="margin-right: 10px;">เลือกประเภทงบประมาณ</label>
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

                                    <!-- เลือกแหล่งเงิน -->
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="fund" class="label-fund"
                                            style="margin-right: 10px;">เลือกแหล่งเงิน</label>
                                        <select name="fund" id="fund" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือก แหล่งเงิน</option>
                                            <?php
                                            foreach ($fundOptions as $value => $label) {
                                                $selected = (isset($_GET['fund']) && $_GET['fund'] == $value) ? 'selected' : '';
                                                echo "<option value=\"$value\" $selected>$label</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- เลือกส่วนงาน/หน่วยงาน -->
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="faculty" class="label-faculty"
                                            style="margin-right: 10px;">เลือกส่วนงาน/หน่วยงาน</label>
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



                                    <!-- ปุ่มค้นหา -->
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
                                        var fund = document.getElementById('fund').value;

                                        var baseUrl = "http://202.28.118.192:8081/template-vertical-nav/report-project-summary.php";
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

                                        // เพิ่ม Fund หากเลือกและไม่เป็นค่าว่าง
                                        if (fund && fund !== "") {
                                            params.push("fund=" + encodeURIComponent(fund));
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
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">

                                        <thead>
                                            <tr>
                                                <th colspan="29" style='text-align: left;'>
                                                    รายงานสรุปคำขอรายโครงการ
                                                </th>
                                            </tr>
                                            <?php
                                            // ตรวจสอบและกำหนดค่า $selectedFacultyName
                                            $selectedFacultyCode = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $selectedYear = isset($_GET['year']) && $_GET['year'] != '' ? (int) $_GET['year'] : '2568';
                                            $scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
                                            $selectedFund = isset($_GET['fund']) ? $_GET['fund'] : null; // ดึงค่า fund จาก $_GET
                                            $selectedFacultyName = 'แสดงทุกหน่วยงาน';

                                            // ค้นหาชื่อคณะจากรหัสคณะที่เลือก
                                            if ($selectedFacultyCode) {
                                                foreach ($faculties as $faculty) {
                                                    if ($faculty['Faculty'] === $selectedFacultyCode) {
                                                        $selectedFacultyName = htmlspecialchars($faculty['Faculty_Name']);
                                                        break;
                                                    }
                                                }
                                            }

                                            // กำหนดชื่อแหล่งเงิน
                                            $selectedFundName = 'แสดงทุกแหล่งเงิน'; // ค่าเริ่มต้น
                                            if ($selectedFund) {
                                                $fundOptions = [
                                                    'FN02' => 'งบประมาณเงินรายได้',
                                                    'FN06' => 'งบประมาณเงินอุดหนุนจากรัฐ',
                                                    'FN08' => 'เงินนอกงบประมาณ',
                                                ];
                                                $selectedFundName = $fundOptions[$selectedFund] ?? 'ไม่รู้จัก'; // ดึงชื่อแหล่งเงินจาก array
                                            }
                                            ?>
                                            <tr>
                                                <th colspan="29" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        if ($selectedYear) {
                                                            echo "ปีงบประมาณ " . $selectedYear;
                                                        } else {
                                                            echo "ปีงบประมาณ: ไม่ได้เลือกปีงบประมาณ";
                                                        }
                                                        ?>
                                                    </span>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th colspan="29" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        echo "ประเภทงบประมาณ: " . (!empty($scenario) ? $scenario : "แสดงทุกประเภทงบประมาณ");
                                                        ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="29" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        echo "แหล่งเงิน: " . $selectedFundName; // แสดงชื่อแหล่งเงินที่เลือก
                                                        ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="29" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        $facultyData = str_replace('-', ':', $selectedFacultyName);
                                                        echo "ส่วนงาน / หน่วยงาน: " . $facultyData;
                                                        ?>
                                                    </span>
                                                </th>
                                            </tr>



                                            <th rowspan="3">โครงการ/กิจกรรม</th>
                                            <th colspan="22">งบประมาณ</th>
                                            <th rowspan="3">รวมงบประมาณ</th>
                                            </tr>
                                            <tr>
                                                <th colspan="11">1.ค่าใช้จ่ายบุคลากร</th>
                                                <th colspan="6">2.ค่าใช้จ่ายดำเนินงาน</th>
                                                <th colspan="3">3.ค่าใช้จ่ายลงทุน</th>
                                                <th rowspan="2">4.ค่าใช้จ่ายเงินอุดหนุนการดำเนินงาน</th>
                                                <th rowspan="2">5.ค่าใช้จ่ายอื่น</th>
                                            </tr>
                                            <tr>
                                                <th>1.1 เงินเดือนข้าราชการและลูกจ้างประจำ</th>
                                                <th>1.2 ค่าจ้างพนักงานมหาวิทยาลัย</th>
                                                <th>1.3 ค่าจ้างลูกจ้างมหาวิทยาลัย</th>
                                                <th>1.4เงินกองทุนสำรองเพื่อผลประโยชน์พนักงานและสวัสดิการผู้ปฏิบัติงานในมหาวิทยาลัยขอนแก่น
                                                </th>
                                                <th>เงินสมทบประกันสังคมส่วนของนายจ้าง</th>
                                                <th>เงินสมทบกองทุนสำรองเลี้ยงชีพของนายจ้าง</th>
                                                <th>เงินชดเชยกรณีเลิกจ้าง</th>
                                                <th>เงินสมทบกองทุนเงินทดแทน</th>
                                                <th>สมทบกองทุนบำเหน็จบำนาญ(กบข.)</th>
                                                <th>สมทบกองทุนสำรองเลี้ยงชีพลูกจ้่างประจำ (กสจ.)</th>
                                                <th>สวัสดิการอื่น ๆ ตามที่คณะกรรมการกำหนด</th>
                                                <th>ค่าตอบแทน</th>
                                                <th>ค่าใช้สอย</th>
                                                <th>ค่าวัสดุ</th>
                                                <th>ค่าสาธารณูปโภค</th>
                                                <th>ค่าใช้จ่ายด้านการฝึกอบรม</th>
                                                <th>ค่าใช้จ่ายเดินทาง</th>
                                                <th>ค่าครุภัณฑ์</th>
                                                <th>ค่าที่ดินและสิ่งก่อสร้าง</th>
                                                <th>ค่าที่ดิน</th>
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
                                                return preg_replace('/^[\d.]+\s*/', '', $text);
                                            }

                                            $previousType = "";
                                            $previousSubType = "";
                                            $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;
                                            $scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;

                                            $Fund = isset($_GET['fund']) ? $_GET['fund'] : null;
                                            try {
                                                $results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $scenario, $Fund);
                                                if (isset($results) && is_array($results) && count($results) > 0) {
                                                    $summary = [];

                                                    foreach ($results as $row) {
                                                        $pilar_name = $row['pilar_name'];
                                                        $Default_Faculty = $row['Default_Faculty'];
                                                        $Faculty = $row['Faculty'];
                                                        $Plan = $row['Plan'];
                                                        $Sub_Plan = $row['Sub_Plan'];
                                                        $Project = $row['Project'];

                                                        // เก็บข้อมูลของ pilar_name
                                                        if (!isset($summary[$pilar_name])) {
                                                            $summary[$pilar_name] = [
                                                                'name' => $row['pilar_name'],
                                                                'Total_Amount_1_1' => 0,
                                                                'Total_Amount_1_2' => 0,
                                                                'Total_Amount_1_3' => 0,
                                                                'Total_Amount_1_4' => 0,
                                                                'Total_Amount_1_4_1' => 0,
                                                                'Total_Amount_1_4_2' => 0,
                                                                'Total_Amount_1_4_3' => 0,
                                                                'Total_Amount_1_4_4' => 0,
                                                                'Total_Amount_1_4_5' => 0,
                                                                'Total_Amount_1_4_6' => 0,
                                                                'Total_Amount_1_4_7' => 0,
                                                                'Total_Amount_2_1' => 0,
                                                                'Total_Amount_2_2' => 0,
                                                                'Total_Amount_2_3' => 0,
                                                                'Total_Amount_2_4' => 0,
                                                                'Total_Amount_2_5' => 0,
                                                                'Total_Amount_2_6' => 0,
                                                                'Total_Amount_3_1' => 0,
                                                                'Total_Amount_3_2' => 0,
                                                                'Total_Amount_3_3' => 0,
                                                                'Total_Amount_4' => 0,
                                                                'Total_Amount_5' => 0,
                                                                'Default_Faculty' => [],
                                                            ];
                                                        }

                                                        // เก็บข้อมูลของ Default_Faculty
                                                        if (!isset($summary[$pilar_name]['Default_Faculty'][$Default_Faculty])) {
                                                            $summary[$pilar_name]['Default_Faculty'][$Default_Faculty] = [
                                                                'name' => $row['Default_Faculty'],
                                                                'Total_Amount_1_1' => 0,
                                                                'Total_Amount_1_2' => 0,
                                                                'Total_Amount_1_3' => 0,
                                                                'Total_Amount_1_4' => 0,
                                                                'Total_Amount_1_4_1' => 0,
                                                                'Total_Amount_1_4_2' => 0,
                                                                'Total_Amount_1_4_3' => 0,
                                                                'Total_Amount_1_4_4' => 0,
                                                                'Total_Amount_1_4_5' => 0,
                                                                'Total_Amount_1_4_6' => 0,
                                                                'Total_Amount_1_4_7' => 0,
                                                                'Total_Amount_2_1' => 0,
                                                                'Total_Amount_2_2' => 0,
                                                                'Total_Amount_2_3' => 0,
                                                                'Total_Amount_2_4' => 0,
                                                                'Total_Amount_2_5' => 0,
                                                                'Total_Amount_2_6' => 0,
                                                                'Total_Amount_3_1' => 0,
                                                                'Total_Amount_3_2' => 0,
                                                                'Total_Amount_3_3' => 0,
                                                                'Total_Amount_4' => 0,
                                                                'Total_Amount_5' => 0,
                                                                'Faculty' => [],
                                                            ];
                                                        }

                                                        // เก็บข้อมูลของ Faculty
                                                        if (!isset($summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty])) {
                                                            $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty] = [
                                                                'name' => str_replace('-', ':', $row['Faculty_name'] ?? ''),
                                                                'Total_Amount_1_1' => 0,
                                                                'Total_Amount_1_2' => 0,
                                                                'Total_Amount_1_3' => 0,
                                                                'Total_Amount_1_4' => 0,
                                                                'Total_Amount_1_4_1' => 0,
                                                                'Total_Amount_1_4_2' => 0,
                                                                'Total_Amount_1_4_3' => 0,
                                                                'Total_Amount_1_4_4' => 0,
                                                                'Total_Amount_1_4_5' => 0,
                                                                'Total_Amount_1_4_6' => 0,
                                                                'Total_Amount_1_4_7' => 0,
                                                                'Total_Amount_2_1' => 0,
                                                                'Total_Amount_2_2' => 0,
                                                                'Total_Amount_2_3' => 0,
                                                                'Total_Amount_2_4' => 0,
                                                                'Total_Amount_2_5' => 0,
                                                                'Total_Amount_2_6' => 0,
                                                                'Total_Amount_3_1' => 0,
                                                                'Total_Amount_3_2' => 0,
                                                                'Total_Amount_3_3' => 0,
                                                                'Total_Amount_4' => 0,
                                                                'Total_Amount_5' => 0,
                                                                'plan' => [],
                                                            ];
                                                        }

                                                        // เก็บข้อมูลของ Plan
                                                        if (!isset($summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan])) {
                                                            $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan] = [
                                                                'name' => $row['plan_name'],
                                                                'Total_Amount_1_1' => 0,
                                                                'Total_Amount_1_2' => 0,
                                                                'Total_Amount_1_3' => 0,
                                                                'Total_Amount_1_4' => 0,
                                                                'Total_Amount_1_4_1' => 0,
                                                                'Total_Amount_1_4_2' => 0,
                                                                'Total_Amount_1_4_3' => 0,
                                                                'Total_Amount_1_4_4' => 0,
                                                                'Total_Amount_1_4_5' => 0,
                                                                'Total_Amount_1_4_6' => 0,
                                                                'Total_Amount_1_4_7' => 0,
                                                                'Total_Amount_2_1' => 0,
                                                                'Total_Amount_2_2' => 0,
                                                                'Total_Amount_2_3' => 0,
                                                                'Total_Amount_2_4' => 0,
                                                                'Total_Amount_2_5' => 0,
                                                                'Total_Amount_2_6' => 0,
                                                                'Total_Amount_3_1' => 0,
                                                                'Total_Amount_3_2' => 0,
                                                                'Total_Amount_3_3' => 0,
                                                                'Total_Amount_4' => 0,
                                                                'Total_Amount_5' => 0,
                                                                'sub_plan' => [],
                                                            ];
                                                        }

                                                        // เก็บข้อมูลของ Sub_Plan
                                                        $SubPlanName = (!empty($row['sub_plan_name']))
                                                            ? htmlspecialchars(str_replace('SP_', '', $row['Sub_Plan'])) . " : " . htmlspecialchars(removeLeadingNumbers($row['sub_plan_name']))
                                                            : htmlspecialchars(str_replace('SP_', '', $row['Sub_Plan']));
                                                        if (!isset($summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan])) {
                                                            $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan] = [
                                                                'name' => $SubPlanName,
                                                                'Total_Amount_1_1' => 0,
                                                                'Total_Amount_1_2' => 0,
                                                                'Total_Amount_1_3' => 0,
                                                                'Total_Amount_1_4' => 0,
                                                                'Total_Amount_1_4_1' => 0,
                                                                'Total_Amount_1_4_2' => 0,
                                                                'Total_Amount_1_4_3' => 0,
                                                                'Total_Amount_1_4_4' => 0,
                                                                'Total_Amount_1_4_5' => 0,
                                                                'Total_Amount_1_4_6' => 0,
                                                                'Total_Amount_1_4_7' => 0,
                                                                'Total_Amount_2_1' => 0,
                                                                'Total_Amount_2_2' => 0,
                                                                'Total_Amount_2_3' => 0,
                                                                'Total_Amount_2_4' => 0,
                                                                'Total_Amount_2_5' => 0,
                                                                'Total_Amount_2_6' => 0,
                                                                'Total_Amount_3_1' => 0,
                                                                'Total_Amount_3_2' => 0,
                                                                'Total_Amount_3_3' => 0,
                                                                'Total_Amount_4' => 0,
                                                                'Total_Amount_5' => 0,
                                                                'Project' => [],
                                                            ];
                                                        }

                                                        // รวมข้อมูลของ pilar_name
                                                        $summary[$pilar_name]['Total_Amount_1_1'] += $row['Total_Amount_1_1'];
                                                        $summary[$pilar_name]['Total_Amount_1_2'] += $row['Total_Amount_1_2'];
                                                        $summary[$pilar_name]['Total_Amount_1_3'] += $row['Total_Amount_1_3'];
                                                        $summary[$pilar_name]['Total_Amount_1_4'] += $row['Total_Amount_1_4'];
                                                        $summary[$pilar_name]['Total_Amount_1_4_1'] += $row['Total_Amount_1_4_1'];
                                                        $summary[$pilar_name]['Total_Amount_1_4_2'] += $row['Total_Amount_1_4_2'];
                                                        $summary[$pilar_name]['Total_Amount_1_4_3'] += $row['Total_Amount_1_4_3'];
                                                        $summary[$pilar_name]['Total_Amount_1_4_4'] += $row['Total_Amount_1_4_4'];
                                                        $summary[$pilar_name]['Total_Amount_1_4_5'] += $row['Total_Amount_1_4_5'];
                                                        $summary[$pilar_name]['Total_Amount_1_4_6'] += $row['Total_Amount_1_4_6'];
                                                        $summary[$pilar_name]['Total_Amount_1_4_7'] += $row['Total_Amount_1_4_7'];
                                                        $summary[$pilar_name]['Total_Amount_2_1'] += $row['Total_Amount_2_1'];
                                                        $summary[$pilar_name]['Total_Amount_2_2'] += $row['Total_Amount_2_2'];
                                                        $summary[$pilar_name]['Total_Amount_2_3'] += $row['Total_Amount_2_3'];
                                                        $summary[$pilar_name]['Total_Amount_2_4'] += $row['Total_Amount_2_4'];
                                                        $summary[$pilar_name]['Total_Amount_2_5'] += $row['Total_Amount_2_5'];
                                                        $summary[$pilar_name]['Total_Amount_2_6'] += $row['Total_Amount_2_6'];
                                                        $summary[$pilar_name]['Total_Amount_3_1'] += $row['Total_Amount_3_1'];
                                                        $summary[$pilar_name]['Total_Amount_3_2'] += $row['Total_Amount_3_2'];
                                                        $summary[$pilar_name]['Total_Amount_3_3'] += $row['Total_Amount_3_3'];
                                                        $summary[$pilar_name]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                        $summary[$pilar_name]['Total_Amount_5'] += $row['Total_Amount_5'];

                                                        // รวมข้อมูลของ Default_Faculty
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_1'] += $row['Total_Amount_1_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_2'] += $row['Total_Amount_1_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_3'] += $row['Total_Amount_1_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_4'] += $row['Total_Amount_1_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_4_1'] += $row['Total_Amount_1_4_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_4_2'] += $row['Total_Amount_1_4_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_4_3'] += $row['Total_Amount_1_4_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_4_4'] += $row['Total_Amount_1_4_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_4_5'] += $row['Total_Amount_1_4_5'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_4_6'] += $row['Total_Amount_1_4_6'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_1_4_7'] += $row['Total_Amount_1_4_7'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_2_1'] += $row['Total_Amount_2_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_2_2'] += $row['Total_Amount_2_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_2_3'] += $row['Total_Amount_2_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_2_4'] += $row['Total_Amount_2_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_2_5'] += $row['Total_Amount_2_5'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_2_6'] += $row['Total_Amount_2_6'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_3_1'] += $row['Total_Amount_3_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_3_2'] += $row['Total_Amount_3_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_3_3'] += $row['Total_Amount_3_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Total_Amount_5'] += $row['Total_Amount_5'];

                                                        // รวมข้อมูลของ Faculty
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_1'] += $row['Total_Amount_1_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_2'] += $row['Total_Amount_1_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_3'] += $row['Total_Amount_1_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_4'] += $row['Total_Amount_1_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_4_1'] += $row['Total_Amount_1_4_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_4_2'] += $row['Total_Amount_1_4_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_4_3'] += $row['Total_Amount_1_4_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_4_4'] += $row['Total_Amount_1_4_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_4_5'] += $row['Total_Amount_1_4_5'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_4_6'] += $row['Total_Amount_1_4_6'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_1_4_7'] += $row['Total_Amount_1_4_7'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_2_1'] += $row['Total_Amount_2_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_2_2'] += $row['Total_Amount_2_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_2_3'] += $row['Total_Amount_2_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_2_4'] += $row['Total_Amount_2_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_2_5'] += $row['Total_Amount_2_5'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_2_6'] += $row['Total_Amount_2_6'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_3_1'] += $row['Total_Amount_3_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_3_2'] += $row['Total_Amount_3_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_3_3'] += $row['Total_Amount_3_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['Total_Amount_5'] += $row['Total_Amount_5'];

                                                        // รวมข้อมูลของ plan
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_1'] += $row['Total_Amount_1_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_2'] += $row['Total_Amount_1_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_3'] += $row['Total_Amount_1_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_4'] += $row['Total_Amount_1_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_4_1'] += $row['Total_Amount_1_4_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_4_2'] += $row['Total_Amount_1_4_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_4_3'] += $row['Total_Amount_1_4_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_4_4'] += $row['Total_Amount_1_4_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_4_5'] += $row['Total_Amount_1_4_5'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_4_6'] += $row['Total_Amount_1_4_6'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_1_4_7'] += $row['Total_Amount_1_4_7'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_2_1'] += $row['Total_Amount_2_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_2_2'] += $row['Total_Amount_2_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_2_3'] += $row['Total_Amount_2_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_2_4'] += $row['Total_Amount_2_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_2_5'] += $row['Total_Amount_2_5'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_2_6'] += $row['Total_Amount_2_6'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_3_1'] += $row['Total_Amount_3_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_3_2'] += $row['Total_Amount_3_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_3_3'] += $row['Total_Amount_3_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['Total_Amount_5'] += $row['Total_Amount_5'];

                                                        // รวมข้อมูลของ Sub_Plan
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_1'] += $row['Total_Amount_1_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_2'] += $row['Total_Amount_1_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_3'] += $row['Total_Amount_1_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_4'] += $row['Total_Amount_1_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_4_1'] += $row['Total_Amount_1_4_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_4_2'] += $row['Total_Amount_1_4_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_4_3'] += $row['Total_Amount_1_4_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_4_4'] += $row['Total_Amount_1_4_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_4_5'] += $row['Total_Amount_1_4_5'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_4_6'] += $row['Total_Amount_1_4_6'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_1_4_7'] += $row['Total_Amount_1_4_7'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_2_1'] += $row['Total_Amount_2_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_2_2'] += $row['Total_Amount_2_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_2_3'] += $row['Total_Amount_2_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_2_4'] += $row['Total_Amount_2_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_2_5'] += $row['Total_Amount_2_5'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_2_6'] += $row['Total_Amount_2_6'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_3_1'] += $row['Total_Amount_3_1'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_3_2'] += $row['Total_Amount_3_2'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_3_3'] += $row['Total_Amount_3_3'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                        $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Total_Amount_5'] += $row['Total_Amount_5'];


                                                        // เก็บข้อมูลของ Project
                                                        if (!isset($summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Project'][$Project])) {
                                                            $summary[$pilar_name]['Default_Faculty'][$Default_Faculty]['Faculty'][$Faculty]['plan'][$Plan]['sub_plan'][$Sub_Plan]['Project'][$Project] = [
                                                                'name' => $row['project_name'],
                                                                'Total_Amount_1_1' => $row['Total_Amount_1_1'],
                                                                'Total_Amount_1_2' => $row['Total_Amount_1_2'],
                                                                'Total_Amount_1_3' => $row['Total_Amount_1_3'],
                                                                'Total_Amount_1_4' => $row['Total_Amount_1_4'],
                                                                'Total_Amount_1_4_1' => $row['Total_Amount_1_4_1'],
                                                                'Total_Amount_1_4_2' => $row['Total_Amount_1_4_2'],
                                                                'Total_Amount_1_4_3' => $row['Total_Amount_1_4_3'],
                                                                'Total_Amount_1_4_4' => $row['Total_Amount_1_4_4'],
                                                                'Total_Amount_1_4_5' => $row['Total_Amount_1_4_5'],
                                                                'Total_Amount_1_4_6' => $row['Total_Amount_1_4_6'],
                                                                'Total_Amount_1_4_7' => $row['Total_Amount_1_4_7'],
                                                                'Total_Amount_2_1' => $row['Total_Amount_2_1'],
                                                                'Total_Amount_2_2' => $row['Total_Amount_2_2'],
                                                                'Total_Amount_2_3' => $row['Total_Amount_2_3'],
                                                                'Total_Amount_2_4' => $row['Total_Amount_2_4'],
                                                                'Total_Amount_2_5' => $row['Total_Amount_2_5'],
                                                                'Total_Amount_2_6' => $row['Total_Amount_2_6'],
                                                                'Total_Amount_3_1' => $row['Total_Amount_3_1'],
                                                                'Total_Amount_3_2' => $row['Total_Amount_3_2'],
                                                                'Total_Amount_3_3' => $row['Total_Amount_3_3'],
                                                                'Total_Amount_4' => $row['Total_Amount_4'],
                                                                'Total_Amount_5' => $row['Total_Amount_5'],
                                                            ];
                                                            $rows = $summary;
                                                            // ตัวแปรสำหรับเก็บผลรวมทั้งหมด
                                                            $total_summary = [
                                                                'Total_Amount_1_1' => 0,
                                                                'Total_Amount_1_2' => 0,
                                                                'Total_Amount_1_3' => 0,
                                                                'Total_Amount_1_4' => 0,
                                                                'Total_Amount_1_4_1' => 0,
                                                                'Total_Amount_1_4_2' => 0,
                                                                'Total_Amount_1_4_3' => 0,
                                                                'Total_Amount_1_4_4' => 0,
                                                                'Total_Amount_1_4_5' => 0,
                                                                'Total_Amount_1_4_6' => 0,
                                                                'Total_Amount_1_4_7' => 0,
                                                                'Total_Amount_2_1' => 0,
                                                                'Total_Amount_2_2' => 0,
                                                                'Total_Amount_2_3' => 0,
                                                                'Total_Amount_2_4' => 0,
                                                                'Total_Amount_2_5' => 0,
                                                                'Total_Amount_2_6' => 0,
                                                                'Total_Amount_3_1' => 0,
                                                                'Total_Amount_3_2' => 0,
                                                                'Total_Amount_3_3' => 0,
                                                                'Total_Amount_4' => 0,
                                                                'Total_Amount_5' => 0,
                                                            ];
                                                            // แสดงผลรวมทั้งหมด
                                                            //print_r($total_summary);
                                                            // Assuming this is inside a loop where $row is updated (e.g., from a database query)
                                                            foreach ($rows as $row) { // Replace $rows with your actual data source
                                                                // รวมผลรวมทั้งหมดโดยไม่สนใจ Faculty
                                                                $total_summary['Total_Amount_1_1'] += (float) ($row['Total_Amount_1_1'] ?? 0);
                                                                $total_summary['Total_Amount_1_2'] += (float) ($row['Total_Amount_1_2'] ?? 0);
                                                                $total_summary['Total_Amount_1_3'] += (float) ($row['Total_Amount_1_3'] ?? 0);
                                                                $total_summary['Total_Amount_1_4'] += (float) ($row['Total_Amount_1_4'] ?? 0);
                                                                $total_summary['Total_Amount_1_4_1'] += (float) ($row['Total_Amount_1_4_1'] ?? 0);
                                                                $total_summary['Total_Amount_1_4_2'] += (float) ($row['Total_Amount_1_4_2'] ?? 0);
                                                                $total_summary['Total_Amount_1_4_3'] += (float) ($row['Total_Amount_1_4_3'] ?? 0);
                                                                $total_summary['Total_Amount_1_4_4'] += (float) ($row['Total_Amount_1_4_4'] ?? 0);
                                                                $total_summary['Total_Amount_1_4_5'] += (float) ($row['Total_Amount_1_4_5'] ?? 0);
                                                                $total_summary['Total_Amount_1_4_6'] += (float) ($row['Total_Amount_1_4_6'] ?? 0);
                                                                $total_summary['Total_Amount_1_4_7'] += (float) ($row['Total_Amount_1_4_7'] ?? 0);
                                                                $total_summary['Total_Amount_2_1'] += (float) ($row['Total_Amount_2_1'] ?? 0);
                                                                $total_summary['Total_Amount_2_2'] += (float) ($row['Total_Amount_2_2'] ?? 0);
                                                                $total_summary['Total_Amount_2_3'] += (float) ($row['Total_Amount_2_3'] ?? 0);
                                                                $total_summary['Total_Amount_2_4'] += (float) ($row['Total_Amount_2_4'] ?? 0);
                                                                $total_summary['Total_Amount_2_5'] += (float) ($row['Total_Amount_2_5'] ?? 0);
                                                                $total_summary['Total_Amount_2_6'] += (float) ($row['Total_Amount_2_6'] ?? 0);
                                                                $total_summary['Total_Amount_3_1'] += (float) ($row['Total_Amount_3_1'] ?? 0);
                                                                $total_summary['Total_Amount_3_2'] += (float) ($row['Total_Amount_3_2'] ?? 0);
                                                                $total_summary['Total_Amount_3_3'] += (float) ($row['Total_Amount_3_3'] ?? 0);
                                                                $total_summary['Total_Amount_4'] += (float) ($row['Total_Amount_4'] ?? 0);
                                                                $total_summary['Total_Amount_5'] += (float) ($row['Total_Amount_5'] ?? 0);
                                                            }
                                                        }
                                                    }

                                                    if (isset($summary) && is_array($summary)) {
                                                        $total = $total_summary['Total_Amount_1_1']
                                                            + $total_summary['Total_Amount_1_2']
                                                            + $total_summary['Total_Amount_1_3']
                                                            + $total_summary['Total_Amount_1_4']
                                                            + $total_summary['Total_Amount_1_4_1']
                                                            + $total_summary['Total_Amount_1_4_2']
                                                            + $total_summary['Total_Amount_1_4_3']
                                                            + $total_summary['Total_Amount_1_4_4']
                                                            + $total_summary['Total_Amount_1_4_5']
                                                            + $total_summary['Total_Amount_1_4_6']
                                                            + $total_summary['Total_Amount_2_1']
                                                            + $total_summary['Total_Amount_2_2']
                                                            + $total_summary['Total_Amount_2_3']
                                                            + $total_summary['Total_Amount_2_4']
                                                            + $total_summary['Total_Amount_2_5']
                                                            + $total_summary['Total_Amount_2_6']
                                                            + $total_summary['Total_Amount_3_1']
                                                            + $total_summary['Total_Amount_3_2']
                                                            + $total_summary['Total_Amount_3_3']
                                                            + $total_summary['Total_Amount_4']
                                                            + $total_summary['Total_Amount_5']
                                                        ;
                                                        echo "<tr>";
                                                        echo "<td style='text-align: left;'>" . 'รวมทั้งสิ้น' . "<br></td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_4_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_4_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_4_3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_4_4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_4_5']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_4_6']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1_4_7']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_2_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_2_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_2_3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_2_4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_2_5']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_2_6']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_3_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_3_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_3_3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_5']) . "</td>";
                                                        echo "<td>" . formatNumber($total) . "</td>";
                                                        echo "</tr>";
                                                    } else {
                                                        echo "<tr><td colspan='29' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                                    }

                                                    // แสดงผลลัพธ์ในรูปแบบตาราง
                                                    foreach ($summary as $pilar_name => $data1) {
                                                        $total = $data1['Total_Amount_1_1']
                                                            + $data1['Total_Amount_1_2']
                                                            + $data1['Total_Amount_1_3']
                                                            + $data1['Total_Amount_1_4']
                                                            + $data1['Total_Amount_1_4_1']
                                                            + $data1['Total_Amount_1_4_2']
                                                            + $data1['Total_Amount_1_4_3']
                                                            + $data1['Total_Amount_1_4_4']
                                                            + $data1['Total_Amount_1_4_5']
                                                            + $data1['Total_Amount_1_4_6']
                                                            + $data1['Total_Amount_1_4_7']
                                                            + $data1['Total_Amount_2_1']
                                                            + $data1['Total_Amount_2_2']
                                                            + $data1['Total_Amount_2_3']
                                                            + $data1['Total_Amount_2_4']
                                                            + $data1['Total_Amount_2_5']
                                                            + $data1['Total_Amount_2_6']
                                                            + $data1['Total_Amount_3_1']
                                                            + $data1['Total_Amount_3_2']
                                                            + $data1['Total_Amount_3_3']
                                                            + $data1['Total_Amount_4']
                                                            + $data1['Total_Amount_5']
                                                        ;
                                                        echo "<tr>";
                                                        if ($selectedFaculty == null) {
                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 8) . htmlspecialchars($data1['name'] ?? '') . "</td>";
                                                        } else {

                                                            echo "<td style='text-align: left;'>" . htmlspecialchars($data1['name'] ?? '') . "</td>";
                                                        }
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_1']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_3']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_4']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_4_1']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_4_2']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_4_3']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_4_4']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_4_5']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_4_6']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_1_4_7']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_2_1']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_2_2']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_2_3']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_2_4']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_2_5']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_2_6']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_3_1']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_3_2']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_3_3']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_4']) . "</td>";
                                                        echo "<td>" . formatNumber($data1['Total_Amount_5']) . "</td>";
                                                        echo "<td>" . formatNumber($total) . "</td>";
                                                        echo "</tr>";

                                                        foreach ($data1['Default_Faculty'] as $Default_Faculty => $data2) {
                                                            $total = $data2['Total_Amount_1_1']
                                                                + $data2['Total_Amount_1_2']
                                                                + $data2['Total_Amount_1_3']
                                                                + $data2['Total_Amount_1_4']
                                                                + $data2['Total_Amount_1_4_1']
                                                                + $data2['Total_Amount_1_4_2']
                                                                + $data2['Total_Amount_1_4_3']
                                                                + $data2['Total_Amount_1_4_4']
                                                                + $data2['Total_Amount_1_4_5']
                                                                + $data2['Total_Amount_1_4_6']
                                                                + $data2['Total_Amount_1_4_7']
                                                                + $data2['Total_Amount_2_1']
                                                                + $data2['Total_Amount_2_2']
                                                                + $data2['Total_Amount_2_3']
                                                                + $data2['Total_Amount_2_4']
                                                                + $data2['Total_Amount_2_5']
                                                                + $data2['Total_Amount_2_6']
                                                                + $data2['Total_Amount_3_1']
                                                                + $data2['Total_Amount_3_2']
                                                                + $data2['Total_Amount_3_3']
                                                                + $data2['Total_Amount_4']
                                                                + $data2['Total_Amount_5']
                                                            ;
                                                            echo "<tr>";
                                                            if ($selectedFaculty == null) {
                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 16) . htmlspecialchars($data2['name'] ?? '') . "</td>";
                                                            } else {
                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 8) . htmlspecialchars($data2['name'] ?? '') . "</td>";
                                                            }
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_1']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_2']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_3']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_4']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_4_1']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_4_2']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_4_3']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_4_4']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_4_5']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_4_6']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_1_4_7']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_2_1']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_2_2']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_2_3']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_2_4']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_2_5']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_2_6']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_3_1']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_3_2']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_3_3']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_4']) . "</td>";
                                                            echo "<td>" . formatNumber($data2['Total_Amount_5']) . "</td>";
                                                            echo "<td>" . formatNumber($total) . "</td>";
                                                            echo "</tr>";

                                                            foreach ($data2['Faculty'] as $Faculty => $data3) {
                                                                $total = $data3['Total_Amount_1_1']
                                                                    + $data3['Total_Amount_1_2']
                                                                    + $data3['Total_Amount_1_3']
                                                                    + $data3['Total_Amount_1_4']
                                                                    + $data3['Total_Amount_1_4_1']
                                                                    + $data3['Total_Amount_1_4_2']
                                                                    + $data3['Total_Amount_1_4_3']
                                                                    + $data3['Total_Amount_1_4_4']
                                                                    + $data3['Total_Amount_1_4_5']
                                                                    + $data3['Total_Amount_1_4_6']
                                                                    + $data3['Total_Amount_1_4_7']
                                                                    + $data3['Total_Amount_2_1']
                                                                    + $data3['Total_Amount_2_2']
                                                                    + $data3['Total_Amount_2_3']
                                                                    + $data3['Total_Amount_2_4']
                                                                    + $data3['Total_Amount_2_5']
                                                                    + $data3['Total_Amount_2_6']
                                                                    + $data3['Total_Amount_3_1']
                                                                    + $data3['Total_Amount_3_2']
                                                                    + $data3['Total_Amount_3_3']
                                                                    + $data3['Total_Amount_4']
                                                                    + $data3['Total_Amount_5']
                                                                ;
                                                                echo "<tr>";
                                                                if ($selectedFaculty == null) {
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 24) . htmlspecialchars($data3['name'] ?? '') . "</td>";
                                                                } else {
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 16) . htmlspecialchars($data3['name'] ?? '') . "</td>";
                                                                }
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_1']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_2']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_3']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_4']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_4_1']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_4_2']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_4_3']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_4_4']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_4_5']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_4_6']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_1_4_7']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_2_1']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_2_2']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_2_3']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_2_4']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_2_5']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_2_6']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_3_1']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_3_2']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_3_3']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_4']) . "</td>";
                                                                echo "<td>" . formatNumber($data3['Total_Amount_5']) . "</td>";
                                                                echo "<td>" . formatNumber($total) . "</td>";
                                                                echo "</tr>";

                                                                foreach ($data3['plan'] as $plan => $data4) {
                                                                    $total = $data4['Total_Amount_1_1']
                                                                        + $data4['Total_Amount_1_2']
                                                                        + $data4['Total_Amount_1_3']
                                                                        + $data4['Total_Amount_1_4']
                                                                        + $data4['Total_Amount_1_4_1']
                                                                        + $data4['Total_Amount_1_4_2']
                                                                        + $data4['Total_Amount_1_4_3']
                                                                        + $data4['Total_Amount_1_4_4']
                                                                        + $data4['Total_Amount_1_4_5']
                                                                        + $data4['Total_Amount_1_4_6']
                                                                        + $data4['Total_Amount_1_4_7']
                                                                        + $data4['Total_Amount_2_1']
                                                                        + $data4['Total_Amount_2_2']
                                                                        + $data4['Total_Amount_2_3']
                                                                        + $data4['Total_Amount_2_4']
                                                                        + $data4['Total_Amount_2_5']
                                                                        + $data4['Total_Amount_2_6']
                                                                        + $data4['Total_Amount_3_1']
                                                                        + $data4['Total_Amount_3_2']
                                                                        + $data4['Total_Amount_3_3']
                                                                        + $data4['Total_Amount_4']
                                                                        + $data4['Total_Amount_5']
                                                                    ;
                                                                    echo "<tr>";
                                                                    if ($selectedFaculty == null) {
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . htmlspecialchars($data4['name'] ?? '') . "</td>";
                                                                    } else {
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 24) . htmlspecialchars($data4['name'] ?? '') . "</td>";
                                                                    }
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_3']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_4']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_4_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_4_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_4_3']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_4_4']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_4_5']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_4_6']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_1_4_7']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_2_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_2_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_2_3']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_2_4']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_2_5']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_2_6']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_3_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_3_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_3_3']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_4']) . "</td>";
                                                                    echo "<td>" . formatNumber($data4['Total_Amount_5']) . "</td>";
                                                                    echo "<td>" . formatNumber($total) . "</td>";
                                                                    echo "</tr>";

                                                                    foreach ($data4['sub_plan'] as $Sub_Plan => $data5) {
                                                                        $total = $data5['Total_Amount_1_1']
                                                                            + $data5['Total_Amount_1_2']
                                                                            + $data5['Total_Amount_1_3']
                                                                            + $data5['Total_Amount_1_4']
                                                                            + $data5['Total_Amount_1_4_1']
                                                                            + $data5['Total_Amount_1_4_2']
                                                                            + $data5['Total_Amount_1_4_3']
                                                                            + $data5['Total_Amount_1_4_4']
                                                                            + $data5['Total_Amount_1_4_5']
                                                                            + $data5['Total_Amount_1_4_6']
                                                                            + $data5['Total_Amount_1_4_7']
                                                                            + $data5['Total_Amount_2_1']
                                                                            + $data5['Total_Amount_2_2']
                                                                            + $data5['Total_Amount_2_3']
                                                                            + $data5['Total_Amount_2_4']
                                                                            + $data5['Total_Amount_2_5']
                                                                            + $data5['Total_Amount_2_6']
                                                                            + $data5['Total_Amount_3_1']
                                                                            + $data5['Total_Amount_3_2']
                                                                            + $data5['Total_Amount_3_3']
                                                                            + $data5['Total_Amount_4']
                                                                            + $data5['Total_Amount_5']
                                                                        ;
                                                                        echo "<tr>";
                                                                        if ($selectedFaculty == null) {
                                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 40) . htmlspecialchars($data5['name'] ?? '') . "</td>";
                                                                        } else {
                                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . htmlspecialchars($data5['name'] ?? '') . "</td>";
                                                                        }

                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_1']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_3']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_4']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_4_1']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_4_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_4_3']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_4_4']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_4_5']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_4_6']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_1_4_7']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_2_1']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_2_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_2_3']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_2_4']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_2_5']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_2_6']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_3_1']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_3_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_3_3']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_4']) . "</td>";
                                                                        echo "<td>" . formatNumber($data5['Total_Amount_5']) . "</td>";
                                                                        echo "<td>" . formatNumber($total) . "</td>";
                                                                        echo "</tr>";

                                                                        foreach ($data5['Project'] as $Project => $data6) {
                                                                            $total = $data6['Total_Amount_1_1']
                                                                                + $data6['Total_Amount_1_2']
                                                                                + $data6['Total_Amount_1_3']
                                                                                + $data6['Total_Amount_1_4']
                                                                                + $data6['Total_Amount_1_4_1']
                                                                                + $data6['Total_Amount_1_4_2']
                                                                                + $data6['Total_Amount_1_4_3']
                                                                                + $data6['Total_Amount_1_4_4']
                                                                                + $data6['Total_Amount_1_4_5']
                                                                                + $data6['Total_Amount_1_4_6']
                                                                                + $data6['Total_Amount_1_4_7']
                                                                                + $data6['Total_Amount_2_1']
                                                                                + $data6['Total_Amount_2_2']
                                                                                + $data6['Total_Amount_2_3']
                                                                                + $data6['Total_Amount_2_4']
                                                                                + $data6['Total_Amount_2_5']
                                                                                + $data6['Total_Amount_2_6']
                                                                                + $data6['Total_Amount_3_1']
                                                                                + $data6['Total_Amount_3_2']
                                                                                + $data6['Total_Amount_3_3']
                                                                                + $data6['Total_Amount_4']
                                                                                + $data6['Total_Amount_5']
                                                                            ;
                                                                            echo "<tr>";
                                                                            if ($selectedFaculty == null) {
                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 48) . htmlspecialchars($data6['name'] ?? '') . "</td>";
                                                                            } else {
                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 40) . htmlspecialchars($data6['name'] ?? '') . "</td>";
                                                                            }

                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_1']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_2']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_3']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_4']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_4_1']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_4_2']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_4_3']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_4_4']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_4_5']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_4_6']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_1_4_7']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_2_1']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_2_2']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_2_3']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_2_4']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_2_5']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_2_6']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_3_1']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_3_2']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_3_3']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_4']) . "</td>";
                                                                            echo "<td>" . formatNumber($data6['Total_Amount_5']) . "</td>";
                                                                            echo "<td>" . formatNumber($total) . "</td>";
                                                                            echo "</tr>";
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='11' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                                }
                                            } catch (Exception $e) {
                                                echo "<tr><td colspan='11' style='color: red; font-weight: bold; font-size: 18px;'>    เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</td></tr>";
                                            }
                                            ?>

                                        </tbody>
                                    </table>
                                    <script>
                                        // การส่งค่าของ selectedFaculty ไปยัง JavaScript
                                        var selectedFaculty = "<?php echo isset($selectedFaculty) ? htmlspecialchars($selectedFaculty, ENT_QUOTES, 'UTF-8') : ''; ?>";
                                        console.log('Selected Faculty: ', selectedFaculty);


                                    </script>


                                </div>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLSX()" class="btn btn-success m-t-15">Export XLSX</button>
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

            // ฟังก์ชันช่วยเติมค่าซ้ำ
            const repeatValue = (value, count) => Array(count).fill(value).join(',');

            // เพิ่มชื่อรายงาน
            csvRows.push(`"รายงานสรุปคำขอรายโครงการ",,,,,,,,,,,,,,,,,,,,,,,,,,,`);

            // ดึงค่าคณะ/หน่วยงานจาก PHP
            const selectedFacultyName = <?php echo json_encode($selectedFacultyName); ?>;
            const facultyData = selectedFacultyName.replace(/-/g, ':');
            csvRows.push(`"ส่วนงาน / หน่วยงาน: ${facultyData}",,,,,,,,,,,,,,,,,,,,,,,,,,,`);

            // เพิ่มปีงบที่ต้องการเปรียบเทียบ
            const selectedYear = <?php echo json_encode($selectedYear); ?>;
            csvRows.push(`"ปีงบที่ต้องการเปรียบเทียบ: ${selectedYear}",,,,,,,,,,,,,,,,,,,,,,,,,,,`);

            // เพิ่มประเภทงบประมาณ
            const scenario = <?php echo json_encode($scenario); ?>;
            csvRows.push(`"ประเภทงบประมาณ: ${scenario ? scenario : 'แสดงทุกประเภทงบประมาณ'}",,,,,,,,,,,,,,,,,,,,,,,,,,,`);

            // แถวที่ 1: โครงสร้างตารางหลัก
            csvRows.push([
                "โครงการ/กิจกรรม",
                "งบประมาณ 1.ค่าใช้จ่ายบุคลากร", "", "", "", "", "", "", "", "", "", "",
                "งบประมาณ 2.ค่าใช้จ่ายดำเนินงาน", "", "", "", "", "",
                "งบประมาณ 3.ค่าใช้จ่ายลงทุน", "", "",
                "4. ค่าใช้จ่ายเงินอุดหนุนการดำเนินงาน",
                "5. ค่าใช้จ่ายอื่น",
                "รวมงบประมาณ"
            ].join(","));

            // แถวที่ 2: กลุ่มค่าใช้จ่าย
            csvRows.push([
                "",
                "1.1 เงินเดือนข้าราชการและลูกจ้างประจำ",
                "1.2 ค่าจ้างพนักงานมหาวิทยาลัย",
                "1.3 ค่าจ้างลูกจ้างมหาวิทยาลัย",
                "1.4 กองทุนสำรองผลประโยชน์และสวัสดิการ",
                "เงินสมทบประกันสังคม",
                "เงินสมทบกองทุนสำรองเลี้ยงชีพ",
                "เงินชดเชยเลิกจ้าง",
                "เงินสมทบกองทุนเงินทดแทน",
                "กบข.",
                "กสจ.",
                "สวัสดิการอื่น ๆ",
                "ค่าตอบแทน",
                "ค่าใช้สอย",
                "ค่าวัสดุ",
                "ค่าสาธารณูปโภค",
                "ค่าฝึกอบรม",
                "ค่าเดินทาง",
                "ค่าครุภัณฑ์",
                "ค่าที่ดินและสิ่งก่อสร้าง",
                "ค่าที่ดิน",
                "",
                "",
                ""
            ].join(","));

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
            link.download = 'รายงานสรุปคำขอรายโครงการ.csv';
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
            doc.text("รายงานสรุปคำขอรายโครงการ", 10, 500);

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
            doc.save('รายงานสรุปคำขอรายโครงการ.pdf');
        }

        function exportXLSX() {
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
            link.download = 'รายงานสรุปคำขอรายโครงการ.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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