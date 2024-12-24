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
                        <h4>รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รรายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th colspan="3">ปี 2566</th>
                                                <th colspan="3">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="3">ปี 2568 (ปีที่ขอตั้ง)</th>
                                                <th rowspan="2">เพิ่ม/ลด</th>
                                                <th rowspan="2">คำชี้แจง</th>
                                            </tr>
                                            <tr>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>รวม</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>รวม</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>รวม</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>แผนงาน (ผลผลิต) [Plan]</td>
                                                <td>10,000</td>
                                                <td>8,000</td>
                                                <td>18,000</td>
                                                <td>12,000</td>
                                                <td>10,000</td>
                                                <td>22,000</td>
                                                <td>14,000</td>
                                                <td>12,000</td>
                                                <td>26,000</td>
                                                <td>+4,000</td>
                                                <td>รองรับโครงการเพิ่มเติม</td>
                                            </tr>
                                            <tr>
                                                <td>แผนงานย่อย (ผลผลิตย่อย/กิจกรรม) [Sub plan]</td>
                                                <td>7,000</td>
                                                <td>6,000</td>
                                                <td>13,000</td>
                                                <td>9,000</td>
                                                <td>7,500</td>
                                                <td>16,500</td>
                                                <td>11,000</td>
                                                <td>8,000</td>
                                                <td>19,000</td>
                                                <td>+2,500</td>
                                                <td>เพิ่มการลงทุนในระบบ</td>
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