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

                                <?php
                                // Include ไฟล์เชื่อมต่อฐานข้อมูล
                                include '../server/connectdb.php';

                                // สร้าง instance ของคลาส Database และเชื่อมต่อ
                                $database = new Database();
                                $conn = $database->connect();

                                // Query เพื่อดึงข้อมูลตามเงื่อนไขที่กำหนด
                                $query = "SELECT
                                            b_actual.FISCAL_YEAR,
                                            annual_bp.Budget_Management_Year,
                                            annual_bp.Plan,
                                            annual_bp.Sub_Plan,
                                            annual_bp.Faculty,
                                            annual_bp.Project,
                                            annual_bp.Total_Amount_Quantity,
                                            f.Alias_Default AS faculty_name,
                                            account.alias_default,
                                            plan.plan_name,
                                            sub_plan.sub_plan_name,
                                            project.project_name,
                                            account.parent
                                        FROM
                                            budget_planning_annual_budget_plan AS annual_bp
                                            LEFT JOIN (SELECT DISTINCT Faculty,fund,plan,subplan,project,account,service,fiscal_year from budget_planning_actual) b_actual 
                                            ON b_actual.PLAN = annual_bp.Plan
                                            AND annual_bp.faculty=b_actual.FACULTY
                                            AND b_actual.SUBPLAN = REPLACE(annual_bp.Sub_Plan, 'SP_', '')
                                            AND b_actual.PROJECT = annual_bp.Project
                                            AND annual_bp.account=b_actual.account
                                            AND b_actual.fund=REPLACE(annual_bp.fund, 'FN', '')
                                            AND b_actual.service=REPLACE(annual_bp.service, 'SR_', '')
                                            LEFT JOIN account ON account.account = annual_bp.Account
                                            LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'FACULTY%') f ON f.Faculty = annual_bp.Faculty
                                            LEFT JOIN plan ON plan.plan_id = annual_bp.Plan
                                            LEFT JOIN sub_plan ON sub_plan.sub_plan_id = annual_bp.Sub_Plan
                                            LEFT JOIN project ON project.project_id = annual_bp.Project
                                        WHERE
                                            annual_bp.Scenario = 'Annual Budget Plan'
                                            AND annual_bp.Fund = 'FN06'";

                                // เตรียมและ execute คำสั่ง SQL
                                $stmt = $conn->prepare($query);
                                $stmt->execute();

                                // ดึงข้อมูล
                                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // จัดกลุ่มข้อมูลตามโครงการและประเภทค่าใช้จ่าย
                                $reportData = [];
                                foreach ($data as $row) {
                                    $project = $row['project_name'];
                                    $parent = $row['parent'];
                                    $amount = $row['Total_Amount_Quantity'];

                                    if (!isset($reportData[$project])) {
                                        $reportData[$project] = array_fill_keys([
                                            '5101010000',
                                            '5101020000',
                                            '5101030000',
                                            '5101040000',
                                            '5101040100',
                                            '5101040200',
                                            '5101040300',
                                            '5101040400',
                                            '5101040500',
                                            '5101040600',
                                            '5101040700',
                                            '5203010000',
                                            '5203020000',
                                            '5203030000',
                                            '5203040000',
                                            '5201000000',
                                            '5202000000',
                                            '12070........',
                                            '12060........',
                                            '1205000000',
                                            '5401000000',
                                            '5500000000'
                                        ], 0);
                                    }

                                    $reportData[$project][$parent] = $amount;
                                }
                                ?>
                                <div class="info-section">
                                    <p>ปีบริหารงบประมาณ: .......................</p>
                                    <p>ปีบริหารงบประมาณ: .......................</p>
                                    <p>ประเภทงบประมาณ: .......................</p>
                                    <p>แหล่งเงิน: .......................</p>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">โครงการ/กิจกรรม</th>
                                                <th colspan="22">งบประมาณ</th>
                                                <th rowspan="3">รวมงบประมาณ</th>
                                            </tr>
                                            <tr>
                                                <th colspan="11">1. ค่าใช้จ่ายบุคลากร</th>
                                                <th colspan="6">2. ค่าใช้จ่ายดำเนินงาน</th>
                                                <th colspan="3">3. ค่าใช้จ่ายลงทุน</th>
                                                <th rowspan="2">4. ค่าใช้จ่ายเงินอุดหนุนการดำเนินงาน</th>
                                                <th rowspan="2">5. ค่าใช้จ่ายอื่น</th>
                                            </tr>
                                            <tr>
                                                <th>1.1 เงินเดือนข้าราชการและลูกจ้างประจำ</th>
                                                <th>1.2 ค่าจ้างพนักงานมหาวิทยาลัย</th>
                                                <th>1.3 ค่าจ้างลูกจ้างมหาวิทยาลัย</th>
                                                <th>1.4 เงินกองทุนสำรองเพื่อผลประโยชน์พนักงานและสวัสดิการผู้ปฏิบัติงานในมหาวิทยาลัยขอนแก่น</th>
                                                <th>เงินสมทบประกันสังคมส่วนของนายจ้าง</th>
                                                <th>เงินสมทบกองทุนสำรองเลี้ยงชีพของนายจ้าง</th>
                                                <th>เงินชดเชยกรณีเลิกจ้าง</th>
                                                <th>เงินสมทบกองทุนเงินทดแทน</th>
                                                <th>สมทบกองทุนบำเหน็จบำนาญ(กบข.)</th>
                                                <th>สมทบกองทุนสำรองเลี้ยงชีพ (กสจ.)</th>
                                                <th>สวัสดิการอื่น ๆ</th>
                                                <th>ค่าตอบแทน</th>
                                                <th>ค่าใช้สอย</th>
                                                <th>ค่าวัสดุ</th>
                                                <th>ค่าสาธารณูปโภค</th>
                                                <th>ค่าใช้จ่ายด้านการฝึกอบรม</th>
                                                <th>ค่าใช้จ่ายเดินทาง</th>
                                                <th>ค่าครุภัณฑ์</th>
                                                <th>ค่าที่ดินและสิ่งก่อสร้าง</th>
                                                <th>ค่าที่ดิน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reportData as $project => $values): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($project) ?></td>
                                                    <td><?= number_format($values['5101010000']) ?></td>
                                                    <td><?= number_format($values['5101020000']) ?></td>
                                                    <td><?= number_format($values['5101030000']) ?></td>
                                                    <td><?= number_format($values['5101040000']) ?></td>
                                                    <td><?= number_format($values['5101040100']) ?></td>
                                                    <td><?= number_format($values['5101040200']) ?></td>
                                                    <td><?= number_format($values['5101040300']) ?></td>
                                                    <td><?= number_format($values['5101040400']) ?></td>
                                                    <td><?= number_format($values['5101040500']) ?></td>
                                                    <td><?= number_format($values['5101040600']) ?></td>
                                                    <td><?= number_format($values['5101040700']) ?></td>
                                                    <td><?= number_format($values['5203010000']) ?></td>
                                                    <td><?= number_format($values['5203020000']) ?></td>
                                                    <td><?= number_format($values['5203030000']) ?></td>
                                                    <td><?= number_format($values['5203040000']) ?></td>
                                                    <td><?= number_format($values['5201000000']) ?></td>
                                                    <td><?= number_format($values['5202000000']) ?></td>
                                                    <td><?= number_format($values['12070........']) ?></td>
                                                    <td><?= number_format($values['12060........']) ?></td>
                                                    <td><?= number_format($values['1205000000']) ?></td>
                                                    <td><?= number_format($values['5401000000']) ?></td>
                                                    <td><?= number_format($values['5500000000']) ?></td>
                                                    <td><?= number_format(array_sum($values)) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
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
    <script>
        function exportCSV() {
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells.join(","));
            }
            const csvContent = "\uFEFF" + rows.join("\n"); // Add BOM
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
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
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells);
            }
            let xlsContent = "<table>";
            rows.forEach(row => {
                xlsContent += "<tr>" + row.map(cell => `<td>${cell}</td>`).join('') + "</tr>";
            });
            xlsContent += "</table>";

            const blob = new Blob([xlsContent], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.xls');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>