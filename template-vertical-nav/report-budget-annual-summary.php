<?php
                                include '../server/connectdb.php';

                                $db = new Database();
                                $conn = $db->connect();

                                // ดึงรายการ "ส่วนงาน/หน่วยงาน"
                                function getFacultyList($conn)
                                {
                                    $query = "SELECT DISTINCT
                                                    abp.Faculty, 
                                                    Faculty.Alias_Default
                                                FROM
                                                    budget_planning_annual_budget_plan abp
                                                LEFT JOIN Faculty 
                                                    ON abp.Faculty = Faculty.Faculty";
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
  	 bap.Faculty,
	 ft.Alias_Default,
    bap.Plan,
    p.plan_id,
    p.plan_name,
    bap.Sub_Plan,
    sp.sub_plan_id,
    sp.sub_plan_name,
    bap.Project,
    pj.project_id,
    pj.project_name,
    CONCAT(LEFT(bap.`Account`, 2), REPEAT('0', 8)) AS a1,
    ac.`type`,
    bap.Total_Amount_Quantity,
	 bap.Fund,  
	 SUM(CASE WHEN bap.Fund = 'FN06' AND bap.Budget_Management_Year = 2568 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Amount_FN06_1,
    SUM(CASE WHEN bap.Fund = 'FN08' AND bap.Budget_Management_Year = 2568 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Amount_FN08_1,
    SUM(CASE WHEN bap.Fund = 'FN02' AND bap.Budget_Management_Year = 2568 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Amount_FN02_1,
    
    SUM(
        CASE 
            WHEN bap.Fund IN ('FN06', 'FN08', 'FN02') 
            AND bap.Budget_Management_Year = 2568 
            THEN bap.Total_Amount_Quantity 
            ELSE 0 
        END
    ) AS Total_Amount_All_1,
    
 	 SUM(CASE WHEN bap.Fund = 'FN06' AND bap.Budget_Management_Year = 2567 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Amount_FN06_2,
    SUM(CASE WHEN bap.Fund = 'FN08' AND bap.Budget_Management_Year = 2567 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Amount_FN08_2,
    SUM(CASE WHEN bap.Fund = 'FN02' AND bap.Budget_Management_Year = 2567 THEN bap.Total_Amount_Quantity ELSE 0 END) AS Amount_FN02_2,
    
     SUM(
        CASE 
            WHEN bap.Fund IN ('FN06', 'FN08', 'FN02') 
            AND bap.Budget_Management_Year = 2567 
            THEN bap.Total_Amount_Quantity 
            ELSE 0 
        END
    ) AS Total_Amount_All_2,
    
    SUM(CASE WHEN baap.Fund = 'FN06'  THEN baap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Allocated_FN06_1,
    SUM(CASE WHEN baap.Fund = 'FN08'  THEN baap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Allocated_FN08_1,
    SUM(CASE WHEN baap.Fund = 'FN02'  THEN baap.Allocated_Total_Amount_Quantity ELSE 0 END) AS Allocated_FN02_1,
  
  
        SUM(
        CASE 
            WHEN baap.Fund IN ('FN06', 'FN08', 'FN02') 
            THEN baap.Allocated_Total_Amount_Quantity
            ELSE 0 
        END
    ) 
    - 
    SUM(
        CASE 
            WHEN bap.Fund IN ('FN06', 'FN08', 'FN02') 
            AND bap.Budget_Management_Year = 2567 
            THEN bap.Total_Amount_Quantity 
            ELSE 0 
        END
    ) AS Difference_Total_1


    
FROM budget_planning_annual_budget_plan bap
INNER JOIN Faculty ft 
    ON bap.Faculty = ft.Faculty 
    AND ft.parent LIKE 'Faculty%' 
LEFT JOIN sub_plan sp 
    ON sp.sub_plan_id = bap.Sub_Plan
LEFT JOIN project pj 
    ON pj.project_id = bap.Project
LEFT JOIN `account` ac 
    ON ac.`account` = bap.`Account`
LEFT JOIN plan p 
    ON p.plan_id = bap.Plan
LEFT JOIN budget_planning_allocated_annual_budget_plan baap
	 ON baap.Service = bap.Service
	 AND baap.Faculty = bap.Faculty
	 AND baap.Fund = bap.Fund
	 AND baap.Project = bap.Project
	 AND baap.Plan = bap.Plan
	 AND baap.Sub_Plan = bap.Sub_Plan
	 AND baap.`Account` = bap.`Account`

         if ($faculty) {
            $query .= " AND bap.Faculty = :faculty"
        }

GROUP BY 
    bap.Faculty,ft.Alias_Default,bap.Plan, p.plan_id, p.plan_name, 
    bap.Sub_Plan, sp.sub_plan_id, sp.sub_plan_name, 
    bap.Project, pj.project_id, pj.project_name, 
    a1, ac.`type`, bap.Total_Amount_Quantity,bap.Fund;
";

                                    

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

<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable th {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: middle;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }

    #reportTable td {
        text-align: left;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: top;
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
        max-height: 60vh;
        /* กำหนดความสูงของตาราง */
        overflow-y: auto;
        /* ทำให้สามารถเลื่อนข้อมูลในตารางได้ */
    }
</style>


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

                                <form method="GET">
                                    <label for="faculty">เลือกส่วนงาน/หน่วยงาน:</label>
                                    <select class="form-control" name="faculty" id="faculty"
                                        onchange="this.form.submit()">
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
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th colspan="2"rowspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                
                                                <th rowspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th rowspan="2">เงินนอกงบประมาณ</th>
                                                <th rowspan="2">เงินรายได้</th>
                                                <th rowspan="2">รวม</th>
                                                <th colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                
                                                <th colspan="2">เงินนอกงบประมาณ</th>
                                                
                                                <th colspan="2">เงินรายได้ </th>
                                                
                                                <th colspan="2">รวม</th>
                                                
                                               
                                            </tr>
                                            <tr>
                                            <th>คำขอ</th>
                                            <th>จัดสรร</th>
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
                                        <?php

                                                    function formatNumber($number)
                                                    {
                                                        return preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', sprintf("%0.2f", (float) $number));
                                                    }

                                                    function removeLeadingNumbers($text)
                                                    {
                                                        // ลบตัวเลขที่อยู่หน้าตัวหนังสือ
                                                        return preg_replace('/^[\d.]+\s*/', '', $text);
                                                    }
                                                    $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : "";
                                                    $results = fetchBudgetData($conn, $selectedFaculty);

                                                    // ตรวจสอบว่า $results มีข้อมูลหรือไม่
                                                    if (isset($results) && is_array($results) && count($results) > 0) {
                                                        $summary = [];
                                                        
                                                        // เรียงข้อมูลใน $results ให้เป็นแบบซ้อนกันตาม Faculty, Plan, Sub_Plan, Project
                                                        foreach ($results as $row) {
                                                            $faculty = $row['Faculty'];
                                                            $plan = $row['Plan'];
                                                            $subplan = $row['Sub_Plan'];
                                                            $project = $row['Project'];

                                                            // สร้างโครงสร้าง summary ถ้ายังไม่มี
                                                            if (!isset($summary[$faculty])) {
                                                                $summary[$faculty] = [
                                                                    'Faculty' => $row['Alias_Default'],
                                                                    'Plan' => [],
                                                                ];
                                                            }
                                                                
                                                            if (!isset($summary[$faculty]['Plan'][$plan])) {
                                                                $summary[$faculty]['Plan'][$plan] = [
                                                                    'PlanName' => $row['plan_name'],
                                                                    'Sub_Plan' => [],
                                                                ];
                                                            }

                                                            if (!isset($summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan])) {
                                                                $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan] = [
                                                                    'SubPlanName' => $row['sub_plan_name'],
                                                                    'Project' => [],
                                                                ];
                                                            }

                                                            if (!isset($summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project])) {
                                                                $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project] = [
                                                                    'ProjectName' => $row['project_name'],
                                                                    'type' => [],
                                                                ];
                                                            }

                                                            // เก็บข้อมูลของ type
                                                            $typeName = (!empty($row['type']))
                                                                ?  htmlspecialchars($row['a1']) . ": " . htmlspecialchars(removeLeadingNumbers($row['type']))
                                                                :  htmlspecialchars($row['a1']) ;

                                                            $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][] = [
                                                                'typeName' => $typeName,
                                                                
                                                            ];
                                                        }

                                                        // แสดงผลข้อมูลในตาราง
                                                        foreach ($summary as $faculty => $data) {
                                                            echo "<tr>";
                                                            echo "<td >" .  htmlspecialchars($faculty )  .' : '. htmlspecialchars($data['Alias_Default']) . "</td>";
                                                            echo "</tr>";
                                                            foreach ($data['Plan'] as $plan => $plandata) {
                                                                echo "<tr>";
                                                                echo "<td > " . htmlspecialchars($plan ) .' : '. htmlspecialchars($plandata['PlanName']) . "</td>";
                                                                echo "</tr>";
                                                                foreach ($plandata['Sub_Plan'] as $subplan => $subplandata) {
                                                                    echo "<tr>";
                                                                    echo "<td> " . str_repeat("&nbsp;", 8). htmlspecialchars(str_replace("SP_", "", $subplan)) . ' : ' . htmlspecialchars($subplandata['SubPlanName']) . "</td>";
                                                                    echo "</tr>";

                                                                    foreach ($subplandata['Project'] as $project => $projectdata) {
                                                                        echo "<tr>";
                                                                        $projectName = $projectdata['ProjectName']; // ข้อมูลเดิม เช่น "101102:การบริหารงานวิจัย"
                                                    $formattedProjectName = str_replace(':', ' : ', $projectName); // แทนที่ ":" ด้วย " : "
                                                    echo "<td>". str_repeat("&nbsp;", 16) . htmlspecialchars($formattedProjectName) . "</td>";
                                                                        echo "</tr>";

                                                                        if (isset($projectdata['type']) && is_array($projectdata['type'])) {
                                                                            foreach ($projectdata['type'] as $type) {
                                                                                echo "<tr>";
                                                                            
                                                                                // แสดงผลข้อมูลที่มี : คั่นระหว่าง $type และ cleanedSubType
                                                                                echo "<td style='text-align: left;'>". str_repeat("&nbsp;", 24) . htmlspecialchars($type['typeName']) . "<br></td>";
                                                                            
                                                                                echo "</tr>";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='9' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                                    }
                                                ?>

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
        const table = document.getElementById('reportTable');
        const csvRows = [];

        // วนลูปทีละ <tr>
        for (const row of table.rows) {
            // เก็บบรรทัดย่อยของแต่ละเซลล์
            const cellLines = [];
            let maxSubLine = 1;

            // วนลูปทีละเซลล์ <td>/<th>
            for (const cell of row.cells) {
                let html = cell.innerHTML;

                // 1) แปลง &nbsp; ติดกันให้เป็น non-breaking space (\u00A0) ตามจำนวน
                html = html.replace(/(&nbsp;)+/g, (match) => {
                    const count = match.match(/&nbsp;/g).length;
                    return '\u00A0'.repeat(count); // ex. 3 &nbsp; → "\u00A0\u00A0\u00A0"
                });


                // 3) (ถ้าต้องการ) ลบ tag HTML อื่นออก
                html = html.replace(/<\/?[^>]+>/g, '');

                // 4) แยกเป็น array บรรทัดย่อย
                const lines = html.split('\n').map(x => x.trimEnd());
                // ใช้ trimEnd() เฉพาะท้าย ไม่ trim ต้นเผื่อบางคนอยากเห็นช่องว่างนำหน้า

                if (lines.length > maxSubLine) {
                    maxSubLine = lines.length;
                }

                cellLines.push(lines);
            }

            // สร้าง sub-row ตามจำนวนบรรทัดย่อยสูงสุด
            for (let i = 0; i < maxSubLine; i++) {
                const rowData = [];

                // วนลูปแต่ละเซลล์
                for (const lines of cellLines) {
                    let text = lines[i] || ''; // ถ้าไม่มีบรรทัดที่ i ก็ว่าง
                    // Escape double quotes
                    text = text.replace(/"/g, '""');
                    // ครอบด้วย ""
                    text = `"${text}"`;
                    rowData.push(text);
                }

                csvRows.push(rowData.join(','));
            }
        }

        // รวมเป็น CSV + BOM
        const csvContent = "\uFEFF" + csvRows.join("\n");
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
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
        doc.text("รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ", 10, 500);

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
        doc.save('รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ.pdf');
    }

    function exportXLS() {
        const table = document.getElementById('reportTable');

        // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
        const {
            theadRows,
            theadMerges
        } = parseThead(table.tHead);

        // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br/>, ไม่ merge) ============
        const tbodyRows = parseTbody(table.tBodies[0]);

        // รวม rows ทั้งหมด: thead + tbody
        const allRows = [...theadRows, ...tbodyRows];

        // สร้าง Workbook + Worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(allRows);

        // ใส่ merges ของ thead ลงใน sheet (ถ้ามี)
        ws['!merges'] = theadMerges;

        // ตั้งค่า vertical-align: bottom ให้ทุกเซลล์
        applyCellStyles(ws, "bottom");

        // เพิ่ม worksheet ลงใน workbook
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        // เขียนไฟล์เป็น .xlsx (แทน .xls เพื่อรองรับ style)
        const excelBuffer = XLSX.write(wb, {
            bookType: 'xlsx',
            type: 'array'
        });

        // สร้าง Blob + ดาวน์โหลด
        const blob = new Blob([excelBuffer], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'รายงานการปรับเปลี่ยนงบประมาณของแผนงานต่างๆ.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    /**
     * -----------------------
     * 1) parseThead: รองรับ merge
     * -----------------------
     */
    function parseThead(thead) {
        const theadRows = [];
        const theadMerges = [];

        if (!thead) {
            return {
                theadRows,
                theadMerges
            };
        }

        const skipMap = {};

        for (let rowIndex = 0; rowIndex < thead.rows.length; rowIndex++) {
            const tr = thead.rows[rowIndex];
            const rowData = [];
            let colIndex = 0;

            for (let cellIndex = 0; cellIndex < tr.cells.length; cellIndex++) {
                while (skipMap[`${rowIndex},${colIndex}`]) {
                    rowData[colIndex] = "";
                    colIndex++;
                }

                const cell = tr.cells[cellIndex];
                let text = cell.innerHTML
                    .replace(/(&nbsp;)+/g, m => ' '.repeat(m.match(/&nbsp;/g).length)) // แทนที่ &nbsp; ด้วยช่องว่าง
                    .replace(/<\/?[^>]+>/g, '') // ลบแท็ก HTML ทั้งหมด
                    .trim();

                rowData[colIndex] = text;

                const rowspan = cell.rowSpan || 1;
                const colspan = cell.colSpan || 1;

                if (rowspan > 1 || colspan > 1) {
                    theadMerges.push({
                        s: {
                            r: rowIndex,
                            c: colIndex
                        },
                        e: {
                            r: rowIndex + rowspan - 1,
                            c: colIndex + colspan - 1
                        }
                    });

                    for (let r = 0; r < rowspan; r++) {
                        for (let c = 0; c < colspan; c++) {
                            if (r === 0 && c === 0) continue;
                            skipMap[`${rowIndex + r},${colIndex + c}`] = true;
                        }
                    }
                }
                colIndex++;
            }
            theadRows.push(rowData);
        }

        return {
            theadRows,
            theadMerges
        };
    }

    /**
     * -----------------------
     * 2) parseTbody: แตก <br/> เป็นหลาย sub-row
     * -----------------------
     */
    function parseTbody(tbody) {
        const rows = [];

        if (!tbody) return rows;

        for (const tr of tbody.rows) {
            const cellLines = [];
            let maxSubLine = 1;

            for (const cell of tr.cells) {
                let html = cell.innerHTML
                    .replace(/(&nbsp;)+/g, match => {
                        const count = match.match(/&nbsp;/g).length;
                        return ' '.repeat(count);
                    })
                    .replace(/<\/?[^>]+>/g, ''); // ลบแท็ก HTML ทั้งหมด

                const lines = html.split('\n').map(x => x.trimEnd());
                if (lines.length > maxSubLine) {
                    maxSubLine = lines.length;
                }
                cellLines.push(lines);
            }

            for (let i = 0; i < maxSubLine; i++) {
                const rowData = [];
                for (const lines of cellLines) {
                    rowData.push(lines[i] || '');
                }
                rows.push(rowData);
            }
        }

        return rows;
    }

    /**
     * -----------------------
     * 3) applyCellStyles: ตั้งค่า vertical-align ให้ทุก cell
     * -----------------------
     */
    function applyCellStyles(ws, verticalAlign) {
        if (!ws['!ref']) return;

        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const cell_address = XLSX.utils.encode_cell({
                    r: R,
                    c: C
                });
                if (!ws[cell_address]) continue;

                if (!ws[cell_address].s) ws[cell_address].s = {};
                ws[cell_address].s.alignment = {
                    vertical: verticalAlign
                };
            }
        }
    }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>


    <!-- โหลดไลบรารีที่จำเป็น -->
    <script src="../assets/plugins/common/common.min.js"></script>
    <script src="../js/custom.min.js"></script>


    <!-- โหลดฟอนต์ THSarabun (ตรวจสอบไม่ให้ประกาศซ้ำ) -->
    <script>
    if (typeof window.thsarabunnew_webfont_normal === 'undefined') {
        window.thsarabunnew_webfont_normal = "data:font/truetype;base64,AAEAAA...";
    }
    </script>
</body>

</html>