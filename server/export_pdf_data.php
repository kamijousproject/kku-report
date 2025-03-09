<?php
include 'connectdb.php';

$database = new Database();
$conn = $database->connect();

// ✅ **ฟังก์ชันจัดรูปแบบ account และชื่อบัญชี**
function formatAccountData($conn, $account, $description)
{
    $accountParts = explode('-', $account);
    $formattedAccount = isset($accountParts[3], $accountParts[1]) ? "{$accountParts[3]}-{$accountParts[1]}" : $account;
    $accountNumber = $accountParts[3] ?? $account;

    // ✅ Query หาชื่อบัญชีจาก budget_account
    $descQuery = "SELECT description FROM budget_account WHERE account = :account_number LIMIT 1";
    $descStmt = $conn->prepare($descQuery);
    $descStmt->bindParam(':account_number', $accountNumber, PDO::PARAM_STR);
    $descStmt->execute();
    $accountDescription = $descStmt->fetch(PDO::FETCH_ASSOC)['description'] ?? $description;

    // ✅ แยก faculty description
    $facultyDesParts = explode("-", $description);
    $facultyDes = count($facultyDesParts) >= 2 ? str_replace("\\", "", $facultyDesParts[1]) : $description;

    return [
        'formatted_account' => $formattedAccount,
        'formatted_description' => $accountDescription . '-' . $facultyDes
    ];
}

// ✅ Query ดึงข้อมูล
$query = "SELECT account, account_description, prior_periods_debit, prior_periods_credit, 
          period_activity_debit, period_activity_credit, ending_balances_debit, ending_balances_credit 
          FROM budget_planning_actual_2";
$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ **จัดรูปแบบข้อมูลที่ได้**
$formattedData = [];
foreach ($data as $row) {
    $formatted = formatAccountData($conn, $row['account'], $row['account_description']);

    $formattedData[] = [
        'account' => $formatted['formatted_account'],
        'account_description' => $formatted['formatted_description'],
        'prior_periods_debit' => $row['prior_periods_debit'],
        'prior_periods_credit' => $row['prior_periods_credit'],
        'period_activity_debit' => $row['period_activity_debit'],
        'period_activity_credit' => $row['period_activity_credit'],
        'ending_balances_debit' => $row['ending_balances_debit'],
        'ending_balances_credit' => $row['ending_balances_credit']
    ];
}

// ✅ **ส่งออก JSON**
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($formattedData);
exit;
