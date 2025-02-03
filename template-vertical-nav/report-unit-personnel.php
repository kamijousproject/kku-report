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
                        <h4>รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">ที่</th>
                                                <th rowspan="3">ส่วนงาน/หน่วยงาน</th>
                                                <th colspan="3">ข้าราชการ</th>
                                                <th >ลูกจ้างประจำ</th>
                                                <th colspan="10">พนักงานมหาวิทยาลัยงบประมาณแผ่นดิน</th>
                                                <th colspan="10">พนักงานมหาวิทยาลัยงบประมาณเงินรายได้</th>
                                                <th colspan="7">ลูกจ้างของมหาวิทยาลัย</th>
                                            </tr>
                                            <tr>
                                                <th >วิชาการ</th>
                                                <th >สนับสนุน</th>
                                                <th rowspan="2">รวม</th>
                                                <th >สนับสนุน</th>
                                                <th >บริหาร</th>
                                                <th colspan="2">วิชาการ</th>
                                                <th colspan="2">วิจัย</th>
                                                <th colspan="2">สนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th rowspan="2">รวมทั้งหมด</th>
                                                <th >บริหาร</th>
                                                <th colspan="2">วิชาการ</th>
                                                <th colspan="2">วิจัย</th>
                                                <th colspan="2">สนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th rowspan="2">รวมทั้งหมด</th>
                                                <th colspan="2">วิจัย</th>
                                                <th colspan="2">สนับสนุน</th>
                                                <th colspan="2">รวม</th>
                                                <th rowspan="2">รวมทั้งหมด</th>
                                            </tr>
                                            <tr>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                                <th>คนครอง</th>
                                                <th>อัตราว่าง</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>หน่วยงาน A</td>
                                                <td>5</td>
                                                <td>3</td>
                                                <td>8</td>
                                                <td>10</td>
                                                <td>5</td>
                                                <td>15</td>
                                                <td>8</td>
                                                <td>6</td>
                                                <td>14</td>
                                                <td>4</td>
                                                <td>2</td>
                                                <td>6</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>หน่วยงาน B</td>
                                                <td>6</td>
                                                <td>4</td>
                                                <td>10</td>
                                                <td>8</td>
                                                <td>6</td>
                                                <td>14</td>
                                                <td>7</td>
                                                <td>5</td>
                                                <td>12</td>
                                                <td>3</td>
                                                <td>1</td>
                                                <td>4</td>
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
                    'command': 'kku_wf_unit-personnel'
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
                                
                                { key: 'c1', value: row.c1 },
                                { key: 'c2', value: row.c2 },
                                { key: 'sum1', value: parseInt(row.c1)+parseInt(row.c2) },
                                
                                { key: 'c3', value: row.c3 },
                                
                                { key: 'c4', value: row.c4 },
                                { key: 'c5', value: row.c5 },
                                { key: 'c6', value: row.c6 },
                                { key: 'c7', value: row.c7 },
                                { key: 'c8', value: row.c8 },
                                { key: 'c9', value: row.c9 },
                                { key: 'c10', value: row.c10 },
                                { key: 'sum2', value: parseInt(row.c4)+parseInt(row.c5)+parseInt(row.c7)+parseInt(row.c9) },
                                { key: 'sum3', value: parseInt(row.c6)+parseInt(row.c8)+parseInt(row.c10) },
                                { key: 'sum4', value: parseInt(row.c4)+parseInt(row.c5)+parseInt(row.c6)+parseInt(row.c7)+parseInt(row.c8)+parseInt(row.c9)+parseInt(row.c10) },
                                
                                { key: 'c11', value: row.c11 },
                                { key: 'c12', value: row.c12 },
                                { key: 'c13', value: row.c13 },
                                { key: 'c14', value: row.c14 },                                
                                { key: 'c15', value: row.c15 },
                                { key: 'c16', value: row.c16 },                               
                                { key: 'c17', value: row.c17 },
                                { key: 'sum5', value: parseInt(row.c11)+parseInt(row.c12)+parseInt(row.c14)+parseInt(row.c16) },
                                { key: 'sum6', value: parseInt(row.c13)+parseInt(row.c15)+parseInt(row.c17) },
                                { key: 'sum7', value: parseInt(row.c11)+parseInt(row.c12)+parseInt(row.c13)+parseInt(row.c14)+parseInt(row.c15)+parseInt(row.c16)+parseInt(row.c17) },

                                { key: 'c18', value: row.c18 },                                
                                { key: 'c19', value: row.c19 },
                                { key: 'c20', value: row.c20 },                               
                                { key: 'c21', value: row.c21 },
                                { key: 'sum8', value: parseInt(row.c18)+parseInt(row.c20) },
                                { key: 'sum9', value: parseInt(row.c19)+parseInt(row.c21)},
                                { key: 'sum10', value: parseInt(row.c18)+parseInt(row.c19)+parseInt(row.c20)+parseInt(row.c21)},
                                
                                
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