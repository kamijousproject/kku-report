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
$query = "SELECT account, account_description, net_ending_balances_debit, net_ending_balances_credit 
          FROM budget_planning_actual_2";

$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// กำหนด header สำหรับดาวน์โหลด CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="รายงานสรุปบัญชีทุนสำรองสะสม.csv"');

// สร้างไฟล์ CSV
$output = fopen('php://output', 'w');

// เพิ่ม BOM
fputs($output, "\xEF\xBB\xBF");

// หัวรายงาน
fputcsv($output, ['รายงานสรุปบัญชีทุนสำรองสะสม']);
fputcsv($output, []);

// หัวตารางตามหน้า HTML
fputcsv($output, [
    'รหัสบัญชี',
    'ชื่อบัญชี',
    'ทุนสำรองสะสม'
]);

// เพิ่มข้อมูล
foreach ($data as $row) {
    list($formattedAccount, $formattedDescription) = formatAccountData($conn, $row['account'], $row['account_description']);

    // เลือกแสดงยอดตาม logic
    if ($row['net_ending_balances_debit'] == 0) {
        $netBalance = '(' . $row['net_ending_balances_credit'] . ')';
    } else {
        $netBalance = $row['net_ending_balances_debit'];
    }

    fputcsv($output, [
        $formattedAccount,
        $formattedDescription,
        $netBalance
    ]);
}

fclose($output);
exit;
