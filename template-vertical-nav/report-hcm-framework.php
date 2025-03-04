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
            const table = document.getElementById('reportTable');
            const csvRows = [];

            // วนลูปทีละ <tr>
            for (const row of table.rows) {
                // เก็บบรรทัดย่อยของแต่ละเซลล์
                const cellLines = [];
                let maxSubLine = 1;

                // วนลูปทีละเซลล์ <td>/<th>
                for (const cell of row.cells) {
                    let html = cell.innerHTML;

                    // 1) แปลง &nbsp; ติดกันให้เป็น non-breaking space (\u00A0) ตามจำนวน
                    html = html.replace(/(&nbsp;)+/g, (match) => {
                        const count = match.match(/&nbsp;/g).length;
                        return '\u00A0'.repeat(count); // ex. 3 &nbsp; → "\u00A0\u00A0\u00A0"
                    });


                    // 3) (ถ้าต้องการ) ลบ tag HTML อื่นออก
                    html = html.replace(/<\/?[^>]+>/g, '');

                    // 4) แยกเป็น array บรรทัดย่อย
                    const lines = html.split('\n').map(x => x.trimEnd());
                    // ใช้ trimEnd() เฉพาะท้าย ไม่ trim ต้นเผื่อบางคนอยากเห็นช่องว่างนำหน้า

                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }

                    cellLines.push(lines);
                }

                // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];

                    // วนลูปแต่ละเซลล์
                    for (const lines of cellLines) {
                        let text = lines[i] || ''; // ถ้าไม่มีบรรทัดที่ i ก็ว่าง
                        // Escape double quotes
                        text = text.replace(/"/g, '""');
                        // ครอบด้วย ""
                        text = `"${text}"`;
                        rowData.push(text);
                    }

                    csvRows.push(rowData.join(','));
                }
            }

            // รวมเป็น CSV + BOM
            const csvContent = "\uFEFF" + csvRows.join("\n");
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานข้อมูลกรอบอัตรากำลัง(จากระบบHCM).csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
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
            doc.text("รายงานข้อมูลกรอบอัตรากำลัง(จากระบบHCM)", 10, 500);

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
            doc.save('รายงานข้อมูลกรอบอัตรากำลัง(จากระบบHCM).pdf');
        }

        function exportXLSX() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const {
                theadRows,
                theadMerges
            } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br />, ไม่ merge) ============
            const tbodyRows = parseTbody(table.tBodies[0]);

            // รวม rows ทั้งหมด: thead + tbody
            const allRows = [...theadRows, ...tbodyRows];

            // สร้าง Workbook + Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(allRows);

            // ใส่ merges ของ thead ลงใน sheet (ถ้ามี)
            ws['!merges'] = theadMerges;

            // ตั้งค่า vertical-align: bottom ให้ทุกเซลล์
            applyCellStyles(ws, "bottom");

            // เพิ่ม worksheet ลงใน workbook
            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // เขียนไฟล์เป็น .xlsx (แทน .xls เพื่อรองรับ style)
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'array'
            });

            // สร้าง Blob + ดาวน์โหลด
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานข้อมูลกรอบอัตรากำลัง(จากระบบHCM).xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        /**
         * -----------------------
         * 1) parseThead: รองรับ merge
         * -----------------------
         */
        function parseThead(thead) {
            const theadRows = [];
            const theadMerges = [];

            if (!thead) {
                return {
                    theadRows,
                    theadMerges
                };
            }

            const skipMap = {};

            for (let rowIndex = 0; rowIndex < thead.rows.length; rowIndex++) {
                const tr = thead.rows[rowIndex];
                const rowData = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                    while (skipMap[`${rowIndex},${colIndex}`]) {
                        rowData[colIndex] = "";
                        colIndex++;
                    }

                    const cell = tr.cells[cellIndex];
                    let text = cell.innerHTML
                        .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length)) // แทนที่ &nbsp; ด้วยช่องว่าง
                        .replace(/<\/?[^>]+>/g, '') // ลบแท็ก HTML ทั้งหมด
                        .trim();

                    rowData[colIndex] = text;

                    const rowspan = cell.rowSpan || 1;
                    const colspan = cell.colSpan || 1;

                    if (rowspan > 1 || colspan > 1) {
                        theadMerges.push({
                            s: {
                                r: rowIndex,
                                c: colIndex
                            },
                            e: {
                                r: rowIndex + rowspan - 1,
                                c: colIndex + colspan - 1
                            }
                        });

                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (r === 0 && c === 0) continue;
                                skipMap[`${rowIndex + r},${colIndex + c}`] = true;
                            }
                        }
                    }
                    colIndex++;
                }
                theadRows.push(rowData);
            }

            return {
                theadRows,
                theadMerges
            };
        }

        /**
         * -----------------------
         * 2) parseTbody: แตก <br/> เป็นหลาย sub-row
         * -----------------------
         */
        function parseTbody(tbody) {
            const rows = [];

            if (!tbody) return rows;

            for (const tr of tbody.rows) {
                const cellLines = [];
                let maxSubLine = 1;

                for (const cell of tr.cells) {
                    let html = cell.innerHTML
                        .replace(/(&nbsp;)+/g, match => {
                            const count = match.match(/&nbsp;/g).length;
                            return ' '.repeat(count);
                        })
                        .replace(/<\/?[^>]+>/g, ''); // ลบแท็ก HTML ทั้งหมด

                    const lines = html.split('\n').map(x => x.trimEnd());
                    if (lines.length > maxSubLine) {
                        maxSubLine = lines.length;
                    }
                    cellLines.push(lines);
                }

                for (let i = 0; i < maxSubLine; i++) {
                    const rowData = [];
                    for (const lines of cellLines) {
                        rowData.push(lines[i] || '');
                    }
                    rows.push(rowData);
                }
            }

            return rows;
        }

        /**
         * -----------------------
         * 3) applyCellStyles: ตั้งค่า vertical-align ให้ทุก cell
         * -----------------------
         */
        function applyCellStyles(ws, verticalAlign) {
            if (!ws['!ref']) return;

            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let R = range.s.r; R <= range.e.r; ++R) {
                for (let C = range.s.c; C <= range.e.c; ++C) {
                    const cell_address = XLSX.utils.encode_cell({
                        r: R,
                        c: C
                    });
                    if (!ws[cell_address]) continue;

                    if (!ws[cell_address].s) ws[cell_address].s = {};
                    ws[cell_address].s.alignment = {
                        vertical: verticalAlign
                    };
                }
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>


    <!-- โหลดไลบรารีที่จำเป็น -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <script src="../js/custom.min.js"></script>


    <!-- โหลดฟอนต์ THSarabun (ตรวจสอบไม่ให้ประกาศซ้ำ) -->
    <script>
        if (typeof window.thsarabunnew_webfont_normal === 'undefined') {
            window.thsarabunnew_webfont_normal = "data:font/truetype;base64,AAEAAA...";
        }
    </script>
</body>

</html>