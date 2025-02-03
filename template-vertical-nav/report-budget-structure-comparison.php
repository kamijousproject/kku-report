<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    .wide-column {
        min-width: 250px;
        /* ปรับขนาด column ให้กว้างขึ้น */
        word-break: break-word;
        /* ทำให้ข้อความขึ้นบรรทัดใหม่ได้ */
        white-space: pre-line;
        /* รักษารูปแบบการขึ้นบรรทัด */
        vertical-align: top;
        /* ทำให้ข้อความอยู่ด้านบนของเซลล์ */
        padding: 10px;
        /* เพิ่มช่องว่างด้านใน */
    }

    .wide-column div {
        margin-bottom: 5px;
        /* เพิ่มระยะห่างระหว่างแต่ละรายการ */
    }
</style>

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
                        <h4>รายงานเปรียบเทียบงบประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณจำแนกตามโครงสร้างองค์กร ตาม
                            แหล่งเงิน ตามแผนงาน/โครงการ โดยสามารถแสดงได้ทุกระดับย่อยของหน่วยงบประมาณ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานเปรียบเทียบงบประมาณ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานเปรียบเทียบงบประมาณ</h4>
                                </div>

                                <?php
                                include '../server/connectdb.php';

                                $db = new Database();
                                $conn = $db->connect();

                                // ฟังก์ชันดึงข้อมูล
                                function fetchBudgetData($conn, $fund)
                                {
                                    $query = "SELECT 
                                                bpaap.*, 
                                                bpa.*, 
                                                f.Alias_Default AS Faculty_Name, 
                                                p.plan_name AS Plan_Name, 
                                                sp.sub_plan_name AS Sub_Plan_Name, 
                                                pr.project_name AS Project_Name
                                            FROM budget_planning_allocated_annual_budget_plan bpaap
                                            LEFT JOIN budget_planning_actual bpa 
                                                ON bpaap.Account = bpa.ACCOUNT
                                            LEFT JOIN Faculty f
                                                ON bpaap.Faculty COLLATE utf8mb4_general_ci = f.Faculty COLLATE utf8mb4_general_ci
                                            LEFT JOIN plan p
                                                ON bpaap.Plan COLLATE utf8mb4_general_ci = p.plan_id COLLATE utf8mb4_general_ci
                                            LEFT JOIN sub_plan sp
                                                ON bpaap.Sub_Plan COLLATE utf8mb4_general_ci = sp.sub_plan_id COLLATE utf8mb4_general_ci
                                            LEFT JOIN project pr
                                                ON bpaap.Project COLLATE utf8mb4_general_ci = pr.project_id COLLATE utf8mb4_general_ci
                                            WHERE bpaap.Fund = :fund 
                                            LIMIT 500;
                                            ";

                                    $stmt = $conn->prepare($query);
                                    $stmt->bindParam(':fund', $fund);
                                    $stmt->execute();
                                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }

                                $resultsFN02 = fetchBudgetData($conn, 'FN02');

                                $resultsFN06 = fetchBudgetData($conn, 'FN06');
                                ?>

                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th colspan="5">เงินอุดหนุนจากรัฐ (FN06)</th>
                                                <th colspan="5">เงินรายได้ (FN02)</th>
                                                <th colspan="5">รวม</th>
                                            </tr>
                                            <tr>
                                                <th>งบประมาณที่ได้รับจัดสรร</th>
                                                <th>ผลการก่อหนี้ผูกพัน</th>
                                                <th>ร้อยละผลการก่อหนี้ผูกพัน</th>
                                                <th>ผลการใช้จ่าย</th>
                                                <th>ร้อยละผลการใช้จ่าย</th>
                                                <th>งบประมาณที่ได้รับจัดสรร</th>
                                                <th>ผลการก่อหนี้ผูกพัน</th>
                                                <th>ร้อยละผลการก่อหนี้ผูกพัน</th>
                                                <th>ผลการใช้จ่าย</th>
                                                <th>ร้อยละผลการใช้จ่าย</th>
                                                <th>งบประมาณที่ได้รับจัดสรร</th>
                                                <th>ผลการก่อหนี้ผูกพัน</th>
                                                <th>จำนวน (รวมจัดสรร68-รวม67)</th>
                                                <th>ผลการใช้จ่าย</th>
                                                <th>ร้อยละผลการใช้จ่าย</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $mergedData = [];
                                            // รวมข้อมูล FN06 และ FN02 โดยใช้ Account เป็นกุญแจหลัก
                                            foreach ($resultsFN06 as $row) {
                                                $account = $row['Account'] ?? 'NO_ACCOUNT_FN06';
                                                if (!isset($mergedData[$account])) {
                                                    $mergedData[$account] = ['FN06' => [], 'FN02' => []];
                                                }
                                                $mergedData[$account]['FN06'][] = $row;
                                            }

                                            foreach ($resultsFN02 as $row) {
                                                $account = $row['Account'] ?? 'NO_ACCOUNT_FN02';
                                                if (!isset($mergedData[$account])) {
                                                    $mergedData[$account] = ['FN06' => [], 'FN02' => []];
                                                }
                                                $mergedData[$account]['FN02'][] = $row;
                                            }

                                            foreach ($mergedData as $account => $data):
                                                $fn06Rows = $data['FN06'];
                                                $fn02Rows = $data['FN02'];
                                                $rowCount = max(count($fn06Rows), count($fn02Rows)); // ใช้จำนวนแถวที่มากที่สุด

                                                for ($i = 0; $i < $rowCount; $i++):
                                                    $rowFN06 = $fn06Rows[$i] ?? null;
                                                    $rowFN02 = $fn02Rows[$i] ?? null;
                                                    $key = ($rowFN06['Faculty'] ?? $rowFN02['Faculty'] ?? '-') . "|" .
                                                        ($rowFN06['Plan'] ?? $rowFN02['Plan'] ?? '-') . "|" .
                                                        ($rowFN06['Sub_Plan'] ?? $rowFN02['Sub_Plan'] ?? '-') . "|" .
                                                        ($rowFN06['Project'] ?? $rowFN02['Project'] ?? '-');
                                            ?>
                                                    <tr>
                                                        <!-- ตรวจสอบว่าควรแสดง Faculty, Plan, Sub_Plan, Project ในแถวแรกของกลุ่ม -->
                                                        <?php if ($i == 0): ?>
                                                            <td rowspan="<?= $rowCount ?>" class="wide-column">
                                                                <div><strong><?= htmlspecialchars($rowFN06['Faculty_Name'] ?? $rowFN02['Faculty_Name'] ?? '-') ?></strong></div>
                                                                <div><?= htmlspecialchars($rowFN06['Plan_Name'] ?? $rowFN02['Plan_Name'] ?? '-') ?></div>
                                                                <div><?= htmlspecialchars($rowFN06['Sub_Plan_Name'] ?? $rowFN02['Sub_Plan_Name'] ?? '-') ?></div>
                                                                <div><?= htmlspecialchars($rowFN06['Project_Name'] ?? $rowFN02['Project_Name'] ?? '-') ?></div>
                                                            </td>
                                                        <?php endif; ?>

                                                        <!-- FN06 ข้อมูล -->
                                                        <td><?= isset($rowFN06) ? number_format($rowFN06['Allocated_Total_Amount_Quantity'] ?? 0, 2) : '-' ?></td>
                                                        <td><?= isset($rowFN06) ? number_format(($rowFN06['OBLIGATIONS'] ?? 0) + ($rowFN06['COMMITMENTS'] ?? 0), 2) : '-' ?></td>
                                                        <td>
                                                            <?php
                                                            if (isset($rowFN06)) {
                                                                $allocated = $rowFN06['Allocated_Total_Amount_Quantity'] ?? 0;
                                                                $total_budget = ($rowFN06['OBLIGATIONS'] ?? 0) + ($rowFN06['COMMITMENTS'] ?? 0);
                                                                echo ($allocated != 0) ? number_format((($total_budget - $allocated) / $allocated) * 100, 2) . '%' : '-';
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?= isset($rowFN06) ? number_format($rowFN06['FUNDS_AVAILABLE_AMOUNT'] ?? 0, 2) : '-' ?></td>
                                                        <td><?= isset($rowFN06) ? number_format(($rowFN06['FUNDS_AVAILABLE_AMOUNT'] ?? 0) / max($rowFN06['Allocated_Total_Amount_Quantity'] ?? 1, 1) * 100, 2) . '%' : '-' ?></td>

                                                        <!-- FN02 ข้อมูล -->
                                                        <td><?= isset($rowFN02) ? number_format($rowFN02['Allocated_Total_Amount_Quantity'] ?? 0, 2) : '-' ?></td>
                                                        <td><?= isset($rowFN02) ? number_format(($rowFN02['OBLIGATIONS'] ?? 0) + ($rowFN02['COMMITMENTS'] ?? 0), 2) : '-' ?></td>
                                                        <td>
                                                            <?php
                                                            if (isset($rowFN02)) {
                                                                $allocated = $rowFN02['Allocated_Total_Amount_Quantity'] ?? 0;
                                                                $total_budget = ($rowFN02['OBLIGATIONS'] ?? 0) + ($rowFN02['COMMITMENTS'] ?? 0);
                                                                echo ($allocated != 0) ? number_format((($total_budget - $allocated) / $allocated) * 100, 2) . '%' : '-';
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?= isset($rowFN02) ? number_format($rowFN02['FUNDS_AVAILABLE_AMOUNT'] ?? 0, 2) : '-' ?></td>
                                                        <td><?= isset($rowFN02) ? number_format(($rowFN02['FUNDS_AVAILABLE_AMOUNT'] ?? 0) / max($rowFN02['Allocated_Total_Amount_Quantity'] ?? 1, 1) * 100, 2) . '%' : '-' ?></td>

                                                        <!-- รวม -->
                                                        <td><?= number_format(($rowFN06['Allocated_Total_Amount_Quantity'] ?? 0) + ($rowFN02['Allocated_Total_Amount_Quantity'] ?? 0), 2) ?></td>
                                                        <td><?= number_format(($rowFN06['OBLIGATIONS'] ?? 0) + ($rowFN06['COMMITMENTS'] ?? 0) + ($rowFN02['OBLIGATIONS'] ?? 0) + ($rowFN02['COMMITMENTS'] ?? 0), 2) ?></td>

                                                        <td>
                                                            <?php
                                                            $total_difference = (($rowFN06['Allocated_Total_Amount_Quantity'] ?? 0) +
                                                                ($rowFN02['Allocated_Total_Amount_Quantity'] ?? 0)) -
                                                                (($rowFN06['OBLIGATIONS'] ?? 0) +
                                                                    ($rowFN06['COMMITMENTS'] ?? 0) +
                                                                    ($rowFN02['OBLIGATIONS'] ?? 0) +
                                                                    ($rowFN02['COMMITMENTS'] ?? 0));
                                                            echo number_format($total_difference, 2);
                                                            ?>
                                                        </td>

                                                        <td><?= number_format(($rowFN06['FUNDS_AVAILABLE_AMOUNT'] ?? 0) + ($rowFN02['FUNDS_AVAILABLE_AMOUNT'] ?? 0), 2) ?></td>
                                                        <td>
                                                            <?php
                                                            $total_expended = ($rowFN06['EXPENDITURES'] ?? 0) + ($rowFN02['EXPENDITURES'] ?? 0);
                                                            $total_allocated = ($rowFN06['Allocated_Total_Amount_Quantity'] ?? 0) + ($rowFN02['Allocated_Total_Amount_Quantity'] ?? 0);
                                                            echo ($total_allocated != 0) ? number_format(($total_expended / $total_allocated) * 100, 2) . '%' : '-';
                                                            ?>
                                                        </td>
                                                    </tr>
                                            <?php
                                                endfor;
                                            endforeach;
                                            ?>
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