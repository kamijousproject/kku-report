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
                        <h4>รายงานเปรียบเทียบงประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานเปรียบเทียบงประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานเปรียบเทียบงประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th>หน่วยรับงบจัดสรร</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="6">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th rowspan="2">เพิ่ม/ลด</th>
                                                <th rowspan="2">คำชี้แจง</th>
                                            </tr>
                                            <tr>
                                                <th>ปริมาณของงบจัดสรร</th>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                                <th>รวม</th>
                                                <th>เงินอุดหนุนจากรัฐ (คำขอ)</th>
                                                <th>เงินอุดหนุนจากรัฐ (จัดสรร)</th>
                                                <th>เงินนอกงบประมาณ (คำขอ)</th>
                                                <th>เงินนอกงบประมาณ (จัดสรร)</th>
                                                <th>เงินรายได้ (คำขอ)</th>
                                                <th>เงินรายได้ (จัดสรร)</th>
                                                <th>รวม</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>แผนงาน (ผลผลิต) [Plan]</td>
                                                <td>หน่วยงาน A</td>
                                                <td>5,000</td>
                                                <td>2,000</td>
                                                <td>3,000</td>
                                                <td>10,000</td>
                                                <td>6,000</td>
                                                <td>5,800</td>
                                                <td>2,500</td>
                                                <td>2,400</td>
                                                <td>3,200</td>
                                                <td>3,100</td>
                                                <td>11,300</td>
                                                <td>+1,300</td>
                                                <td>เพิ่มเพื่อรองรับโครงการใหม่</td>
                                            </tr>
                                            <tr>
                                                <td>แผนงานย่อย (ผลผลิตย่อย/กิจกรรม) [Sub plan]</td>
                                                <td>หน่วยงาน B</td>
                                                <td>4,000</td>
                                                <td>1,500</td>
                                                <td>2,500</td>
                                                <td>8,000</td>
                                                <td>4,500</td>
                                                <td>4,300</td>
                                                <td>1,800</td>
                                                <td>1,700</td>
                                                <td>2,800</td>
                                                <td>2,700</td>
                                                <td>9,000</td>
                                                <td>+1,000</td>
                                                <td>เพิ่มเพื่อการพัฒนาโครงสร้างพื้นฐาน</td>
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