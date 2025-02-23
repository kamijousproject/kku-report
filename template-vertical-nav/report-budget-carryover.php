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
        height: auto;
        /* ให้ความสูงของเซลล์ปรับอัตโนมัติตามเนื้อหา */
        vertical-align: top;
        /* จัดตำแหน่งเนื้อหาของเซลล์ให้เริ่มต้นจากด้านบน */
        word-wrap: break-word;
        /* หากข้อความยาวเกินจะทำการห่อคำ */
        white-space: normal;
        /* ป้องกันไม่ให้ข้อความยาวในแถวตัดข้าม */
    }


    /* ทำให้ข้อมูลในตารางเลื่อนได้ */
    .table-responsive {
        max-height: 60vh;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        display: block;
    }
</style>
<?php

include('../component/header.php');
include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();

function fetchBudgetData($conn, $faculty = null, $limit = 10, $offset = 0)
{
    try {
        $query = "SELECT 
        bap.Faculty, 
        ft.Faculty, 
        ft.Alias_Default, 
        bpa.BUDGET_PERIOD,
        CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)) AS a1,
        ac.`type`,
        CONCAT(LEFT(bap.`Account`, 4), REPEAT('0', 6)) AS a2, 
        ac.sub_type,
        bap.`Account`,
        bap.KKU_Item_Name,
        SUM(CASE WHEN bap.Fund = 'FN02' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_FN02,
        SUM(CASE WHEN bap.Fund = 'FN06' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_FN06,
        SUM(CASE WHEN bap.Fund = 'FN08' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_FN08,
        SUM(bap.Allocated_Total_Amount_Quantity) AS Total_Amount,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 AND bap.Fund = 'FN02' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568_FN02,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 AND bap.Fund = 'FN06' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568_FN06,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 AND bap.Fund = 'FN08' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568_FN08,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2568_SUM,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 AND bap.Fund = 'FN02' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567_FN02,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 AND bap.Fund = 'FN06' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567_FN06,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 AND bap.Fund = 'FN08' THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567_FN08,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Total_Amount_2567_SUM,
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) - 
        SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Difference_2568_2567,
        CASE
            WHEN SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) = 0 THEN 100
            ELSE (SUM(CASE WHEN bpa.BUDGET_PERIOD = 2568 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END) / 
                  SUM(CASE WHEN bpa.BUDGET_PERIOD = 2567 THEN bap.Allocated_Total_Amount_Quantity ELSE 0 END)) * 100
        END AS Percentage_2568_to_2567
        FROM budget_planning_allocated_annual_budget_plan bap
        INNER JOIN Faculty ft ON bap.Faculty = ft.Faculty AND ft.parent LIKE 'Faculty%'
        LEFT JOIN plan p ON bap.Plan = p.plan_id
        LEFT JOIN sub_plan sp ON bap.Sub_Plan = sp.sub_plan_id
        LEFT JOIN project pj ON bap.Project = pj.project_id
        INNER JOIN account ac ON bap.`Account` = ac.`account`
        INNER JOIN budget_planning_actual bpa ON bpa.PROJECT = bap.Project
            AND bpa.`ACCOUNT` = bap.`Account`
            AND bpa.PLAN = bap.Plan
            AND bpa.FUND = bap.Fund
            AND bpa.SUBPLAN = CAST(SUBSTRING(bap.Sub_Plan, 4) AS UNSIGNED)
            AND bpa.SERVICE = CAST(REPLACE(bap.Service, 'SR_', '') AS UNSIGNED)";

        if ($faculty) {
            $query .= " AND bap.Faculty = :faculty";
        }

        $query .= " GROUP BY 
            bap.Faculty, 
            ft.Faculty, 
            ft.Alias_Default, 
            bpa.BUDGET_PERIOD, 
            bap.`Account`, 
            ac.`type`, 
            ac.sub_type, 
            bap.KKU_Item_Name
        ORDER BY 
            bap.Faculty ASC, 
            CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)) ASC, 
            CONCAT(LEFT(bap.`Account`, 4), REPEAT('0', 6)) ASC, 
            bap.`Account` ASC
        LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($query);

        if ($faculty) {
            $stmt->bindParam(':faculty', $faculty, PDO::PARAM_STR);
        }

        // Binding limit and offset
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

function fetchFacultyData($conn)
{
    try {
        $query = "SELECT DISTINCT bap.Faculty, ft.Alias_Default AS Faculty_Name
                  FROM budget_planning_annual_budget_plan bap
                  LEFT JOIN Faculty ft ON ft.Faculty = bap.Faculty";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

?>


<!DOCTYPE html>
<html lang="en">

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
                        <h4>รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">
                                รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว
                                        ประเภทที่ยังไม่มีหนี้</h4>
                                </div>

                                <?php
                                $faculties = fetchFacultyData($conn);
                                ?>
                                <form method="GET" action="" onsubmit="return validateForm()">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <label for="faculty" class="label-faculty" style="margin-right: 10px;">เลือก
                                            ส่วนงาน/หน่วยงาน</label>
                                        <select name="faculty" id="faculty" class="form-control"
                                            style="width: 40%; height: 40px; font-size: 16px; margin-right: 10px;">
                                            <option value="">เลือก ส่วนงาน/หน่วยงาน</option>
                                            <?php
                                            // แสดง Faculty ที่ดึงมาจากฟังก์ชัน fetchFacultyData
                                            foreach ($faculties as $faculty) {
                                                $facultyName = htmlspecialchars($faculty['Faculty_Name']); // ใช้ Faculty_Name แทน Faculty
                                                $facultyCode = htmlspecialchars($faculty['Faculty']); // ใช้ Faculty รหัสเพื่อส่งไปใน GET
                                                $selected = (isset($_GET['faculty']) && $_GET['faculty'] == $facultyCode) ? 'selected' : '';
                                                echo "<option value=\"$facultyCode\" $selected>$facultyName</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- ปุ่มค้นหาที่อยู่ด้านล่างฟอร์ม -->
                                    <div class="form-group" style="display: flex; justify-content: center;">
                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                    </div>
                                </form>





                                <script>
                                    function validateForm() {
                                        var faculty = document.getElementById('faculty').value;
                                        if (faculty == '') {
                                            alert('กรุณาเลือกส่วนงาน/หน่วยงาน');
                                            return false;
                                        }
                                        return true;
                                    }
                                </script>


                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover text-center">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="4">ปี 2568 (ปีที่ขอตั้ง)</th>
                                                <th colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                                <th>รวม</th>
                                                <th>เงินอุดหนุนจากรัฐ</th>
                                                <th>เงินนอกงบประมาณ</th>
                                                <th>เงินรายได้</th>
                                                <th>รวม</th>
                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>

                                        <?php
                                        // ตัวแปรเก็บค่าของแถวก่อนหน้า
                                        $previousType = "";
                                        $previousSubType = "";
                                        $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;

                                        $results = fetchBudgetData($conn, $selectedFaculty);

                                        // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                        if (isset($results) && is_array($results) && count($results) > 0) {
                                            foreach ($results as $row) {
                                                echo "<tr>";
                                                echo "<td style='text-align: left;'>";

                                                // เช็คและแสดง Type ถ้าเปลี่ยนแปลง
                                                if ($row['a1'] != $previousType) {
                                                    // ลบตัวเลขและจุดจาก type
                                                    $cleanedType = preg_replace('/[0-9.]/', '', $row['type']);
                                                    echo "<strong>" . htmlspecialchars($row['a1']) . "</strong> : " . htmlspecialchars($cleanedType) . "<br>";
                                                    $previousType = $row['a1'];
                                                    $previousSubType = "";
                                                }

                                                // เช็คและแสดง Sub Type ถ้าเปลี่ยนแปลง
                                                if ($row['a2'] != $previousSubType) {
                                                    // ลบตัวเลขและจุดจาก sub_type
                                                    $cleanedSubType = preg_replace('/[0-9.]/', '', $row['sub_type']);
                                                    echo str_repeat("&nbsp;", 16) . "<strong>" . htmlspecialchars($row['a2']) . "</strong> : " . htmlspecialchars($cleanedSubType) . "<br>";
                                                    $previousSubType = $row['a2'];
                                                }


                                                // เช็คและกำหนดค่า kkuItemName
                                                $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                    ? "<strong>" . htmlspecialchars($row['Account']) . "</strong> : " . htmlspecialchars($row['KKU_Item_Name'])
                                                    : "<strong>" . htmlspecialchars($row['Account']) . "</strong>";

                                                // แสดงผล
                                                echo str_repeat("&nbsp;", 32) . $kkuItemName;
                                                echo "</td>";

                                                // แสดงยอดเงิน
                                                echo "<td>" . number_format($row['Total_Amount_2567_FN06'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Total_Amount_2567_FN08'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Total_Amount_2567_FN02'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Total_Amount_2567_SUM'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Total_Amount_2568_FN06'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Total_Amount_2568_FN08'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Total_Amount_2568_FN02'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Total_Amount_2568_SUM'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Difference_2568_2567'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Percentage_2568_to_2567'], 2) . "</td>";


                                                echo "</tr>";

                                                // อัปเดตตัวแปรก่อนหน้า
                                                $previousType = $row['a1'];
                                                $previousSubType = $row['a2'];
                                            }
                                        } else {
                                            echo "<tr><td colspan='8'>ไม่มีข้อมูลที่ค้นหามา</td></tr>";
                                        }

                                        ?>
                                    </table>
                                    <script>
                                        // การส่งค่าของ selectedFaculty ไปยัง JavaScript
                                        var selectedFaculty = "<?php echo isset($selectedFaculty) ? htmlspecialchars($selectedFaculty, ENT_QUOTES, 'UTF-8') : ''; ?>";
                                        console.log('Selected Faculty: ', selectedFaculty);


                                    </script>


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

            for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                const row = table.rows[rowIndex];
                const cells = Array.from(row.cells).map((cell, index) => {
                    let text = cell.innerText.trim();

                    // เช็คว่าเป็นตัวเลข float (ไม่มี , ในหน้าเว็บ)
                    if (!isNaN(text) && text !== "") {
                        text = `"${parseFloat(text).toLocaleString("en-US", { minimumFractionDigits: 2 })}"`;
                    }

                    return text;
                });

                // ขยับเฉพาะแถวที่ 2 (rowIndex === 1) ไป 1 คอลัมน์
                if (rowIndex === 1) {  // rowIndex === 1 คือแถวที่ 2
                    cells.splice(0, 0, "");  // เพิ่มช่องว่างในคอลัมน์แรก
                }

                // ขยับแค่แถวที่ 2 เท่านั้น
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>