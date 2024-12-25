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
                        <h4>รายงานสรุปคำขอรายโครงการ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอรายโครงการ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขอรายโครงการ</h4>
                                </div>
                                <div class="info-section">
                                    <p>ปีงบประมาณ: .......................</p>
                                    <p>ปีบริหารงบประมาณ: .......................</p>
                                    <p>ประเภทของงบประมาณ: .......................</p>
                                    <p>แหล่งเงิน: .......................</p>
                                    <p>ส่วนงาน/หน่วยงาน: .......................</p>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">ที่</th>
                                                <th rowspan="2">โครงการ/กิจกรรม</th>
                                                <th rowspan="2">ประเด็นยุทธศาสตร์</th>
                                                <th rowspan="2">OKR</th>
                                                <th rowspan="2">แผนงาน (ผลผลิต)</th>
                                                <th rowspan="2">แผนงานย่อย (ผลผลิตย่อย/กิจกรรม)</th>
                                                <th colspan="5">งบประมาณ</th>
                                                <th rowspan="2">รวมงบประมาณ</th>
                                                <th colspan="4">แผนการใช้ง่ายงบประมาณ</th>
                                            </tr>
                                            <tr>
                                                <th>1. ค่าใช้จ่าย</th>
                                                <th>2. ค่าใช้จ่าย</th>
                                                <th>3. ค่าใช้จ่าย</th>
                                                <th>4. ค่าใช้จ่าย</th>
                                                <th>5. ค่าใช้จ่ายอื่น</th>
                                                <th>ไตรมาสที่ 1</th>
                                                <th>ไตรมาสที่ 2</th>
                                                <th>ไตรมาสที่ 3</th>
                                                <th>ไตรมาสที่ 4</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>โครงการ A</td>
                                                <td>ยุทธศาสตร์ 1</td>
                                                <td>OKR 1</td>
                                                <td>ผลผลิต A</td>
                                                <td>กิจกรรมย่อย A1</td>
                                                <td>1,000,000</td>
                                                <td>500,000</td>
                                                <td>300,000</td>
                                                <td>200,000</td>
                                                <td>100,000</td>
                                                <td>2,100,000</td>
                                                <td>500,000</td>
                                                <td>500,000</td>
                                                <td>600,000</td>
                                                <td>500,000</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>โครงการ B</td>
                                                <td>ยุทธศาสตร์ 2</td>
                                                <td>OKR 2</td>
                                                <td>ผลผลิต B</td>
                                                <td>กิจกรรมย่อย B1</td>
                                                <td>800,000</td>
                                                <td>400,000</td>
                                                <td>200,000</td>
                                                <td>300,000</td>
                                                <td>100,000</td>
                                                <td>1,800,000</td>
                                                <td>400,000</td>
                                                <td>500,000</td>
                                                <td>500,000</td>
                                                <td>400,000</td>
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