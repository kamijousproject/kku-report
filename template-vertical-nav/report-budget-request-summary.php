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
                        <h4>รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี(สรุปประมาณการรายรับและประมาณการรายจ่าย)</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสรุปคำขอตั้งงบประมาณรายจ่ายประจำปี(สรุปประมาณการรายรับและประมาณการรายจ่าย)</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุปรายรับรายจ่าย</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ปีงบประมาณ:</th>
                                                <th colspan="2">...................................</th>
                                                <th>ประเภทงบประมาณ:</th>
                                                <th colspan="2">...................................</th>
                                                <th>ส่วนงาน/หน่วยงาน:</th>
                                                <th colspan="2">...................................</th>
                                            </tr>
                                            <tr>
                                                <th>รายการ</th>
                                                <th>งบประมาณ</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>รายรับ</strong></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>1. เงินอุดหนุนจากรัฐ</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>2. เงินและทรัพย์สินอื่นที่รัฐมอบให้แก่มหาวิทยาลัย</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>3. เงินกองทุนที่รัฐมอบหรือมหาวิทยาลัยจัดตั้งขึ้นและรายได้หรือผลประโยชน์จากกองทุน</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>4. ค่าธรรมเนียม ค่าบำรุง ค่าตอบแทน เปลี่ยนชดใช้ และค่าบริการต่างๆ ของมหาวิทยาลัย</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>5. รายได้หรือผลประโยชน์ที่ได้จากการลงทุนหรือบริการร่วมลงทุนจากทรัพย์สินของมหาวิทยาลัย</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>6. รายได้หรือผลประโยชน์ที่ได้จากการใช้หรือทรัพย์สินหรือจัดหาในรูปแบบอื่นที่ทรัพย์สินมหาวิทยาลัยปกครองดูแล ใช้ หรือจัดทำประโยชน์</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>7. เงินอุดหนุนจากหน่วยงานภายนอก เงินทุนอุดหนุนการวิจัยหรือบริการวิชาการที่ได้รับจากหน่วยงานของรัฐ</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>8. เงินและผลประโยชน์ที่ได้รับจากการบริการวิชาการ การวิจัย และนำทรัพย์สินทางปัญญาไปทำประโยชน์</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>9. รายได้ผลประโยชน์อย่างอื่น</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><strong>รวมรายรับ</strong></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><strong>รายจ่าย</strong></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>1. ค่าใช้จ่ายบุคลากร</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>2. ค่าใช้จ่ายดำเนินงาน</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>3. ค่าใช้จ่ายลงทุน</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>4. เงินอุดหนุนดำเนินงาน</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>5. ค่าใช้จ่ายอื่น</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><strong>รวมรายจ่าย</strong></td>
                                                <td></td>
                                                <td></td>
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