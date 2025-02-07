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
                        <h4>รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานกรอบอัตรากำลัง 4 ปี แยกตามประเภท และภาระงาน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">ที่</th>
                                            <th rowspan="2">ส่วนงาน</th>
                                            <th colspan="2">ประเภทบริหาร</th>
                                            <th colspan="6">ประเภทวิชาการ</th>
                                            <th colspan="2">ประเภทวิจัย</th>
                                            <th colspan="14">ประเภทสนับสนุน</th>
                                            <th rowspan="2">รวมกรอบอัตราพึงมีทั้งหมด</th>
                                        </tr>
                                        <tr>
                                            <!-- ประเภทบริหาร -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมี</th>
                                            <!-- ประเภทวิชาการ -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบพึงมีวิชาการตามแผน 2563-2566</th>
                                            <th>เกณฑ์ FTES</th>
                                            <th>เกณฑ์ภาระงานวิจัย</th>
                                            <th>เกณฑ์ภาระงานบริการวิชาการ</th>
                                            <th>รวมวิชาการ</th>
                                            <!-- ประเภทวิจัย -->
                                            <th>เกณฑ์ภาระงานวิจัย</th>
                                            <th>รวมวิจัย</th>
                                            <!-- ประเภทสนับสนุน -->
                                            <th>Healthcare Services</th>
                                            <th>Student and Faculty Services</th>
                                            <th>Technical and Research services</th>
                                            <th>Internationalization</th>
                                            <th>Human Resources</th>
                                            <th>Administration</th>
                                            <th>Legal, Compliance and Protection</th>
                                            <th>Strategic Management</th>
                                            <th>Information Technology</th>
                                            <th>Infrastructure and Facility Services</th>
                                            <th>Communication and Relation Management</th>
                                            <th>Cultural Affair</th>
                                            <th>Financial Services</th>
                                            <th>รวมประเภทสนับสนุน</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>20</td>
                                                <td>25</td>
                                                <td>30</td>
                                                <td>35</td>
                                                <td>15</td>
                                                <td>45</td>
                                                <td>10</td>
                                                <td>20</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>15</td>
                                                <td>20</td>
                                                <td>25</td>
                                                <td>30</td>
                                                <td>12</td>
                                                <td>37</td>
                                                <td>8</td>
                                                <td>18</td>
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
                    'command': 'kku_wf_4year-workload'
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
                                
                                { key: 'Actual_type1', value: row.Actual_type1 },
                                { key: 'wf_type1', value: row.wf_type1 },
                                
                                { key: 'Actual_type2', value: row.Actual_type2 },
                                { key: 'wf_plan', value: "" },
                                { key: 'sum_FTES', value: row.sum_FTES },
                                
                                { key: 'sum_RWC', value: row.sum_RWC },
                                { key: 'sum_WCAS', value: row.sum_WCAS },
                                
                                { key: 'total_type2', value:  parseInt(row.Actual_type2) +parseInt(row.sum_FTES) +parseInt(row.sum_RWC) +parseInt(row.sum_WCAS) },
                                { key: 'sum_RWC2', value: row.sum_RWC2 },
                                
                                { key: 'sum_RWC2', value: row.sum_RWC2 },

                                { key: 'j1', value: row.j1 },        
                                { key: 'j2', value: row.j2 },
                                { key: 'j3', value: row.j3 },
                                { key: 'j4', value: row.j4},
                                { key: 'j5', value: row.j5 },
                                { key: 'j6', value: row.j6 },
                                { key: 'j7', value: row.j7 },
                                { key: 'j8', value: row.j8},
                                { key: 'j9', value: row.j9 },
                                { key: 'j10', value: row.j10 },
                                { key: 'j11', value: row.j11 },
                                { key: 'j12', value: row.j12 },        
                                { key: 'j13', value: row.j13 },
                                { key: 'j14', value: parseInt(row.j1) + 
                                                        parseInt(row.j2) + 
                                                        parseInt(row.j3) + 
                                                        parseInt(row.j4) + 
                                                        parseInt(row.j5) + 
                                                        parseInt(row.j6) + 
                                                        parseInt(row.j7) + 
                                                        parseInt(row.j8) + 
                                                        parseInt(row.j9) + 
                                                        parseInt(row.j10) + 
                                                        parseInt(row.j11) + 
                                                        parseInt(row.j12) + 
                                                        parseInt(row.j13) },
                                { key: 'j15', value: "0"},
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