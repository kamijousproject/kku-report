<?php
include 'connectdb.php';

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

// กำหนด header สำหรับดาวน์โหลดไฟล์ Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=รายงานสรุปบัญชีทุนสำรองสะสม.xls");

// สร้างไฟล์ Excel
echo "<table border='1'>";
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
  list($formattedAccount, $formattedDescription) = formatAccountData($row['account'], $row['account_description']);

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
