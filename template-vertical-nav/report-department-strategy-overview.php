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
                        <h4>รายงานภาพรวมยุทธศาสตร์ ส่วนงาน/หน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานภาพรวมยุทธศาสตร์ ส่วนงาน/หน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานภาพรวมยุทธศาสตร์ ส่วนงาน/หน่วยงาน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>รหัส</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>รหัส</th>
                                                <th>เสาหลัก</th>
                                                <th>รหัส</th>
                                                <th>ยุทธศาสตร์</th>
                                                <th>รหัส</th>
                                                <th>กลยุทธ์</th>
                                                <th>เป้าหมายของกลยุทธ์</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>001</td>
                                                <td>หน่วยงาน A</td>
                                                <td>ST001</td>
                                                <td>เสาหลักที่ 1</td>
                                                <td>YS001</td>
                                                <td>ยุทธศาสตร์ที่ 1</td>
                                                <td>G001</td>
                                                <td>กลยุทธ์ที่ 1</td>
                                                <td>เพิ่มผลผลิต</td>
                                            </tr>
                                            <tr>
                                                <td>002</td>
                                                <td>หน่วยงาน B</td>
                                                <td>ST002</td>
                                                <td>เสาหลักที่ 2</td>
                                                <td>YS002</td>
                                                <td>ยุทธศาสตร์ที่ 2</td>
                                                <td>G002</td>
                                                <td>กลยุทธ์ที่ 2</td>
                                                <td>พัฒนาคุณภาพ</td>
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