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
                        <h4>รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานกรอบอัตรากำลังตามงบประมาณประเภทต่างๆ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายละเอียดงบประมาณและจำนวนอัตรากำลัง</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th colspan="2">ประเภทบริหาร</th>
                                                <th colspan="2">ประเภทวิชาการ</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                                <th>จำนวน (อัตรา)</th>
                                                <th>งบประมาณ (บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>หน่วยงาน A</td>
                                                <td>10</td>
                                                <td>1,000,000</td>
                                                <td>15</td>
                                                <td>1,500,000</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>หน่วยงาน B</td>
                                                <td>8</td>
                                                <td>800,000</td>
                                                <td>12</td>
                                                <td>1,200,000</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2">รวมทั้งหมด</td>
                                                <td>18</td>
                                                <td>1,800,000</td>
                                                <td>27</td>
                                                <td>2,700,000</td>
                                            </tr>
                                        </tfoot>
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