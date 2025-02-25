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

                                        function formatNumber($number)
                                        {
                                            return preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', sprintf("%0.2f", (float) $number));
                                        }

                                        function removeLeadingNumbers($text)
                                        {
                                            // ลบตัวเลขที่อยู่หน้าตัวหนังสือ
                                            return preg_replace('/^[\d.]+\s*/', '', $text);
                                        }

                                        $previousType = "";
                                        $previousSubType = "";
                                        $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;

                                        // ดึงข้อมูล faculty จากฐานข้อมูล
                                        $faculties = fetchFacultyData($conn);

                                        // ตรวจสอบว่ามีการเลือก faculty หรือไม่
                                        $selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : null;

                                        // ดึงข้อมูล budget โดยส่งค่า faculty ที่เลือก (หรือ null ถ้าไม่เลือก)
                                        $results = fetchBudgetData($conn, $selectedFaculty);

                                        // แสดงข้อมูลในตาราง
                                        if (isset($results) && is_array($results) && count($results) > 0) {
                                            // สร้าง associative array เพื่อเก็บผลรวมของแต่ละ Plan, Sub_Plan, Project, และ Sub_Type
                                            $summary = [];
                                            foreach ($results as $row) {
                                                $type = $row['type'];
                                                $sub_type = $row['sub_type'];

                                                if (!isset($summary[$type])) {
                                                    $summary[$type] = [
                                                        'type' => $row['type'],
                                                        'a1' => $row['a1'],
                                                        'Total_Amount_2567_FN06' => 0,
                                                        'Total_Amount_2567_FN08' => 0,
                                                        'Total_Amount_2567_FN02' => 0,
                                                        'Total_Amount_2567_SUM' => 0,
                                                        'Total_Amount_2568_FN06' => 0,
                                                        'Total_Amount_2568_FN08' => 0,
                                                        'Total_Amount_2568_FN02' => 0,
                                                        'Total_Amount_2568_SUM' => 0,
                                                        'Difference_2568_2567' => 0,
                                                        'Percentage_2568_to_2567' => 0,
                                                        'sub_type' => [], // เก็บข้อมูลของ Sub_Plan
                                                    ];
                                                }
                                                // เก็บข้อมูลของ Sub_Plan
                                                if (!isset($summary[$type]['sub_type'][$sub_type])) {
                                                    $summary[$type]['sub_type'][$sub_type] = [
                                                        'sub_type' => $row['sub_type'],
                                                        'a2' => $row['a2'],
                                                        'Total_Amount_2567_FN06' => 0,
                                                        'Total_Amount_2567_FN08' => 0,
                                                        'Total_Amount_2567_FN02' => 0,
                                                        'Total_Amount_2567_SUM' => 0,
                                                        'Total_Amount_2568_FN06' => 0,
                                                        'Total_Amount_2568_FN08' => 0,
                                                        'Total_Amount_2568_FN02' => 0,
                                                        'Total_Amount_2568_SUM' => 0,
                                                        'Difference_2568_2567' => 0,
                                                        'Percentage_2568_to_2567' => 0,
                                                        'kku_items' => [], // เก็บข้อมูลของ Sub_Plan
                                                    ];
                                                }
                                                // รวมข้อมูลของ type
                                                $summary[$type]['Total_Amount_2567_FN06'] += $row['Total_Amount_2567_FN06'];
                                                $summary[$type]['Total_Amount_2567_FN08'] += $row['Total_Amount_2567_FN08'];
                                                $summary[$type]['Total_Amount_2567_FN02'] += $row['Total_Amount_2567_FN02'];
                                                $summary[$type]['Total_Amount_2568_FN06'] += $row['Total_Amount_2568_FN06'];
                                                $summary[$type]['Total_Amount_2568_FN08'] += $row['Total_Amount_2568_FN08'];
                                                $summary[$type]['Total_Amount_2568_FN02'] += $row['Total_Amount_2568_FN02'];

                                                // รวมข้อมูลของ Subtype
                                                $summary[$type]['sub_type'][$sub_type]['Total_Amount_2567_FN06'] += $row['Total_Amount_2567_FN06'];
                                                $summary[$type]['sub_type'][$sub_type]['Total_Amount_2567_FN08'] += $row['Total_Amount_2567_FN08'];
                                                $summary[$type]['sub_type'][$sub_type]['Total_Amount_2567_FN02'] += $row['Total_Amount_2567_FN02'];
                                                $summary[$type]['sub_type'][$sub_type]['Total_Amount_2568_FN06'] += $row['Total_Amount_2568_FN06'];
                                                $summary[$type]['sub_type'][$sub_type]['Total_Amount_2568_FN08'] += $row['Total_Amount_2568_FN08'];
                                                $summary[$type]['sub_type'][$sub_type]['Total_Amount_2568_FN02'] += $row['Total_Amount_2568_FN02'];


                                                // เก็บข้อมูลของ KKU_Item_Name
                                                $kkuItemName = (!empty($row['KKU_Item_Name']))
                                                    ? "<strong>" . htmlspecialchars($row['Account']) . "</strong> : " . htmlspecialchars(removeLeadingNumbers($row['KKU_Item_Name']))
                                                    : "<strong>" . htmlspecialchars($row['Account']) . "</strong>";
                                                $summary[$type]['sub_type'][$sub_type]['kku_items'][] = [
                                                    'name' => $kkuItemName,
                                                    'Total_Amount_2567_FN06' => $row['Total_Amount_2567_FN06'],
                                                    'Total_Amount_2567_FN08' => $row['Total_Amount_2567_FN08'],
                                                    'Total_Amount_2567_FN02' => $row['Total_Amount_2567_FN02'],
                                                    'Total_Amount_2567_SUM' => $row['Total_Amount_2567_SUM'],
                                                    'Total_Amount_2568_FN06' => $row['Total_Amount_2568_FN06'],
                                                    'Total_Amount_2568_FN08' => $row['Total_Amount_2568_FN08'],
                                                    'Total_Amount_2568_FN02' => $row['Total_Amount_2568_FN02'],
                                                    'Total_Amount_2568_SUM' => $row['Total_Amount_2568_SUM'],
                                                    'Difference_2568_2567' => $row['Difference_2568_2567'],
                                                    'Percentage_2568_to_2567' => $row['Percentage_2568_to_2567'],
                                                ];

                                            }
                                            // แสดงผลลัพธ์
                                            foreach ($summary as $type => $data) {
                                                // แสดงผลรวมของ Plan
                                                echo "<tr>";
                                                $cleanedSubType = preg_replace('/^[\d.]+\s*/', '', $type);

                                                // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a2 และ subType
                                                echo "<td style='text-align: left; '>" . htmlspecialchars($data['a1']) . " : " . htmlspecialchars($cleanedSubType) . "<br></td>";

                                                echo "<td>" . formatNumber($data['Total_Amount_2567_FN06']) . "</td>";
                                                echo "<td>" . formatNumber($data['Total_Amount_2567_FN08']) . "</td>";
                                                echo "<td>" . formatNumber($data['Total_Amount_2567_FN02']) . "</td>";
                                                $total1 = $data['Total_Amount_2567_FN06'] + $data['Total_Amount_2567_FN08'] + $data['Total_Amount_2567_FN02'];
                                                echo "<td>" . formatNumber($total1) . "</td>";
                                                echo "<td>" . formatNumber($data['Total_Amount_2568_FN06']) . "</td>";
                                                echo "<td>" . formatNumber($data['Total_Amount_2568_FN08']) . "</td>";
                                                echo "<td>" . formatNumber($data['Total_Amount_2568_FN02']) . "</td>";
                                                $total2 = $data['Total_Amount_2568_FN06'] + $data['Total_Amount_2568_FN08'] + $data['Total_Amount_2568_FN02'];
                                                echo "<td>" . formatNumber($total2) . "</td>";
                                                $Difference = $total2 - $total1;
                                                echo "<td>" . formatNumber($Difference) . "</td>";
                                                $Percentage_Difference = ($total1 != 0) ? ($Difference / $total1) * 100 : 100;
                                                echo "<td>" . formatNumber($Percentage_Difference) . "</td>";

                                                echo "</tr>";
                                                foreach ($data['sub_type'] as $sub_type => $SubTypeData) {
                                                    // แสดงผลรวมของ Plan
                                                    echo "<tr>";
                                                    $cleanedSubType = preg_replace('/^[\d.]+\s*/', '', $sub_type);

                                                    // แสดงผลข้อมูลโดยเพิ่ม `:` คั่นระหว่าง a2 และ subType
                                                    echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 8) . htmlspecialchars($SubTypeData['a2']) . " : " . htmlspecialchars($cleanedSubType) . "<br></td>";

                                                    echo "<td>" . formatNumber($SubTypeData['Total_Amount_2567_FN06']) . "</td>";
                                                    echo "<td>" . formatNumber($SubTypeData['Total_Amount_2567_FN08']) . "</td>";
                                                    echo "<td>" . formatNumber($SubTypeData['Total_Amount_2567_FN02']) . "</td>";
                                                    $total1 = $SubTypeData['Total_Amount_2567_FN06'] + $SubTypeData['Total_Amount_2567_FN08'] + $SubTypeData['Total_Amount_2567_FN02'];
                                                    echo "<td>" . formatNumber($total1) . "</td>";
                                                    echo "<td>" . formatNumber($SubTypeData['Total_Amount_2568_FN06']) . "</td>";
                                                    echo "<td>" . formatNumber($SubTypeData['Total_Amount_2568_FN08']) . "</td>";
                                                    echo "<td>" . formatNumber($SubTypeData['Total_Amount_2568_FN02']) . "</td>";
                                                    $total2 = $SubTypeData['Total_Amount_2568_FN06'] + $SubTypeData['Total_Amount_2568_FN08'] + $SubTypeData['Total_Amount_2568_FN02'];
                                                    echo "<td>" . formatNumber($total2) . "</td>";
                                                    $Difference = $total2 - $total1;
                                                    echo "<td>" . formatNumber($Difference) . "</td>";
                                                    $Percentage_Difference = ($total1 != 0) ? ($Difference / $total1) * 100 : 100;
                                                    echo "<td>" . formatNumber($Percentage_Difference) . "</td>";

                                                    echo "</tr>";

                                                    // แสดงข้อมูล KKU_Item_Name
                                                    foreach ($SubTypeData['kku_items'] as $kkuItem) {
                                                        echo "<tr>";

                                                        echo "<td style='text-align: left; '>" . str_repeat("&nbsp;", 16) . $kkuItem['name'] . "<br></td>";
                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_2567_FN06']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_2567_FN08']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_2567_FN02']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_2567_SUM']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_2568_FN06']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_2568_FN08']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_2568_FN02']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Total_Amount_2568_SUM']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Difference_2568_2567']) . "</td>";
                                                        echo "<td>" . formatNumber($kkuItem['Percentage_2568_to_2567']) . "</td>";

                                                        echo "</tr>";
                                                    }
                                                }
                                            }

                                        } else {
                                            echo "<tr><td colspan='9' style='color: red; font-weight: bold; font-size: 18px;'>ไม่มีข้อมูล</td></tr>";
                                        }

                                        /*
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
*/
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
                                <button onclick="exportXLSX()" class="btn btn-success m-t-15">Export XLSX</button>
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
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้.csv';
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
            doc.text("รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้", 10, 500);

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
            doc.save('รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้.pdf');
        }

        function exportXLSX() {
            const table = document.getElementById('reportTable');

            // ============ ส่วนที่ 1: ประมวลผล THEAD (รองรับ Merge) ============
            const { theadRows, theadMerges } = parseThead(table.tHead);

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
            const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'รายงานรายการกันเงินงบประมาณเหลื่อมปีประเภทมีการสร้างหนี้แล้ว ประเภทที่ยังไม่มีหนี้.xlsx'; // เปลี่ยนนามสกุลเป็น .xlsx
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
                return { theadRows, theadMerges };
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
                            s: { r: rowIndex, c: colIndex },
                            e: { r: rowIndex + rowspan - 1, c: colIndex + colspan - 1 }
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

            return { theadRows, theadMerges };
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
                    const cell_address = XLSX.utils.encode_cell({ r: R, c: C });
                    if (!ws[cell_address]) continue;

                    if (!ws[cell_address].s) ws[cell_address].s = {};
                    ws[cell_address].s.alignment = { vertical: verticalAlign };
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