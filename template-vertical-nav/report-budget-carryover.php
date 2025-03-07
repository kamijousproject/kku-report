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

function fetchBudgetData($conn, $faculty = null)
{
    try {
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
        bap.Faculty AS Faculty_Id,
        ft.Faculty, 
        ft.Alias_Default, 
        bpa.BUDGET_PERIOD,
       
        ac.`type`,
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


,
        ac.sub_type,
        ac.alias_default AS Account_Name_default,
        bap.`Account`,
        bap.KKU_Item_Name,
        SUM(CASE WHEN bap.Fund = 'FN02' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_FN02,
        SUM(CASE WHEN bap.Fund = 'FN06' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_FN06,
        SUM(CASE WHEN bap.Fund = 'FN08' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_FN08,
        SUM(bap.Allocated_Total_Amount_Quantity) AS Total_Amount,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 AND bap.Fund = 'FN02' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568_FN02,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 AND bap.Fund = 'FN06' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568_FN06,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 AND bap.Fund = 'FN08' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568_FN08,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568_SUM,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 AND bap.Fund = 'FN02' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567_FN02,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 AND bap.Fund = 'FN06' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567_FN06,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 AND bap.Fund = 'FN08' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567_FN08,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567_SUM,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) - 
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Difference_2568_2567,
        CASE
            WHEN SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) = 0 THEN 100
            ELSE (SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) / 
                  SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END)) * 100
        END AS Percentage_2568_to_2567,
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
    m.TotalLevels
        FROM budget_planning_allocated_annual_budget_plan bap
        INNER JOIN Faculty ft ON bap.Faculty = ft.Faculty AND ft.parent LIKE 'Faculty%'
        LEFT JOIN plan p ON bap.Plan = p.plan_id
        LEFT JOIN sub_plan sp ON bap.Sub_Plan = sp.sub_plan_id
        LEFT JOIN project pj ON bap.Project = pj.project_id
        INNER JOIN account ac ON bap.`Account` = ac.`account`
        LEFT JOIN main m
ON bap.`Account`=m.CurrentAccount
        INNER JOIN budget_planning_actual bpa ON bpa.PROJECT = bap.Project
            AND bpa.`ACCOUNT` = bap.`Account`
            AND bpa.PLAN = bap.Plan
            AND bpa.FUND = bap.Fund
            AND bpa.SUBPLAN = CAST(SUBSTRING(bap.Sub_Plan, 4) AS UNSIGNED)
            AND bpa.SERVICE = CAST(REPLACE(bap.Service, 'SR_', '') AS UNSIGNED)";

        if ($faculty) {
            $query .= " AND bap.Faculty = :faculty";
        }

        $query .= " GROUP BY 
            bap.Faculty, 
            ft.Faculty, 
            ft.Alias_Default, 
            bpa.BUDGET_PERIOD, 
            bap.`Account`,ac.alias_default, 
            ac.`type`, 
            ac.sub_type, 
            bap.KKU_Item_Name,
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
    m.TotalLevels
        ORDER BY 
            bap.Faculty ASC, 
            ac.`type` ASC, 
            ac.sub_type ASC,
            bap.`Account` ASC

)
SELECT * FROM t1";

        $stmt = $conn->prepare($query);

        if ($faculty) {
            $stmt->bindParam(':faculty', $faculty, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}


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
                        <h4>รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">
                                รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว
                                        ประเภทที่ยังไม่มีหนี้</h4>
                                </div>

                                <?php
                                $faculties = fetchFacultyData($conn);
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
                                            window.location.href = "http://localhost/kku-report/template-vertical-nav/report-budget-carryover.php";
                                            return false; // ป้องกันการส่งฟอร์ม
                                        }
                                        return true;
                                    }
                                </script>

                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">
                                        <thead>
                                            <tr>
                                                <th colspan="11" style='text-align: left;'>
                                                    รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้วประเภทที่ยังไม่มีหนี้
                                                </th>
                                            </tr>
                                            <?php


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
                                                <th colspan="11" style='text-align: left;'>
                                                    <span style="font-size: 16px;">


                                                        <?php
                                                        $facultyData = str_replace('-', ':', $selectedFacultyName);

                                                        echo "ส่วนงาน / หน่วยงาน: " . $facultyData; ?>
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="4">ปี 2568 (ปีที่ขอตั้ง)</th>
                                                <th colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                                <th>รวม</th>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                                <th>รวม</th>
                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>

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

                                        $results = fetchBudgetData($conn, $selectedFaculty);

                                        // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                        if (isset($results) && is_array($results) && count($results) > 0) {
                                            // สร้าง associative array เพื่อเก็บผลรวมของแต่ละ Plan, Sub_Plan, Project, และ Sub_Type
                                            $summary = [];
                                            foreach ($results as $row) {
                                                $Faculty = $row['Alias_Default'];
                                                $Name_a1 = $row['Name_a1'];
                                                $Name_a2 = $row['Name_a2'];
                                                $Name_a3 = $row['Name_a3'];


                                                if (!isset($summary[$Faculty])) {
                                                    $summary[$Faculty] = [
                                                        'Alias_Default' => $row['Alias_Default'],
                                                        'Total_Amount_2567_FN06' => 0,
                                                        'Total_Amount_2567_FN08' => 0,
                                                        'Total_Amount_2567_FN02' => 0,
                                                        'Total_Amount_2567_SUM' => 0,
                                                        'Total_Amount_2568_FN06' => 0,
                                                        'Total_Amount_2568_FN08' => 0,
                                                        'Total_Amount_2568_FN02' => 0,
                                                        'Total_Amount_2568_SUM' => 0,
                                                        'Difference_2568_2567' => 0,
                                                        'Percentage_2568_to_2567' => 0,
                                                        'Name_a1' => [], // เก็บข้อมูลของ Sub_Plan
                                                    ];
                                                }
                                                $ItemName_a1 = (!empty($row['Name_a1']))
                                                    ? "" . htmlspecialchars($row['a1']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a1']))
                                                    : "" . htmlspecialchars($row['a1']) . "";
                                                if (!isset($summary[$Faculty]['Name_a1'][$Name_a1])) {
                                                    $summary[$Faculty]['Name_a1'][$Name_a1] = [
                                                        'name' => $ItemName_a1,
                                                        'a1' => $row['a1'],
                                                        'Total_Amount_2567_FN06' => 0,
                                                        'Total_Amount_2567_FN08' => 0,
                                                        'Total_Amount_2567_FN02' => 0,
                                                        'Total_Amount_2567_SUM' => 0,
                                                        'Total_Amount_2568_FN06' => 0,
                                                        'Total_Amount_2568_FN08' => 0,
                                                        'Total_Amount_2568_FN02' => 0,
                                                        'Total_Amount_2568_SUM' => 0,
                                                        'Difference_2568_2567' => 0,
                                                        'Percentage_2568_to_2567' => 0,
                                                        'Name_a2' => [], // เก็บข้อมูลของ Sub_Plan
                                                    ];
                                                }
                                                $ItemName_a2 = (!empty($row['Name_a2']))
                                                    ? "" . htmlspecialchars($row['a2']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']))
                                                    : "" . htmlspecialchars($row['a2']) . "";
                                                if (!isset($summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2])) {
                                                    $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2] = [
                                                        'name' => $ItemName_a2,
                                                        'a2' => $row['a2'],
                                                        'Total_Amount_2567_FN06' => 0,
                                                        'Total_Amount_2567_FN08' => 0,
                                                        'Total_Amount_2567_FN02' => 0,
                                                        'Total_Amount_2567_SUM' => 0,
                                                        'Total_Amount_2568_FN06' => 0,
                                                        'Total_Amount_2568_FN08' => 0,
                                                        'Total_Amount_2568_FN02' => 0,
                                                        'Total_Amount_2568_SUM' => 0,
                                                        'Difference_2568_2567' => 0,
                                                        'Percentage_2568_to_2567' => 0,
                                                        'Name_a3' => [], // เก็บข้อมูลของ Sub_Plan
                                                    ];
                                                }
                                                $ItemName_a3 = (!empty($row['Name_a3']))
                                                    ? "" . htmlspecialchars($row['a3']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a3']))
                                                    : "" . htmlspecialchars($row['a3']) . "";
                                                if (!isset($summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3])) {
                                                    $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3] = [
                                                        'name' => $ItemName_a3,
                                                        'a3' => $row['a3'],
                                                        'Total_Amount_2567_FN06' => 0,
                                                        'Total_Amount_2567_FN08' => 0,
                                                        'Total_Amount_2567_FN02' => 0,
                                                        'Total_Amount_2567_SUM' => 0,
                                                        'Total_Amount_2568_FN06' => 0,
                                                        'Total_Amount_2568_FN08' => 0,
                                                        'Total_Amount_2568_FN02' => 0,
                                                        'Total_Amount_2568_SUM' => 0,
                                                        'Difference_2568_2567' => 0,
                                                        'Percentage_2568_to_2567' => 0,
                                                        'Name_a4' => [], // เก็บข้อมูลของ Sub_Plan
                                                    ];
                                                }

                                                // รวมข้อมูลของ Faculty
                                                $summary[$Faculty]['Total_Amount_2567_FN06'] += $row['Total_Amount_2567_FN06'];
                                                $summary[$Faculty]['Total_Amount_2567_FN08'] += $row['Total_Amount_2567_FN08'];
                                                $summary[$Faculty]['Total_Amount_2567_FN02'] += $row['Total_Amount_2567_FN02'];
                                                $summary[$Faculty]['Total_Amount_2568_FN06'] += $row['Total_Amount_2568_FN06'];
                                                $summary[$Faculty]['Total_Amount_2568_FN08'] += $row['Total_Amount_2568_FN08'];
                                                $summary[$Faculty]['Total_Amount_2568_FN02'] += $row['Total_Amount_2568_FN02'];

                                                // รวมข้อมูลของ Name_a1
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Total_Amount_2567_FN06'] += $row['Total_Amount_2567_FN06'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Total_Amount_2567_FN08'] += $row['Total_Amount_2567_FN08'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Total_Amount_2567_FN02'] += $row['Total_Amount_2567_FN02'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Total_Amount_2568_FN06'] += $row['Total_Amount_2568_FN06'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Total_Amount_2568_FN08'] += $row['Total_Amount_2568_FN08'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Total_Amount_2568_FN02'] += $row['Total_Amount_2568_FN02'];

                                                // รวมข้อมูลของ Name_a2
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_2567_FN06'] += $row['Total_Amount_2567_FN06'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_2567_FN08'] += $row['Total_Amount_2567_FN08'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_2567_FN02'] += $row['Total_Amount_2567_FN02'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_2568_FN06'] += $row['Total_Amount_2568_FN06'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_2568_FN08'] += $row['Total_Amount_2568_FN08'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_Amount_2568_FN02'] += $row['Total_Amount_2568_FN02'];

                                                // รวมข้อมูลของ Name_a2
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_2567_FN06'] += $row['Total_Amount_2567_FN06'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_2567_FN08'] += $row['Total_Amount_2567_FN08'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_2567_FN02'] += $row['Total_Amount_2567_FN02'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_2568_FN06'] += $row['Total_Amount_2568_FN06'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_2568_FN08'] += $row['Total_Amount_2568_FN08'];
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_Amount_2568_FN02'] += $row['Total_Amount_2568_FN02'];

                                                // เก็บข้อมูลของ KKU_Item_Name
                                                $ItemName_a4 = (!empty($row['Name_a4']))
                                                    ? "" . htmlspecialchars($row['Account']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a4']))
                                                    : "" . htmlspecialchars($row['Account']) . "";
                                                $summary[$Faculty]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][] = [
                                                    'name' => $ItemName_a4,
                                                    'test' => $row['Name_a4'],
                                                    'Total_Amount_2567_FN06' => $row['Total_Amount_2567_FN06'],
                                                    'Total_Amount_2567_FN08' => $row['Total_Amount_2567_FN08'],
                                                    'Total_Amount_2567_FN02' => $row['Total_Amount_2567_FN02'],
                                                    'Total_Amount_2567_SUM' => $row['Total_Amount_2567_SUM'],
                                                    'Total_Amount_2568_FN06' => $row['Total_Amount_2568_FN06'],
                                                    'Total_Amount_2568_FN08' => $row['Total_Amount_2568_FN08'],
                                                    'Total_Amount_2568_FN02' => $row['Total_Amount_2568_FN02'],
                                                    'Total_Amount_2568_SUM' => $row['Total_Amount_2568_SUM'],
                                                    'Difference_2568_2567' => $row['Difference_2568_2567'],
                                                    'Percentage_2568_to_2567' => $row['Percentage_2568_to_2567'],
                                                ];
                                                $rows = $summary;
                                                // ตัวแปรสำหรับเก็บผลรวมทั้งหมด
                                                $total_summary = [
                                                    'Total_Amount_2567_FN06' => 0,
                                                    'Total_Amount_2567_FN08' => 0,
                                                    'Total_Amount_2567_FN02' => 0,
                                                    'Total_Amount_2567_SUM' => 0,
                                                    'Total_Amount_2568_FN06' => 0,
                                                    'Total_Amount_2568_FN08' => 0,
                                                    'Total_Amount_2568_FN02' => 0,
                                                    'Total_Amount_2568_SUM' => 0,
                                                    'Difference_2568_2567' => 0,
                                                    'Percentage_2568_to_2567' => 0,
                                                ];
                                                // แสดงผลรวมทั้งหมด
                                                //print_r($total_summary);
                                                // Assuming this is inside a loop where $row is updated (e.g., from a database query)
                                                foreach ($rows as $row) { // Replace $rows with your actual data source
                                                    // รวมผลรวมทั้งหมดโดยไม่สนใจ Faculty
                                                    $total_summary['Total_Amount_2567_FN06'] += (float) ($row['Total_Amount_2567_FN06'] ?? 0);
                                                    $total_summary['Total_Amount_2567_FN08'] += (float) ($row['Total_Amount_2567_FN08'] ?? 0);
                                                    $total_summary['Total_Amount_2567_FN02'] += (float) ($row['Total_Amount_2567_FN02'] ?? 0);

                                                    $total_summary['Total_Amount_2568_FN06'] += (float) ($row['Total_Amount_2568_FN06'] ?? 0);
                                                    $total_summary['Total_Amount_2568_FN08'] += (float) ($row['Total_Amount_2568_FN08'] ?? 0);
                                                    $total_summary['Total_Amount_2568_FN02'] += (float) ($row['Total_Amount_2568_FN02'] ?? 0);
                                                }
                                            }

                                            if ($selectedFaculty == null) {
                                                if (isset($summary) && is_array($summary)) {
                                                    // แสดงผลลัพธ์ในรูปแบบตาราง
                                                    echo "<tr>";
                                                    // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a2 และ subType
                                                    echo "<td style='text-align: left;'>" . 'รวมทั้งสิ้น' . "<br></td>";

                                                    // Check if the keys exist before accessing them
                                                    echo "<td>" . (isset($total_summary['Total_Amount_2567_FN06']) ? formatNumber($total_summary['Total_Amount_2567_FN06']) : '0') . "</td>";
                                                    echo "<td>" . (isset($total_summary['Total_Amount_2567_FN08']) ? formatNumber($total_summary['Total_Amount_2567_FN08']) : '0') . "</td>";
                                                    echo "<td>" . (isset($total_summary['Total_Amount_2567_FN02']) ? formatNumber($total_summary['Total_Amount_2567_FN02']) : '0') . "</td>";

                                                    $total1 = (isset($total_summary['Total_Amount_2567_FN06']) ? $total_summary['Total_Amount_2567_FN06'] : 0) +
                                                        (isset($total_summary['Total_Amount_2567_FN08']) ? $total_summary['Total_Amount_2567_FN08'] : 0) +
                                                        (isset($total_summary['Total_Amount_2567_FN02']) ? $total_summary['Total_Amount_2567_FN02'] : 0);
                                                    echo "<td>" . formatNumber($total1) . "</td>";

                                                    echo "<td>" . (isset($total_summary['Total_Amount_2568_FN06']) ? formatNumber($total_summary['Total_Amount_2568_FN06']) : '0') . "</td>";
                                                    echo "<td>" . (isset($total_summary['Total_Amount_2568_FN08']) ? formatNumber($total_summary['Total_Amount_2568_FN08']) : '0') . "</td>";
                                                    echo "<td>" . (isset($total_summary['Total_Amount_2568_FN02']) ? formatNumber($total_summary['Total_Amount_2568_FN02']) : '0') . "</td>";

                                                    $total2 = (isset($total_summary['Total_Amount_2568_FN06']) ? $total_summary['Total_Amount_2568_FN06'] : 0) +
                                                        (isset($total_summary['Total_Amount_2568_FN08']) ? $total_summary['Total_Amount_2568_FN08'] : 0) +
                                                        (isset($total_summary['Total_Amount_2568_FN02']) ? $total_summary['Total_Amount_2568_FN02'] : 0);
                                                    echo "<td>" . formatNumber($total2) . "</td>";

                                                    $Difference = $total2 - $total1;
                                                    echo "<td>" . formatNumber($Difference) . "</td>";

                                                    $Percentage_Difference = ($total1 != 0) ? ($Difference / $total1) * 100 : 100;
                                                    echo "<td>" . formatNumber($Percentage_Difference) . "</td>";
                                                    echo "</tr>";
                                                } else {
                                                    // แสดงข้อความหากไม่มีข้อมูล
                                                    echo "<tr><td colspan='7' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                                }
                                            }
                                            // แสดงผลลัพธ์
                                            foreach ($summary as $Alias_Default => $data) {
                                                // แสดงผลรวมของ Plan
                                                echo "<tr>";


                                                if ($selectedFaculty == null) {
                                                    $facultyData = str_replace('-', ':', $data['Alias_Default']);
                                                    echo "<td style='text-align: left;'>" . htmlspecialchars($facultyData) . "<br></td>";
                                                }
                                                if ($selectedFaculty != null) {
                                                    echo "<td style='text-align: left;'>" . 'รวมทั้งสิ้น' . "<br></td>";
                                                }

                                                // Check if the keys exist before accessing them
                                                echo "<td>" . (isset($data['Total_Amount_2567_FN06']) ? formatNumber($data['Total_Amount_2567_FN06']) : '0') . "</td>";
                                                echo "<td>" . (isset($data['Total_Amount_2567_FN08']) ? formatNumber($data['Total_Amount_2567_FN08']) : '0') . "</td>";
                                                echo "<td>" . (isset($data['Total_Amount_2567_FN02']) ? formatNumber($data['Total_Amount_2567_FN02']) : '0') . "</td>";

                                                $total1 = (isset($data['Total_Amount_2567_FN06']) ? $data['Total_Amount_2567_FN06'] : 0) +
                                                    (isset($data['Total_Amount_2567_FN08']) ? $data['Total_Amount_2567_FN08'] : 0) +
                                                    (isset($data['Total_Amount_2567_FN02']) ? $data['Total_Amount_2567_FN02'] : 0);
                                                echo "<td>" . formatNumber($total1) . "</td>";

                                                echo "<td>" . (isset($data['Total_Amount_2568_FN06']) ? formatNumber($data['Total_Amount_2568_FN06']) : '0') . "</td>";
                                                echo "<td>" . (isset($data['Total_Amount_2568_FN08']) ? formatNumber($data['Total_Amount_2568_FN08']) : '0') . "</td>";
                                                echo "<td>" . (isset($data['Total_Amount_2568_FN02']) ? formatNumber($data['Total_Amount_2568_FN02']) : '0') . "</td>";

                                                $total2 = (isset($data['Total_Amount_2568_FN06']) ? $data['Total_Amount_2568_FN06'] : 0) +
                                                    (isset($data['Total_Amount_2568_FN08']) ? $data['Total_Amount_2568_FN08'] : 0) +
                                                    (isset($data['Total_Amount_2568_FN02']) ? $data['Total_Amount_2568_FN02'] : 0);
                                                echo "<td>" . formatNumber($total2) . "</td>";

                                                $Difference = $total2 - $total1;
                                                echo "<td>" . formatNumber($Difference) . "</td>";

                                                $Percentage_Difference = ($total1 != 0) ? ($Difference / $total1) * 100 : 100;
                                                echo "<td>" . formatNumber($Percentage_Difference) . "</td>";

                                                echo "</tr>";

                                                // แสดงข้อมูลของ Name_a1
                                                if (isset($data['Name_a1']) && is_array($data['Name_a1'])) {
                                                    foreach ($data['Name_a1'] as $Name_a1 => $dataName_a1) {
                                                        echo "<tr>";
                                                        $cleanedName_a1 = preg_replace('/^[\d.]+\s*/', '', $Name_a1);

                                                        // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a1 และ Name_a1
                                                        echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 8) . htmlspecialchars($dataName_a1['a1']) . " : " . htmlspecialchars($cleanedName_a1) . "<br></td>";

                                                        echo "<td>" . formatNumber($dataName_a1['Total_Amount_2567_FN06']) . "</td>";
                                                        echo "<td>" . formatNumber($dataName_a1['Total_Amount_2567_FN08']) . "</td>";
                                                        echo "<td>" . formatNumber($dataName_a1['Total_Amount_2567_FN02']) . "</td>";
                                                        $total1 = $dataName_a1['Total_Amount_2567_FN06'] + $dataName_a1['Total_Amount_2567_FN08'] + $dataName_a1['Total_Amount_2567_FN02'];
                                                        echo "<td>" . formatNumber($total1) . "</td>";
                                                        echo "<td>" . formatNumber($dataName_a1['Total_Amount_2568_FN06']) . "</td>";
                                                        echo "<td>" . formatNumber($dataName_a1['Total_Amount_2568_FN08']) . "</td>";
                                                        echo "<td>" . formatNumber($dataName_a1['Total_Amount_2568_FN02']) . "</td>";
                                                        $total2 = $dataName_a1['Total_Amount_2568_FN06'] + $dataName_a1['Total_Amount_2568_FN08'] + $dataName_a1['Total_Amount_2568_FN02'];
                                                        echo "<td>" . formatNumber($total2) . "</td>";
                                                        $Difference = $total2 - $total1;
                                                        echo "<td>" . formatNumber($Difference) . "</td>";
                                                        $Percentage_Difference = ($total1 != 0) ? ($Difference / $total1) * 100 : 100;
                                                        echo "<td>" . formatNumber($Percentage_Difference) . "</td>";

                                                        echo "</tr>";
                                                        if (isset($dataName_a1['Name_a2']) && is_array($dataName_a1['Name_a2'])) {
                                                            foreach ($dataName_a1['Name_a2'] as $Name_a2 => $dataName_a2) {
                                                                echo "<tr>";
                                                                $cleanedName_a2 = preg_replace('/^[\d.]+\s*/', '', $Name_a2);

                                                                // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a1 และ Name_a2
                                                                echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 16) . htmlspecialchars($dataName_a2['a2']) . " : " . htmlspecialchars($cleanedName_a2) . "<br></td>";

                                                                echo "<td>" . formatNumber($dataName_a2['Total_Amount_2567_FN06']) . "</td>";
                                                                echo "<td>" . formatNumber($dataName_a2['Total_Amount_2567_FN08']) . "</td>";
                                                                echo "<td>" . formatNumber($dataName_a2['Total_Amount_2567_FN02']) . "</td>";
                                                                $total1 = $dataName_a2['Total_Amount_2567_FN06'] + $dataName_a2['Total_Amount_2567_FN08'] + $dataName_a2['Total_Amount_2567_FN02'];
                                                                echo "<td>" . formatNumber($total1) . "</td>";
                                                                echo "<td>" . formatNumber($dataName_a2['Total_Amount_2568_FN06']) . "</td>";
                                                                echo "<td>" . formatNumber($dataName_a2['Total_Amount_2568_FN08']) . "</td>";
                                                                echo "<td>" . formatNumber($dataName_a2['Total_Amount_2568_FN02']) . "</td>";
                                                                $total2 = $dataName_a2['Total_Amount_2568_FN06'] + $dataName_a2['Total_Amount_2568_FN08'] + $dataName_a2['Total_Amount_2568_FN02'];
                                                                echo "<td>" . formatNumber($total2) . "</td>";
                                                                $Difference = $total2 - $total1;
                                                                echo "<td>" . formatNumber($Difference) . "</td>";
                                                                $Percentage_Difference = ($total1 != 0) ? ($Difference / $total1) * 100 : 100;
                                                                echo "<td>" . formatNumber($Percentage_Difference) . "</td>";

                                                                echo "</tr>";
                                                                if ($Name_a3 != null) {
                                                                    if (isset($dataName_a2['Name_a3']) && is_array($dataName_a2['Name_a3'])) {
                                                                        foreach ($dataName_a2['Name_a3'] as $Name_a3 => $dataName_a3) {
                                                                            echo "<tr>";
                                                                            $cleanedName_a3 = preg_replace('/^[\d.]+\s*/', '', $Name_a3);

                                                                            // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a1 และ Name_a3
                                                                            echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 24) . htmlspecialchars($dataName_a3['a3']) . " : " . htmlspecialchars($cleanedName_a3) . "<br></td>";

                                                                            echo "<td>" . formatNumber($dataName_a3['Total_Amount_2567_FN06']) . "</td>";
                                                                            echo "<td>" . formatNumber($dataName_a3['Total_Amount_2567_FN08']) . "</td>";
                                                                            echo "<td>" . formatNumber($dataName_a3['Total_Amount_2567_FN02']) . "</td>";
                                                                            $total1 = $dataName_a3['Total_Amount_2567_FN06'] + $dataName_a3['Total_Amount_2567_FN08'] + $dataName_a3['Total_Amount_2567_FN02'];
                                                                            echo "<td>" . formatNumber($total1) . "</td>";
                                                                            echo "<td>" . formatNumber($dataName_a3['Total_Amount_2568_FN06']) . "</td>";
                                                                            echo "<td>" . formatNumber($dataName_a3['Total_Amount_2568_FN08']) . "</td>";
                                                                            echo "<td>" . formatNumber($dataName_a3['Total_Amount_2568_FN02']) . "</td>";
                                                                            $total2 = $dataName_a3['Total_Amount_2568_FN06'] + $dataName_a3['Total_Amount_2568_FN08'] + $dataName_a3['Total_Amount_2568_FN02'];
                                                                            echo "<td>" . formatNumber($total2) . "</td>";
                                                                            $Difference = $total2 - $total1;
                                                                            echo "<td>" . formatNumber($Difference) . "</td>";
                                                                            $Percentage_Difference = ($total1 != 0) ? ($Difference / $total1) * 100 : 100;
                                                                            echo "<td>" . formatNumber($Percentage_Difference) . "</td>";

                                                                            echo "</tr>";


                                                                            if (isset($dataName_a3['Name_a4']) && is_array($dataName_a3['Name_a4'])) {
                                                                                foreach ($dataName_a3['Name_a4'] as $dataName_a4) {
                                                                                    if ($dataName_a4['test'] == null || $dataName_a4['test'] == '') {
                                                                                        continue;
                                                                                    }
                                                                                    echo "<tr>";
                                                                                    // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a1 และ Name_a4
                                                                                    echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 30) . $dataName_a4['name'] . "<br></td>";
                                                                                    echo "<td>" . formatNumber($dataName_a4['Total_Amount_2567_FN06']) . "</td>";
                                                                                    echo "<td>" . formatNumber($dataName_a4['Total_Amount_2567_FN08']) . "</td>";
                                                                                    echo "<td>" . formatNumber($dataName_a4['Total_Amount_2567_FN02']) . "</td>";
                                                                                    $total1 = $dataName_a4['Total_Amount_2567_FN06'] + $dataName_a4['Total_Amount_2567_FN08'] + $dataName_a4['Total_Amount_2567_FN02'];
                                                                                    echo "<td>" . formatNumber($total1) . "</td>";
                                                                                    echo "<td>" . formatNumber($dataName_a4['Total_Amount_2568_FN06']) . "</td>";
                                                                                    echo "<td>" . formatNumber($dataName_a4['Total_Amount_2568_FN08']) . "</td>";
                                                                                    echo "<td>" . formatNumber($dataName_a4['Total_Amount_2568_FN02']) . "</td>";
                                                                                    $total2 = $dataName_a4['Total_Amount_2568_FN06'] + $dataName_a4['Total_Amount_2568_FN08'] + $dataName_a4['Total_Amount_2568_FN02'];
                                                                                    echo "<td>" . formatNumber($total2) . "</td>";
                                                                                    $Difference = $total2 - $total1;
                                                                                    echo "<td>" . formatNumber($Difference) . "</td>";
                                                                                    $Percentage_Difference = ($total1 != 0) ? ($Difference / $total1) * 100 : 100;
                                                                                    echo "<td>" . formatNumber($Percentage_Difference) . "</td>";

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
                                        } else {
                                            echo "<tr><td colspan='9' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                        }
                                        ?>
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
            csvRows.push(`"รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้",,,,,,,`);

            // ดึงค่าคณะ/หน่วยงานจาก PHP
            const selectedFacultyName = <?php echo json_encode($selectedFacultyName); ?>;
            const facultyData = selectedFacultyName.replace(/-/g, ':');
            csvRows.push(`"ส่วนงาน / หน่วยงาน: ${facultyData}",,,,,,,`);

            // เพิ่มส่วนหัวของตาราง
            csvRows.push(`"รายการ","ปี 2567 (ปีปัจจุบัน)","","","","ปี 2568(ปีที่ขอ)","","","","เพิ่ม/ลด "`);
            csvRows.push(`"","เงินอุดหนุนจากรัฐ","เงินนอกงบประมาณ","เงินรายได้ ","รวม","เงินอุดหนุนจากรัฐ","เงินนอกงบประมาณ","เงินรายได้ ","รวม","จำนวน","ร้อยละ"`);

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
            link.download = 'รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้.csv';
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
            doc.text("รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้", 10, 500);

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
            doc.save('รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้.pdf');
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
            link.download = 'รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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