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
                        <h4>รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานอัตรากำลังประเภทต่างๆ ของหน่วยงาน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ที่</th>
                                                <th>ส่วนงาน/หน่วยงาน</th>
                                                <th colspan="3">ข้าราชการ</th>
                                                <th colspan="3">พนักงานมหาวิทยาลัยงบประมาณแผ่นดิน</th>
                                                <th colspan="3">พนักงานมหาวิทยาลัยงบประมาณเงินรายได้</th>
                                                <th colspan="3">ลูกจ้างมหาวิทยาลัย</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>วิชาการ (คน/อัตรา)</th>
                                                <th>สนับสนุน (คน/อัตรา)</th>
                                                <th>รวม</th>
                                                <th>วิชาการ (คน/อัตรา)</th>
                                                <th>สนับสนุน (คน/อัตรา)</th>
                                                <th>รวม</th>
                                                <th>วิชาการ (คน/อัตรา)</th>
                                                <th>สนับสนุน (คน/อัตรา)</th>
                                                <th>รวม</th>
                                                <th>วิชาการ (คน/อัตรา)</th>
                                                <th>สนับสนุน (คน/อัตรา)</th>
                                                <th>รวม</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>หน่วยงาน A</td>
                                                <td>5</td>
                                                <td>3</td>
                                                <td>8</td>
                                                <td>10</td>
                                                <td>5</td>
                                                <td>15</td>
                                                <td>8</td>
                                                <td>6</td>
                                                <td>14</td>
                                                <td>4</td>
                                                <td>2</td>
                                                <td>6</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>หน่วยงาน B</td>
                                                <td>6</td>
                                                <td>4</td>
                                                <td>10</td>
                                                <td>8</td>
                                                <td>6</td>
                                                <td>14</td>
                                                <td>7</td>
                                                <td>5</td>
                                                <td>12</td>
                                                <td>3</td>
                                                <td>1</td>
                                                <td>4</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2">รวมทั้งหมด</td>
                                                <td>11</td>
                                                <td>7</td>
                                                <td>18</td>
                                                <td>18</td>
                                                <td>11</td>
                                                <td>29</td>
                                                <td>15</td>
                                                <td>11</td>
                                                <td>26</td>
                                                <td>7</td>
                                                <td>3</td>
                                                <td>10</td>
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