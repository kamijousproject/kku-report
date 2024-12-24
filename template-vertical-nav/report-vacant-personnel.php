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
                        <h4>รายงานอัตรากำลังว่าง</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานอัตรากำลังว่าง</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานอัตรากำลังว่าง</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th>ประเภทบุคลากร</th>
                                                <th>ประเภทตำแหน่ง</th>
                                                <th>กลุ่มบุคลากร</th>
                                                <th>เลขอัตรา</th>
                                                <th>ชื่อตำแหน่ง</th>
                                                <th>สถานที่ปฏิบัติงาน</th>
                                                <th>Job Family</th>
                                                <th>จำนวนที่ว่าง</th>
                                                <th>ว่าง ณ วันที่/เดือน/ปี</th>
                                                <th>สาเหตุที่ว่าง</th>
                                                <th>สถานะอัตราว่าง</th>
                                                <th>ครบระยะเวลาว่าง 6 เดือน ณ วันที่/เดือน/ปี</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>หน่วยงาน A</td>
                                                <td>ข้าราชการ</td>
                                                <td>วิชาการ</td>
                                                <td>กลุ่ม A</td>
                                                <td>12345</td>
                                                <td>อาจารย์</td>
                                                <td>กรุงเทพฯ</td>
                                                <td>Teaching</td>
                                                <td>2</td>
                                                <td>01/01/2567</td>
                                                <td>เกษียณอายุ</td>
                                                <td>อยู่ระหว่างสรรหา</td>
                                                <td>01/07/2567</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>หน่วยงาน B</td>
                                                <td>พนักงานมหาวิทยาลัย</td>
                                                <td>สนับสนุน</td>
                                                <td>กลุ่ม B</td>
                                                <td>67890</td>
                                                <td>นักวิชาการ</td>
                                                <td>เชียงใหม่</td>
                                                <td>Research</td>
                                                <td>1</td>
                                                <td>01/02/2567</td>
                                                <td>ลาออก</td>
                                                <td>กำลังพิจารณา</td>
                                                <td>01/08/2567</td>
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