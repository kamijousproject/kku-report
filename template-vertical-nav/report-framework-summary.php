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
                        <h4>รายงานสรุปแผนกรอบอตัรากำลัง 4 ปีแยกตามประเภท</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปแผนกรอบอตัรากำลัง 4 ปีแยกตามประเภท</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปแผนกรอบอตัรากำลัง 4 ปีแยกตามประเภท</h4>
                                    <p>ปีเริ่มต้น: 25XX</p>
                                    <p>ปีสิ้นสุด: 25XX</p>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">ที่</th>
                                            <th rowspan="2">ส่วนงาน</th>
                                            <th colspan="6">ประเภทบริหาร</th>
                                            <th colspan="6">ประเภทวิชาการ</th>
                                            <th colspan="6">ประเภทวิจัย</th>
                                            <th colspan="6">ประเภทสนับสนุน</th>
                                        </tr>
                                        <tr>
                                            <!-- ประเภทบริหาร -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมีตามแผน 4 ปีเดิม</th>
                                            <th>ปี 2567 (ปีที่ 1)</th>
                                            <th>ปี 2568 (ปีที่ 2)</th>
                                            <th>ปี 2569 (ปีที่ 3)</th>
                                            <th>ปี 2570 (ปีที่ 4)</th>
                                            <!-- ประเภทวิชาการ -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมีตามแผน 4 ปีเดิม</th>
                                            <th>ปี 2567 (ปีที่ 1)</th>
                                            <th>ปี 2568 (ปีที่ 2)</th>
                                            <th>ปี 2569 (ปีที่ 3)</th>
                                            <th>ปี 2570 (ปีที่ 4)</th>
                                            <!-- ประเภทวิจัย -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมีตามแผน 4 ปีเดิม</th>
                                            <th>ปี 2567 (ปีที่ 1)</th>
                                            <th>ปี 2568 (ปีที่ 2)</th>
                                            <th>ปี 2569 (ปีที่ 3)</th>
                                            <th>ปี 2570 (ปีที่ 4)</th>
                                            <!-- ประเภทสนับสนุน -->
                                            <th>อัตราปัจจุบัน</th>
                                            <th>กรอบที่พึงมีตามแผน 4 ปีเดิม</th>
                                            <th>ปี 2567 (ปีที่ 1)</th>
                                            <th>ปี 2568 (ปีที่ 2)</th>
                                            <th>ปี 2569 (ปีที่ 3)</th>
                                            <th>ปี 2570 (ปีที่ 4)</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>20</td>
                                                <td>25</td>
                                                <td>22</td>
                                                <td>24</td>
                                                <td>26</td>
                                                <td>28</td>
                                                <td>30</td>
                                                <td>35</td>
                                                <td>32</td>
                                                <td>34</td>
                                                <td>36</td>
                                                <td>38</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>15</td>
                                                <td>20</td>
                                                <td>18</td>
                                                <td>19</td>
                                                <td>21</td>
                                                <td>23</td>
                                                <td>25</td>
                                                <td>30</td>
                                                <td>28</td>
                                                <td>29</td>
                                                <td>31</td>
                                                <td>33</td>
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
                    'command': 'kku_wf_framework-summary'
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
                                { key: 'wf1', value: ""},
                                { key: 'wf_type1_y1', value: row.wf_type1_y1 },
                                { key: 'wf_type2_y1', value: row.wf_type2_y1 },
                                { key: 'wf_type3_y1', value: row.wf_type3_y1 },
                                { key: 'wf_type4_y1', value: row.wf_type4_y1 },
                                
                                { key: 'Actual_type2', value: row.Actual_type1 },
                                { key: 'wf2', value: ""},
                                { key: 'wf_type1_y2', value: row.wf_type1_y2 },
                                { key: 'wf_type2_y2', value: row.wf_type2_y2 },
                                { key: 'wf_type3_y2', value: row.wf_type3_y2 },
                                { key: 'wf_type4_y2', value: row.wf_type4_y2 },

                                { key: 'Actual_type3', value: row.Actual_type1 },
                                { key: 'wf3', value: ""},
                                { key: 'wf_type1_y3', value: row.wf_type1_y3 },
                                { key: 'wf_type2_y3', value: row.wf_type2_y3 },
                                { key: 'wf_type3_y3', value: row.wf_type3_y3 },
                                { key: 'wf_type4_y3', value: row.wf_type4_y3 },

                                { key: 'Actual_type4', value: row.Actual_type4 },
                                { key: 'wf4', value: ""},
                                { key: 'wf_type1_y4', value: row.wf_type1_y4 },
                                { key: 'wf_type2_y4', value: row.wf_type2_y4 },
                                { key: 'wf_type3_y4', value: row.wf_type3_y4 },
                                { key: 'wf_type4_y4', value: row.wf_type4_y4 },
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