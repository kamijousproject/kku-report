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
$scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
$faculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
function fetchBudgetData($conn, $budget_year1 = null, $scenario = null, $faculty = null)
{

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
    ),
    t1 AS (
        SELECT 
            bap.Faculty,
            fta.Alias_Default AS Default_Faculty,
            ft.Alias_Default,
            bap.Plan,
            p.plan_id,
            p.plan_name,
            bap.Sub_Plan,
            sp.sub_plan_id,
            sp.sub_plan_name,
            bap.Project,
            pj.project_id,
            pj.project_name,
            ac.`type`,
            ac.sub_type,
            bap.`Account`,
            bap.KKU_Item_Name,
            bap.Total_Amount_Quantity,
            SUM(CASE WHEN bap.Fund = 'FN02' THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_FN02,
            SUM(CASE WHEN bap.Fund = 'FN06' THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_FN06,
            SUM(CASE WHEN bap.Fund = 'FN08' THEN bap.Total_Amount_Quantity ELSE 0 END) AS Total_FN08,
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
            ) AS a3,
            COALESCE(
                CASE  
                    WHEN m.TotalLevels = 5 THEN m.CurrentAccount
                    WHEN m.TotalLevels = 4 THEN NULL
                    WHEN m.TotalLevels = 3 THEN NULL
                END,
                bap.Account -- หากผลลัพธ์เป็น NULL ให้ใช้ค่า bap.Account
            ) AS a4,
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
        FROM budget_planning_annual_budget_plan bap
        INNER JOIN Faculty ft 
            ON bap.Faculty = ft.Faculty 
            AND ft.parent LIKE 'Faculty%' 
        LEFT JOIN Faculty fta
            ON ft.Parent = fta.Faculty
        LEFT JOIN sub_plan sp 
            ON sp.sub_plan_id = bap.Sub_Plan
        LEFT JOIN project pj 
            ON pj.project_id = bap.Project
        LEFT JOIN `account` ac 
            ON ac.`account` = bap.`Account`
        LEFT JOIN plan p 
            ON p.plan_id = bap.Plan
        LEFT JOIN main m ON bap.Account = m.CurrentAccount
        WHERE ac.id > (SELECT MAX(id) FROM account WHERE parent = 'Expenses')
    ";
    if ($faculty) {
        $query .= " AND bap.Faculty = :faculty"; // กรองตาม Faculty ที่เลือก
    }
    // เพิ่มเงื่อนไขสำหรับ Scenario ถ้ามี
    if ($scenario) {
        $query .= " AND bap.Scenario = :scenario"; // กรองตาม Scenario ที่เลือก
    }

    if ($budget_year1 != null) {
        $query .= " AND bap.Budget_Management_Year = :budget_year1"; // กรองตาม Faculty ที่เลือก
    }
    // เพิ่มการจัดกลุ่มข้อมูล
    $query .= " GROUP BY 
        bap.Faculty, fta.Alias_Default, ft.Alias_Default, bap.Plan, p.plan_id, p.plan_name, 
        bap.Sub_Plan, sp.sub_plan_id, sp.sub_plan_name, bap.Project, pj.project_id, pj.project_name,
        ac.`type`, ac.sub_type, bap.`Account`, bap.KKU_Item_Name, bap.Total_Amount_Quantity,    
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
        fta.Alias_Default ASC,
        bap.Plan ASC,
        bap.Sub_Plan ASC,
        bap.Project ASC,
        ac.`type` ASC,
        ac.sub_type ASC,
        bap.`Account` ASC
    )
    SELECT * FROM t1";

    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($query);

    // ผูกค่า :scenario ถ้ามี
    if ($scenario) {
        $stmt->bindParam(':scenario', $scenario, PDO::PARAM_STR);
    }
    if ($faculty) {
        $stmt->bindParam(':faculty', $faculty, PDO::PARAM_STR);
    }

    if ($budget_year1) {
        $stmt->bindParam(':budget_year1', $budget_year1, PDO::PARAM_STR);
    }
    // รันคำสั่ง SQL
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$results = fetchBudgetData($conn, $budget_year1, $scenario, $faculty);
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
                        <h4>รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</h4>
                                </div>
                                <?php
                                $years = fetchYearsData($conn);  // ดึงข้อมูลปีจากฐานข้อมูล
                                $scenarios = fetchScenariosData($conn);
                                $faculties = fetchFacultyData($conn);
                                ?>

                                <form method="GET" action="" onsubmit="return validateForm()">

                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="year" class="label-year"
                                            style="margin-right: 10px;">เลือกปีงบประมาณ</label>
                                        <select name="year" id="year" class="form-control"
                                            style="width: 100%; height: 40px; font-size: 16px; margin-right: 10px;">
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
                                        <label for="scenario" class="label-scenario"
                                            style="margin-right: 10px;">เลือก
                                            ประเภทงบประมาณ</label>
                                        <select name="scenario" id="scenario" class="form-control"
                                            style="width: 100%; height: 40px; font-size: 16px; margin-right: 10px;">
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
                                            style="width: 100%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือก ทุกส่วนงาน</option>
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
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                                        </div>
                                        <!-- โหลด SweetAlert2 (ใส่ใน <head> หรือก่อนปิด </body>) -->
                                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                        <!-- ปุ่ม -->
                                        <button class="btn btn-primary" onclick="runCmd()" style="margin-bottom: 10px;">อัพเดทข้อมูล</button>

                                        <script>
                                            function runCmd() {
                                                // แสดง SweetAlert ขณะกำลังรัน .cmd
                                                Swal.fire({
                                                    title: 'กำลังอัปเดตข้อมูล',
                                                    text: 'กรุณารอสักครู่...',
                                                    allowOutsideClick: false,
                                                    didOpen: () => {
                                                        Swal.showLoading(); // แสดง loading spinner
                                                    }
                                                });

                                                // เรียก PHP เพื่อรัน .cmd
                                                fetch('/kku-report/server/automateEPM/budget_planning/run_cmd_budget_planning.php')
                                                    .then(response => response.text())
                                                    .then(result => {
                                                        // เมื่อทำงานเสร็จ ปิด loading แล้วแสดงผลลัพธ์
                                                        Swal.fire({
                                                            title: 'อัปเดตข้อมูลเสร็จสิ้น',
                                                            html: result, // ใช้ .html เพื่อแสดงผลเป็น <br>
                                                            icon: 'success'
                                                        });
                                                    })
                                                    .catch(error => {
                                                        Swal.fire({
                                                            title: 'เกิดข้อผิดพลาด',
                                                            text: 'ไม่สามารถอัปเดตข้อมูลได้',
                                                            icon: 'error'
                                                        });
                                                        console.error(error);
                                                    });
                                            }
                                        </script>
                                    </div>
                                </form>

                                <script>
                                    function validateForm(event) {
                                        event.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ

                                        var faculty = document.getElementById('faculty').value;
                                        var year = document.getElementById('year').value;
                                        var scenario = document.getElementById('scenario').value;

                                        var baseUrl = "http://202.28.118.192:8081/template-vertical-nav/report-budget-requests.php";
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
                                    // แสดงค่าของ budget_year ในคอนโซล
                                    console.log('Budget Year 1:', budgetYear1);
                                </script>
                                <br>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="4" style='text-align: left;'>
                                                    รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</th>
                                            </tr>
                                            <tr>
                                                <th>งบประมาณรายจ่าย</th>
                                                <th value="fn06">เงินอุดหนุนจากรัฐ</th>
                                                <th value="fn08">เงินนอกงบประมาณ</th>
                                                <th value="fn02">เงินรายได้</th>
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
                                            $scenario = isset($_GET['scenario']) ? $_GET['scenario'] : null;
                                            $faculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
                                            $budget_year1 = isset($_GET['year']) ? $_GET['year'] : null;
                                            $results = fetchBudgetData($conn, $budget_year1, $scenario, $faculty);

                                            // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                            if (isset($results) && is_array($results) && count($results) > 0) {

                                                $summary = [];
                                                foreach ($results as $row) {
                                                    $DefaultFaculty = $row['Default_Faculty'];
                                                    $faculty = $row['Faculty'];
                                                    $plan = $row['Plan'];
                                                    $subPlan = $row['Sub_Plan'];
                                                    $project = $row['project_name'];
                                                    $Name_a1 = $row['Name_a1'];
                                                    $Name_a2 = $row['Name_a2'];
                                                    $Name_a3 = $row['Name_a3'];
                                                    $Name_a4 = $row['Name_a4'];
                                                    // เก็บข้อมูลของ DefaultFaculty
                                                    if (!isset($summary[$DefaultFaculty])) {
                                                        $summary[$DefaultFaculty] = [
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'faculty' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ faculty
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty] = [
                                                            'FacultyName' => $row['Alias_Default'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'plan' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ plan
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan] = [
                                                            'PlanName' => $row['plan_name'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'sub_plan' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ sub plan
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan] = [
                                                            'SubPlanName' => $row['sub_plan_name'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'project' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ project
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project] = [
                                                            'PlanName' => $row['plan_name'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'Name_a1' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    $ItemName_a1 = (!empty($row['Name_a1']))
                                                        ? "" . htmlspecialchars($row['a1']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a1']))
                                                        : "" . htmlspecialchars($row['a1']) . "";
                                                    // เก็บข้อมูลของ Name_a1
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1] = [
                                                            'a1' => $row['a1'],
                                                            'name' => $ItemName_a1,
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'Name_a2' => [], // เก็บข้อมูลของ Sub_Plan
                                                        ];
                                                    }

                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a2
                                                    if (!empty($row['a2']) && !empty($row['Name_a2']) && $row['Name_a2'] != $row['KKU_Item_Name']) {
                                                        $ItemName_a2 = htmlspecialchars($row['a2']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']));
                                                    } elseif (empty($row['a2']) && !empty($row['Name_a2'])) {
                                                        $ItemName_a2 = "- " . htmlspecialchars(removeLeadingNumbers($row['Name_a2']));
                                                    } else {
                                                        $ItemName_a2 = "- " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    // เก็บข้อมูลของ Name_a2
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2] = [
                                                            'a2' => $row['a2'],
                                                            'name' => $ItemName_a2,
                                                            'test' => $row['Name_a2'],
                                                            'tes1' => $row['Name_a1'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'Name_a3' => [], // เก็บข้อมูลของ kku
                                                        ];
                                                    }

                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a3
                                                    if (!empty($row['a3']) && !empty($row['Name_a3']) && $row['Name_a3'] != $row['KKU_Item_Name']) {
                                                        $ItemName_a3 = htmlspecialchars($row['a3']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a3']));
                                                    } elseif (empty($row['a3']) && !empty($row['Name_a3'])) {
                                                        $ItemName_a3 = "- " . htmlspecialchars(removeLeadingNumbers($row['Name_a3']));
                                                    } else {
                                                        $ItemName_a3 = "- " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    // เก็บข้อมูลของ Name_a3
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3] = [
                                                            'a3' => $row['a3'],
                                                            'name' => $ItemName_a3,
                                                            'test' => $row['Name_a3'],
                                                            'tes1' => $row['Name_a2'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'Name_a4' => [], // เก็บข้อมูลของ kku
                                                        ];
                                                    }

                                                    // ตรวจสอบและกำหนดค่าของ $ItemName_a3
                                                    if (!empty($row['a4']) && !empty($row['Name_a4']) && $row['Name_a4'] != $row['KKU_Item_Name']) {
                                                        $ItemName_a4 = htmlspecialchars($row['a4']) . " : " . htmlspecialchars(removeLeadingNumbers($row['Name_a4']));
                                                    } elseif (empty($row['a4']) && !empty($row['Name_a4'])) {
                                                        $ItemName_a4 = "- " . htmlspecialchars(removeLeadingNumbers($row['Name_a4']));
                                                    } else {
                                                        $ItemName_a4 = "- " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']));
                                                    }
                                                    // เก็บข้อมูลของ Name_a4
                                                    if (!isset($summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4])) {
                                                        $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4] = [
                                                            'a4' => $row['a4'],
                                                            'name' => $ItemName_a4,
                                                            'test' => $row['Name_a4'],
                                                            'tes1' => $row['Name_a3'],
                                                            'Total_FN06' => 0,
                                                            'Total_FN02' => 0,
                                                            'Total_FN08' => 0,
                                                            'kku_items' => [], // เก็บข้อมูลของ kku
                                                        ];
                                                    }
                                                    // เก็บข้อมูลของ DefaultFaculty
                                                    $summary[$DefaultFaculty]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ faculty
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ plan
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ subPlan
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ project
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Total_FN08'] += $row['Total_FN08'];


                                                    // เก็บข้อมูลของ Name_a1
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ Name_a2
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ Name_a3
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Total_FN08'] += $row['Total_FN08'];

                                                    // เก็บข้อมูลของ Name_a4
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Total_FN06'] += $row['Total_FN06'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Total_FN02'] += $row['Total_FN02'];
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['Total_FN08'] += $row['Total_FN08'];


                                                    // เก็บข้อมูลของ KKU_Item_Name
                                                    $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                        ? "" . "- " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                        : "" . "";
                                                    $summary[$DefaultFaculty]['faculty'][$faculty]['plan'][$plan]['sub_plan'][$subPlan]['project'][$project]['Name_a1'][$Name_a1]['Name_a2'][$Name_a2]['Name_a3'][$Name_a3]['Name_a4'][$Name_a4]['kku_items'][] = [
                                                        'name' => $kkuItemName,
                                                        'test' => $row['KKU_Item_Name'],
                                                        'tes1' => $row['Name_a3'],
                                                        'Total_FN06' => $row['Total_FN06'],
                                                        'Total_FN02' => $row['Total_FN02'],
                                                        'Total_FN08' => $row['Total_FN08'],
                                                    ];
                                                    $rows = $summary;
                                                    // ตัวแปรสำหรับเก็บผลรวมทั้งหมด
                                                    $total_summary = [
                                                        'Total_FN06' => 0,
                                                        'Total_FN02' => 0,
                                                        'Total_FN08' => 0,
                                                    ];
                                                    // แสดงผลรวมทั้งหมด
                                                    //print_r($total_summary);
                                                    // Assuming this is inside a loop where $row is updated (e.g., from a database query)
                                                    foreach ($rows as $row) { // Replace $rows with your actual data source
                                                        // รวมผลรวมทั้งหมดโดยไม่สนใจ Faculty
                                                        $total_summary['Total_FN06'] += (float) ($row['Total_FN06'] ?? 0);
                                                        $total_summary['Total_FN02'] += (float) ($row['Total_FN02'] ?? 0);
                                                        $total_summary['Total_FN08'] += (float) ($row['Total_FN08'] ?? 0);
                                                    }
                                                }


                                                foreach ($summary as $DefaultFaculty => $data) {
                                                    echo "<tr>";
                                                    echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 0) . $DefaultFaculty . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_FN06']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_FN08']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_FN02']) . "</td>";
                                                    echo "</tr>";
                                                    foreach ($data['faculty'] as $faculty => $facultyData) {
                                                        $facultyName = str_replace(' - ', ' : ', $facultyData['FacultyName']);
                                                        echo "<tr>";
                                                        echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 8) . htmlspecialchars($facultyName) . "</td>";
                                                        echo "<td>" . formatNumber($facultyData['Total_FN06']) . "</td>";
                                                        echo "<td>" . formatNumber($facultyData['Total_FN08']) . "</td>";
                                                        echo "<td>" . formatNumber($facultyData['Total_FN02']) . "</td>";
                                                        echo "</tr>";
                                                        foreach ($facultyData['plan'] as $plan => $planData) {
                                                            echo "<tr>";
                                                            echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 16) . htmlspecialchars($planData['PlanName']) . "</td>";
                                                            echo "<td>" . formatNumber($planData['Total_FN06']) . "</td>";
                                                            echo "<td>" . formatNumber($planData['Total_FN08']) . "</td>";
                                                            echo "<td>" . formatNumber($planData['Total_FN02']) . "</td>";
                                                            echo "</tr>";
                                                            foreach ($planData['sub_plan'] as $subPlan => $subPlanData) {
                                                                $cleanedSubPlan = preg_replace('/^SP_/', '', $subPlan);
                                                                echo "<tr>";
                                                                echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 24) . $cleanedSubPlan . ' : ' . htmlspecialchars($subPlanData['SubPlanName']) . "</td>";
                                                                echo "<td>" . formatNumber($subPlanData['Total_FN06']) . "</td>";
                                                                echo "<td>" . formatNumber($subPlanData['Total_FN08']) . "</td>";
                                                                echo "<td>" . formatNumber($subPlanData['Total_FN02']) . "</td>";
                                                                echo "</tr>";
                                                                foreach ($subPlanData['project'] as $project => $projectData) {
                                                                    $projectFormatted = str_replace(":", " : ", $project);
                                                                    echo "<tr>";
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 32) . htmlspecialchars($projectFormatted) . "</td>";
                                                                    echo "<td>" . formatNumber($projectData['Total_FN06']) . "</td>";
                                                                    echo "<td>" . formatNumber($projectData['Total_FN08']) . "</td>";
                                                                    echo "<td>" . formatNumber($projectData['Total_FN02']) . "</td>";
                                                                    echo "</tr>";
                                                                    foreach ($projectData['Name_a1'] as $Name_a1 => $Name_a1Data) {

                                                                        echo "<tr>";
                                                                        echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 40) . $Name_a1Data['name'] . "<br></td>";
                                                                        echo "<td>" . formatNumber($Name_a1Data['Total_FN06']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a1Data['Total_FN08']) . "</td>";
                                                                        echo "<td>" . formatNumber($Name_a1Data['Total_FN02']) . "</td>";
                                                                        echo "</tr>";
                                                                        foreach ($Name_a1Data['Name_a2'] as $Name_a2 => $Name_a2Data) {
                                                                            if ($Name_a2Data['test'] == null || $Name_a2Data['test'] == '') {
                                                                                continue;
                                                                            }
                                                                            echo "<tr>";
                                                                            echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 48) . $Name_a2Data['name'] . "<br></td>";
                                                                            echo "<td>" . formatNumber($Name_a2Data['Total_FN06']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a2Data['Total_FN08']) . "</td>";
                                                                            echo "<td>" . formatNumber($Name_a2Data['Total_FN02']) . "</td>";
                                                                            echo "</tr>";
                                                                            foreach ($Name_a2Data['Name_a3'] as $Name_a3 => $Name_a3Data) {
                                                                                if ($Name_a3Data['test'] == null || $Name_a3Data['test'] == '' || $Name_a2Data['name'] == $Name_a3Data['name']) {
                                                                                    continue;
                                                                                }
                                                                                echo "<tr>";
                                                                                echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 56) . $Name_a3Data['name'] . "<br></td>";
                                                                                echo "<td>" . formatNumber($Name_a3Data['Total_FN06']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a3Data['Total_FN08']) . "</td>";
                                                                                echo "<td>" . formatNumber($Name_a3Data['Total_FN02']) . "</td>";
                                                                                echo "</tr>";
                                                                                foreach ($Name_a3Data['Name_a4'] as $Name_a4 => $Name_a4Data) {
                                                                                    if ($Name_a4Data['test'] == null || $Name_a4Data['test'] == '' || $Name_a3Data['name'] == $Name_a4Data['name']) {
                                                                                        continue;
                                                                                    }
                                                                                    echo "<tr>";
                                                                                    echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 64) . $Name_a4Data['name'] . "<br></td>";
                                                                                    echo "<td>" . formatNumber($Name_a4Data['Total_FN06']) . "</td>";
                                                                                    echo "<td>" . formatNumber($Name_a4Data['Total_FN08']) . "</td>";
                                                                                    echo "<td>" . formatNumber($Name_a4Data['Total_FN02']) . "</td>";
                                                                                    echo "</tr>";
                                                                                    foreach ($Name_a4Data['kku_items'] as $kkuItem) {
                                                                                        if ($kkuItem['test'] == null || $kkuItem['test'] == '' || $Name_a4Data['name'] == $kkuItem['name']) {
                                                                                            continue;
                                                                                        }
                                                                                        echo "<tr>";
                                                                                        echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 72) . $kkuItem['name'] . "<br></td>";
                                                                                        echo "<td>" . formatNumber($kkuItem['Total_FN06']) . "</td>";
                                                                                        echo "<td>" . formatNumber($kkuItem['Total_FN08']) . "</td>";
                                                                                        echo "<td>" . formatNumber($kkuItem['Total_FN02']) . "</td>";
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
                                        </tbody>
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

</body>

</html>
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
        link.download = 'รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ.csv';
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
        doc.text("รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ", 10, 500);

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
        doc.save('รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ.pdf');
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
        link.download = 'รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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