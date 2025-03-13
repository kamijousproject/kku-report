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

                                $query_year = "SELECT DISTINCT
                                                    x.Budget_Management_Year
                                                FROM
                                                    budget_planning_annual_budget_plan x";
                                $stmt = $conn->prepare($query_year);
                                $stmt->execute();
                                $year = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                $query_Scenario = "SELECT DISTINCT
                                                    x.Scenario
                                                FROM
                                                    budget_planning_annual_budget_plan x";
                                $stmt = $conn->prepare($query_Scenario);
                                $stmt->execute();
                                $Scenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // รับค่าที่เลือกจากฟอร์ม
                                $selected_faculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';

                                $selected_year = isset($_GET['years']) ? $_GET['years'] : '';

                                $selected_Scenario = isset($_GET['Scenario']) ? $_GET['Scenario'] : '';



                                // WHERE Clause แบบ Dynamic
                                $where_clause = "WHERE 1=1";
                                $where_clause_2 = "WHERE 1=1";

                                if ($selected_year !== '') {
                                    $where_clause .= " AND Budget_Management_Year = :years";
                                }
                                if ($selected_Scenario !== '') {
                                    $where_clause_2 .= " AND b.Scenario = :Scenario";
                                }
                                if ($selected_faculty !== '') {
                                    $where_clause .= " AND Faculty = :faculty";
                                }

                                

                                // ฟังก์ชันดึงข้อมูล
                                function fetchBudgetData($conn, $where_clause, $selected_faculty, $selected_year, $where_clause_2, $selected_Scenario)
                                {
                                    $query = "WITH
                                                RECURSIVE account_hierarchy AS (
                                                    -- Anchor member: Start with all accounts that have a parent
                                                    SELECT
                                                        a1.account,
                                                        a1.account AS acc_id,
                                                        a1.alias_default AS alias,
                                                        a1.parent,
                                                        1 AS LEVEL
                                                    FROM
                                                        account a1
                                                    WHERE
                                                        a1.parent IS NOT NULL
                                                    UNION ALL
                                                        -- Recursive member: Find parent accounts
                                                    SELECT
                                                        ah.account,
                                                        a2.account AS acc_id,
                                                        a2.alias_default AS alias,
                                                        a2.parent,
                                                        ah.level + 1 AS LEVEL
                                                    FROM
                                                        account_hierarchy ah
                                                        JOIN account a2 ON ah.parent = a2.account COLLATE UTF8MB4_GENERAL_CI
                                                    WHERE
                                                        a2.parent IS NOT NULL
                                                        AND ah.level < 6 -- Maximum 6 levels (increased from 5)
                                                        ),
                                                -- Get the maximum level for each account to determine total depth
                                                max_levels AS (
                                                    SELECT
                                                        account,
                                                        MAX(LEVEL) AS max_level
                                                    FROM
                                                        account_hierarchy
                                                    GROUP BY
                                                        account
                                                ),
                                                -- Create a pivot table with all levels for each account
                                                hierarchy_pivot AS (
                                                    SELECT
                                                        h.account,
                                                        m.max_level,
                                                        MAX(
                                                            CASE WHEN h.level = 1 THEN CONCAT(
                                                                h.acc_id,
                                                                ' : ',
                                                                REGEXP_REPLACE(
                                                                    h.alias, '^[0-9]+(.[0-9]+)*[. ]+',
                                                                    ''
                                                                )
                                                            ) END
                                                        ) AS level1_value,
                                                        MAX(
                                                            CASE WHEN h.level = 2 THEN CONCAT(
                                                                h.acc_id,
                                                                ' : ',
                                                                REGEXP_REPLACE(
                                                                    h.alias, '^[0-9]+(.[0-9]+)*[. ]+',
                                                                    ''
                                                                )
                                                            ) END
                                                        ) AS level2_value,
                                                        MAX(
                                                            CASE WHEN h.level = 3 THEN CONCAT(
                                                                h.acc_id,
                                                                ' : ',
                                                                REGEXP_REPLACE(
                                                                    h.alias, '^[0-9]+(.[0-9]+)*[. ]+',
                                                                    ''
                                                                )
                                                            ) END
                                                        ) AS level3_value,
                                                        MAX(
                                                            CASE WHEN h.level = 4 THEN CONCAT(
                                                                h.acc_id,
                                                                ' : ',
                                                                REGEXP_REPLACE(
                                                                    h.alias, '^[0-9]+(.[0-9]+)*[. ]+',
                                                                    ''
                                                                )
                                                            ) END
                                                        ) AS level4_value,
                                                        MAX(
                                                            CASE WHEN h.level = 5 THEN CONCAT(
                                                                h.acc_id,
                                                                ' : ',
                                                                REGEXP_REPLACE(
                                                                    h.alias, '^[0-9]+(.[0-9]+)*[. ]+',
                                                                    ''
                                                                )
                                                            ) END
                                                        ) AS level5_value,
                                                        MAX(
                                                            CASE WHEN h.level = 6 THEN CONCAT(
                                                                h.acc_id,
                                                                ' : ',
                                                                REGEXP_REPLACE(
                                                                    h.alias, '^[0-9]+(.[0-9]+)*[. ]+',
                                                                    ''
                                                                )
                                                            ) END
                                                        ) AS level6_value
                                                    FROM
                                                        account_hierarchy h
                                                        JOIN max_levels m ON h.account = m.account
                                                    GROUP BY
                                                        h.account,
                                                        m.max_level
                                                ),
                                                -- Shift the hierarchy to the left (compact it)
                                                shifted_hierarchy AS (
                                                    SELECT
                                                        account AS current_acc,
                                                        max_level AS TotalLevels,
                                                        CASE WHEN max_level = 1 THEN level1_value WHEN max_level = 2 THEN level2_value WHEN max_level = 3 THEN level3_value WHEN max_level = 4 THEN level4_value WHEN max_level = 5 THEN level5_value WHEN max_level = 6 THEN level6_value END AS level6,
                                                        CASE WHEN max_level = 1 THEN NULL WHEN max_level = 2 THEN level1_value WHEN max_level = 3 THEN level2_value WHEN max_level = 4 THEN level3_value WHEN max_level = 5 THEN level4_value WHEN max_level = 6 THEN level5_value END AS level5,
                                                        CASE WHEN max_level = 1 THEN NULL WHEN max_level = 2 THEN NULL WHEN max_level = 3 THEN level1_value WHEN max_level = 4 THEN level2_value WHEN max_level = 5 THEN level3_value WHEN max_level = 6 THEN level4_value END AS level4,
                                                        CASE WHEN max_level = 1 THEN NULL WHEN max_level = 2 THEN NULL WHEN max_level = 3 THEN NULL WHEN max_level = 4 THEN level1_value WHEN max_level = 5 THEN level2_value WHEN max_level = 6 THEN level3_value END AS level3,
                                                        CASE WHEN max_level = 1 THEN NULL WHEN max_level = 2 THEN NULL WHEN max_level = 3 THEN NULL WHEN max_level = 4 THEN NULL WHEN max_level = 5 THEN level1_value WHEN max_level = 6 THEN level2_value END AS level2,
                                                        CASE WHEN max_level = 1 THEN NULL WHEN max_level = 2 THEN NULL WHEN max_level = 3 THEN NULL WHEN max_level = 4 THEN NULL WHEN max_level = 5 THEN NULL WHEN max_level = 6 THEN level1_value END AS level1
                                                    FROM
                                                        hierarchy_pivot
                                                ),
                                                    t1 AS(
                                                        SELECT
                                                            b.*,
                                                            p.plan_name,
                                                            sp.sub_plan_name
                                                        FROM
                                                            budget_planning_annual_budget_plan b
                                                            LEFT JOIN plan p ON b.Plan = p.plan_id
                                                            LEFT JOIN sub_plan sp ON b.Sub_Plan = sp.sub_plan_id
                                                            $where_clause_2
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
                                                            t.*,
                                                            p.project_name
                                                        FROM
                                                            t11 t
                                                            LEFT JOIN project p ON t.Project = p.project_id
                                                    )
                                                SELECT
                                                    *
                                                FROM
                                                    t12 t
                                                    LEFT JOIN shifted_hierarchy h ON t.account = h.current_acc
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
                                        if ($selected_year !== '') {
                                            $stmt->bindParam(':years', $selected_year, PDO::PARAM_STR);
                                        }
                                        if ($selected_Scenario !== '') {
                                            $stmt->bindParam(':Scenario', $selected_Scenario, PDO::PARAM_STR);
                                        }
                                        $stmt->execute();
                                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    } catch (PDOException $e) {
                                        echo "Error: " . $e->getMessage();
                                        return [];
                                    }
                                }

                                $resultsFN = fetchBudgetData($conn, $where_clause, $selected_faculty, $selected_year, $where_clause_2, $selected_Scenario);
                                ?>

                                <form method="GET" class="d-flex align-items-center gap-2">
                                    <label for="years" class="me-2">เลือกปีงบประมาณ:</label>
                                    <select name="years" id="years" class="form-control me-2">
                                        <option value="">เลือกปีงบประมาณ ทั้งหมด</option>
                                        <?php foreach ($year as $all_year): ?>
                                            <option value="<?= $all_year['Budget_Management_Year'] ?>" <?= ($selected_year == $all_year['Budget_Management_Year']) ? 'selected' : '' ?>>
                                                <?= $all_year['Budget_Management_Year'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <label for="Scenario" class="me-2">เลือกประเภทงบประมาณ:</label>
                                    <select name="Scenario" id="Scenario" class="form-control me-2">
                                        <option value="">เลือกประเภทงบประมาณ ทั้งหมด</option>
                                        <?php foreach ($Scenarios as $all_Scenario): ?>
                                            <option value="<?= $all_Scenario['Scenario'] ?>" <?= ($selected_Scenario == $all_Scenario['Scenario']) ? 'selected' : '' ?>>
                                                <?= $all_Scenario['Scenario'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>


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
                                        $groupedData = [];

                                        foreach ($resultsFN as $row) {
                                            $planKey = $row['Plan'];
                                            $subPlanKey = $row['Sub_Plan'];
                                            $projectKey = $row['Project'];
                                            $level3Key = $row['level3']; // ใช้แทน expense
                                            $level5Key = $row['level5']; // ใช้แทน expense_type

                                            if (!isset($groupedData[$planKey])) {
                                                $groupedData[$planKey] = [
                                                    'plan_name' => $row['plan_name'],
                                                    'sub_plans' => [],
                                                    'expenses' => []
                                                ];
                                            }

                                            if (!isset($groupedData[$planKey]['sub_plans'][$subPlanKey])) {
                                                $groupedData[$planKey]['sub_plans'][$subPlanKey] = [
                                                    'sub_plan_name' => $row['sub_plan_name'],
                                                    'sub_plan_items' => [],
                                                    'projects' => []
                                                ];
                                            }

                                            if (!empty($row['KPI']) && strpos($row['TYPE'], 'sub_plan_kpi') !== false) {
                                                if (!in_array($row['kpi_name'], $groupedData[$planKey]['sub_plans'][$subPlanKey]['sub_plan_items'])) {
                                                    $groupedData[$planKey]['sub_plans'][$subPlanKey]['sub_plan_items'][] = $row['kpi_name'];
                                                }
                                            }

                                            if (!isset($groupedData[$planKey]['sub_plans'][$subPlanKey]['projects'][$projectKey])) {
                                                $groupedData[$planKey]['sub_plans'][$subPlanKey]['projects'][$projectKey] = [
                                                    'project_name' => $row['project_name'],
                                                    'project_items' => []
                                                ];
                                            }

                                            if (!empty($row['KPI']) && strpos($row['TYPE'], 'project_kpi') !== false) {
                                                if (!in_array($row['kpi_name'], $groupedData[$planKey]['sub_plans'][$subPlanKey]['projects'][$projectKey]['project_items'])) {
                                                    $groupedData[$planKey]['sub_plans'][$subPlanKey]['projects'][$projectKey]['project_items'][] = $row['kpi_name'];
                                                }
                                            }

                                            if (!empty($row['KKU_Item_Name'])) {
                                                if (!isset($groupedData[$planKey]['expenses'][$level5Key])) {
                                                    $groupedData[$planKey]['expenses'][$level5Key] = [
                                                        'level3_items' => []
                                                    ];
                                                }

                                                if (!isset($groupedData[$planKey]['expenses'][$level5Key]['level3_items'][$level3Key])) {
                                                    $groupedData[$planKey]['expenses'][$level5Key]['level3_items'][$level3Key] = [
                                                        'kku_items' => []
                                                    ];
                                                }

                                                if (!in_array($row['KKU_Item_Name'], $groupedData[$planKey]['expenses'][$level5Key]['level3_items'][$level3Key]['kku_items'])) {
                                                    $groupedData[$planKey]['expenses'][$level5Key]['level3_items'][$level3Key]['kku_items'][] = $row['KKU_Item_Name'];
                                                }
                                            }
                                        }

                                        echo "<tbody>";


                                        foreach ($groupedData as $planKey => $planData) {
                                            echo "<tr>
                                                    <td><strong>" . $planData['plan_name'] . "</strong></td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td>
                                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                  </tr>";

                                            foreach ($planData['sub_plans'] as $subPlanKey => $subPlanData) {
                                                echo "<tr>
                                                        <td>" . str_repeat("&nbsp;", 15) . $subPlanData['sub_plan_name'] . "</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                      </tr>";

                                                foreach ($subPlanData['sub_plan_items'] as $subPlanItem) {
                                                    $dataByYear = [];

                                                    foreach ($resultsFN as $row) {
                                                        if ($row['kpi_name'] === $subPlanItem && $row['Sub_Plan'] === $subPlanKey) {
                                                            $year = $row['Budget_Management_Year']; // แยกข้อมูลตามปี
                                                            $dataByYear[$year] = [
                                                                'uom' => $row['uom_kpi'],
                                                                'kpi_target' => $row['kpi_target'],
                                                                'total06' => $row['total06'],
                                                                'allocated_total06' => $row['allocated_total06'],
                                                                'total08' => $row['total08'],
                                                                'allocated_total08' => $row['allocated_total08'],
                                                                'total02' => $row['total02'],
                                                                'allocated_total02' => $row['allocated_total02'],
                                                                'sumfn' => $row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08'],
                                                                'Reason' => $row['Reason']
                                                            ];
                                                        }
                                                    }

                                                    // กำหนดค่าเริ่มต้นให้เป็น "-" ถ้าไม่มีข้อมูลในปีนั้น ๆ
                                                    $year_2567 = $dataByYear['2567'] ?? [
                                                        'uom' => '-',
                                                        'kpi_target' => '-',
                                                        'total06' => '-',
                                                        'allocated_total06' => '-',
                                                        'total08' => '-',
                                                        'allocated_total08' => '-',
                                                        'total02' => '-',
                                                        'allocated_total02' => '-',
                                                        'sumfn' => '-',
                                                        'Reason' => '-'
                                                    ];
                                                    $year_2568 = $dataByYear['2568'] ?? [
                                                        'uom' => '-',
                                                        'kpi_target' => '-',
                                                        'total06' => '-',
                                                        'allocated_total06' => '-',
                                                        'total08' => '-',
                                                        'allocated_total08' => '-',
                                                        'total02' => '-',
                                                        'allocated_total02' => '-',
                                                        'sumfn' => '-',
                                                        'Reason' => '-'
                                                    ];

                                                    echo "<tr>
                                                                <td>" . str_repeat("&nbsp;", 30) . $subPlanItem . "</td>
                                                                <td>" . $year_2567['uom'] . "</td>
                                                                <td>" . $year_2567['kpi_target'] . "</td>
                                                                <td>" . $year_2567['allocated_total06'] . "</td>
                                                                <td>" . $year_2567['allocated_total02'] . "</td>
                                                                <td>" . $year_2567['allocated_total08'] . "</td>
                                                                <td>" . $year_2567['sumfn'] . "</td>
                                                                <td>" . $year_2568['kpi_target'] . "</td>
                                                                <td>" . $year_2568['total06'] . "</td>
                                                                <td>" . $year_2568['allocated_total06'] . "</td>
                                                                <td>" . $year_2568['total08'] . "</td>
                                                                <td>" . $year_2568['allocated_total08'] . "</td>
                                                                <td>" . $year_2568['total02'] . "</td>
                                                                <td>" . $year_2568['allocated_total02'] . "</td>
                                                                <td>" . $year_2568['sumfn'] . "</td>
                                                                <td>" . (($year_2568['sumfn'] !== '-' && $year_2567['sumfn'] !== '-') ? ($year_2568['sumfn'] - $year_2567['sumfn']) : '-') . "</td>
                                                                <td>100%</td>
                                                                <td>" . ($year_2568['Reason'] !== '-' ? $year_2568['Reason'] : $year_2567['Reason']) . "</td>
                                                              </tr>";
                                                }
                                            }

                                            foreach ($planData['expenses'] as $level5Key => $level5Data) {
                                                echo "<tr>
                                                        <td>" . str_repeat("&nbsp;", 45) . $level5Key . "</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td>
                                                        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                      </tr>";

                                                foreach ($level5Data['level3_items'] as $level3Key => $level3Data) {
                                                    echo "<tr>
                                                            <td>" . str_repeat("&nbsp;", 60) . $level3Key . "</td>
                                                            <td>-</td><td>-</td><td>-</td><td>-</td>
                                                            <td>-</td><td>-</td><td>-</td><td>-</td>
                                                            <td>-</td><td>-</td><td>-</td><td>-</td>
                                                            <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                                          </tr>";

                                                    foreach ($level3Data['kku_items'] as $kkuItem) {
                                                        $dataByYear = [];

                                                        foreach ($resultsFN as $row) {
                                                            if ($row['KKU_Item_Name'] === $kkuItem && $row['level3'] === $level3Key) {
                                                                $year = $row['Budget_Management_Year']; // แยกข้อมูลตามปี
                                                                $dataByYear[$year] = [
                                                                    'uom' => $row['uom_kpi'],
                                                                    'kpi_target' => $row['kpi_target'],
                                                                    'total06' => $row['total06'],
                                                                    'allocated_total06' => $row['allocated_total06'],
                                                                    'total08' => $row['total08'],
                                                                    'allocated_total08' => $row['allocated_total08'],
                                                                    'total02' => $row['total02'],
                                                                    'allocated_total02' => $row['allocated_total02'],
                                                                    'sumfn' => $row['allocated_total06'] + $row['allocated_total02'] + $row['allocated_total08'],
                                                                    'Reason' => $row['Reason']
                                                                ];
                                                            }
                                                        }

                                                        // ถ้าไม่มีข้อมูลของปีนั้น ให้แสดง "-"
                                                        $year_2567 = $dataByYear['2567'] ?? [
                                                            'uom' => '-',
                                                            'kpi_target' => '-',
                                                            'total06' => '-',
                                                            'allocated_total06' => '-',
                                                            'total08' => '-',
                                                            'allocated_total08' => '-',
                                                            'total02' => '-',
                                                            'allocated_total02' => '-',
                                                            'sumfn' => '-',
                                                            'Reason' => '-'
                                                        ];
                                                        $year_2568 = $dataByYear['2568'] ?? [
                                                            'uom' => '-',
                                                            'kpi_target' => '-',
                                                            'total06' => '-',
                                                            'allocated_total06' => '-',
                                                            'total08' => '-',
                                                            'allocated_total08' => '-',
                                                            'total02' => '-',
                                                            'allocated_total02' => '-',
                                                            'sumfn' => '-',
                                                            'Reason' => '-'
                                                        ];

                                                        echo "<tr>
                                                                <td>" . str_repeat("&nbsp;", 75) . "- " . $kkuItem . "</td>
                                                                <td>" . $year_2567['uom'] . "</td>
                                                                <td>" . $year_2567['kpi_target'] . "</td>
                                                                <td>" . $year_2567['allocated_total06'] . "</td>
                                                                <td>" . $year_2567['allocated_total02'] . "</td>
                                                                <td>" . $year_2567['allocated_total08'] . "</td>
                                                                <td>" . $year_2567['sumfn'] . "</td>
                                                                <td>" . $year_2568['kpi_target'] . "</td>
                                                                <td>" . $year_2568['total06'] . "</td>
                                                                <td>" . $year_2568['allocated_total06'] . "</td>
                                                                <td>" . $year_2568['total08'] . "</td>
                                                                <td>" . $year_2568['allocated_total08'] . "</td>
                                                                <td>" . $year_2568['total02'] . "</td>
                                                                <td>" . $year_2568['allocated_total02'] . "</td>
                                                                <td>" . $year_2568['sumfn'] . "</td>
                                                                <td>" . (($year_2568['sumfn'] !== '-' && $year_2567['sumfn'] !== '-') ? ($year_2568['sumfn'] - $year_2567['sumfn']) : '-') . "</td>
                                                                <td>100%</td>
                                                                <td>" . ($year_2568['Reason'] !== '-' ? $year_2568['Reason'] : $year_2567['Reason']) . "</td>
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