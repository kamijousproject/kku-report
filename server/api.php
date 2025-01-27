<?php
header('Content-Type: application/json');
class Database
{
    private $host = "110.164.146.250";
    private $dbname = "epm_report";
    private $username = "root";
    private $password = "TDyutdYdyudRTYDsEFOPI";
    private $conn;

    public function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $command = $_POST["command"];
    switch ($command) {
        case "get_kku_planing":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                                pka.*, 
                                si.pilar_name AS si_name,
                                so.pilar_name AS so_name,
                                ksp.ksp_name AS ksp_name
                            FROM 
                                planing_ka AS pka
                            LEFT JOIN 
                                pilar AS si 
                            ON 
                                si.pilar_id = REPLACE(SUBSTRING_INDEX(pka.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so ON so.pilar_id = pka.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = pka.Strategic_Project; 
";
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
