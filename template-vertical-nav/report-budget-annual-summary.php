<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>

<body class="v-light vertical-nav fix-header fix-sidebar">
    <div id="main-wrapper">
        <?php include('../component/left-nev.php') ?>

        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                                <th>รวม</th>
                                                <th>เงินอุดหนุนจากรัฐ (คำขอ)</th>
                                                <th>เงินอุดหนุนจากรัฐ (จัดสรร)</th>
                                                <th>เงินนอกงบประมาณ (คำขอ)</th>
                                                <th>เงินนอกงบประมาณ (จัดสรร)</th>
                                                <th>เงินรายได้ (คำขอ)</th>
                                                <th>เงินรายได้ (จัดสรร)</th>
                                                <th>รวม (คำขอ)</th>
                                                <th>รวม (จัดสรร)</th>
                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>ตัวอย่างรายการ 1</td>
                                                <td>5,000</td>
                                                <td>2,000</td>
                                                <td>3,000</td>
                                                <td>10,000</td>
                                                <td>6,000</td>
                                                <td>5,800</td>
                                                <td>2,500</td>
                                                <td>2,200</td>
                                                <td>3,500</td>
                                                <td>3,200</td>
                                                <td>12,000</td>
                                                <td>11,200</td>
                                                <td>1,200</td>
                                                <td>12%</td>
                                            </tr>
                                            <tr>
                                                <td>ตัวอย่างรายการ 2</td>
                                                <td>4,500</td>
                                                <td>1,800</td>
                                                <td>2,500</td>
                                                <td>8,800</td>
                                                <td>5,500</td>
                                                <td>5,300</td>
                                                <td>2,200</td>
                                                <td>2,100</td>
                                                <td>3,200</td>
                                                <td>3,000</td>
                                                <td>10,900</td>
                                                <td>10,400</td>
                                                <td>1,600</td>
                                                <td>18.18%</td>
                                            </tr>
                                            <!-- เพิ่มข้อมูลตัวอย่างได้ตามต้องการ -->
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
            const csvContent = "\uFEFF" + rows.join("\n");
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

            doc.setFont("THSarabun");
            doc.setFontSize(12);
            doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                styles: {
                    font: "THSarabun",
                    fontSize: 10,
                    lineColor: [0, 0, 0],
                    lineWidth: 0.5,
                },
                bodyStyles: {
                    lineColor: [0, 0, 0],
                    lineWidth: 0.5,
                },
                headStyles: {
                    fillColor: [102, 153, 225],
                    textColor: [0, 0, 0],
                    lineColor: [0, 0, 0],
                    lineWidth: 0.5,
                },
            });

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
    <script src="../assets/plugins/common/common.min.js"></script>
    <script src="../js/custom.min.js"></script>
</body>

</html>