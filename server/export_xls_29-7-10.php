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

// กำหนด header สำหรับดาวน์โหลดไฟล์ Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=รายงานสรุปบัญชีทุนสำรองสะสม.xls");

// **เพิ่ม BOM เพื่อให้ Excel รองรับ UTF-8**
echo "\xEF\xBB\xBF";

// **เพิ่มชื่อรายงาน**
echo "<table border='1'>";
echo "<tr><th colspan='10' style='text-align:center;'>รายงานสรุปบัญชีทุนสำรองสะสม</th></tr>";

// **เพิ่มบรรทัดว่างให้แยกหัวรายงานกับตาราง**
echo "<tr><td colspan='10'></td></tr>";

// **เพิ่มหัวตาราง**
echo "<tr>
        <th>รหัสบัญชี</th>
        <th>ชื่อบัญชี</th>
        <th>รหัส GF</th>
        <th>ชื่อบัญชี GF</th>
        <th>ยอดยกมา (เดบิต)</th>
        <th>ยอดยกมา (เครดิต)</th>
        <th>ประจำงวด (เดบิต)</th>
        <th>ประจำงวด (เครดิต)</th>
        <th>ยอดยกไป (เดบิต)</th>
        <th>ยอดยกไป (เครดิต)</th>
      </tr>";

foreach ($data as $row) {
  list($formattedAccount, $formattedDescription) = formatAccountData($conn, $row['account'], $row['account_description']);

  echo "<tr>
            <td>{$formattedAccount}</td>
            <td>{$formattedDescription}</td>
            <td>-</td>
            <td>-</td>
            <td>{$row['prior_periods_debit']}</td>
            <td>{$row['prior_periods_credit']}</td>
            <td>{$row['period_activity_debit']}</td>
            <td>{$row['period_activity_credit']}</td>
            <td>{$row['ending_balances_debit']}</td>
            <td>{$row['ending_balances_credit']}</td>
          </tr>";
}

echo "</table>";
exit;
