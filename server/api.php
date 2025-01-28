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
                $sqlPlan = "SELECT  pfap.*,si.pilar_name AS si_name, so.pilar_name AS so_name,ksp.ksp_name
                            FROM planning_faculty_action_plan AS pfap
                            LEFT JOIN pilar AS si 
                            ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pfap.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so
                            ON si.pilar_id = pfap.Strategic_Object
                            LEFT JOIN ksp 
                            ON ksp.id = pfap.Strategic_Project;";
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
        case "get_kku_planing_status":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
  pp.*, 
  si.pilar_name AS si_name,
  REPLACE(SUBSTRING_INDEX(pp.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
  ksp.ksp_name,
  Faculty.name_th AS fa_name
FROM planning_kku_project_progress AS pp
LEFT JOIN ksp
  ON ksp.ksp_id = TRIM(pp.Strategic_Project)
LEFT JOIN pilar AS si 
  ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pp.Strategic_Object, '-', 1), 'SO', 'SI')
  
LEFT JOIN Faculty 
  ON Faculty.id = pp.Faculty ORDER BY Faculty.id, 
  REPLACE(SUBSTRING_INDEX(pp.Strategic_Object, '-', 1), 'SO', 'SI'), 
  pp.Strategic_Project;
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
