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
        case "kku_bgp_budget-revenue-summary":
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
        case "kku_bgp_budget-structure-comparison2":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS (
                        SELECT faculty,Alias_Default FROM Faculty 
                        WHERE parent='Total KKU' AND Faculty !='Faculty-00')
                        ,t1_1 AS (
                        SELECT account,alias_default,TYPE,sub_type
                        FROM account)
                        ,t2 AS (
                        SELECT b.*,f.parent,f.Alias_Default AS f2
                        FROM budget_planning_allocated_annual_budget_plan b
                        LEFT JOIN Faculty f
                        ON b.Faculty=f.Faculty
                        WHERE f.parent NOT LIKE '%BU%' AND b.fund IN ('FN06','FN02'))
                        ,t2_1 AS (
                        SELECT t.*,tt.alias_default AS account_name,tt.type,tt.sub_type
                        FROM t2 t
                        LEFT JOIN t1_1 tt
                        ON t.account=tt.account)
                        ,t3 AS (
                        SELECT t.faculty,t.fund,t.plan,t.sub_plan,t.KKU_Item_Name,t.type,t.sub_type,t.account
                        ,t.project
                        ,SUM(Allocated_Total_Amount_Quantity) AS Allocated_Total_Amount_Quantity
                        ,tt.Alias_Default AS f1
                        ,t.f2
                        FROM t2_1 t
                        LEFT JOIN t1 tt
                        ON t.parent=tt.faculty
                        GROUP BY t.faculty,t.fund,t.plan,t.sub_plan,t.KKU_Item_Name,t.type,t.sub_type,t.account
                        ,t.project,tt.Alias_Default,t.f2)
                        ,t4 AS (
                        SELECT Faculty,fund,plan,subplan,project,account
                        ,SUM(COMMITMENTS) AS COMMITMENTS
                        ,SUM(OBLIGATIONS) AS OBLIGATIONS
                        ,SUM(EXPENDITURES) AS EXPENDITURES
                        FROM budget_planning_actual
                        GROUP BY Faculty,fund,plan,subplan,project,account)
                        ,t5 AS (
                        SELECT t.*,a.COMMITMENTS,a.OBLIGATIONS,a.EXPENDITURES
                        FROM t3 t
                        LEFT JOIN t4 a
                        ON t.faculty=a.FACULTY AND t.fund= CONCAT('FN',a.Fund) AND t.plan=a.plan 
                        AND t.sub_plan=CONCAT('SP_',a.SUBPLAN) 
                        AND t.project=a.project AND t.account=a.account)
                        , t6 AS (
                        SELECT Faculty,plan,sub_plan,project,f1,f2,KKU_Item_Name,type,sub_type
                        ,sum(case when fund='FN02' then COALESCE(Allocated_Total_Amount_Quantity,0) ELSE 0 END) AS a2
                        ,sum(case when fund='FN02' then COALESCE(COMMITMENTS,0) ELSE 0 END) AS c2
                        ,sum(case when fund='FN02' then COALESCE(OBLIGATIONS,0) ELSE 0 END) AS o2
                        ,sum(case when fund='FN02' then COALESCE(EXPENDITURES,0) ELSE 0 END) AS e2
                        ,sum(case when fund='FN06' then COALESCE(Allocated_Total_Amount_Quantity,0) ELSE 0 END) AS a6
                        ,sum(case when fund='FN06' then COALESCE(COMMITMENTS,0) ELSE 0 END) AS c6
                        ,sum(case when fund='FN06' then COALESCE(OBLIGATIONS,0) ELSE 0 END) AS o6
                        ,sum(case when fund='FN06' then COALESCE(EXPENDITURES,0) ELSE 0 END) AS e6
                        FROM t5
                        GROUP BY Faculty,plan,sub_plan,project,f1,f2,KKU_Item_Name,type,sub_type)
                        ,t7 AS (
                        SELECT t.*,p.plan_name,sp.sub_plan_name,pr.project_name ,a.id AS account,aa.id AS sub_account
                        FROM t6 t
                        LEFT JOIN plan p
                        ON t.plan = p.plan_id
                        LEFT JOIN sub_plan sp
                        on t.sub_plan=sp.sub_plan_id
                        LEFT JOIN project pr
                        ON t.project=pr.project_id
                        LEFT JOIN account a
                        ON t.type=a.alias_default COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN account aa
                        ON t.sub_type=aa.alias_default COLLATE UTF8MB4_GENERAL_CI)

                        SELECT * FROM t7
                        ORDER BY Faculty,plan,sub_plan,project,account,sub_account";
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
        case "kku_bgp_budget-spending-status":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS (
                        SELECT b.Faculty
                        ,sum(case when b.Fund='FN06' then b.Total_Amount_Quantity ELSE 0 END) AS t06
                        ,sum(case when b.Fund='FN02' then b.Total_Amount_Quantity ELSE 0 END) AS t02
                        ,sum(case when b.Fund='FN08' then b.Total_Amount_Quantity ELSE 0 END) AS t08
                        ,b.Account
                        ,b.KKU_Item_Name
                        ,b.Budget_Management_Year
                        ,b2.KKU_Strategic_Plan_LOV
                        ,p.pillar_name
                        ,a.`type`
                        ,a.sub_type
                        ,a.id AS p_id
                        ,f.Alias_Default
                        FROM budget_planning_annual_budget_plan b
                        LEFT JOIN budget_planning_project_kpi b2
                        ON b.Faculty=b2.Faculty AND b.Project=b2.Project
                        LEFT JOIN pilars2 p
                        ON b2.KKU_Strategic_Plan_LOV=p.pillar_id
                        LEFT JOIN account a
                        ON b.Account=a.account
                        LEFT JOIN Faculty f
                        ON b.Faculty=f.Faculty
                        GROUP BY b.Faculty
                        ,b.Account
                        ,b.KKU_Item_Name
                        ,b.Budget_Management_Year
                        ,b2.KKU_Strategic_Plan_LOV
                        ,p.pillar_name
                        ,a.`type`
                        ,a.sub_type
                        ,a.id
                        ,f.Alias_Default)

                        SELECT distinct * FROM t1
                        ORDER BY Faculty,KKU_Strategic_Plan_LOV,p_id";
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