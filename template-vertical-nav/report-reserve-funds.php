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
                        <h4>รายงานสรุปบัญชีทุนสำรองสะสม</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปบัญชีทุนสำรองสะสม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปบัญชีทุนสำรองสะสม</h4>
                                </div>

                                <?php
                                // Include ไฟล์เชื่อมต่อฐานข้อมูล
                                include '../server/connectdb.php';

                                // สร้าง instance ของคลาส Database และเชื่อมต่อ
                                $database = new Database();
                                $conn = $database->connect();

                                // กำหนดค่าจำนวนแถวต่อหน้า
                                $limit = 10;
                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                $offset = ($page - 1) * $limit;

                                // Query นับจำนวนข้อมูลทั้งหมด
                                $countQuery = "SELECT COUNT(*) as total FROM budget_planning_actual_2";
                                $countStmt = $conn->prepare($countQuery);
                                $countStmt->execute();
                                $totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                                $totalPages = ceil($totalRows / $limit);

                                // Query ดึงข้อมูลตามหน้าปัจจุบัน
                                $query = "SELECT 
                                            account, 
                                            account_description, 
                                            prior_periods_debit, 
                                            prior_periods_credit, 
                                            period_activity_debit, 
                                            period_activity_credit, 
                                            ending_balances_debit, 
                                            ending_balances_credit 
                                        FROM budget_planning_actual_2 
                                        LIMIT :limit OFFSET :offset";

                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                                $stmt->execute();
                                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>

                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>รหัสบัญชี</th>
                                                <th>ชื่อบัญชี</th>
                                                <th>รหัส GF</th>
                                                <th>ชื่อบัญชี GF</th>
                                                <th colspan="2">ยอดยกมา</th>
                                                <th colspan="2">ประจำงวด</th>
                                                <th colspan="2">ยอดยกไป</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>เดบิต</th>
                                                <th>เครดิต</th>
                                                <th>เดบิต</th>
                                                <th>เครดิต</th>
                                                <th>เดบิต</th>
                                                <th>เครดิต</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $row): ?>
                                                <tr>
                                                    <?php
                                                    $accountParts = explode('-', $row['account']);
                                                    $formattedAccount = isset($accountParts[3], $accountParts[1]) ? "{$accountParts[3]}-{$accountParts[1]}" : $row['account'];
                                                    ?>
                                                    <td><?= $formattedAccount ?></td>
                                                    <td>
                                                        <?php
                                                        // ลบเครื่องหมาย \ และช่องว่างพิเศษออกจากข้อความก่อน
                                                        $cleanedDescription = preg_replace('/\\\\/', '', $row['account_description']);

                                                        // ใช้ regex ดึงเฉพาะส่วนที่ต้องการ
                                                        if (preg_match('/(บัญชี[^-]+)-([^\\-]+)-([^\\-]+)/u', $cleanedDescription, $matches)) {
                                                            echo trim("{$matches[1]}-{$matches[2]}-{$matches[3]}");
                                                        } else {
                                                            echo trim($cleanedDescription); // ถ้าไม่ตรงเงื่อนไขให้แสดงค่าเดิม
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td><?= $row['prior_periods_debit'] ?></td>
                                                    <td><?= $row['prior_periods_credit'] ?></td>
                                                    <td><?= $row['period_activity_debit'] ?></td>
                                                    <td><?= $row['period_activity_credit'] ?></td>
                                                    <td><?= $row['ending_balances_debit'] ?></td>
                                                    <td><?= $row['ending_balances_credit'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <!-- ปุ่ม Pagination -->
                                    <nav>
                                        <ul class="pagination">
                                            <?php if ($page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=1">หน้าแรก</a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page - 1 ?>">ก่อนหน้า</a>
                                                </li>
                                            <?php endif; ?>

                                            <?php
                                            $visiblePages = 5; // จำนวนหน้าที่แสดงรอบๆ หน้าปัจจุบัน
                                            $startPage = max(1, $page - $visiblePages);
                                            $endPage = min($totalPages, $page + $visiblePages);

                                            if ($startPage > 1) {
                                                echo '<li class="page-item"><a class="page-link">...</a></li>';
                                            }

                                            for ($i = $startPage; $i <= $endPage; $i++):
                                            ?>
                                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($endPage < $totalPages): ?>
                                                <li class="page-item"><a class="page-link">...</a></li>
                                            <?php endif; ?>

                                            <?php if ($page < $totalPages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page + 1 ?>">ถัดไป</a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $totalPages ?>">หน้าสุดท้าย</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                                <!-- <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button> -->
                                <button onclick="window.location.href='../server/export_csv_29-7-10.php'" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <!-- <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLS</button> -->
                                <button onclick="window.location.href='../server/export_xls_29-7-10.php'" class="btn btn-success m-t-15">Export XLS</button>

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
            doc.save('รายงานสรุปบัญชีทุนสำรองสะสม.pdf');
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>