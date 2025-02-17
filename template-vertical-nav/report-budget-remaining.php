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

        border: 2px solid #000;
        /* เพิ่มความหนาของเส้นขอบเซลล์ */
        padding: 8px;
        /* เพิ่มช่องว่างภายในเซลล์ */
    }

    /* ทำให้ข้อมูลในตารางเลื่อนได้ */
    .table-responsive {
        max-height: 60vh;
        /* กำหนดความสูงของตาราง */
        overflow-y: auto;
        /* ทำให้สามารถเลื่อนข้อมูลในตารางได้ */
    }

    .container-custom {
        max-width: 1200px;
        /* กำหนดค่าความกว้างสูงสุด */
        width: 120%;
        /* ใช้ 90% ของหน้าจอเพื่อให้ขนาดพอดี */
        margin: 0 auto;
        /* จัดให้อยู่ตรงกลาง */
    }

    @media (max-width: 768px) .container-custom {
        width: 95%;
        /* ขยายให้เต็มที่ขึ้นเมื่อเป็นหน้าจอเล็ก */
    }

    table {
        font-size: 12px;

        /* ลดขนาดตัวอักษรของตารางในหน้าจอเล็ก */
        border: 3px solid #000;
        /* กำหนดเส้นขอบของตารางให้หนาขึ้น */
    }

    thead th {
        border-bottom: 3px solid #000;
        /* ทำให้เส้นขอบของหัวตารางหนากว่า */


    }
</style>

<?php
include('../component/header.php');
include '../server/connectdb.php';

// รับค่าจากฟอร์ม (หากมีการส่งค่าจากฟอร์ม)
$fiscal_year = isset($_POST['fiscal_year']) ? $_POST['fiscal_year'] : '';
$scenario = isset($_POST['scenario']) ? $_POST['scenario'] : '';
$fund = isset($_POST['fund']) ? $_POST['fund'] : '';
$faculty_alias = isset($_POST['faculty_alias']) ? $_POST['faculty_alias'] : '';
$plan_name = isset($_POST['plan_name']) ? $_POST['plan_name'] : '';
$sub_plan_name = isset($_POST['sub_plan_name']) ? $_POST['sub_plan_name'] : '';
$project_name = isset($_POST['project_name']) ? $_POST['project_name'] : '';

$db = new Database();
$conn = $db->connect();

// ฟังก์ชันดึงข้อมูล
function fetchData($conn)
{
    // รับค่าจากฟอร์ม (หากมีการส่งค่าจากฟอร์ม)
    $fiscal_year = isset($_POST['fiscal_year']) ? $_POST['fiscal_year'] : '';
    $scenario = isset($_POST['scenario']) ? $_POST['scenario'] : '';
    $fund = isset($_POST['fund']) ? $_POST['fund'] : '';
    $faculty_alias = isset($_POST['faculty_alias']) ? $_POST['faculty_alias'] : '';
    $plan_name = isset($_POST['plan_name']) ? $_POST['plan_name'] : '';
    $sub_plan_name = isset($_POST['sub_plan_name']) ? $_POST['sub_plan_name'] : '';
    $project_name = isset($_POST['project_name']) ? $_POST['project_name'] : '';

    $query = "SELECT 
       bpa.FISCAL_YEAR,
       fy.Alias_Default,
       bpdbpar.Scenario,
       bpanbp.Fund,
       f.Alias_Default AS Faculty_Alias,
       bpanbp.Plan,
       p.plan_name,
       bpanbp.Sub_Plan,
       sp.sub_plan_name,
       bpanbp.Project,
       pr.project_name,
       bpanbp.`Account`,
       ac.`type`,
       ac.sub_type,
       bpanbp.Allocated_Total_Amount_Quantity,
       bpa.BUDGET_ADJUSTMENTS,
       (bpanbp.Allocated_Total_Amount_Quantity + bpa.BUDGET_ADJUSTMENTS) AS Remaining_Unapproved_Budget,
       (bpa.COMMITMENTS + bpa.OBLIGATIONS) AS Budget_Commitments,
       ((bpa.COMMITMENTS + bpa.OBLIGATIONS) / NULLIF( bpanbp.Allocated_Total_Amount_Quantity, 0)) * 100 AS Allocated_Percentage,
       (bpanbp.Allocated_Total_Amount_Quantity - (bpa.COMMITMENTS + bpa.OBLIGATIONS) )  AS Remaining_after_budget_commitment,
       ((bpanbp.Allocated_Total_Amount_Quantity - (bpa.COMMITMENTS + bpa.OBLIGATIONS) ) / NULLIF( bpanbp.Allocated_Total_Amount_Quantity, 0)) * 100 AS Allocated_Remaining_after_budget_commitment_Percentage,
       bpa.EXPENDITURES,
        ( bpa.EXPENDITURES  / NULLIF( bpanbp.Allocated_Total_Amount_Quantity, 0)) * 100 AS EXPENDITURES_Percentage,
        (bpanbp.Allocated_Total_Amount_Quantity - bpa.EXPENDITURES )  AS EXPENDITURES_Total,
            ( (bpanbp.Allocated_Total_Amount_Quantity - bpa.EXPENDITURES ) / NULLIF( bpanbp.Allocated_Total_Amount_Quantity, 0)) * 100 AS EXPENDITURES_Total_Percentage,
            bpa.EXPENDITURES
    FROM budget_planning_allocated_annual_budget_plan bpanbp
    LEFT JOIN budget_planning_actual bpa 
        ON bpanbp.Fund = CONCAT('FN', bpa.FUND)
        AND bpanbp.Faculty = bpa.FACULTY
        AND bpanbp.`Account` = bpa.`Account`
        AND bpanbp.Plan = bpa.PLAN
        AND bpanbp.Sub_Plan = CONCAT('SP_', bpa.SUBPLAN)
        AND bpanbp.Project = bpa.PROJECT
        AND bpanbp.`Account` = bpa.`ACCOUNT`
    LEFT JOIN budget_planning_annual_budget_plan bpabp 
        ON bpanbp.Faculty = bpabp.Faculty
        AND bpanbp.Plan = bpabp.Plan
        AND bpanbp.`Account` = bpa.`Account`
        AND bpanbp.Sub_Plan = bpabp.Sub_Plan
        AND bpanbp.Project = bpabp.Project
        AND bpanbp.Fund = bpabp.Fund
        AND bpanbp.`Account` = bpabp.`Account`
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
        ON bpanbp.Project = pr.project_id
    LEFT JOIN fiscal_year AS fy 
        ON bpa.FISCAL_YEAR = fy.Fiscal_Year
    LEFT JOIN `account` AS ac 
        ON bpanbp.`Account` = ac.`account`
    WHERE 1=1";

    // การเพิ่มเงื่อนไขกรองข้อมูล
    if (!empty($fiscal_year)) {
        $query .= " AND bpa.FISCAL_YEAR = :fiscal_year";
    }
    if (!empty($scenario)) {
        $query .= " AND bpdbpar.Scenario = :scenario";
    }
    if (!empty($fund)) {
        $query .= " AND bpanbp.Fund = :fund";
    }
    if (!empty($faculty_alias)) {
        $query .= " AND f.Alias_Default = :faculty_alias";
    }
    if (!empty($plan_name)) {
        $query .= " AND p.plan_name = :plan_name";
    }
    if (!empty($sub_plan_name)) {
        $query .= " AND sp.sub_plan_name = :sub_plan_name";
    }
    if (!empty($project_name)) {
        $query .= " AND pr.project_name = :project_name";
    }

    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($query);

    // Bind ค่าตัวแปร
    if (!empty($fiscal_year)) {
        $stmt->bindParam(':fiscal_year', $fiscal_year);
    }
    if (!empty($scenario)) {
        $stmt->bindParam(':scenario', $scenario);
    }
    if (!empty($fund)) {
        $stmt->bindParam(':fund', $fund);
    }
    if (!empty($faculty_alias)) {
        $stmt->bindParam(':faculty_alias', $faculty_alias);
    }
    if (!empty($plan_name)) {
        $stmt->bindParam(':plan_name', $plan_name);
    }
    if (!empty($sub_plan_name)) {
        $stmt->bindParam(':sub_plan_name', $sub_plan_name);
    }
    if (!empty($project_name)) {
        $stmt->bindParam(':project_name', $project_name);
    }

    // Execute the query
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ดึงข้อมูลทั้งหมดจากฐานข้อมูล
$data = fetchData($conn);

// ดึงรายการสำหรับ dropdown โดยให้ค่าไม่ซ้ำกัน
$fiscal_years = array_unique(array_column($data, 'FISCAL_YEAR'));
$scenarios = array_unique(array_column($data, 'Scenario'));
$funds = array_unique(array_column($data, 'Fund'));
$faculty_aliases = array_unique(array_column($data, 'Faculty_Alias'));
$plan_names = array_unique(array_column($data, 'plan_name'));
$sub_plan_names = array_unique(array_column($data, 'sub_plan_name'));
$project_names = array_unique(array_column($data, 'project_name'));

// กำหนดค่าเริ่มต้นสำหรับตัวแปรที่จะแสดงในส่วนหัวตารางผลลัพธ์
$fiscal_year = $scenario = $fund = $faculty_alias = $plan_name = $sub_plan_name = $project_name = "";

// ตรวจสอบเมื่อมีการกดปุ่มค้นหา
if (isset($_POST['search'])) {
    $fiscal_year = $_POST['fiscal_year'];
    $scenario = $_POST['scenario'];
    $fund = $_POST['fund'];
    $faculty_alias = $_POST['faculty_alias'];
    $plan_name = $_POST['plan_name'];
    $sub_plan_name = $_POST['sub_plan_name'];
    $project_name = $_POST['project_name'];

    // ตรวจสอบว่ามีการเลือกครบทุกตัวเลือก (ยกเว้น 'scenario')
    if (empty($fiscal_year) || empty($fund) || empty($faculty_alias) || empty($plan_name) || empty($sub_plan_name) || empty($project_name)) {
        $error_message = "กรุณาเลือกทุกตัวเลือก ยกเว้นประเภทงบประมาณ.";
    } else {
        // กรองข้อมูลให้ตรงกับเงื่อนไขที่เลือก
        $data = array_filter($data, function ($row) use ($fiscal_year, $scenario, $fund, $faculty_alias, $plan_name, $sub_plan_name, $project_name) {
            return (
                ($fiscal_year == '' || $row['FISCAL_YEAR'] == $fiscal_year) &&
                ($scenario == '' || $row['Scenario'] == $scenario) &&
                ($fund == '' || $row['Fund'] == $fund) &&
                ($faculty_alias == '' || $row['Faculty_Alias'] == $faculty_alias) &&
                ($plan_name == '' || $row['plan_name'] == $plan_name) &&
                ($sub_plan_name == '' || $row['sub_plan_name'] == $sub_plan_name) &&
                ($project_name == '' || $row['project_name'] == $project_name)
            );
        });
    }
}
?>

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
        <?php include('../component/left-nev.php'); ?>
        <div class="content-body">
            <div class="container">
                <div class="row page-titles">
                    <div class="col p-0">
                        <h4>รายงานสรุปยอดงบประมาณคงเหลือ</h4>
                    </div>
                    <div class="col p-0">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">รายงาน</a></li>
                            <li class="breadcrumb-item active">รายงานสรุปยอดงบประมาณคงเหลือ</li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- ส่วนหัวของหน้ารายงาน -->
                                <div class="card-title">
                                    <h4>รายงานสรุปยอดงบประมาณคงเหลือ</h4>
                                </div>
                                <?php
                                // ดึงข้อมูลทั้งหมดจากฐานข้อมูล
                                $data = fetchData($conn);

                                // ดึงรายการสำหรับ dropdown โดยให้ค่าไม่ซ้ำกัน
                                $fiscal_years = array_unique(array_column($data, 'FISCAL_YEAR'));
                                $scenarios = array_unique(array_column($data, 'Scenario'));
                                $funds = array_unique(array_column($data, 'Fund'));
                                $faculty_aliases = array_unique(array_column($data, 'Faculty_Alias'));
                                $plan_names = array_unique(array_column($data, 'plan_name'));
                                $sub_plan_names = array_unique(array_column($data, 'sub_plan_name'));
                                $project_names = array_unique(array_column($data, 'project_name'));

                                // กำหนดค่าเริ่มต้นสำหรับตัวแปรที่จะแสดงในส่วนหัวตารางผลลัพธ์
                                $fiscal_year = $scenario = $fund = $faculty_alias = $plan_name = $sub_plan_name = $project_name = "";

                                // ตรวจสอบเมื่อมีการกดปุ่มค้นหา
                                if (isset($_POST['search'])) {
                                    $fiscal_year = $_POST['fiscal_year'];
                                    $scenario = $_POST['scenario'];
                                    $fund = $_POST['fund'];
                                    $faculty_alias = $_POST['faculty_alias'];
                                    $plan_name = $_POST['plan_name'];
                                    $sub_plan_name = $_POST['sub_plan_name'];
                                    $project_name = $_POST['project_name'];

                                    // ตรวจสอบว่ามีการเลือกครบทุกตัวเลือก (ยกเว้น 'scenario')
                                    if (empty($fiscal_year) || empty($fund) || empty($faculty_alias) || empty($plan_name) || empty($sub_plan_name) || empty($project_name)) {
                                        $error_message = "กรุณาเลือกทุกตัวเลือก ยกเว้นประเภทงบประมาณ.";
                                    } else {
                                        // กรองข้อมูลให้ตรงกับเงื่อนไขที่เลือก
                                        $data = array_filter($data, function ($row) use ($fiscal_year, $scenario, $fund, $faculty_alias, $plan_name, $sub_plan_name, $project_name) {
                                            return (
                                                ($fiscal_year == '' || $row['FISCAL_YEAR'] == $fiscal_year) &&
                                                ($scenario == '' || $row['Scenario'] == $scenario) &&
                                                ($fund == '' || $row['Fund'] == $fund) &&
                                                ($faculty_alias == '' || $row['Faculty_Alias'] == $faculty_alias) &&
                                                ($plan_name == '' || $row['plan_name'] == $plan_name) &&
                                                ($sub_plan_name == '' || $row['sub_plan_name'] == $sub_plan_name) &&
                                                ($project_name == '' || $row['project_name'] == $project_name)
                                            );
                                        });
                                    }
                                }
                                ?>

                                <!-- ส่วนฟอร์มค้นหา -->
                                <div class="info-section">
                                    <form method="POST" action="">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="fiscal_year"
                                                    class="form-label">เลือกปีบริหารงบประมาณ:</label>
                                                <select name="fiscal_year" id="fiscal_year"
                                                    class="form-select form-select-sm">
                                                    <option value="">-- เลือกปีบริหารงบประมาณ --</option>
                                                    <?php foreach ($fiscal_years as $year): ?>
                                                        <option value="<?php echo $year; ?>" <?php echo (isset($_POST['fiscal_year']) && $_POST['fiscal_year'] == $year) ? 'selected' : ''; ?>>
                                                            <?php echo $year; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="scenario" class="form-label">เลือกประเภทงบประมาณ:</label>
                                                <select name="scenario" id="scenario"
                                                    class="form-select form-select-sm">
                                                    <option value="">-- เลือกประเภทงบประมาณ --</option>
                                                    <?php foreach ($scenarios as $item): ?>
                                                        <option value="<?php echo $item; ?>" <?php echo (isset($_POST['scenario']) && $_POST['scenario'] == $item) ? 'selected' : ''; ?>>
                                                            <?php echo $item; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="fund" class="form-label">เลือกแหล่งเงิน:</label>
                                                <select name="fund" id="fund" class="form-select form-select-sm">
                                                    <option value="">-- เลือกแหล่งเงิน --</option>
                                                    <?php foreach ($funds as $item): ?>
                                                        <option value="<?php echo $item; ?>" <?php echo (isset($_POST['fund']) && $_POST['fund'] == $item) ? 'selected' : ''; ?>>
                                                            <?php echo $item; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="faculty_alias"
                                                    class="form-label">เลือกส่วนงาน/หน่วยงาน:</label>
                                                <select name="faculty_alias" id="faculty_alias"
                                                    class="form-select form-select-sm">
                                                    <option value="">-- เลือกส่วนงาน/หน่วยงาน --</option>
                                                    <?php foreach ($faculty_aliases as $item): ?>
                                                        <option value="<?php echo $item; ?>" <?php echo (isset($_POST['faculty_alias']) && $_POST['faculty_alias'] == $item) ? 'selected' : ''; ?>>
                                                            <?php echo $item; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="plan_name" class="form-label">เลือกแผนงาน:</label>
                                                <select name="plan_name" id="plan_name"
                                                    class="form-select form-select-sm">
                                                    <option value="">-- เลือกแผนงาน --</option>
                                                    <?php foreach ($plan_names as $item): ?>
                                                        <option value="<?php echo $item; ?>" <?php echo (isset($_POST['plan_name']) && $_POST['plan_name'] == $item) ? 'selected' : ''; ?>>
                                                            <?php echo $item; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="sub_plan_name" class="form-label">เลือกแผนงานย่อย:</label>
                                                <select name="sub_plan_name" id="sub_plan_name"
                                                    class="form-select form-select-sm">
                                                    <option value="">-- เลือกแผนงานย่อย --</option>
                                                    <?php foreach ($sub_plan_names as $item): ?>
                                                        <option value="<?php echo $item; ?>" <?php echo (isset($_POST['sub_plan_name']) && $_POST['sub_plan_name'] == $item) ? 'selected' : ''; ?>>
                                                            <?php echo $item; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="project_name" class="form-label">เลือกโครงการ:</label>
                                                <select name="project_name" id="project_name"
                                                    class="form-select form-select-sm">
                                                    <option value="">-- เลือกโครงการ --</option>
                                                    <?php foreach ($project_names as $item): ?>
                                                        <option value="<?php echo $item; ?>" <?php echo (isset($_POST['project_name']) && $_POST['project_name'] == $item) ? 'selected' : ''; ?>>
                                                            <?php echo $item; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- ปุ่มค้นหาและรีเซ็ต -->
                                            <div class="col-12 text-center mt-3">
                                                <button type="submit" name="search"
                                                    class="btn btn-primary">ค้นหา</button>
                                                <button type="reset" class="btn btn-secondary"
                                                    onclick="window.location.href=window.location.href;">รีเซ็ต</button>
                                            </div>
                                        </div>
                                    </form>

                                    <?php
                                    // แสดงข้อความผิดพลาด (ถ้ามี)
                                    if (isset($error_message)) {
                                        echo '<p class="text-danger text-center mt-3">' . $error_message . '</p>';
                                    }
                                    ?>
                                </div>

                                <!-- แสดงตารางเฉพาะเมื่อมีการกดค้นหาและไม่มีข้อผิดพลาด -->
                                <?php if (isset($_POST['search']) && !isset($error_message)): ?>
                                    <div class="table-responsive">
                                        <table id="reportTable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th colspan="22" style="text-align: center;">
                                                        รายงานสรุปยอดงบประมาณคงเหลือ</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="22" style="text-align: left;">
                                                        แสดงข้อมูล:
                                                        <br> ปีบริหารงบประมาณ: <?php echo $fiscal_year; ?>
                                                        <br> ประเภทงบประมาณ: <?php echo $scenario; ?>
                                                        <br> แหล่งเงิน: <?php echo $fund; ?>
                                                        <br> ส่วนงาน/หน่วยงาน: <?php echo $faculty_alias; ?>
                                                        <br> แผนงาน (ผลผลิต): <?php echo $plan_name; ?>
                                                        <br> แผนงานย่อย (ผลผลิตย่อย/กิจกรรม): <?php echo $sub_plan_name; ?>
                                                        <br> โครงการ (Project): <?php echo $project_name; ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">คำใช้จ่าย</th>
                                                    <th colspan="4">ยอดรวมงบประมาณ</th>
                                                    <th colspan="8">เงินประจำงวด</th>
                                                    <th colspan="2">ผูกพัน</th>
                                                    <th colspan="2">ผูกพันงบประมาณตามข้อตกลง/สัญญา</th>
                                                    <th rowspan="2">จำนวนงบประมาณเบิกจ่าย</th>
                                                    <th rowspan="2">เบิกงบประมาณเกินส่งคืน</th>
                                                </tr>
                                                <tr>
                                                    <th>จำนวนงบประมาณ</th>
                                                    <th>จำนวนงบประมาณโอนเข้า</th>
                                                    <th>จำนวนงบประมาณโอนออก</th>
                                                    <th>คงเหลือไม่อนุมัติงวดเงิน</th>
                                                    <th>ผูกพันงบประมาณ</th>
                                                    <th>ร้อยละ</th>
                                                    <th>คงเหลือหลังผูกพันงบประมาณ</th>
                                                    <th>ร้อยละ</th>
                                                    <th>เบิกจ่ายงบประมาณ</th>
                                                    <th>ร้อยละ</th>
                                                    <th>คงเหลือหลังเบิกจ่ายงบประมาณ</th>
                                                    <th>ร้อยละ</th>
                                                    <th>จำนวนงบประมาณ</th>
                                                    <th>คงเหลือหลังเบิกจ่ายงบประมาณ</th>
                                                    <th>จำนวนงบประมาณ</th>
                                                    <th>คงเหลือหลังเบิกจ่ายงบประมาณ</th>
                                                </tr>
                                            </thead>
                                            <?php
                                            // ตรวจสอบว่ามีข้อมูลหรือไม่
                                            if (!empty($data)) {
                                                $groupedData = [];

                                                // วนลูปข้อมูลเพื่อนำมาจัดกลุ่ม
                                                foreach ($data as $row) {
                                                    $key = $row['sub_type']; // ใช้ sub_type เป็น key สำหรับการ grouping
                                        
                                                    if (!isset($groupedData[$key])) {
                                                        // ถ้ายังไม่มี ให้สร้างแถวใหม่
                                                        $groupedData[$key] = $row;
                                                    } else {
                                                        // ถ้ามีแล้ว ให้รวมค่าของฟิลด์ตัวเลข
                                                        $groupedData[$key]['Allocated_Total_Amount_Quantity'] += $row['Allocated_Total_Amount_Quantity'];
                                                        $groupedData[$key]['BUDGET_ADJUSTMENTS'] += $row['BUDGET_ADJUSTMENTS'];
                                                        $groupedData[$key]['BUDGET_ADJUSTMENTS'] += $row['BUDGET_ADJUSTMENTS'];
                                                        $groupedData[$key]['Remaining_Unapproved_Budget'] += $row['Remaining_Unapproved_Budget'];
                                                        $groupedData[$key]['Budget_Commitments'] += $row['Budget_Commitments'];
                                                        $groupedData[$key]['Allocated_Percentage'] += $row['Allocated_Percentage'];
                                                        $groupedData[$key]['Remaining_after_budget_commitment'] += $row['Remaining_after_budget_commitment'];
                                                        $groupedData[$key]['EXPENDITURES'] += $row['EXPENDITURES'];
                                                        $groupedData[$key]['EXPENDITURES_Total'] += $row['EXPENDITURES_Total'];

                                                        // สำหรับเปอร์เซ็นต์ ควรคำนวณค่าเฉลี่ยหรือลองเลือกวิธีที่เหมาะสม
                                                        $groupedData[$key]['Allocated_Percentage'] = round(($groupedData[$key]['Allocated_Percentage'] + $row['Allocated_Percentage']) / 2, 2);
                                                        $groupedData[$key]['Allocated_Remaining_after_budget_commitment_Percentage'] = round(($groupedData[$key]['Allocated_Remaining_after_budget_commitment_Percentage'] + $row['Allocated_Remaining_after_budget_commitment_Percentage']) / 2, 2);
                                                        $groupedData[$key]['EXPENDITURES_Percentage'] = round(($groupedData[$key]['EXPENDITURES_Percentage'] + $row['EXPENDITURES_Percentage']) / 2, 2);
                                                    }
                                                }

                                                // เปลี่ยนเป็น array ที่สามารถวนลูปได้
                                                $groupedData = array_values($groupedData);
                                            } else {
                                                $groupedData = [];
                                            }
                                            ?>

                                            <tbody>
                                                <?php
                                                // เรียงลำดับ type และ sub_type
                                                usort($groupedData, function ($a, $b) {
                                                    $typeCompare = strcmp($a['type'], $b['type']); // เปรียบเทียบ type
                                                    if ($typeCompare === 0) {
                                                        return strcmp($a['sub_type'], $b['sub_type']); // ถ้า type เท่ากัน ให้เรียงตาม sub_type
                                                    }
                                                    return $typeCompare;
                                                });

                                                $prevType = null; // เก็บค่า type ก่อนหน้า
                                                ?>

                                                <?php if (!empty($groupedData)): ?>
                                                    <?php foreach ($groupedData as $row): ?>
                                                        <tr>
                                                            <td>
                                                                <?php if ($prevType !== $row['type']): ?>
                                                                    <?php echo $row['type']; ?> <br>
                                                                <?php endif; ?>
                                                                <?php echo $row['sub_type']; ?>
                                                            </td>
                                                            <td><?php echo $row['Allocated_Total_Amount_Quantity']; ?></td>
                                                            <td><?php echo $row['BUDGET_ADJUSTMENTS']; ?></td>
                                                            <td><?php echo $row['BUDGET_ADJUSTMENTS']; ?></td>
                                                            <td><?php echo $row['Remaining_Unapproved_Budget']; ?></td>
                                                            <td><?php echo $row['Budget_Commitments']; ?></td>
                                                            <td><?php echo $row['Allocated_Percentage']; ?>%</td>
                                                            <td><?php echo $row['Remaining_after_budget_commitment']; ?></td>
                                                            <td><?php echo $row['Allocated_Remaining_after_budget_commitment_Percentage']; ?>%
                                                            </td>
                                                            <td><?php echo $row['EXPENDITURES']; ?></td>
                                                            <td><?php echo $row['EXPENDITURES_Percentage']; ?>%</td>
                                                            <td><?php echo $row['EXPENDITURES_Total']; ?></td>
                                                            <td>0</td>
                                                            <td>0</td>
                                                            <td>0</td>
                                                            <td>0</td>
                                                            <td>0</td>
                                                            <td>0</td>
                                                            <td><?php echo $row['EXPENDITURES']; ?></td>
                                                            <td>0</td>
                                                        </tr>
                                                        <?php $prevType = $row['type']; // อัปเดตค่า type ที่แสดงล่าสุด ?>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="22" style="text-align: center; color: red;">
                                                            ไม่มีข้อมูลที่ตรงกับเงื่อนไขที่เลือก
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>


                                            </tbody>

                                        </table>
                                    </div>
                                <?php endif; ?>
                                <button onclick="exportCSV()" class="btn btn-primary m-t-15">Export CSV</button>
                        <button onclick="exportPDF()" class="btn btn-danger m-t-15">Export PDF</button>
                        <button onclick="exportXLS()" class="btn btn-success m-t-15">Export XLS</button>
                            </div>
                        

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