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
function fetchBudgetData($conn, $selectedFaculty = null, $budget_year1 = null, $budget_year2 = null, $scenario = null)
{
    $budget_year1 = $budget_year1 ?? 2568;
    $budget_year2 = $budget_year2 ?? 2567;
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
    ppp.pilar_name 
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
    tm.kku_item_name,
    tm.Reason,
    tm.Service,
    tm.Scenario,
    SUM(CASE WHEN tm.Budget_Management_Year =  $budget_year1 AND tm.Fund = 'FN06'THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_Quantity_FN06_1,
    SUM(CASE WHEN tm.Budget_Management_Year =  $budget_year1 AND tm.Fund = 'FN02'THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_Quantity_FN02_1,
    SUM(CASE WHEN tm.Budget_Management_Year =  $budget_year1 AND tm.Fund = 'FN08'THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_Quantity_FN08_1,
    SUM(CASE WHEN tm.Budget_Management_Year =  $budget_year2 AND tm.Fund = 'FN06'THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_Quantity_FN06_2,
    SUM(CASE WHEN tm.Budget_Management_Year =  $budget_year2 AND tm.Fund = 'FN02'THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_Quantity_FN02_2,
    SUM(CASE WHEN tm.Budget_Management_Year =  $budget_year2 AND tm.Fund = 'FN08'THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_Quantity_FN08_2
FROM 
    budget_planning_annual_budget_plan tm  
GROUP BY  
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
totalActual AS (
SELECT 
    ta.Faculty,
    ta.Plan,
    ta.Sub_plan,
    ta.Project,   
    ta.account,
    ta.Service,
    ta.Scenario,
    SUM(CASE WHEN ta.`YEAR` =  $budget_year1 AND ta.Fund = 'FN06'THEN ta.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Allocated_Quantity_FN06_1,
    SUM(CASE WHEN ta.`YEAR` =  $budget_year1 AND ta.Fund = 'FN02'THEN ta.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Allocated_Quantity_FN02_1,
    SUM(CASE WHEN ta.`YEAR` =  $budget_year1 AND ta.Fund = 'FN08'THEN ta.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Allocated_Quantity_FN08_1,
    SUM(CASE WHEN ta.`YEAR` =  $budget_year2 AND ta.Fund = 'FN06'THEN ta.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Allocated_Quantity_FN06_2,
    SUM(CASE WHEN ta.`YEAR` =  $budget_year2 AND ta.Fund = 'FN02'THEN ta.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Allocated_Quantity_FN02_2,
    SUM(CASE WHEN ta.`YEAR` =  $budget_year2 AND ta.Fund = 'FN08'THEN ta.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Allocated_Quantity_FN08_2
FROM 
    budget_planning_allocated_annual_budget_plan ta  
GROUP BY  
    ta.Faculty,
    ta.Plan,
    ta.Sub_plan,
    ta.Project,   
    ta.account,
    ta.Service,
    ta.Scenario
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
    END AS Name_a4,
    Total_Amount_Quantity_FN06_1 AS Amount_FN06_1,
    Total_Amount_Quantity_FN02_1 AS Amount_FN02_1,
    Total_Amount_Quantity_FN08_1 AS Amount_FN08_1,
    Total_Amount_Quantity_FN06_2 AS Amount_FN06_2,
    Total_Amount_Quantity_FN02_2 AS Amount_FN02_2,
    Total_Amount_Quantity_FN08_2 AS Amount_FN08_2,
    Total_Allocated_Quantity_FN06_1 AS Allocated_FN06_1,
    Total_Allocated_Quantity_FN02_1 AS Allocated_FN02_1,
    Total_Allocated_Quantity_FN08_1 AS Allocated_FN08_1,
    Total_Allocated_Quantity_FN06_2 AS Allocated_FN06_2,
    Total_Allocated_Quantity_FN02_2 AS Allocated_FN02_2,
    Total_Allocated_Quantity_FN08_2 AS Allocated_FN08_2
    
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
LEFT JOIN totalActual ta
ON ta.Faculty = tm.Faculty
AND ta.Plan = tm.Plan
AND ta.Sub_Plan = tm.Sub_Plan
AND ta.Project = tm.Project
AND ta.account = tm.Account
AND ta.Service = tm.Service
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
    tm.Reason,
    m.TotalLevels,
    m.GreatGrandparentAccount,
    m.GrandparentAccount,
    m.ParentAccount,
    m.GreatGrandparent,
    m.Grandparent,
    m.Parent,
    m.Current,
    Total_Amount_Quantity_FN06_1 ,
    Total_Amount_Quantity_FN02_1 ,
    Total_Amount_Quantity_FN08_1 ,
    Total_Amount_Quantity_FN06_2 ,
    Total_Amount_Quantity_FN02_2 ,
    Total_Amount_Quantity_FN08_2 ,
    Total_Allocated_Quantity_FN06_1 ,
    Total_Allocated_Quantity_FN02_1 ,
    Total_Allocated_Quantity_FN08_1 ,
    Total_Allocated_Quantity_FN06_2 ,
    Total_Allocated_Quantity_FN02_2 ,
    Total_Allocated_Quantity_FN08_2 
 
ORDER BY fta.Alias_Default asc, tm.Faculty ASC , tm.Plan ASC, tm.Sub_Plan ASC, tm.Project ASC, Name_a1 ASC,Name_a2 ASC,Name_a3 ASC,Name_a4 ASC,tm.Account ASC
 
)

SELECT * FROM t1";

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

$results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $budget_year2, $scenario);

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



<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>

<body class="v-light vertical-nav fix-header fix-sidebar">
    <div id="main-wrapper">
        <?php include('../component/left-nev.php') ?>

        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4> รายงานรายละเอียดตัวชี้วัดและงบประมาณรายจ่ายประจำปี จำแนกตามแผนงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">
                                รายงานรายละเอียดตัวชี้วัดและงบประมาณรายจ่ายประจำปี จำแนกตามแผนงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4> รายงานรายละเอียดตัวชี้วัดและงบประมาณรายจ่ายประจำปี จำแนกตามแผนงาน</h4>
                                </div>

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
                                        <label for="scenario" class="label-scenario" style="margin-right: 10px;">เลือก
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

                                        var baseUrl = "http://202.28.118.192:8081/template-vertical-nav/report-budget-comparison.php";
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

                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="18" style='text-align: left;'>
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
                                                <th colspan="18" style='text-align: left;'>
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
                                                <th colspan="18" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        echo "ประเภทงบประมาณ: " . (!empty($scenario) ? $scenario : "แสดงทุกประเภทงบประมาณ");
                                                        ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="18" style='text-align: left;'>
                                                    <span style="font-size: 16px;">


                                                        <?php
                                                        $facultyData = str_replace('-', ':', $selectedFacultyName);

                                                        echo "ส่วนงาน / หน่วยงาน: " . $facultyData; ?>
                                                    </span>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th rowspan="3" value="UOM">หน่วยนับของตัวชี้วัด (UOM)</th>
                                                <th colspan="5">ปี <?= ($selectedYear - 1) ?></th>
                                                <th colspan="8">ปี <?= ($selectedYear) ?></th>
                                                <th colspan="2" rowspan="2">เพิ่ม/ลด</th>
                                                <th rowspan="3" value="explain">คำชี้แจง</th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2">ปริมาณของตัวชี้วัด</th>
                                                <th rowspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th rowspan="2">เงินนอกงบประมาณ</th>
                                                <th rowspan="2">เงินรายได้</th>
                                                <th rowspan="2">รวม</th>
                                                <th rowspan="2" value="indicators">ปริมาณของตัวชี้วัด</th>
                                                <th colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th colspan="2">เงินนอกงบประมาณ</th>
                                                <th colspan="2">เงินรายได้</th>
                                                <th rowspan="2" value="sumfn">รวม</th>
                                            </tr>
                                            <tr>
                                                <th value="fn06-1">คำขอ</th>
                                                <th value="fn06-2">จัดสรร</th>
                                                <th value="fn08-1">คำขอ</th>
                                                <th value="fn08-2">จัดสรร</th>
                                                <th value="fn02-1">คำขอ</th>
                                                <th value="fn02-2">จัดสรร</th>
                                                <th value="quantity">จำนวน</th>
                                                <th value="percentage">ร้อยละ</th>
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
                                            $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;
                                            $budget_year2 = isset($_GET['year']) ? $_GET['year'] - 1 : null;
                                            $scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
                                            $results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $budget_year2, $scenario);

                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                $summary = [];


                                                // เรียงข้อมูลใน $results ให้เป็นแบบซ้อนกันตาม Faculty, Plan, Sub_Plan, Project, Type, SubType
                                                foreach ($results as $row) {
                                                    $Faculty = $row['Faculty'];
                                                    $Plan = $row['Plan'];
                                                    $Sub_Plan = $row['Sub_Plan'];
                                                    $Project = $row['Project'];
                                                    $Name_a1 = $row['Name_a1'];
                                                    $Name_a2 = $row['Name_a2'];
                                                    $Name_a3 = $row['Name_a3'];
                                                    $Name_a4 = $row['Name_a4'];
                                                    if (!isset($summary[$Faculty])) {
                                                        $summary[$Faculty] = [
                                                            'name' => $row['Faculty_name'],
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN06_2' => 0,
                                                            'Allocated_FN02_2' => 0,
                                                            'Allocated_FN08_2' => 0,
                                                            'plan' => [],
                                                        ];
                                                    }
                                                    if (!isset($summary[$Faculty]['plan'][$Plan])) {
                                                        $summary[$Faculty]['plan'][$Plan] = [
                                                            'name' => $row['plan_name'],
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN06_2' => 0,
                                                            'Allocated_FN02_2' => 0,
                                                            'Allocated_FN08_2' => 0,
                                                            'subPlan' => [],
                                                        ];
                                                    }
                                                    if (!isset($summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan])) {
                                                        $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan] = [
                                                            'name' => $row['sub_plan_name'],
                                                            'Faculty' => $row['Faculty'],
                                                            'Plan' => $row['Plan'],
                                                            'Sub_Plan' => $row['Sub_Plan'],
                                                            'ProjectID' => $row['Project'],
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN06_2' => 0,
                                                            'Allocated_FN02_2' => 0,
                                                            'Allocated_FN08_2' => 0,
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
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN06_2' => 0,
                                                            'Allocated_FN02_2' => 0,
                                                            'Allocated_FN08_2' => 0,
                                                            'name_a1' => [],
                                                        ];
                                                    }
                                                    $ItemName_a1 = (!empty($row['Name_a1']))
                                                        ? "" . htmlspecialchars($row['a1']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a1']))
                                                        : "" . htmlspecialchars($row['a1']) . "";
                                                    if (!isset($summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1])) {
                                                        $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1] = [
                                                            'a1' => $row['a1'],
                                                            'name' => $ItemName_a1,
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN06_2' => 0,
                                                            'Allocated_FN02_2' => 0,
                                                            'Allocated_FN08_2' => 0,
                                                            'Reason' => $row['Reason'],
                                                            'name_a2' => [],
                                                        ];
                                                    }
                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a2
                                                    if (!empty($row['a2']) && !empty($row['Name_a2']) && $row['Name_a2'] != $row['KKU_Item_Name']) {
                                                        $ItemName_a2 = htmlspecialchars($row['a2']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']));
                                                    } elseif (!empty($row['a2']) && !empty($row['Name_a2'])) {
                                                        $ItemName_a2 = "-" . " " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']));
                                                    } else {
                                                        $ItemName_a2 = "-" . " " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    if (!isset($summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2])) {
                                                        $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2] = [
                                                            'a1' => $row['a1'],
                                                            'a2' => $row['a2'],
                                                            'name' => $ItemName_a2,
                                                            'test' => $row['Name_a2'],
                                                            'test2' => $row['Name_a3'],
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN06_2' => 0,
                                                            'Allocated_FN02_2' => 0,
                                                            'Allocated_FN08_2' => 0,
                                                            'Reason' => $row['Reason'],
                                                            'name_a3' => [],
                                                        ];
                                                    }
                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a3
                                                    if (!empty($row['a3']) && !empty($row['Name_a3']) && $row['Name_a3'] != $row['KKU_Item_Name']) {
                                                        $ItemName_a3 = htmlspecialchars($row['a3']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a3']));
                                                    } elseif (!empty($row['a3']) && !empty($row['Name_a3'])) {
                                                        $ItemName_a3 = "-" . " " . htmlspecialchars(removeLeadingNumbers($row['Name_a3']));
                                                    } else {
                                                        $ItemName_a3 = "-" . " " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    if (!isset($summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3])) {
                                                        $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3] = [
                                                            'a2' => $row['a2'],
                                                            'a3' => $row['a3'],
                                                            'name' => $ItemName_a3,
                                                            'test' => $row['Name_a3'],
                                                            'test2' => $row['Name_a4'],
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN06_2' => 0,
                                                            'Allocated_FN02_2' => 0,
                                                            'Allocated_FN08_2' => 0,
                                                            'Reason' => $row['Reason'],
                                                            'name_a4' => [],
                                                        ];
                                                    }
                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a4
                                                    if (!empty($row['a4']) && !empty($row['Name_a4']) && $row['Name_a4'] != $row['KKU_Item_Name']) {
                                                        $ItemName_a4 = htmlspecialchars($row['a4']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a4']));
                                                    } elseif (!empty($row['a4']) && !empty($row['Name_a4'])) {
                                                        $ItemName_a4 = "-" . " " . htmlspecialchars(removeLeadingNumbers($row['Name_a4']));
                                                    } else {
                                                        $ItemName_a4 = "-" . " " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    if (!isset($summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4])) {
                                                        $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4] = [
                                                            'a3' => $row['a3'],
                                                            'a4' => $row['a4'],
                                                            'name' => $ItemName_a4,
                                                            'test' => $row['Name_a4'],
                                                            'test2' => $row['KKU_Item_Name'],
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN06_2' => 0,
                                                            'Allocated_FN02_2' => 0,
                                                            'Allocated_FN08_2' => 0,
                                                            'Reason' => $row['Reason'],
                                                            'kku_items' => [],
                                                        ];
                                                    }
                                                    //ผลรวมขึ้น Faculty
                                                    $summary[$Faculty]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$Faculty]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$Faculty]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$Faculty]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$Faculty]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$Faculty]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$Faculty]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$Faculty]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$Faculty]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$Faculty]['Allocated_FN06_2'] += $row['Allocated_FN06_2'];
                                                    $summary[$Faculty]['Allocated_FN02_2'] += $row['Allocated_FN02_2'];
                                                    $summary[$Faculty]['Allocated_FN08_2'] += $row['Allocated_FN08_2'];

                                                    //ผลรวมขึ้น Plan
                                                    $summary[$Faculty]['plan'][$Plan]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['Allocated_FN06_2'] += $row['Allocated_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['Allocated_FN02_2'] += $row['Allocated_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['Allocated_FN08_2'] += $row['Allocated_FN08_2'];

                                                    //ผลรวมขึ้น Sub_Plan
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Allocated_FN06_2'] += $row['Allocated_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Allocated_FN02_2'] += $row['Allocated_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['Allocated_FN08_2'] += $row['Allocated_FN08_2'];

                                                    //ผลรวมขึ้น Project
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Allocated_FN06_2'] += $row['Allocated_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Allocated_FN02_2'] += $row['Allocated_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['Allocated_FN08_2'] += $row['Allocated_FN08_2'];

                                                    //ผลรวมขึ้น Name_a1
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Allocated_FN06_2'] += $row['Allocated_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Allocated_FN02_2'] += $row['Allocated_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['Allocated_FN08_2'] += $row['Allocated_FN08_2'];

                                                    //ผลรวมขึ้น Name_a2
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Allocated_FN06_2'] += $row['Allocated_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Allocated_FN02_2'] += $row['Allocated_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['Allocated_FN08_2'] += $row['Allocated_FN08_2'];

                                                    //ผลรวมขึ้น Name_a3
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Allocated_FN06_2'] += $row['Allocated_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Allocated_FN02_2'] += $row['Allocated_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['Allocated_FN08_2'] += $row['Allocated_FN08_2'];

                                                    //ผลรวมขึ้น Name_a4
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Allocated_FN06_2'] += $row['Allocated_FN06_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Allocated_FN02_2'] += $row['Allocated_FN02_2'];
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['Allocated_FN08_2'] += $row['Allocated_FN08_2'];
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "" . "-" . " " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                        : "" . "";
                                                    $summary[$Faculty]['plan'][$Plan]['subPlan'][$Sub_Plan]['project'][$Project]['name_a1'][$Name_a1]['name_a2'][$Name_a2]['name_a3'][$Name_a3]['name_a4'][$Name_a4]['kku_items'][] = [
                                                        'name' => $kkuItemName,
                                                        'a4' => $row['a4'],
                                                        'a5' => $row['Account'],
                                                        'Amount_FN06_1' => $row['Amount_FN06_1'],
                                                        'Amount_FN02_1' => $row['Amount_FN02_1'],
                                                        'Amount_FN08_1' => $row['Amount_FN08_1'],
                                                        'Amount_FN06_2' => $row['Amount_FN06_2'],
                                                        'Amount_FN02_2' => $row['Amount_FN02_2'],
                                                        'Amount_FN08_2' => $row['Amount_FN08_2'],
                                                        'Allocated_FN06_1' => $row['Allocated_FN06_1'],
                                                        'Allocated_FN02_1' => $row['Allocated_FN02_1'],
                                                        'Allocated_FN08_1' => $row['Allocated_FN08_1'],
                                                        'Allocated_FN06_2' => $row['Allocated_FN06_2'],
                                                        'Allocated_FN02_2' => $row['Allocated_FN02_2'],
                                                        'Allocated_FN08_2' => $row['Allocated_FN08_2'],
                                                        'Reason' => $row['Reason'],
                                                        'test' => $row['KKU_Item_Name'],
                                                    ];
                                                    $rows = $summary;
                                                    // ตัวแปรสำหรับเก็บผลรวมทั้งหมด
                                                    $total_summary = [
                                                        'Amount_FN06_1' => 0,
                                                        'Amount_FN02_1' => 0,
                                                        'Amount_FN08_1' => 0,
                                                        'Amount_FN06_2' => 0,
                                                        'Amount_FN02_2' => 0,
                                                        'Amount_FN08_2' => 0,
                                                        'Allocated_FN06_1' => 0,
                                                        'Allocated_FN02_1' => 0,
                                                        'Allocated_FN08_1' => 0,
                                                        'Allocated_FN06_2' => 0,
                                                        'Allocated_FN02_2' => 0,
                                                        'Allocated_FN08_2' => 0,
                                                    ];
                                                    // แสดงผลรวมทั้งหมด
                                                    //print_r($total_summary);
                                                    // Assuming this is inside a loop where $row is updated (e.g., from a database query)
                                                    foreach ($rows as $row) { // Replace $rows with your actual data source
                                                        // รวมผลรวมทั้งหมดโดยไม่สนใจ Faculty
                                                        $total_summary['Amount_FN06_1'] += (float) ($row['Amount_FN06_1'] ?? 0);
                                                        $total_summary['Amount_FN02_1'] += (float) ($row['Amount_FN02_1'] ?? 0);
                                                        $total_summary['Amount_FN08_1'] += (float) ($row['Amount_FN08_1'] ?? 0);

                                                        $total_summary['Amount_FN06_2'] += (float) ($row['Amount_FN06_2'] ?? 0);
                                                        $total_summary['Amount_FN02_2'] += (float) ($row['Amount_FN02_2'] ?? 0);
                                                        $total_summary['Amount_FN08_2'] += (float) ($row['Amount_FN08_2'] ?? 0);

                                                        $total_summary['Allocated_FN06_1'] += (float) ($row['Allocated_FN06_1'] ?? 0);
                                                        $total_summary['Allocated_FN02_1'] += (float) ($row['Allocated_FN02_1'] ?? 0);
                                                        $total_summary['Allocated_FN08_1'] += (float) ($row['Allocated_FN08_1'] ?? 0);

                                                        $total_summary['Allocated_FN06_2'] += (float) ($row['Allocated_FN06_2'] ?? 0);
                                                        $total_summary['Allocated_FN02_2'] += (float) ($row['Allocated_FN02_2'] ?? 0);
                                                        $total_summary['Allocated_FN08_2'] += (float) ($row['Allocated_FN08_2'] ?? 0);
                                                    }
                                                }
                                                if ($selectedFaculty == null) {
                                                    if (isset($summary) && is_array($summary)) {
                                                        $TotalAllocate1 = $total_summary['Allocated_FN06_1'] + $total_summary['Allocated_FN02_1'] + $total_summary['Allocated_FN08_1'];
                                                        $TotalAllocate2 = $total_summary['Allocated_FN06_2'] + $total_summary['Allocated_FN02_2'] + $total_summary['Allocated_FN08_2'];
                                                        $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                        $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;
                                                        echo "<tr>";
                                                        echo "<td style='text-align: left;'>" . "รวมทั้งสิ้น" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Allocated_FN06_2']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Allocated_FN02_2']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Allocated_FN08_2']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Amount_FN06_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Allocated_FN06_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Amount_FN02_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Allocated_FN02_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Amount_FN08_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($total_summary['Allocated_FN08_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                        echo "</tr>";
                                                    } else {
                                                        echo "<tr><td colspan='18' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                                    }
                                                }
                                                foreach ($summary as $Faculty => $data1) {
                                                    $facultyData = str_replace('-', ':', $data1['name']);
                                                    $TotalAllocate1 = $data1['Allocated_FN06_1'] + $data1['Allocated_FN02_1'] + $data1['Allocated_FN08_1'];
                                                    $TotalAllocate2 = $data1['Allocated_FN06_2'] + $data1['Allocated_FN02_2'] + $data1['Allocated_FN08_2'];
                                                    $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                    $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;
                                                    echo "<tr>";
                                                    if ($selectedFaculty == null) {
                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 8) . htmlspecialchars($facultyData) . "</td>";
                                                    } else {
                                                        echo "<td style='text-align: left;'>" . "รวมทั้งสิ้น" . "</td>";
                                                    }

                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Allocated_FN06_2']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Allocated_FN02_2']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Allocated_FN08_2']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Amount_FN06_1']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Allocated_FN06_1']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Amount_FN02_1']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Allocated_FN02_1']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Amount_FN08_1']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($data1['Allocated_FN08_1']) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                    echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                    echo "</tr>";
                                                    foreach ($data1['plan'] as $Plan => $data2) {
                                                        $TotalAllocate1 = $data2['Allocated_FN06_1'] + $data2['Allocated_FN02_1'] + $data2['Allocated_FN08_1'];
                                                        $TotalAllocate2 = $data2['Allocated_FN06_2'] + $data2['Allocated_FN02_2'] + $data2['Allocated_FN08_2'];
                                                        $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                        $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;
                                                        echo "<tr>";
                                                        if ($selectedFaculty == null) {
                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 16) . htmlspecialchars($data2['name']) . "</td>";
                                                        } else {
                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 8) . htmlspecialchars($data2['name']) . "</td>";
                                                        }

                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Allocated_FN06_2']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Allocated_FN02_2']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Allocated_FN08_2']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Amount_FN06_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Allocated_FN06_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Amount_FN02_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Allocated_FN02_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Amount_FN08_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($data2['Allocated_FN08_1']) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                        echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                        echo "</tr>";
                                                        foreach ($data2['subPlan'] as $Sub_Plan => $data3) {
                                                            // ใช้ข้อมูลจาก $summary ไปดึงข้อมูลจาก $results2 ที่ตรงกัน
                                                            $matchingResults2 = array_filter($results2, function ($result2) use ($data3) {
                                                                return $result2['Faculty'] === $data3['Faculty'] && $result2['Plan'] === $data3['Plan'] && $result2['Sub_Plan'] === $data3['Sub_Plan'];
                                                            });

                                                            // นำข้อมูลที่ตรงกันไปเก็บใน $data3
                                                            $data3['kpi_data1'] = $matchingResults2;
                                                            $subPlanName = str_replace('SP_', '', $Sub_Plan ?? '');
                                                            $TotalAllocate1 = $data3['Allocated_FN06_1'] + $data3['Allocated_FN02_1'] + $data3['Allocated_FN08_1'];
                                                            $TotalAllocate2 = $data3['Allocated_FN06_2'] + $data3['Allocated_FN02_2'] + $data3['Allocated_FN08_2'];
                                                            $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                            $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;
                                                            // แสดงข้อมูล Sub Plan
                                                            echo "<tr>";
                                                            if ($selectedFaculty == null) {
                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 24) . htmlspecialchars($subPlanName) . " : " . htmlspecialchars($data3['name'] ?? '') . "</td>";
                                                            } else {
                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 16) . htmlspecialchars($subPlanName) . " : " . htmlspecialchars($data3['name'] ?? '') . "</td>";
                                                            }

                                                            echo "<td style='text-align: center;'>" . "</td>";
                                                            echo "<td style='text-align: center;'>" . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Allocated_FN06_2']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Allocated_FN02_2']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Allocated_FN08_2']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                            echo "<td style='text-align: center;'>" . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Amount_FN06_1']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Allocated_FN06_1']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Amount_FN02_1']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Allocated_FN02_1']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Amount_FN08_1']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($data3['Allocated_FN08_1']) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                            echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                            echo "<td style='text-align: center;'>" . "</td>";
                                                            echo "</tr>";
                                                            // แสดงข้อมูล KPI ของ Sub Plan
                                                            if (!empty($data3['kpi_data1'])) {
                                                                foreach ($data3['kpi_data1'] as $row2) {
                                                                    echo "<tr>";
                                                                    if ($selectedFaculty == null) {
                                                                        echo "<td>" . str_repeat("&nbsp;", 24) . " - " . htmlspecialchars($row2['Sub_plan_KPI_Name']) . "</td>";
                                                                    } else {
                                                                        echo "<td>" . str_repeat("&nbsp;", 16) . " - " . htmlspecialchars($row2['Sub_plan_KPI_Name']) . "</td>";
                                                                    }

                                                                    echo "<td style='text-align: center;'>" . htmlspecialchars($row2['UoM_for_Sub_plan_KPI']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . htmlspecialchars($row2['Sub_plan_KPI_Target_2']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . htmlspecialchars($row2['Sub_plan_KPI_Target_1']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "</tr>";
                                                                }
                                                            } else {
                                                                echo "<td  style='text-align:center;'>ไม่มีข้อมูล KPI</td>";
                                                                echo "<td colspan='14' style='text-align:center;'> -</td>";
                                                            }

                                                            foreach ($data3['project'] as $Project => $data4) {
                                                                // ค้นหาข้อมูลที่ตรงกันจาก $results3
                                                                $matchingResults3 = array_filter($results3, function ($result3) use ($data4) {
                                                                    return $result3['Faculty'] === $data4['Faculty'] && $result3['Project'] === $data4['ProjectID'];
                                                                });

                                                                // นำข้อมูลที่ตรงกันไปเก็บใน $data4
                                                                $data4['kpi_data2'] = $matchingResults3;
                                                                $TotalAllocate1 = $data4['Allocated_FN06_1'] + $data4['Allocated_FN02_1'] + $data4['Allocated_FN08_1'];
                                                                $TotalAllocate2 = $data4['Allocated_FN06_2'] + $data4['Allocated_FN02_2'] + $data4['Allocated_FN08_2'];
                                                                $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                                $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;
                                                                echo "<tr>";
                                                                if ($selectedFaculty == null) {
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 32) . htmlspecialchars($data4['name']) . "</td>";
                                                                } else {
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 24) . htmlspecialchars($data4['name']) . "</td>";
                                                                }

                                                                echo "<td style='text-align: center;'>" . "</td>";
                                                                echo "<td style='text-align: center;'>" . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Allocated_FN06_2']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Allocated_FN02_2']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Allocated_FN08_2']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                                echo "<td style='text-align: center;'>" . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Amount_FN06_1']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Allocated_FN06_1']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Amount_FN02_1']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Allocated_FN02_1']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Amount_FN08_1']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($data4['Allocated_FN08_1']) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                                echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                                echo "<td style='text-align: center;'>" . "</td>";
                                                                echo "</tr>";
                                                                if (!empty($data4['kpi_data2'])) {
                                                                    foreach ($data4['kpi_data2'] as $row3) {
                                                                        echo "<tr>";
                                                                        if ($selectedFaculty == null) {
                                                                            echo "<td>" . str_repeat("&nbsp;", 32) . " - " . htmlspecialchars($row3['Proj_KPI_Name']) . "</td>";
                                                                        } else {
                                                                            echo "<td>" . str_repeat("&nbsp;", 24) . " - " . htmlspecialchars($row3['Proj_KPI_Name']) . "</td>";
                                                                        }
                                                                        echo "<td style='text-align: center;'>" . htmlspecialchars($row3['UoM_for_Proj_KPI']) . "</td>";
                                                                        echo "<td style='text-align: center;'>" . htmlspecialchars($row3['Proj_KPI_Target_2']) . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . htmlspecialchars($row3['Proj_KPI_Target_1']) . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "<td style='text-align: center;'>" . "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                } else {
                                                                    echo "<tr>";
                                                                    echo "<td  style='text-align:center;'>ไม่มีข้อมูล KPI</td>";
                                                                    echo "<td colspan='14' style='text-align:center;'>-</td>";
                                                                    echo "</tr>";
                                                                }
                                                                foreach ($data4['name_a1'] as $Name_a1 => $data5) {
                                                                    $TotalAllocate1 = $data5['Allocated_FN06_1'] + $data5['Allocated_FN02_1'] + $data5['Allocated_FN08_1'];
                                                                    $TotalAllocate2 = $data5['Allocated_FN06_2'] + $data5['Allocated_FN02_2'] + $data5['Allocated_FN08_2'];
                                                                    $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                                    $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;
                                                                    echo "<tr>";
                                                                    if ($selectedFaculty == null) {
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 40) . htmlspecialchars($data5['name']) . "</td>";
                                                                    } else {
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 32) . htmlspecialchars($data5['name']) . "</td>";
                                                                    }
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Allocated_FN06_2']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Allocated_FN02_2']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Allocated_FN08_2']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Amount_FN06_1']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Allocated_FN06_1']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Amount_FN02_1']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Allocated_FN02_1']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Amount_FN08_1']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($data5['Allocated_FN08_1']) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                                    echo "<td style='text-align: center;'>" . "</td>";
                                                                    echo "</tr>";
                                                                    if (isset($data5['name_a2']) && is_array($data5['name_a2'])) {
                                                                        foreach ($data5['name_a2'] as $Name_a2 => $data6) {
                                                                            if ($data6['test'] == null || $data6['test'] == '' || $data5['name'] == $data6['name']) {
                                                                                continue;
                                                                            }
                                                                            $TotalAllocate1 = $data6['Allocated_FN06_1'] + $data6['Allocated_FN02_1'] + $data6['Allocated_FN08_1'];
                                                                            $TotalAllocate2 = $data6['Allocated_FN06_2'] + $data6['Allocated_FN02_2'] + $data6['Allocated_FN08_2'];
                                                                            $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                                            $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;

                                                                            echo "<tr>";
                                                                            if ($selectedFaculty == null) {
                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 48) . htmlspecialchars($data6['name']) . "</td>";
                                                                            } else {
                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 40) . htmlspecialchars($data6['name']) . "</td>";
                                                                            }

                                                                            echo "<td style='text-align: center;'>" . "</td>";
                                                                            echo "<td style='text-align: center;'>" . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Allocated_FN06_2']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Allocated_FN02_2']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Allocated_FN08_2']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Amount_FN06_1']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Allocated_FN06_1']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Amount_FN02_1']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Allocated_FN02_1']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Amount_FN08_1']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($data6['Allocated_FN08_1']) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                                            echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                                            if ($data6['test2'] == null || $data6['test2'] == '' || $data6['test'] == $data6['test2']) {
                                                                                echo "<td style='text-align: center;'>" . (isset($data6['Reason']) && !empty($data6['Reason']) ? htmlspecialchars($data6['Reason']) : "") . "</td>";
                                                                            } else {
                                                                                echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                            }
                                                                            echo "</tr>";
                                                                            if (isset($data6['name_a3']) && is_array($data6['name_a3'])) {
                                                                                foreach ($data6['name_a3'] as $Name_a3 => $data7) {
                                                                                    if ($data7['test'] == null || $data7['test'] == '' || $data6['name'] == $data7['name']) {
                                                                                        continue;
                                                                                    }
                                                                                    $TotalAllocate1 = $data7['Allocated_FN06_1'] + $data7['Allocated_FN02_1'] + $data7['Allocated_FN08_1'];
                                                                                    $TotalAllocate2 = $data7['Allocated_FN06_2'] + $data7['Allocated_FN02_2'] + $data7['Allocated_FN08_2'];
                                                                                    $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                                                    $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;

                                                                                    echo "<tr>";
                                                                                    if ($selectedFaculty == null) {
                                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 56) . htmlspecialchars($data7['name']) . "</td>";
                                                                                    } else {
                                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 48) . htmlspecialchars($data7['name']) . "</td>";
                                                                                    }

                                                                                    echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Allocated_FN06_2']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Allocated_FN02_2']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Allocated_FN08_2']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Amount_FN06_1']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Allocated_FN06_1']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Amount_FN02_1']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Allocated_FN02_1']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Amount_FN08_1']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data7['Allocated_FN08_1']) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                                                    echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                                                    if ($data7['test2'] == null || $data7['test2'] == '' || $data7['test'] == $data7['test2']) {
                                                                                        echo "<td style='text-align: center;'>" . (isset($data7['Reason']) && !empty($data7['Reason']) ? htmlspecialchars($data7['Reason']) : "") . "</td>";
                                                                                    } else {
                                                                                        echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                    }
                                                                                    echo "</tr>";
                                                                                    if (isset($data7['name_a4']) && is_array($data7['name_a4'])) {
                                                                                        foreach ($data7['name_a4'] as $Name_a3 => $data8) {
                                                                                            if ($data8['test'] == null || $data8['test'] == '' || $data7['name'] == $data8['name']) {
                                                                                                continue;
                                                                                            }
                                                                                            $TotalAllocate1 = $data8['Allocated_FN06_1'] + $data8['Allocated_FN02_1'] + $data8['Allocated_FN08_1'];
                                                                                            $TotalAllocate2 = $data8['Allocated_FN06_2'] + $data8['Allocated_FN02_2'] + $data8['Allocated_FN08_2'];
                                                                                            $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                                                            $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;

                                                                                            echo "<tr>";
                                                                                            if ($selectedFaculty == null) {
                                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 64) . htmlspecialchars($data8['name']) . "</td>";
                                                                                            } else {
                                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 56) . htmlspecialchars($data8['name']) . "</td>";
                                                                                            }

                                                                                            echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Allocated_FN06_2']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Allocated_FN02_2']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Allocated_FN08_2']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Amount_FN06_1']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Allocated_FN06_1']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Amount_FN02_1']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Allocated_FN02_1']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Amount_FN08_1']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($data8['Allocated_FN08_1']) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                                                            echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                                                            if ($data8['test2'] == null || $data8['test2'] == '' || $data8['test'] == $data8['test2']) {
                                                                                                echo "<td style='text-align: center;'>" . (isset($data8['Reason']) && !empty($data8['Reason']) ? htmlspecialchars($data8['Reason']) : "") . "</td>";
                                                                                            } else {
                                                                                                echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                            }
                                                                                            echo "</tr>";
                                                                                            if (isset($data8['kku_items']) && is_array($data8['kku_items'])) {
                                                                                                foreach ($data8['kku_items'] as $data9) {
                                                                                                    if ($data9['test'] == null || $data9['test'] == '' || $data8['name'] == $data9['name']) {
                                                                                                        continue;
                                                                                                    }
                                                                                                    $TotalAllocate1 = $data9['Allocated_FN06_1'] + $data9['Allocated_FN02_1'] + $data9['Allocated_FN08_1'];
                                                                                                    $TotalAllocate2 = $data9['Allocated_FN06_2'] + $data9['Allocated_FN02_2'] + $data9['Allocated_FN08_2'];
                                                                                                    $deffAllocate = $TotalAllocate1 - $TotalAllocate2;
                                                                                                    $Percen = ($TotalAllocate1 != 0) ? ($deffAllocate / $TotalAllocate1) * 100 : 0;
                                                                                                    echo "<tr>";
                                                                                                    if ($selectedFaculty == null) {
                                                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 72) . htmlspecialchars($data9['name']) . "</td>";
                                                                                                    } else {
                                                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", times: 64) . htmlspecialchars($data9['name']) . "</td>";
                                                                                                    }

                                                                                                    echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                                    echo "<td style='text-align: center;' >" . " - " . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Allocated_FN06_2']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Allocated_FN02_2']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Allocated_FN08_2']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate2) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . " - " . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Amount_FN06_1']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Allocated_FN06_1']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Amount_FN02_1']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Allocated_FN02_1']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Amount_FN08_1']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($data9['Allocated_FN08_1']) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($TotalAllocate1) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($deffAllocate) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . formatNumber($Percen) . "</td>";
                                                                                                    echo "<td style='text-align: center;'>" . htmlentities($data9['Reason']) . "</td>";
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
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                echo "<tr><td colspan='9' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                            }
                                            ?>

                                        </tbody>
                                        <script>
                                            // การส่งค่าของ selectedFaculty ไปยัง JavaScript
                                            var selectedFaculty = "<?php echo isset($selectedFaculty) ? htmlspecialchars($selectedFaculty, ENT_QUOTES, 'UTF-8') : ''; ?>";
                                            console.log('Selected Faculty: ', selectedFaculty);</script>
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
            link.download = 'รายงานรายละเอียดตัวชี้วัดและงบประมาณรายจ่ายประจำปี จำแนกตามแผนงาน.csv';
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
            doc.text("รายงานรายละเอียดตัวชี้วัดและงบประมาณรายจ่ายประจำปี จำแนกตามแผนงาน", 10, 500);

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
            doc.save('รายงานรายละเอียดตัวชี้วัดและงบประมาณรายจ่ายประจำปี จำแนกตามแผนงาน.pdf');
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
            link.download = 'รายงานรายละเอียดตัวชี้วัดและงบประมาณรายจ่ายประจำปี จำแนกตามแผนงาน.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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