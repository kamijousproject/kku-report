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
                        <h4>รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/</br>ผลการใช้งบประมาณในภาพรวม</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">
                                รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</h4>
                                </div>
                                </br>
                                <?php
                                error_reporting(E_ALL);
                                ini_set('display_errors', 1);

                                include '../server/connectdb.php';

                                $db = new Database();
                                $conn = $db->connect();

                                // ดึงข้อมูล Faculty
                                $query_faculty = "SELECT DISTINCT
                                                    abp.Faculty, 
                                                    Faculty.Alias_Default
                                                FROM
                                                    budget_planning_allocated_annual_budget_plan abp
                                                LEFT JOIN Faculty 
                                                    ON abp.Faculty = Faculty.Faculty";
                                $stmt = $conn->prepare($query_faculty);
                                $stmt->execute();
                                $faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // รับค่าที่เลือกจากฟอร์ม
                                $selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';

                                // WHERE Clause แบบ Dynamic
                                $where_clause = "WHERE 1=1";
                                if ($selected_faculty !== '') {
                                    $where_clause .= " AND Faculty = :faculty";
                                }

                                // ฟังก์ชันดึงข้อมูล
                                function fetchBudgetData($conn, $where_clause, $selected_faculty)
                                {
                                    $query = "WITH
                                                t1 AS(
                                                    SELECT
                                                        b.*,
                                                        p.plan_name,
                                                        sp.sub_plan_name
                                                    FROM
                                                        budget_planning_annual_budget_plan b
                                                        LEFT JOIN plan p ON b.Plan = p.plan_id
                                                        LEFT JOIN sub_plan sp ON b.Sub_Plan = sp.sub_plan_id
                                                ),
                                                t2 AS (
                                                    SELECT
                                                        t.*,
                                                        skpi.UoM_for_Sub_plan_KPI,
                                                        skpi.KPI,
                                                        skpi.Sub_plan_KPI_Name,
                                                        skpi.Sub_plan_KPI_Target
                                                    FROM
                                                        t1 t
                                                        LEFT JOIN budget_planning_subplan_kpi skpi ON t.faculty = skpi.faculty COLLATE utf8mb4_general_ci
                                                        AND t.plan = skpi.plan
                                                        AND t.sub_plan = skpi.Sub_Plan
                                                    WHERE
                                                        skpi.KPI IS NOT NULL
                                                ),
                                                t3 AS (
                                                    SELECT
                                                        Faculty,
                                                        Budget_Management_Year,
                                                        fund,
                                                        plan,
                                                        plan_name,
                                                        sub_plan,
                                                        sub_plan_name,
                                                        project,
                                                        service,
                                                        reason,
                                                        kpi,
                                                        sub_plan_kpi_name AS kpi_name,
                                                        Sub_plan_KPI_Target AS kpi_target,
                                                        UoM_for_Sub_plan_KPI AS uom_kpi,
                                                        account,
                                                        NULL AS expense,
                                                        NULL AS expense_type,
                                                        kku_item_name,
                                                        CASE WHEN fund = 'FN02' THEN Total_Amount_Quantity ELSE 0 END AS total02,
                                                        CASE WHEN fund = 'FN06' THEN Total_Amount_Quantity ELSE 0 END AS total06,
                                                        CASE WHEN fund = 'FN08' THEN Total_Amount_Quantity ELSE 0 END AS total08,
                                                        '1.sub_plan_kpi' AS TYPE
                                                    FROM
                                                        t2
                                                ),
                                                t4 AS (
                                                    SELECT
                                                        t.*,
                                                        CASE WHEN t.fund = 'FN02' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total02,
                                                        CASE WHEN t.fund = 'FN06' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total06,
                                                        CASE WHEN t.fund = 'FN08' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total08
                                                    FROM
                                                        t3 t
                                                        LEFT JOIN budget_planning_allocated_annual_budget_plan b ON t.faculty = b.Faculty
                                                        AND t.fund = b.Fund
                                                        AND t.plan = b.Plan
                                                        AND t.sub_plan = b.Sub_Plan
                                                        AND t.project = b.Project
                                                        AND t.service = b.Service
                                                        AND t.account = b.Account
                                                ),
                                                t5 AS (
                                                    SELECT
                                                        t.*,
                                                        b.UoM_for_Proj_KPI,
                                                        b.KPI,
                                                        b.Proj_KPI_Name,
                                                        b.Proj_KPI_Target
                                                    FROM
                                                        t1 t
                                                        LEFT JOIN budget_planning_project_kpi b ON t.faculty = b.Faculty COLLATE UTF8MB4_GENERAL_CI
                                                        AND t.project = b.project
                                                    WHERE
                                                        b.KPI IS NOT NULL
                                                ),
                                                t6 AS (
                                                    SELECT
                                                        Faculty,
                                                        Budget_Management_Year,
                                                        fund,
                                                        plan,
                                                        plan_name,
                                                        sub_plan,
                                                        sub_plan_name,
                                                        project,
                                                        service,
                                                        reason,
                                                        kpi,
                                                        Proj_KPI_Name AS kpi_name,
                                                        Proj_KPI_Target AS kpi_target,
                                                        UoM_for_Proj_KPI AS uom_kpi,
                                                        account,
                                                        NULL AS expense,
                                                        NULL AS expense_type,
                                                        kku_item_name,
                                                        CASE WHEN fund = 'FN02' THEN Total_Amount_Quantity ELSE 0 END AS total02,
                                                        CASE WHEN fund = 'FN06' THEN Total_Amount_Quantity ELSE 0 END AS total06,
                                                        CASE WHEN fund = 'FN08' THEN Total_Amount_Quantity ELSE 0 END AS total08,
                                                        '2.project_kpi' AS TYPE
                                                    FROM
                                                        t5
                                                ),
                                                t7 AS (
                                                    SELECT
                                                        t.*,
                                                        CASE WHEN t.fund = 'FN02' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total02,
                                                        CASE WHEN t.fund = 'FN06' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total06,
                                                        CASE WHEN t.fund = 'FN08' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total08
                                                    FROM
                                                        t6 t
                                                        LEFT JOIN budget_planning_allocated_annual_budget_plan b ON t.faculty = b.Faculty
                                                        AND t.fund = b.Fund
                                                        AND t.plan = b.Plan
                                                        AND t.sub_plan = b.Sub_Plan
                                                        AND t.project = b.Project
                                                        AND t.service = b.Service
                                                        AND t.account = b.Account
                                                ),
                                                t8 AS (
                                                    SELECT
                                                        t.*,
                                                        a.alias_default,
                                                        a.type
                                                    FROM
                                                        t1 t
                                                        LEFT JOIN (
                                                            SELECT
                                                                *
                                                            FROM
                                                                account
                                                            WHERE
                                                                id >(
                                                                    SELECT
                                                                        id
                                                                    FROM
                                                                        account
                                                                    WHERE
                                                                        parent = 'Expenses'
                                                                )
                                                        ) a ON t.account = a.account
                                                    WHERE
                                                        a.type IS NOT NULL
                                                ),
                                                t9 AS (
                                                    SELECT
                                                        Faculty,
                                                        Budget_Management_Year,
                                                        fund,
                                                        plan,
                                                        plan_name,
                                                        sub_plan,
                                                        sub_plan_name,
                                                        project,
                                                        service,
                                                        reason,
                                                        NULL AS kpi,
                                                        NULL AS kpi_name,
                                                        NULL AS kpi_target,
                                                        NULL AS uom_kpi,
                                                        account,
                                                        alias_default AS expense,
                                                        TYPE AS expense_type,
                                                        kku_item_name,
                                                        CASE WHEN fund = 'FN02' THEN Total_Amount_Quantity ELSE 0 END AS total02,
                                                        CASE WHEN fund = 'FN06' THEN Total_Amount_Quantity ELSE 0 END AS total06,
                                                        CASE WHEN fund = 'FN08' THEN Total_Amount_Quantity ELSE 0 END AS total08,
                                                        '3.expense' AS TYPE
                                                    FROM
                                                        t8
                                                ),
                                                t10 AS (
                                                    SELECT
                                                        t.*,
                                                        CASE WHEN t.fund = 'FN02' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total02,
                                                        CASE WHEN t.fund = 'FN06' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total06,
                                                        CASE WHEN t.fund = 'FN08' THEN COALESCE(
                                                            Allocated_Total_Amount_Quantity,
                                                            0
                                                        ) ELSE 0 END AS allocated_total08
                                                    FROM
                                                        t9 t
                                                        LEFT JOIN budget_planning_allocated_annual_budget_plan b ON t.faculty = b.Faculty
                                                        AND t.fund = b.Fund
                                                        AND t.plan = b.Plan
                                                        AND t.sub_plan = b.Sub_Plan
                                                        AND t.project = b.Project
                                                        AND t.service = b.Service
                                                        AND t.account = b.Account
                                                ),
                                                t11 AS (
                                                    SELECT
                                                        *
                                                    FROM
                                                        t4
                                                    UNION ALL
                                                    SELECT
                                                        *
                                                    FROM
                                                        t7
                                                    UNION ALL
                                                    SELECT
                                                        *
                                                    FROM
                                                        t10
                                                ),
                                                t12 AS (
                                                    SELECT
                                                        t.*, p.project_name
                                                    FROM
                                                        t11 t
                                                        left JOIN project p ON t.Project = p.project_id
                                                )
                                            SELECT
                                                *
                                            FROM
                                                t12
                                            $where_clause
                                            ORDER BY
                                                Faculty,
                                                fund,
                                                plan,
                                                sub_plan,
                                                project";

                                    try {
                                        $stmt = $conn->prepare($query);

                                        // ผูกค่า Parameter ป้องกัน SQL Injection
                                        if ($selected_faculty !== '') {
                                            $stmt->bindParam(':faculty', $selected_faculty, PDO::PARAM_STR);
                                        }

                                        $stmt->execute();
                                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    } catch (PDOException $e) {
                                        echo "Error: " . $e->getMessage();
                                        return [];
                                    }
                                }

                                $resultsFN = fetchBudgetData($conn, $where_clause, $selected_faculty);
                                ?>

                                <form method="GET" class="d-flex align-items-center gap-2">
                                    <label for="faculty" class="me-2">เลือกส่วนงาน/หน่วยงาน:</label>
                                    <select name="faculty" id="faculty" class="form-control me-2">
                                        <option value="">เลือกส่วนงาน/หน่วยงาน ทั้งหมด</option>
                                        <?php foreach ($faculties as $faculty): ?>
                                            <option value="<?= $faculty['Faculty'] ?>" <?= ($selected_faculty == $faculty['Faculty']) ? 'selected' : '' ?>>
                                                <?= $faculty['Alias_Default'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                </form>

                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th rowspan="3" value="UOM">หน่วยนับของตัวชี้วัด (UOM)</th>
                                                <th colspan="5">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
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
                                        <?php
                                        // echo "<pre>";
                                        // print_r($resultsFN);
                                        // echo "</pre>";

                                        $groupedData = [];

                                        // จัดกลุ่มข้อมูลตาม Plan, Sub_Plan, Project และ Expense
                                        foreach ($resultsFN as $row) {
                                            $planKey = $row['Plan'];
                                            $subPlanKey = $row['Sub_Plan'];
                                            $projectKey = $row['Project'];
                                            $expenseKey = $row['expense'];

                                            if (!isset($groupedData[$planKey])) {
                                                $groupedData[$planKey] = ['plan_name' => $row['plan_name'], 'sub_plans' => [], 'expenses' => []];
                                            }
                                            if (!isset($groupedData[$planKey]['sub_plans'][$subPlanKey])) {
                                                $groupedData[$planKey]['sub_plans'][$subPlanKey] = ['sub_plan_name' => $row['sub_plan_name'], 'projects' => [], 'sub_plan_items' => []];
                                            }
                                            if (!empty($row['KPI']) && strpos($row['TYPE'], 'sub_plan_kpi') !== false) {
                                                if (!in_array($row['kpi_name'], $groupedData[$planKey]['sub_plans'][$subPlanKey]['sub_plan_items'])) {
                                                    $groupedData[$planKey]['sub_plans'][$subPlanKey]['sub_plan_items'][] = $row['kpi_name'];
                                                }
                                            }
                                            if (!isset($groupedData[$planKey]['sub_plans'][$subPlanKey]['projects'][$projectKey])) {
                                                $groupedData[$planKey]['sub_plans'][$subPlanKey]['projects'][$projectKey] = ['project_name' => $row['project_name'], 'project_items' => []];
                                            }
                                            if (!empty($row['KPI']) && strpos($row['TYPE'], 'project_kpi') !== false) {
                                                if (!in_array($row['kpi_name'], $groupedData[$planKey]['sub_plans'][$subPlanKey]['projects'][$projectKey]['project_items'])) {
                                                    $groupedData[$planKey]['sub_plans'][$subPlanKey]['projects'][$projectKey]['project_items'][] = $row['kpi_name'];
                                                }
                                            }
                                            if (!empty($expenseKey)) {
                                                if (!isset($groupedData[$planKey]['expenses'][$expenseKey])) {
                                                    $groupedData[$planKey]['expenses'][$expenseKey] = ['expense_type' => $row['expense_type'], 'kku_items' => []];
                                                }
                                                if (!empty($row['KKU_Item_Name']) && !in_array($row['KKU_Item_Name'], $groupedData[$planKey]['expenses'][$expenseKey]['kku_items'])) {
                                                    $groupedData[$planKey]['expenses'][$expenseKey]['kku_items'][] = $row['KKU_Item_Name'];
                                                }
                                            }
                                        }

                                        echo "<tbody>";
                                        $shownPlans = [];
                                        $shownSubPlans = [];
                                        $shownProjects = [];
                                        $shownExpenses = [];
                                        $shownExpenseTypes = [];

                                        foreach ($groupedData as $planKey => $planData) {
                                            if (!in_array($planData['plan_name'], $shownPlans)) {
                                                echo "<tr>
                                                        <td><strong>" . $planData['plan_name'] . "</strong></td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                    </tr>";
                                                $shownPlans[] = $planData['plan_name'];
                                            }

                                            foreach ($planData['sub_plans'] as $subPlanKey => $subPlanData) {
                                                $cleanSubPlanItem = str_replace("SP_", "", $subPlanKey);

                                                if (!in_array($subPlanData['sub_plan_name'], $shownSubPlans)) {
                                                    echo "<tr>
                                                            <td>" . str_repeat("&nbsp;", 15) . $cleanSubPlanItem . " : " . $subPlanData['sub_plan_name'] . "</td>
                                                            <td>-</td><td>-</td><td>-</td><td>-</td>
                                                            <td>-</td><td>-</td><td>-</td><td>-</td>
                                                            <td>-</td><td>-</td><td>-</td><td>-</td>
                                                            <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                        </tr>";

                                                    $shownSubPlans[] = $subPlanData['sub_plan_name'];
                                                }

                                                $uniqueSubPlanItems = array_unique($subPlanData['sub_plan_items']);
                                                foreach ($uniqueSubPlanItems as $subPlanItem) {
                                                    foreach ($resultsFN as $row) {
                                                        if ($row['kpi_name'] === $subPlanItem && $row['Sub_Plan'] === $subPlanKey) {
                                                            echo "<tr>
                                                                    <td>" . str_repeat("&nbsp;", 30) . $subPlanItem . "</td>
                                                                    <td>" . $row['uom_kpi'] . "</td>
                                                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                                    <td>" . $row['kpi_target'] . "</td>
                                                                    <td>" . $row['total06'] . "</td>
                                                                    <td>" . $row['allocated_total06'] . "</td>
                                                                    <td>" . $row['total08'] . "</td>
                                                                    <td>" . $row['allocated_total08'] . "</td>
                                                                    <td>" . $row['total02'] . "</td>
                                                                    <td>" . $row['allocated_total02'] . "</td>
                                                                    <td>" . ($row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08']) . "</td>
                                                                    <td>" . (($row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08']) - 0) . "</td>
                                                                    <td>100%</td>
                                                                    <td>" . $row['Reason'] . "</td>
                                                                </tr>";
                                                            break;
                                                        }
                                                    }
                                                }

                                                foreach ($subPlanData['projects'] as $projectKey => $projectData) {
                                                    if (!in_array($projectData['project_name'], $shownProjects)) {
                                                        echo "<tr>
                                                                <td>" . str_repeat("&nbsp;", 15) . $projectData['project_name'] . "</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                            </tr>";

                                                        $shownProjects[] = $projectData['project_name'];
                                                    }

                                                    $uniqueProjectItems = array_unique($projectData['project_items']);
                                                    foreach ($uniqueProjectItems as $projectItem) {
                                                        echo "<tr>
                                                                <td>" . str_repeat("&nbsp;", 30) . $projectItem . "</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                            </tr>";
                                                    }
                                                }
                                            }

                                            if (!empty($planData['expenses'])) {
                                                foreach ($planData['expenses'] as $expenseKey => $expenseData) {
                                                    $expenseAccount = "";
                                                    foreach ($resultsFN as $row) {
                                                        if ($row['expense'] === $expenseKey) {
                                                            $expenseAccount = $row['Account'] . " : ";
                                                            break;
                                                        }
                                                    }

                                                    if (!in_array($expenseData['expense_type'], $shownExpenseTypes)) {
                                                        echo "<tr>
                                                                <td>" . str_repeat("&nbsp;", 20) . $expenseData['expense_type'] . "</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                            </tr>";

                                                        $shownExpenseTypes[] = $expenseData['expense_type'];
                                                    }

                                                    if (!in_array($expenseKey, $shownExpenses)) {
                                                        echo "<tr>
                                                                <td>" . str_repeat("&nbsp;", 25) . $expenseAccount . $expenseKey . "</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                            </tr>";

                                                        $shownExpenses[] = $expenseKey;
                                                    }

                                                    $uniqueKkuItems = array_unique($expenseData['kku_items']);
                                                    foreach ($uniqueKkuItems as $kkuItem) {
                                                        echo "<tr>
                                                                <td>" . str_repeat("&nbsp;", 35) . $row['Account'] . " : " . $kkuItem . "</td>
                                                                <td>" . $row['uom_kpi'] . "</td>
                                                                <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                                <td>" . $row['kpi_target'] . "</td>
                                                                <td>" . $row['total06'] . "</td>
                                                                <td>" . $row['allocated_total06'] . "</td>
                                                                <td>" . $row['total08'] . "</td>
                                                                <td>" . $row['allocated_total08'] . "</td>
                                                                <td>" . $row['total02'] . "</td>
                                                                <td>" . $row['allocated_total02'] . "</td>
                                                                <td>" . ($row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08']) . "</td>
                                                                <td>" . (($row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08']) - 0) . "</td>
                                                                <td>100%</td>
                                                                <td>" . $row['Reason'] . "</td>
                                                            </tr>";
                                                    }
                                                }
                                            }
                                        }
                                        echo "</tbody>";
                                        ?>

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
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        function exportCSV() {
            const table = document.getElementById('reportTable');
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน",
                raw: true
            });
            XLSX.writeFile(wb, 'รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม.csv', {
                bookType: 'csv',
                type: 'array'
            });
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
            doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

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
            doc.save('รายงาน.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน"
            });
            XLSX.writeFile(wb, 'รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>