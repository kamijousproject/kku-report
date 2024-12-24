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
                        <h4>รายงานแผนงานระดับต่าง ๆ ของหน่วยงาน (มหาวิทยาลัย)</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานแผนงานระดับต่าง ๆ ของหน่วยงาน (มหาวิทยาลัย)</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายละเอียดแผนงาน/โครงการ</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>กลยุทธ์</th>
                                                <th>รหัส</th>
                                                <th>ผลลัพธ์สำคัญ</th>
                                                <th>ค่าเป้าหมาย</th>
                                                <th>หน่วยนับ</th>
                                                <th>รหัส</th>
                                                <th>แผนงาน/โครงการ</th>
                                                <th>กรอบวงเงินงบประมาณ</th>
                                                <th colspan="2">ระยะเวลาที่ดำเนินการ</th>
                                                <th>ระดับและการปรับใช้</th>
                                                <th>ผู้รับผิดชอบ</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>วันเริ่มต้น</th>
                                                <th>วันสิ้นสุด</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>กลยุทธ์ที่ 1</td>
                                                <td>001</td>
                                                <td>เพิ่มประสิทธิภาพ</td>
                                                <td>100%</td>
                                                <td>เปอร์เซ็นต์</td>
                                                <td>PRJ01</td>
                                                <td>โครงการเพิ่มประสิทธิภาพ</td>
                                                <td class="color-primary">1,000,000 บาท</td>
                                                <td>01/01/2024</td>
                                                <td>31/12/2024</td>
                                                <td>ระดับ A</td>
                                                <td>ทีมงาน A</td>
                                            </tr>
                                            <tr>
                                                <td>กลยุทธ์ที่ 2</td>
                                                <td>002</td>
                                                <td>พัฒนาคุณภาพ</td>
                                                <td>85%</td>
                                                <td>เปอร์เซ็นต์</td>
                                                <td>PRJ02</td>
                                                <td>โครงการพัฒนาคุณภาพ</td>
                                                <td class="color-success">2,500,000 บาท</td>
                                                <td>01/03/2024</td>
                                                <td>30/11/2024</td>
                                                <td>ระดับ B</td>
                                                <td>ทีมงาน B</td>
                                            </tr>
                                            <tr>
                                                <td>กลยุทธ์ที่ 3</td>
                                                <td>003</td>
                                                <td>ขยายตลาด</td>
                                                <td>70%</td>
                                                <td>เปอร์เซ็นต์</td>
                                                <td>PRJ03</td>
                                                <td>โครงการขยายตลาด</td>
                                                <td class="color-danger">3,000,000 บาท</td>
                                                <td>01/05/2024</td>
                                                <td>31/10/2024</td>
                                                <td>ระดับ C</td>
                                                <td>ทีมงาน C</td>
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