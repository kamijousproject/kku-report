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
    table {
        width: 100%;
        border-collapse: collapse;
        /* ลบช่องว่างระหว่างเซลล์ */
    }

    /* ทำให้หัวตารางติดอยู่กับด้านบน */
    th {
        position: sticky;
        /* ทำให้ header ติดอยู่กับด้านบน */
        top: 0;
        /* กำหนดให้หัวตารางอยู่ที่ตำแหน่งด้านบน */
        background-color: #fff;
        /* กำหนดพื้นหลังให้กับหัวตาราง */
        z-index: 2;
        /* กำหนด z-index ให้สูงกว่าแถวอื่น ๆ */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        /* เพิ่มเงาให้หัวตาราง */
        padding: 8px;
    }

    /* เพิ่มเงาให้กับแถวหัวตาราง */
    th,
    td {
        border: 1px solid #ddd;
        /* เพิ่มขอบให้เซลล์ */
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
                                            acc.alias_default AS Account_Alias_Default, -- แก้ไข alias เพื่อหลีกเลี่ยงการซ้ำ
                                            acc.type,
                                            acc.sub_type,
                                            bpanbp.Service,
                                            bpanbp.Account,
                                            bpanbp.Faculty,
                                            bpanbp.Plan,
                                            bpanbp.Sub_Plan,
                                            bpanbp.Project,
                                            bpanbp.KKU_Item_Name,
                                            bpanbp.Allocated_Total_Amount_Quantity,
                                            bpabp.Total_Amount_Quantity,                                            
                                            f.Alias_Default AS Faculty_Name, -- ใช้ alias ที่ไม่ซ้ำ
                                            p.plan_name AS Plan_Name,
                                            sp.sub_plan_name AS Sub_Plan_Name,
                                            pr.project_name AS Project_Name
                                            FROM budget_planning_allocated_annual_budget_plan bpanbp
                                            LEFT JOIN budget_planning_annual_budget_plan bpabp ON bpanbp.Account = bpabp.Account -- ตรวจสอบให้แน่ใจว่า Account ตรงกัน
                                            LEFT JOIN account acc ON bpanbp.Account = acc.account
                                            LEFT JOIN Faculty f ON bpanbp.Faculty = f.Faculty
                                            LEFT JOIN plan p ON bpanbp.Plan = p.plan_id
                                            LEFT JOIN sub_plan sp ON bpanbp.Sub_Plan = sp.sub_plan_id
                                            LEFT JOIN project pr ON bpanbp.Project = pr.project_id;

                                            WHERE
                                                bpanbp.Plan = bpabp.PLAN
                                                AND bpanbp.Sub_Plan = bpabp.Sub_Plan
                                                AND bpanbp.Project = bpabp.PROJECT
                                                AND bpabp.ACCOUNT = bpanbp.ACCOUNT
                                                AND bpabp.Fund = :fund";

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
                                                <th rowspan="1">รายการ</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                <th rowspan="1"></th>
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

                                                <td style="text-align: left; white-space: nowrap;">
                                                    <?php

                                                    // แสดง Plan และ Plan_Name
                                                    echo "<strong>" . str_repeat('&nbsp;', 5) . "{$row['Plan']} : {$row['Plan_Name']}</strong><br>";

                                                    // ลบข้อมูลในวงเล็บออกจาก Sub_Plan_Name และแสดง Sub_Plan กับ Sub_Plan_Name
                                                    $subPlanName = preg_replace('/\([^\)]*\)/', '', $row['Sub_Plan_Name']);
                                                    $subPlan = preg_replace('/[a-zA-Z_]+/', '', $row['Sub_Plan']);
                                                    echo "<strong>" . str_repeat('&nbsp;', 10) . "{$subPlan} : {$subPlanName}</strong><br>";

                                                    // แก้ไข Project_Name ให้มีช่องว่างหลังเครื่องหมาย :
                                                    $projectName = preg_replace('/(\d+):(\S)/', '$1 : $2', $row['Project_Name']);
                                                    echo "<strong>" . str_repeat('&nbsp;', 15) . "{$projectName}</strong><br>";

                                                    // แสดงข้อมูล Account_Alias_Default
                                                    echo "<strong>" . str_repeat('&nbsp;', 20) . "{$row['type']}</strong><br>";
                                                    ?>
                                                </td>



                                                <!-- -------------- 67 -------------- -->
                                                <td style="vertical-align: bottom;">
                                                    0
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    0
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    0
                                                </td>
                                                <td style="vertical-align: bottom;">0</td>

                                                <!-- --  ---------- 68 -------------- -->
                                                <td style="vertical-align: bottom;">
                                                    <?= $row['Total_Amount_Quanity'] ?? 0 ?>
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    <?= $row['Allocated_Total_Amount_Quantity'] ?? 0 ?>
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    <?= $fn08['Total_Amount_Quanity'] ?? 0 ?>
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    <?= $fn08['Allocated_Total_Amount_Quantity'] ?? 0 ?>
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    <?= $fn02['Total_Amount_Quanity'] ?? 0 ?>
                                                </td>
                                                <td style="vertical-align: bottom;">
                                                    <?= $fn02['Allocated_Total_Amount_Quantity'] ?? 0 ?>
                                                </td>
                                                <td style="vertical-align: bottom;"><?= $sum68Request ?></td>
                                                <td style="vertical-align: bottom;"><?= $sum68Allocated ?></td>
                                                <td style="vertical-align: bottom;"><?= $diff ?></td>
                                                <td style="vertical-align: bottom;"><?= $percent ?>%</td>

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
                        text = `"${parseFloat(text).toLocaleString("en-US", { minimumFractionDigits: 2 })}"`;
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