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
                        <h4>รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการจัดสรรกรอบอัตรากำลัง ประจำปีงบประมาณ</h4>
                                    <p>ประเภทการจัดสรร: ............................................</p>
                                    <p>ส่วนงาน/หน่วยงาน: ............................................</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ลำดับที่</th>
                                                <th>ประเภทการจัดสรร</th>
                                                <th>ส่วนงาน</th>
                                                <th>หน่วยงาน</th>
                                                <th>ชื่อ - นามสกุล</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ประเภทการจ้าง</th>
                                                <th>เลขประจำตำแหน่ง</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>จำนวนจัดสรร</th>
                                                <th>Job Family</th>
                                                <th>ประเภทตำแหน่ง</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>ใหม่</td>
                                                <td>คณะวิทยาศาสตร์</td>
                                                <td>ภาควิชาเคมี</td>
                                                <td>สมชาย ใจดี</td>
                                                <td>วิชาการ</td>
                                                <td>สัญญาประจำ</td>
                                                <td>12345</td>
                                                <td>อาจารย์</td>
                                                <td>1</td>
                                                <td>Teaching</td>
                                                <td>วิทยาศาสตร์</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>แทนตำแหน่งเดิม</td>
                                                <td>คณะวิศวกรรมศาสตร์</td>
                                                <td>ภาควิชาวิศวกรรมเครื่องกล</td>
                                                <td>สมหญิง สมศรี</td>
                                                <td>สนับสนุน</td>
                                                <td>สัญญาจ้าง</td>
                                                <td>67890</td>
                                                <td>นักวิจัย</td>
                                                <td>1</td>
                                                <td>Research</td>
                                                <td>วิศวกรรม</td>
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