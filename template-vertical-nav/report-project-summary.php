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
                        <h4>รายงานสรุปรายโครงการ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปรายโครงการ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายโครงการ</h4>
                                </div>
                                <br>
                                <div class="table-responsive">
                                    <?php
                                    error_reporting(E_ALL);
                                    ini_set('display_errors', 1);

                                    include '../server/connectdb.php';
                                    $database = new Database();
                                    $conn = $database->connect();

                                    $query_fsy = "SELECT DISTINCT Budget_Management_Year FROM budget_planning_annual_budget_plan ORDER BY Budget_Management_Year DESC";
                                    $stmt = $conn->prepare($query_fsy);
                                    $stmt->execute();
                                    $years = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // ดึงค่า Scenario
                                    $query_scenario = "SELECT DISTINCT Scenario FROM budget_planning_annual_budget_plan";
                                    $stmt = $conn->prepare($query_scenario);
                                    $stmt->execute();
                                    $scenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    $query_faculty = "SELECT DISTINCT abp.Faculty, Faculty.Alias_Default 
                                                    FROM budget_planning_allocated_annual_budget_plan abp
                                                    LEFT JOIN Faculty ON abp.Faculty = Faculty.Faculty";
                                    $stmt = $conn->prepare($query_faculty);
                                    $stmt->execute();
                                    $faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);



                                    $selected_scenario = isset($_GET['Scenario']) ? $_GET['Scenario'] : '';
                                    $selected_faculty = isset($_GET['Faculty']) ? $_GET['Faculty'] : '';
                                    $selected_year = isset($_GET['years']) ? $_GET['years'] : '';
                                    $select_fund = isset($_GET['fundSelect']) ? $_GET['fundSelect'] : '';

                                    // WHERE Clause
                                    $where_clause = "WHERE 1=1";
                                    if ($selected_scenario !== '') {
                                        $where_clause .= " AND annual_bp.Scenario = '$selected_scenario'";
                                    }

                                    if ($selected_faculty !== '') {
                                        $where_clause .= " AND annual_bp.Faculty = '$selected_faculty'";
                                    }

                                    if ($select_fund !== '') {
                                        $where_clause .= " AND annual_bp.Fund = '$select_fund'";
                                    }

                                    if ($selected_year !== '') {
                                        $where_clause .= " AND Budget_Management_Year = :years";
                                    }

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
                                                b_actual AS (
                                                    SELECT
                                                        DISTINCT Faculty,
                                                        fund,
                                                        plan,
                                                        subplan,
                                                        project,
                                                        account,
                                                        service,
                                                        fiscal_year
                                                    FROM
                                                        budget_planning_actual
                                                ),
                                                kpi AS (
                                                    SELECT
                                                        Project,
                                                        MAX(KKU_Strategic_Plan_LOV) AS KKU_Strategic_Plan_LOV
                                                    FROM
                                                        budget_planning_project_kpi
                                                    GROUP BY
                                                        Project
                                                ),
                                                pilar AS (
                                                    SELECT
                                                        pillar_id,
                                                        MAX(pillar_name) AS pillar_name
                                                    FROM
                                                        pilars2
                                                    GROUP BY
                                                        pillar_id
                                                )
                                            SELECT
                                                project.project_name,
                                                annual_bp.Project,
                                                b_actual.FISCAL_YEAR,
                                                annual_bp.Budget_Management_Year,
                                                annual_bp.Plan,
                                                annual_bp.Sub_Plan,
                                                annual_bp.Faculty,
                                                annual_bp.Total_Amount_Quantity,
                                                f.Alias_Default AS faculty_name,
                                                account.alias_default,
                                                plan.plan_name,
                                                sub_plan.sub_plan_name,
                                                account.parent,
                                                kpi.KKU_Strategic_Plan_LOV,
                                                pilar.pillar_name,
                                                sh.*
                                            FROM
                                                budget_planning_annual_budget_plan AS annual_bp
                                                LEFT JOIN shifted_hierarchy sh ON sh.current_acc = annual_bp.`Account`
                                                LEFT JOIN b_actual ON b_actual.PLAN = annual_bp.Plan
                                                AND annual_bp.faculty = b_actual.Faculty
                                                AND b_actual.SUBPLAN = REPLACE(annual_bp.Sub_Plan, 'SP_', '')
                                                AND b_actual.PROJECT = annual_bp.Project
                                                AND annual_bp.account = b_actual.account
                                                AND b_actual.fund = REPLACE(annual_bp.fund, 'FN', '')
                                                AND b_actual.service = REPLACE(annual_bp.service, 'SR_', '')
                                                LEFT JOIN account ON account.account = annual_bp.Account
                                                LEFT JOIN (
                                                    SELECT
                                                        *
                                                    FROM
                                                        Faculty
                                                    WHERE
                                                        parent LIKE 'FACULTY%'
                                                ) f ON f.Faculty = annual_bp.Faculty
                                                LEFT JOIN plan ON plan.plan_id = annual_bp.Plan
                                                LEFT JOIN sub_plan ON sub_plan.sub_plan_id = annual_bp.Sub_Plan
                                                LEFT JOIN project ON project.project_id = annual_bp.Project
                                                LEFT JOIN kpi ON kpi.Project = annual_bp.Project
                                                LEFT JOIN pilar ON pilar.pillar_id = REPLACE(
                                                    kpi.KKU_Strategic_Plan_LOV, '_',
                                                    ''
                                                )
                                    $where_clause";

                                    if ($selected_faculty !== '') {
                                        $stmt->bindParam(':faculty', $selected_faculty, PDO::PARAM_STR);
                                    }
                                    if ($selected_year !== '') {
                                        $stmt->bindParam(':years', $selected_year, PDO::PARAM_STR);
                                    }
                                    if ($selected_scenario !== '') {
                                        $stmt->bindParam(':Scenario', $selected_scenario, PDO::PARAM_STR);
                                    }
                                    if ($select_fund !== '') {
                                        $stmt->bindParam(':fund', $select_fund, PDO::PARAM_STR);
                                    }

                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    ?>
                                    <form method="GET" class="d-flex align-items-center gap-2">
                                        <label for="Budget_Management_Year" class="me-2">เลือกปีงบประมาณ:</label>
                                        <select name="Budget_Management_Year" id="Budget_Management_Year" class="form-control me-2">
                                            <?php foreach ($years as $year): ?>
                                                <option value="<?= $year['Budget_Management_Year'] ?>" <?= ($selected_year == $year['Budget_Management_Year']) ? 'selected' : '' ?>>
                                                    <?= $year['Budget_Management_Year'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <label for="Scenario" class="me-2">เลือกประเภทงบประมาณ:</label>
                                        <select name="Scenario" id="Scenario" class="form-control me-2">
                                            <option value="">เลือกทั้งหมด</option>
                                            <?php foreach ($scenarios as $scenario): ?>
                                                <option value="<?= $scenario['Scenario'] ?>" <?= ($selected_scenario == $scenario['Scenario']) ? 'selected' : '' ?>>
                                                    <?= $scenario['Scenario'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <label for="Faculty" class="me-2">เลือกส่วนงาน/หน่วยงาน:</label>
                                        <select name="Faculty" id="Faculty" class="form-control me-2">
                                            <option value="">เลือกทั้งหมด</option>
                                            <?php foreach ($faculties as $faculty): ?>
                                                <option value="<?= $faculty['Faculty'] ?>" <?= ($selected_faculty == $faculty['Faculty']) ? 'selected' : '' ?>>
                                                    <?= $faculty['Alias_Default'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <label for="fundSelect">แหล่งเงิน:</label>
                                        <select id="fundSelect" name="fundSelect" class="form-control me-2">
                                            <option value="FN02">FN02</option>
                                            <option value="FN06" selected>FN06</option>
                                        </select>

                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                    </form>
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr class="">
                                                <th rowspan="3">โครงการ/กิจกรรม</th>
                                                <th colspan="22">งบประมาณ</th>
                                                <th rowspan="3">รวมงบประมาณ</th>
                                            </tr>
                                            <tr class="">
                                                <th colspan="11">1. ค่าใช้จ่ายบุคลากร</th>
                                                <th colspan="6">2. ค่าใช้จ่ายดำเนินงาน</th>
                                                <th colspan="3">3. ค่าใช้จ่ายลงทุน</th>
                                                <th rowspan="2" value="">4. ค่าใช้จ่ายเงินอุดหนุนการดำเนินงาน</th>
                                                <th rowspan="2" value="5500000000">5. ค่าใช้จ่ายอื่น</th>
                                            </tr>
                                            <tr class="">
                                                <th value="5101010000">1.1 เงินเดือนข้าราชการและลูกจ้างประจำ</th>
                                                <th value="5101020000">1.2 ค่าจ้างพนักงานมหาวิทยาลัย</th>
                                                <th value="5101030000">1.3 ค่าจ้างลูกจ้างมหาวิทยาลัย</th>
                                                <th value="5101040000">1.4 เงินกองทุนสำรองเพื่อผลประโยชน์พนักงานและสวัสดิการผู้ปฏิบัติงานในมหาวิทยาลัยขอนแก่น</th>
                                                <th value="5101040100">เงินสมทบประกันสังคมส่วนของนายจ้าง</th>
                                                <th value="5101040200">เงินสมทบกองทุนสำรองเลี้ยงชีพของนายจ้าง</th>
                                                <th value="5101040300">เงินชดเชยกรณีเลิกจ้าง</th>
                                                <th value="5101040400">เงินสมทบกองทุนเงินทดแทน</th>
                                                <th value="5101040500">สมทบกองทุนบำเหน็จบำนาญ(กบข.)</th>
                                                <th value="5101040600">สมทบกองทุนสำรองเลี้ยงชีพ (กสจ.)</th>
                                                <th value="5101040700">สวัสดิการอื่น ๆ</th>
                                                <th value="5203010000">ค่าตอบแทน</th>
                                                <th value="5203020000">ค่าใช้สอย</th>
                                                <th value="5203030000">ค่าวัสดุ</th>
                                                <th value="5203040000">ค่าสาธารณูปโภค</th>
                                                <th value="5201000000">ค่าใช้จ่ายด้านการฝึกอบรม</th>
                                                <th value="5202000000">ค่าใช้จ่ายเดินทาง</th>
                                                <th value="1207000000">ค่าครุภัณฑ์</th>
                                                <th value="1206000000">ค่าที่ดินและสิ่งก่อสร้าง</th>
                                                <th value="1205000000">ค่าที่ดิน</th>
                                            </tr>
                                        </thead>
                                        <tbody>

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
            XLSX.writeFile(wb, 'รายงานการจัดสรรเงินรายงวด.csv', {
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
            doc.text("รายงานการจัดสรรเงินรายงวด", 10, 10);

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
            XLSX.writeFile(wb, 'รายงานการจัดสรรเงินรายงวด.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>