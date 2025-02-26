<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = __DIR__ . "/uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;

    // ตรวจสอบประเภทไฟล์ CSV
    $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
    if ($file_type != "csv") {
        header("Location: ../template-vertical-nav/index.php?status=error&message=Only CSV files are allowed.");
        exit();
    }

    // ตรวจสอบการเลือกประเภทไฟล์
    if (!isset($_POST['file_type']) || empty($_POST['file_type'])) {
        header("Location: ../template-vertical-nav/index.php?status=error&message=File type is required.");
        exit();
    }

    $file_category = $_POST['file_type'];

    // แมปประเภทไฟล์กับ Python script
    $python_scripts = [
        "budget_planning_actual" => __DIR__ . "/budget_planing/insert_budget_planning_actual.py",
        "budget_planning_allocated_annual_budget_plan" => __DIR__ . "/budget_planing/insert_budget_planning_allocated_annual_budget_plan.py",
        "budget_planning_annual_budget_plan" => __DIR__ . "/budget_planing/insert_budget_planning_annual_budget_plan.py",
        "budget_planning_disbursement_budget_plan_anl_release" => __DIR__ . "/budget_planing/insert_budget_planning_disbursement_budget_plan_anl_release.py",
        "budget_planning_project_kpi_progress" => __DIR__ . "/budget_planing/insert_budget_planning_project_kpi_progress.py",
        "budget_planning_project_kpi" => __DIR__ . "/budget_planing/insert_budget_planning_project_kpi.py",
        "budget_planning_sub_plan_kpi_progress" => __DIR__ . "/budget_planing/insert_budget_planning_sub_plan_kpi_progress.py",
        "budget_planning_subplan_kpi" => __DIR__ . "/budget_planing/insert_budget_planning_subplan_kpi.py",
        "GLTrialBalance" => __DIR__ . "/budget_planing/insert_budget_planning_actual_2.py",

        "workforce_4year_plan" => __DIR__ . "/workforce/insert_workforce_4year_plan.py",
        "workforce_current_position_request" => __DIR__ . "/workforce/insert_workforce_current_position_request.py",
        "workforce_current_positions_allocation" => __DIR__ . "/workforce/insert_workforce_current_positions_allocation.py",
        "workforce_new_positions_allocation" => __DIR__ . "/workforce/insert_workforce_new_positions_allocation.py",
        "workforce_new_position_request" => __DIR__ . "/workforce/workforce_new_position_request.py",
        "workforce_hcm_actual" => __DIR__ . "/workforce/insert_hcm_actual.py",

        "planning_faculty_action_plan" => __DIR__ . "/planning/insert_planning_faculty_action_plan.py",
        "planning_faculty_okr_progress" => __DIR__ . "/planning/insert_planning_faculty_okr_progress.py",
        "planning_faculty_project_progress" => __DIR__ . "/planning/insert_planning_faculty_project_progress.py",
        "planning_faculty_revised_action_plan" => __DIR__ . "/planning/insert_planning_faculty_revised_action_plan.py",
        "planning_faculty_strategic_plan" => __DIR__ . "/planning/insert_planning_faculty_strategic_plan.py",
        "planning_kku_action_plan" => __DIR__ . "/planning/insert_planning_kku_action_plan.py",
        "planning_kku_okr_progress" => __DIR__ . "/planning/insert_planning_kku_okr_progress.py",
        "planning_kku_project_progress" => __DIR__ . "/planning/insert_planning_kku_project_progress.py",
        "planning_kku_revised_action_plan" => __DIR__ . "/planning/insert_planning_kku_revised_action_plan.py",
        "planning_kku_strategic_plan" => __DIR__ . "/planning/insert_planning_kku_strategic_plan.py",
    ];

    // ตรวจสอบว่า script มีอยู่จริง
    if (!array_key_exists($file_category, $python_scripts) || !file_exists($python_scripts[$file_category])) {
        header("Location: ../template-vertical-nav/index.php?status=error&message=Invalid or missing Python script.");
        exit();
    }

    // ย้ายไฟล์ไปยังโฟลเดอร์ `uploads/`
    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        header("Location: ../template-vertical-nav/index.php?status=error&message=File upload failed.");
        exit();
    }

    // เรียกใช้ Python script และรับ error message จาก Python
    $python_script = escapeshellarg($python_scripts[$file_category]);
    $command = "python $python_script " . escapeshellarg($target_file) . " 2>&1";

    $output = shell_exec($command);
    $output = trim($output); // ตัดช่องว่างและ new line ออกจากข้อความ

    // Debugging (เอาออกใน production)
    // var_dump($output);
    // die();

    if ($output === "SUCCESS") {
        header("Location: ../template-vertical-nav/index.php?status=success");
        exit();
    } else {
        header("Location: ../template-vertical-nav/index.php?status=error&message=" . urlencode($output));
        exit();
    }
}
