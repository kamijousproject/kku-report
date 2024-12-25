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
                        <h4>รายงานข้อมูลกรอบอัตรากำลัง(จากระบบHCM) เพื่อนำไปตั้งงบประมาณขอไฟล์ database จากระบบ HCM</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานข้อมูลกรอบอัตรากำลัง</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานข้อมูลกรอบอัตรากำลัง</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>ชื่อ-สกุล</th>
                                                <th>รหัส</th>
                                                <th>เลขที่อัตรา</th>
                                                <th>เลขบัตรประจำตัวประชาชน</th>
                                                <th>สถานะอัตรา</th>
                                                <th>วันบรรจุ</th>
                                                <th>วันเกษียณ</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>สังกัด</th>
                                                <th>ระดับการศึกษา</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>แหล่งเงิน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>นายสมชาย ใจดี</td>
                                                <td>001</td>
                                                <td>12345</td>
                                                <td>1234567890123</td>
                                                <td>ประจำ</td>
                                                <td>01/01/2015</td>
                                                <td>31/12/2035</td>
                                                <td>อาจารย์</td>
                                                <td>วิชาการ</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>ปริญญาโท</td>
                                                <td>พนักงานมหาวิทยาลัย</td>
                                                <td>เงินรายได้</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>นางสาวสุดา แสนสุข</td>
                                                <td>002</td>
                                                <td>54321</td>
                                                <td>9876543210987</td>
                                                <td>ประจำ</td>
                                                <td>01/01/2016</td>
                                                <td>31/12/2036</td>
                                                <td>นักวิชาการ</td>
                                                <td>วิชาการ</td>
                                                <td>คณะมนุษยศาสตร์</td>
                                                <td>ปริญญาเอก</td>
                                                <td>พนักงานราชการ</td>
                                                <td>งบประมาณแผ่นดิน</td>
                                            </tr>
                                        </tbody>
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