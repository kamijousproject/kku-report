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
                        <h4>รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปคำขออนุมัติกรอบอัตรากำลัง ประจำปีงบประมาณ แยกตามประเภทบุคลากร</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="2">ส่วนงาน / หน่วยงาน</th>
                                                <th colspan="2">ผ่านการพิจารณาประชุมส่วนงาน / หน่วยงาน ครั้งที่</th>
                                                <th colspan="2">ประเภทบุคลากร</th>
                                                <th>อัตราเดิม</th>
                                                <th>อัตราใหม่</th>
                                                <th>ณ วันที่</th>
                                                <th>รวม (อัตรา)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="2">คณะวิทยาศาสตร์</td>
                                                <td colspan="2">ครั้งที่ 1/2567</td>
                                                <td colspan="2">พนักงานมหาวิทยาลัย ประเภทเงินรายได้</td>
                                                <td>10</td>
                                                <td>12</td>
                                                <td>01/01/2567</td>
                                                <td>22</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">คณะวิศวกรรมศาสตร์</td>
                                                <td colspan="2">ครั้งที่ 1/2567</td>
                                                <td colspan="2">ลูกจ้างของมหาวิทยาลัย</td>
                                                <td>5</td>
                                                <td>6</td>
                                                <td>01/01/2567</td>
                                                <td>11</td>
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