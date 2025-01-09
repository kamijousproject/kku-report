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
                        <h4>รายงานแผนงานระดับต่าง ๆ ของหน่วยงาน (มหาวิทยาลัย)</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานแผนงานระดับต่าง ๆ ของหน่วยงาน (มหาวิทยาลัย)</li>
                        </ol>
                    </div>
                </div>
                <?php
                // Database connection
                include('../server/connectdb.php');
                try {
                    // Query to fetch data
                    $sql = "SELECT 
                                rpl.`so_code`,
                                rpl.`okr_code`,
                                rpl.`stp_code`,
                                rpl.`Target`,
                                rpl.`UOM`,
                                rpl.`Start_Date`,
                                rpl.`End_Date`,
                                rpl.`Budget_Amount`,
                                rpl.`Tiers_&_Deploy`,
                                rpl.`Responsible_person`,
                                p.`pilar_name`,
                                p1.`pilar_name` AS so_code_1,
                                ksp.`ksp_name` AS stp_name,
                                okr.`okr_name` AS okr_name
                            FROM 
                                `report_plan_levels` AS rpl
                            LEFT JOIN 
                                `pilar` AS p
                            ON 
                                rpl.`so_code` = p.`pilar_id`
                            LEFT JOIN 
                                `pilar` AS p1
                            ON 
                                p1.`pilar_id` = REGEXP_REPLACE(rpl.`so_code`, 'SO(\\d+)-\\d+', 'SI\\1')
                            LEFT JOIN 
                                `ksp`
                            ON 
                                rpl.`stp_code` = ksp.`ksp_id`
                            LEFT JOIN 
                                `okr`
                            ON 
                                rpl.`okr_code` = okr.`okr_id`;
                            ";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();

                    // Fetch all rows
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    die("Connection failed: " . $e->getMessage());
                }
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายละเอียดแผนงาน/โครงการ</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>รหัส</th>
                                                <th>ยุทธศาสตร์</th>
                                                <th>รหัส</th>
                                                <th>กลยุทธ์</th>
                                                <th>รหัส</th>
                                                <th>ผลลัพธ์สำคัญ</th>
                                                <th>ค่าเป้าหมาย</th>
                                                <th>หน่วยนับ</th>
                                                <th>รหัส</th>
                                                <th>แผนงาน/โครงการ</th>
                                                <th>กรอบวงเงินงบประมาณ</th>
                                                <th colspan="2">ระยะเวลาที่ดำเนินการ</th>
                                                <th>ระดับและการปรับใช้</th>
                                                <th>ผู้รับผิดชอบ</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>วันเริ่มต้น</th>
                                                <th>วันสิ้นสุด</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars(preg_replace('/SO(\d+)-\d+/', 'SI$1', $row['so_code'])) ?></td>
                                                    <td><?= htmlspecialchars($row['so_code_1']) ?></td>
                                                    <td><?= htmlspecialchars($row['so_code']) ?></td>
                                                    <td><?= htmlspecialchars($row['pilar_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['okr_code']) ?></td> <!-- ยังคงแสดง okr_code หากต้องการ -->
                                                    <td><?= htmlspecialchars($row['okr_name']) ?></td> <!-- ใช้ okr_name -->
                                                    <td><?= htmlspecialchars($row['Target']) ?></td>
                                                    <td><?= htmlspecialchars($row['UOM']) ?></td>
                                                    <td><?= htmlspecialchars($row['stp_code']) ?></td>
                                                    <td><?= htmlspecialchars($row['stp_name']) ?></td> <!-- ใช้ ksp_name -->
                                                    <td><?= htmlspecialchars($row['Budget_Amount']) ?></td>
                                                    <td><?= htmlspecialchars($row['Start_Date']) ?></td>
                                                    <td><?= htmlspecialchars($row['End_Date']) ?></td>
                                                    <td><?= htmlspecialchars($row['Tiers_&_Deploy']) ?></td>
                                                    <td><?= htmlspecialchars($row['Responsible_person']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLS</button>

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

        function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape');

            // เพิ่มฟอนต์ภาษาไทย
            doc.addFileToVFS("THSarabun.ttf", thsarabunnew_webfont_normal); // ใช้ตัวแปรที่ได้จากไฟล์
            doc.addFont("THSarabun.ttf", "THSarabun", "normal");
            doc.setFont("THSarabun");

            // ตั้งค่าฟอนต์และข้อความ
            doc.setFontSize(12);
            doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

            // ใช้ autoTable สำหรับสร้างตาราง
            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                styles: {
                    font: "THSarabun", // ใช้ฟอนต์ที่รองรับภาษาไทย
                    fontSize: 10,
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
                bodyStyles: {
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
                headStyles: {
                    fillColor: [102, 153, 225], // สีพื้นหลังของหัวตาราง
                    textColor: [0, 0, 0], // สีข้อความในหัวตาราง
                    lineColor: [0, 0, 0], // สีของเส้นขอบ (ดำ)
                    lineWidth: 0.5, // ความหนาของเส้นขอบ
                },
            });

            // บันทึกไฟล์ PDF
            doc.save('รายงาน.pdf');
        }

        function exportXLS() {
            const rows = [];
            const table = document.getElementById('reportTable');
            for (let row of table.rows) {
                const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                rows.push(cells);
            }
            let xlsContent = "<table>";
            rows.forEach(row => {
                xlsContent += "<tr>" + row.map(cell => `<td>${cell}</td>`).join('') + "</tr>";
            });
            xlsContent += "</table>";

            const blob = new Blob([xlsContent], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.xls');
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