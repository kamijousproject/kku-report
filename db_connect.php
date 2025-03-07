<?php
$servername = "110.164.146.250"; // หรือ IP ของ MySQL Server
$username = "root"; // เปลี่ยนตาม MySQL ของคุณ
$password = "TDyutdYdyudRTYDsEFOPI"; // เปลี่ยนตาม MySQL ของคุณ
$database = "epm_report"; // ชื่อฐานข้อมูลของคุณ

$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
