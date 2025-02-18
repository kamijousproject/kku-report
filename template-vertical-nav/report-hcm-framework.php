<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th,
    #reportTable td {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: middle;
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

    .container-custom {
        max-width: 1200px;
        /* กำหนดค่าความกว้างสูงสุด */
        width: 120%;
        /* ใช้ 90% ของหน้าจอเพื่อให้ขนาดพอดี */
        margin: 0 auto;
        /* จัดให้อยู่ตรงกลาง */
    }

    @media (max-width: 768px) .container-custom {
        width: 95%;
        /* ขยายให้เต็มที่ขึ้นเมื่อเป็นหน้าจอเล็ก */
    }

    table {
        font-size: 12px;
        /* ลดขนาดตัวอักษรของตารางในหน้าจอเล็ก */
    }
</style>

<?php
include('../component/header.php');
include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();

// ฟังก์ชันดึงข้อมูลพร้อมการแบ่งหน้า
function fetchData($conn, $start, $limit)
{
    $query = "SELECT 
    wha.WORKERS_NAME_SURNAME,
    wha.EMPLOYEE_ID,
    wha.POSITION_NUMBER,
    wha.NATIONAL_ID,
    wha.RATE_STATUS,
    wha.APPOINTMENT_DATE,
    wha.RETIREMENT_DATE,
    wha.`POSITION`,
    wha.all_position_types,
    wha.LOCATION_CODE,
    wha.EMPLOYEE_QUALIFICATIONS,
    wha.PERSONNEL_TYPE,
    wha.FUND_FT,
    wha.MANAGEMENT_POSITION_NAME,
    wha.POSITION_STATUS,
    wha.POSITION_APPOINTMENT_DATE,
    wha.END_OF_TERM_DATE,
    wha.APPOINTMENT_ORDER_DOCUMENT,
    wha.POSITION_NAME_ACADEMIC_SUPPORT_MAPPED_TO_POSITION_NAME,
    wha.DATE_OF_APPOINTMENT_POSITION,
    wha.LETTER_OF_APPOINTMENT_ORDER,
    wha.POSITION_NAME_PENDING_PROMOTION_HIGHER_LEVEL,
    wha.DATE_OF_PROMOTION_REQUEST_HIGHER_LEVEL,
    wha.SALARY_RATE,
    wha.GOVT_FUND,
    CONCAT(wha.DIVISION_REVENUE, ' ', wha.OOP_CENTRAL_REVENUE) AS Revenue_Disbursement,
    wha.PROVIDENT_FUND,
    wha.SOCIAL_SECURITY_FUND,
    wha.WORKERS_COMPENSATION_FUND,
    wha.SEVERANCE_FUND,
    wha.GOVERNMENT_PENSION_FUND_GPF,
    wha.GOVERNMENT_SERVICE_INSURANCE_FUND_GSIF,
    wha.HOUSING_ALLOWANCE,
    wha.POSITION_CAR_ALLOWANCE,
    wha.OTHER_BENEFITS,
    wha.ACADEMIC_POSITION_ALLOWANCE,
    wha.POSITION_COMPENSATION_MNGT_ACADEMIC,
    wha.LEVEL_8_COMPENSATION,
    wha.FULL_SALARY_COMPENSATION,
    wha.EXECUTIVE_ALLOWANCE,
    wha.EXECUTIVE_COMPENSATION,
    wha.DOWN_PAYMENT_ACCORDING_TO_CABINET_RESOLUTION,
    wha.PTK_COMPENSATION,
    wha.PTS_COMPENSATION,
    wha.OTHER_SPECIAL_COMPENSATION
    FROM workforce_hcm_actual wha
    LIMIT :start, :limit";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ฟังก์ชันดึงจำนวนแถวทั้งหมด
function fetchTotalRows($conn)
{
    $query = "SELECT COUNT(*) FROM workforce_hcm_actual";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// จำนวนแถวที่จะแสดงในแต่ละหน้า
$limit = 100;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// ดึงข้อมูลจากฐานข้อมูล
$data = fetchData($conn, $start, $limit);
$totalRows = fetchTotalRows($conn);
$totalPages = ceil($totalRows / $limit);

?>

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
                        <h4>รายงานข้อมูลกรอบอัตรากำลัง(จากระบบHCM)</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">รายงานข้อมูลกรอบอัตรากำลัง</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานข้อมูลกรอบอัตรากำลัง</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">ลำดับ</th>
                                                <th rowspan="2">ชื่อ-สกุล</th>
                                                <th rowspan="2">รหัส</th>
                                                <th rowspan="2">เลขที่อัตรา</th>
                                                <th rowspan="2">เลขบัตรประจำตัวประชาชน</th>
                                                <th rowspan="2">สถานะอัตรา</th>
                                                <th rowspan="2">วันบรรจุ</th>
                                                <th rowspan="2">วันเกษียณ</th>
                                                <th rowspan="2">ชื่อตำแหน่ง</th>
                                                <th rowspan="2">ประเภทตำแหน่ง</th>
                                                <th rowspan="2">สังกัด</th>
                                                <th rowspan="2">ระดับการศึกษา</th>
                                                <th rowspan="2">ประเภทบุคลากร</th>
                                                <th rowspan="2">แหล่งเงิน</th>
                                                <th colspan="5">ระดับตำแหน่งบริหาร</th>
                                                <th colspan="5">ระดับตำแหน่งวิชาการ/วิชาชีพเฉพาะ/เชี่ยวชาญเฉพาะ/ทั่วไป
                                                </th>
                                                <th colspan="3">เงินเดือน/ค่าจ้าง</th>
                                                <th colspan="9">สวัสดิการ</th>
                                                <th colspan="10">เงินประจำตำแหน่ง/ค่าตอบแทน</th>
                                            </tr>
                                            <!-- ระดับตำแหน่งบริหาร -->
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>ประเภท
                                                ตำแหน่ง</th>
                                            <th>วันที่แต่งตั้ง
                                                ตำแหน่ง</th>
                                            <th>วันครบวาระการ
                                                ดำรงตำแหน่ง</th>
                                            <th>หนังสือคำสั่งแต่งตั้ง</th>

                                            <!-- ระดับตำแหน่งวิชาการ/วิชาชีพเฉพาะ/เชี่ยวชาญเฉพาะ/ทั่วไป -->
                                            <th>ชื่อตำแหน่ง</th>
                                            <th>วันที่แต่งตั้ง
                                                ตำแหน่ง</th>
                                            <th>หนังสือคำสั่งแต่งตั้ง</th>
                                            <th>ชื่อตำแหน่ง
                                                ที่อยู่ระหว่างขอกำหนด
                                                (สูงขึ้น)</th>
                                            <th>วันที่ยื่นขอกำหนด
                                                (สูงขึ้น)</th>

                                            <!-- เงินเดือน/ค่าจ้าง -->
                                            <th>รวม</th>
                                            <th>เบิกแผ่นดิน</th>
                                            <th>เบิกรายได้</th>

                                            <!-- สวัสดิการ -->
                                            <th>กองทุนสำรอง
                                                เลี้ยงชีพ</th>
                                            <th>กองทุนประกัน
                                                สังคม</th>
                                            <th>กองทุนเงิน
                                                ทดแทน</th>
                                            <th>กองทุนเงินชดเชย
                                                กรณีเลิกจ้าง</th>
                                            <th>กบข.</th>
                                            <th>กสจ</th>
                                            <th>ค่าเช่าบ้าน</th>
                                            <th>ค่ารถประจำ
                                                ตำแหน่ง</th>
                                            <th>สวัสดิการอื่นๆ</th>

                                            <!-- เงินประจำตำแหน่ง/ค่าตอบแทน -->
                                            <th>เงินประจำตำแหน่งทาง
                                                วิชาการ/วิชาชีพเฉพาะ
                                                /เชี่ยวชาญเฉพาะ/ทั่วไป</th>
                                            <th>ค่าตอบแทนตำแหน่งทาง
                                                วิชาการ/วิชาชีพเฉพาะ
                                                /เชี่ยวชาญเฉพาะ/ทั่วไป</th>
                                            <th>เงินค่าตอบ
                                                แทนระดับ 8</th>
                                            <th>ค่าตอบแทนเงิน
                                                เดือนเต็มขั้น</th>
                                            <th>เงินประจำ
                                                ตำแหน่งบริหาร</th>
                                            <th>ค่าตอบแทน
                                                ตำแหน่งบริหาร</th>
                                            <th>เงินดาวน์
                                                ตามมติ ค.ร.ม.</th>
                                            <th>ค่าตอบแทน
                                                พ.ต.ก.</th>
                                            <th>ค่าตอบแทน
                                                พ.ต.ส.</th>
                                            <th>ค่าตอบแทน
                                                พิเศษอื่นๆ</th>

                                            <tr>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $index = 1; // กำหนดตัวแปรลำดับเริ่มต้น ?>
                                            <?php foreach ($data as $row): ?>
                                                <?php if (!empty($row['WORKERS_NAME_SURNAME'])): // ตรวจสอบว่ามีชื่อก่อนแสดง ?>
                                                    <tr>
                                                        <td><?php echo $index++; ?></td> <!-- เพิ่มลำดับที่ -->
                                                        <td><?php echo $row['WORKERS_NAME_SURNAME']; ?></td>
                                                        <td><?php echo !empty($row['EMPLOYEE_ID']) ? $row['EMPLOYEE_ID'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['POSITION_NUMBER']) ? $row['POSITION_NUMBER'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['NATIONAL_ID']) ? $row['NATIONAL_ID'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['RATE_STATUS']) ? $row['RATE_STATUS'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['APPOINTMENT_DATE']) ? $row['APPOINTMENT_DATE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['RETIREMENT_DATE']) ? $row['RETIREMENT_DATE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['POSITION']) ? $row['POSITION'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['all_position_types']) ? $row['all_position_types'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['LOCATION_CODE']) ? $row['LOCATION_CODE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['EMPLOYEE_QUALIFICATIONS']) ? $row['EMPLOYEE_QUALIFICATIONS'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['PERSONNEL_TYPE']) ? $row['PERSONNEL_TYPE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['FUND_FT']) ? $row['FUND_FT'] : '-'; ?></td>
                                                        <td><?php echo !empty($row['MANAGEMENT_POSITION_NAME']) ? $row['MANAGEMENT_POSITION_NAME'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['POSITION_STATUS']) ? $row['POSITION_STATUS'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['POSITION_APPOINTMENT_DATE']) ? $row['POSITION_APPOINTMENT_DATE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['END_OF_TERM_DATE']) ? $row['END_OF_TERM_DATE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['APPOINTMENT_ORDER_DOCUMENT']) ? $row['APPOINTMENT_ORDER_DOCUMENT'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['POSITION_NAME_ACADEMIC_SUPPORT_MAPPED_TO_POSITION_NAME']) ? $row['POSITION_NAME_ACADEMIC_SUPPORT_MAPPED_TO_POSITION_NAME'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['DATE_OF_APPOINTMENT_POSITION']) ? $row['DATE_OF_APPOINTMENT_POSITION'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['LETTER_OF_APPOINTMENT_ORDER']) ? $row['LETTER_OF_APPOINTMENT_ORDER'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['POSITION_NAME_PENDING_PROMOTION_HIGHER_LEVEL']) ? $row['POSITION_NAME_PENDING_PROMOTION_HIGHER_LEVEL'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['DATE_OF_PROMOTION_REQUEST_HIGHER_LEVEL']) ? $row['DATE_OF_PROMOTION_REQUEST_HIGHER_LEVEL'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['SALARY_RATE']) ? $row['SALARY_RATE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['GOVT_FUND']) ? $row['GOVT_FUND'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['Revenue_Disbursement']) ? $row['Revenue_Disbursement'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['PROVIDENT_FUND']) ? $row['PROVIDENT_FUND'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['SOCIAL_SECURITY_FUND']) ? $row['SOCIAL_SECURITY_FUND'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['WORKERS_COMPENSATION_FUND']) ? $row['WORKERS_COMPENSATION_FUND'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['SEVERANCE_FUND']) ? $row['SEVERANCE_FUND'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['GOVERNMENT_PENSION_FUND_GPF']) ? $row['GOVERNMENT_PENSION_FUND_GPF'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['GOVERNMENT_SERVICE_INSURANCE_FUND_GSIF']) ? $row['GOVERNMENT_SERVICE_INSURANCE_FUND_GSIF'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['HOUSING_ALLOWANCE']) ? $row['HOUSING_ALLOWANCE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['POSITION_CAR_ALLOWANCE']) ? $row['POSITION_CAR_ALLOWANCE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['OTHER_BENEFITS']) ? $row['OTHER_BENEFITS'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['ACADEMIC_POSITION_ALLOWANCE']) ? $row['ACADEMIC_POSITION_ALLOWANCE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['POSITION_COMPENSATION_MNGT_ACADEMIC']) ? $row['POSITION_COMPENSATION_MNGT_ACADEMIC'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['LEVEL_8_COMPENSATION']) ? $row['LEVEL_8_COMPENSATION'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['FULL_SALARY_COMPENSATION']) ? $row['FULL_SALARY_COMPENSATION'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['EXECUTIVE_ALLOWANCE']) ? $row['EXECUTIVE_ALLOWANCE'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['EXECUTIVE_COMPENSATION']) ? $row['EXECUTIVE_COMPENSATION'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['DOWN_PAYMENT_ACCORDING_TO_CABINET_RESOLUTION']) ? $row['DOWN_PAYMENT_ACCORDING_TO_CABINET_RESOLUTION'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['PTK_COMPENSATION']) ? $row['PTK_COMPENSATION'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['PTS_COMPENSATION']) ? $row['PTS_COMPENSATION'] : '-'; ?>
                                                        </td>
                                                        <td><?php echo !empty($row['OTHER_SPECIAL_COMPENSATION']) ? $row['OTHER_SPECIAL_COMPENSATION'] : '-'; ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>

                                    </table>
                                </div>

                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                        </li>

                                        <!-- แสดงหน้าปัจจุบัน -->
                                        <li class="page-item active">
                                            <a class="page-link" href="javascript:void(0)"><?php echo $page; ?></a>
                                        </li>

                                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>

                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLSX()" class="btn btn-success m-t-15">Export XLSX</button>

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
            const cells = Array.from(row.cells).map(cell => {
                let text = cell.innerText.trim();

                // เช็คว่าเป็นตัวเลข float (ไม่มี , ในหน้าเว็บ)
                if (!isNaN(text) && text !== "") {
                    text = `"${parseFloat(text).toLocaleString("en-US", { minimumFractionDigits: 2 })}"`;
                }

                return text;
            });

            rows.push(cells.join(",")); // ใช้ , เป็นตัวคั่น CSV
        }

        const csvContent = "\uFEFF" + rows.join("\n"); // ป้องกัน Encoding เพี้ยน
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', 'รายงาน.csv');
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

    function exportXLSX() {
            const table = document.getElementById('reportTable');
            const rows = [];
            const merges = [];
            const mergedCells = {}; // ใช้เก็บตำแหน่งเซลล์ที่ถูก merge

            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const row = table.rows[rowIndex];
                const rowData = [];
                let colIndex = 0; // ควบคุม index ของคอลัมน์ใน Excel

                for (let cellIndex = 0; cellIndex < row.cells.length; cellIndex++) {
                    let cell = row.cells[cellIndex];

                    // ข้ามช่องที่ถูก merge ไว้แล้ว
                    while (mergedCells[`${rowIndex},${colIndex}`]) {
                        rowData[colIndex] = "";
                        colIndex++;
                    }

                    let cellText = cell.innerText.trim();
                    rowData[colIndex] = cellText; // ใช้ colIndex ให้คงค่าเดิมใน merged cell

                    let rowspan = cell.rowSpan || 1;
                    let colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        merges.push({
                            s: { r: rowIndex, c: colIndex }, // จุดเริ่มต้น
                            e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 } // จุดสิ้นสุด
                        });

                        // บันทึกว่าเซลล์เหล่านี้ถูก merge แล้ว
                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (r !== 0 || c !== 0) {
                                    mergedCells[`${rowIndex + r},${colIndex + c}`] = true;
                                }
                            }
                        }
                    }

                    colIndex += colspan;
                }
                rows.push(rowData);
            }

            // สร้างไฟล์ Excel
            const XLSX = window.XLSX;
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(rows);

            // ✅ เพิ่ม Merge Cells
            ws['!merges'] = merges;

            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // ✅ ดาวน์โหลดไฟล์ Excel
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'array'
            });
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงาน.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
    <!-- โหลดไลบรารี xlsx จาก CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


</body>

</html>