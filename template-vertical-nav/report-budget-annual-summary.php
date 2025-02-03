<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>

<body class="v-light vertical-nav fix-header fix-sidebar">
    <div id="main-wrapper">
        <?php include('../component/left-nev.php') ?>

        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี</h4>
                                </div>
                                <?php
                                include '../server/connectdb.php';

                                $db = new Database();
                                $conn = $db->connect();

                                function fetchBudgetData($conn, $fund)
                                {
                                    $query = "SELECT
                                                bpaap.*,
                                                bpabp.*,
                                                f.Alias_Default AS Faculty_Name,
                                                p.plan_name AS Plan_Name,
                                                sp.sub_plan_name AS Sub_Plan_Name,
                                                pr.project_name AS Project_Name
                                            FROM
                                                budget_planning_allocated_annual_budget_plan bpaap
                                                LEFT JOIN budget_planning_annual_budget_plan bpabp ON bpaap.Account = bpabp.`Account`
                                                LEFT JOIN Faculty f ON bpaap.Faculty COLLATE utf8mb4_general_ci = f.Faculty COLLATE utf8mb4_general_ci
                                                LEFT JOIN plan p ON bpaap.Plan COLLATE utf8mb4_general_ci = p.plan_id COLLATE utf8mb4_general_ci
                                                LEFT JOIN sub_plan sp ON bpaap.Sub_Plan COLLATE utf8mb4_general_ci = sp.sub_plan_id COLLATE utf8mb4_general_ci
                                                LEFT JOIN project pr ON bpaap.Project COLLATE utf8mb4_general_ci = pr.project_id COLLATE utf8mb4_general_ci
                                            WHERE
                                                bpaap.Fund = :fund
                                            LIMIT 500;";

                                    $stmt = $conn->prepare($query);
                                    $stmt->bindParam(':fund', $fund);
                                    $stmt->execute();
                                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }

                                $resultsFN06 = fetchBudgetData($conn, 'FN06');
                                $resultsFN08 = fetchBudgetData($conn, 'FN08');
                                $resultsFN02 = fetchBudgetData($conn, 'FN02');

                                ?>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
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
                                                <th>รวม (คำขอ)</th>
                                                <th>รวม (จัดสรร)</th>
                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($resultsFN06 as $row):
                                                $fn08 = $resultsFN08[array_search($row['Account'], array_column($resultsFN08, 'Account'))] ?? [];
                                                $fn02 = $resultsFN02[array_search($row['Account'], array_column($resultsFN02, 'Account'))] ?? [];
                                                $sum67 = ($row['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn08['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0);
                                                $sum68Request = ($row['Total_Amount_Quanity'] ?? 0) + ($fn08['Total_Amount_Quanity'] ?? 0) + ($fn02['Total_Amount_Quanity'] ?? 0);
                                                $sum68Allocated = ($row['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn08['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0);
                                                $diff = $sum67 - $sum68Allocated;
                                                // $percent = $diff / $sum68Allocated * 100;
                                                $percent = ($sum67 - $sum68Allocated) / $sum68Allocated * 100;
                                            ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($row['Faculty_Name'] ?? '-') ?></strong><br>
                                                        <?= htmlspecialchars($row['Plan_Name'] ?? '-') ?><br>
                                                        <?= htmlspecialchars($row['Sub_Plan_Name'] ?? '-') ?><br>
                                                        <?= htmlspecialchars($row['Project_Name'] ?? '-') ?>
                                                    </td>
                                                    <!-- -------------- 67 -------------- -->
                                                    <td><?= number_format($row['Allocated_Total_Amount_Quantity'] ?? 0, decimals: 2) ?></td>
                                                    <td><?= number_format(num: $fn08['Allocated_Total_Amount_Quantity'] ?? 0, decimals: 2) ?></td>
                                                    <td><?= number_format($fn02['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($sum67, 2) ?></td>
                                                    <!-- --  ---------- 68 -------------- -->
                                                    <td><?= number_format($row['Total_Amount_Quanity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($row['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>

                                                    <td><?= number_format($fn08['Total_Amount_Quanity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($fn08['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>

                                                    <td><?= number_format($fn02['Total_Amount_Quanity'] ?? 0, 2) ?></td>
                                                    <td><?= number_format($fn02['Allocated_Total_Amount_Quantity'] ?? 0, 2) ?></td>

                                                    <td><?= number_format($sum68Request, 2) ?></td>
                                                    <td><?= number_format($sum68Allocated, 2) ?></td>

                                                    <td><?= number_format($diff, 2) ?></td>
                                                    <td><?= number_format($percent, 2) . '%' ?></td>
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
            const csvContent = "\uFEFF" + rows.join("\n");
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

            doc.setFont("THSarabun");
            doc.setFontSize(12);
            doc.text("รายงานกรอบอัตรากำลังระยะเวลา 4 ปี", 10, 10);

            doc.autoTable({
                html: '#reportTable',
                startY: 20,
                styles: {
                    font: "THSarabun",
                    fontSize: 10,
                    lineColor: [0, 0, 0],
                    lineWidth: 0.5,
                },
                bodyStyles: {
                    lineColor: [0, 0, 0],
                    lineWidth: 0.5,
                },
                headStyles: {
                    fillColor: [102, 153, 225],
                    textColor: [0, 0, 0],
                    lineColor: [0, 0, 0],
                    lineWidth: 0.5,
                },
            });

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
    <script src="../assets/plugins/common/common.min.js"></script>
    <script src="../js/custom.min.js"></script>
</body>

</html>