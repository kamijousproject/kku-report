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
                                                <th>ที่</th>
                                                <th>ส่วนงาน</th>
                                                <th colspan="5">ประเภทบริหาร</th>
                                                <th colspan="5">ประเภทวิชาการ</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>อัตราปัจจุบัน</th>
                                                <th>กรอบที่ตั้ง</th>
                                                <th>ปี 2567 (ปีที่ 1)</th>
                                                <th>ปี 2568 (ปีที่ 2)</th>
                                                <th>ปี 2569 (ปีที่ 3)</th>
                                                <th>ปี 2570 (ปีที่ 4)</th>
                                                <th>อัตราปัจจุบัน</th>
                                                <th>กรอบที่ตั้ง</th>
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
                                            <tr>
                                                <td colspan="2">รวมทั้งหมด</td>
                                                <td>35</td>
                                                <td>45</td>
                                                <td>40</td>
                                                <td>43</td>
                                                <td>47</td>
                                                <td>51</td>
                                                <td>55</td>
                                                <td>65</td>
                                                <td>60</td>
                                                <td>63</td>
                                                <td>67</td>
                                                <td>71</td>
                                            </tr>
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