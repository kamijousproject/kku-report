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
    
    CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)) AS a1,
    CONCAT(LEFT(bap.`Account`, 4), REPEAT('0', 6)) AS a2,
    bap.`Account`,bap.KKU_Item_Name,
    bap.Total_Amount_Quantity,
    
    SUM(CASE WHEN bap.Budget_Management_Year = $budget_year1 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_1,
    SUM(CASE WHEN bap.Budget_Management_Year = $budget_year2 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2,
    SUM(CASE WHEN bap.Budget_Management_Year = 2566 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_3,
    SUM(CASE WHEN bap.Budget_Management_Year = 2565 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_4,
    SUM(CASE WHEN bap.Budget_Management_Year = 2564 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_Amount_5,
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
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET3,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET3,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2566
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_3,
    
    
    
         SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2565
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET4,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2565
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET4,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2565
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET4,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2565
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET4,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2565
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2565
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2565
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2565
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END)
    ) AS TOTAL_BUDGET_YEAR_4,
    
    
    
         SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2564
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q1_BUDGET5,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2564
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q2_BUDGET5,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2564
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q3_BUDGET5,
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2564
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) AS Q4_BUDGET5,

    (SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 10 AND 12 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2564
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 1 AND 3 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2564
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 4 AND 6 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2564
        THEN bpa.TOTAL_BUDGET 
        ELSE 0 
    END) +
    SUM(CASE 
        WHEN MONTH(bpa.created_at) BETWEEN 7 AND 9 
             AND (CAST(SUBSTRING(bpa.FISCAL_YEAR, 3, 2) AS UNSIGNED) + 2543) = 2564
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
    $query .= " GROUP BY bap.id, bap.Faculty, bap.Sub_Plan, sp.sub_plan_name, 
    bap.Project, pj.project_name, bap.`Account`, ac.sub_type, 
    bap.KKU_Item_Name, ft.Alias_Default
    ORDER BY bap.Faculty ASC, bap.Plan ASC, bap.Sub_Plan ASC, bap.Project ASC, 
                ac.sub_type ASC, 
                
                bap.`Account` ASC";

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
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">
                                        <thead>
                                            <tr>

                                            <tr>
                                                <?php
                                                // ตรวจสอบและกำหนดค่า $selectedYear
                                                $selectedYear = isset($_GET['year']) && $_GET['year'] != '' ? (int) $_GET['year'] : '2568';

                                                // ตรวจสอบและกำหนดค่า $selectedFacultyName
                                                $selectedFacultyCode = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                                $selectedFacultyName = 'ไม่ได้เลือก';

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
                                                            echo "ปีงบที่ต้องการเปรียบเทียบ " . ($selectedYear - 4) . " ถึง " . $selectedYear;
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
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="6">
                                                    <?php echo $selectedYear ? "ปี " . ($selectedYear - 4) : "ปี: ไม่ได้เลือก"; ?>
                                                </th>
                                                <th colspan="6">
                                                    <?php echo $selectedYear ? "ปี " . ($selectedYear - 3) : "ปี: ไม่ได้เลือก"; ?>
                                                </th>
                                                <th colspan="6">
                                                    <?php
                                                    if ($selectedYear) {
                                                        echo "จำนวน <br/>(รวมจัดสรร " . $selectedYear . " - " . ($selectedYear - 1) . ")";
                                                    } else {
                                                        echo "จำนวน: ไม่ได้เลือกปีงบประมาณ";
                                                    }
                                                    ?>
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
                                                <th rowspan="2">รวม<br />
                                                    รายรับจริง</th>
                                                <th rowspan="2">ประมาณการรายรับ</th>
                                                <th colspan="4">รายรับจริง</th>
                                                <th rowspan="2">รวม<br />
                                                    รายรับจริง</th>
                                                <th rowspan="2">ประมาณการรายรับ</th>
                                                <th colspan="4">รายรับจริง</th>
                                                <th rowspan="2">รวม <br />
                                                    รายรับจริง</th>
                                                <th rowspan="2">ประมาณการรายรับ</th>
                                                <th colspan="4">รายรับจริง</th>
                                                <th rowspan="2">รวม<br />
                                                    รายรับจริง</th>
                                                <th rowspan="2">ประมาณการรายรับ</th>
                                                <th colspan="4">รายรับจริง</th>
                                                <th rowspan="2">รวม<br />
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

                                            $results = fetchBudgetData($conn, $selectedFaculty, $budget_year1, $budget_year2, $budget_year3, $budget_year4, $budget_year5);

                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {
                                                // สร้าง associative array เพื่อเก็บผลรวมของแต่ละ Plan, Sub_Plan, Project, และ Sub_Type
                                                $summary = [];
                                                foreach ($results as $row) {
                                                    $faculty = $row['Faculty'];
                                                    $plan = $row['Plan'];
                                                    $subPlan = $row['Sub_Plan'];
                                                    $project = $row['project_name'];
                                                    $subType = $row['sub_type'];

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
                                                            'Total_Amount_Quantity1_2' => 0,
                                                            'Q1_BUDGET1_2' => 0,
                                                            'Q2_BUDGET1_2' => 0,
                                                            'Q3_BUDGET1_2' => 0,
                                                            'Q4_BUDGET1_2' => 0,
                                                            'TOTAL_BUDGET_1_2' => 0,
                                                            'Total_Amount_4' => 0,
                                                            'Q1_BUDGET4' => 0,
                                                            'Q2_BUDGET4' => 0,
                                                            'Q3_BUDGET4' => 0,
                                                            'Q4_BUDGET4' => 0,
                                                            'TOTAL_BUDGET_YEAR_4' => 0,
                                                            'Total_Amount_5' => 0,
                                                            'Q1_BUDGET5' => 0,
                                                            'Q2_BUDGET5' => 0,
                                                            'Q3_BUDGET5' => 0,
                                                            'Q4_BUDGET5' => 0,
                                                            'TOTAL_BUDGET_YEAR_5' => 0,
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
                                                            'Total_Amount_Quantity1_2' => 0,
                                                            'Q1_BUDGET1_2' => 0,
                                                            'Q2_BUDGET1_2' => 0,
                                                            'Q3_BUDGET1_2' => 0,
                                                            'Q4_BUDGET1_2' => 0,
                                                            'TOTAL_BUDGET_1_2' => 0,
                                                            'Total_Amount_4' => 0,
                                                            'Q1_BUDGET4' => 0,
                                                            'Q2_BUDGET4' => 0,
                                                            'Q3_BUDGET4' => 0,
                                                            'Q4_BUDGET4' => 0,
                                                            'TOTAL_BUDGET_YEAR_4' => 0,
                                                            'Total_Amount_5' => 0,
                                                            'Q1_BUDGET5' => 0,
                                                            'Q2_BUDGET5' => 0,
                                                            'Q3_BUDGET5' => 0,
                                                            'Q4_BUDGET5' => 0,
                                                            'TOTAL_BUDGET_YEAR_5' => 0,
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
                                                            'Total_Amount_Quantity1_2' => 0,
                                                            'Q1_BUDGET1_2' => 0,
                                                            'Q2_BUDGET1_2' => 0,
                                                            'Q3_BUDGET1_2' => 0,
                                                            'Q4_BUDGET1_2' => 0,
                                                            'TOTAL_BUDGET_1_2' => 0,
                                                            'Total_Amount_4' => 0,
                                                            'Q1_BUDGET4' => 0,
                                                            'Q2_BUDGET4' => 0,
                                                            'Q3_BUDGET4' => 0,
                                                            'Q4_BUDGET4' => 0,
                                                            'TOTAL_BUDGET_YEAR_4' => 0,
                                                            'Total_Amount_5' => 0,
                                                            'Q1_BUDGET5' => 0,
                                                            'Q2_BUDGET5' => 0,
                                                            'Q3_BUDGET5' => 0,
                                                            'Q4_BUDGET5' => 0,
                                                            'TOTAL_BUDGET_YEAR_5' => 0,
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
                                                            'Total_Amount_Quantity1_2' => 0,
                                                            'Q1_BUDGET1_2' => 0,
                                                            'Q2_BUDGET1_2' => 0,
                                                            'Q3_BUDGET1_2' => 0,
                                                            'Q4_BUDGET1_2' => 0,
                                                            'TOTAL_BUDGET_1_2' => 0,
                                                            'Total_Amount_4' => 0,
                                                            'Q1_BUDGET4' => 0,
                                                            'Q2_BUDGET4' => 0,
                                                            'Q3_BUDGET4' => 0,
                                                            'Q4_BUDGET4' => 0,
                                                            'TOTAL_BUDGET_YEAR_4' => 0,
                                                            'Total_Amount_5' => 0,
                                                            'Q1_BUDGET5' => 0,
                                                            'Q2_BUDGET5' => 0,
                                                            'Q3_BUDGET5' => 0,
                                                            'Q4_BUDGET5' => 0,
                                                            'TOTAL_BUDGET_YEAR_5' => 0,
                                                            'sub_types' => [], // เก็บข้อมูลของ Sub_Type
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ Sub_Type
                                                    if (!isset($summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType])) {
                                                        $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType] = [
                                                            'a2' => $row['a2'],
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
                                                            'Total_Amount_Quantity1_2' => 0,
                                                            'Q1_BUDGET1_2' => 0,
                                                            'Q2_BUDGET1_2' => 0,
                                                            'Q3_BUDGET1_2' => 0,
                                                            'Q4_BUDGET1_2' => 0,
                                                            'TOTAL_BUDGET_1_2' => 0,
                                                            'Total_Amount_4' => 0,
                                                            'Q1_BUDGET4' => 0,
                                                            'Q2_BUDGET4' => 0,
                                                            'Q3_BUDGET4' => 0,
                                                            'Q4_BUDGET4' => 0,
                                                            'TOTAL_BUDGET_YEAR_4' => 0,
                                                            'Total_Amount_5' => 0,
                                                            'Q1_BUDGET5' => 0,
                                                            'Q2_BUDGET5' => 0,
                                                            'Q3_BUDGET5' => 0,
                                                            'Q4_BUDGET5' => 0,
                                                            'TOTAL_BUDGET_YEAR_5' => 0,
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
                                                    $summary[$faculty]['Total_Amount_Quantity1_2'] += $row['Total_Amount_Quantity1_2'];
                                                    $summary[$faculty]['Q1_BUDGET1_2'] += $row['Q1_BUDGET1_2'];
                                                    $summary[$faculty]['Q2_BUDGET1_2'] += $row['Q2_BUDGET1_2'];
                                                    $summary[$faculty]['Q3_BUDGET1_2'] += $row['Q3_BUDGET1_2'];
                                                    $summary[$faculty]['Q4_BUDGET1_2'] += $row['Q4_BUDGET1_2'];
                                                    $summary[$faculty]['TOTAL_BUDGET_1_2'] += $row['TOTAL_BUDGET_1_2'];
                                                    $summary[$faculty]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                    $summary[$faculty]['Q1_BUDGET4'] += $row['Q1_BUDGET4'];
                                                    $summary[$faculty]['Q2_BUDGET4'] += $row['Q2_BUDGET4'];
                                                    $summary[$faculty]['Q3_BUDGET4'] += $row['Q3_BUDGET4'];
                                                    $summary[$faculty]['Q4_BUDGET4'] += $row['Q4_BUDGET4'];
                                                    $summary[$faculty]['TOTAL_BUDGET_YEAR_4'] += $row['TOTAL_BUDGET_YEAR_4'];
                                                    $summary[$faculty]['Total_Amount_5'] += $row['Total_Amount_5'];
                                                    $summary[$faculty]['Q1_BUDGET5'] += $row['Q1_BUDGET5'];
                                                    $summary[$faculty]['Q2_BUDGET5'] += $row['Q2_BUDGET5'];
                                                    $summary[$faculty]['Q3_BUDGET5'] += $row['Q3_BUDGET5'];
                                                    $summary[$faculty]['Q4_BUDGET5'] += $row['Q4_BUDGET5'];
                                                    $summary[$faculty]['TOTAL_BUDGET_YEAR_5'] += $row['TOTAL_BUDGET_YEAR_5'];


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
                                                    $summary[$faculty]['plans'][$plan]['Total_Amount_Quantity1_2'] += $row['Total_Amount_Quantity1_2'];
                                                    $summary[$faculty]['plans'][$plan]['Q1_BUDGET1_2'] += $row['Q1_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['Q2_BUDGET1_2'] += $row['Q2_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['Q3_BUDGET1_2'] += $row['Q3_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['Q4_BUDGET1_2'] += $row['Q4_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['TOTAL_BUDGET_1_2'] += $row['TOTAL_BUDGET_1_2'];
                                                    $summary[$faculty]['plans'][$plan]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                    $summary[$faculty]['plans'][$plan]['Q1_BUDGET4'] += $row['Q1_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['Q2_BUDGET4'] += $row['Q2_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['Q3_BUDGET4'] += $row['Q3_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['Q4_BUDGET4'] += $row['Q4_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['TOTAL_BUDGET_YEAR_4'] += $row['TOTAL_BUDGET_YEAR_4'];
                                                    $summary[$faculty]['plans'][$plan]['Total_Amount_5'] += $row['Total_Amount_5'];
                                                    $summary[$faculty]['plans'][$plan]['Q1_BUDGET5'] += $row['Q1_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['Q2_BUDGET5'] += $row['Q2_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['Q3_BUDGET5'] += $row['Q3_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['Q4_BUDGET5'] += $row['Q4_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['TOTAL_BUDGET_YEAR_5'] += $row['TOTAL_BUDGET_YEAR_5'];

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
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Total_Amount_Quantity1_2'] += $row['Total_Amount_Quantity1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q1_BUDGET1_2'] += $row['Q1_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q2_BUDGET1_2'] += $row['Q2_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q3_BUDGET1_2'] += $row['Q3_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q4_BUDGET1_2'] += $row['Q4_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['TOTAL_BUDGET_1_2'] += $row['TOTAL_BUDGET_1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q1_BUDGET4'] += $row['Q1_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q2_BUDGET4'] += $row['Q2_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q3_BUDGET4'] += $row['Q3_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q4_BUDGET4'] += $row['Q4_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['TOTAL_BUDGET_YEAR_4'] += $row['TOTAL_BUDGET_YEAR_4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Total_Amount_5'] += $row['Total_Amount_5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q1_BUDGET5'] += $row['Q1_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q2_BUDGET5'] += $row['Q2_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q3_BUDGET5'] += $row['Q3_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['Q4_BUDGET5'] += $row['Q4_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['TOTAL_BUDGET_YEAR_5'] += $row['TOTAL_BUDGET_YEAR_5'];


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
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_Quantity1_2'] += $row['Total_Amount_Quantity1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q1_BUDGET1_2'] += $row['Q1_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q2_BUDGET1_2'] += $row['Q2_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q3_BUDGET1_2'] += $row['Q3_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q4_BUDGET1_2'] += $row['Q4_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['TOTAL_BUDGET_1_2'] += $row['TOTAL_BUDGET_1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q1_BUDGET4'] += $row['Q1_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q2_BUDGET4'] += $row['Q2_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q3_BUDGET4'] += $row['Q3_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q4_BUDGET4'] += $row['Q4_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['TOTAL_BUDGET_YEAR_4'] += $row['TOTAL_BUDGET_YEAR_4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Total_Amount_5'] += $row['Total_Amount_5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q1_BUDGET5'] += $row['Q1_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q2_BUDGET5'] += $row['Q2_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q3_BUDGET5'] += $row['Q3_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['Q4_BUDGET5'] += $row['Q4_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['TOTAL_BUDGET_YEAR_5'] += $row['TOTAL_BUDGET_YEAR_5'];



                                                    // เก็บข้อมูลของ Sub_Type
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Total_Amount_1'] += $row['Total_Amount_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q1_BUDGET1'] += $row['Q1_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q2_BUDGET1'] += $row['Q2_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q3_BUDGET1'] += $row['Q3_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q4_BUDGET1'] += $row['Q4_BUDGET1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['TOTAL_BUDGET_YEAR_1'] += $row['TOTAL_BUDGET_YEAR_1'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Total_Amount_2'] += $row['Total_Amount_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q1_BUDGET2'] += $row['Q1_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q2_BUDGET2'] += $row['Q2_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q3_BUDGET2'] += $row['Q3_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q4_BUDGET2'] += $row['Q4_BUDGET2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['TOTAL_BUDGET_YEAR_2'] += $row['TOTAL_BUDGET_YEAR_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Total_Amount_Quantity1_2'] += $row['Total_Amount_Quantity1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q1_BUDGET1_2'] += $row['Q1_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q2_BUDGET1_2'] += $row['Q2_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q3_BUDGET1_2'] += $row['Q3_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q4_BUDGET1_2'] += $row['Q4_BUDGET1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['TOTAL_BUDGET_1_2'] += $row['TOTAL_BUDGET_1_2'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Total_Amount_4'] += $row['Total_Amount_4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q1_BUDGET4'] += $row['Q1_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q2_BUDGET4'] += $row['Q2_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q3_BUDGET4'] += $row['Q3_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q4_BUDGET4'] += $row['Q4_BUDGET4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['TOTAL_BUDGET_YEAR_4'] += $row['TOTAL_BUDGET_YEAR_4'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Total_Amount_5'] += $row['Total_Amount_5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q1_BUDGET5'] += $row['Q1_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q2_BUDGET5'] += $row['Q2_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q3_BUDGET5'] += $row['Q3_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['Q4_BUDGET5'] += $row['Q4_BUDGET5'];
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['TOTAL_BUDGET_YEAR_5'] += $row['TOTAL_BUDGET_YEAR_5'];


                                                    // เก็บข้อมูลของ KKU_Item_Name
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "<strong>" . htmlspecialchars($row['Account']) . "</strong> : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                        : "<strong>" . htmlspecialchars($row['Account']) . "</strong>";
                                                    $summary[$faculty]['plans'][$plan]['sub_plans'][$subPlan]['projects'][$project]['sub_types'][$subType]['kku_items'][] = [
                                                        'name' => $kkuItemName,
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
                                                        'Total_Amount_Quantity1_2' => $row['Total_Amount_Quantity1_2'],
                                                        'Q1_BUDGET1_2' => $row['Q1_BUDGET1_2'],
                                                        'Q2_BUDGET1_2' => $row['Q2_BUDGET1_2'],
                                                        'Q3_BUDGET1_2' => $row['Q3_BUDGET1_2'],
                                                        'Q4_BUDGET1_2' => $row['Q4_BUDGET1_2'],
                                                        'TOTAL_BUDGET_1_2' => $row['TOTAL_BUDGET_1_2'],
                                                        'Total_Amount_4' => $row['Total_Amount_4'],
                                                        'Q1_BUDGET4' => $row['Q1_BUDGET4'],
                                                        'Q2_BUDGET4' => $row['Q2_BUDGET4'],
                                                        'Q3_BUDGET4' => $row['Q3_BUDGET4'],
                                                        'Q4_BUDGET4' => $row['Q4_BUDGET4'],
                                                        'TOTAL_BUDGET_YEAR_4' => $row['TOTAL_BUDGET_YEAR_4'],
                                                        'Total_Amount_5' => $row['Total_Amount_5'],
                                                        'Q1_BUDGET5' => $row['Q1_BUDGET5'],
                                                        'Q2_BUDGET5' => $row['Q2_BUDGET5'],
                                                        'Q3_BUDGET5' => $row['Q3_BUDGET5'],
                                                        'Q4_BUDGET5' => $row['Q4_BUDGET5'],
                                                        'TOTAL_BUDGET_YEAR_5' => $row['TOTAL_BUDGET_YEAR_5'],

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
                                                        'Total_Amount_Quantity1_2' => 0,
                                                        'Q1_BUDGET1_2' => 0,
                                                        'Q2_BUDGET1_2' => 0,
                                                        'Q3_BUDGET1_2' => 0,
                                                        'Q4_BUDGET1_2' => 0,
                                                        'TOTAL_BUDGET_1_2' => 0,
                                                        'Total_Amount_4' => 0,
                                                        'Q1_BUDGET4' => 0,
                                                        'Q2_BUDGET4' => 0,
                                                        'Q3_BUDGET4' => 0,
                                                        'Q4_BUDGET4' => 0,
                                                        'TOTAL_BUDGET_YEAR_4' => 0,
                                                        'Total_Amount_5' => 0,
                                                        'Q1_BUDGET5' => 0,
                                                        'Q2_BUDGET5' => 0,
                                                        'Q3_BUDGET5' => 0,
                                                        'Q4_BUDGET5' => 0,
                                                        'TOTAL_BUDGET_YEAR_5' => 0,
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
                                                        $total_summary['Total_Amount_Quantity1_2'] += (float) ($row['Total_Amount_Quantity1_2'] ?? 0);
                                                        $total_summary['Q1_BUDGET1_2'] += (float) ($row['Q1_BUDGET1_2'] ?? 0);
                                                        $total_summary['Q2_BUDGET1_2'] += (float) ($row['Q2_BUDGET1_2'] ?? 0);
                                                        $total_summary['Q3_BUDGET1_2'] += (float) ($row['Q3_BUDGET1_2'] ?? 0);
                                                        $total_summary['Q4_BUDGET1_2'] += (float) ($row['Q4_BUDGET1_2'] ?? 0);
                                                        $total_summary['TOTAL_BUDGET_1_2'] += (float) ($row['TOTAL_BUDGET_1_2'] ?? 0);
                                                        $total_summary['Total_Amount_4'] += (float) ($row['Total_Amount_4'] ?? 0);
                                                        $total_summary['Q1_BUDGET4'] += (float) ($row['Q1_BUDGET4'] ?? 0);
                                                        $total_summary['Q2_BUDGET4'] += (float) ($row['Q2_BUDGET4'] ?? 0);
                                                        $total_summary['Q3_BUDGET4'] += (float) ($row['Q3_BUDGET4'] ?? 0);
                                                        $total_summary['Q4_BUDGET4'] += (float) ($row['Q4_BUDGET4'] ?? 0);
                                                        $total_summary['TOTAL_BUDGET_YEAR_4'] += (float) ($row['TOTAL_BUDGET_YEAR_4'] ?? 0);
                                                        $total_summary['Total_Amount_5'] += (float) ($row['Total_Amount_5'] ?? 0);
                                                        $total_summary['Q1_BUDGET5'] += (float) ($row['Q1_BUDGET5'] ?? 0);
                                                        $total_summary['Q2_BUDGET5'] += (float) ($row['Q2_BUDGET5'] ?? 0);
                                                        $total_summary['Q3_BUDGET5'] += (float) ($row['Q3_BUDGET5'] ?? 0);
                                                        $total_summary['Q4_BUDGET5'] += (float) ($row['Q4_BUDGET5'] ?? 0);
                                                        $total_summary['TOTAL_BUDGET_YEAR_5'] += (float) ($row['TOTAL_BUDGET_YEAR_5'] ?? 0);
                                                    }
                                                }

                                                if ($selectedFaculty == null) {
                                                    // ตรวจสอบว่ามีข้อมูลใน $summary หรือไม่
                                                    if (isset($summary) && is_array($summary)) {
                                                        echo "<tr>";
                                                        echo "<td style='text-align: left;'><strong>" . 'รวมทั้งสิ้น' . "<br></td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_5']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q1_BUDGET5']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q2_BUDGET5']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q3_BUDGET5']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q4_BUDGET5']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['TOTAL_BUDGET_YEAR_5']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q1_BUDGET4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q2_BUDGET4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q3_BUDGET4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q4_BUDGET4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['TOTAL_BUDGET_YEAR_4']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_Quantity1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q1_BUDGET1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q2_BUDGET1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q3_BUDGET1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Q4_BUDGET1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['TOTAL_BUDGET_1_2']) . "</td>";
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
                                                        echo "<td style='text-align: left;'><strong>". htmlspecialchars($facultyData) ."<br></td>";
                                                        }
                                                    if ($selectedFaculty != null) {
                                                    echo "<td style='text-align: left;'><strong>" . 'รวมทั้งสิ้น' . "<br></td>";
                                                    }
                                                    echo "<td>" . formatNumber($data['Total_Amount_5']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q1_BUDGET5']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q2_BUDGET5']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q3_BUDGET5']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q4_BUDGET5']) . "</td>";
                                                    echo "<td>" . formatNumber($data['TOTAL_BUDGET_YEAR_5']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_Amount_4']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q1_BUDGET4']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q2_BUDGET4']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q3_BUDGET4']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q4_BUDGET4']) . "</td>";
                                                    echo "<td>" . formatNumber($data['TOTAL_BUDGET_YEAR_4']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_Amount_Quantity1_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q1_BUDGET1_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q2_BUDGET1_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q3_BUDGET1_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Q4_BUDGET1_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['TOTAL_BUDGET_1_2']) . "</td>";
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
                                                        echo "<td style='text-align: left;'><strong>" . htmlspecialchars($plan) . "</strong> : " . htmlspecialchars($planData['plan_name']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Total_Amount_5']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q1_BUDGET5']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q2_BUDGET5']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q3_BUDGET5']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q4_BUDGET5']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['TOTAL_BUDGET_YEAR_5']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Total_Amount_4']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q1_BUDGET4']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q2_BUDGET4']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q3_BUDGET4']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q4_BUDGET4']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['TOTAL_BUDGET_YEAR_4']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Total_Amount_Quantity1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q1_BUDGET1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q2_BUDGET1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q3_BUDGET1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['Q4_BUDGET1_2']) . "</td>";
                                                        echo "<td>" . formatNumber($planData['TOTAL_BUDGET_1_2']) . "</td>";
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

                                                            // แสดงผลข้อมูล
                                                            echo "<td style='text-align: left;'><strong>" . str_repeat("&nbsp;", 8) . htmlspecialchars($cleanedSubPlan) . "</strong> : " . htmlspecialchars($subData['sub_plan_name']) . "<br></td>";
                                                            echo "<td>" . formatNumber($subData['Total_Amount_5']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q1_BUDGET5']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q2_BUDGET5']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q3_BUDGET5']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q4_BUDGET5']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['TOTAL_BUDGET_YEAR_5']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Total_Amount_4']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q1_BUDGET4']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q2_BUDGET4']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q3_BUDGET4']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q4_BUDGET4']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['TOTAL_BUDGET_YEAR_4']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Total_Amount_Quantity1_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q1_BUDGET1_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q2_BUDGET1_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q3_BUDGET1_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['Q4_BUDGET1_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subData['TOTAL_BUDGET_1_2']) . "</td>";
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
                                                                echo "<td style='text-align: left;'><strong>" . str_repeat("&nbsp;", 16) . htmlspecialchars($project) . "</strong><br></td>";
                                                                echo "<td>" . formatNumber($projectData['Total_Amount_5']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q1_BUDGET5']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q2_BUDGET5']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q3_BUDGET5']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q4_BUDGET5']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['TOTAL_BUDGET_YEAR_5']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Total_Amount_4']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q1_BUDGET4']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q2_BUDGET4']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q3_BUDGET4']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q4_BUDGET4']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['TOTAL_BUDGET_YEAR_4']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Total_Amount_Quantity1_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q1_BUDGET1_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q2_BUDGET1_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q3_BUDGET1_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['Q4_BUDGET1_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectData['TOTAL_BUDGET_1_2']) . "</td>";
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
                                                                foreach ($projectData['sub_types'] as $subType => $subTypeData) {
                                                                    echo "<tr>";
                                                                    // ใช้ Regex ลบตัวเลขและจุดข้างหน้า
                                                                    $cleanedSubType = preg_replace('/^[\d.]+\s*/', '', $subType);

                                                                    // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a2 และ subType
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 24) . htmlspecialchars($subTypeData['a2']) . " : " . htmlspecialchars($cleanedSubType) . "<br></td>";

                                                                    echo "<td>" . formatNumber($subTypeData['Total_Amount_5']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q1_BUDGET5']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q2_BUDGET5']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q3_BUDGET5']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q4_BUDGET5']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['TOTAL_BUDGET_YEAR_5']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Total_Amount_4']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q1_BUDGET4']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q2_BUDGET4']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q3_BUDGET4']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q4_BUDGET4']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['TOTAL_BUDGET_YEAR_4']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Total_Amount_Quantity1_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q1_BUDGET1_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q2_BUDGET1_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q3_BUDGET1_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q4_BUDGET1_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['TOTAL_BUDGET_1_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Total_Amount_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q1_BUDGET2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q2_BUDGET2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q3_BUDGET2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q4_BUDGET2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['TOTAL_BUDGET_YEAR_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Total_Amount_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q1_BUDGET1']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q2_BUDGET1']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q3_BUDGET1']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['Q4_BUDGET1']) . "</td>";
                                                                    echo "<td>" . formatNumber($subTypeData['TOTAL_BUDGET_YEAR_1']) . "</td>";



                                                                    echo "</tr>";

                                                                    // แสดงข้อมูล KKU_Item_Name
                                                                    foreach ($subTypeData['kku_items'] as $kkuItem) {
                                                                        echo "<tr>";
                                                                        echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . $kkuItem['name'] . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_5']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q1_BUDGET5']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q2_BUDGET5']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q3_BUDGET5']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q4_BUDGET5']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['TOTAL_BUDGET_YEAR_5']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_4']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q1_BUDGET4']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q2_BUDGET4']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q3_BUDGET4']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q4_BUDGET4']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['TOTAL_BUDGET_YEAR_4']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_Quantity1_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q1_BUDGET1_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q2_BUDGET1_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q3_BUDGET1_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['Q4_BUDGET1_2']) . "</td>";
                                                                        echo "<td>" . formatNumber($kkuItem['TOTAL_BUDGET_1_2']) . "</td>";
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