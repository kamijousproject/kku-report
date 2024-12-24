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
                        <h4>รายงานการสรุป คำขออัตราใหม่และอัตราเดิม (หลังจากส่วนงาน/หน่วยงาน กรอกคำขออัตราเดิมและอัตราใหม่)</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการสรุป คำขออัตราใหม่และอัตราเดิม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>(หลังจากส่วนงาน/หน่วยงาน กรอกคำขออัตราเดิมและอัตราใหม่)</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>ประเภทจ้าง</th>
                                                <th>ชื่อ - นามสกุล</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ประเภทการจ้าง</th>
                                                <th>เลขประจำตำแหน่ง</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>จำนวนที่ขอ</th>
                                                <th>Job Family</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>กลุ่มบุคลากร</th>
                                                <th>ประเภทสัญญา</th>
                                                <th>ระยะเวลาสัญญา</th>
                                                <th>คุณวุฒิของตำแหน่ง</th>
                                                <th>สถานะอัตรา</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>สัญญาประจำ</td>
                                                <td>สมชาย ใจดี</td>
                                                <td>วิชาการ</td>
                                                <td>ประจำ</td>
                                                <td>12345</td>
                                                <td>อาจารย์</td>
                                                <td>1</td>
                                                <td>Teaching</td>
                                                <td>วิชาการ</td>
                                                <td>บุคลากรสายวิชาการ</td>
                                                <td>ประจำ</td>
                                                <td>5 ปี</td>
                                                <td>ปริญญาเอก</td>
                                                <td>ว่าง</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>สัญญาจ้าง</td>
                                                <td>สมหญิง สมศรี</td>
                                                <td>สนับสนุน</td>
                                                <td>ชั่วคราว</td>
                                                <td>67890</td>
                                                <td>นักวิจัย</td>
                                                <td>1</td>
                                                <td>Research</td>
                                                <td>สนับสนุน</td>
                                                <td>บุคลากรสายสนับสนุน</td>
                                                <td>ชั่วคราว</td>
                                                <td>1 ปี</td>
                                                <td>ปริญญาโท</td>
                                                <td>เต็ม</td>
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