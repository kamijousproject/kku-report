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
                        <h4>รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานผลการขอนุมัติกรอบอัตรากำลัง รายส่วนงาน/หน่วยงาน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>คุณวุฒิ</th>
                                                <th>เลขประจำตำแหน่ง</th>
                                                <th>สถานที่ปฏิบัติงาน</th>
                                                <th>อัตราเงินเดือน</th>
                                                <th>แหล่งงบประมาณ</th>
                                                <th>ประเภทสัญญา</th>
                                                <th>ระยะเวลาสัญญา</th>
                                                <th>หมายเหตุอื่นๆ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>ข้าราชการ</td>
                                                <td>วิชาการ</td>
                                                <td>อาจารย์</td>
                                                <td>ปริญญาเอก</td>
                                                <td>12345</td>
                                                <td>กรุงเทพฯ</td>
                                                <td>50,000</td>
                                                <td>งบประมาณแผ่นดิน</td>
                                                <td>ประจำ</td>
                                                <td>ไม่กำหนด</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>พนักงานมหาวิทยาลัย</td>
                                                <td>สนับสนุน</td>
                                                <td>เจ้าหน้าที่วิจัย</td>
                                                <td>ปริญญาโท</td>
                                                <td>67890</td>
                                                <td>เชียงใหม่</td>
                                                <td>35,000</td>
                                                <td>รายได้มหาวิทยาลัย</td>
                                                <td>สัญญาจ้าง</td>
                                                <td>1 ปี</td>
                                                <td>มีการต่อสัญญา</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
    <!-- Common JS -->
    <script src="../../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>