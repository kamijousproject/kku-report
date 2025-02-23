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

                                // ดึงรายการ "ส่วนงาน/หน่วยงาน"
                                function getFacultyList($conn)
                                {
                                    $query = "SELECT Faculty, Alias_Default FROM Faculty ORDER BY Alias_Default ASC";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }

                                $facultyList = getFacultyList($conn);

                                // รับค่าที่ User เลือกจาก Dropdown
                                $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : "";

                                // ดึงข้อมูลงบประมาณตามหน่วยงานที่เลือก
                                function fetchBudgetData($conn, $selectedFaculty)
                                {
                                    $query = "SELECT
                                                acc.alias_default AS Account_Alias_Default,
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
                                                bpanbp.Fund,
                                                bpabp.Total_Amount_Quantity,
                                                bpabp.Fund,
                                                f.Alias_Default AS Faculty_Name,
                                                (
                                                    SELECT Faculty_Parent.Alias_Default
                                                    FROM Faculty Faculty_Parent
                                                    WHERE Faculty_Parent.Faculty = CONCAT(LEFT(f.Faculty, 2), '000')
                                                    LIMIT 1
                                                ) AS Alias_Default_Parent,
                                                p.plan_name AS Plan_Name,
                                                sp.sub_plan_name AS Sub_Plan_Name,
                                                pr.project_name AS Project_Name
                                            FROM budget_planning_allocated_annual_budget_plan bpanbp
                                            LEFT JOIN budget_planning_annual_budget_plan bpabp 
                                                ON bpanbp.Account = bpabp.Account
                                                AND bpanbp.Plan = bpabp.Plan
                                                AND bpanbp.Sub_Plan = bpabp.Sub_Plan
                                                AND bpanbp.Project = bpabp.Project
                                            LEFT JOIN account acc ON bpanbp.Account = acc.account
                                            LEFT JOIN Faculty f ON bpanbp.Faculty = f.Faculty
                                            LEFT JOIN plan p ON bpanbp.Plan = p.plan_id
                                            LEFT JOIN sub_plan sp ON bpanbp.Sub_Plan = sp.sub_plan_id
                                            LEFT JOIN project pr ON bpanbp.Project = pr.project_id";

                                    // เพิ่มเงื่อนไขกรองข้อมูลถ้า User เลือกหน่วยงาน
                                    if (!empty($selectedFaculty)) {
                                        $query .= " WHERE bpanbp.Faculty = :selectedFaculty";
                                    }

                                    $stmt = $conn->prepare($query);

                                    // ผูกค่า Parameter ถ้ามีการเลือกหน่วยงาน
                                    if (!empty($selectedFaculty)) {
                                        $stmt->bindParam(':selectedFaculty', $selectedFaculty, PDO::PARAM_STR);
                                    }

                                    $stmt->execute();
                                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }

                                // ดึงข้อมูลตามค่าที่เลือก
                                $results = fetchBudgetData($conn, $selectedFaculty);
                                ?>
                                <form method="GET">
                                    <label for="faculty">เลือกส่วนงาน/หน่วยงาน:</label>
                                    <select class="form-control" name="faculty" id="faculty" onchange="this.form.submit()">
                                        <option value="">-- เลือกทั้งหมด --</option>
                                        <?php foreach ($facultyList as $faculty): ?>
                                            <option value="<?php echo $faculty['Faculty']; ?>"
                                                <?php echo ($selectedFaculty == $faculty['Faculty']) ? "selected" : ""; ?>>
                                                <?php echo htmlspecialchars($faculty['Alias_Default']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                                <br>
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
                                            <?php foreach ($results as $row):
                                                // รวมค่าที่ต้องการในช่อง "รายการ"
                                                $item_name =
                                                    htmlspecialchars($row['Faculty_Name']) . "<br>" .
                                                    htmlspecialchars($row['Alias_Default_Parent']) . "<br>" .
                                                    htmlspecialchars($row['Plan_Name']) . "<br>" .
                                                    htmlspecialchars($row['Sub_Plan_Name']) . "<br>" .
                                                    htmlspecialchars($row['Project_Name']);

                                                // ตรวจสอบค่าของปี 2567 (ถ้าไม่มีให้ใส่ "-")
                                                $total_2567 = !empty($row['Allocated_Total_Amount_Quantity']) ? $row['Allocated_Total_Amount_Quantity'] : null;
                                                $display_2567 = !is_null($total_2567) ? number_format($total_2567) : "-";

                                                // คำนวณค่ารวมของปี 2568
                                                $total_2568 = !empty($row['Total_Amount_Quantity']) ? $row['Total_Amount_Quantity'] : 0;

                                                // แยก Fund ออกมา
                                                $fund_allocated = isset($row['Fund']) ? $row['Fund'] : null;
                                                $fund_total = isset($row['Fund']) ? $row['Fund'] : null;

                                                // คำนวณ "เงินอุดหนุนจากรัฐ (คำขอ 2568)" และ "เงินอุดหนุนจากรัฐ (จัดสรร 2568)"
                                                $fund_fn06_allocated = ($fund_allocated === 'FN06') ? $row['Allocated_Total_Amount_Quantity'] : 0;
                                                $fund_fn06_total = ($fund_total === 'FN06') ? $row['Total_Amount_Quantity'] : 0;

                                                // คำนวณ "เงินนอกงบประมาณ (คำขอ 2568)" และ "เงินนอกงบประมาณ (จัดสรร 2568)"
                                                $fund_fn08_allocated = ($fund_allocated === 'FN08') ? $row['Allocated_Total_Amount_Quantity'] : 0;
                                                $fund_fn08_total = ($fund_total === 'FN08') ? $row['Total_Amount_Quantity'] : 0;

                                                // คำนวณ "เงินรายได้ (คำขอ 2568)" และ "เงินรายได้ (จัดสรร 2568)"
                                                $fund_fn02_allocated = ($fund_allocated === 'FN02') ? $row['Allocated_Total_Amount_Quantity'] : 0;
                                                $fund_fn02_total = ($fund_total === 'FN02') ? $row['Total_Amount_Quantity'] : 0;

                                                // คำนวณ "รวม (คำขอ 2568)"
                                                $total_request_2568 = $fund_fn06_allocated + $fund_fn08_allocated + $fund_fn02_allocated;

                                                // คำนวณ "รวม (จัดสรร 2568)"
                                                $total_allocated_2568 = $fund_fn06_total + $fund_fn08_total + $fund_fn02_total;

                                                // คำนวณ "เพิ่ม/ลด (จำนวน)" = "รวม (จัดสรร 2568)" - 0
                                                $change_amount = $total_allocated_2568;

                                                // คำนวณ "เพิ่ม/ลด (%)" = 100%
                                                $change_percent = 100;
                                            ?>
                                                <tr>
                                                    <td><?php echo $item_name; ?></td>

                                                    <!-- ปี 2567 -->
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>

                                                    <!-- ปี 2568 -->
                                                    <td><?php echo number_format($fund_fn06_allocated); ?></td> <!-- เงินอุดหนุนจากรัฐ (คำขอ) -->
                                                    <td><?php echo number_format($fund_fn06_total); ?></td> <!-- เงินอุดหนุนจากรัฐ (จัดสรร) -->
                                                    <td><?php echo number_format($fund_fn08_allocated); ?></td> <!-- เงินนอกงบประมาณ (คำขอ) -->
                                                    <td><?php echo number_format($fund_fn08_total); ?></td> <!-- เงินนอกงบประมาณ (จัดสรร) -->
                                                    <td><?php echo number_format($fund_fn02_allocated); ?></td> <!-- เงินรายได้ (คำขอ) -->
                                                    <td><?php echo number_format($fund_fn02_total); ?></td> <!-- เงินรายได้ (จัดสรร) -->
                                                    <td><?php echo number_format($total_request_2568); ?></td> <!-- รวม (คำขอ) -->
                                                    <td><?php echo number_format($total_allocated_2568); ?></td> <!-- รวม (จัดสรร) -->

                                                    <!-- การเพิ่ม/ลด -->
                                                    <td><?php echo number_format($change_amount); ?></td> <!-- เพิ่ม/ลด (จำนวน) -->
                                                    <td><?php echo number_format($change_percent, 2) . "%"; ?></td> <!-- เพิ่ม/ลด (%) -->
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
            link.setAttribute('download', 'รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี.csv');
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
            doc.save('รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี.pdf');
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
            link.download = 'รายงานสรุป การจัดทำและจัดสรรงบประมาณประจำปี.xlsx';
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