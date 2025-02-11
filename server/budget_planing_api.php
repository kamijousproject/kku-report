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
        case "kku_wf_budget-revenue-summary":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS(
                        SELECT b.*,a.parent FROM budget_planning_annual_budget_plan b
                        LEFT JOIN account a
                        ON b.Account=a.account
                        WHERE a.parent IN ('4100000000','4205000000','3200000000','4201000000','4204000000','4203000000','4206000000','4202000000','4207000000'))
                        , t2 AS(
                        SELECT Faculty
                        ,parent
                        ,SUM(Total_Amount_Quantity) AS Total_Amount
                        FROM t1
                        GROUP BY Faculty
                        ,parent)
                        ,t3 AS(
                        SELECT Faculty
                        ,sum(case when parent='4100000000' then Total_Amount ELSE 0 END) AS a1
                        ,sum(case when parent='4205000000' then Total_Amount ELSE 0 END) AS a2
                        ,sum(case when parent='3200000000' then Total_Amount ELSE 0 END) AS a3
                        ,sum(case when parent='4201000000' then Total_Amount ELSE 0 END) AS a4
                        ,sum(case when parent='4204000000' then Total_Amount ELSE 0 END) AS a5
                        ,sum(case when parent='4203000000' then Total_Amount ELSE 0 END) AS a6
                        ,sum(case when parent='4206000000' then Total_Amount ELSE 0 END) AS a7
                        ,sum(case when parent='4202000000' then Total_Amount ELSE 0 END) AS a8
                        ,sum(case when parent='4207000000' then Total_Amount ELSE 0 END) AS a9
                        FROM t2
                        GROUP BY Faculty)
                        SELECT t.*,f.Alias_Default FROM t3 t
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default 
                        FROM Faculty) f 
                        ON t.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $bgp = $cmd->fetchAll(PDO::FETCH_ASSOC);

                $conn = null;

                $response = array(
                    'bgp' => $bgp,
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