<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password, name, faculty_id, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            // อัปเดต last_login
            $update_stmt = $conn->prepare("UPDATE user SET last_login = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $user["id"]);
            $update_stmt->execute();

            // ตั้งค่า Session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["faculty_id"] = $user["faculty_id"];
            $_SESSION["role"] = $user["role"];

            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "รหัสผ่านไม่ถูกต้อง!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ไม่พบชื่อผู้ใช้!"]);
    }
}
