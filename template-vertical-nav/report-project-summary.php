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
                        <h4>รายงานสรุปรายโครงการ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปรายโครงการ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายโครงการ</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ปีงบประมาณ</th>
                                                <th>ปีบริหารงบประมาณ</th>
                                                <th>ประเภทงบประมาณ</th>
                                                <th>แหล่งเงิน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2567</td>
                                                <td>แผนประจำปี</td>
                                                <td>เงินอุดหนุน</td>
                                                <td>งบประมาณรัฐ</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>โครงการ/กิจกรรม</th>
                                                <th colspan="4">1. ค่าใช้จ่ายบุคลากร</th>
                                                <th>เงินสมทบประกันสังคม</th>
                                                <th>เงินสมทบกองทุน</th>
                                                <th>สวัสดิการอื่น ๆ</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th>1.1 เงินเดือน</th>
                                                <th>1.2 ค่าจ้างพนักงาน</th>
                                                <th>1.3 ค่าจ้างลูกจ้าง</th>
                                                <th>1.4 เงินกองทุน</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>โครงการพัฒนาทักษะบุคลากร</td>
                                                <td>1,000,000</td>
                                                <td>500,000</td>
                                                <td>300,000</td>
                                                <td>200,000</td>
                                                <td>100,000</td>
                                                <td>150,000</td>
                                                <td>50,000</td>
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