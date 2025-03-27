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

                                // กำหนดปีปัจจุบัน (ค.ศ.) และ 3 ปีย้อนหลัง
                                $currentYearAD = date("Y");
                                $years = [];
                                for ($i = 0; $i < 4; $i++) {
                                    $years[] = $currentYearAD - $i;
                                }

                                // รับค่าปีที่เลือกจาก URL (แปลง พ.ศ. → ค.ศ.)
                                $selectedYearBE = isset($_GET['year']) ? $_GET['year'] : ($currentYearAD + 543);
                                $selectedYearAD = $selectedYearBE - 543; // แปลง พ.ศ. → ค.ศ.

                                $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : ''; // ค่าที่เลือกจาก dropdown ส่วนงาน

                                // กำหนดค่าจำนวนแถวต่อหน้า
                                $limit = 10;
                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                $offset = ($page - 1) * $limit;

                                // Query นับจำนวนข้อมูลทั้งหมดตามปีและส่วนงาน
                                $countQuery = "SELECT COUNT(*) as total FROM budget_planning_actual_2 
                                    WHERE YEAR(timestamp) = :selectedYear";
                                if ($selectedFaculty !== '') {
                                    $countQuery .= " AND SUBSTRING_INDEX(SUBSTRING_INDEX(account, '-', 2), '-', -1) = :faculty";
                                }

                                $countStmt = $conn->prepare($countQuery);
                                $countStmt->bindParam(':selectedYear', $selectedYearAD, PDO::PARAM_INT);
                                if ($selectedFaculty !== '') {
                                    $countStmt->bindParam(':faculty', $selectedFaculty, PDO::PARAM_STR);
                                }
                                $countStmt->execute();
                                $totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                                $totalPages = ceil($totalRows / $limit);

                                // Query ดึงข้อมูลจาก budget_planning_actual_2 ตามปีที่เลือก และกรองส่วนงาน
                                $query = "SELECT 
                                            account, 
                                            account_description, 
                                            prior_periods_debit, 
                                            prior_periods_credit, 
                                            period_activity_debit, 
                                            period_activity_credit,
                                            ending_balances_debit, 
                                            ending_balances_credit,
                                            net_ending_balances_debit,
                                            net_ending_balances_credit
                                        FROM budget_planning_actual_2 
                                        WHERE YEAR(timestamp) = :selectedYear";
                                if ($selectedFaculty !== '') {
                                    $query .= " AND SUBSTRING_INDEX(SUBSTRING_INDEX(account, '-', 2), '-', -1) = :faculty";
                                }
                                $query .= " LIMIT :limit OFFSET :offset";

                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':selectedYear', $selectedYearAD, PDO::PARAM_INT);
                                if ($selectedFaculty !== '') {
                                    $stmt->bindParam(':faculty', $selectedFaculty, PDO::PARAM_STR);
                                }
                                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                                $stmt->execute();
                                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>

                                <!-- ส่วน Dropdown ปี (เป็น พ.ศ.) -->
                                <div class="d-flex align-items-center gap-2">
                                    <label for="budgetYearSelect">ปีงบประมาณ:</label>
                                    <select id="budgetYearSelect" class="form-control" onchange="updateFilters()">
                                        <?php foreach ($years as $yearAD): ?>
                                            <?php $yearBE = $yearAD + 543; // แปลง ค.ศ. → พ.ศ. 
                                            ?>
                                            <option value="<?= $yearBE ?>" <?= ($yearBE == $selectedYearBE) ? 'selected' : '' ?>><?= $yearBE ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- ส่วน Dropdown เลือกส่วนงาน -->
                                <div class="d-flex align-items-center gap-2">
                                    <label for="facultySelect">เลือกส่วนงาน:</label>
                                    <select id="facultySelect" class="form-control" onchange="updateFilters()">
                                        <option value="">เลือกส่วนงาน ทั้งหมด</option>
                                        <option value="01" <?= ($selectedFaculty == '01') ? 'selected' : '' ?>>01 : สำนักงานอธิการบดี</option>
                                        <option value="02" <?= ($selectedFaculty == '02') ? 'selected' : '' ?>>02 : คณะวิทยาศาสตร์</option>
                                        <option value="03" <?= ($selectedFaculty == '03') ? 'selected' : '' ?>>03 : คณะเกษตรศาสตร์</option>
                                        <option value="04" <?= ($selectedFaculty == '04') ? 'selected' : '' ?>>04 : คณะวิศวกรรมศาสตร์</option>
                                        <option value="05" <?= ($selectedFaculty == '05') ? 'selected' : '' ?>>05 : คณะศึกษาศาสตร์</option>
                                        <option value="06" <?= ($selectedFaculty == '06') ? 'selected' : '' ?>>06 : คณะพยาบาลศาสตร์</option>
                                        <option value="07" <?= ($selectedFaculty == '07') ? 'selected' : '' ?>>07 : คณะแพทยศาสตร์</option>
                                        <option value="08" <?= ($selectedFaculty == '08') ? 'selected' : '' ?>>08 : คณะมนุษยศาสตร์และสังคมศาสตร์</option>
                                        <option value="09" <?= ($selectedFaculty == '09') ? 'selected' : '' ?>>09 : คณะเทคนิคการแพทย์</option>
                                        <option value="10" <?= ($selectedFaculty == '10') ? 'selected' : '' ?>>10 : บัณฑิตวิทยาลัย</option>
                                        <option value="11" <?= ($selectedFaculty == '11') ? 'selected' : '' ?>>11 : คณะสาธารณสุขศาสตร์</option>
                                        <option value="12" <?= ($selectedFaculty == '12') ? 'selected' : '' ?>>12 : สำนักหอสมุด</option>
                                        <option value="13" <?= ($selectedFaculty == '13') ? 'selected' : '' ?>>13 : คณะทันตแพทยศาสตร์</option>
                                        <option value="14" <?= ($selectedFaculty == '14') ? 'selected' : '' ?>>14 : วิทยาลัยบัณฑิตศึกษาการจัดการ</option>
                                        <option value="15" <?= ($selectedFaculty == '15') ? 'selected' : '' ?>>15 : คณะเภสัชศาสตร์</option>
                                        <option value="16" <?= ($selectedFaculty == '16') ? 'selected' : '' ?>>16 : คณะเทคโนโลยี</option>
                                        <option value="17" <?= ($selectedFaculty == '17') ? 'selected' : '' ?>>17 : สำนักเทคโนโลยีดิจิทัล</option>
                                        <option value="18" <?= ($selectedFaculty == '18') ? 'selected' : '' ?>>18 : คณะสัตวแพทยศาสตร์</option>
                                        <option value="19" <?= ($selectedFaculty == '19') ? 'selected' : '' ?>>19 : คณะสถาปัตยกรรมศาสตร์</option>
                                        <option value="20" <?= ($selectedFaculty == '20') ? 'selected' : '' ?>>20 : สำนักบริการวิชาการ</option>
                                        <option value="21" <?= ($selectedFaculty == '21') ? 'selected' : '' ?>>21 : สำนักงานสภามหาวิทยาลัย</option>
                                        <option value="22" <?= ($selectedFaculty == '22') ? 'selected' : '' ?>>22 : คณะบริหารธุรกิจและการบัญชี</option>
                                        <option value="23" <?= ($selectedFaculty == '23') ? 'selected' : '' ?>>23 : สำนักบริหารและพัฒนาวิชาการ</option>
                                        <option value="24" <?= ($selectedFaculty == '24') ? 'selected' : '' ?>>24 : คณะศิลปกรรมศาสตร์</option>
                                        <option value="25" <?= ($selectedFaculty == '25') ? 'selected' : '' ?>>25 : วิทยาลัยการปกครองท้องถิ่น</option>
                                        <option value="26" <?= ($selectedFaculty == '26') ? 'selected' : '' ?>>26 : วิทยาลัยนานาชาติ</option>
                                        <option value="27" <?= ($selectedFaculty == '27') ? 'selected' : '' ?>>27 : คณะเศรษฐศาสตร์</option>
                                        <option value="28" <?= ($selectedFaculty == '28') ? 'selected' : '' ?>>28 : คณะสหวิทยาการ</option>
                                        <option value="29" <?= ($selectedFaculty == '29') ? 'selected' : '' ?>>29 : วิทยาลัยการคอมพิวเตอร์</option>
                                        <option value="30" <?= ($selectedFaculty == '30') ? 'selected' : '' ?>>30 : คณะนิติศาสตร์</option>
                                    </select>
                                </div>
                                <br>
                                <script>
                                    function updateFilters() {
                                        var year = document.getElementById("budgetYearSelect").value;
                                        var faculty = document.getElementById("facultySelect").value;
                                        var queryString = "?year=" + year;
                                        if (faculty !== "") {
                                            queryString += "&faculty=" + faculty;
                                        }
                                        window.location.href = queryString;
                                    }
                                </script>
                                <br>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>รหัสบัญชี</th>
                                                <th>ชื่อบัญชี</th>
                                                <th>ทุนสำรองสะสม</th>

                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
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
                                                    <td>
                                                        <?php
                                                        if ($row['net_ending_balances_debit'] == 0) {
                                                            echo '(' . $row['net_ending_balances_credit'] . ')';
                                                        } else {
                                                            echo $row['net_ending_balances_debit'];
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <!-- ปุ่ม Pagination -->
                                    <nav>
                                        <ul class="pagination">
                                            <?php if ($page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=1&year=<?= $selectedYearBE ?>">หน้าแรก</a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page - 1 ?>&year=<?= $selectedYearBE ?>">ก่อนหน้า</a>
                                                </li>
                                            <?php endif; ?>

                                            <?php
                                            $visiblePages = 5;
                                            $startPage = max(1, $page - $visiblePages);
                                            $endPage = min($totalPages, $page + $visiblePages);

                                            if ($startPage > 1) echo '<li class="page-item"><a class="page-link">...</a></li>';

                                            for ($i = $startPage; $i <= $endPage; $i++):
                                            ?>
                                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>&year=<?= $selectedYearBE ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($endPage < $totalPages): ?>
                                                <li class="page-item"><a class="page-link">...</a></li>
                                            <?php endif; ?>

                                            <?php if ($page < $totalPages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page + 1 ?>&year=<?= $selectedYearBE ?>">ถัดไป</a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $totalPages ?>&year=<?= $selectedYearBE ?>">หน้าสุดท้าย</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>

                                <script>
                                    function updateYear() {
                                        var year = document.getElementById("budgetYearSelect").value;
                                        window.location.href = "?year=" + year;
                                    }
                                </script>

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="thsarabunnew-normal.js"></script> <!-- โหลดไฟล์ฟอนต์ที่แปลงเป็นตัวแปรแล้ว -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- ✅ เพิ่ม SweetAlert2 -->
    <script>
        async function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape');

            // ✅ แสดง SweetAlert2 ขณะโหลดข้อมูล
            Swal.fire({
                title: "กำลังสร้างรายงาน...",
                text: "โปรดรอสักครู่ ระบบกำลังประมวลผล",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // แสดงไอคอนโหลด
                }
            });

            try {
                // ✅ ใช้ฟอนต์ภาษาไทย
                doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal);
                doc.addFont("THSarabun.ttf", "THSarabun", "normal");
                doc.setFont("THSarabun");
                doc.setFontSize(14);

                // ✅ เพิ่มชื่อรายงาน
                doc.text("รายงานสรุปบัญชีทุนสำรองสะสม", 140, 15, null, null, "center");

                // ✅ ดึงข้อมูลที่ตัดคำแล้วจาก `export_pdf_data.php`
                const response = await fetch('../server/export_pdf_data.php');
                const jsonData = await response.json();

                // ✅ หัวตาราง
                const tableColumn = [
                    "รหัสบัญชี", "ชื่อบัญชี", "รหัส GF", "ชื่อบัญชี GF",
                    "ยอดยกมา (เดบิต)", "ยอดยกมา (เครดิต)",
                    "ประจำงวด (เดบิต)", "ประจำงวด (เครดิต)",
                    "ยอดยกไป (เดบิต)", "ยอดยกไป (เครดิต)"
                ];

                // ✅ แปลงข้อมูลเป็นตาราง
                const tableRows = jsonData.map(row => [
                    row.account,
                    row.account_description,
                    "-", "-",
                    row.prior_periods_debit,
                    row.prior_periods_credit,
                    row.period_activity_debit,
                    row.period_activity_credit,
                    row.ending_balances_debit,
                    row.ending_balances_credit
                ]);

                // ✅ สร้างตาราง PDF
                doc.autoTable({
                    head: [tableColumn],
                    body: tableRows,
                    startY: 30,
                    styles: {
                        font: "THSarabun",
                        fontSize: 12,
                        cellPadding: 2,
                        lineColor: [0, 0, 0],
                        lineWidth: 0.5
                    },
                    headStyles: {
                        fillColor: [102, 153, 225],
                        textColor: [0, 0, 0],
                        fontSize: 12
                    },
                    bodyStyles: {
                        textColor: [0, 0, 0],
                        fontSize: 12
                    }
                });

                // ✅ ซ่อน SweetAlert2 เมื่อโหลดเสร็จ
                Swal.close();

                // ✅ บันทึกไฟล์ PDF
                doc.save('รายงานสรุปบัญชีทุนสำรองสะสม.pdf');

            } catch (error) {
                console.error("Error loading data:", error);

                // ✅ แสดงข้อความแจ้งเตือนเมื่อเกิดข้อผิดพลาด
                Swal.fire({
                    icon: "error",
                    title: "เกิดข้อผิดพลาด!",
                    text: "ไม่สามารถโหลดข้อมูลได้ โปรดลองอีกครั้ง",
                    confirmButtonText: "ตกลง"
                });
            }
        }
    </script>

    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>