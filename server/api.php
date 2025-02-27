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
        case "get_kku_planing_level":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT  pkap.*,REPLACE(SUBSTRING_INDEX(pkap.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,si.pillar_name AS si_name, so.pillar_name AS so_name,ksp.ksp_name , okr.okr_name
                            FROM planning_kku_action_plan AS pkap
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pkap.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pkap.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = pkap.Strategic_Project
                            LEFT JOIN okr ON okr.okr_id = pkap.OKR
                            ORDER BY 
                            si_code,
                            pkap.Strategic_Object,
                            pkap.OKR,
                            pkap.Strategic_Project";
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
        case "get_kku_planing_change":
            try {
                $db = new Database();
                $conn = $db->connect();

                // KKU Action Plan/ Faculty Action Plan
                $sqlPlan = "SELECT
                            REPLACE(SUBSTRING_INDEX(pkap.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,  
                            pkap.Strategic_Object,
                            pkap.Strategic_Project,
                            pkap.Faculty,
                            pkap.Budget_Amount,
                            pkap.Responsible_person,
                            si.pilar_name AS si_name, 
                            so.pilar_name AS so_name, 
                            ksp.ksp_name
                            FROM planning_kku_action_plan AS pkap
                            LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pkap.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so ON so.pilar_id = pkap.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = TRIM(pkap.Strategic_Project)


                            UNION ALL

                            SELECT
                            REPLACE(SUBSTRING_INDEX(pfap.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,  
                            pfap.Strategic_Object,
                            pfap.Strategic_Project,
                            pfap.Faculty,
                            pfap.Budget_Amount,
                            pfap.Responsible_person,
                            si.pilar_name AS si_name, 
                            so.pilar_name AS so_name, 
                            ksp.ksp_name
                            FROM planning_faculty_action_plan AS pfap
                            LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pfap.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so ON so.pilar_id = pfap.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = TRIM(pfap.Strategic_Project)

                            ORDER BY Faculty desc,si_code, Strategic_Object, Strategic_Project";
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
                            pkpp.Faculty,
                            Faculty.Alias_Default AS fa_name,
                            REPLACE(SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                            si.pillar_name AS si_name,
                            pkpp.Strategic_Object,
                            pkpp.Strategic_Project,
                            ksp.ksp_name,
                            pkpp.Progress_Status,
                            pkpp.Strategic_Project_Progress_Details
                        FROM planning_kku_project_progress AS pkpp
                        LEFT JOIN ksp ON ksp.ksp_id = TRIM(pkpp.Strategic_Project)
                        LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1), 'SO', 'SI')
                        LEFT JOIN Faculty ON Faculty.Faculty = pkpp.Faculty

                        UNION ALL 

                        SELECT 
                            pfpp.Faculty,
                            f.Alias_Default AS fa_name,
                            REPLACE(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                            si.pillar_name AS si_name,
                            pfpp.Strategic_Object,
                            pfpp.Strategic_Project,
                            ksp.ksp_name,
                            pfpp.Progress_Status,
                            pfpp.Strategic_Project_Progress_Details
                        FROM planning_faculty_project_progress AS pfpp
                        LEFT JOIN ksp ON ksp.ksp_id = TRIM(pfpp.Strategic_Project)
                        LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), 'SO', 'SI')
                        LEFT JOIN (
                            SELECT DISTINCT Faculty, Alias_Default
                            FROM Faculty
                        ) AS f ON pfpp.Faculty = f.Faculty

                        ORDER BY fa_name, si_code, Strategic_Project";
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

        case "get_kku_planing_indicator_compare":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT pfap.*,REPLACE(SUBSTRING_INDEX(pfap.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,si.pillar_name AS si_name, so.pillar_name AS so_name,ksp.ksp_name , okr.okr_name
                            FROM planning_faculty_action_plan AS pfap
                            LEFT JOIN ksp ON ksp.ksp_id = TRIM(pfap.Strategic_Project)
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pfap.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pfap.Strategic_Object
                            LEFT JOIN okr ON okr.okr_id = pfap.OKR
                            ORDER BY 
                            si_code,
                            pfap.Strategic_Object,
                            pfap.OKR,
                            pfap.Strategic_Project";
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
        case "get_kku_strategy-overview":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                            pksp.*, 
                            Faculty.Alias_Default  AS fa_name,
                            CONCAT(
                            LEFT(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1), LOCATE('SO', pksp.Strategic_Object) - 1),
                            'P',
                            SUBSTRING(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1), LOCATE('SO', pksp.Strategic_Object) + 2, 2 ) ) as pilar_code,
                            p.pillar_name,
                            REPLACE(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                            si.pillar_name AS si_name,
                            so.pillar_name as so_name,
                            ksp.ksp_name,
                            okr.okr_name,
                            pkop.Quarter_Progress_Value
                            FROM 
                            planning_kku_strategic_plan AS pksp
                            LEFT JOIN Faculty 
                            ON pksp.Faculty = Faculty.Faculty
                            LEFT JOIN pilars2 AS p ON p.pillar_id = CONCAT(LEFT(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1), LOCATE('SO', pksp.Strategic_Object) - 1),'P',
                            SUBSTRING(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1),LOCATE('SO', pksp.Strategic_Object) + 2,2))
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pksp.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = TRIM(pksp.Strategic_Project)
                            LEFT JOIN okr ON okr.okr_id = pksp.okr
                            LEFT JOIN planning_kku_okr_progress as pkop ON pkop.OKR = pksp.OKR
                            ORDER BY Faculty, pilar_code, si_code , pksp.Strategic_Object , pksp.Strategic_Project,pkop.OKR";
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
        case "get_kku_budget_expenses":
            try {
                $db = new Database();
                $conn = $db->connect();
                $faculty = $_POST["faculty"];
                // เชื่อมต่อฐานข้อมูล
                /* $sqlPlan = "SELECT 
                            pkpp.Faculty,
                            Faculty.Alias_Default AS fa_name,
                            CONCAT(
                            LEFT(SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1), LOCATE('SO', pkpp.Strategic_Object) - 1),
                            'P',
                            SUBSTRING(SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1), LOCATE('SO', pkpp.Strategic_Object) + 2, 2 ) ) as pilar_code,
                            p.pilar_name,
                            REPLACE(SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1), 'SO', 'SI')AS si_code,
                            si.pilar_name AS si_name,
                            pkpp.Strategic_Object,
                            so.pilar_name AS so_name,
                            pkpp.Strategic_Project,
                            ksp.ksp_name,
                            pkap.Budget_Amount AS budget,
                            pkpp.Allocated_budget,
                            pkpp.Actual_Spend_Amount
                            FROM planning_kku_project_progress AS pkpp
                            LEFT JOIN Faculty ON Faculty.Faculty = pkpp.Faculty
                            LEFT JOIN pilar AS p 
                            ON p.pilar_id = CONCAT(
                            LEFT(SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1), LOCATE('SO', pkpp.Strategic_Object) - 1),
                            'P',
                            SUBSTRING(
                            SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1),
                            LOCATE('SO', pkpp.Strategic_Object) + 2,2))
                            LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so ON so.pilar_id = pkpp.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = TRIM(pkpp.Strategic_Project)
                            LEFT JOIN planning_kku_action_plan AS pkap ON TRIM(pkap.Strategic_Project) = TRIM(pkpp.Strategic_Project)

                            UNION ALL 

                            SELECT 
                            pfpp.Faculty,
                            f.Alias_Default AS fa_name,
                            CONCAT(
                            LEFT(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), LOCATE('SO', pfpp.Strategic_Object) - 1),
                            'P',
                            SUBSTRING(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), LOCATE('SO', pfpp.Strategic_Object) + 2, 2 ) ) as pilar_code,
                            p.pilar_name,
                            REPLACE(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), 'SO', 'SI')AS si_code,
                            si.pilar_name AS si_name,
                            pfpp.Strategic_Object,
                            so.pilar_name AS so_name,
                            pfpp.Strategic_Project,
                            ksp.ksp_name,
                            pfap.Budget_Amount AS budget,
                            pfpp.Allocated_budget,
                            pfpp.Actual_Spend_Amount
                            FROM planning_faculty_project_progress AS pfpp
                            LEFT JOIN (
                                                        SELECT DISTINCT Faculty, Alias_Default
                                                        FROM Faculty
                                                    ) AS f ON pfpp.Faculty = f.Faculty
                            LEFT JOIN pilar AS p 
                            ON p.pilar_id = CONCAT(
                            LEFT(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), LOCATE('SO', pfpp.Strategic_Object) - 1),
                            'P',
                            SUBSTRING(
                            SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1),
                            LOCATE('SO', pfpp.Strategic_Object) + 2,2))
                            LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so ON so.pilar_id = pfpp.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = TRIM(pfpp.Strategic_Project)
                            LEFT JOIN planning_faculty_action_plan AS pfap ON TRIM(pfap.Strategic_Project) = TRIM(pfpp.Strategic_Project)

                            ORDER BY fa_name, pilar_code, si_code, Strategic_Object, Strategic_Project"; */
                $sqlPlan = "WITH t1 AS(
                            SELECT *
                            FROM pilars2
                            WHERE pillar_id LIKE 'F00SI%' or pillar_id LIKE 'F00P%'
                            ORDER BY id)
                            ,t2 AS (
                            SELECT KKU_Strategic_Plan_LOV
                            ,SUM(Budget_Amount) AS Budget_Amount
                            FROM planning_faculty_action_plan
                            where faculty=:faculty
                            GROUP BY KKU_Strategic_Plan_LOV)
                            ,t3 AS (
                            SELECT t.*,tt.Budget_Amount
                            FROM t1 t
                            LEFT JOIN t2 tt
                            ON t.pillar_id=replace(tt.KKU_Strategic_Plan_LOV,'_',''))
                            ,t4 AS (
                            SELECT p1.KKU_Strategic_Plan_LOV
                            ,SUM(p2.Allocated_budget) AS Allocated_budget
                            ,SUM(p2.Actual_Spend_Amount) AS Actual_Spend_Amount
                            FROM planning_faculty_action_plan p1
                            LEFT JOIN planning_faculty_project_progress p2
                            ON p1.faculty=p2.Faculty AND p1.Strategic_Project=p2.Strategic_Project
                            where p1.faculty=:faculty
                            GROUP BY p1.KKU_Strategic_Plan_LOV)
                            ,t5 AS (
                            SELECT t.*,tt.*
                            FROM t3 t
                            LEFT JOIN t4 tt
                            ON t.pillar_id=replace(tt.KKU_Strategic_Plan_LOV,'_',''))
                
                            SELECT * FROM t5";
                $stmtPlan = $conn->prepare($sqlPlan);
                $stmtPlan->bindParam(':faculty', $faculty, PDO::PARAM_STR);
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
        case "get_kku_annual_action_summary":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                            pkop.*,
                            CONCAT(
                            LEFT(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), LOCATE('SO', pkop.Strategic_Object) - 1),
                            'P',
                            SUBSTRING(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), LOCATE('SO', pkop.Strategic_Object) + 2, 2 ) ) as pilar_code,
                            p.pillar_name AS pilar_name,
                            REPLACE(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                            si.pillar_name AS si_name,
                            pkop.Strategic_Object,
                            so.pillar_name AS so_name,
                            pkop.OKR,
                            okr.okr_name,
                            pkap.Target_OKR_Objective_and_Key_Result,
                            pkap.UOM,
                            pkap.Budget_Amount,
                            pkpp.Allocated_budget,
                            pkpp.Actual_Spend_Amount,
                            pkap.Responsible_person,
                            Faculty.Alias_Default AS fa_name,
                            ksp.ksp_name
                            FROM planning_kku_okr_progress AS pkop
                            LEFT JOIN okr ON okr.okr_id = pkop.OKR
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pkop.Strategic_Object
                            LEFT JOIN pilars2 AS p ON p.pillar_id = CONCAT(LEFT(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), LOCATE('SO', pkop.Strategic_Object) - 1),'P',
                            SUBSTRING(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1),LOCATE('SO', pkop.Strategic_Object) + 2,2))
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN planning_kku_action_plan AS pkap ON pkap.OKR = pkop.OKR
                            LEFT JOIN planning_kku_project_progress AS pkpp ON TRIM(pkpp.Strategic_Project) = TRIM(pkap.Strategic_Project)
                            LEFT JOIN Faculty ON Faculty.Faculty = pkop.Faculty
                            LEFT JOIN ksp ON ksp.ksp_id = pkap.Strategic_Project
                            ORDER BY Faculty, si_code,pkop.Strategic_Object, okr.okr_id,ksp.ksp_id";
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

        case "get_strategic_issues":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                                pkop.*,
                                CONCAT(
                                LEFT(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), LOCATE('SO', pkop.Strategic_Object) - 1),
                                'P',
                                SUBSTRING(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), LOCATE('SO', pkop.Strategic_Object) + 2, 2 ) ) as pilar_code,
                                p.pillar_name AS pilar_name,
                                REPLACE(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                                si.pillar_name AS si_name,
                                pkop.Strategic_Object,
                                so.pillar_name AS so_name,
                                pkop.OKR,
                                okr.okr_name,
                                pkap.Target_OKR_Objective_and_Key_Result,
                                pkap.UOM,
                                pkap.Budget_Amount,
                                pkpp.Allocated_budget,
                                pkpp.Actual_Spend_Amount,
                                pkap.Responsible_person,
                                Faculty.Alias_Default AS fa_name,
                                ksp.ksp_name
                                FROM planning_kku_okr_progress AS pkop
                                LEFT JOIN okr ON okr.okr_id = pkop.OKR
                                LEFT JOIN pilars2 AS so ON so.pillar_id = pkop.Strategic_Object
                                LEFT JOIN pilars2 AS p ON p.pillar_id = CONCAT(LEFT(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), LOCATE('SO', pkop.Strategic_Object) - 1),'P',
                                SUBSTRING(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1),LOCATE('SO', pkop.Strategic_Object) + 2,2))
                                LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), 'SO', 'SI')
                                LEFT JOIN planning_kku_action_plan AS pkap ON pkap.OKR = pkop.OKR
                                LEFT JOIN planning_kku_project_progress AS pkpp ON TRIM(pkpp.Strategic_Project) = TRIM(pkap.Strategic_Project)
                                LEFT JOIN Faculty ON Faculty.Faculty = pkop.Faculty
                                LEFT JOIN ksp ON ksp.ksp_id = pkap.Strategic_Project
                                ORDER BY Faculty, si_code,pkop.Strategic_Object, okr.okr_id,ksp.ksp_id";
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





        case "get_department_strategy_overview":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                                pfsp.*,
                                f.Alias_Default AS fa_name,
                                CONCAT(LEFT(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1), LOCATE('SO', pfsp.Strategic_Object) - 1),'P',
                                SUBSTRING(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1),LOCATE('SO', pfsp.Strategic_Object) + 2,2)) AS pilar_code,
                                p.pillar_name AS pilar_name ,
                                REPLACE(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                                si.pillar_name AS si_name,
                                so.pillar_name AS so_name,
                                ksp.ksp_name,
                                okr.okr_name,
                                pfop.Quarter_Progress_Value
                                FROM planning_faculty_strategic_plan AS pfsp
                                LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default FROM Faculty ) AS f ON pfsp.Faculty = f.Faculty
                                LEFT JOIN pilars2 AS so ON so.pillar_id = pfsp.Strategic_Object
                                LEFT JOIN pilars2 AS p ON p.pillar_id = CONCAT(LEFT(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1), LOCATE('SO', pfsp.Strategic_Object) - 1),'P',
                                SUBSTRING(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1),LOCATE('SO', pfsp.Strategic_Object) + 2,2))
                                LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1), 'SO', 'SI')
                                LEFT JOIN ksp ON ksp.ksp_id = pfsp.Strategic_Project
                                LEFT JOIN okr ON okr.okr_id = pfsp.OKR
                                LEFT JOIN planning_faculty_okr_progress AS pfop ON pfop.OKR = pfsp.OKR
                                ORDER BY Faculty,pilar_code,si_code,pfsp.Strategic_Object,pfsp.Strategic_Project,pfop.OKR";
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
        case "get_faculty_get_strategic_indicators":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT f.Alias_Default AS faculty ,b.faculty as fcode
                        FROM planning_faculty_strategic_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty";

                $cmd = $conn->prepare($sql);
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
        case "get_strategic-indicators":
            try {
                $db = new Database();
                $conn = $db->connect();
                $faculty = $_POST["faculty"];
                // เชื่อมต่อฐานข้อมูล
                /* $sqlPlan = "SELECT
                            f.Alias_Default AS fa_name,
                            pfsp.* 
                            FROM planning_faculty_strategic_plan AS pfsp
                            LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default FROM Faculty) AS f ON pfsp.Faculty = f.Faculty
                            ORDER BY fa_name"; */
                $sqlPlan = "WITH t1 AS (
                            SELECT Faculty,COUNT(*) AS count_okr
                            FROM planning_faculty_strategic_plan
                            where faculty=:faculty
                            GROUP BY Faculty)
                            ,t2 AS (
                            SELECT t.*,tt.KKU_Strategic_Plan_LOV,COUNT(*) AS count_st
                            FROM t1 t
                            LEFT JOIN planning_faculty_strategic_plan tt
                            ON t.Faculty=tt.Faculty
                            GROUP BY t.Faculty,t.count_okr,tt.KKU_Strategic_Plan_LOV)
                            ,t3 AS (
                            SELECT Faculty,count_okr
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI111' then count_st ELSE 0 END ) AS s1
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI112' then count_st ELSE 0 END ) AS s2
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI113' then count_st ELSE 0 END ) AS s3
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI114' then count_st ELSE 0 END ) AS s4
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI125' then count_st ELSE 0 END ) AS s5
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI126' then count_st ELSE 0 END ) AS s6
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI127' then count_st ELSE 0 END ) AS s7
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI128' then count_st ELSE 0 END ) AS s8
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI129' then count_st ELSE 0 END ) AS s9
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI1310' then count_st ELSE 0 END ) AS s10
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI1311' then count_st ELSE 0 END ) AS s11
                            FROM t2
                            GROUP BY Faculty,count_okr)
                            ,t4 AS (
                            SELECT Faculty ,COUNT(*) AS dev_plan
                            FROM planning_faculty_strategic_plan
                            WHERE Dev_Plan_Proposed_to_Nomination_Co_LOV='สอดคล้อง'
                            GROUP BY Faculty)
                            ,t5 AS (
                            SELECT Faculty ,COUNT(*) AS divis
                            FROM planning_faculty_strategic_plan
                            WHERE Division_Noteworthy_Plan_LOV='สอดคล้อง'
                            GROUP BY Faculty)
                            ,t6 AS (
                            SELECT distinct t.*,tt.dev_plan,ttt.divis,f.Alias_Default
                            FROM t3 t
                            LEFT JOIN t4 tt
                            ON t.faculty=tt.Faculty
                            LEFT JOIN t5 ttt
                            ON t.faculty=tt.Faculty
                            LEFT JOIN Faculty f
                            ON t.faculty = f.Faculty)

                            SELECT * FROM t6";
                $stmtPlan = $conn->prepare($sqlPlan);
                $stmtPlan->bindParam(':faculty', $faculty, PDO::PARAM_STR);
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
        case "get_department-indicators":
            try {
                $db = new Database();
                $conn = $db->connect();
                $faculty = $_POST["faculty"];
                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "WITH t1 AS (
                            SELECT Faculty,COUNT(*) AS count_okr
                            FROM planning_faculty_action_plan
                            where faculty=:faculty
                            GROUP BY Faculty)
                            ,t2 AS (
                            SELECT t.*,tt.KKU_Strategic_Plan_LOV,COUNT(*) AS count_st
                            FROM t1 t
                            LEFT JOIN planning_faculty_action_plan tt
                            ON t.Faculty=tt.Faculty
                            GROUP BY t.Faculty,t.count_okr,tt.KKU_Strategic_Plan_LOV)
                            ,t3 AS (
                            SELECT Faculty,count_okr
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI111' then count_st ELSE 0 END ) AS s1
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI112' then count_st ELSE 0 END ) AS s2
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI113' then count_st ELSE 0 END ) AS s3
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI114' then count_st ELSE 0 END ) AS s4
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI125' then count_st ELSE 0 END ) AS s5
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI126' then count_st ELSE 0 END ) AS s6
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI127' then count_st ELSE 0 END ) AS s7
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI128' then count_st ELSE 0 END ) AS s8
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI129' then count_st ELSE 0 END ) AS s9
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI1310' then count_st ELSE 0 END ) AS s10
                            ,sum(case when  KKU_Strategic_Plan_LOV='F00_SI1311' then count_st ELSE 0 END ) AS s11
                            FROM t2
                            GROUP BY Faculty,count_okr)
                            ,t4 AS (
                            SELECT Faculty ,COUNT(*) AS dev_plan
                            FROM planning_faculty_action_plan
                            WHERE Dev_Plan_Proposed_to_Nomination_Co_LOV='สอดคล้อง'
                            GROUP BY Faculty)
                            ,t5 AS (
                            SELECT Faculty ,COUNT(*) AS divis
                            FROM planning_faculty_action_plan
                            WHERE Division_Noteworthy_Plan_LOV='สอดคล้อง'
                            GROUP BY Faculty)
                            ,t6 AS (
                            SELECT distinct t.*,tt.dev_plan,ttt.divis,f.Alias_Default
                            FROM t3 t
                            LEFT JOIN t4 tt
                            ON t.faculty=tt.Faculty
                            LEFT JOIN t5 ttt
                            ON t.faculty=tt.Faculty
                            LEFT JOIN Faculty f
                            ON t.faculty = f.Faculty)

                            SELECT * FROM t6
                            ";
                $stmtPlan = $conn->prepare($sqlPlan);
                $stmtPlan->bindParam(':faculty', $faculty, PDO::PARAM_STR);
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
        case "get_department-action-summary":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                                pfop.*,
                                CONCAT(
                                LEFT(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), LOCATE('SO', pfop.Strategic_Object) - 1),
                                'P',
                                SUBSTRING(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), LOCATE('SO', pfop.Strategic_Object) + 2, 2 ) ) as pilar_code,
                                p.pilar_name,
                                REPLACE(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                                si.pilar_name AS si_name,
                                pfop.Strategic_Object,
                                so.pilar_name AS so_name,
                                pfop.OKR,
                                okr.okr_name,
                                pfap.Target_OKR_Objective_and_Key_Result,
                                pfap.UOM,
                                pfap.Budget_Amount,
                                pfpp.Allocated_budget,
                                pfpp.Actual_Spend_Amount,
                                pfap.Responsible_person,
                                Faculty.Alias_Default AS fa_name,
                                ksp.ksp_name
                                FROM planning_faculty_okr_progress AS pfop
                                LEFT JOIN okr ON okr.okr_id = pfop.OKR
                                LEFT JOIN pilar AS so ON so.pilar_id = pfop.Strategic_Object
                                LEFT JOIN pilar AS p ON p.pilar_id = CONCAT(LEFT(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), LOCATE('SO', pfop.Strategic_Object) - 1),'P',
                                SUBSTRING(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1),LOCATE('SO', pfop.Strategic_Object) + 2,2))
                                LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), 'SO', 'SI')
                                LEFT JOIN planning_faculty_action_plan AS pfap ON pfap.OKR = pfop.OKR
                                LEFT JOIN planning_faculty_project_progress AS pfpp ON TRIM(pfpp.Strategic_Project) = TRIM(pfap.Strategic_Project)
                                LEFT JOIN Faculty ON Faculty.Faculty = pfop.Faculty
                                LEFT JOIN ksp ON ksp.ksp_id = pfap.Strategic_Project
                                ORDER BY Faculty, si_code,pfop.Strategic_Object, okr.okr_id,ksp.ksp_id";
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

        case "get_department_strategic_issues":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT
                            pfop.*,
                            CONCAT(
                            LEFT(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), LOCATE('SO', pfop.Strategic_Object) - 1),
                            'P',
                            SUBSTRING(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), LOCATE('SO', pfop.Strategic_Object) + 2, 2 ) ) as pilar_code,
                            p.pillar_name AS pilar_name,
                            REPLACE(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                            si.pillar_name AS si_name,
                            pfop.Strategic_Object,
                            so.pillar_name AS so_name,
                            pfop.OKR,
                            okr.okr_name,
                            pfap.Target_OKR_Objective_and_Key_Result,
                            pfap.UOM,
                            pfap.Budget_Amount,
                            pfpp.Allocated_budget,
                            pfpp.Actual_Spend_Amount,
                            pfap.Responsible_person,
                            Faculty.Alias_Default AS fa_name,
                            ksp.ksp_name
                            FROM planning_faculty_okr_progress AS pfop
                            LEFT JOIN okr ON okr.okr_id = pfop.OKR
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pfop.Strategic_Object
                            LEFT JOIN pilars2 AS p ON p.pillar_id = CONCAT(LEFT(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), LOCATE('SO', pfop.Strategic_Object) - 1),'P',
                            SUBSTRING(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1),LOCATE('SO', pfop.Strategic_Object) + 2,2))
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(SUBSTRING_INDEX(pfop.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN planning_faculty_action_plan AS pfap ON pfap.OKR = pfop.OKR
                            LEFT JOIN planning_faculty_project_progress AS pfpp ON TRIM(pfpp.Strategic_Project) = TRIM(pfap.Strategic_Project)
                            LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default FROM Faculty) AS Faculty ON Faculty.Faculty = pfop.Faculty
                            LEFT JOIN ksp ON ksp.ksp_id = pfap.Strategic_Project
                            ORDER BY Faculty, si_code,pfop.Strategic_Object, okr.okr_id,ksp.ksp_id";
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




        case "report-project-summary":
            try {
                $db = new Database();
                $conn = $db->connect();

                $scenario = isset($_POST['scenario']) ? $_POST['scenario'] : 'Annual Budget Plan';
                $fund = isset($_POST['fund']) ? $_POST['fund'] : 'FN06';

                $sqlPlan = "SELECT
                                    project.project_name,
                                    annual_bp.Project,
                                    b_actual.FISCAL_YEAR,
                                    annual_bp.Budget_Management_Year,
                                    annual_bp.Plan,
                                    annual_bp.Sub_Plan,
                                    annual_bp.Faculty,
                                    annual_bp.Total_Amount_Quantity,
                                    f.Alias_Default AS faculty_name,
                                    account.alias_default,
                                    plan.plan_name,
                                    sub_plan.sub_plan_name,
                                    account.parent,
                                    kpi.KKU_Strategic_Plan_LOV,  
                                    pilar.pillar_name  
                                FROM
                                    budget_planning_annual_budget_plan AS annual_bp
                                    LEFT JOIN (
                                        SELECT DISTINCT Faculty, fund, plan, subplan, project, account, service, fiscal_year
                                        FROM budget_planning_actual
                                    ) b_actual 
                                    ON b_actual.PLAN = annual_bp.Plan
                                    AND annual_bp.faculty = b_actual.Faculty
                                    AND b_actual.SUBPLAN = REPLACE(annual_bp.Sub_Plan, 'SP_', '')
                                    AND b_actual.PROJECT = annual_bp.Project
                                    AND annual_bp.account = b_actual.account
                                    AND b_actual.fund = REPLACE(annual_bp.fund, 'FN', '')
                                    AND b_actual.service = REPLACE(annual_bp.service, 'SR_', '')
            
                                    LEFT JOIN account ON account.account = annual_bp.Account
                                    LEFT JOIN (
                                        SELECT * FROM Faculty WHERE parent LIKE 'FACULTY%'
                                    ) f ON f.Faculty = annual_bp.Faculty
                                    LEFT JOIN plan ON plan.plan_id = annual_bp.Plan
                                    LEFT JOIN sub_plan ON sub_plan.sub_plan_id = annual_bp.Sub_Plan
                                    LEFT JOIN project ON project.project_id = annual_bp.Project
                                    
                                    LEFT JOIN (
                                        SELECT Project, MAX(KKU_Strategic_Plan_LOV) AS KKU_Strategic_Plan_LOV 
                                        FROM budget_planning_project_kpi 
                                        GROUP BY Project
                                    ) kpi ON kpi.Project = annual_bp.Project
            
                                    LEFT JOIN (
                                        SELECT pillar_id, MAX(pillar_name) AS pillar_name
                                        FROM pilars2
                                        GROUP BY pillar_id
                                    ) pilar ON pilar.pillar_id = REPLACE(kpi.KKU_Strategic_Plan_LOV, '_', '')
            
                                WHERE
                                    annual_bp.Scenario = :scenario
                                    AND annual_bp.Fund = :fund";

                $stmtPlan = $conn->prepare($sqlPlan);
                $stmtPlan->bindParam(':scenario', $scenario, PDO::PARAM_STR);
                $stmtPlan->bindParam(':fund', $fund, PDO::PARAM_STR);
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
        case "report-revenue-estimation-comparison":
            try {
                $db = new Database();
                $conn = $db->connect();

                $yearselect = isset($_POST['yearselect']) ? $_POST['yearselect'] : '2568';

                $sqlrevenue = "SELECT
                                            abp.KKU_Item_Name,
                                            abp.Q1_Spending_Plan,
                                            abp.Q2_Spending_Plan,
                                            abp.Q3_Spending_Plan,
                                            abp.Q4_Spending_Plan,
                                            abp.Total_Amount_Quantity,
                                            acc.sub_type,
                                            acc.parent,
                                            pj.project_name,
                                            sp.sub_plan_name,
                                            plan.plan_name
                                    FROM
                                            budget_planning_annual_budget_plan abp
                                            LEFT JOIN account acc ON abp.`Account` = acc.`account`
                                            LEFT JOIN project pj ON pj.project_id = abp.Project
                                            LEFT JOIN sub_plan sp ON sp.sub_plan_id = abp.Sub_Plan
                                            LEFT JOIN plan ON plan.plan_id = abp.Plan
                                    WHERE
                                            abp.Budget_Management_Year = :yearselect
                                            AND acc.parent LIKE '4%'";

                $stmtrevenue = $conn->prepare($sqlrevenue);
                $stmtrevenue->bindParam(':yearselect', $yearselect, PDO::PARAM_STR);
                $stmtrevenue->execute();
                $revenue = $stmtrevenue->fetchAll(PDO::FETCH_ASSOC);
                $conn = null;

                $response = array(
                    'revenue' => $revenue
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
        case "report-budget-requests":
            try {
                $db = new Database();
                $conn = $db->connect();

                $yearselect = isset($_POST['yearselect']) ? $_POST['yearselect'] : '2568';

                $sqlbudget = "SELECT
                                                plan.plan_name,
                                                sp.sub_plan_name,
                                                pj.project_name,
                                                acc.alias_default,
                                                acc.`type`,
                                                abp.KKU_Item_Name,
                                                abp.Fund,
                                                abp.Total_Amount_Quantity,
                                                Faculty.Faculty,
                                                Faculty.Alias_Default,
                                                (
                                                    SELECT
                                                            Faculty_Parent.Alias_Default
                                                    FROM
                                                            Faculty Faculty_Parent
                                                    WHERE
                                                            Faculty_Parent.Faculty = CONCAT(
                                                            LEFT(Faculty.Faculty, 2),
                                                            '000'
                                                )
                                                    LIMIT
                                                            1
                                                ) AS Alias_Default_Parent
                                        FROM
                                                budget_planning_annual_budget_plan abp
                                                LEFT JOIN account acc ON abp.`Account` = acc.`account`
                                                LEFT JOIN plan ON abp.Plan = plan.plan_id
                                                LEFT JOIN sub_plan sp ON sp.sub_plan_id = abp.Sub_Plan
                                                LEFT JOIN project pj ON pj.project_id = abp.Project
                                                LEFT JOIN Faculty ON Faculty.Faculty = abp.Faculty
                                        WHERE
                                                abp.Fund IN ('FN06', 'FN02', 'FN08')
                                                AND acc.`type` LIKE '%ค่าใช้จ่าย%'
                                                AND abp.Budget_Management_Year = :yearselect ";

                $stmtbudget = $conn->prepare($sqlbudget);
                $stmtbudget->bindParam(':yearselect', $yearselect, PDO::PARAM_STR);
                $stmtbudget->execute();
                $budget = $stmtbudget->fetchAll(PDO::FETCH_ASSOC);
                $conn = null;

                $response = array(
                    'budget' => $budget
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
        case "get_faculty_action_plan":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT f.Alias_Default AS faculty ,b.faculty as fcode
                        FROM planning_faculty_action_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty";

                $cmd = $conn->prepare($sql);
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
        case "report-budget-comparison":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT
                                    DISTINCT acc.alias_default AS Account_Alias_Default,
                                    acc.type,
                                    acc.sub_type,
                                    bpanbp.Service,
                                    bpanbp.Account,
                                    bpanbp.Faculty,
                                    bpanbp.Plan,
                                    bpanbp.Sub_Plan,
                                    bpanbp.Project,
                                    bpanbp.KKU_Item_Name,
                                    bpanbp.Allocated_Total_Amount_Quantity,
                                    bpanbp.Reason,
                                    sp_kpi.UoM_for_Sub_plan_KPI,
                                    sp_kpi.Sub_plan_KPI_Name,
                                    pj_kpi.UoM_for_Proj_KPI,
                                    pj_kpi.Proj_KPI_Name,
                                    bpanbp.Fund,
                                    bpabp.Total_Amount_Quantity,
                                    bpabp.Fund,
                                    f.Alias_Default AS Faculty_Name,
                            (
                                    SELECT
                                            Faculty_Parent.Alias_Default
                                    FROM
                                            Faculty Faculty_Parent
                                    WHERE
                                            Faculty_Parent.Faculty = CONCAT(
                                            LEFT(f.Faculty, 2),
                                            '000'
                                    )
                                    LIMIT
                                    1
                                                ) AS Alias_Default_Parent,
                                    p.plan_name AS Plan_Name,
                                    sp.sub_plan_name AS Sub_Plan_Name,
                                    pr.project_name AS Project_Name
                                    FROM
                                    budget_planning_allocated_annual_budget_plan bpanbp
                                    LEFT JOIN (
                                    SELECT
                                    DISTINCT Account,
                                            Plan,
                                            Sub_Plan,
                                            Project,
                                            Total_Amount_Quantity,
                                            Fund
                                    FROM
                                            budget_planning_annual_budget_plan
                                        ) bpabp ON bpanbp.Account = bpabp.Account
                                    AND bpanbp.Plan = bpabp.Plan
                                    AND bpanbp.Sub_Plan = bpabp.Sub_Plan
                                    AND bpanbp.Project = bpabp.Project
                                    LEFT JOIN (
                                    SELECT
                                    DISTINCT Plan,
                                    Sub_Plan,
                                    Faculty,
                                    UoM_for_Sub_plan_KPI,
                                    Sub_plan_KPI_Name
                                    FROM
                                    budget_planning_subplan_kpi
                                    ) sp_kpi ON bpanbp.Plan = sp_kpi.Plan
                                    AND bpanbp.Sub_Plan = sp_kpi.Sub_Plan
                                    AND bpanbp.Faculty = sp_kpi.Faculty
                                    LEFT JOIN (
                                    SELECT
                                    DISTINCT Faculty,
                                    Project,
                                    UoM_for_Proj_KPI,
                                    Proj_KPI_Name
                                    FROM
                                    budget_planning_project_kpi
                                    ) pj_kpi ON bpanbp.Faculty = pj_kpi.Faculty
                                    AND bpanbp.Project = pj_kpi.Project
                                    LEFT JOIN account acc ON bpanbp.Account = acc.account
                                    LEFT JOIN Faculty f ON bpanbp.Faculty = f.Faculty
                                    LEFT JOIN plan p ON bpanbp.Plan = p.plan_id
                                    LEFT JOIN sub_plan sp ON bpanbp.Sub_Plan = sp.sub_plan_id
                                    LEFT JOIN project pr ON bpanbp.Project = pr.project_id
                                    WHERE
                                    acc.type LIKE '%ค่าใช้จ่าย%';";

                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $budget = $cmd->fetchAll(PDO::FETCH_ASSOC);

                $conn = null;

                $response = array(
                    'budget' => $budget,
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
