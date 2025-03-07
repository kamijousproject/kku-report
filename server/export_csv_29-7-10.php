<?php
include 'connectdb.php';

$database = new Database();
$conn = $database->connect();

// ฟังก์ชันจัดรูปแบบ account และชื่อบัญชี
function formatAccountData($conn, $account, $description)
{
    $accountParts = explode('-', $account);
    $formattedAccount = isset($accountParts[3], $accountParts[1]) ? "{$accountParts[3]}-{$accountParts[1]}" : $account;
    $accountNumber = $accountParts[3] ?? $account;

    // Query หาชื่อบัญชีจาก budget_account
    $descQuery = "SELECT description FROM budget_account WHERE account = :account_number LIMIT 1";
    $descStmt = $conn->prepare($descQuery);
    $descStmt->bindParam(':account_number', $accountNumber, PDO::PARAM_STR);
    $descStmt->execute();
    $accountDescription = $descStmt->fetch(PDO::FETCH_ASSOC)['description'] ?? $description;

    // แยก faculty description
    $facultyDes = explode("-", $description);
    if (count($facultyDes) >= 2) {
        $facultyDes = str_replace("\\", "", $facultyDes[1]);
    } else {
        $facultyDes = $description;
    }

    return [$formattedAccount, $accountDescription . '-' . $facultyDes];
}

// Query ดึงข้อมูล
$query = "SELECT account, account_description, prior_periods_debit, prior_periods_credit, period_activity_debit, 
          period_activity_credit, ending_balances_debit, ending_balances_credit FROM budget_planning_actual_2";

$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// กำหนด header สำหรับดาวน์โหลด CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="รายงานสรุปบัญชีทุนสำรองสะสม.csv"');

// สร้างไฟล์ CSV
$output = fopen('php://output', 'w');

// **เพิ่ม BOM เพื่อให้ Excel รองรับ UTF-8**
fputs($output, "\xEF\xBB\xBF");

// เขียนหัวตาราง
fputcsv($output, [
    'รหัสบัญชี',
    'ชื่อบัญชี',
    'รหัส GF',
    'ชื่อบัญชี GF',
    'ยอดยกมา (เดบิต)',
    'ยอดยกมา (เครดิต)',
    'ประจำงวด (เดบิต)',
    'ประจำงวด (เครดิต)',
    'ยอดยกไป (เดบิต)',
    'ยอดยกไป (เครดิต)'
]);

// เขียนข้อมูล
foreach ($data as $row) {
    list($formattedAccount, $formattedDescription) = formatAccountData($conn, $row['account'], $row['account_description']);

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
