<?php
header('Content-Type: application/json');
require_once 'connectdb.php';

// ตรวจสอบว่าได้รับข้อมูล POST ถูกต้อง
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $command = $_POST["command"];
    switch ($command) {
        case "get_kku_planing":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                                kp.*, 
                                so.pilar_name AS so_name, 
                                si.pilar_name AS si_name,
                                ksp.ksp_name 
                            FROM 
                                kku_planing AS kp
                            LEFT JOIN 
                                pilar AS so ON kp.so = so.pilar_id
                            LEFT JOIN 
                                pilar AS si ON kp.si = si.pilar_id
                            LEFT JOIN 
                                ksp ON kp.strategic_project = ksp.ksp_id;";
                $stmtPlan = $conn->prepare($sqlPlan);
                $stmtPlan->execute();
                $plan = $stmtPlan->fetchAll(PDO::FETCH_ASSOC);
                $conn = null;

                $response = array(
                    'plan' => $plan
                );
                echo json_encode($response);
            } catch (PDOException $e) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                );
                echo json_encode($response);
            }
            break;
        default:
            break;
    }
} else {
    $response = array('status' => 'error', 'message' => 'Invalid request');
    echo json_encode($response);
}
