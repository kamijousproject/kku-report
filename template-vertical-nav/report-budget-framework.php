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
                        <h4>รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายละเอียดงบประมาณและจำนวนอัตรากำลัง</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                            <th rowspan="3">ที่</th>
                                            <th rowspan="3">ส่วนงาน/หน่วยงาน</th>
                                            <th colspan="4">ประเภทบริหาร</th>
                                            <th colspan="4">ประเภทวิชาการ</th>
                                            <th colspan="4">ประเภทวิจัย</th>
                                            <th colspan="4">ประเภทสนับสนุน</th>
                                            <th colspan="4">รวม</th>
                                            </tr>
                                            <tr>
                                            <th colspan="2">งบประมาณแผ่นดิน</th>
                                            <th colspan="2">งบประมาณเงินรายได้</th>
                                            <th colspan="2">งบประมาณแผ่นดิน</th>
                                            <th colspan="2">งบประมาณเงินรายได้</th>
                                            <th colspan="2">งบประมาณแผ่นดิน</th>
                                            <th colspan="2">งบประมาณเงินรายได้</th>
                                            <th colspan="2">งบประมาณแผ่นดิน</th>
                                            <th colspan="2">งบประมาณเงินรายได้</th>
                                            <th colspan="2">งบประมาณแผ่นดิน</th>
                                            <th colspan="2">งบประมาณเงินรายได้</th>
                                            </tr>
                                            <tr>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            <th>จำนวน (อัตรา)</th>
                                            <th>งบประมาณ (บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>หน่วยงาน A</td>
                                                <td>10</td>
                                                <td>1,000,000</td>
                                                <td>15</td>
                                                <td>1,500,000</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>หน่วยงาน B</td>
                                                <td>8</td>
                                                <td>800,000</td>
                                                <td>12</td>
                                                <td>1,200,000</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            
                                        </tfoot>
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
        $(document).ready(function() {
            laodData();
            
        });

        function laodData() {
            $.ajax({
                type: "POST",
                url: "../server/workforce_api.php",
                data: {
                    'command': 'kku_wf_budget-framework'
                },
                dataType: "json",
                success: function(response) {
                    console.log(response.wf);
                    console.log(response.faculty);
                    const tableBody = document.querySelector('#reportTable tbody');
                    tableBody.innerHTML = ''; // ล้างข้อมูลเก่า


                        response.wf.forEach((row, index) => {                   
                            const tr = document.createElement('tr');

                            const columns = [
                                { key: 'No', value: index+1 },
                                { key: 'Alias_Default', value: row.Alias_Default },
                                
                                { key: 'TYPE1_fund1_num', value: row.TYPE1_fund1_num },
                                { key: 'TYPE1_fund1', value: row.TYPE1_fund1 },
                                
                                { key: 'TYPE1_fund2_num', value: row.TYPE1_fund2_num },
                                { key: 'TYPE1_fund2', value: row.TYPE1_fund2 },
                                
                                { key: 'TYPE2_fund1_num', value: row.TYPE2_fund1_num },
                                { key: 'TYPE2_fund1', value: row.TYPE2_fund1 },
                                
                                { key: 'TYPE2_fund2_num', value: row.TYPE2_fund2_num },
                                { key: 'TYPE2_fund2', value: row.TYPE2_fund2 },
                                
                                { key: 'TYPE3_fund1_num', value: row.TYPE3_fund1_num },
                                { key: 'TYPE3_fund1', value: row.TYPE3_fund1 },
                                
                                { key: 'TYPE3_fund2_num', value: row.TYPE3_fund2_num },
                                { key: 'TYPE3_fund2', value: row.TYPE3_fund2 },
                                
                                { key: 'TYPE4_fund1_num', value: row.TYPE4_fund1_num },
                                { key: 'TYPE4_fund1', value: row.TYPE4_fund1 },
                                
                                { key: 'TYPE4_fund2_num', value: row.TYPE4_fund2_num },
                                { key: 'TYPE4_fund2', value: row.TYPE4_fund2 },
                                
                                { key: 'sum_fund1_num', value: parseInt(row.TYPE1_fund1_num)+parseInt(row.TYPE2_fund1_num)+parseInt(row.TYPE3_fund1_num)+parseInt(row.TYPE4_fund1_num) },
                                { key: 'sum_fund1', value: parseInt(row.TYPE1_fund1)+parseInt(row.TYPE2_fund1)+parseInt(row.TYPE3_fund1)+parseInt(row.TYPE4_fund1) },
                                
                                { key: 'sum_fund2_num', value: parseInt(row.TYPE1_fund2_num)+parseInt(row.TYPE2_fund2_num)+parseInt(row.TYPE3_fund2_num)+parseInt(row.TYPE4_fund2_num)},
                                { key: 'sum_fund2', value: parseInt(row.TYPE1_fund2)+parseInt(row.TYPE2_fund2)+parseInt(row.TYPE3_fund2)+parseInt(row.TYPE4_fund2) },
                            ];

                            columns.forEach(col => {
                                const td = document.createElement('td');
                                td.textContent = col.value;
                                tr.appendChild(td);
                            });


                            tableBody.appendChild(tr);
                            
                        });
                        calculateSum();

                },
                error: function(jqXHR, exception) {
                    console.error("Error: " + exception);
                    responseError(jqXHR, exception);
                }
            });
        }
        function calculateSum() {
        const table = document.querySelector('table');
        const rows = table.querySelectorAll('tbody tr');
        const footer = table.querySelector('tfoot');
        const columns = rows[0].querySelectorAll('td').length;

        // สร้างแถว footer
        let footerRow = document.createElement('tr');
        footerRow.innerHTML = '<td colspan="2">รวม</td>';

        // เริ่มต้นผลรวมแต่ละคอลัมน์
        let sums = new Array(columns - 2).fill(0); 

        // คำนวณผลรวม
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
            if (index >= 2) { // "ส่วนงาน/หน่วยงาน"               
                sums[index - 2] += parseFloat(cell.textContent) || 0;
            }
            });
        });

        // เพิ่มผลรวมลงใน footer
        sums.forEach(sum => {
            footerRow.innerHTML += `<td>${sum}</td>`;
        });

        // เพิ่มแถว footer ลงในตาราง
        footer.appendChild(footerRow);
        }
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
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>