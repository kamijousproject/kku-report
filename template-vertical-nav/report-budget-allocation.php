<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
        /* ปรับขนาดความกว้างของคอลัมน์ "รายการ" */
    }

    #reportTable1 th,
    #reportTable1 td {
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        vertical-align: middle;
        /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        white-space: nowrap;
        /* ป้องกันข้อความตัดบรรทัด */
    }


    #reportTable2 th,
    #reportTable2 td {
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

// ฟังก์ชันตัดตัวอักษรออกให้เหลือแค่ตัวเลข
function extractNumericPart($string)
{
    return preg_replace('/\D/', '', $string);
}

// ฟังก์ชันเปรียบเทียบ Fund และ Sub_Plan โดยตัดตัวอักษรออกให้เหลือแค่ตัวเลข
function compareSubPlanAndFund($subPlan1, $subPlan2, $fund1, $fund2)
{
    $subPlan1 = extractNumericPart($subPlan1);
    $subPlan2 = extractNumericPart($subPlan2);
    $fund1 = extractNumericPart($fund1);
    $fund2 = extractNumericPart($fund2);

    return $subPlan1 === $subPlan2 && $fund1 === $fund2;
}

// ฟังก์ชันดึงข้อมูล
function fetchBudgetData($conn, $fund)
{
    $query = "SELECT DISTINCT
    ksp.ksp_id AS Ksp_id,
    ksp.ksp_name AS Ksp_Name,
    bpanbp.Account,
    acc.type,
    acc.sub_type,
    bpanbp.Project,
    project.project_name,
    f.Alias_Default AS Faculty_Name,
    bpanbp.Faculty,
    bpanbp.Plan,
    p.plan_name AS Plan_Name,
    bpanbp.Sub_Plan,
    CONCAT('SP_', bpa.SUBPLAN) AS SUBPLAN,
    sp.sub_plan_name AS Sub_Plan_Name,
    bpanbp.Fund,
    CONCAT('FN', bpa.FUND) AS FUND,
    bpanbp.Reason AS Reason,
    bpanbp.KKU_Item_Name,
     -- แยก Scenario ออกเป็นแต่ละคอลัมน์
    (SELECT Release_Amount FROM budget_planning_disbursement_budget_plan_anl_release WHERE Scenario = 'ANL-RELEASE-1' AND Faculty = bpanbp.Faculty AND Plan = bpanbp.Plan AND Sub_Plan = bpanbp.Sub_Plan AND Project = bpanbp.Project AND Fund = bpanbp.Fund LIMIT 1) AS Scenario1,
    (SELECT Release_Amount FROM budget_planning_disbursement_budget_plan_anl_release WHERE Scenario = 'ANL-RELEASE-2' AND Faculty = bpanbp.Faculty AND Plan = bpanbp.Plan AND Sub_Plan = bpanbp.Sub_Plan AND Project = bpanbp.Project AND Fund = bpanbp.Fund LIMIT 1) AS Scenario2,
    (SELECT Release_Amount FROM budget_planning_disbursement_budget_plan_anl_release WHERE Scenario = 'ANL-RELEASE-3' AND Faculty = bpanbp.Faculty AND Plan = bpanbp.Plan AND Sub_Plan = bpanbp.Sub_Plan AND Project = bpanbp.Project AND Fund = bpanbp.Fund LIMIT 1) AS Scenario3,
    (SELECT Release_Amount FROM budget_planning_disbursement_budget_plan_anl_release WHERE Scenario = 'ANL-RELEASE-4' AND Faculty = bpanbp.Faculty AND Plan = bpanbp.Plan AND Sub_Plan = bpanbp.Sub_Plan AND Project = bpanbp.Project AND Fund = bpanbp.Fund LIMIT 1) AS Scenario4,
    bpanbp.Allocated_Total_Amount_Quantity,
    bpa.FISCAL_YEAR,
    bpdbpar.Pre_Release_Amount,
    bpa.TOTAL_BUDGET,
    bpa.TOTAL_CONSUMPTION,
    bpa.EXPENDITURES,
    bpa.COMMITMENTS,
    bpa.OBLIGATIONS
FROM
    budget_planning_allocated_annual_budget_plan bpanbp
    LEFT JOIN budget_planning_actual bpa 
        ON bpanbp.Fund = CONCAT('FN', bpa.FUND)
        AND bpanbp.Faculty = bpa.FACULTY
        AND bpanbp.`Account` = bpa.`Account`
        AND bpanbp.Plan = bpa.PLAN
        AND bpanbp.Sub_Plan = CONCAT('SP_', bpa.SUBPLAN)
        AND bpanbp.Project = bpa.PROJECT
    LEFT JOIN budget_planning_annual_budget_plan bpabp 
        ON bpanbp.Faculty = bpabp.Faculty
        AND bpanbp.Plan = bpabp.Plan
        AND bpanbp.`Account` = bpa.`Account`
        AND bpanbp.Sub_Plan = bpabp.Sub_Plan
        AND bpanbp.Project = bpabp.Project
        AND bpanbp.Fund = bpabp.Fund
LEFT JOIN budget_planning_disbursement_budget_plan_anl_release bpdbpar 
    ON bpanbp.Faculty = bpdbpar.Faculty
    AND bpanbp.Plan = bpdbpar.Plan
    AND bpanbp.Sub_Plan = bpdbpar.Sub_Plan
    AND bpanbp.Project = bpdbpar.Project
    AND bpanbp.Fund = bpdbpar.Fund
    AND bpdbpar.Scenario IN ('ANL-RELEASE-1', 'ANL-RELEASE-2')
    LEFT JOIN budget_planning_project_kpi bppk 
        ON bpanbp.Project = bppk.Project
    LEFT JOIN project 
        ON bpanbp.Project = project.project_id
    LEFT JOIN ksp 
        ON bppk.KKU_Strategic_Plan_LOV = ksp.ksp_id
    LEFT JOIN account acc 
        ON bpanbp.Account = acc.account
    LEFT JOIN Faculty AS f 
        ON bpanbp.Faculty = f.Faculty
    LEFT JOIN plan AS p 
        ON bpanbp.Plan = p.plan_id
    LEFT JOIN sub_plan AS sp 
        ON bpanbp.Sub_Plan = sp.sub_plan_id
    LEFT JOIN project AS pr 
        ON bpanbp.Project = pr.project_id;
    WHERE bpanbp.Fund = :fund";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':fund', $fund);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch results for FN02, FN06, FN08
$resultsFN02 = fetchBudgetData($conn, 'FN02');
$resultsFN06 = fetchBudgetData($conn, 'FN06');
$resultsFN08 = fetchBudgetData($conn, 'FN08');

$mergedData = [];

foreach ($resultsFN06 as $fn06) {
    // Matching FN02 and FN08 with FN06 based on Scenario
    $fn02Match = array_filter($resultsFN02, function ($fn02) use ($fn06) {
        return compareSubPlanAndFund($fn06['Sub_Plan'], $fn02['Sub_Plan'], $fn06['Fund'], $fn02['Fund']) &&
            (string) ($fn06['Plan'] ?? '') === (string) ($fn02['Plan'] ?? '') &&
            (string) ($fn06['Project'] ?? '') === (string) ($fn02['Project'] ?? '');
    });
    $fn08Match = array_filter($resultsFN08, function ($fn08) use ($fn06) {
        return compareSubPlanAndFund($fn06['Sub_Plan'], $fn08['Sub_Plan'], $fn06['Fund'], $fn08['Fund']) &&
            (string) ($fn06['Plan'] ?? '') === (string) ($fn08['Plan'] ?? '') &&
            (string) ($fn06['Project'] ?? '') === (string) ($fn08['Project'] ?? '');
    });

    $fn02 = reset($fn02Match);
    $fn08 = reset($fn08Match);

    // Handle Commitments, Expenditures, and Scenario for FN06, FN02, FN08
    $commitment_FN06 = ($fn06['COMMITMENTS'] ?? 0) + ($fn06['OBLIGATIONS'] ?? 0);
    $commitment_FN02 = ($fn02['COMMITMENTS'] ?? 0) + ($fn02['OBLIGATIONS'] ?? 0);
    $commitment_FN08 = ($fn08['COMMITMENTS'] ?? 0) + ($fn08['OBLIGATIONS'] ?? 0);

    // Calculate Total Allocated and Commitments
    $Total_Allocated = ($fn06['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn02['Allocated_Total_Amount_Quantity'] ?? 0) + ($fn08['Allocated_Total_Amount_Quantity'] ?? 0);
    $Total_Commitments = $commitment_FN06 + $commitment_FN02 + $commitment_FN08;
    $Total_Release_Amount = $fn06['Scenario1'] + $fn06['Scenario2'] + $fn06['Scenario3'] + $fn06['Scenario4'];

    // คำนวณผลรวมของ Scenario1, Scenario2, Scenario3, Scenario4
// คำนวณผลรวมของ Scenario1, Scenario2, Scenario3, Scenario4
    $Total_Scenario =
        ($fn06['Scenario1'] ?? 0) +
        ($fn06['Scenario2'] ?? 0) +
        ($fn06['Scenario3'] ?? 0) +
        ($fn06['Scenario4'] ?? 0);


    // เพิ่มข้อมูลรวมใน mergedData
    $mergedData[] = [
        'Account' => $fn06['Account'] ?? '-',
        'Ksp_id' => $fn06['Ksp_id'] ?? '-',
        'Ksp_Name' => $fn06['Ksp_Name'] ?? '-',
        'Plan' => $fn06['Plan'] ?? '',
        'Sub_Plan' => $fn06['Sub_Plan'] ?? '',
        'Reason' => $fn06['Reason'] ?? '',
        'Plan_Name' => $fn06['Plan_Name'] ?? '',
        'Sub_Plan_Name' => $fn06['Sub_Plan_Name'] ?? '',
        'Type' => $fn06['type'] ?? '',
        'Sub_Type' => $fn06['sub_type'] ?? '',
        'Project_Name' => $fn06['Project_Name'] ?? '',
        'KKU_Item_Name' => $fn06['KKU_Item_Name'] ?? '',
        'Allocated_FN06' => $fn06['Allocated_Total_Amount_Quantity'] ?? 0,
        'Commitments_FN06' => $commitment_FN06,
        'Expenditures_FN06' => $fn06['EXPENDITURES'] ?? 0,
        'Allocated_FN02' => $fn02['Allocated_Total_Amount_Quantity'] ?? 0,
        'Commitments_FN02' => $commitment_FN02,
        'Expenditures_FN02' => $fn02['EXPENDITURES'] ?? 0,
        'Allocated_FN08' => $fn08['Allocated_Total_Amount_Quantity'] ?? 0,
        'Commitments_FN08' => $commitment_FN08,
        'Expenditures_FN08' => $fn08['EXPENDITURES'] ?? 0,
        'Pre_Release_Amount' => $fn06['Pre_Release_Amount'] ?? 0,
        'Total_Allocated' => $Total_Allocated,
        'Total_Commitments' => $Total_Commitments,
        'Scenario1' => ($fn06['Scenario1'] ?? 0),
        'Scenario2' => ($fn06['Scenario2'] ?? 0),
        'Scenario3' => ($fn06['Scenario3'] ?? 0),
        'Scenario4' => ($fn06['Scenario4'] ?? 0),
        'Total_Scenarios' => $Total_Scenario,  // ผลรวมของ Scenario ทั้งหมด
    ];

}

// Creating rowspan for scenarios
$rowspanData = [];

foreach ($mergedData as $row) {
    $type = $row['Type'] ?? '';
    $subType = $row['Sub_Type'] ?? '';

    if (!isset($rowspanData[$type][$subType])) {
        $rowspanData[$type][$subType] = 1;
    } else {
        $rowspanData[$type][$subType]++;
    }
}

$usedRowspan = [];


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
                        <h4>รายงานการจัดสรรเงินรายงวด</span></h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a>
                            </li>
                            <li class="breadcrumb-item active">รายงานการจัดสรรเงินรายงวด</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการจัดสรรเงินรายงวด</h4>
                                </div>
                                <div class="card-title">
                                    <span>จัดสรรงวดที่ 1 ครั้งที่ 1 </span>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable1" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th rowspan="2">รวมทั้งสิ้น</th>
                                                <th colspan="3">เงินงวด</th>
                                                <th rowspan="2">งบประมาณรายจ่ายคงเหลือ</th>
                                                <th rowspan="2">หมายเหตุ</th>
                                            </tr>
                                            <tr>
                                                <th>เงินจัดสรรกำหนดให้แล้ว</th>
                                                <th>เงินจัดสรรอนุมัติงวดที่ 1</th>
                                                <th>รวมเงินจัดสรรทั้งสิ้น</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // ตัวแปรเก็บค่าของแถวก่อนหน้า
                                            $previousAccount = "";
                                            $previousType = "";
                                            $previousPlan = "";
                                            $previousSubPlan = "";
                                            $previousSubType = "";
                                            $previousKKU_Item_Name = "";  // เก็บค่าของ KKU_Item_Name
                                            
                                            // วนลูปแสดงข้อมูลที่รวมกัน
                                            foreach ($mergedData as $row) {
                                                echo "<tr>";
                                                echo "<td style='text-align: left;'>";  // เริ่มต้น <td> สำหรับแสดงข้อมูล
                                            
                                                // เช็คว่า Account ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่
                                                if ($row['Account'] != $previousAccount) {
                                                    $previousAccount = $row['Account'];  // เก็บค่าปัจจุบันของ Account
                                                }

                                                if ($row['Plan'] != $previousPlan) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 5) . "{$row['Plan']} : {$row['Plan_Name']}</strong><br>";
                                                    $previousPlan = $row['Plan'];  // อัปเดตค่า previousPlan
                                                }

                                                if ($row['Sub_Plan'] != $previousSubPlan) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 10) . "{$row['Sub_Plan']} : {$row['Sub_Plan_Name']}</strong><br>";
                                                    $previousSubPlan = $row['Sub_Plan'];  // เก็บค่าปัจจุบันของ Type
                                                }

                                                if ($row['Type'] != $previousType) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 15) . "{$row['Type']}</strong><br>";
                                                    $previousType = $row['Type'];  // เก็บค่าปัจจุบันของ Type
                                                }

                                                // เช็คว่า Sub_Type ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่
                                                if ($row['Sub_Type'] != $previousSubType) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 25) . "{$row['Sub_Type']}</strong><br>";
                                                    $previousSubType = $row['Sub_Type'];  // เก็บค่าปัจจุบันของ Sub_Type
                                                }

                                                // แสดงค่า KKU_Item_Name (เอาคำแรกของ KKU_Item_Name มาแสดง)
                                                echo "<strong>" . str_repeat('&nbsp;', 30) . implode(' ', array_slice(explode(' ', $row['KKU_Item_Name']), 0, 1)) . "</strong>";

                                                echo "</td>"; // ปิด <td>
                                            
                                                // แสดงค่า Allocated_FN06, Pre_Release_Amount, Scenario1
                                                echo "<td>" . ($row['Allocated_FN06'] ?? '-') . "</td>";
                                                echo "<td>" . ($row['Pre_Release_Amount'] ?? '-') . "</td>";
                                                echo "<td>" . ($row['Scenario1'] ?? '-') . "</td>";

                                                // คำนวณ Total_Scenarios1 = Allocated_FN06 + Pre_Release_Amount
                                                $Allocated_FN06 = isset($row['Allocated_FN06']) ? (float) $row['Allocated_FN06'] : 0.00;
                                                $Scenario1 = isset($row['Scenario1']) ? (float) $row['Scenario1'] : 0.00;
                                                $Pre_Release_Amount = isset($row['Pre_Release_Amount']) ? (float) $row['Pre_Release_Amount'] : 0.00;
                                                $Total_Scenarios1 = $Scenario1 + $Pre_Release_Amount;

                                                // แสดงค่า Total_Scenarios1
                                                echo "<td>" . sprintf('%.2f', $Total_Scenarios1) . "</td>";

                                                // คำนวณ Remaining_budget = Allocated_FN06 - Total_Scenarios1
                                                $Remaining_budget = $Allocated_FN06 - $Total_Scenarios1;
                                                echo "<td>" . sprintf('%.2f', $Remaining_budget) . "</td>";

                                                echo "<td>" . ($row['Reason'] ?? '-') . "</td>";

                                                echo "</tr>"; // ปิด <tr>
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button onclick="exportCSV('reportTable1', 'งวดที่1-ครั้งที่1.csv')"
                                    class="btn btn-primary m-t-15">Export CSV งวดที่ 1 ครั้งที่ 1</button>
                                <button onclick="exportPDF('reportTable1', 'งวดที่1-ครั้งที่1.pdf')"
                                    class="btn btn-danger m-t-15">Export PDF งวดที่ 1 ครั้งที่ 1</button>
                                <button onclick="exportXLS('reportTable1', 'งวดที่1-ครั้งที่1.xls')"
                                    class="btn btn-success m-t-15">Export XLS งวดที่ 1 ครั้งที่ 1</button>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>รายงานการจัดสรรเงินรายงวด</h4>
                                </div>
                                <div class="card-title">
                                    <span>จัดสรรงวดที่ 2 ครั้งที่ 1 </span>
                                </div>
                                <div class="table-responsive">
                                    <table id="reportTable2" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">รายการ</th>
                                                <th rowspan="2">รวมทั้งสิ้น</th>
                                                <th colspan="3">เงินงวด</th>
                                                <th rowspan="2">งบประมาณรายจ่ายคงเหลือ</th>
                                                <th rowspan="2">หมายเหตุ</th>
                                            </tr>
                                            <tr>
                                                <th>เงินจัดสรรกำหนดให้แล้ว</th>
                                                <th>เงินจัดสรรอนุมัติงวดที่ 2</th>
                                                <th>รวมเงินจัดสรรทั้งสิ้น</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // ตัวแปรเก็บค่าของแถวก่อนหน้า
                                            $previousAccount = "";
                                            $previousType = "";
                                            $previousPlan = "";
                                            $previousSubPlan = "";
                                            $previousSubType = "";
                                            $previousKKU_Item_Name = "";  // เก็บค่าของ KKU_Item_Name
                                            
                                            // วนลูปแสดงข้อมูลที่รวมกัน
                                            foreach ($mergedData as $row) {
                                                echo "<tr>";
                                                echo "<td style='text-align: left;'>";  // เริ่มต้น <td> สำหรับแสดงข้อมูล
                                            
                                                // เช็คว่า Account ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่
                                                if ($row['Account'] != $previousAccount) {
                                                    $previousAccount = $row['Account'];  // เก็บค่าปัจจุบันของ Account
                                                }

                                                if ($row['Plan'] != $previousPlan) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 5) . "{$row['Plan']} : {$row['Plan_Name']}</strong><br>";
                                                    $previousPlan = $row['Plan'];  // อัปเดตค่า previousPlan
                                                }

                                                if ($row['Sub_Plan'] != $previousSubPlan) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 10) . "{$row['Sub_Plan']} : {$row['Sub_Plan_Name']}</strong><br>";
                                                    $previousSubPlan = $row['Sub_Plan'];  // เก็บค่าปัจจุบันของ Type
                                                }

                                                if ($row['Type'] != $previousType) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 15) . "{$row['Type']}</strong><br>";
                                                    $previousType = $row['Type'];  // เก็บค่าปัจจุบันของ Type
                                                }

                                                // เช็คว่า Sub_Type ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่
                                                if ($row['Sub_Type'] != $previousSubType) {
                                                    echo "<strong>" . str_repeat('&nbsp;', 25) . "{$row['Sub_Type']}</strong><br>";
                                                    $previousSubType = $row['Sub_Type'];  // เก็บค่าปัจจุบันของ Sub_Type
                                                }

                                                // เช็คว่า KKU_Item_Name ก่อนหน้าต่างจากแถวปัจจุบันหรือไม่
                                                echo "<strong>" . str_repeat('&nbsp;', 30) . implode(' ', array_slice(explode(' ', $row['KKU_Item_Name']), 0, 1)) . "</strong>";

                                                echo "</td>";  // ปิด <td>
                                            
                                                // แสดงค่า Allocated_FN06, Scenario1, Scenario2, Scenario3
                                                echo "<td>" . ($row['Allocated_FN06'] ?? '-') . "</td>";
                                                echo "<td>" . ($row['Scenario1'] ?? '-') . "</td>";
                                                echo "<td>" . ($row['Scenario2'] ?? '-') . "</td>";


                                                // คำนวณ Total_Scenarios
                                                $Total_Scenarios = isset($row['Total_Scenarios']) && $row['Total_Scenarios'] !== '' ? sprintf('%.2f', $row['Total_Scenarios']) : '0.00';
                                                echo "<td>" . $Total_Scenarios . "</td>";

                                                // คำนวณ Remaining_budget = Allocated_FN06 - Total_Scenarios
                                                $Allocated_FN06 = isset($row['Allocated_FN06']) ? (float) $row['Allocated_FN06'] : 0.00;
                                                $Remaining_budget = $Allocated_FN06 - (float) $Total_Scenarios;
                                                echo "<td>" . sprintf('%.2f', $Remaining_budget) . "</td>";
                                                echo "<td>" . ($row['Reason'] ?? '-') . "</td>";
                                                echo "</tr>";  // ปิด <tr>
                                            }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                                <button onclick="exportCSV('reportTable2', 'งวดที่2-ครั้งที่1.csv')"
                                    class="btn btn-primary m-t-15">Export CSV งวดที่ 2 ครั้งที่ 1</button>
                                <button onclick="exportPDF('reportTable2', 'งวดที่2-ครั้งที่1.pdf')"
                                    class="btn btn-danger m-t-15">Export PDF งวดที่ 2 ครั้งที่ 1</button>
                                <button onclick="exportXLS('reportTable2', 'งวดที่2-ครั้งที่1.xls')"
                                    class="btn btn-success m-t-15">Export XLS งวดที่ 2 ครั้งที่ 1</button>

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
        <script>
            function exportCSV(tableId, filename) {
                const rows = [];
                const table = document.getElementById(tableId);

                for (let row of table.rows) {
                    const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                    rows.push(cells.join(","));
                }

                const csvContent = "\uFEFF" + rows.join("\n"); // Add BOM for UTF-8
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);

                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            function exportXLS(tableId, filename) {
                const rows = [];
                const table = document.getElementById(tableId);

                for (let row of table.rows) {
                    const cells = Array.from(row.cells).map(cell => cell.innerText.trim());
                    rows.push(cells);
                }

                let xlsContent = "<table>";
                rows.forEach(row => {
                    xlsContent += "<tr>" + row.map(cell => `<td>${cell}</td>`).join('') + "</tr>";
                });
                xlsContent += "</table>";

                const blob = new Blob([xlsContent], { type: 'application/vnd.ms-excel' });
                const url = URL.createObjectURL(blob);

                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            function exportPDF(tableId, filename) {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape');

                doc.setFont("THSarabun");
                doc.setFontSize(12);
                doc.text("รายงานการจัดสรรเงินรายงวด", 10, 10);

                doc.autoTable({
                    html: `#${tableId}`,
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

                doc.save(filename);
            }
        </script>
        <!-- Common JS -->
        <script src="../assets/plugins/common/common.min.js"></script>
        <!-- Custom script -->
        <script src="../js/custom.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

        <!-- โหลดฟอนต์ THSarabun -->
        <script>
            const thsarabunnew_webfont_normal = "data:font/truetype;base64,AAEAAA...";
        </script>
</body>

</html>