<?php
include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();

// ฟังก์ชันดึงข้อมูล
function fetchBudgetData($conn, $fund)
{
    $query = "SELECT DISTINCT
        ksp.ksp_name,
	    project.project_name,
	    bpanbp.Account,
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
	    bpa.COMMITMENTS,
	    bpa.OBLIGATIONS,
	    f.Alias_Default AS Faculty_Name,
	    p.plan_name AS Plan_Name,
	    sp.sub_plan_name AS Sub_Plan_Name,
	    pr.project_name AS Project_Name
FROM
	budget_planning_allocated_annual_budget_plan bpanbp
	LEFT JOIN budget_planning_actual bpa ON bpanbp.Faculty = bpa.FACULTY
	AND bpanbp.Plan = bpa.PLAN
	AND bpanbp.Sub_Plan = bpa.SUBPLAN
	AND bpanbp.Project = bpa.PROJECT
	AND bpanbp.Fund = bpa.FUND
	LEFT JOIN budget_planning_annual_budget_plan bpabp ON bpanbp.Faculty = bpabp.Faculty
	AND bpanbp.Plan = bpabp.Plan
	AND bpanbp.Sub_Plan = bpabp.Sub_Plan
	AND bpanbp.Project = bpabp.Project
	AND bpanbp.Fund = bpabp.Fund
	LEFT JOIN budget_planning_project_kpi bppk ON bpanbp.Project = bppk.Project
	LEFT JOIN project ON bpanbp.Project = project.project_id
	LEFT JOIN ksp ON bppk.KKU_Strategic_Plan_LOV = ksp.ksp_id
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
        return (string) ($fn06['Plan'] ?? '') === (string) ($fn02['Plan'] ?? '') &&
            (string) ($fn06['Sub_Plan'] ?? '') === (string) ($fn02['Sub_Plan'] ?? '') &&
            (string) ($fn06['Project'] ?? '') === (string) ($fn02['Project'] ?? '') &&
            (string) ($fn06['Account'] ?? '') === (string) ($fn02['ACCOUNT'] ?? '');
    });


    $fn02 = reset($fn02Match);

    // ✅ ตรวจสอบและกำหนดค่าเริ่มต้นเพื่อป้องกัน Warning
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

    // ✅ คำนวณค่า Total
    $Total_Allocated = ($fn06['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0);
    $Total_Commitments = $commitment_FN06 + $commitment_FN02;
    $Total_Commitments_Percent = $commitment_percent_FN06 + $commitment_percent_FN02;
    $Total_Expenditures = ($fn06['EXPENDITURES'] ?? 0) + ($fn02['EXPENDITURES'] ?? 0);
    $Total_Expenditures_Percent = $Expenditures_Percent_FN06 + $Expenditures_Percent_FN02;

    // ✅ เพิ่มข้อมูลลงใน mergedData พร้อมป้องกัน Undefined Index
    $mergedData[] = [
        'Plan' => $fn06['Plan'] ?? '',
        'ksp_name' => $fn06['ksp_name'] ?? '',
        'Sub_Plan' => $fn06['Sub_Plan'] ?? '',
        'Project' => $fn06['Project'] ?? '',
        'Plan_Name' => $fn06['Plan_Name'] ?? '',
        'Sub_Plan_Name' => $fn06['Sub_Plan_Name'] ?? '',
        'Project_Name' => $fn06['Project_Name'] ?? '',
        'KKU_Item_Name' => $fn06['KKU_Item_Name'] ?? '',
        'Faculty_Name' => $fn06['Faculty_Name'] ?? '', // ✅ เพิ่มข้อมูล Faculty
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
// foreach ($mergedData as $row) {
//     $type = $row['Type'] ?? '';
//     $subType = $row['Sub_Type'] ?? '';

//     $key = $row['Plan'] . '|' . $row['Sub_Plan'] . '|' . $row['Project'] . '|' . $type . '|' . $subType;

//     if (!isset($rowspanData[$key])) {
//         $rowspanData[$key] = 0;
//     }
//     $rowspanData[$key]++;
// }


// ใช้ตัวแปรนี้เพื่อติดตามแถวที่ถูก merge ไปแล้ว
$usedRowspan = [];
?>

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
                        <h4>รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รรายงานสถานการณ์ใช้จ่ายงบประมาณตามแหล่งเงิน</h4>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="8">ปี 2566</th>
                                                <th colspan="8">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th rowspan="2" colspan="2">เพิ่ม/ลด</th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">เงินอุดหนุนจากรัฐ (FN06)</th>
                                                <th colspan="2">เงินนอกงบประมาณ (FN08)</th>
                                                <th colspan="2">เงินรายได้ (FN02)</th>
                                                <th colspan="2">รวม</th>

                                                <th colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th colspan="2">เงินนอกงบประมาณ</th>
                                                <th colspan="2">จำนวน</th>
                                                <th colspan="2">รวม</th>

                                                <th rowspan="2" colspan="2">เงินอุดหนุนจากรัฐ</th>
                                                <th rowspan="2" colspan="2">เงินรายได้</th>
                                                <th rowspan="2" colspan="2">เงินนอกงบประมาณ</th>
                                                <th rowspan="2" colspan="2">รวม</th>
                                            </tr>
                                            <tr>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>ประมาณการ</th>
                                                <th>จ่ายจริง</th>
                                                <th>จำนวน</th>
                                                <th>ร้อยละ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($mergedData as $row) {
                                                echo "<tr>";
                                                echo "<td style='text-align: left;'>";

                                                // แสดงข้อมูล ksp_name
                                                echo "<strong>" . str_repeat('&nbsp;', 5) . "{$row['ksp_name']} : {$row['ksp_name']}</strong><br>";

                                                // แสดงข้อมูล Sub_Plan
                                                $subPlanName = preg_replace('/\([^\)]*\)/', '', $row['Sub_Plan_Name']); // ลบข้อมูลในวงเล็บ
                                                $subPlan = preg_replace('/[a-zA-Z_]+/', '', $row['Sub_Plan']); // ลบตัวหนังสือและ _ จาก Sub_Plan
                                                echo "<strong>" . str_repeat('&nbsp;', 10) . "{$subPlan} : {$subPlanName}</strong><br>";

                                                // แสดงข้อมูล Project
                                                $projectName = preg_replace('/(\d+):(\S)/', '$1 : $2', $row['Project_Name']); // แก้ไข Project_Name
                                                echo "<strong>" . str_repeat('&nbsp;', 15) . "{$projectName}</strong><br>";

                                                // แสดงข้อมูล KKU_Item_Name
                                                echo "<strong>" . str_repeat('&nbsp;', 30) . "{$row['KKU_Item_Name']}</strong>";
                                                echo "</td>";

                                                // แสดงข้อมูลในตารางตามที่ต้องการ (จำนวน Allocated, Expenditures)
                                                echo "<td>" . number_format($row['Allocated_FN06'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Expenditures_FN06'], 2) . "</td>";
                                                echo "<td></td>";
                                                echo "<td></td>";
                                                echo "<td>" . number_format($row['Allocated_FN02'], 2) . "</td>";
                                                echo "<td>" . number_format($row['Expenditures_FN02'], 2) . "</td>";

                                                echo "</tr>";
                                            }
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