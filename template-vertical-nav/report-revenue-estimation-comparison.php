<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* -ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
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

function fetchBudgetData($conn, $faculty = null, $budget_year1 = null, $budget_year2 = null, $budget_year3 = null, $budget_year4 = null, $budget_year5 = null, $scenario = null)
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
    if ($budget_year4 === null) {
        $budget_year4 = 2565;  // ค่าเริ่มต้น
    }
    if ($budget_year5 === null) {
        $budget_year5 = 2564;  // ค่าเริ่มต้น
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
),totalActual AS (
   SELECT 
    bpa.FACULTY,
    bpa.ACCOUNT,
    bpa.SUBPLAN,
    bpa.PROJECT,
    bpa.PLAN,
    bpa.FUND,
    bpa.SERVICE,
SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year1 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET1,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year1 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET1,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year1 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET1,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year1 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET1,
    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year1 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year1 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year1 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year1 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_1,
        
        SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year2- 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET2,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year2- 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET2,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year2- 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET2,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year2- 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET2,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year2- 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year2- 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year2- 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year2- 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_2,

     SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year3 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year3 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year3 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year3 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET3,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year3 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year3 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year3 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND bpa.FISCAL_YEAR = CONCAT('FY', SUBSTRING(CAST(:budget_year3 - 543 AS CHAR), -2))
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_3
FROM budget_planning_actual bpa
GROUP BY 
    bpa.FUND,
    bpa.FACULTY,
    bpa.ACCOUNT,
    bpa.SUBPLAN,
    bpa.PROJECT,
    bpa.PLAN,
    bpa.SERVICE
),totalAmount AS (
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
    SUM(CASE WHEN tm.Budget_Management_Year = :budget_year1 THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568,
    SUM(CASE WHEN tm.Budget_Management_Year = :budget_year2 THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567,
    SUM(CASE WHEN tm.Budget_Management_Year = :budget_year3 THEN tm.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2566
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
),t1 AS(
SELECT
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
    tm.Scenario,
    tm.Reason,
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
    
   tm.Total_Amount_2566 AS Total_Amount_3,
   ta.Q1_BUDGET3 AS Q1_BUDGET3,
   ta.Q2_BUDGET3 AS Q2_BUDGET3,
   ta.Q3_BUDGET3 AS Q3_BUDGET3,
   ta.Q4_BUDGET3 AS Q4_BUDGET3,
   ta.TOTAL_BUDGET_YEAR_3 AS TOTAL_BUDGET_YEAR_3,
   tm.Total_Amount_2567 AS Total_Amount_2,
   ta.Q1_BUDGET2 AS Q1_BUDGET2,
   ta.Q2_BUDGET2 AS Q2_BUDGET2,
   ta.Q3_BUDGET2 AS Q3_BUDGET2,
   ta.Q4_BUDGET2 AS Q4_BUDGET2,
	ta.TOTAL_BUDGET_YEAR_2 AS TOTAL_BUDGET_YEAR_2,
	tm.Total_Amount_2568 AS Total_Amount_1,
	ta.Q1_BUDGET1 AS Q1_BUDGET1,
   ta.Q2_BUDGET1 AS Q2_BUDGET1,
   ta.Q3_BUDGET1 AS Q3_BUDGET1,
   ta.Q4_BUDGET1 AS Q4_BUDGET1,
	ta.TOTAL_BUDGET_YEAR_1 AS TOTAL_BUDGET_YEAR_1
FROM totalAmount tm 
INNER JOIN Faculty ft 
    ON ft.Faculty = tm.Faculty 
    AND ft.parent LIKE 'Faculty%' 
LEFT JOIN main m ON tm.Account = m.CurrentAccount
LEFT JOIN sub_plan sp ON sp.sub_plan_id = tm.Sub_Plan
LEFT JOIN project pj ON pj.project_id = tm.Project
LEFT JOIN `account` ac ON ac.account COLLATE utf8mb4_general_ci = tm.Account COLLATE UTF8MB4_GENERAL_CI
LEFT JOIN plan p ON p.plan_id = tm.Plan
LEFT JOIN totalActual ta
ON  tm.Faculty = ta.FACULTY
AND tm.Account = ta.ACCOUNT
AND tm.Plan = ta.PLAN
AND CAST(REPLACE(tm.Service, 'SR_', '') AS UNSIGNED) = ta.SERVICE
AND tm.Project = tm.PROJECT
AND CAST(SUBSTRING(tm.Sub_Plan, 4) AS UNSIGNED) = ta.SUBPLAN
AND CAST(REPLACE(tm.Fund, 'FN', '') AS UNSIGNED) = ta.FUND

 
WHERE ac.id < (SELECT MAX(id) FROM account WHERE parent = 'Expenses')
GROUP BY 
    tm.Faculty, 
    tm.Plan, 
    ft.Alias_Default,
    tm.Sub_Plan, 
    sp.sub_plan_name,
    tm.Project, 
    pj.project_name, 
    tm.KKU_Item_Name,
    tm.Scenario,
    tm.Account,
    tm.Fund,
    tm.Reason,
    m.TotalLevels,
    m.GreatGrandparentAccount,
    m.GrandparentAccount,
    m.ParentAccount,
    m.GreatGrandparent,
    m.Grandparent,
    m.Parent,
    m.Current,
   tm.Total_Amount_2566,
   ta.Q1_BUDGET3,
   ta.Q2_BUDGET3,
   ta.Q3_BUDGET3,
   ta.Q4_BUDGET3,
   ta.TOTAL_BUDGET_YEAR_3,
   tm.Total_Amount_2567,
   ta.Q1_BUDGET2,
   ta.Q2_BUDGET2,
   ta.Q3_BUDGET2,
   ta.Q4_BUDGET2,
	ta.TOTAL_BUDGET_YEAR_2,
	tm.Total_Amount_2568,
	ta.Q1_BUDGET1,
   ta.Q2_BUDGET1,
   ta.Q3_BUDGET1,
   ta.Q4_BUDGET1,
	ta.TOTAL_BUDGET_YEAR_1
 
ORDER BY tm.Faculty ASC , tm.Plan ASC, tm.Sub_Plan ASC, tm.Project ASC, Name_a1 ASC,Name_a2 ASC,Name_a3 ASC,Name_a4 ASC,tm.Account ASC
 
)
SELECT * FROM t1";


    // Array to hold the parameters to bind
    $params = [
        'budget_year1' => $budget_year1,
        'budget_year2' => $budget_year2,
        'budget_year3' => $budget_year3,
        'budget_year4' => $budget_year4,
        'budget_year5' => $budget_year5
    ];

    // Add WHERE clause if Faculty is provided
    $whereClauses = [];

    if ($faculty) {
        $whereClauses[] = "Faculty = :faculty";
        $params['faculty'] = $faculty;  // Bind the faculty parameter
    }

    // Add AND clause if Scenario is provided
    if ($scenario) {
        $whereClauses[] = "Scenario = :scenario";
        $params['scenario'] = $scenario;  // Bind the scenario parameter
    }

    // If we have any WHERE clauses, add them to the query
    if (count($whereClauses) > 0) {
        $query .= " WHERE " . implode(" AND ", $whereClauses);
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare($query);
    // Bind parameters to the query
    $stmt->bindParam(':budget_year1', $budget_year1, PDO::PARAM_INT);
    $stmt->bindParam(':budget_year2', $budget_year2, PDO::PARAM_INT);
    $stmt->bindParam(':budget_year3', $budget_year3, PDO::PARAM_INT);
    // Bind other parameters as necessary...
    // Execute the query with the bound parameters
    $stmt->execute($params);

    // Fetch and return the results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Retrieve parameters from the query string
$faculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
$year = isset($_GET['year']) ? (int) $_GET['year'] : 2568; // Default to 2568 if not provided
$budget_year1 = $year;
$budget_year2 = $year - 1;
$budget_year3 = $year - 2;
$budget_year4 = $year - 3;
$budget_year5 = $year - 4;
$scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;

// Fetch the results
$results = fetchBudgetData($conn, $faculty, $budget_year1, $budget_year2, $budget_year3, $budget_year4, $budget_year5, $scenario);

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
                        <h4>รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">
                                รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง</h4>
                                </div>

                                <?php
                                $faculties = fetchFacultyData($conn);  // ดึงข้อมูล Faculty
                                $years = fetchYearsData($conn);  // ดึงข้อมูลปีจากฐานข้อมูล
                                $scenarios = fetchScenariosData($conn); // ดึงข้อมูล Scenario จากฐานข้อมูล
                                ?>

                                <form method="GET" action="" onsubmit="return validateForm()">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="faculty" class="label-faculty" style="margin-right: 10px;">เลือก
                                            ส่วนงาน/หน่วยงาน</label>
                                        <select name="faculty" id="faculty" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือก ส่วนงาน/หน่วยงาน</option>
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
                                    <!-- ปุ่มค้นหาที่อยู่ด้านล่างฟอร์ม -->
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

                                        var baseUrl = "http://202.28.118.192:8081/template-vertical-nav/report-revenue-estimation-comparison.php";
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


                                <script>
                                    // ส่งค่าจาก PHP ไปยัง JavaScript
                                    const budgetYear1 = <?php echo json_encode($budget_year1); ?>;
                                    const budgetYear2 = <?php echo json_encode($budget_year2); ?>;
                                    const budgetYear3 = <?php echo json_encode($budget_year3); ?>;
                                    const budgetYear4 = <?php echo json_encode($budget_year4); ?>;
                                    const budgetYear5 = <?php echo json_encode($budget_year5); ?>;

                                    // แสดงค่าของ budget_year ในคอนโซล
                                    console.log('Budget Year 1:', budgetYear1);
                                    console.log('Budget Year 2:', budgetYear2);
                                    console.log('Budget Year 3:', budgetYear3);
                                    console.log('Budget Year 4:', budgetYear4);
                                    console.log('Budget Year 5:', budgetYear5);
                                </script>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">
                                        <thead>

                                            <tr>
                                                <th colspan="31" style='text-align: left;'>
                                                    รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง</th>
                                            </tr>
                                            <?php
                                            // ตรวจสอบและกำหนดค่า $selectedYear
                                            $selectedYear = isset($_GET['year']) && $_GET['year'] != '' ? (int) $_GET['year'] : '2568';
                                            $scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
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
                                                <th colspan="31" style='text-align: left;'>
                                                    <span style="font-size: 16px;">
                                                        <?php
                                                        if ($selectedYear) {
                                                            echo "ปีงบที่ต้องการเปรียบเทียบ " . ($selectedYear - 2) . " ถึง " . $selectedYear;
                                                        } else {
                                                            echo "ปีงบที่ต้องการเปรียบเทียบ: ไม่ได้เลือกปีงบประมาณ";
                                                        }
                                                        ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="31" style='text-align: left;'>
                                                    <span style="font-size: 16px;">


                                                        <?php
                                                        $facultyData = str_replace('-', ':', $selectedFacultyName);

                                                        echo "ส่วนงาน / หน่วยงาน: " . $facultyData; ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="31" style='text-align: left;'>
                                                    <span style="font-size: 16px;">


                                                        <?php
                                                        echo "ประเภทงบประมาณ: " . (!empty($scenario) ? $scenario : "แสดงทุกประเภทงบประมาณ");
                                                        ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th rowspan="3">รายการ</th>

                                                <th colspan="6">
                                                    <?php echo $selectedYear ? "ปี " . ($selectedYear - 2) : "ปี: ไม่ได้เลือก"; ?>
                                                </th>
                                                <th colspan="6">
                                                    <?php echo $selectedYear ? "ปี " . ($selectedYear - 1) : "ปี: ไม่ได้เลือก"; ?>
                                                </th>
                                                <th colspan="6">
                                                    <?php echo $selectedYear ? "ปี " . $selectedYear : "ปี: ไม่ได้เลือก"; ?>
                                                </th>
                                            </tr>
                                            <tr>


                                                <th rowspan="2">ประมาณการรายรับ</th>
                                                <th colspan="4">รายรับจริง</th>
                                                <th rowspan="2" class="center-text">
                                                    รวม<br />รายรับจริง
                                                </th>

                                                <th rowspan="2">ประมาณการรายรับ</th>
                                                <th colspan="4">รายรับจริง</th>
                                                <th rowspan="2" class="center-text">
                                                    รวม<br />รายรับจริง
                                                </th>

                                                <th rowspan="2">ประมาณการรายรับ</th>
                                                <th colspan="4">รายรับจริง</th>
                                                <th rowspan="2" class="center-text">
                                                    รวม<br />รายรับจริง
                                                </th>


                                            </tr>
                                            <tr>

                                                <th>ไตรมาสที่1</th>
                                                <th>ไตรมาสที่2</th>
                                                <th>ไตรมาสที่3</th>
                                                <th>ไตรมาสที่4</th>
                                                <th>ไตรมาสที่1</th>
                                                <th>ไตรมาสที่2</th>
                                                <th>ไตรมาสที่3</th>
                                                <th>ไตรมาสที่4</th>
                                                <th>ไตรมาสที่1</th>
                                                <th>ไตรมาสที่2</th>
                                                <th>ไตรมาสที่3</th>
                                                <th>ไตรมาสที่4</th>

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
                                            $previousName_a1 = "";

                                            $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $year = isset($_GET['year']) ? (int) $_GET['year'] : 2568; // Default to 2568 if not provided
                                            $budget_year1 = $year;
                                            $budget_year2 = $year - 1;
                                            $budget_year3 = $year - 2;
                                            $budget_year4 = $year - 3;
                                            $budget_year5 = $year - 4;
                                            $scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
                                            $results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $budget_year2, $budget_year3, $budget_year4, $budget_year5, $scenario);

                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                // สร้าง associative array เพื่อเก็บผลรวมของแต่ละ Plan, Sub_Plan, Project, และ Sub_Type
                                                $summary = [];
                                                foreach ($results as $row) {
                                                    $faculty = $row['Faculty'];
                                                    $plan = $row['Plan'];
                                                    $subPlan = $row['Sub_Plan'];
                                                    $project = $row['project_name'];
                                                    $Name_a1 = $row['Name_a1'];
                                                    $Name_a2 = $row['Name_a2'];
                                                    $Name_a3 = $row['Name_a3'];
                                                    $Name_a4 = $row['Name_a4'];
                                                    // เก็บข้อมูลของ faculty
                                                    if (!isset($summary[$faculty])) {
                                                        $summary[$faculty] = [
                                                            'Faculty' => $row['Faculty_name'],
                                                            'Total_Amount_1' => 0,
                                                            'Q1_BUDGET1' => 0,
                                                            'Q2_BUDGET1' => 0,
                                                            'Q3_BUDGET1' => 0,
                                                            'Q4_BUDGET1' => 0,
                                                            'TOTAL_BUDGET_YEAR_1' => 0,
                                                            'Total_Amount_2' => 0,
                                                            'Q1_BUDGET2' => 0,
                                                            'Q2_BUDGET2' => 0,
                                                            'Q3_BUDGET2' => 0,
                                                            'Q4_BUDGET2' => 0,
                                                            'TOTAL_BUDGET_YEAR_2' => 0,
                                                            'Total_Amount_3' => 0,
                                                            'Q1_BUDGET3' => 0,
                                                            'Q2_BUDGET3' => 0,
                                                            'Q3_BUDGET3' => 0,
                                                            'Q4_BUDGET3' => 0,
                                                            'TOTAL_BUDGET_YEAR_3' => 0,

                                                            'plans' => [], // เก็บข้อมูลของ Plan
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ Plan
                                                    if (!isset($summary[$faculty]['plans'][$plan])) {
                                                        $summary[$faculty]['plans'][$plan] = [
                                                            'plan_name' => $row['plan_name'],
                                                            'Total_Amount_1' => 0,
                                                            'Q1_BUDGET1' => 0,
                                                            'Q2_BUDGET1' => 0,
                                                            'Q3_BUDGET1' => 0,
                                                            'Q4_BUDGET1' => 0,
                                                            'TOTAL_BUDGET_YEAR_1' => 0,
                                                            'Total_Amount_2' => 0,
                                                            'Q1_BUDGET2' => 0,
                                                            'Q2_BUDGET2' => 0,
                                                            'Q3_BUDGET2' => 0,
                                                            'Q4_BUDGET2' => 0,
                                                            'TOTAL_BUDGET_YEAR_2' => 0,
                                                            'Total_Amount_3' => 0,
                                                            'Q1_BUDGET3' => 0,
                                                            'Q2_BUDGET3' => 0,
                                                            'Q3_BUDGET3' => 0,
                                                            'Q4_BUDGET3' => 0,
                                                            'TOTAL_BUDGET_YEAR_3' => 0,

                                                            'sub_plans' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Sub_Plan
                                                    if (!isset($summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan])) {
                                                        $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan] = [
                                                            'sub_plan_name' => $row['sub_plan_name'],
                                                            'Total_Amount_1' => 0,
                                                            'Q1_BUDGET1' => 0,
                                                            'Q2_BUDGET1' => 0,
                                                            'Q3_BUDGET1' => 0,
                                                            'Q4_BUDGET1' => 0,
                                                            'TOTAL_BUDGET_YEAR_1' => 0,
                                                            'Total_Amount_2' => 0,
                                                            'Q1_BUDGET2' => 0,
                                                            'Q2_BUDGET2' => 0,
                                                            'Q3_BUDGET2' => 0,
                                                            'Q4_BUDGET2' => 0,
                                                            'TOTAL_BUDGET_YEAR_2' => 0,
                                                            'Total_Amount_3' => 0,
                                                            'Q1_BUDGET3' => 0,
                                                            'Q2_BUDGET3' => 0,
                                                            'Q3_BUDGET3' => 0,
                                                            'Q4_BUDGET3' => 0,
                                                            'TOTAL_BUDGET_YEAR_3' => 0,

                                                            'projects' => [], // เก็บข้อมูลของ Project
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Project
                                                    if (!isset($summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project])) {
                                                        $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project] = [
                                                            'Total_Amount_1' => 0,
                                                            'Q1_BUDGET1' => 0,
                                                            'Q2_BUDGET1' => 0,
                                                            'Q3_BUDGET1' => 0,
                                                            'Q4_BUDGET1' => 0,
                                                            'TOTAL_BUDGET_YEAR_1' => 0,
                                                            'Total_Amount_2' => 0,
                                                            'Q1_BUDGET2' => 0,
                                                            'Q2_BUDGET2' => 0,
                                                            'Q3_BUDGET2' => 0,
                                                            'Q4_BUDGET2' => 0,
                                                            'TOTAL_BUDGET_YEAR_2' => 0,
                                                            'Total_Amount_3' => 0,
                                                            'Q1_BUDGET3' => 0,
                                                            'Q2_BUDGET3' => 0,
                                                            'Q3_BUDGET3' => 0,
                                                            'Q4_BUDGET3' => 0,
                                                            'TOTAL_BUDGET_YEAR_3' => 0,

                                                            'Name_a1' => [], // เก็บข้อมูลของ Sub_Type
                                                        ];
                                                    }

                                                    $ItemName_a1 = (!empty($row['Name_a1']))
                                                        ? "" . htmlspecialchars($row['a1']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a1']))
                                                        : "" . htmlspecialchars($row['a1']) . "";
                                                    // เก็บข้อมูลของ Name_a1
                                                    if (!isset($summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1])) {
                                                        $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1] = [
                                                            'a1' => $row['a1'],
                                                            'name' => $ItemName_a1,
                                                            'test' => $row['Name_a1'],
                                                            'Total_Amount_1' => 0,
                                                            'Q1_BUDGET1' => 0,
                                                            'Q2_BUDGET1' => 0,
                                                            'Q3_BUDGET1' => 0,
                                                            'Q4_BUDGET1' => 0,
                                                            'TOTAL_BUDGET_YEAR_1' => 0,
                                                            'Total_Amount_2' => 0,
                                                            'Q1_BUDGET2' => 0,
                                                            'Q2_BUDGET2' => 0,
                                                            'Q3_BUDGET2' => 0,
                                                            'Q4_BUDGET2' => 0,
                                                            'TOTAL_BUDGET_YEAR_2' => 0,
                                                            'Total_Amount_3' => 0,
                                                            'Q1_BUDGET3' => 0,
                                                            'Q2_BUDGET3' => 0,
                                                            'Q3_BUDGET3' => 0,
                                                            'Q4_BUDGET3' => 0,
                                                            'TOTAL_BUDGET_YEAR_3' => 0,
                                                            'Name_a2' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }

                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a2
                                                    if (!empty($row['a2']) && !empty($row['Name_a2'])) {
                                                        $ItemName_a2 = htmlspecialchars($row['a2']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']));
                                                    } elseif (empty($row['a2']) && !empty($row['Name_a2'])) {
                                                        $ItemName_a2 = htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']));
                                                    } else {
                                                        $ItemName_a2 = htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    if (!isset($summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2])) {
                                                        $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2] = [
                                                            'name' => $ItemName_a2,
                                                            'test' => $row['Name_a2'],
                                                            'test2' => $row['Name_a3'],
                                                            'Total_Amount_1' => 0,
                                                            'Q1_BUDGET1' => 0,
                                                            'Q2_BUDGET1' => 0,
                                                            'Q3_BUDGET1' => 0,
                                                            'Q4_BUDGET1' => 0,
                                                            'TOTAL_BUDGET_YEAR_1' => 0,
                                                            'Total_Amount_2' => 0,
                                                            'Q1_BUDGET2' => 0,
                                                            'Q2_BUDGET2' => 0,
                                                            'Q3_BUDGET2' => 0,
                                                            'Q4_BUDGET2' => 0,
                                                            'TOTAL_BUDGET_YEAR_2' => 0,
                                                            'Total_Amount_3' => 0,
                                                            'Q1_BUDGET3' => 0,
                                                            'Q2_BUDGET3' => 0,
                                                            'Q3_BUDGET3' => 0,
                                                            'Q4_BUDGET3' => 0,
                                                            'TOTAL_BUDGET_YEAR_3' => 0,
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
                                                    // เก็บข้อมูลของ superSubType
                                                    if (!isset($summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3])) {
                                                        $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3] = [
                                                            'name' => $ItemName_a3,
                                                            'test' => $row['Name_a3'],
                                                            'test2' => $row['Name_a4'],
                                                            'Total_Amount_1' => 0,
                                                            'Q1_BUDGET1' => 0,
                                                            'Q2_BUDGET1' => 0,
                                                            'Q3_BUDGET1' => 0,
                                                            'Q4_BUDGET1' => 0,
                                                            'TOTAL_BUDGET_YEAR_1' => 0,
                                                            'Total_Amount_2' => 0,
                                                            'Q1_BUDGET2' => 0,
                                                            'Q2_BUDGET2' => 0,
                                                            'Q3_BUDGET2' => 0,
                                                            'Q4_BUDGET2' => 0,
                                                            'TOTAL_BUDGET_YEAR_2' => 0,
                                                            'Total_Amount_3' => 0,
                                                            'Q1_BUDGET3' => 0,
                                                            'Q2_BUDGET3' => 0,
                                                            'Q3_BUDGET3' => 0,
                                                            'Q4_BUDGET3' => 0,
                                                            'TOTAL_BUDGET_YEAR_3' => 0,
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
                                                    if (!isset($summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4])) {
                                                        $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4] = [
                                                            'name' => $ItemName_a4,
                                                            'test' => $row['Name_a4'],
                                                            'test2' => $row['KKU_Item_Name'],
                                                            'Total_Amount_1' => 0,
                                                            'Q1_BUDGET1' => 0,
                                                            'Q2_BUDGET1' => 0,
                                                            'Q3_BUDGET1' => 0,
                                                            'Q4_BUDGET1' => 0,
                                                            'TOTAL_BUDGET_YEAR_1' => 0,
                                                            'Total_Amount_2' => 0,
                                                            'Q1_BUDGET2' => 0,
                                                            'Q2_BUDGET2' => 0,
                                                            'Q3_BUDGET2' => 0,
                                                            'Q4_BUDGET2' => 0,
                                                            'TOTAL_BUDGET_YEAR_2' => 0,
                                                            'Total_Amount_3' => 0,
                                                            'Q1_BUDGET3' => 0,
                                                            'Q2_BUDGET3' => 0,
                                                            'Q3_BUDGET3' => 0,
                                                            'Q4_BUDGET3' => 0,
                                                            'TOTAL_BUDGET_YEAR_3' => 0,
                                                            'kku_items' => [], // เก็บข้อมูลของ KKU_Item_Name
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ faculty
                                                    $summary[$faculty]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['Total_Amount_3'] += $row['Total_Amount_3'];
                                                    $summary[$faculty]['Q1_BUDGET3'] += $row['Q1_BUDGET3'];
                                                    $summary[$faculty]['Q2_BUDGET3'] += $row['Q2_BUDGET3'];
                                                    $summary[$faculty]['Q3_BUDGET3'] += $row['Q3_BUDGET3'];
                                                    $summary[$faculty]['Q4_BUDGET3'] += $row['Q4_BUDGET3'];
                                                    $summary[$faculty]['TOTAL_BUDGET_YEAR_3'] += $row['TOTAL_BUDGET_YEAR_3'];



                                                    // เก็บข้อมูลของ Plan
                                                    $summary[$faculty]['plans'][$plan]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['plans'][$plan]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['plans'][$plan]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['plans'][$plan]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['plans'][$plan]['Total_Amount_3'] += $row['Total_Amount_3'];
                                                    $summary[$faculty]['plans'][$plan]['Q1_BUDGET3'] += $row['Q1_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['Q2_BUDGET3'] += $row['Q2_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['Q3_BUDGET3'] += $row['Q3_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['Q4_BUDGET3'] += $row['Q4_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['TOTAL_BUDGET_YEAR_3'] += $row['TOTAL_BUDGET_YEAR_3'];


                                                    // เก็บข้อมูลของ Sub_Plan
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Total_Amount_3'] += $row['Total_Amount_3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q1_BUDGET3'] += $row['Q1_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q2_BUDGET3'] += $row['Q2_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q3_BUDGET3'] += $row['Q3_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q4_BUDGET3'] += $row['Q4_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['TOTAL_BUDGET_YEAR_3'] += $row['TOTAL_BUDGET_YEAR_3'];



                                                    // เก็บข้อมูลของ Project
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_3'] += $row['Total_Amount_3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q1_BUDGET3'] += $row['Q1_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q2_BUDGET3'] += $row['Q2_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q3_BUDGET3'] += $row['Q3_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q4_BUDGET3'] += $row['Q4_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['TOTAL_BUDGET_YEAR_3'] += $row['TOTAL_BUDGET_YEAR_3'];



                                                    // เก็บข้อมูลของ Name_a1
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Total_Amount_3'] += $row['Total_Amount_3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q1_BUDGET3'] += $row['Q1_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q2_BUDGET3'] += $row['Q2_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q3_BUDGET3'] += $row['Q3_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Q4_BUDGET3'] += $row['Q4_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['TOTAL_BUDGET_YEAR_3'] += $row['TOTAL_BUDGET_YEAR_3'];

                                                    // เก็บข้อมูลของ Name_a2
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_3'] += $row['Total_Amount_3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q1_BUDGET3'] += $row['Q1_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q2_BUDGET3'] += $row['Q2_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q3_BUDGET3'] += $row['Q3_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Q4_BUDGET3'] += $row['Q4_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['TOTAL_BUDGET_YEAR_3'] += $row['TOTAL_BUDGET_YEAR_3'];


                                                    // เก็บข้อมูลของ Name_a3
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_3'] += $row['Total_Amount_3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q1_BUDGET3'] += $row['Q1_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q2_BUDGET3'] += $row['Q2_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q3_BUDGET3'] += $row['Q3_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Q4_BUDGET3'] += $row['Q4_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['TOTAL_BUDGET_YEAR_3'] += $row['TOTAL_BUDGET_YEAR_3'];

                                                    // เก็บข้อมูลของ Name_a4
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Total_Amount_3'] += $row['Total_Amount_3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q1_BUDGET3'] += $row['Q1_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q2_BUDGET3'] += $row['Q2_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q3_BUDGET3'] += $row['Q3_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Q4_BUDGET3'] += $row['Q4_BUDGET3'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['TOTAL_BUDGET_YEAR_3'] += $row['TOTAL_BUDGET_YEAR_3'];


                                                    // เก็บข้อมูลของ KKU_Item_Name
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "" . htmlspecialchars($row['Account']) . ": " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                        : "" . htmlspecialchars($row['Account']) . "</strong>";
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['kku_items'][] = [
                                                        'name' => $kkuItemName,
                                                        'test' => $row['KKU_Item_Name'],
                                                        'Total_Amount_1' => $row['Total_Amount_1'],
                                                        'Q1_BUDGET1' => $row['Q1_BUDGET1'],
                                                        'Q2_BUDGET1' => $row['Q2_BUDGET1'],
                                                        'Q3_BUDGET1' => $row['Q3_BUDGET1'],
                                                        'Q4_BUDGET1' => $row['Q4_BUDGET1'],
                                                        'TOTAL_BUDGET_YEAR_1' => $row['TOTAL_BUDGET_YEAR_1'],
                                                        'Total_Amount_2' => $row['Total_Amount_2'],
                                                        'Q1_BUDGET2' => $row['Q1_BUDGET2'],
                                                        'Q2_BUDGET2' => $row['Q2_BUDGET2'],
                                                        'Q3_BUDGET2' => $row['Q3_BUDGET2'],
                                                        'Q4_BUDGET2' => $row['Q4_BUDGET2'],
                                                        'TOTAL_BUDGET_YEAR_2' => $row['TOTAL_BUDGET_YEAR_2'],
                                                        'Total_Amount_3' => $row['Total_Amount_3'],
                                                        'Q1_BUDGET3' => $row['Q1_BUDGET3'],
                                                        'Q2_BUDGET3' => $row['Q2_BUDGET3'],
                                                        'Q3_BUDGET3' => $row['Q3_BUDGET3'],
                                                        'Q4_BUDGET3' => $row['Q4_BUDGET3'],
                                                        'TOTAL_BUDGET_YEAR_3' => $row['TOTAL_BUDGET_YEAR_3'],

                                                    ];
                                                    $rows = $summary;
                                                    // ตัวแปรสำหรับเก็บผลรวมทั้งหมด
                                                    $total_summary = [
                                                        'Total_Amount_1' => 0,
                                                        'Q1_BUDGET1' => 0,
                                                        'Q2_BUDGET1' => 0,
                                                        'Q3_BUDGET1' => 0,
                                                        'Q4_BUDGET1' => 0,
                                                        'TOTAL_BUDGET_YEAR_1' => 0,
                                                        'Total_Amount_2' => 0,
                                                        'Q1_BUDGET2' => 0,
                                                        'Q2_BUDGET2' => 0,
                                                        'Q3_BUDGET2' => 0,
                                                        'Q4_BUDGET2' => 0,
                                                        'TOTAL_BUDGET_YEAR_2' => 0,
                                                        'Total_Amount_3' => 0,
                                                        'Q1_BUDGET3' => 0,
                                                        'Q2_BUDGET3' => 0,
                                                        'Q3_BUDGET3' => 0,
                                                        'Q4_BUDGET3' => 0,
                                                        'TOTAL_BUDGET_YEAR_3' => 0,
                                                    ];
                                                    // แสดงผลรวมทั้งหมด
                                                    //print_r($total_summary);
                                                    // Assuming this is inside a loop where $row is updated (e.g., from a database query)
                                                    foreach ($rows as $row) { // Replace $rows with your actual data source
                                                        // รวมผลรวมทั้งหมดโดยไม่สนใจ Faculty
                                                        $total_summary['Total_Amount_1'] += (float) ($row['Total_Amount_1'] ?? 0);
                                                        $total_summary['Q1_BUDGET1'] += (float) ($row['Q1_BUDGET1'] ?? 0);
                                                        $total_summary['Q2_BUDGET1'] += (float) ($row['Q2_BUDGET1'] ?? 0);
                                                        $total_summary['Q3_BUDGET1'] += (float) ($row['Q3_BUDGET1'] ?? 0);
                                                        $total_summary['Q4_BUDGET1'] += (float) ($row['Q4_BUDGET1'] ?? 0);
                                                        $total_summary['TOTAL_BUDGET_YEAR_1'] += (float) ($row['TOTAL_BUDGET_YEAR_1'] ?? 0);
                                                        $total_summary['Total_Amount_2'] += (float) ($row['Total_Amount_2'] ?? 0);
                                                        $total_summary['Q1_BUDGET2'] += (float) ($row['Q1_BUDGET2'] ?? 0);
                                                        $total_summary['Q2_BUDGET2'] += (float) ($row['Q2_BUDGET2'] ?? 0);
                                                        $total_summary['Q3_BUDGET2'] += (float) ($row['Q3_BUDGET2'] ?? 0);
                                                        $total_summary['Q4_BUDGET2'] += (float) ($row['Q4_BUDGET2'] ?? 0);
                                                        $total_summary['TOTAL_BUDGET_YEAR_2'] += (float) ($row['TOTAL_BUDGET_YEAR_2'] ?? 0);
                                                        $total_summary['Total_Amount_3'] += (float) ($row['Total_Amount_3'] ?? 0);
                                                        $total_summary['Q1_BUDGET3'] += (float) ($row['Q1_BUDGET3'] ?? 0);
                                                        $total_summary['Q2_BUDGET3'] += (float) ($row['Q2_BUDGET3'] ?? 0);
                                                        $total_summary['Q3_BUDGET3'] += (float) ($row['Q3_BUDGET3'] ?? 0);
                                                        $total_summary['Q4_BUDGET3'] += (float) ($row['Q4_BUDGET3'] ?? 0);
                                                        $total_summary['TOTAL_BUDGET_YEAR_3'] += (float) ($row['TOTAL_BUDGET_YEAR_3'] ?? 0);
                                                    }
                                                }

                                                if ($selectedFaculty == null) {
                                                    // ตรวจสอบว่ามีข้อมูลใน $summary หรือไม่
                                                    if (isset($summary) && is_array($summary)) {
                                                        echo "<tr>";
                                                        echo "<td style='text-align: left;'>" . 'รวมทั้งสิ้น' . "</td>";

                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q1_BUDGET3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q2_BUDGET3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q3_BUDGET3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q4_BUDGET3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q1_BUDGET2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q2_BUDGET2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q3_BUDGET2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q4_BUDGET2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q1_BUDGET1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q2_BUDGET1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q3_BUDGET1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q4_BUDGET1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['TOTAL_BUDGET_YEAR_1']) . "</td>";
                                                        echo "</tr>";
                                                    }
                                                }

                                                // แสดงผลลัพธ์
                                                foreach ($summary as $faculty => $data) {
                                                    // แสดงผลรวมของ Faculty
                                            
                                                    echo "<tr>";
                                                    if ($selectedFaculty == null) {
                                                        $facultyData = str_replace('-', ':', $data['Faculty']);
                                                        echo "<td style='text-align: left;'>" . htmlspecialchars($facultyData) . "</td>";
                                                    }
                                                    if ($selectedFaculty != null) {
                                                        echo "<td style='text-align: left;'>" . 'รวมทั้งสิ้น' . "</td>";
                                                    }

                                                    echo "<td>" . formatNumber($data['Total_Amount_3']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q1_BUDGET3']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q2_BUDGET3']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q3_BUDGET3']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q4_BUDGET3']) . "</td>";
                                                    echo "<td>" . formatNumber($data['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_Amount_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q1_BUDGET2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q2_BUDGET2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q3_BUDGET2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q4_BUDGET2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_Amount_1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q1_BUDGET1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q2_BUDGET1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q3_BUDGET1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q4_BUDGET1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['TOTAL_BUDGET_YEAR_1']) . "</td>";
                                                    echo "</tr>";

                                                    // การวนลูปเพื่อแสดงแผนใน data['plans']
                                                    foreach ($data['plans'] as $plan => $planData) {
                                                        // แสดงผลรวมของ Plan
                                                        echo "<tr>";

                                                        if ($selectedFaculty == null) {
                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 8) . htmlspecialchars($planData['plan_name']) . "</td>";
                                                        }
                                                        if ($selectedFaculty != null) {
                                                            echo "<td style='text-align: left;'>" . htmlspecialchars($planData['plan_name']) . "</td>";
                                                        }

                                                        echo "<td>" . formatNumber($planData['Total_Amount_3']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q1_BUDGET3']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q2_BUDGET3']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q3_BUDGET3']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q4_BUDGET3']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Total_Amount_2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q1_BUDGET2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q2_BUDGET2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q3_BUDGET2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q4_BUDGET2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Total_Amount_1']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q1_BUDGET1']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q2_BUDGET1']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q3_BUDGET1']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q4_BUDGET1']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['TOTAL_BUDGET_YEAR_1']) . "</td>";
                                                        echo "</tr>";

                                                        // แสดงผลรวมของแต่ละ Sub_Plan
                                                        foreach ($planData['sub_plans'] as $subPlan => $subData) {
                                                            echo "<tr>";

                                                            // ลบ 'SP_' ที่อยู่หน้าสุดของข้อความ
                                                            $cleanedSubPlan = preg_replace('/^SP_/', '', $subPlan);

                                                            if ($selectedFaculty == null) {
                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 16) . htmlspecialchars($cleanedSubPlan) . ": " . htmlspecialchars($subData['sub_plan_name']) . "</td>";
                                                            }
                                                            if ($selectedFaculty != null) {
                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 8) . htmlspecialchars($cleanedSubPlan) . ": " . htmlspecialchars($subData['sub_plan_name']) . "</td>";
                                                            }

                                                            echo "<td>" . formatNumber($subData['Total_Amount_3']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q1_BUDGET3']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q2_BUDGET3']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q3_BUDGET3']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q4_BUDGET3']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Total_Amount_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q1_BUDGET2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q2_BUDGET2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q3_BUDGET2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q4_BUDGET2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Total_Amount_1']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q1_BUDGET1']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q2_BUDGET1']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q3_BUDGET1']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q4_BUDGET1']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['TOTAL_BUDGET_YEAR_1']) . "</td>";
                                                            echo "</tr>";

                                                            // แสดงผลรวมของแต่ละ Project
                                                            foreach ($subData['projects'] as $project => $projectData) {
                                                                echo "<tr>";

                                                                if ($selectedFaculty == null) {
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 24) . htmlspecialchars($project) . "</strong></td>";
                                                                }
                                                                if ($selectedFaculty != null) {
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 16) . htmlspecialchars($project) . "</strong></td>";
                                                                }

                                                                echo "<td>" . formatNumber($projectData['Total_Amount_3']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q1_BUDGET3']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q2_BUDGET3']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q3_BUDGET3']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q4_BUDGET3']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Total_Amount_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q1_BUDGET2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q2_BUDGET2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q3_BUDGET2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q4_BUDGET2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Total_Amount_1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q1_BUDGET1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q2_BUDGET1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q3_BUDGET1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q4_BUDGET1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['TOTAL_BUDGET_YEAR_1']) . "</td>";

                                                                echo "</tr>";

                                                                // แสดงผลรวมของแต่ละ Sub_Type
                                                                foreach ($projectData['Name_a1'] as $Name_a1 => $Name_a1Data) {
                                                                    echo "<tr>";
                                                                    // ใช้ Regex ลบตัวเลขและจุดข้างหน้า
                                                                    $cleanedName_a1 = preg_replace('/^[\d.]+\s*/', '', $Name_a1);


                                                                    if ($selectedFaculty == null) {
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . htmlspecialchars($Name_a1Data['name']) . "</td>";
                                                                    }
                                                                    if ($selectedFaculty != null) {
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 24) . htmlspecialchars($Name_a1Data['name']) . "</td>";
                                                                    }

                                                                    echo "<td>" . formatNumber($Name_a1Data['Total_Amount_3']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q1_BUDGET3']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q2_BUDGET3']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q3_BUDGET3']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q4_BUDGET3']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Total_Amount_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q1_BUDGET2']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q2_BUDGET2']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q3_BUDGET2']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q4_BUDGET2']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Total_Amount_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q1_BUDGET1']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q2_BUDGET1']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q3_BUDGET1']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['Q4_BUDGET1']) . "</td>";
                                                                    echo "<td>" . formatNumber($Name_a1Data['TOTAL_BUDGET_YEAR_1']) . "</td>";



                                                                    echo "</tr>";

                                                                    // แสดงผลรวมของแต่ละ Name_a2
                                                                    foreach ($Name_a1Data['Name_a2'] as $Name_a2 => $Name_a2Data) {
                                                                        if ($Name_a2Data['test'] == null || $Name_a2Data['test'] == '') {
                                                                            continue;
                                                                        }

                                                                        echo "<tr>";
                                                                        $cleanedName_a2 = preg_replace('/^[\d.]+\s*/', '', $Name_a2);

                                                                        if ($selectedFaculty == null) {
                                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 40) . htmlspecialchars($Name_a2Data['name']) . "</td>";
                                                                        } else {
                                                                            echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . htmlspecialchars($Name_a2Data['name']) . "</td>";
                                                                        }

                                                                        echo "<td>" . formatNumber($Name_a2Data['Total_Amount_3']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q1_BUDGET3']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q2_BUDGET3']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q3_BUDGET3']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q4_BUDGET3']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Total_Amount_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q1_BUDGET2']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q2_BUDGET2']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q3_BUDGET2']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q4_BUDGET2']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Total_Amount_1']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q1_BUDGET1']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q2_BUDGET1']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q3_BUDGET1']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['Q4_BUDGET1']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a2Data['TOTAL_BUDGET_YEAR_1']) . "</td>";

                                                                        echo "</tr>";

                                                                        // วนลูป Name_a3
                                                                        foreach ($Name_a2Data['Name_a3'] as $Name_a3 => $Name_a3Data) {
                                                                            if ($Name_a3Data['test'] == null || $Name_a3Data['test'] == '' || $Name_a2Data['name'] == $Name_a3Data['name']) {
                                                                                continue;
                                                                            }

                                                                            echo "<tr>";

                                                                            if ($selectedFaculty == null) {
                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 48) . htmlspecialchars($Name_a3Data['name']) . "</td>";
                                                                            } else {
                                                                                echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 40) . htmlspecialchars($Name_a3Data['name']) . "</td>";
                                                                            }
                                                                            echo "<td>" . formatNumber($Name_a3Data['Total_Amount_3']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q1_BUDGET3']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q2_BUDGET3']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q3_BUDGET3']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q4_BUDGET3']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Total_Amount_2']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q1_BUDGET2']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q2_BUDGET2']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q3_BUDGET2']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q4_BUDGET2']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Total_Amount_1']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q1_BUDGET1']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q2_BUDGET1']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q3_BUDGET1']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['Q4_BUDGET1']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a3Data['TOTAL_BUDGET_YEAR_1']) . "</td>";
                                                                            echo "</tr>";
                                                                            foreach ($Name_a3Data['Name_a4'] as $Name_a4 => $Name_a4Data) {
                                                                                if ($Name_a4Data['test'] == null || $Name_a4Data['test'] == '' || $Name_a3Data['name'] == $Name_a4Data['name']) {
                                                                                    continue;
                                                                                }

                                                                                echo "<tr>";


                                                                                if ($selectedFaculty == null) {
                                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 56) . htmlspecialchars($Name_a4Data['name']) . "</td>";
                                                                                } else {
                                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 48) . htmlspecialchars($Name_a4Data['name']) . "</td>";
                                                                                }
                                                                                echo "<td>" . formatNumber($Name_a4Data['Total_Amount_3']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q1_BUDGET3']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q2_BUDGET3']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q3_BUDGET3']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q4_BUDGET3']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Total_Amount_2']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q1_BUDGET2']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q2_BUDGET2']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q3_BUDGET2']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q4_BUDGET2']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Total_Amount_1']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q1_BUDGET1']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q2_BUDGET1']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q3_BUDGET1']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['Q4_BUDGET1']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a4Data['TOTAL_BUDGET_YEAR_1']) . "</td>";
                                                                                echo "</tr>";
                                                                                // วนลูป kku_items
                                                                                foreach ($Name_a4Data['kku_items'] as $kkuItem) {
                                                                                    if ($kkuItem['test'] == null || $kkuItem['test'] == '' || $Name_a4Data['name'] == $kkuItem['name']) {
                                                                                        continue;
                                                                                    }

                                                                                    echo "<tr>";
                                                                                    if ($selectedFaculty == null) {
                                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 64) . htmlspecialchars($kkuItem['name']) . "</td>";
                                                                                    } else {
                                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 56) . htmlspecialchars($kkuItem['name']) . "</td>";
                                                                                    }
                                                                                    echo "<td>" . formatNumber($kkuItem['Total_Amount_3']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q1_BUDGET3']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q2_BUDGET3']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q3_BUDGET3']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q4_BUDGET3']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['TOTAL_BUDGET_YEAR_3']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Total_Amount_2']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q1_BUDGET2']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q2_BUDGET2']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q3_BUDGET2']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q4_BUDGET2']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Total_Amount_1']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q1_BUDGET1']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q2_BUDGET1']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q3_BUDGET1']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['Q4_BUDGET1']) . "</td>";
                                                                                    echo "<td>" . formatNumber($kkuItem['TOTAL_BUDGET_YEAR_1']) . "</td>";
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
                                        var budget_year4 = "<?php echo isset($budget_year4) ? $budget_year4 : ''; ?>";
                                        var budget_year5 = "<?php echo isset($budget_year5) ? $budget_year5 : ''; ?>";

                                        console.log('Selected Year 1: ', budget_year1);
                                        console.log('Selected Year 2: ', budget_year2);
                                        console.log('Selected Year 3: ', budget_year3);
                                        console.log('Selected Year 4: ', budget_year4);
                                        console.log('Selected Year 5: ', budget_year5);

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

            // ฟังก์ชันช่วยเติมค่าซ้ำ
            const repeatValue = (value, count) => Array(count).fill(value).join(',');


            // เพิ่มชื่อรายงาน
            csvRows.push(["รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""]);

            // ดึงค่าปีงบประมาณจาก PHP
            const selectedYear = <?php echo json_encode($selectedYear); ?>;
            const yearRange = selectedYear ? `ปีงบที่ต้องการเปรียบเทียบ ${selectedYear - 2} ถึง ${selectedYear}` : "ปีงบที่ต้องการเปรียบเทียบ: ไม่ได้เลือกปีงบประมาณ";
            csvRows.push([yearRange, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""]);

            // ดึงค่าคณะ/หน่วยงานจาก PHP
            const selectedFacultyName = <?php echo json_encode($selectedFacultyName); ?>;
            const facultyData = selectedFacultyName.replace(/-/g, ':');
            csvRows.push([`ส่วนงาน / หน่วยงาน: ${facultyData}`, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""]);

            // เพิ่มส่วนหัวของตาราง
            csvRows.push([
                "รายการ",
                `ปี ${selectedYear - 2}`,
                "",
                "",
                "",
                "",
                "",
                `ปี ${selectedYear - 1}`,
                "",
                "",
                "",
                "",
                "",
                `ปี ${selectedYear}`,
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                ""
            ]);

            csvRows.push([
                "",
                "ประมาณการรายรับ",
                "รายรับจริง",
                "",
                "",
                "",
                "รวมรายรับจริง",
                "ประมาณการรายรับ",
                "รายรับจริง",
                "",
                "",
                "",
                "รวมรายรับจริง",
                "ประมาณการรายรับ",
                "รายรับจริง",
                "",
                "",
                "",
                "รวมรายรับจริง",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                ""
            ]);

            csvRows.push([
                "",
                "",
                "ไตรมาสที่ 1",
                "ไตรมาสที่ 2",
                "ไตรมาสที่ 3",
                "ไตรมาสที่ 4",
                "",
                "",
                "ไตรมาสที่ 1",
                "ไตรมาสที่ 2",
                "ไตรมาสที่ 3",
                "ไตรมาสที่ 4",
                "",
                "",
                "ไตรมาสที่ 1",
                "ไตรมาสที่ 2",
                "ไตรมาสที่ 3",
                "ไตรมาสที่ 4",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                ""
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
            link.download = 'รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง.csv';
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
            doc.text("รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง", 10, 500);

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
            doc.save('รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง.pdf');
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
            link.download = 'รายงานแสดงการเปรียบเทียบการประมาณการรายรับกับรายรับจริง.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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