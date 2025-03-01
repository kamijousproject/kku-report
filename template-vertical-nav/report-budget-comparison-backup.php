<!DOCTYPE html>
<html lang="en">
<?php include('../component/header.php'); ?>
<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th,
    #reportTable td {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: middle;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }

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

    /* กำหนดให้ตารางขยายขนาดเต็มหน้าจอ */
    /* สำหรับการล็อคทั้งตาราง */
    table {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: #fff;
        border-collapse: collapse;

        width: 100%;

    }

    td,
    th {
        padding: 0;
        /* ลบ padding ของเซลล์ */
        margin: 0;
        /* ลบ margin ของเซลล์ */
        border: 20px solid #fff;
        /* สีขอบเซลล์เป็นสีเดียวกับพื้นหลัง (ทำให้ไม่เห็นขอบ) */
    }

    /* สำหรับการล็อคแถว */
    thead {
        position: sticky;
        top: 0;
        background-color: #fff;
        z-index: 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.0);
    }



    /* ทำให้ข้อมูลในตารางเลื่อนได้ */
    .table-responsive {
        max-height: 65vh;
        /* กำหนดความสูงของตาราง */
        overflow-y: auto;
        /* ทำให้สามารถเลื่อนข้อมูลในตารางได้ */
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
                        <h4>รายงานเปรียบเทียบงประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">
                                รายงานเปรียบเทียบงประมาณที่ได้รับการจัดสรร/ผลการใช้งบประมาณในภาพรวม</li>
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
                                    f.Alias_Default AS Faculty_Name,
                                    p.plan_id AS Plan_id,
                                    p.plan_name AS Plan_Name,
                                    sp.sub_plan_id AS Sup_Plan,
                                    sp.sub_plan_name AS Sub_Plan_Name,
                                    bpsk.Sub_plan_KPI_Name AS Sub_plan_KPI_Name,
                                    bpsk.UoM_for_Sub_plan_KPI AS UoM_for_Sub_plan_KPI,
                                    bpsk.Sub_plan_KPI_Target AS Sub_plan_KPI_Target,
                                    bppk.Proj_KPI_Name AS Project_KPI_Name,
                                    pr.project_id,
                                    pr.project_name AS Project_Name,
                                    acc.`type` AS `Type`,
                                    acc.sub_type AS Sup_Type,
                                    bpaap.`Fund`,
                                    bpaap.KKU_Item_Name,
                                    bpabp.Reason,
                                    SUM(bpaap.Allocated_Total_Amount_Quantity) AS Total_Allocated_Amount_Quantity,
                                    SUM(bpabp.Total_Amount_Quantity) AS Total_Amount_Quantity
                                FROM
                                    budget_planning_allocated_annual_budget_plan AS bpaap
                                    LEFT JOIN budget_planning_annual_budget_plan AS bpabp ON bpaap.Account = bpabp.Account 
                                    LEFT JOIN budget_planning_subplan_kpi AS bpsk ON bpaap.Sub_Plan = bpsk.Sub_plan_KPI_Name
                                    LEFT JOIN budget_planning_project_kpi AS bppk ON bppk.`Project` = bpaap.`Project`
                                    LEFT JOIN Faculty AS f ON bpaap.Faculty = f.Faculty
                                    LEFT JOIN plan AS p ON bpaap.Plan = p.plan_id
                                    LEFT JOIN sub_plan AS sp ON bpaap.Sub_Plan = sp.sub_plan_id
                                    LEFT JOIN project AS pr ON bpaap.Project = pr.project_id
                                    LEFT JOIN account acc ON bpaap.Account = acc.account
                                WHERE
                                    bpaap.Fund = :fund
                                GROUP BY
                                    p.plan_id, p.plan_name, sp.sub_plan_id, sp.sub_plan_name, 
                                    bpsk.Sub_plan_KPI_Name, bpsk.UoM_for_Sub_plan_KPI, bpsk.Sub_plan_KPI_Target, 
                                    bppk.Proj_KPI_Name, pr.project_id, pr.project_name,
                                    f.Alias_Default, acc.`type`, acc.sub_type, bpaap.`Fund`, 
                                    bpaap.KKU_Item_Name, bpabp.Reason";

                                try {
                                    $stmt = $conn->prepare($query);
                                    $stmt->bindParam(':fund', $fund, PDO::PARAM_STR);
                                    $stmt->execute();

                                    // ตรวจสอบว่ามีข้อมูลที่ได้จากฐานข้อมูลหรือไม่
                                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (!$result) {
                                        // ถ้าไม่มีข้อมูลในฐานข้อมูล ส่งคืนค่า null หรือค่าที่คุณต้องการ
                                        return [];
                                    }

                                    return $result;
                                } catch (PDOException $e) {
                                    // ถ้ามีข้อผิดพลาดในการดึงข้อมูลจากฐานข้อมูล
                                    echo "Error: " . $e->getMessage();
                                    return [];
                                }
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

                                                $sum67 = ($row['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn08['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0);
                                                $sum68Request = ($row['Total_Amount_Quanity'] ?? 0) + ($fn08['Total_Amount_Quanity'] ?? 0) + ($fn02['Total_Amount_Quanity'] ?? 0);
                                                $sum68Allocated = ($row['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn08['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0);
                                                $diff = $sum68Allocated - $sum67;
                                                $percent = ($diff / max($sum67, 1)) * 100;
                                            ?>
                                                <tr>
                                                    <td style="text-align: left; white-space: nowrap;">
                                                        <?php

                                                        // แสดง Plan และ Plan_Name
                                                        echo "<strong>" . str_repeat('&nbsp;', 5) . "{$row['Plan_id']} : {$row['Plan_Name']}</strong><br>";
                                                        // ลบข้อมูลในวงเล็บออกจาก Sub_Plan_Name และแสดง Sub_Plan กับ Sub_Plan_Name
                                                        $subPlanName = preg_replace('/\([^\)]*\)/', '', $row['Sub_Plan_Name'] ?? '');
                                                        $subPlan = $row['Sup_Plan'] ?? '';
                                                        echo "<strong>" . str_repeat('&nbsp;', 10) . "{$subPlan} : {$subPlanName}</strong><br>";
                                                        echo "<strong>" . str_repeat('&nbsp;', 10) . (isset($row['Sub_plan_KPI_Name']) && !empty($row['Sub_plan_KPI_Name']) ? $row['Sub_plan_KPI_Name'] : 'ไม่มี KPI') . "</strong><br>";

                                                        // แก้ไข Project_Name ให้มีช่องว่างหลังเครื่องหมาย :
                                                        $projectName = preg_replace('/(\d+):(\S)/', '$1 : $2', $row['Project_Name']);
                                                        echo "<strong>" . str_repeat('&nbsp;', 15) . "{$projectName}</strong><br>";
                                                        echo "<strong>" . str_repeat('&nbsp;', 15) . "{$row['Project_KPI_Name']}</strong><br>";
                                                        echo "<strong>" . str_repeat('&nbsp;', 20) . "{$row['Type']}</strong><br>";
                                                        echo "<strong>" . str_repeat('&nbsp;', 25) . "{$row['Sup_Type']}</strong><br>";
                                                        echo "<strong>" . str_repeat('&nbsp;', 30) . "{$row['KKU_Item_Name']}</strong><br>";


                                                        ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $row['UoM_for_Sub_plan_KPI'] ?? 'ไม่มี ข้อมูล' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $row['Sub_plan_KPI_Target'] ?? 'ไม่มี ข้อมูล' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        ไม่มี ข้อมูล
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        ไม่มี ข้อมูล
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        ไม่มี ข้อมูล
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        ไม่มี ข้อมูล
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $row['Sub_plan_KPI_Target'] ?? 'ไม่มี ข้อมูล' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $row['Total_Amount_Quanity'] ?? '-' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $row['Allocated_Total_Amount_Quantity'] ?? '-' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $fn08['Total_Amount_Quanity'] ?? '-' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $fn08['Allocated_Total_Amount_Quantity'] ?? '-' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $fn02['Total_Amount_Quanity'] ?? '-' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $fn02['Allocated_Total_Amount_Quantity'] ?? '-' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $sum68Allocated ?? '-' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $diff ?? '-' ?>
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $percent ?? '-' ?>%
                                                    </td>
                                                    <td style="vertical-align: bottom;">
                                                        <?= $row['Reason'] ?? '-' ?>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                                <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                                <button onclick="exportXLSX()" class="btn btn-success m-t-15">Export XLS</button>
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
                const cells = Array.from(row.cells).map(cell => {
                    let text = cell.innerText.trim();

                    // เช็คว่าเป็นตัวเลข float (ไม่มี , ในหน้าเว็บ)
                    if (!isNaN(text) && text !== "") {
                        text =
                            `"${parseFloat(text).toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}"`;
                    }

                    return text;
                });

                rows.push(cells.join(",")); // ใช้ , เป็นตัวคั่น CSV
            }

            const csvContent = "\uFEFF" + rows.join("\n"); // ป้องกัน Encoding เพี้ยน
            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'รายงาน.csv');
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

        function exportXLSX() {
            const table = document.getElementById('reportTable');
            const rows = [];
            const merges = [];
            const rowSpans = {}; // เก็บค่า rowspan
            const colSpans = {}; // เก็บค่า colspan

            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const row = table.rows[rowIndex];
                const cells = [];
                let colIndex = 0;

                for (let cellIndex = 0; cellIndex < row.cells.length; cellIndex++) {
                    let cell = row.cells[cellIndex];
                    let cellText = cell.innerText.trim();

                    // ตรวจสอบว่ามี rowspan หรือ colspan หรือไม่
                    let rowspan = cell.rowSpan || 1;
                    let colspan = cell.colSpan || 1;

                    // หากเป็นเซลล์ที่เคยถูก Merge ข้ามมา ให้ข้ามไป
                    while (rowSpans[`${rowIndex},${colIndex}`]) {
                        cells.push(""); // ใส่ค่าว่างแทน Merge
                        colIndex++;
                    }

                    // ถ้าเซลล์มีตัวเลขและไม่ใช่ค่าว่าง
                    if (!isNaN(cellText) && cellText !== "") {
                        // ใช้ toLocaleString เพื่อเพิ่ม , และกำหนดทศนิยม
                        cellText = parseFloat(cellText).toLocaleString("en-US", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }

                    // เพิ่มค่าลงไปในแถว
                    cells.push(cellText);

                    // ถ้ามี colspan หรือ rowspan
                    if (rowspan > 1 || colspan > 1) {
                        merges.push({
                            s: {
                                r: rowIndex,
                                c: colIndex
                            }, // จุดเริ่มต้นของ Merge
                            e: {
                                r: rowIndex + rowspan - 1,
                                c: colIndex + colspan - 1
                            } // จุดสิ้นสุดของ Merge
                        });

                        // บันทึกตำแหน่งเซลล์ที่ถูก Merge เพื่อกันการซ้ำ
                        for (let r = 0; r < rowspan; r++) {
                            for (let c = 0; c < colspan; c++) {
                                if (r !== 0 || c !== 0) {
                                    rowSpans[`${rowIndex + r},${colIndex + c}`] = true;
                                }
                            }
                        }
                    }

                    colIndex++;
                }
                rows.push(cells);
            }

            // สร้างไฟล์ Excel
            const XLSX = window.XLSX;
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(rows);

            // ✅ เพิ่ม Merge Cells
            ws['!merges'] = merges;

            XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

            // ✅ ดาวน์โหลดไฟล์ Excel
            const excelBuffer = XLSX.write(wb, {
                bookType: 'xlsx',
                type: 'array'
            });
            const blob = new Blob([excelBuffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงาน.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
    </script>
    <!-- Common JS -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <!-- Custom script -->
    <script src="../js/custom.min.js"></script>
    <!-- โหลดไลบรารี xlsx จาก CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


</body>

</html>