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
                            <?php
                            include '../server/connectdb.php';

                            $db = new Database();
                            $conn = $db->connect();

                            function fetchBudgetData($conn, $fund)
                            {
                                $query = "SELECT
                                            bpaap.*, bpsk.*, bpabp.*, 
                                            f.Alias_Default AS Faculty_Name,
                                            p.plan_name AS Plan_Name,
                                            sp.sub_plan_name AS Sub_Plan_Name,
                                            pr.project_name AS Project_Name
                                        FROM
                                            budget_planning_allocated_annual_budget_plan AS bpaap
                                            LEFT JOIN budget_planning_subplan_kpi AS bpsk ON bpaap.Sub_Plan = bpsk.Sub_Plan
                                            LEFT JOIN budget_planning_annual_budget_plan AS bpabp ON bpabp.`Account` = bpaap.`Account`
                                            LEFT JOIN Faculty AS f ON bpaap.Faculty = f.Faculty
                                            LEFT JOIN plan AS p ON bpaap.Plan = p.plan_id
                                            LEFT JOIN sub_plan AS sp ON bpaap.Sub_Plan = sp.sub_plan_id
                                            LEFT JOIN project AS pr ON bpaap.Project = pr.project_id
                                        WHERE
                                            bpaap.Fund = :fund";

                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':fund', $fund);
                                $stmt->execute();
                                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }

                            $resultsFN06 = fetchBudgetData($conn, 'FN06');
                            $resultsFN08 = fetchBudgetData($conn, 'FN08');
                            $resultsFN02 = fetchBudgetData($conn, 'FN02');
                            ?>

                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th rowspan="3">หน่วยนับของตัวชี้วัด (UOM)</th>
                                                <th colspan="5">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th colspan="2" rowspan="2">เพิ่ม/ลด</th>
                                                <th rowspan="3">คำชี้แจง</th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2">ปริมาณของตัวชี้วัด</th>
                                                <th rowspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th rowspan="2">เงินนอกงบประมาณ</th>
                                                <th rowspan="2">เงินรายได้</th>
                                                <th rowspan="2">รวม</th>
                                                <th rowspan="2">ปริมาณของตัวชี้วัด</th>
                                                <th colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th colspan="2">เงินนอกงบประมาณ</th>
                                                <th colspan="2">เงินรายได้</th>
                                                <th rowspan="2">รวม</th>
                                            </tr>
                                            <tr>
                                                <th>คำขอ</th>
                                                <th>จัดสรร</th>
                                                <th>คำขอ</th>
                                                <th>จัดสรร</th>
                                                <th>คำขอ</th>
                                                <th>จัดสรร</th>
                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($resultsFN06 as $row):
                                                $fn08 = $resultsFN08[array_search($row['Account'], array_column($resultsFN08, 'Account'))] ?? [];
                                                $fn02 = $resultsFN02[array_search($row['Account'], array_column($resultsFN02, 'Account'))] ?? [];
                                                $sum67 = ($row['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn08['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0);
                                                $sum68Request = ($row['Total_Amount_Quanity'] ?? 0) + ($fn08['Total_Amount_Quanity'] ?? 0) + ($fn02['Total_Amount_Quanity'] ?? 0);
                                                $sum68Allocated = ($row['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn08['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0);
                                                $diff = $sum68Allocated - $sum67;
                                                $percent = ($diff / max($sum67, 1)) * 100;
                                            ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($row['Faculty_Name'] ?? '-') ?></strong><br>
                                                        <?= htmlspecialchars($row['Plan_Name'] ?? '-') ?><br>
                                                        <?= htmlspecialchars($row['Sub_Plan_Name'] ?? '-') ?><br>
                                                        <?= htmlspecialchars($row['Project_Name'] ?? '-') ?>
                                                    </td>
                                                    <td><?php echo $row['UoM_for_Sub_plan_KPI'] ?></td>
                                                    <td><?php echo $row['Sub_plan_KPI_Name'] ?></td>
                                                    <td><?= number_format($row['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($fn02['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($fn08['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($sum67, 2) ?></td>
                                                    <td><?php echo $row['Sub_plan_KPI_Name'] ?></td>
                                                    <td><?= number_format($row['Total_Amount_Quanity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($row['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($fn08['Total_Amount_Quanity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($fn08['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($fn02['Total_Amount_Quanity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($fn02['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>
                                                    
                                                    <td><?= number_format($sum68Allocated, 2) ?></td>
                                                    <td><?= number_format($diff, 2) ?></td>
                                                    <td><?= number_format($percent, 2) . '%' ?></td>
                                                    <td><?php echo $row['Reason'] ?></td>
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
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
</body>

</html>