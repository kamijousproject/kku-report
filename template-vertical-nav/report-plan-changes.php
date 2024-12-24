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
                        <h4>รายงานการปรับเปลี่ยนแผนงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการปรับเปลี่ยนแผนงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการปรับเปลี่ยนแผนงาน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ยุทธศาสตร์</th>
                                                <th>กลยุทธ์</th>
                                                <th colspan="2">โครงการตามยุทธศาสตร์</th>
                                                <th>ผู้รับผิดชอบ</th>
                                                <th>เหตุผลการปรับเปลี่ยนแผนงาน</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>แผนงาน/โครงการ</th>
                                                <th>กรอบวงเงินงบประมาณ</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>ยุทธศาสตร์ที่ 1</td>
                                                <td>กลยุทธ์ที่ 1.1</td>
                                                <td>โครงการตัวอย่าง 1</td>
                                                <td class="color-primary">1,000,000 บาท</td>
                                                <td>ผู้รับผิดชอบ A</td>
                                                <td>เหตุผลปรับเปลี่ยน A</td>
                                            </tr>
                                            <tr>
                                                <td>ยุทธศาสตร์ที่ 2</td>
                                                <td>กลยุทธ์ที่ 2.1</td>
                                                <td>โครงการตัวอย่าง 2</td>
                                                <td class="color-success">2,000,000 บาท</td>
                                                <td>ผู้รับผิดชอบ B</td>
                                                <td>เหตุผลปรับเปลี่ยน B</td>
                                            </tr>
                                            <tr>
                                                <td>ยุทธศาสตร์ที่ 3</td>
                                                <td>กลยุทธ์ที่ 3.1</td>
                                                <td>โครงการตัวอย่าง 3</td>
                                                <td class="color-danger">500,000 บาท</td>
                                                <td>ผู้รับผิดชอบ C</td>
                                                <td>เหตุผลปรับเปลี่ยน C</td>
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