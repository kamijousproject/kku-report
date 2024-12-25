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
                        <h4>รายงานผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานผลการดำเนินงานตามแผนปฏิบัติการประจำปีงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>(จำแนกตามประเด็นยุทธศาสตร์-ระดับ ส่วนงาน/หน่วยงาน)</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>เสาหลัก</th>
                                                <th>ยุทธศาสตร์</th>
                                                <th colspan="3">จำนวน</th>
                                                <th>ร้อยละ ความสำเร็จ</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>กลยุทธ์</th>
                                                <th>ผลลัพธ์ตามวัตถุประสงค์</th>
                                                <th>แผนงาน/โครงการ</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>เสาหลักที่ 1</td>
                                                <td>ยุทธศาสตร์ที่ 1</td>
                                                <td>กลยุทธ์ที่ 1</td>
                                                <td>เพิ่มผลผลิต</td>
                                                <td>โครงการพัฒนา</td>
                                                <td>87.5%</td>
                                            </tr>
                                            <tr>
                                                <td>เสาหลักที่ 2</td>
                                                <td>ยุทธศาสตร์ที่ 2</td>
                                                <td>กลยุทธ์ที่ 2</td>
                                                <td>พัฒนาคุณภาพ</td>
                                                <td>โครงการปรับปรุง</td>
                                                <td>77.5%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>

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
    <script>
        function exportCSV() {
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells.join(","));
            }
            const csvContent = "\uFEFF" + rows.join("\n"); // Add BOM
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

    </script>
    <!-- Common JS -->
    <script src="../../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>