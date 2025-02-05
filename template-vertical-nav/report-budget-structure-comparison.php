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
                                    $query = "SELECT DISTINCT
                                                acc.sub_type,
                                                acc.type,
                                                bpanbp.Service,
                                                bpa.SERVICE,
                                                bpanbp.Account,
                                                bpa.ACCOUNT,
                                                bpanbp.Fund,
                                                bpanbp.Faculty,
                                                bpanbp.Plan,
                                                bpanbp.Sub_Plan,
                                                bpanbp.Project,
                                                bpanbp.KKU_Item_Name,
                                                bpanbp.Allocated_Total_Amount_Quantity,
                                                bpa.TOTAL_BUDGET,
                                                bpa.TOTAL_CONSUMPTION,
                                                bpa.EXPENDITURES,
                                                bpa.FUNDS_AVAILABLE_AMOUNT,
                                                bpa.INITIAL_BUDGET,
                                                bpa.FUNDS_AVAILABLE_PERCENTAGE,
                                                bpa.COMMITMENTS,
                                                bpa.OBLIGATIONS,
                                                f.Alias_Default AS Faculty_Name,
                                                p.plan_name AS Plan_Name,
                                                sp.sub_plan_name AS Sub_Plan_Name,
                                                pr.project_name AS Project_Name
                                            FROM
                                                budget_planning_allocated_annual_budget_plan bpanbp
                                                LEFT JOIN budget_planning_actual bpa 
                                                ON bpanbp.Faculty = bpa.FACULTY 
                                                AND bpanbp.Plan = bpa.PLAN
                                                AND bpanbp.Sub_Plan = bpa.SUBPLAN
                                                AND bpanbp.Project = bpa.PROJECT
                                                AND bpanbp.Fund = bpa.FUND
                                                LEFT JOIN account acc ON bpanbp.Account = acc.account
                                                LEFT JOIN Faculty AS f ON bpanbp.Faculty = f.Faculty
                                                LEFT JOIN plan AS p ON bpanbp.Plan = p.plan_id
                                                LEFT JOIN sub_plan AS sp ON bpanbp.Sub_Plan = sp.sub_plan_id
                                                LEFT JOIN project AS pr ON bpanbp.Project = pr.project_id
                                            WHERE
                                                bpanbp.Fund = :fund";

                                    $stmt = $conn->prepare($query);
                                    $stmt->bindParam(':fund', $fund);
                                    $stmt->execute();
                                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }

                                $resultsFN02 = fetchBudgetData($conn, 'FN02');
                                $resultsFN06 = fetchBudgetData($conn, 'FN06');

                                echo "<pre>";
                                // print_r($resultsFN02);
                                // print_r($resultsFN06);
                                echo "</pre>";


                                $mergedData = [];

                                foreach ($resultsFN06 as $fn06) {
                                    $fn02Match = array_filter($resultsFN02, function ($fn02) use ($fn06) {
                                        return (string) $fn06['Plan'] === (string) $fn02['Plan'] &&
                                            (string) $fn06['Sub_Plan'] === (string) $fn02['Sub_Plan'] &&
                                            (string) $fn06['Project'] === (string) $fn02['Project'] &&
                                            (string) $fn06['Account'] === (string) $fn02['ACCOUNT'];
                                    });

                                    // ใช้แค่ตัวแรกที่ตรงกับ FN06
                                    $fn02 = reset($fn02Match);

                                    // ✅ กำหนดค่าเริ่มต้นให้ตัวแปร
                                    $commitment_FN06 = 0;
                                    $commitment_FN02 = 0;
                                    $commitment_percent_FN06 = 0;
                                    $commitment_percent_FN02 = 0;
                                    $Expenditures_Percent_FN06 = 0;
                                    $Expenditures_Percent_FN02 = 0;

                                    // ✅ คำนวณค่าเริ่มต้นสำหรับ Total
                                    $Total_Allocated = 0;
                                    $Total_Commitments = 0;
                                    $Total_Commitments_Percent = 0;
                                    $Total_Expenditures = 0;
                                    $Total_Expenditures_Percent = 0;

                                    // ✅ ตรวจสอบว่ามีข้อมูล FN02 หรือไม่
                                    if ($fn02) {
                                        $commitment_FN06 = ($fn06['COMMITMENTS'] ?? 0) + ($fn06['OBLIGATIONS'] ?? 0);
                                        $commitment_FN02 = ($fn02['COMMITMENTS'] ?? 0) + ($fn02['OBLIGATIONS'] ?? 0);

                                        $commitment_percent_FN06 = ($fn06['Allocated_Total_Amount_Quantity'] ?? 0) != 0
                                            ? (($commitment_FN06 - $fn06['Allocated_Total_Amount_Quantity']) / $fn06['Allocated_Total_Amount_Quantity']) * 100
                                            : 0;

                                        $commitment_percent_FN02 = ($fn02['Allocated_Total_Amount_Quantity'] ?? 0) != 0
                                            ? (($commitment_FN02 - $fn02['Allocated_Total_Amount_Quantity']) / $fn02['Allocated_Total_Amount_Quantity']) * 100
                                            : 0;

                                        $Expenditures_Percent_FN06 = ($fn06['Allocated_Total_Amount_Quantity'] ?? 0) != 0
                                            ? (($fn06['EXPENDITURES'] - $fn06['Allocated_Total_Amount_Quantity']) / $fn06['Allocated_Total_Amount_Quantity']) * 100
                                            : 0;

                                        $Expenditures_Percent_FN02 = ($fn02['Allocated_Total_Amount_Quantity'] ?? 0) != 0
                                            ? (($fn02['EXPENDITURES'] - $fn02['Allocated_Total_Amount_Quantity']) / $fn02['Allocated_Total_Amount_Quantity']) * 100
                                            : 0;
                                    }

                                    // ✅ คำนวณค่า Total
                                    $Total_Allocated = ($fn06['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0);
                                    $Total_Commitments = $commitment_FN06 + $commitment_FN02;
                                    $Total_Commitments_Percent = $commitment_percent_FN06 + $commitment_percent_FN02;
                                    $Total_Expenditures = ($fn06['EXPENDITURES'] ?? 0) + ($fn02['EXPENDITURES'] ?? 0);
                                    $Total_Expenditures_Percent = $Expenditures_Percent_FN06 + $Expenditures_Percent_FN02;

                                    // ✅ เพิ่มข้อมูลลงใน mergedData
                                    $mergedData[] = [
                                        'Plan' => $fn06['Plan'],
                                        'Sub_Plan' => $fn06['Sub_Plan'],
                                        'Project' => $fn06['Project'],
                                        'Type' => $fn06['type'],
                                        'Sub_Type' => $fn06['sub_type'],
                                        'KKU_Item_Name' => $fn06['KKU_Item_Name'],
                                        'Allocated_FN06' => $fn06['Allocated_Total_Amount_Quantity'] ?? 0,
                                        'Commitments_FN06' => $commitment_FN06,
                                        'Commitment_Percent_FN06' => $commitment_percent_FN06,
                                        'Expenditures_FN06' => $fn06['EXPENDITURES'] ?? 0,
                                        'Expenditures_Percent_FN06' => $Expenditures_Percent_FN06,
                                        'Allocated_FN02' => $fn02['Allocated_Total_Amount_Quantity'] ?? 0,
                                        'Commitments_FN02' => $commitment_FN02,
                                        'Commitment_Percent_FN02' => $commitment_percent_FN02,
                                        'Expenditures_FN02' => $fn02['EXPENDITURES'] ?? 0,
                                        'Expenditures_Percent_FN02' => $Expenditures_Percent_FN02,
                                        'Total_Allocated' => $Total_Allocated,
                                        'Total_Commitments' => $Total_Commitments,
                                        'Total_Commitments_Percent' => $Total_Commitments_Percent,
                                        'Total_Expenditures' => $Total_Expenditures,
                                        'Total_Expenditures_Percent' => $Total_Expenditures_Percent,
                                    ];
                                }


                                // สร้างตัวแปรเก็บจำนวนแถวที่ต้อง merge
                                $rowspanData = [];

                                // วนลูปเพื่อคำนวณว่าข้อมูลไหนต้อง merge
                                foreach ($mergedData as $row) {
                                    $key = $row['Plan'] . '|' . $row['Sub_Plan'] . '|' . $row['Project'] . '|' . $row['Type'] . '|' . $row['Sub_Type'];

                                    if (!isset($rowspanData[$key])) {
                                        $rowspanData[$key] = 0;
                                    }
                                    $rowspanData[$key]++;
                                }

                                // ใช้ตัวแปรนี้เพื่อติดตามแถวที่ถูก merge ไปแล้ว
                                $usedRowspan = [];
                                ?>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="6">รายการ</th>
                                                <th colspan="5">เงินอุดหนุนจากรัฐ (FN06)</th>
                                                <th colspan="5">เงินรายได้ (FN02)</th>
                                                <th colspan="5">รวม</th>
                                            </tr>
                                            <tr>
                                                <th>Plan</th>
                                                <th>Subplan</th>
                                                <th>Project</th>
                                                <th>ค่าใช้จ่าย</th>
                                                <th>ประเภทรายจ่าย</th>
                                                <th>รายการรายจ่าย</th>
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
                                                <th>จำนวน(รวมจัดสรร68-รวม67)</th>
                                                <th>ผลการใช้จ่าย</th>
                                                <th>ร้อยละผลการใช้จ่าย</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($mergedData as $row) : ?>
                                                <?php
                                                // กำหนด Key เพื่อเช็คว่า Row นี้ต้อง merge หรือไม่
                                                $key = $row['Plan'] . '|' . $row['Sub_Plan'] . '|' . $row['Project'] . '|' . $row['Type'] . '|' . $row['Sub_Type'];

                                                // ตรวจสอบว่าเคยใช้ rowspan ไปแล้วหรือยัง
                                                $showRowspan = !isset($usedRowspan[$key]);
                                                if ($showRowspan) {
                                                    $usedRowspan[$key] = true;
                                                }
                                                ?>
                                                <tr>
                                                    <?php if ($showRowspan) : ?>
                                                        <td rowspan="<?= $rowspanData[$key] ?>"><?= $row['Plan'] ?></td>
                                                        <td rowspan="<?= $rowspanData[$key] ?>"><?= $row['Sub_Plan'] ?></td>
                                                        <td rowspan="<?= $rowspanData[$key] ?>"><?= $row['Project'] ?></td>
                                                        <td rowspan="<?= $rowspanData[$key] ?>"><?= $row['Type'] ?></td>
                                                        <td rowspan="<?= $rowspanData[$key] ?>"><?= $row['Sub_Type'] ?></td>
                                                    <?php endif; ?>
                                                    <td><?= $row['KKU_Item_Name'] ?></td>
                                                    <td><?= $row['Allocated_FN06'] ?></td>
                                                    <td><?= $row['Commitments_FN06'] ?></td>
                                                    <td><?= number_format($row['Commitment_Percent_FN06'], 2) ?>%</td>
                                                    <td><?= $row['Expenditures_FN06'] ?></td>
                                                    <td><?= number_format($row['Expenditures_Percent_FN06'], 2) ?>%</td>
                                                    <td><?= $row['Allocated_FN02'] ?></td>
                                                    <td><?= $row['Commitments_FN02'] ?></td>
                                                    <td><?= number_format($row['Commitment_Percent_FN02'], 2) ?>%</td>
                                                    <td><?= $row['Expenditures_FN02'] ?></td>
                                                    <td><?= number_format($row['Expenditures_Percent_FN02'], 2) ?>%</td>
                                                    <td><?= number_format($row['Total_Allocated'], 2) ?></td>
                                                    <td><?= number_format($row['Total_Commitments'], 2) ?></td>
                                                    <td><?= number_format($row['Total_Commitments_Percent'], 2) ?>%</td>
                                                    <td><?= number_format($row['Total_Expenditures'], 2) ?></td>
                                                    <td><?= number_format($row['Total_Expenditures_Percent'], 2) ?>%</td>
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