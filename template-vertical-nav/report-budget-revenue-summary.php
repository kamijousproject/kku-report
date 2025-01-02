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
                        <h4>รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปงบประมาณรายรับ จำแนกตามประเภทรายรับ</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>1. เงินอุดหนุนจากรัฐ</th>
                                                <th>2. เงินและทรัพย์สินซึ่งมีผู้บริจาคให้แก่มหาวิทยาลัย</th>
                                                <th>3. เงินกองทุนที่รัฐบาลหรือมหาวิทยาลัยจัดตั้งขึ้นและรายได้หรือผลประโยชน์จากกองทุน</th>
                                                <th>4. ค่าธรรมเนียม ค่าบำรุง ค่าตอบแทน เบี้ยปรับ และค่าบริการต่างๆ ของมหาวิทยาลัย</th>
                                                <th>5. รายได้หรือผลประโยชน์ที่ได้จากการลงทุนหรือการร่วมลงทุนจากทรัพย์สินของมหาวิทยาลัย</th>
                                                <th>6. รายได้หรือผลประโยชน์ที่ได้จากการใช้ทรัพย์สินหรือจัดทำเพื่อเป็นที่ราชพัสดุหรือทรัพย์สินของมหาวิทยาลัยปกครอง ดูแล ใช้ หรือจัดทำประโยชน์</th>
                                                <th>7. เงินอุดหนุนจากหน่วยงานภายนอก เงินทุนอุดหนุนการวิจัยหรือการบริการวิชาการที่ได้รับจากหน่วยงานของรัฐ</th>
                                                <th>8. เงินและผลประโยชน์ที่ได้รับจากการบริการวิชาการ การวิจัย และนำทรัพย์สินทางปัญญาไปทำประโยชน์</th>
                                                <th>9. รายได้ผลประโยชน์อื่นๆ</th>
                                                <th>รวมทั้งหมด</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>5,000,000</td>
                                                <td>1,200,000</td>
                                                <td>2,000,000</td>
                                                <td>3,000,000</td>
                                                <td>1,500,000</td>
                                                <td>500,000</td>
                                                <td>4,000,000</td>
                                                <td>2,500,000</td>
                                                <td>300,000</td>
                                                <td>20,000,000</td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>คณะบริหารธุรกิจ</td>
                                                <td>3,000,000</td>
                                                <td>800,000</td>
                                                <td>1,500,000</td>
                                                <td>2,000,000</td>
                                                <td>1,000,000</td>
                                                <td>400,000</td>
                                                <td>3,000,000</td>
                                                <td>2,000,000</td>
                                                <td>200,000</td>
                                                <td>13,900,000</td>
                                            </tr>
                                            <tr>
                                                <th>3</th>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>4,000,000</td>
                                                <td>1,000,000</td>
                                                <td>2,200,000</td>
                                                <td>2,500,000</td>
                                                <td>1,200,000</td>
                                                <td>600,000</td>
                                                <td>3,500,000</td>
                                                <td>2,300,000</td>
                                                <td>400,000</td>
                                                <td>17,700,000</td>
                                            </tr>
                                            <tr>
                                                <th>รวมทั้งสิ้น</th>
                                                <td></td>
                                                <td>12,000,000</td>
                                                <td>3,000,000</td>
                                                <td>5,700,000</td>
                                                <td>7,500,000</td>
                                                <td>3,700,000</td>
                                                <td>1,500,000</td>
                                                <td>10,500,000</td>
                                                <td>6,800,000</td>
                                                <td>900,000</td>
                                                <td>51,600,000</td>
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