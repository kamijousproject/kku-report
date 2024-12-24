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
                        <h4>รายงานสรุปผลการจัดสรรกรอบอตัรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปผลการจัดสรรกรอบอตัรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปผลการจัดสรรกรอบอตัรากำลังทุกประเภทภาพรวมของมหาวิทยาลัย</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>ปีงบประมาณที่จัดสรร</th>
                                                <th>ประเภทอัตรา</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ชื่อ - นามสกุล</th>
                                                <th>ประเภทการจ้าง</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>กลุ่มตำแหน่ง</th>
                                                <th>Job Family</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>คุณวุฒิของตำแหน่ง</th>
                                                <th>เลขประจำตำแหน่ง</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>2567</td>
                                                <td>พนักงานประจำ</td>
                                                <td>วิชาการ</td>
                                                <td>สมชาย ใจดี</td>
                                                <td>สัญญาประจำ</td>
                                                <td>อาจารย์</td>
                                                <td>วิทยาศาสตร์</td>
                                                <td>Teaching</td>
                                                <td>ผู้ช่วยศาสตราจารย์</td>
                                                <td>ปริญญาเอก</td>
                                                <td>12345</td>
                                            </tr>
                                            <tr>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>2567</td>
                                                <td>พนักงานชั่วคราว</td>
                                                <td>สนับสนุน</td>
                                                <td>สมหญิง สมศรี</td>
                                                <td>สัญญาจ้าง</td>
                                                <td>เจ้าหน้าที่วิจัย</td>
                                                <td>วิศวกรรม</td>
                                                <td>Research</td>
                                                <td>นักวิจัย</td>
                                                <td>ปริญญาโท</td>
                                                <td>67890</td>
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