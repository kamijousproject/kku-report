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
                        <h4>รายงานสถานะของแผนงานแต่ละแผน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสถานะของแผนงานแต่ละแผน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสถานะของแผนงานแต่ละแผน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>รหัส</th>
                                                <th>ยุทธศาสตร์</th>
                                                <th>รหัส</th>
                                                <th>แผนงาน/โครงการ</th>
                                                <th colspan="4">สถานะ (Status)</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>ยังไม่ดำเนินการ</th>
                                                <th>อยู่ระหว่างดำเนินการ</th>
                                                <th>ดำเนินการแล้ว</th>
                                                <th>ยกเลิก</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>001</td>
                                                <td>ยุทธศาสตร์ที่ 1</td>
                                                <td>PRJ01</td>
                                                <td>โครงการพัฒนา</td>
                                                <td><span class="badge badge-secondary">✔</span></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>002</td>
                                                <td>ยุทธศาสตร์ที่ 2</td>
                                                <td>PRJ02</td>
                                                <td>โครงการปรับปรุง</td>
                                                <td></td>
                                                <td><span class="badge badge-primary">✔</span></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>003</td>
                                                <td>ยุทธศาสตร์ที่ 3</td>
                                                <td>PRJ03</td>
                                                <td>โครงการขยาย</td>
                                                <td></td>
                                                <td></td>
                                                <td><span class="badge badge-success">✔</span></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>004</td>
                                                <td>ยุทธศาสตร์ที่ 4</td>
                                                <td>PRJ04</td>
                                                <td>โครงการยกเลิก</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><span class="badge badge-danger">✔</span></td>
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