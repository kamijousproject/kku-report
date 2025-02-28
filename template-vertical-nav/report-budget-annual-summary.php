<style>
    #reportTable th:nth-child(1),
    #reportTable td:nth-child(1) {
        width: 300px;
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
<?php

include('../component/header.php');
include '../server/connectdb.php';

$db = new Database();
$conn = $db->connect();
$faculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;
function fetchBudgetData($conn, $faculty = null)
{
    try {
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
    ) AS Total_Allocated1,
  
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
    ) AS Difference_Total_1,CASE 
    WHEN SUM(
        CASE 
            WHEN bap.Fund IN ('FN06', 'FN08', 'FN02') 
            AND bap.Budget_Management_Year = 2567 
            THEN bap.Total_Amount_Quantity 
            ELSE 0 
        END
    ) = 0 THEN 100  -- If denominator is 0, return 100%
    ELSE (
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
        )
    ) 
    / 
    SUM(
        CASE 
            WHEN bap.Fund IN ('FN06', 'FN08', 'FN02') 
            AND bap.Budget_Management_Year = 2567 
            THEN bap.Total_Amount_Quantity 
            ELSE 0 
        END
    ) * 100
END AS Percentage_Difference

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
	 AND baap.`Account` = bap.`Account`";

        if ($faculty) {
            $query .= " WHERE bap.Faculty = :faculty";
        }

        $query .= " GROUP BY 
          bap.Faculty,ft.Alias_Default,bap.Plan, p.plan_id, p.plan_name, 
    bap.Sub_Plan, sp.sub_plan_id, sp.sub_plan_name, 
    bap.Project, pj.project_id, pj.project_name, 
    a1, ac.`type`, bap.Total_Amount_Quantity,bap.Fund
        ORDER BY bap.Faculty ASC,
            bap.Plan ASC, 
            bap.Sub_Plan ASC, 
            bap.Project ASC,
            ac.`type` ASC";

        $stmt = $conn->prepare($query);

        if ($faculty) {
            $stmt->bindParam(':faculty', $faculty, PDO::PARAM_STR);
        }

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
<?php include('../component/header.php'); ?>

<body class="v-light vertical-nav fix-header fix-sidebar">
    <div id="main-wrapper">
        <?php include('../component/left-nev.php') ?>

        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4> รายงานสรุปการจัดทำและจัดสรรงบประมาณประจำปี</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active"> รายงานสรุปการจัดทำและจัดสรรงบประมาณประจำปี</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4> รายงานสรุปการจัดทำและจัดสรรงบประมาณประจำปี</h4>
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
                                    <table id="reportTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th rowspan="3">รายการ</th>
                                                <th colspan="4">ปี 2567 (ปีปัจจุบัน)</th>
                                                <th colspan="8">ปี 2568 (ปีที่ขอตั้งงบ)</th>
                                                <th colspan="2" rowspan="2">เพิ่ม/ลด</th>
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
                                                    $type = $row['type'];

                                                    // สร้างโครงสร้าง summary ถ้ายังไม่มี
                                                    if (!isset($summary[$faculty])) {
                                                        $summary[$faculty] = [
                                                            'Faculty' => $row['Alias_Default'],
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Total_Amount_All_2' => 0,
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Total_Amount_All_1' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Total_Allocated1' => 0,
                                                            'Difference_Total_1' => 0,
                                                            'Plan' => [],
                                                        ];
                                                    }

                                                    if (!isset($summary[$faculty]['Plan'][$plan])) {
                                                        $summary[$faculty]['Plan'][$plan] = [
                                                            'PlanName' => $row['plan_name'],
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Total_Amount_All_2' => 0,
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Total_Amount_All_1' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Total_Allocated1' => 0,
                                                            'Difference_Total_1' => 0,
                                                            'Sub_Plan' => [],
                                                        ];
                                                    }

                                                    if (!isset($summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan])) {
                                                        $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan] = [
                                                            'SubPlanName' => $row['sub_plan_name'],
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Total_Amount_All_2' => 0,
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Total_Amount_All_1' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Total_Allocated1' => 0,
                                                            'Difference_Total_1' => 0,
                                                            'Project' => [],
                                                        ];
                                                    }

                                                    if (!isset($summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project])) {
                                                        $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project] = [
                                                            'ProjectName' => $row['project_name'],
                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Total_Amount_All_2' => 0,
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Total_Amount_All_1' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Total_Allocated1' => 0,
                                                            'Difference_Total_1' => 0,
                                                            'type' => [],
                                                        ];
                                                    }

                                                    // เก็บข้อมูลของ type
                                                    $typeName = (!empty($row['type']))
                                                        ? htmlspecialchars($row['a1']) . " : " . htmlspecialchars(removeLeadingNumbers($row['type']))
                                                        : htmlspecialchars($row['a1']);

                                                    if (!isset($summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type])) {
                                                        $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type] = [
                                                            'typeName' => $typeName,

                                                            'Amount_FN06_2' => 0,
                                                            'Amount_FN08_2' => 0,
                                                            'Amount_FN02_2' => 0,
                                                            'Total_Amount_All_2' => 0,
                                                            'Amount_FN06_1' => 0,
                                                            'Amount_FN08_1' => 0,
                                                            'Amount_FN02_1' => 0,
                                                            'Total_Amount_All_1' => 0,
                                                            'Allocated_FN06_1' => 0,
                                                            'Allocated_FN08_1' => 0,
                                                            'Allocated_FN02_1' => 0,
                                                            'Total_Allocated1' => 0,
                                                            'Difference_Total_1' => 0,

                                                            'kku_items' => [],
                                                        ];
                                                    }

                                                    //รวมข้อมูลของ Faculty
                                                    $summary[$faculty]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$faculty]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$faculty]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$faculty]['Total_Amount_All_2'] += $row['Total_Amount_All_2'];
                                                    $summary[$faculty]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$faculty]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$faculty]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$faculty]['Total_Amount_All_1'] += $row['Total_Amount_All_1'];
                                                    $summary[$faculty]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$faculty]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$faculty]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$faculty]['Total_Allocated1'] += $row['Total_Allocated1'];

                                                    //รวมข้อมูลของ Plan
                                                    $summary[$faculty]['Plan'][$plan]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Total_Amount_All_2'] += $row['Total_Amount_All_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Total_Amount_All_1'] += $row['Total_Amount_All_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Total_Allocated1'] += $row['Total_Allocated1'];

                                                    //รวมข้อมูลของ Sub_Plan
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Total_Amount_All_2'] += $row['Total_Amount_All_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Total_Amount_All_1'] += $row['Total_Amount_All_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Total_Allocated1'] += $row['Total_Allocated1'];

                                                    //รวมข้อมูลของ Project
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Total_Amount_All_2'] += $row['Total_Amount_All_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Total_Amount_All_1'] += $row['Total_Amount_All_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['Total_Allocated1'] += $row['Total_Allocated1'];

                                                    //รวมข้อมูลของ Type
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Amount_FN06_2'] += $row['Amount_FN06_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Amount_FN08_2'] += $row['Amount_FN08_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Amount_FN02_2'] += $row['Amount_FN02_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Total_Amount_All_2'] += $row['Total_Amount_All_2'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Amount_FN06_1'] += $row['Amount_FN06_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Amount_FN08_1'] += $row['Amount_FN08_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Amount_FN02_1'] += $row['Amount_FN02_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Total_Amount_All_1'] += $row['Total_Amount_All_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Allocated_FN06_1'] += $row['Allocated_FN06_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Allocated_FN08_1'] += $row['Allocated_FN08_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Allocated_FN02_1'] += $row['Allocated_FN02_1'];
                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['Total_Allocated1'] += $row['Total_Allocated1'];



                                                    $summary[$faculty]['Plan'][$plan]['Sub_Plan'][$subplan]['Project'][$project]['type'][$type]['kku_items'][] = [

                                                        'Amount_FN06_2' => $row['Amount_FN06_2'],
                                                        'Amount_FN08_2' => $row['Amount_FN08_2'],
                                                        'Amount_FN02_2' => $row['Amount_FN02_2'],
                                                        'Total_Amount_All_2' => $row['Total_Amount_All_2'],
                                                        'Amount_FN06_1' => $row['Amount_FN06_1'],
                                                        'Amount_FN08_1' => $row['Amount_FN08_1'],
                                                        'Amount_FN02_1' => $row['Amount_FN02_1'],
                                                        'Total_Amount_All_1' => $row['Total_Amount_All_1'],
                                                        'Allocated_FN06_1' => $row['Allocated_FN06_1'],
                                                        'Allocated_FN08_1' => $row['Allocated_FN08_1'],
                                                        'Allocated_FN02_1' => $row['Allocated_FN02_1'],
                                                        'Total_Allocated1' => $row['Total_Allocated1'],
                                                        'Difference_Total_1' => $row['Difference_Total_1'],
                                                        'Percentage_Difference' => $row['Percentage_Difference'],


                                                    ];

                                                    $rows = $summary;
                                                    // ตัวแปรสำหรับเก็บผลรวมทั้งหมด
                                                    $total_summary = [
                                                        'Amount_FN06_2' => 0,
                                                        'Amount_FN08_2' => 0,
                                                        'Amount_FN02_2' => 0,
                                                        'Total_Amount_All_2' => 0,
                                                        'Amount_FN06_1' => 0,
                                                        'Amount_FN08_1' => 0,
                                                        'Amount_FN02_1' => 0,
                                                        'Total_Amount_All_1' => 0,
                                                        'Allocated_FN06_1' => 0,
                                                        'Allocated_FN08_1' => 0,
                                                        'Allocated_FN02_1' => 0,
                                                        'Total_Allocated1' => 0,
                                                        'Difference_Total_1' => 0,
                                                    ];
                                                    // แสดงผลรวมทั้งหมด
                                                    //print_r($total_summary);
                                                    // Assuming this is inside a loop where $row is updated (e.g., from a database query)
                                                    foreach ($rows as $row) { // Replace $rows with your actual data source
                                                        // รวมผลรวมทั้งหมดโดยไม่สนใจ Faculty
                                                        $total_summary['Amount_FN06_2'] += (float) ($row['Amount_FN06_2'] ?? 0);
                                                        $total_summary['Amount_FN08_2'] += (float) ($row['Amount_FN08_2'] ?? 0);
                                                        $total_summary['Amount_FN02_2'] += (float) ($row['Amount_FN02_2'] ?? 0);
                                                        $total_summary['Total_Amount_All_2'] += (float) ($row['Total_Amount_All_2'] ?? 0);

                                                        $total_summary['Amount_FN06_1'] += (float) ($row['Amount_FN06_1'] ?? 0);
                                                        $total_summary['Amount_FN08_1'] += (float) ($row['Amount_FN08_1'] ?? 0);
                                                        $total_summary['Amount_FN02_1'] += (float) ($row['Amount_FN02_1'] ?? 0);
                                                        $total_summary['Total_Amount_All_1'] += (float) ($row['Total_Amount_All_1'] ?? 0);

                                                        $total_summary['Allocated_FN06_1'] += (float) ($row['Allocated_FN06_1'] ?? 0);
                                                        $total_summary['Allocated_FN08_1'] += (float) ($row['Allocated_FN08_1'] ?? 0);
                                                        $total_summary['Allocated_FN02_1'] += (float) ($row['Allocated_FN02_1'] ?? 0);
                                                        $total_summary['Total_Allocated1'] += (float) ($row['Total_Allocated1'] ?? 0);
                                                    }
                                                }
                                                if ($selectedFaculty == null) {
                                                    if (isset($summary) && is_array($summary)) {
                                                        // แสดงผลลัพธ์ในรูปแบบตาราง
                                                        echo "<tr>";
                                                        echo "<td>" . 'รวมทั้งสิ้น' . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Amount_FN06_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Amount_FN08_2']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Amount_FN02_2']) . "</td>";
                                                        $total1 = $total_summary['Amount_FN06_2'] + $total_summary['Amount_FN08_2'] + $total_summary['Amount_FN02_2'];
                                                        echo "<td>" . formatNumber($total1) . "</td>";

                                                        echo "<td>" . formatNumber($total_summary['Amount_FN06_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Allocated_FN06_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Amount_FN08_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Allocated_FN08_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Amount_FN02_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Allocated_FN02_1']) . "</td>";

                                                        echo "<td>" . formatNumber($total_summary['Total_Amount_All_1']) . "</td>";
                                                        echo "<td>" . formatNumber($total_summary['Total_Allocated1']) . "</td>";

                                                        $Difference_Total = $total_summary['Total_Allocated1'] - $total1;
                                                        echo "<td>" . formatNumber($Difference_Total) . "</td>";

                                                        if ($total1 == 0) {
                                                            $percentage = 100; // If denominator is 0, set percentage to 100%
                                                        } else {
                                                            $percentage = ($Difference_Total / $total1) * 100;
                                                        }

                                                        // Format the percentage to 2 decimal places
                                                        $formattedPercentage = number_format($percentage, 2);

                                                        // Output the result
                                                        echo "<td>" . $formattedPercentage . "%</td>";
                                                        echo "</tr>";
                                                    } else {
                                                        // แสดงข้อความหากไม่มีข้อมูล
                                                        echo "<tr><td colspan='7' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                                    }
                                                }


                                                // แสดงผลข้อมูลในตาราง
                                                foreach ($summary as $faculty => $data) {
                                                    echo "<tr>";
                                                    if ($selectedFaculty == null) {
                                                        $facultyData = str_replace('-', ':', $data['Faculty']);
                                                        echo "<td style='text-align: left;'><strong>" . htmlspecialchars($facultyData) . "<br></td>";
                                                    }
                                                    if ($selectedFaculty != null) {
                                                        echo "<td style='text-align: left;'><strong>" . 'รวมทั้งสิ้น' . "<br></td>";
                                                    }


                                                    echo "<td>" . formatNumber($data['Amount_FN06_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Amount_FN08_2']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Amount_FN02_2']) . "</td>";
                                                    $total1 = $data['Amount_FN06_2'] + $data['Amount_FN08_2'] + $data['Amount_FN02_2'];
                                                    echo "<td>" . formatNumber($total1) . "</td>";

                                                    echo "<td>" . formatNumber($data['Amount_FN06_1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Allocated_FN06_1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Amount_FN08_1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Allocated_FN08_1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Amount_FN02_1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Allocated_FN02_1']) . "</td>";

                                                    echo "<td>" . formatNumber($data['Total_Amount_All_1']) . "</td>";
                                                    echo "<td>" . formatNumber($data['Total_Allocated1']) . "</td>";

                                                    $Difference_Total = $data['Total_Allocated1'] - $total1;
                                                    echo "<td>" . formatNumber($Difference_Total) . "</td>";

                                                    if ($total1 == 0) {
                                                        $percentage = 100; // If denominator is 0, set percentage to 100%
                                                    } else {
                                                        $percentage = ($Difference_Total / $total1) * 100;
                                                    }

                                                    // Format the percentage to 2 decimal places
                                                    $formattedPercentage = number_format($percentage, 2);

                                                    // Output the result
                                                    echo "<td>" . $formattedPercentage . "%</td>";
                                                    echo "</tr>";

                                                    foreach ($data['Plan'] as $plan => $plandata) {
                                                        echo "<tr>";
                                                        echo "<td > " . htmlspecialchars($plan) . ' : ' . htmlspecialchars($plandata['PlanName']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Amount_FN06_2']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Amount_FN08_2']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Amount_FN02_2']) . "</td>";
                                                        $total1 = $plandata['Amount_FN06_2'] + $plandata['Amount_FN08_2'] + $plandata['Amount_FN02_2'];
                                                        echo "<td>" . formatNumber($total1) . "</td>";

                                                        echo "<td>" . formatNumber($plandata['Amount_FN06_1']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Allocated_FN06_1']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Amount_FN08_1']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Allocated_FN08_1']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Amount_FN02_1']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Allocated_FN02_1']) . "</td>";

                                                        echo "<td>" . formatNumber($plandata['Total_Amount_All_1']) . "</td>";
                                                        echo "<td>" . formatNumber($plandata['Total_Allocated1']) . "</td>";

                                                        $Difference_Total = $plandata['Total_Allocated1'] - $total1;
                                                        echo "<td>" . formatNumber($Difference_Total) . "</td>";

                                                        if ($total1 == 0) {
                                                            $percentage = 100; // If denominator is 0, set percentage to 100%
                                                        } else {
                                                            $percentage = ($Difference_Total / $total1) * 100;
                                                        }

                                                        // Format the percentage to 2 decimal places
                                                        $formattedPercentage = number_format($percentage, 2);

                                                        // Output the result
                                                        echo "<td>" . $formattedPercentage . "%</td>";
                                                        echo "</tr>";

                                                        foreach ($plandata['Sub_Plan'] as $subplan => $subplandata) {
                                                            echo "<tr>";
                                                            echo "<td> " . str_repeat("&nbsp;", 8) . htmlspecialchars(str_replace("SP_", "", $subplan)) . ' : ' . htmlspecialchars($subplandata['SubPlanName']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Amount_FN06_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Amount_FN08_2']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Amount_FN02_2']) . "</td>";
                                                            $total1 = $subplandata['Amount_FN06_2'] + $subplandata['Amount_FN08_2'] + $subplandata['Amount_FN02_2'];
                                                            echo "<td>" . formatNumber($total1) . "</td>";

                                                            echo "<td>" . formatNumber($subplandata['Amount_FN06_1']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Allocated_FN06_1']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Amount_FN08_1']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Allocated_FN08_1']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Amount_FN02_1']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Allocated_FN02_1']) . "</td>";

                                                            echo "<td>" . formatNumber($subplandata['Total_Amount_All_1']) . "</td>";
                                                            echo "<td>" . formatNumber($subplandata['Total_Allocated1']) . "</td>";

                                                            $Difference_Total = $subplandata['Total_Allocated1'] - $total1;
                                                            echo "<td>" . formatNumber($Difference_Total) . "</td>";

                                                            if ($total1 == 0) {
                                                                $percentage = 100; // If denominator is 0, set percentage to 100%
                                                            } else {
                                                                $percentage = ($Difference_Total / $total1) * 100;
                                                            }

                                                            // Format the percentage to 2 decimal places
                                                            $formattedPercentage = number_format($percentage, 2);

                                                            // Output the result
                                                            echo "<td>" . $formattedPercentage . "%</td>";
                                                            echo "</tr>";

                                                            foreach ($subplandata['Project'] as $project => $projectdata) {
                                                                echo "<tr>";
                                                                $projectName = $projectdata['ProjectName'];
                                                                $formattedProjectName = str_replace(':', ' : ', $projectName);
                                                                echo "<td>" . str_repeat("&nbsp;", 16) . htmlspecialchars($formattedProjectName) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Amount_FN06_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Amount_FN08_2']) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Amount_FN02_2']) . "</td>";
                                                                $total1 = $projectdata['Amount_FN06_2'] + $projectdata['Amount_FN08_2'] + $projectdata['Amount_FN02_2'];
                                                                echo "<td>" . formatNumber($total1) . "</td>";

                                                                echo "<td>" . formatNumber($projectdata['Amount_FN06_1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Allocated_FN06_1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Amount_FN08_1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Allocated_FN08_1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Amount_FN02_1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Allocated_FN02_1']) . "</td>";

                                                                echo "<td>" . formatNumber($projectdata['Total_Amount_All_1']) . "</td>";
                                                                echo "<td>" . formatNumber($projectdata['Total_Allocated1']) . "</td>";

                                                                $Difference_Total = $projectdata['Total_Allocated1'] - $total1;
                                                                echo "<td>" . formatNumber($Difference_Total) . "</td>";

                                                                if ($total1 == 0) {
                                                                    $percentage = 100; // If denominator is 0, set percentage to 100%
                                                                } else {
                                                                    $percentage = ($Difference_Total / $total1) * 100;
                                                                }

                                                                // Format the percentage to 2 decimal places
                                                                $formattedPercentage = number_format($percentage, 2);

                                                                // Output the result
                                                                echo "<td>" . $formattedPercentage . "%</td>";
                                                                echo "</tr>";


                                                                foreach ($projectdata['type'] as $type) {
                                                                    echo "<tr>";

                                                                    // แสดงผลข้อมูลที่มี : คั่นระหว่าง $type และ cleanedSubType
                                                                    echo "<td style='text-align: left;'>" . str_repeat("&nbsp;", 24) . htmlspecialchars($type['typeName']) . "<br></td>";
                                                                    echo "<td>" . formatNumber($type['Amount_FN06_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($type['Amount_FN08_2']) . "</td>";
                                                                    echo "<td>" . formatNumber($type['Amount_FN02_2']) . "</td>";
                                                                    $total1 = $type['Amount_FN06_2'] + $type['Amount_FN08_2'] + $type['Amount_FN02_2'];
                                                                    echo "<td>" . formatNumber($total1) . "</td>";

                                                                    echo "<td>" . formatNumber($type['Amount_FN06_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($type['Allocated_FN06_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($type['Amount_FN08_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($type['Allocated_FN08_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($type['Amount_FN02_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($type['Allocated_FN02_1']) . "</td>";

                                                                    echo "<td>" . formatNumber($type['Total_Amount_All_1']) . "</td>";
                                                                    echo "<td>" . formatNumber($type['Total_Allocated1']) . "</td>";

                                                                    $Difference_Total = $type['Total_Allocated1'] - $total1;
                                                                    echo "<td>" . formatNumber($Difference_Total) . "</td>";

                                                                    if ($total1 == 0) {
                                                                        $percentage = 100; // If denominator is 0, set percentage to 100%
                                                                    } else {
                                                                        $percentage = ($Difference_Total / $total1) * 100;
                                                                    }

                                                                    // Format the percentage to 2 decimal places
                                                                    $formattedPercentage = number_format($percentage, 2);

                                                                    // Output the result
                                                                    echo "<td>" . $formattedPercentage . "%</td>";

                                                                    echo "</tr>";
                                                                    foreach ($type['kku_items'] as $kkuItem) {

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
                                        <script>
                                            // การส่งค่าของ selectedFaculty ไปยัง JavaScript
                                            var selectedFaculty = "<?php echo isset($selectedFaculty) ? htmlspecialchars($selectedFaculty, ENT_QUOTES, 'UTF-8') : ''; ?>";
                                            console.log('Selected Faculty: ', selectedFaculty);</script>
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
            link.download = ' รายงานสรุปการจัดทำและจัดสรรงบประมาณประจำปี.csv';
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
            doc.text(" รายงานสรุปการจัดทำและจัดสรรงบประมาณประจำปี", 10, 500);

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
            doc.save(' รายงานสรุปการจัดทำและจัดสรรงบประมาณประจำปี.pdf');
        }

        function exportXLS() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const {
                theadRows,
                theadMerges
            } = parseThead(table.tHead);

            // ============ ส่วนที่ 2: ประมวลผล TBODY (แตก <br />, ไม่ merge) ============
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
            link.download = ' รายงานสรุปการจัดทำและจัดสรรงบประมาณประจำปี.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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