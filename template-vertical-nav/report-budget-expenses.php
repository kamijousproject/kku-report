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
                        <h4>รายงานการใช้จ่ายงบประมาณตามแผนงาน</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการใช้จ่ายงบประมาณตามแผนงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการใช้จ่ายงบประมาณตามแผนงาน</h4>
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
                                                <th>รหัส</th>
                                                <th>แผนงาน/โครงการ</th>
                                                <th>รหัส</th>
                                                <th>ผลลัพธ์สำคัญ</th>
                                                <th>หน่วยนับ</th>
                                                <th colspan="4">ผลการดำเนินงาน</th>
                                                <th colspan="4">ค่าเป้าหมาย (ปี)</th>
                                                <th>กรอบวงเงินงบประมาณ (บาท)</th>
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
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>2564</th>
                                                <th>2565</th>
                                                <th>2566</th>
                                                <th>ค่าเฉลี่ย</th>
                                                <th>2567</th>
                                                <th>2568</th>
                                                <th>2569</th>
                                                <th>2570</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>001</td>
                                                <td>หน่วยงาน A</td>
                                                <td>S001</td>
                                                <td>เสาหลักที่ 1</td>
                                                <td>ST001</td>
                                                <td>ยุทธศาสตร์ที่ 1</td>
                                                <td>G001</td>
                                                <td>กลยุทธ์ที่ 1</td>
                                                <td>เพิ่มผลผลิต</td>
                                                <td>PJ001</td>
                                                <td>โครงการพัฒนา</td>
                                                <td>R001</td>
                                                <td>ผลลัพธ์ A</td>
                                                <td>เปอร์เซ็นต์</td>
                                                <td>80%</td>
                                                <td>85%</td>
                                                <td>90%</td>
                                                <td>85%</td>
                                                <td>88%</td>
                                                <td>90%</td>
                                                <td>92%</td>
                                                <td>95%</td>
                                                <td>1,500,000</td>
                                                <td>ทีม A</td>
                                            </tr>
                                            <tr>
                                                <td>002</td>
                                                <td>หน่วยงาน B</td>
                                                <td>S002</td>
                                                <td>เสาหลักที่ 2</td>
                                                <td>ST002</td>
                                                <td>ยุทธศาสตร์ที่ 2</td>
                                                <td>G002</td>
                                                <td>กลยุทธ์ที่ 2</td>
                                                <td>พัฒนาคุณภาพ</td>
                                                <td>PJ002</td>
                                                <td>โครงการปรับปรุง</td>
                                                <td>R002</td>
                                                <td>ผลลัพธ์ B</td>
                                                <td>หน่วย</td>
                                                <td>70</td>
                                                <td>75</td>
                                                <td>80</td>
                                                <td>75</td>
                                                <td>78</td>
                                                <td>80</td>
                                                <td>85</td>
                                                <td>90</td>
                                                <td>2,000,000</td>
                                                <td>ทีม B</td>
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