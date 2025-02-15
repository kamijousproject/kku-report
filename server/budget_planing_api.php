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
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
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
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
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
                        ON t.faculty=f.faculty
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
        case "kku_bgp_project-requests":
            try {
                $db = new Database();
                $conn = $db->connect();
                $fyear = $_POST["fiscal_year"];
                $fund = $_POST["fund"];
                $faculty = $_POST["faculty"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS (
                        SELECT b.*,f.Alias_Default
                        FROM budget_planning_annual_budget_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
                        WHERE b.Budget_Management_Year= :fyear AND b.fund= :fund AND f.Alias_Default= :faculty)
                        ,t1_1 AS (
                        SELECT t.faculty
                        ,t.fund
                        ,t.plan
                        ,t.sub_plan
                        ,t.project
                        ,t.Alias_Default
                        ,sum(case when a.type='1.ค่าใช้จ่ายบุคลากร' then t.Total_Amount_Quantity ELSE 0 END) AS a1
                        ,sum(case when a.type='2.ค่าใช้จ่ายดำเนินงาน' then t.Total_Amount_Quantity ELSE 0 END) AS a2
                        ,sum(case when a.type='3.ค่าใช้จ่ายลงทุน' then t.Total_Amount_Quantity ELSE 0 END) AS a3
                        ,sum(case when a.type='4.ค่าใช้จ่ายเงินอุดหนุนดำเนินงาน' then t.Total_Amount_Quantity ELSE 0 END) AS a4
                        ,sum(case when a.type='5.ค่าใช้จ่ายอื่น' then t.Total_Amount_Quantity ELSE 0 END) AS a5
                        ,SUM(t.Q1_Spending_Plan) AS q1
                        ,SUM(t.Q2_Spending_Plan) AS q2
                        ,SUM(t.Q3_Spending_Plan) AS q3
                        ,SUM(t.Q4_Spending_Plan) AS q4
                        FROM t1 t
                        LEFT JOIN account a
                        ON t.account=a.account
                        GROUP BY t.faculty
                        ,t.fund
                        ,t.plan
                        ,t.sub_plan
                        ,t.project
                        ,t.Alias_Default)
                        ,t2 AS (
                        SELECT t.*,pr.project_name
                        FROM t1_1 t
                        LEFT JOIN project pr
                        ON t.project=pr.project_id)
                        ,t3 AS (
                        SELECT t.*,p.pillar_name,ok.okr_name
                        FROM t2 t
                        LEFT JOIN budget_planning_project_kpi b2
                        ON t.project=b2.Project AND t.faculty = b2.Faculty
                        LEFT JOIN pilars2 p
                        ON b2.KKU_Strategic_Plan_LOV=p.pillar_id
                        LEFT JOIN okr ok
                        ON b2.OKRs_LOV=ok.okr_id)
                        ,t4 AS (
                        SELECT t.*,pl.plan_name,sp.sub_plan_name
                        FROM t3 t
                        LEFT JOIN plan pl
                        ON t.plan=pl.plan_id
                        LEFT JOIN sub_plan sp
                        on t.sub_plan=sp.sub_plan_id)

                        SELECT * FROM t4
                        ORDER BY project";
                $cmd = $conn->prepare($sql);
                $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->bindParam(':fund', $fund, PDO::PARAM_STR);
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
        case "kku_bgp_budget-request-summary-revenue":
            try {
                $db = new Database();
                $conn = $db->connect();
                $fyear = $_POST["fiscal_year"];
                $faculty = $_POST["faculty"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS(
                SELECT TYPE 
                FROM account 
                WHERE id < (SELECT id FROM account WHERE account = 'Expenses') AND TYPE is not null
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