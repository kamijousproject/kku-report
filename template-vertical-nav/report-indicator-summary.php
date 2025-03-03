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
                                    <p>ส่วนงาน/หน่วยงาน: คณะบริหารธุรกิจ</p>
                                </div>
                                <div class="table-responsive">
                                    <?php
                                    // Include ไฟล์เชื่อมต่อฐานข้อมูล
                                    include '../server/connectdb.php';

                                    // สร้าง instance ของคลาส Database และเชื่อมต่อ
                                    $database = new Database();
                                    $conn = $database->connect();

                                    $query = "SELECT
                                                sub_p_kpi.Plan,
                                                sub_p_kpi.Sub_Plan,
                                                sub_p_kpi.Sub_plan_KPI_Name,
                                                sub_p_kpi.Sub_plan_KPI_Target,
                                                sub_p_kpi.UoM_for_Sub_plan_KPI,
                                                sub_p_progress.Prog_Q1 AS Sub_Prog_Q1,
                                                sub_p_progress.Prog_Q2 AS Sub_Prog_Q2,
                                                sub_p_progress.Prog_Q3 AS Sub_Prog_Q3,
                                                sub_p_progress.Prog_Q4 AS Sub_Prog_Q4,
                                                p_kpi.Proj_KPI_Name,
                                                p_kpi.Proj_KPI_Target,
                                                p_kpi.UoM_for_Proj_KPI,
                                                p_kpi_progress.Prog_Q1 AS Proj_Prog_Q1,
                                                p_kpi_progress.Prog_Q2 AS Proj_Prog_Q2,
                                                p_kpi_progress.Prog_Q3 AS Proj_Prog_Q3,
                                                p_kpi_progress.Prog_Q4 AS Proj_Prog_Q4,
                                                plan.plan_name,
                                                sub_plan.sub_plan_name,
                                                project.project_name
                                            FROM
                                                budget_planning_annual_budget_plan AS annual_bp
                                                
                                                LEFT JOIN budget_planning_subplan_kpi AS sub_p_kpi
                                                ON annual_bp.Plan = sub_p_kpi.Plan
                                                AND annual_bp.Sub_Plan = sub_p_kpi.Sub_Plan
                                                AND annual_bp.Faculty = sub_p_kpi.Faculty
                                                
                                                LEFT JOIN budget_planning_sub_plan_kpi_progress AS sub_p_progress 
                                                ON annual_bp.Plan = sub_p_progress.Plan
                                                AND annual_bp.Sub_Plan = sub_p_progress.Sub_Plan
                                                AND annual_bp.Faculty = sub_p_progress.Faculty
                                                
                                                LEFT JOIN budget_planning_project_kpi AS p_kpi 
                                                ON annual_bp.Project = p_kpi.Project
                                                AND annual_bp.Faculty = p_kpi.Faculty
                                                
                                                LEFT JOIN budget_planning_project_kpi_progress AS p_kpi_progress 
                                                ON annual_bp.Project = p_kpi_progress.Project
                                                AND annual_bp.Faculty = p_kpi_progress.Faculty
                                                
                                                LEFT JOIN plan ON annual_bp.Plan = plan.plan_id
                                                LEFT JOIN sub_plan ON annual_bp.Sub_Plan = sub_plan.sub_plan_id
                                                LEFT JOIN project ON annual_bp.Project = project.project_id";

                                    // เตรียมและ execute คำสั่ง SQL
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();

                                    // ดึงข้อมูล
                                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>

                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="7">ปี 2567 (ปีก่อน)</th>
                                                <th colspan="7">ปี 2568 (ปีปัจจุบัน)</th>
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
                                            $Sub_plan_KPI_Name = [];

                                            $project_name = [];
                                            $Proj_KPI_Name = [];
                                            $expense_totals = [];

                                            foreach ($data as $row):
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
                                                    </tr>
                                                <?php
                                                    array_push($current_plan, $row['plan_name']);
                                                endif;
                                                ?>
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
                                                    </tr>
                                                <?php
                                                    array_push($current_sub_plan, $row['Sub_Plan']);
                                                endif;
                                                ?>
                                                <?php if (!in_array($row['Sub_plan_KPI_Name'], $Sub_plan_KPI_Name)): ?>
                                                    <tr>
                                                        <td><?= $row['Sub_plan_KPI_Name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- -- -->
                                                        <td><?= $row['UoM_for_Sub_plan_KPI'] ?></td>
                                                        <td><?= $row['Sub_plan_KPI_Target'] ?></td>
                                                        <td><?= $row['Sub_Prog_Q1'] ?></td>
                                                        <td><?= $row['Sub_Prog_Q2'] ?></td>
                                                        <td><?= $row['Sub_Prog_Q3'] ?></td>
                                                        <td><?= $row['Sub_Prog_Q4'] ?></td>
                                                        <td><?= $row['Sub_Prog_Q1'] + $row['Sub_Prog_Q2'] + $row['Sub_Prog_Q3'] + $row['Sub_Prog_Q4'] ?></td>
                                                    </tr>
                                                <?php
                                                    array_push($Sub_plan_KPI_Name, $row['Sub_plan_KPI_Name']);
                                                endif;
                                                ?>
                                                <?php if (!in_array($row['project_name'], $project_name)): ?>
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
                                                    </tr>
                                                <?php
                                                    array_push($project_name, $row['project_name']);
                                                endif;
                                                ?>
                                                <?php if (!in_array($row['Proj_KPI_Name'], $Proj_KPI_Name)): ?>
                                                    <tr>
                                                        <td><?= $row['Proj_KPI_Name'] ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <!-- -- -->
                                                        <td><?= $row['UoM_for_Proj_KPI'] ?></td>
                                                        <td><?= $row['Proj_KPI_Target'] ?></td>
                                                        <td><?= $row['Proj_Prog_Q1'] ?></td>
                                                        <td><?= $row['Proj_Prog_Q2'] ?></td>
                                                        <td><?= $row['Proj_Prog_Q3'] ?></td>
                                                        <td><?= $row['Proj_Prog_Q4'] ?></td>
                                                        <td><?= $row['Proj_Prog_Q1'] + $row['Proj_Prog_Q2'] + $row['Proj_Prog_Q3'] + $row['Proj_Prog_Q4'] ?></td>
                                                    </tr>
                                                <?php
                                                    array_push($Proj_KPI_Name, $row['Proj_KPI_Name']);
                                                endif;
                                                ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <?php
                                    // ปิดการเชื่อมต่อฐานข้อมูล
                                    $conn = null;
                                    ?>

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
            XLSX.writeFile(wb, 'รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.csv', {
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
            doc.save('รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "รายงาน"
            });
            XLSX.writeFile(wb, 'รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย.xlsx');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>