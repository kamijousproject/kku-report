<?php
// ปิด error เพื่อไม่ให้โชว์รายละเอียด server
ini_set('display_errors', 0);
error_reporting(0);

// รายการ .cmd ที่ต้องรัน
$cmdList = [
    realpath(__DIR__ . "/planning_faculty_action_plan/autodownload.cmd"),
    realpath(__DIR__ . "/planning_faculty_okr_progress/autodownload.cmd"),
    realpath(__DIR__ . "/planning_faculty_project_progress/autodownload.cmd"),
    realpath(__DIR__ . "/planning_faculty_revised_action_plan/autodownload.cmd"),
    realpath(__DIR__ . "/planning_faculty_strategic_plan/autodownload.cmd"),
    realpath(__DIR__ . "/planning_kku_action_plan/autodownload.cmd"),
    realpath(__DIR__ . "/planning_kku_okr_progress/autodownload.cmd"),
    realpath(__DIR__ . "/planning_kku_project_progress/autodownload.cmd"),
    realpath(__DIR__ . "/planning_kku_revised_action_plan/autodownload.cmd"),
    realpath(__DIR__ . "/planning_kku_strategic_plan/autodownload.cmd"),
];

$results = [];

foreach ($cmdList as $cmdFile) {
    if (file_exists($cmdFile)) {
        $output = [];
        $return_var = 0;

        // ใช้ start /B เพื่อให้ทำงานเบื้องหลัง
        exec("start /B \"\" \"$cmdFile\"", $output, $return_var);

        if ($return_var === 0) {
            $results[] = basename(dirname($cmdFile)) . ": รันสำเร็จ";
        } else {
            $results[] = basename(dirname($cmdFile)) . ": รันไม่สำเร็จ (code $return_var)";
        }
    } else {
        $results[] = basename(dirname($cmdFile)) . ": ไม่พบไฟล์";
    }
}

// รวมผลลัพธ์ทั้งหมดส่งกลับ
echo implode("<br>", $results);
