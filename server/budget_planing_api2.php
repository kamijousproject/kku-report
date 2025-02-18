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
        case "kku_bgp_project-activities":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS (
                        SELECT p.Faculty
                        ,p.Plan
                        ,p.Sub_Plan
                        ,p.Project
                        ,p.Fund
                        ,sum(p.Total_Amount_Quantity) AS Total_Amount_Quantity
                        ,b.Proj_KPI_Name
                        ,sum(b.Proj_KPI_Target) AS Proj_KPI_Target
                        ,b.UoM_for_Proj_KPI
                        ,b.Objective
                        ,b.Project_Output
                        ,b.Project_Outcome
                        ,b.Project_Impact
                        ,b.KKU_Strategic_Plan_LOV
                        ,b.OKRs_LOV
                        ,b.Principles_of_good_governance
                        ,b.SDGs
                        FROM budget_planning_annual_budget_plan p
                        LEFT JOIN budget_planning_project_kpi b
                        ON p.Faculty=b.Faculty AND p.Project=b.Project
                        GROUP BY p.Faculty
                        ,p.Plan
                        ,p.Sub_Plan
                        ,p.Project
                        ,p.Fund
                        ,b.Proj_KPI_Name
                        ,b.UoM_for_Proj_KPI
                        ,b.Objective
                        ,b.Project_Output
                        ,b.Project_Outcome
                        ,b.Project_Impact
                        ,b.KKU_Strategic_Plan_LOV
                        ,b.OKRs_LOV
                        ,b.Principles_of_good_governance
                        ,b.SDGs)
                        ,t2 AS (
                        SELECT t.* ,f.Alias_Default
                        FROM t1 t
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON t.faculty=f.faculty)
                        ,t3 AS (
                        SELECT tt.*,pl.plan_name
                        FROM t2 tt
                        LEFT JOIN plan pl
                        ON tt.plan=pl.plan_id)
                        ,t4 AS (
                        SELECT tt.*,s.sub_plan_name
                        FROM t3 tt
                        LEFT JOIN sub_plan s
                        ON tt.sub_plan=s.sub_plan_id)
                        ,t5 AS (
                        SELECT tt.*,pr.project_name
                        FROM t4 tt
                        LEFT JOIN project pr
                        ON tt.project=pr.project_id)
                        ,t6 AS (
                        SELECT tt.*,pil.pillar_name
                        FROM t5 tt
                        LEFT JOIN pilars2 pil
                        ON tt.KKU_Strategic_Plan_LOV=pil.pillar_id)
                        ,t7 AS (
                        SELECT tt.*,ok.okr_name
                        FROM t6 tt
                        LEFT JOIN okr ok
                        ON tt.OKRs_LOV=ok.okr_id)

                        SELECT DISTINCT * FROM t7
                        ORDER BY Faculty,fund,plan,sub_plan,project,KKU_Strategic_Plan_LOV,OKRs_LOV";
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
        case "get_fiscal_year":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT Budget_Management_Year AS y FROM budget_planning_annual_budget_plan";
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
        case "get_fund":
            try {
                $fyear = $_POST["fiscal_year"];
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT fund AS f 
                        FROM budget_planning_annual_budget_plan 
                        WHERE Budget_Management_Year=:fyear";
                $cmd = $conn->prepare($sql);
                $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->execute();
                $bgp = $cmd->fetchAll(PDO::FETCH_ASSOC);

                $conn = null;

                $response = array(
                    'fund' => $bgp,
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
        case "get_faculty":
            try {
                $fyear = $_POST["fiscal_year"];
                $fund = $_POST["fund"];
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT f.Alias_Default AS faculty 
                        FROM budget_planning_annual_budget_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
                        WHERE b.Budget_Management_Year = :fyear
                        AND b.fund = :fund";

                $cmd = $conn->prepare($sql);
                $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->bindParam(':fund', $fund, PDO::PARAM_STR);
                $cmd->execute();
                $bgp = $cmd->fetchAll(PDO::FETCH_ASSOC);

                $conn = null;

                $response = array(
                    'fac' => $bgp,
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
        case "get_faculty_2":
            try {
                $fyear = $_POST["fiscal_year"];
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT f.Alias_Default AS faculty 
                        FROM budget_planning_annual_budget_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
                        WHERE b.Budget_Management_Year = :fyear";

                $cmd = $conn->prepare($sql);
                $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->execute();
                $bgp = $cmd->fetchAll(PDO::FETCH_ASSOC);

                $conn = null;

                $response = array(
                    'fac' => $bgp,
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
        case "kku_bgp_budget-request-summary-expense":
            try {
                $db = new Database();
                $conn = $db->connect();
                $fyear = $_POST["fiscal_year"];
                $faculty = $_POST["faculty"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS(
                SELECT TYPE 
                FROM account 
                WHERE id > (SELECT id FROM account WHERE account = 'Expenses') AND TYPE is not null
                GROUP BY TYPE)
                ,t2 AS (
                SELECT b.Total_Amount_Quantity,a.`type`,f.Alias_Default
                FROM budget_planning_annual_budget_plan b
                LEFT JOIN account a
                ON b.account=a.account
                LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                ON b.faculty=f.faculty
                where b.Budget_Management_Year=:fyear and f.Alias_Default=:faculty)
                ,t3 AS (
                SELECT t.type
                ,COALESCE(SUM(Total_Amount_Quantity),0) AS Total_Amount_Quantity
                FROM t1 t
                LEFT JOIN t2 tt
                ON t.type=tt.type
                GROUP BY t.type)

                SELECT * FROM t3";
                $cmd = $conn->prepare($sql);
                $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->bindParam(':faculty', $faculty, PDO::PARAM_STR);
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