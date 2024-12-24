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
                        <h4>รายงานสรุปรายการตัวชี้วัดแผน/ผลของแผนงานย่อย</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปรายการตัวชี้วัดแผน/ผลของแผนงานย่อย</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายการ ตัวชี้วัดแผน/ผลของแผนงานย่อย</h4>
                                    <p>ส่วนงาน/หน่วยงาน: คณะบริหารธุรกิจ</p>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th rowspan="2">หน่วยนับของตัวชี้วัด</th>
                                                <th colspan="6">ปี 2567 (ปีก่อน)</th>
                                                <th colspan="6">ปี 2568 (ปีปัจจุบัน)</th>
                                            </tr>
                                            <tr>
                                                <th>ค่าเป้าหมาย</th>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
                                                <th>ผลรวมของตัวชี้วัด</th>
                                                <th>ค่าเป้าหมาย</th>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
                                                <th>ผลของตัวชี้วัด</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>แผนงาน (ผลผลิต) [Plan]</td>
                                                <td>โครงการ</td>
                                                <td>10</td>
                                                <td>2</td>
                                                <td>3</td>
                                                <td>2</td>
                                                <td>3</td>
                                                <td>10</td>
                                                <td>12</td>
                                                <td>3</td>
                                                <td>3</td>
                                                <td>3</td>
                                                <td>3</td>
                                                <td>12</td>
                                            </tr>
                                            <tr>
                                                <td>แผนงานย่อย (ผลผลิตย่อย/กิจกรรม) [Sub plan]</td>
                                                <td>กิจกรรม</td>
                                                <td>20</td>
                                                <td>5</td>
                                                <td>5</td>
                                                <td>5</td>
                                                <td>5</td>
                                                <td>20</td>
                                                <td>25</td>
                                                <td>6</td>
                                                <td>6</td>
                                                <td>6</td>
                                                <td>7</td>
                                                <td>25</td>
                                            </tr>
                                            <tr>
                                                <td>โครงการ/กิจกรรม [Project]</td>
                                                <td>หน่วย</td>
                                                <td>15</td>
                                                <td>4</td>
                                                <td>4</td>
                                                <td>3</td>
                                                <td>4</td>
                                                <td>15</td>
                                                <td>18</td>
                                                <td>5</td>
                                                <td>5</td>
                                                <td>4</td>
                                                <td>4</td>
                                                <td>18</td>
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