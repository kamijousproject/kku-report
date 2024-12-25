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
                        <h4>รายงานโครงการ/กิจกรรม</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานโครงการ/กิจกรรม</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>ตารางข้อมูลแผนงานและโครงการ</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">ส่วนงาน/หน่วยงาน</th>
                                                <th rowspan="2">แผนงาน (ผลผลิต)</th>
                                                <th rowspan="2">แผนงานย่อย (ผลผลิตย่อย/กิจกรรม)</th>
                                                <th rowspan="2">โครงการ/กิจกรรม</th>
                                                <th rowspan="2">แหล่งงบประมาณ</th>
                                                <th rowspan="2">งบประมาณ</th>
                                                <th colspan="2">KPI For Project</th>
                                                <th rowspan="2">วัตถุประสงค์</th>
                                                <th rowspan="2">ผลผลิต</th>
                                                <th rowspan="2">ผลลัพธ์</th>
                                                <th rowspan="2">ผลกระทบ</th>
                                                <th colspan="4">ความเชื่อมโยงกับระบบแผนงาน</th>
                                            </tr>
                                            <tr>
                                                <th>KPI Name For Project</th>
                                                <th>KPI Unit For Project</th>
                                                <th>ประเด็นยุทธศาสตร์ / ทิศทาง Mission</th>
                                                <th>OKRs</th>
                                                <th>Good Governance</th>
                                                <th>SDGs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>ส่วนงาน A</td>
                                                <td>แผนงาน A</td>
                                                <td>แผนงานย่อย A1</td>
                                                <td>โครงการ A1.1</td>
                                                <td>งบประมาณแผ่นดิน</td>
                                                <td>1,000,000</td>
                                                <td>เพิ่มจำนวนผู้เข้าถึง</td>
                                                <td>หน่วย</td>
                                                <td>สนับสนุนการศึกษา</td>
                                                <td>100 ราย</td>
                                                <td>ผลสัมฤทธิ์สูงขึ้น</td>
                                                <td>ผลกระทบเชิงบวกต่อชุมชน</td>
                                                <td>การศึกษา</td>
                                                <td>เพิ่มโอกาส</td>
                                                <td>ความโปร่งใส</td>
                                                <td>เป้าหมายที่ 4</td>
                                            </tr>
                                            <tr>
                                                <td>ส่วนงาน B</td>
                                                <td>แผนงาน B</td>
                                                <td>แผนงานย่อย B1</td>
                                                <td>โครงการ B1.1</td>
                                                <td>งบประมาณอื่นๆ</td>
                                                <td>500,000</td>
                                                <td>ลดความเหลื่อมล้ำ</td>
                                                <td>ร้อยละ</td>
                                                <td>สนับสนุนการพัฒนา</td>
                                                <td>50 ราย</td>
                                                <td>ผลสัมฤทธิ์เพิ่มขึ้น</td>
                                                <td>ผลกระทบระดับประเทศ</td>
                                                <td>สุขภาพ</td>
                                                <td>ปรับปรุงระบบ</td>
                                                <td>ธรรมาภิบาล</td>
                                                <td>เป้าหมายที่ 3</td>
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