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
                        <h4>รายงานสรุปยอดงบประมาณคงเหลือ</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปยอดงบประมาณคงเหลือ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปยอดงบประมาณคงเหลือ</h4>
                                </div>
                                <div class="info-section">
                                    <p>ปีบริหารงบประมาณ: .......................</p>
                                    <p>ประเภทของงบประมาณ: .......................</p>
                                    <p>แหล่งเงิน: .......................</p>
                                    <p>ส่วนงาน/หน่วยงาน: .......................</p>
                                    <p>แผนงาน (ผลผลิต): .......................</p>
                                    <p>แผนงานย่อย (ผลผลิตย่อย/กิจกรรม): .......................</p>
                                    <p>โครงการ (Project): .......................</p>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">คำใช้งบ</th>
                                                <th rowspan="2">รายการรายจ่าย<br>[Item Name]</th>
                                                <th colspan="2">ยอดรวมงบประมาณ</th>
                                                <th colspan="2">เงินประจำงวด</th>
                                                <th colspan="2">ยุททัศน์</th>
                                                <th colspan="2">ยุททัศน์ระบบงบประมาณและข้อผูกพัน</th>
                                                <th rowspan="2">จำนวนงบประมาณ<br>เบิกจ่าย</th>
                                            </tr>
                                            <tr>
                                                <th>จำนวนงบประมาณ<br>โดยรวม</th>
                                                <th>จำนวนงบประมาณ<br>โดยยก</th>
                                                <th>คงเหลือไม่<br>สมบูรณ์</th>
                                                <th>เปอร์เซ็นต์</th>
                                                <th>คงเหลือหลัง<br>เบิกจ่าย</th>
                                                <th>เปอร์เซ็นต์</th>
                                                <th>จำนวนงบประมาณ<br>คงเหลือ</th>
                                                <th>เปอร์เซ็นต์</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>ค่าใช้จ่ายบุคลากร</td>
                                                <td>1. เงินเดือนข้าราชการและลูกจ้างประจำ</td>
                                                <td>5,000,000</td>
                                                <td>1,000,000</td>
                                                <td>3,000,000</td>
                                                <td>60%</td>
                                                <td>2,000,000</td>
                                                <td>40%</td>
                                                <td>1,000,000</td>
                                                <td>20%</td>
                                                <td>5,000,000</td>
                                            </tr>
                                            <tr>
                                                <td>ค่าใช้จ่ายดำเนินงาน</td>
                                                <td>2. ค่าเบี้ยเลี้ยงและค่าเดินทาง</td>
                                                <td>1,000,000</td>
                                                <td>500,000</td>
                                                <td>400,000</td>
                                                <td>80%</td>
                                                <td>300,000</td>
                                                <td>60%</td>
                                                <td>200,000</td>
                                                <td>40%</td>
                                                <td>1,000,000</td>
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
    <script src="../../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>