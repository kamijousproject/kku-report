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
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>ส่วนงาน</th>
                                                <th colspan="2">ประเภทบริหาร</th>
                                                <th colspan="4">ประเภทวิชาการ</th>
                                                <th colspan="2">ประเภทวิจัย</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>อัตราปัจจุบัน</th>
                                                <th>กรอบที่ตั้ง</th>
                                                <th>อัตราปัจจุบัน</th>
                                                <th>กรอบที่ตั้ง</th>
                                                <th>เกณฑ์ FTES</th>
                                                <th>รวมวิชาการ</th>
                                                <th>เกณฑ์การวิจัย</th>
                                                <th>รวมวิจัย</th>
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
                                            <tr>
                                                <td colspan="2">รวมทั้งหมด</td>
                                                <td>35</td>
                                                <td>45</td>
                                                <td>55</td>
                                                <td>65</td>
                                                <td>27</td>
                                                <td>82</td>
                                                <td>18</td>
                                                <td>38</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>

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
    </script>
    <!-- Common JS -->
    <script src="../../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>