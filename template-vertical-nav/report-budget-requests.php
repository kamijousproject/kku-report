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
                        <h4>รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขอตามส่วนงาน/หน่วยงานและแหล่งงบประมาณ</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ส่วนงาน</th>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>หน่วยงาน A</td>
                                                <td>2,000,000</td>
                                                <td>1,500,000</td>
                                                <td>500,000</td>
                                            </tr>
                                            <tr>
                                                <td>หน่วยงาน B</td>
                                                <td>3,000,000</td>
                                                <td>2,000,000</td>
                                                <td>700,000</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="section">
                                        <p>หน่วยงาน: ........................................</p>
                                        <p>แผนงาน(ผลผลิต): ........................................</p>
                                        <p>แผนงานย่อย(ผลผลิตย่อย/กิจกรรม): ........................................</p>
                                        <p>โครงการ/กิจกรรม: ........................................</p>
                                        <p>งบรายจ่าย: [Expenses Code / Name]</p>
                                        <p>รายการรายจ่าย: [Item Name]</p>
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