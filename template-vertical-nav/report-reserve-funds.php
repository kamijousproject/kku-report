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
                        <h4>รายงานสรุปบัญชีทุนสำรองสะสม</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปบัญชีทุนสำรองสะสม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปบัญชีทุนสำรองสะสม</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>รหัสบัญชี</th>
                                                <th>ชื่อบัญชี</th>
                                                <th>รหัส GF</th>
                                                <th>ชื่อบัญชี GF</th>
                                                <th colspan="2">ยอดยกมา</th>
                                                <th colspan="2">ประจำงวด</th>
                                                <th colspan="2">ยอดยกไป</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>เดบิต</th>
                                                <th>เครดิต</th>
                                                <th>เดบิต</th>
                                                <th>เครดิต</th>
                                                <th>เดบิต</th>
                                                <th>เครดิต</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>315-01-00</td>
                                                <td>บัญชีทุนสำรองสะสม - สนง.อธิการบดี</td>
                                                <td>3105010101</td>
                                                <td>บัญชีทุนของหน่วยงาน</td>
                                                <td>1,000,000</td>
                                                <td>500,000</td>
                                                <td>300,000</td>
                                                <td>200,000</td>
                                                <td>1,200,000</td>
                                                <td>700,000</td>
                                            </tr>
                                            <tr>
                                                <td>315-02-00</td>
                                                <td>บัญชีทุนสำรองสะสม - สำนักหอสมุด</td>
                                                <td>3105010101</td>
                                                <td>บัญชีทุนของหน่วยงาน</td>
                                                <td>1,200,000</td>
                                                <td>600,000</td>
                                                <td>400,000</td>
                                                <td>300,000</td>
                                                <td>1,500,000</td>
                                                <td>900,000</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="section">
                                        <p>ปีงบประมาณ: ........................................</p>
                                        <p>ปีบริหารงบประมาณ: ........................................</p>
                                        <p>แหล่งเงิน: ........................................</p>
                                    </div>
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