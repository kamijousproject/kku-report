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
                $sqlPlan = "SELECT  pkap.*,REPLACE(SUBSTRING_INDEX(pkap.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,si.pilar_name AS si_name, so.pilar_name AS so_name,ksp.ksp_name , okr.okr_name
                            FROM planning_kku_action_plan AS pkap
                            LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pkap.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so ON so.pilar_id = pkap.Strategic_Object
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
                            si.pilar_name AS si_name,
                            pkpp.Strategic_Object,
                            pkpp.Strategic_Project,
                            ksp.ksp_name,
                            pkpp.Progress_Status
                        FROM planning_kku_project_progress AS pkpp
                        LEFT JOIN ksp ON ksp.ksp_id = TRIM(pkpp.Strategic_Project)
                        LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pkpp.Strategic_Object, '-', 1), 'SO', 'SI')
                        LEFT JOIN Faculty ON Faculty.Faculty = pkpp.Faculty

                        UNION ALL 

                        SELECT 
                            pfpp.Faculty,
                            f.Alias_Default AS fa_name,
                            REPLACE(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                            si.pilar_name AS si_name,
                            pfpp.Strategic_Object,
                            pfpp.Strategic_Project,
                            ksp.ksp_name,
                            pfpp.Progress_Status
                        FROM planning_faculty_project_progress AS pfpp
                        LEFT JOIN ksp ON ksp.ksp_id = TRIM(pfpp.Strategic_Project)
                        LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pfpp.Strategic_Object, '-', 1), 'SO', 'SI')
                        LEFT JOIN (
                            SELECT DISTINCT Faculty, Alias_Default
                            FROM Faculty
                        ) AS f ON pfpp.Faculty = f.Faculty

                        ORDER BY fa_name, si_code, Strategic_Project;";
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
                $sqlPlan = "SELECT pfap.*,REPLACE(SUBSTRING_INDEX(pfap.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,si.pilar_name AS si_name, so.pilar_name AS so_name,ksp.ksp_name , okr.okr_name
                            FROM planning_faculty_action_plan AS pfap
                            LEFT JOIN ksp ON ksp.ksp_id = TRIM(pfap.Strategic_Project)
                            LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pfap.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so ON so.pilar_id = pfap.Strategic_Object
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
                            p.pilar_name,
                            REPLACE(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                            si.pilar_name AS si_name,
                            so.pilar_name as so_name,
                            ksp.ksp_name,
                            okr.okr_name,
                            pkop.Quarter_Progress_Value
                            FROM 
                            planning_kku_strategic_plan AS pksp
                            LEFT JOIN Faculty 
                            ON pksp.Faculty = Faculty.Faculty
                            LEFT JOIN pilar AS p ON p.pilar_id = CONCAT(LEFT(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1), LOCATE('SO', pksp.Strategic_Object) - 1),'P',
                            SUBSTRING(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1),LOCATE('SO', pksp.Strategic_Object) + 2,2))
                            LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pksp.Strategic_Object, '-', 1), 'SO', 'SI')
                            LEFT JOIN pilar AS so ON so.pilar_id = pksp.Strategic_Object
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

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
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

                            ORDER BY fa_name, pilar_code, si_code, Strategic_Object, Strategic_Project";
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
                            p.pilar_name,
                            REPLACE(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                            si.pilar_name AS si_name,
                            pkop.Strategic_Object,
                            so.pilar_name AS so_name,
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
                            LEFT JOIN pilar AS so ON so.pilar_id = pkop.Strategic_Object
                            LEFT JOIN pilar AS p ON p.pilar_id = CONCAT(LEFT(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), LOCATE('SO', pkop.Strategic_Object) - 1),'P',
                            SUBSTRING(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1),LOCATE('SO', pkop.Strategic_Object) + 2,2))
                            LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pkop.Strategic_Object, '-', 1), 'SO', 'SI')
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
                                p.pilar_name,
                                REPLACE(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1), 'SO', 'SI') AS si_code,
                                si.pilar_name AS si_name,
                                so.pilar_name AS so_name,
                                ksp.ksp_name,
                                okr.okr_name,
                                pfop.Quarter_Progress_Value
                                FROM planning_faculty_strategic_plan AS pfsp
                                LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default FROM Faculty ) AS f ON pfsp.Faculty = f.Faculty
                                LEFT JOIN pilar AS so ON so.pilar_id = pfsp.Strategic_Object
                                LEFT JOIN pilar AS p ON p.pilar_id = CONCAT(LEFT(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1), LOCATE('SO', pfsp.Strategic_Object) - 1),'P',
                                SUBSTRING(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1),LOCATE('SO', pfsp.Strategic_Object) + 2,2))
                                LEFT JOIN pilar AS si ON si.pilar_id = REPLACE(SUBSTRING_INDEX(pfsp.Strategic_Object, '-', 1), 'SO', 'SI')
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
        case "get_strategic-indicators":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT
                            f.Alias_Default AS fa_name,
                            pfsp.* 
                            FROM planning_faculty_strategic_plan AS pfsp
                            LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default FROM Faculty) AS f ON pfsp.Faculty = f.Faculty
                            ORDER BY fa_name";
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
        case "get_department-indicators":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT
                            f.Alias_Default AS fa_name,
                            pfap.* 
                            FROM planning_faculty_action_plan AS pfap
                            LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default FROM Faculty) AS f ON pfap.Faculty = f.Faculty
                            ORDER BY fa_name
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
        default:
            break;
    }
} else {
    $response = array('status' => 'error', 'message' => 'Invalid request');
    echo json_encode($response);
}
