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
        case "kku_wf_current-vs-ideal":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT f.name_th,w.All_PositionTypes,w.Position,w.WF,w.Current_HC_of_the_Position
                            ,case when w.wf- w.Current_HC_of_the_Position < 0 then 'เกิน'
                            when w.wf- w.Current_HC_of_the_Position > 0 then 'ขาด'
                            ELSE '' END AS state
                            from workforce_new_positions_allocation w
                            LEFT JOIN Faculty f
                            ON w.Faculty=f.id COLLATE utf8mb4_general_ci";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $wf = $cmd->fetchAll(PDO::FETCH_ASSOC);
                $conn = null;

                $response = array(
                    'wf' => $wf
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

?>