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
                        <h4>รายงานผลการตัดโอน – เปลี่ยนตำแหน่ง</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานผลการตัดโอน – เปลี่ยนตำแหน่ง</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานผลการตัดโอน – เปลี่ยนตำแหน่ง</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>การเปลี่ยนแปลงอัตรา</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>เลขประจำตำแหน่ง</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>สถานะอัตรา</th>
                                                <th colspan="2">กรณีเปลี่ยนตำแหน่ง</th>
                                                <th colspan="2">กรณีตัดโอนอัตรา</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>ชื่อตำแหน่งเดิม</th>
                                                <th>ชื่อตำแหน่งใหม่</th>
                                                <th>ส่วนงาน/หน่วยงาน 1</th>
                                                <th>ส่วนงาน/หน่วยงาน 2</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>ปรับเปลี่ยน</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>12345</td>
                                                <td>วิชาการ</td>
                                                <td>ว่าง</td>
                                                <td>อาจารย์</td>
                                                <td>ผู้ช่วยศาสตราจารย์</td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>ตัดโอน</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>67890</td>
                                                <td>สนับสนุน</td>
                                                <td>เต็ม</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>คณะเทคโนโลยี</td>
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