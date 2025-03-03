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
                                $selected_fund = isset($_GET['fund']) ? $_GET['fund'] : '';

                                // WHERE Clause แบบ Dynamic
                                $where_clause = "WHERE 1=1";
                                if ($selected_faculty !== '') {
                                    $where_clause .= " AND Faculty = :faculty";
                                }
                                if ($selected_fund !== '') {
                                    $where_clause .= " AND fund = :fund";
                                }

                                // ฟังก์ชันดึงข้อมูล
                                function fetchBudgetData($conn, $where_clause, $selected_faculty, $selected_fund)
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
                                                                        account = 'Expenses'
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
                                        if ($selected_fund !== '') {
                                            $stmt->bindParam(':fund', $selected_fund, PDO::PARAM_STR);
                                        }

                                        $stmt->execute();
                                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    } catch (PDOException $e) {
                                        echo "Error: " . $e->getMessage();
                                        return [];
                                    }
                                }

                                $resultsFN = fetchBudgetData($conn, $where_clause, $selected_faculty, $selected_fund);
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

                                    <label for="fund" class="me-2">เลือกแหล่งงบประมาณ:</label>
                                    <select name="fund" id="fund" class="form-control me-2">
                                        <option value="">ทุกแหล่งงบประมาณ</option>
                                        <option value="FN02" <?= ($selected_fund == "FN02") ? 'selected' : '' ?>>FN02</option>
                                        <option value="FN08" <?= ($selected_fund == "FN08") ? 'selected' : '' ?>>FN08</option>
                                        <option value="FN06" <?= ($selected_fund == "FN06") ? 'selected' : '' ?>>FN06</option>
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
                                        <tbody>
                                            <?php
                                            $current_plan = [];
                                            $current_sub_plan = [];
                                            $current_project = [];
                                            $KKU_Item_Name = [];
                                            $expense_totals = [];

                                            $current_expense = [];
                                            $current_expense_type = [];
                                            $current_kku_item_name = [];

                                            foreach ($resultsFN as $row):
                                            ?>
                                                <?php if (!in_array($row['plan_name'], $current_plan)): ?>
                                                    <tr>
                                                        <td><?= $row['Plan'] . ":" . $row['plan_name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                    <?php
                                                    array_push($current_plan, $row['plan_name']);
                                                    ?>
                                                <?php endif; ?>
                                                <?php if (!in_array($row['Sub_Plan'], $current_sub_plan)): ?>
                                                    <tr>
                                                        <td><?= $row['Sub_Plan'] . ":" . $row['sub_plan_name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                    </tr>
                                                    <?php
                                                    array_push($current_sub_plan, $row['Sub_Plan']);
                                                    ?>
                                                <?php endif; ?>
                                                <?php if ((!in_array($row['kpi_name'], $KKU_Item_Name)) && $row['TYPE'] == '1.sub_plan_kpi'): ?>
                                                    <tr>
                                                        <td><?= $row['kpi_name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- ปี 2568 -->
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- <td><?= $row['uom_kpi'] ?></td>
                                                        <td><?= $row['total06'] ?></td>
                                                        <td><?= $row['allocated_total06'] ?></td>
                                                        <td><?= $row['total08'] ?></td>
                                                        <td><?= $row['allocated_total08'] ?></td>
                                                        <td><?= $row['total02'] ?></td>
                                                        <td><?= $row['allocated_total02'] ?></td>
                                                        <td><?= $row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08'] ?></td>
                                                        <td><?= ($row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08']) - 0 ?></td>
                                                        <td>100%</td>
                                                        <td><?= $row['Reason'] ?></td> -->
                                                    </tr>
                                                    <?php
                                                    array_push($KKU_Item_Name, $row['kpi_name']);
                                                    ?>
                                                <?php endif; ?>
                                                <?php if (!in_array($row['Project'], $current_project)): ?>
                                                    <tr>
                                                        <td><?= $row['project_name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- ปี 2568 -->
                                                    </tr>
                                                    <?php
                                                    array_push($current_project, $row['Project']);
                                                    ?>
                                                <?php endif; ?>
                                                <?php if ((!in_array($row['kpi_name'], $KKU_Item_Name)) && $row['TYPE'] == '2.project_kpi'): ?>
                                                    <tr>
                                                        <td><?= $row['kpi_name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- ปี 2568 -->
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- <td><?= $row['uom_kpi'] ?></td>
                                                        <td><?= $row['total06'] ?></td>
                                                        <td><?= $row['allocated_total06'] ?></td>
                                                        <td><?= $row['total08'] ?></td>
                                                        <td><?= $row['allocated_total08'] ?></td>
                                                        <td><?= $row['total02'] ?></td>
                                                        <td><?= $row['allocated_total02'] ?></td>
                                                        <td><?= $row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08'] ?></td>
                                                        <td><?= ($row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08']) - 0 ?></td>
                                                        <td>100%</td>
                                                        <td><?= $row['Reason'] ?></td> -->
                                                    </tr>
                                                <?php
                                                    array_push($KKU_Item_Name, $row['kpi_name']);
                                                endif;
                                                ?>
                                                <?php if (!in_array($row['expense'], $current_expense)): ?>
                                                    <tr>
                                                        <td><?= $row['expense'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- ปี 2568 -->
                                                    </tr>
                                                    <?php
                                                    array_push($current_expense, $row['expense']);
                                                    ?>
                                                <?php endif; ?>
                                                <?php if (!in_array($row['expense_type'], $current_expense_type)): ?>
                                                    <tr>
                                                        <td><?= $row['expense_type'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- ปี 2568 -->
                                                    </tr>
                                                    <?php
                                                    array_push($current_expense_type, $row['expense_type']);
                                                    ?>
                                                <?php endif; ?>
                                                <?php if ((!in_array($row['KKU_Item_Name'], $current_kku_item_name))): ?>
                                                    <tr>
                                                        <td><?= $row['KKU_Item_Name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- ปี 2568 -->
                                                        <td><?= $row['uom_kpi'] ?></td>
                                                        <td><?= $row['total06'] ?></td>
                                                        <td><?= $row['allocated_total06'] ?></td>
                                                        <td><?= $row['total08'] ?></td>
                                                        <td><?= $row['allocated_total08'] ?></td>
                                                        <td><?= $row['total02'] ?></td>
                                                        <td><?= $row['allocated_total02'] ?></td>
                                                        <td><?= $row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08'] ?></td>
                                                        <td><?= ($row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08']) - 0 ?></td>
                                                        <td>100%</td>
                                                        <td><?= $row['Reason'] ?></td>
                                                    </tr>
                                                <?php
                                                    array_push($current_kku_item_name, $row['KKU_Item_Name']);
                                                endif;
                                                ?>
                                            <?php endforeach; ?>
                                        </tbody>
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