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
                        <h4>รายงานอัตรากำลังที่เกษียณอายุในแต่ละช่วงเวลา</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานอัตรากำลังที่เกษียณอายุในแต่ละช่วงเวลา</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานอัตรากำลังที่เกษียณอายุในแต่ละช่วงเวลา</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ชื่อ - นามสกุล</th>
                                                <th>ตำแหน่งชื่อ</th>
                                                <th>เลขประจำตำแหน่ง</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>Job Family</th>
                                                <th>ปีที่เกษียณอายุราชการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>หน่วยงาน A</td>
                                                <td>ข้าราชการ</td>
                                                <td>สมชาย ใจดี</td>
                                                <td>อาจารย์</td>
                                                <td>12345</td>
                                                <td>วิชาการ</td>
                                                <td>Teaching</td>
                                                <td>2568</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>หน่วยงาน B</td>
                                                <td>พนักงานมหาวิทยาลัย</td>
                                                <td>สมหญิง สมศรี</td>
                                                <td>นักวิจัย</td>
                                                <td>67890</td>
                                                <td>สนับสนุน</td>
                                                <td>Research</td>
                                                <td>2570</td>
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