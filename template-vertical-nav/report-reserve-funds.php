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

                                // Query ดึงข้อมูลจาก budget_planning_actual_2
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
                                                    // ดึงค่าจาก account และ format ใหม่
                                                    $accountParts = explode('-', $row['account']);
                                                    $formattedAccount = isset($accountParts[3], $accountParts[1]) ? "{$accountParts[3]}-{$accountParts[1]}" : $row['account'];

                                                    // ดึงค่า account number (ตัวเลขเท่านั้น) เช่น 5401080000
                                                    $accountNumber = $accountParts[3] ?? $row['account'];

                                                    // Query หาชื่อบัญชีจาก budget_account
                                                    $descQuery = "SELECT description FROM budget_account WHERE account = :account_number LIMIT 1";
                                                    $descStmt = $conn->prepare($descQuery);
                                                    $descStmt->bindParam(':account_number', $accountNumber, PDO::PARAM_STR);
                                                    $descStmt->execute();
                                                    $accountDescription = $descStmt->fetch(PDO::FETCH_ASSOC)['description'] ?? $row['account_description'];

                                                    $facultyDes = explode("-", $row['account_description']);
                                                    if (count($facultyDes) >= 2) {
                                                        $facultyDes = $facultyDes[1]; // ดึงค่าหลัง "-" ตัวแรก
                                                        $facultyDes = str_replace("\\", "", $facultyDes); // ลบเครื่องหมาย backslash
                                                    } else {
                                                        echo "ไม่พบข้อมูลที่ต้องการ";
                                                    }
                                                    ?>

                                                    <td><?= $formattedAccount ?></td>
                                                    <td><?= $accountDescription . '-' . $facultyDes ?></td>
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