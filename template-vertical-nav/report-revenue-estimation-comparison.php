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
$budget_year4 = isset($_GET['year']) ? $_GET['year'] - 3 : null;
$budget_year5 = isset($_GET['year']) ? $_GET['year'] - 4 : null;

function fetchBudgetData($conn, $faculty = null, $budget_year1 = null, $budget_year2 = null, $budget_year3 = null, $budget_year4 = null, $budget_year5 = null)
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
    $query = "SELECT
    bap.id, bap.Faculty,
    bap.Plan,
    ft.Alias_Default AS Faculty_name,
    MAX(p.plan_name) AS plan_name,
    (SELECT fc.Alias_Default 
     FROM Faculty fc 
     WHERE fc.Faculty = bap.Faculty 
     LIMIT 1) AS Faculty_Name,
    bap.Sub_Plan, sp.sub_plan_name,
    bap.Project, pj.project_name,
    bap.`Account`, ac.sub_type,
    bap.KKU_Item_Name,
    CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)) AS a1,
    CONCAT(LEFT(bap.`Account`, 4), REPEAT('0', 6)) AS a2,
    bap.`Account`,
    bap.Total_Amount_Quantity,
    
    SUM(CASE WHEN bap.Budget_Management_Year = $budget_year1 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_1,
    SUM(CASE WHEN bap.Budget_Management_Year = $budget_year2 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2,
    SUM(CASE WHEN bap.Budget_Management_Year = $budget_year3 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_3,
    SUM(CASE WHEN bap.Budget_Management_Year = $budget_year4 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_4,
    SUM(CASE WHEN bap.Budget_Management_Year = $budget_year5 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_5,
    (SUM(CASE WHEN bap.Budget_Management_Year = $budget_year1 THEN bap.Total_Amount_Quantity ELSE 0 END) +
    SUM(CASE WHEN bap.Budget_Management_Year = $budget_year2 THEN bap.Total_Amount_Quantity ELSE 0 END)
    ) AS Total_Amount_Quantity1_2,

    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET1,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET1,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET1,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET1,
    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_1,
        
        SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET2,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET2,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET2,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET2,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_2,
    
     	(SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12  
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)) AS Q1_BUDGET1_2,
    
    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)) AS Q2_BUDGET1_2,


    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)) AS Q3_BUDGET1_2,
    
        (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)) AS Q4_BUDGET1_2,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year1
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year2
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_1_2,
    
     SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year3
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year3
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year3
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year3
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET3,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year3
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year3
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year3
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year3
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_3,
    
    
    
         SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year4
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET4,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year4
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET4,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year4
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET4,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year4
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET4,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year4
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year4
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year4
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year4
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_4,
    
    
    
         SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year5
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET5,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year5
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET5,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year5
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET5,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year5
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET5,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year5
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year5
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year5
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = $budget_year5
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_5
    
FROM budget_planning_annual_budget_plan bap
INNER JOIN Faculty ft 
    ON bap.Faculty = ft.Faculty 
    AND ft.parent LIKE 'Faculty%' 
LEFT JOIN sub_plan sp 
    ON sp.sub_plan_id = bap.Sub_Plan
LEFT JOIN project pj 
    ON pj.project_id = bap.Project
LEFT JOIN `account` ac 
    ON ac.`account` = bap.`Account`
LEFT JOIN plan p 
    ON p.plan_id = bap.Plan
LEFT JOIN budget_planning_actual bpa
    ON bpa.FACULTY = bap.Faculty
    AND bpa.`ACCOUNT` = bap.`Account`
    AND bpa.SUBPLAN = CAST(SUBSTRING(bap.Sub_Plan, 4) AS UNSIGNED)
    AND bpa.PROJECT = bap.Project
    AND bpa.PLAN = bap.Plan
    AND bpa.SERVICE = CAST(REPLACE(bap.Service, 'SR_', '') AS UNSIGNED)
    AND bpa.FUND = bap.Fund
    AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = bap.Budget_Management_Year
WHERE ac.id < (SELECT MAX(id) FROM account WHERE account = 'Expenses')";

    // เพิ่มเงื่อนไขสำหรับ Faculty ถ้ามี
    if ($faculty) {
        $query .= " AND bap.Faculty = :faculty"; // กรองตาม Faculty ที่เลือก
    }

    // เพิ่มการจัดกลุ่มข้อมูล
    $query .= "  GROUP BY bap.id, bap.Faculty, bap.Sub_Plan, sp.sub_plan_name, 
    bap.Project, pj.project_name, bap.`Account`, ac.sub_type, 
    bap.KKU_Item_Name, ft.Alias_Default
    ORDER BY bap.Faculty ASC, bap.Plan ASC, bap.Sub_Plan ASC, bap.Project ASC,ac.sub_type ASC,bap.`Account` ASC";

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



$results = fetchBudgetData($conn, $faculty, $budget_year1, $budget_year2, $budget_year3, $budget_year4, $budget_year5);

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
                        <h4>รายงานแสดงการเปรียบเทียบการประมาณการรายได้กับรายได้จริง</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานแสดงการเปรียบเทียบการประมาณการรายได้กับรายได้จริง
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                <?php
                                $faculties = fetchFacultyData($conn);  // ดึงข้อมูล Faculty
                                $years = fetchYearsData($conn);  // ดึงข้อมูลปีจากฐานข้อมูล
                                
                                $selectedYear = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : '';
                                $selectedFaculty = isset($_GET['faculty']) ? htmlspecialchars($_GET['faculty']) : '';
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

                                    <!-- ปุ่มค้นหาที่อยู่ด้านล่างฟอร์ม -->
                                    <div class="form-group" style="display: flex; justify-content: center;">
                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                    </div>
                                </form>

                                <script>
                                    function validateForm() {
                                        // ตรวจสอบว่าเลือกส่วนงาน/หน่วยงาน
                                        var faculty = document.getElementById('faculty').value;
                                        var year = document.getElementById('year').value;

                                        // หากไม่ได้เลือกส่วนงานหรือปี จะมีการแจ้งเตือนและไม่ส่งฟอร์ม
                                        if (faculty == '' || year == '') {
                                            alert('กรุณาเลือกส่วนงาน/หน่วยงานและปีงบประมาณ และ ปีงบประมาณ');
                                            return false;  // ป้องกันการส่งฟอร์ม
                                        }
                                        return true;  // ส่งฟอร์มได้
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
                                <div class="row">
                                </div>
                                <br>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                            <th colspan="33" style='text-align: left;'><span
                                                        style="font-size: 16px;"><?php echo "ปีงบที่ต้องการเปรียบเทียบ " . ($selectedYear - 4) . " ถึง " . $selectedYear; ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                            <th colspan="33" style='text-align: left;'><span
                                                        style="font-size: 16px;"><?php echo "ส่วนงาน / หน่วยงาน " . $selectedFaculty; ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="6"><?php echo "ปี " . ($selectedYear - 4) ?></th>
                                                <th colspan="6"><?php echo "ปี " . ($selectedYear - 3) ?></th>
                                                <th colspan="6">
                                                    <?php echo "จำนวน <br/>(รวมจัดสรร " . $selectedYear . " - " . ($selectedYear - 1) . ")"; ?>
                                                </th>

                                                <th colspan="6"><?php echo "ปี " . ($selectedYear - 1) ?></th>
                                                <th colspan="6"><?php echo "ปี " . ($selectedYear) ?></th>

                                            </tr>
                                            <tr>
                                            <th rowspan="2">ประมาณการรายรับ</th>
                                            <th colspan="4">รายรับจริง</th>
                                            <th rowspan="2">รวม<br/>
                                                รายรับจริง</th>
                                            <th rowspan="2">ประมาณการรายรับ</th>
                                            <th colspan="4">รายรับจริง</th>
                                            <th rowspan="2">รวม<br/>
                                                รายรับจริง</th>
                                            <th rowspan="2">ประมาณการรายรับ</th>
                                            <th colspan="4">รายรับจริง</th>
                                            <th rowspan="2">รวม <br/>
                                                รายรับจริง</th>
                                            <th rowspan="2">ประมาณการรายรับ</th>
                                            <th colspan="4">รายรับจริง</th>
                                            <th rowspan="2">รวม<br/>
                                                รายรับจริง</th>
                                            <th rowspan="2">ประมาณการรายรับ</th>
                                            <th colspan="4">รายรับจริง</th>
                                            <th rowspan="2">รวม<br/>
                                                รายรับจริง</th>

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
                                            $previousSubType = "";

                                            $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;
                                            $budget_year2 = isset($_GET['year']) ? $_GET['year'] - 1 : null;
                                            $budget_year3 = isset($_GET['year']) ? $_GET['year'] - 2 : null;
                                            $budget_year4 = isset($_GET['year']) ? $_GET['year'] - 3 : null;
                                            $budget_year5 = isset($_GET['year']) ? $_GET['year'] - 4 : null;
                                            $results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $budget_year2, $budget_year3,$budget_year4,$budget_year5);

                                            if (isset($results) && is_array($results) && count($results) > 0) {

                                            }

                                            ?>
                                    </table>
                                    <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                    <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                    <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLS</button>

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