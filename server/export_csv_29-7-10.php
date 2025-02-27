<?php
include 'connectdb.php';
include 'functions.php'; // นำเข้าฟังก์ชันที่เราสร้าง

$database = new Database();
$conn = $database->connect();
function formatAccountData($account, $description)
{
    // 1. จัดรูปแบบ account (เลขที่ 3 - เลขที่ 1)
    $accountParts = explode('-', $account);
    $formattedAccount = isset($accountParts[3], $accountParts[1]) ? "{$accountParts[3]}-{$accountParts[1]}" : $account;

    // 2. ลบเครื่องหมาย \ และช่องว่างพิเศษออกจาก description
    $cleanedDescription = preg_replace('/\\\\/', '', $description);

    // 3. ใช้ regex ดึงเฉพาะส่วนที่ต้องการจาก account_description
    if (preg_match('/(บัญชี[^-]+)-([^\\-]+)-([^\\-]+)/u', $cleanedDescription, $matches)) {
        $formattedDescription = trim("{$matches[1]}-{$matches[2]}-{$matches[3]}");
    } else {
        $formattedDescription = trim($cleanedDescription); // ถ้าไม่ตรงเงื่อนไขให้แสดงค่าเดิม
    }

    return [$formattedAccount, $formattedDescription];
}
// Query ดึงข้อมูลทั้งหมด
$query = "SELECT 
            account, 
            account_description, 
            prior_periods_debit, 
            prior_periods_credit, 
            period_activity_debit, 
            period_activity_credit, 
            ending_balances_debit, 
            ending_balances_credit 
          FROM budget_planning_actual_2";

$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// กำหนด header สำหรับดาวน์โหลด CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="รายงานสรุปบัญชีทุนสำรองสะสม.csv"');

// สร้างไฟล์ CSV
$output = fopen('php://output', 'w');

// เขียนหัวตาราง
fputcsv($output, ['รหัสบัญชี', 'ชื่อบัญชี', 'รหัส GF', 'ชื่อบัญชี GF', 'ยอดยกมา (เดบิต)', 'ยอดยกมา (เครดิต)', 'ประจำงวด (เดบิต)', 'ประจำงวด (เครดิต)', 'ยอดยกไป (เดบิต)', 'ยอดยกไป (เครดิต)']);

// เขียนข้อมูล
foreach ($data as $row) {
    list($formattedAccount, $formattedDescription) = formatAccountData($row['account'], $row['account_description']);

    fputcsv($output, [
        $formattedAccount,
        $formattedDescription,
        '-',
        '-',
        $row['prior_periods_debit'],
        $row['prior_periods_credit'],
        $row['period_activity_debit'],
        $row['period_activity_credit'],
        $row['ending_balances_debit'],
        $row['ending_balances_credit']
    ]);
}

fclose($output);
exit;
