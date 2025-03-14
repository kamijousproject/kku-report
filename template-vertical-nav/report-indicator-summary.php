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
                        <h4>รายงานสรุปรายการตัวชี้วัดแผน/ผลของแผนงานย่อย</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปรายการตัวชี้วัดแผน/ผลของแผนงานย่อย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย</h4>
                                </div>
                                <div class="table-responsive">
                                    <?php
                                    error_reporting(E_ALL);
                                    ini_set('display_errors', 1);

                                    include '../server/connectdb.php';
                                    $database = new Database();
                                    $conn = $database->connect();

                                    // ดึงปีงบประมาณที่มีในระบบ
                                    $query_fsy = "SELECT DISTINCT Budget_Management_Year FROM budget_planning_annual_budget_plan ORDER BY Budget_Management_Year DESC";
                                    $stmt = $conn->prepare($query_fsy);
                                    $stmt->execute();
                                    $years = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // ดึงค่า Scenario
                                    $query_scenario = "SELECT DISTINCT Scenario FROM budget_planning_annual_budget_plan";
                                    $stmt = $conn->prepare($query_scenario);
                                    $stmt->execute();
                                    $scenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // ดึงค่า Faculty
                                    $query_faculty = "SELECT DISTINCT abp.Faculty, Faculty.Alias_Default 
                                    FROM budget_planning_allocated_annual_budget_plan abp
                                    LEFT JOIN Faculty ON abp.Faculty = Faculty.Faculty";
                                    $stmt = $conn->prepare($query_faculty);
                                    $stmt->execute();
                                    $faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // กำหนดค่าที่เลือกจาก Dropdown
                                    $selected_year = isset($_GET['Budget_Management_Year']) ? $_GET['Budget_Management_Year'] : (count($years) > 0 ? $years[0]['Budget_Management_Year'] : '');
                                    $compare_year = $selected_year ? $selected_year - 1 : '';
                                    $selected_scenario = isset($_GET['Scenario']) ? $_GET['Scenario'] : '';
                                    $selected_faculty = isset($_GET['Faculty']) ? $_GET['Faculty'] : '';

                                    // WHERE Clause
                                    $where_clause = "WHERE t4.Budget_Management_Year IN ('$selected_year', '$compare_year')";

                                    if ($selected_scenario !== '') {
                                        $where_clause .= " AND t4.Scenario = '$selected_scenario'";
                                    }

                                    if ($selected_faculty !== '') {
                                        $where_clause .= " AND t4.Faculty = '$selected_faculty'";
                                    }

                                    // ดึงข้อมูล
                                    $query = "
                                            WITH t1 AS (
                                                SELECT DISTINCT annual_bp.Faculty,
                                                                annual_bp.Plan,
                                                                annual_bp.Sub_Plan,
                                                                annual_bp.Scenario,
                                                                annual_bp.Budget_Management_Year,
                                                                NULL AS project,
                                                                sub_p_kpi.Sub_plan_KPI_Name,
                                                                sub_p_kpi.Sub_plan_KPI_Target,
                                                                sub_p_kpi.UoM_for_Sub_plan_KPI,
                                                                sub_p_progress.Prog_Q1 AS Sub_Prog_Q1,
                                                                sub_p_progress.Prog_Q2 AS Sub_Prog_Q2,
                                                                sub_p_progress.Prog_Q3 AS Sub_Prog_Q3,
                                                                sub_p_progress.Prog_Q4 AS Sub_Prog_Q4,
                                                                sub_p_kpi.KPI,
                                                                '1.sub_plan' AS type
                                                FROM budget_planning_annual_budget_plan AS annual_bp
                                                LEFT JOIN budget_planning_subplan_kpi AS sub_p_kpi
                                                    ON annual_bp.Plan = sub_p_kpi.Plan
                                                    AND annual_bp.Sub_Plan = sub_p_kpi.Sub_Plan
                                                    AND annual_bp.Faculty = sub_p_kpi.Faculty
                                                LEFT JOIN budget_planning_sub_plan_kpi_progress AS sub_p_progress 
                                                    ON sub_p_kpi.Plan = sub_p_progress.Plan
                                                    AND sub_p_kpi.Sub_Plan = sub_p_progress.Sub_Plan
                                                    AND sub_p_kpi.Faculty = sub_p_progress.Faculty
                                                    AND sub_p_kpi.KPI = sub_p_progress.KPI
                                                WHERE sub_p_kpi.KPI IS NOT NULL
                                            ),
                                            t2 AS (
                                                SELECT DISTINCT annual_bp.Faculty,
                                                                annual_bp.Plan,
                                                                annual_bp.Sub_Plan,
                                                                annual_bp.Scenario,
                                                                annual_bp.Budget_Management_Year,
                                                                annual_bp.Project,
                                                                p_kpi.Proj_KPI_Name,
                                                                p_kpi.Proj_KPI_Target,
                                                                p_kpi.UoM_for_Proj_KPI,
                                                                p_kpi_progress.Prog_Q1 AS Proj_Prog_Q1,
                                                                p_kpi_progress.Prog_Q2 AS Proj_Prog_Q2,
                                                                p_kpi_progress.Prog_Q3 AS Proj_Prog_Q3,
                                                                p_kpi_progress.Prog_Q4 AS Proj_Prog_Q4,
                                                                p_kpi.KPI,
                                                                '2.project' AS type
                                                FROM budget_planning_annual_budget_plan AS annual_bp
                                                LEFT JOIN budget_planning_project_kpi AS p_kpi 
                                                    ON annual_bp.Project = p_kpi.Project
                                                    AND annual_bp.Faculty = p_kpi.Faculty
                                                LEFT JOIN budget_planning_project_kpi_progress AS p_kpi_progress 
                                                    ON p_kpi.Project = p_kpi_progress.Project
                                                    AND p_kpi.Faculty = p_kpi_progress.Faculty
                                                    AND p_kpi.KPI = p_kpi_progress.KPI
                                                WHERE p_kpi.kpi IS NOT NULL
                                            ),
                                            t3 AS (
                                                SELECT * FROM t1
                                                UNION ALL
                                                SELECT * FROM t2
                                            ),
                                            t4 AS (
                                                SELECT t.*, p.plan_name, sp.sub_plan_name, pj.project_name
                                                FROM t3 t
                                                LEFT JOIN plan p ON t.Plan = p.plan_id
                                                LEFT JOIN sub_plan sp ON t.Sub_Plan = sp.sub_plan_id
                                                LEFT JOIN project pj ON t.Project = pj.project_id
                                            )
                                            SELECT DISTINCT * FROM t4 $where_clause
                                            ORDER BY Faculty, type, plan, sub_plan, kpi";

                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>

                                    <!-- Dropdown เลือกปีงบประมาณ -->
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

                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                    </form>

                                    <!-- แสดงตารางเปรียบเทียบ -->
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="7">ปี <?= $compare_year ?></th>
                                                <th colspan="7">ปี <?= $selected_year ?></th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2">หน่วยนับของตัวชี้วัด</th>
                                                <th rowspan="2">ค่าเป้าหมาย</th>
                                                <th colspan="4">ผลของตัวชี้วัด</th>
                                                <th rowspan="2">ผลรวมของตัวชี้วัด</th>
                                                <th rowspan="2">หน่วยนับของตัวชี้วัด</th>
                                                <th rowspan="2">ค่าเป้าหมาย</th>
                                                <th colspan="4">ผลของตัวชี้วัด</th>
                                                <th rowspan="2">ผลรวมของตัวชี้วัด</th>
                                            </tr>
                                            <tr>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $current_plan = [];
                                            $current_sub_plan = [];
                                            $grouped_kpi = [];
                                            $grouped_project_kpi = [];

                                            foreach ($data as $row):
                                                $year = $row['Budget_Management_Year']; // ปีของข้อมูลที่กำลังอ่าน

                                                // ตรวจสอบและแสดง Plan Name (แยกปี)
                                                if (!isset($current_plan[$year][$row['plan_name']]) && $row['plan_name'] != ''):
                                            ?>
                                                    <tr>
                                                        <td><?= $row['plan_name'] ?></td>
                                                        <td colspan="14">-</td>
                                                    </tr>
                                                <?php
                                                    $current_plan[$year][$row['plan_name']] = true;
                                                endif;

                                                // ตรวจสอบและแสดง Sub Plan (ไม่แยกปี)
                                                if (!isset($current_sub_plan[$row['Sub_Plan']]) && $row['Sub_Plan'] != ''):
                                                ?>
                                                    <tr>
                                                        <td><?= str_repeat("&nbsp;", 15) . str_replace("SP_", "", $row['Sub_Plan']) . ":" . $row['sub_plan_name'] ?></td>
                                                        <td colspan="14">-</td>
                                                    </tr>
                                                    <?php
                                                    $current_sub_plan[$row['Sub_Plan']] = true;
                                                endif;

                                                // จัดกลุ่ม KPI แยกตามชื่อ KPI
                                                if ($row['type'] == '1.sub_plan' && $row['Sub_plan_KPI_Name'] != '') {
                                                    $kpi_name = $row['Sub_plan_KPI_Name'];
                                                    if (!isset($grouped_kpi[$kpi_name])) {
                                                        $grouped_kpi[$kpi_name] = [$compare_year => null, $selected_year => null];
                                                    }
                                                    $grouped_kpi[$kpi_name][$year] = $row;
                                                }

                                                // จัดกลุ่ม Project KPI แยกตามชื่อ KPI
                                                if ($row['type'] == '2.project' && $row['Sub_plan_KPI_Name'] != '') {
                                                    $project_kpi_name = $row['Sub_plan_KPI_Name'];
                                                    if (!isset($grouped_project_kpi[$project_kpi_name])) {
                                                        $grouped_project_kpi[$project_kpi_name] = [$compare_year => null, $selected_year => null];
                                                    }
                                                    $grouped_project_kpi[$project_kpi_name][$year] = $row;
                                                }
                                            endforeach;

                                            // ฟังก์ชันแสดง KPI (Sub Plan KPI และ Project KPI แยกกลุ่มกัน)
                                            function displayKPI($grouped_data, $compare_year, $selected_year)
                                            {
                                                foreach ($grouped_data as $kpi_name => $years) {
                                                    if ($years[$compare_year] && $years[$selected_year]) {
                                                        // กรณีมีทั้งปีเปรียบเทียบ และปีที่เลือก -> แสดงในบรรทัดเดียวกัน
                                                    ?>
                                                        <tr>
                                                            <td><?= str_repeat("&nbsp;", 30) . $kpi_name ?></td>
                                                            <td><?= $years[$compare_year]['UoM_for_Sub_plan_KPI'] ?></td>
                                                            <td><?= $years[$compare_year]['Sub_plan_KPI_Target'] ?></td>
                                                            <td><?= $years[$compare_year]['Sub_Prog_Q1'] ?></td>
                                                            <td><?= $years[$compare_year]['Sub_Prog_Q2'] ?></td>
                                                            <td><?= $years[$compare_year]['Sub_Prog_Q3'] ?></td>
                                                            <td><?= $years[$compare_year]['Sub_Prog_Q4'] ?></td>
                                                            <td><?= $years[$compare_year]['Sub_Prog_Q1'] + $years[$compare_year]['Sub_Prog_Q2'] + $years[$compare_year]['Sub_Prog_Q3'] + $years[$compare_year]['Sub_Prog_Q4'] ?></td>
                                                            <td><?= $years[$selected_year]['UoM_for_Sub_plan_KPI'] ?></td>
                                                            <td><?= $years[$selected_year]['Sub_plan_KPI_Target'] ?></td>
                                                            <td><?= $years[$selected_year]['Sub_Prog_Q1'] ?></td>
                                                            <td><?= $years[$selected_year]['Sub_Prog_Q2'] ?></td>
                                                            <td><?= $years[$selected_year]['Sub_Prog_Q3'] ?></td>
                                                            <td><?= $years[$selected_year]['Sub_Prog_Q4'] ?></td>
                                                            <td><?= $years[$selected_year]['Sub_Prog_Q1'] + $years[$selected_year]['Sub_Prog_Q2'] + $years[$selected_year]['Sub_Prog_Q3'] + $years[$selected_year]['Sub_Prog_Q4'] ?></td>
                                                        </tr>
                                                        <?php
                                                    } else {
                                                        // กรณีมีเฉพาะปีเดียว -> แสดงแยกบรรทัด
                                                        foreach ([$compare_year, $selected_year] as $year) {
                                                            if ($years[$year]) {
                                                        ?>
                                                                <tr>
                                                                    <td><?= str_repeat("&nbsp;", 30) . $kpi_name ?></td>
                                                                    <?php if ($year == $compare_year): ?>
                                                                        <td><?= $years[$year]['UoM_for_Sub_plan_KPI'] ?></td>
                                                                        <td><?= $years[$year]['Sub_plan_KPI_Target'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q1'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q2'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q3'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q4'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q1'] + $years[$year]['Sub_Prog_Q2'] + $years[$year]['Sub_Prog_Q3'] + $years[$year]['Sub_Prog_Q4'] ?></td>
                                                                        <td colspan="7">-</td>
                                                                    <?php else: ?>
                                                                        <td colspan="7">-</td>
                                                                        <td><?= $years[$year]['UoM_for_Sub_plan_KPI'] ?></td>
                                                                        <td><?= $years[$year]['Sub_plan_KPI_Target'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q1'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q2'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q3'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q4'] ?></td>
                                                                        <td><?= $years[$year]['Sub_Prog_Q1'] + $years[$year]['Sub_Prog_Q2'] + $years[$year]['Sub_Prog_Q3'] + $years[$year]['Sub_Prog_Q4'] ?></td>
                                                                    <?php endif; ?>
                                                                </tr>
                                            <?php
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            // แสดง Sub Plan KPI
                                            displayKPI($grouped_kpi, $compare_year, $selected_year);

                                            // แสดง Project KPI
                                            displayKPI($grouped_project_kpi, $compare_year, $selected_year);
                                            ?>
                                        </tbody>

                                    </table>

                                    <?php $conn = null; ?>
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
            const table = document.getElementById('reportTable').cloneNode(true);
            const newRow = table.insertRow(0);
            const cell = newRow.insertCell(0);
            cell.colSpan = table.rows[1].cells.length;
            cell.style.textAlign = "center";
            cell.style.fontWeight = "bold";
            cell.innerText = "รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย";

            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน",
                raw: true
            });
            XLSX.writeFile(wb, 'รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.csv', {
                bookType: 'csv',
                type: 'array'
            });
        }

        function exportXLS() {
            const table = document.getElementById('reportTable').cloneNode(true);
            const newRow = table.insertRow(0);
            const cell = newRow.insertCell(0);
            cell.colSpan = table.rows[1].cells.length;
            cell.style.textAlign = "center";
            cell.style.fontWeight = "bold";
            cell.innerText = "รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย";

            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน"
            });
            XLSX.writeFile(wb, 'รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.xlsx');
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
            doc.save('รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.pdf');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>