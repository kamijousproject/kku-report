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
                        <h4>รายงานการสรุป คำขออัตราใหม่และอัตราเดิม (หลังจากส่วนงาน/หน่วยงาน กรอกคำขออัตราเดิมและอัตราใหม่)</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการสรุป คำขออัตราใหม่และอัตราเดิม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>(หลังจากส่วนงาน/หน่วยงาน กรอกคำขออัตราเดิมและอัตราใหม่)</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>ประเภทจ้าง</th>
                                                <th>ชื่อ - นามสกุล</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ประเภทการจ้าง</th>
                                                <th>เลขประจำตำแหน่ง</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>จำนวนที่ขอ</th>
                                                <th>Job Family</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>กลุ่มบุคลากร</th>
                                                <th>ประเภทสัญญา</th>
                                                <th>ระยะเวลาสัญญา</th>
                                                <th>คุณวุฒิของตำแหน่ง</th>
                                                <th>สถานะอัตรา</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>สัญญาประจำ</td>
                                                <td>สมชาย ใจดี</td>
                                                <td>วิชาการ</td>
                                                <td>ประจำ</td>
                                                <td>12345</td>
                                                <td>อาจารย์</td>
                                                <td>1</td>
                                                <td>Teaching</td>
                                                <td>วิชาการ</td>
                                                <td>บุคลากรสายวิชาการ</td>
                                                <td>ประจำ</td>
                                                <td>5 ปี</td>
                                                <td>ปริญญาเอก</td>
                                                <td>ว่าง</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>สัญญาจ้าง</td>
                                                <td>สมหญิง สมศรี</td>
                                                <td>สนับสนุน</td>
                                                <td>ชั่วคราว</td>
                                                <td>67890</td>
                                                <td>นักวิจัย</td>
                                                <td>1</td>
                                                <td>Research</td>
                                                <td>สนับสนุน</td>
                                                <td>บุคลากรสายสนับสนุน</td>
                                                <td>ชั่วคราว</td>
                                                <td>1 ปี</td>
                                                <td>ปริญญาโท</td>
                                                <td>เต็ม</td>
                                            </tr>
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
            const doc = new jsPDF();

            // ตั้งค่าชื่อหัวข้อของเอกสาร
            doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

            // ใช้ autoTable
            doc.autoTable({
                html: '#reportTable', // ดึงข้อมูลจากตาราง HTML
                startY: 20, // เริ่มการวาดตารางด้านล่างข้อความ
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
    <script src="../../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>