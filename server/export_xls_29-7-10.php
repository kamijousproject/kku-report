<?php
include 'connectdb.php';

$database = new Database();
$conn = $database->connect();

// รับค่าจาก query string (GET)
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
$selectedFaculty = isset($_GET['faculty']) ? $_GET['faculty'] : '';

// ฟังก์ชันจัดรูปแบบ account และชื่อบัญชี
function formatAccountData($conn, $account, $description)
{
  $accountParts = explode('-', $account);
  $formattedAccount = isset($accountParts[3], $accountParts[1]) ? "{$accountParts[3]}-{$accountParts[1]}" : $account;
  $accountNumber = $accountParts[3] ?? $account;

  $descQuery = "SELECT description FROM budget_account WHERE account = :account_number LIMIT 1";
  $descStmt = $conn->prepare($descQuery);
  $descStmt->bindParam(':account_number', $accountNumber, PDO::PARAM_STR);
  $descStmt->execute();
  $accountDescription = $descStmt->fetch(PDO::FETCH_ASSOC)['description'] ?? $description;

  $facultyDes = explode("-", $description);
  if (count($facultyDes) >= 2) {
    $facultyDes = str_replace("\\", "", $facultyDes[1]);
  } else {
    $facultyDes = $description;
  }

  return [$formattedAccount, $accountDescription . '-' . $facultyDes];
}

// --------------------
// สร้าง SQL แบบมีเงื่อนไข
// --------------------
$query = "SELECT account, account_description, net_ending_balances_debit, net_ending_balances_credit
          FROM budget_planning_actual_2
          WHERE account LIKE '%-02-%'";

// เงื่อนไขปี
if (!empty($selectedYear)) {
  $query .= " AND RIGHT(account, 4) = :year";
}

// เงื่อนไข faculty
if (!empty($selectedFaculty)) {
  $query .= " AND SUBSTRING_INDEX(SUBSTRING_INDEX(account, '-', 2), '-', -1) = :faculty";
}

$stmt = $conn->prepare($query);

// bindParam ตามเงื่อนไข
if (!empty($selectedYear)) {
  $stmt->bindParam(':year', $selectedYear, PDO::PARAM_STR);
}
if (!empty($selectedFaculty)) {
  $stmt->bindParam(':faculty', $selectedFaculty, PDO::PARAM_STR);
}

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Header สำหรับ export
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=รายงานสรุปบัญชีทุนสำรองสะสม.xls");
echo "\xEF\xBB\xBF";

// แสดงหัวตาราง
echo "<table border='1'>";
echo "<tr><th colspan='3' style='text-align:center;'>รายงานสรุปบัญชีทุนสำรองสะสม</th></tr>";
echo "<tr><td colspan='3'></td></tr>";
echo "<tr><th>รหัสบัญชี</th><th>ชื่อบัญชี</th><th>ทุนสำรองสะสม</th></tr>";

// แสดงข้อมูลในตาราง
foreach ($data as $row) {
  list($formattedAccount, $formattedDescription) = formatAccountData($conn, $row['account'], $row['account_description']);

  $netBalance = ($row['net_ending_balances_debit'] == 0)
    ? '(' . $row['net_ending_balances_credit'] . ')'
    : $row['net_ending_balances_debit'];

  echo "<tr>
          <td>{$formattedAccount}</td>
          <td>{$formattedDescription}</td>
          <td>{$netBalance}</td>
        </tr>";
}

echo "</table>";
exit;
