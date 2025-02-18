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
                            SELECT p.Strategic_Object AS so
                            ,p.Strategic_Project AS sp
                            ,p.Faculty
                            ,p.Budget_Amount
                            ,p2.parent AS si
                            ,p2.pillar_name AS so_name 
                            ,p3.pillar_name AS si_name
                            ,p4.pillar_name AS p
                            ,k.ksp_name AS sp_name
                            ,COALESCE(pfp.Allocated_budget,'0.00') AS Allocated_budget
                            ,COALESCE(pfp.Actual_Spend_Amount,'0.00') AS Actual_Spend_Amount
                            ,f.Alias_Default
                            FROM planning_faculty_action_plan p 
                            LEFT JOIN pilars2 p2
                            ON p.Strategic_Object=p2.pillar_id
                            LEFT JOIN pilars2 p3
                            ON p2.parent=p3.pillar_id
                            LEFT JOIN pilars2 p4
                            ON p3.parent=p4.pillar_id
                            LEFT JOIN ksp k
                            ON p.Strategic_Project=k.ksp_id
                            LEFT JOIN planning_faculty_project_progress pfp
                            ON p.Strategic_Object=pfp.Strategic_Object AND p.Strategic_Project=pfp.Strategic_Project AND p.faculty=pfp.Faculty
                            LEFT JOIN Faculty f
                            ON p.Faculty=f.Faculty)


                            SELECT * FROM t1";   
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
                /* $sqlPlan = "SELECT
                            f.Alias_Default AS fa_name,
                            pfsp.* 
                            FROM planning_faculty_strategic_plan AS pfsp
                            LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default FROM Faculty) AS f ON pfsp.Faculty = f.Faculty
                            ORDER BY fa_name"; */
                $sqlPlan = "WITH t1 AS (
                            SELECT Faculty,COUNT(*) AS count_okr
                            FROM planning_faculty_strategic_plan
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
                $sqlPlan = "WITH t1 AS (
                            SELECT Faculty,COUNT(*) AS count_okr
                            FROM planning_faculty_action_plan
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


            case "report-project-summary":
                try {
                    $db = new Database();
                    $conn = $db->connect();
    
                    // เชื่อมต่อฐานข้อมูล
                    $sqlPlan = "SELECT
                                    b_actual.FISCAL_YEAR,
                                    annual_bp.Budget_Management_Year,
                                    annual_bp.Plan,
                                    annual_bp.Sub_Plan,
                                    annual_bp.Faculty,
                                    annual_bp.Project,
                                    annual_bp.Total_Amount_Quantity,
                                    f.Alias_Default AS faculty_name,
                                    account.alias_default,
                                    plan.plan_name,
                                    sub_plan.sub_plan_name,
                                    project.project_name,
                                    account.parent
                                FROM
                                    budget_planning_annual_budget_plan AS annual_bp
                                    LEFT JOIN (SELECT DISTINCT Faculty,fund,plan,subplan,project,account,service,fiscal_year from budget_planning_actual) b_actual 
                                    ON b_actual.PLAN = annual_bp.Plan
                                    AND annual_bp.faculty=b_actual.FACULTY
                                    AND b_actual.SUBPLAN = REPLACE(annual_bp.Sub_Plan, 'SP_', '')
                                    AND b_actual.PROJECT = annual_bp.Project
                                    AND annual_bp.account=b_actual.account
                                    AND b_actual.fund=REPLACE(annual_bp.fund, 'FN', '')
                                    AND b_actual.service=REPLACE(annual_bp.service, 'SR_', '')
                                    LEFT JOIN account ON account.account = annual_bp.Account
                                    LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'FACULTY%') f ON f.Faculty = annual_bp.Faculty
                                    LEFT JOIN plan ON plan.plan_id = annual_bp.Plan
                                    LEFT JOIN sub_plan ON sub_plan.sub_plan_id = annual_bp.Sub_Plan
                                    LEFT JOIN project ON project.project_id = annual_bp.Project
                                WHERE
                                    annual_bp.Scenario = 'Annual Budget Plan'
                                    AND annual_bp.Fund = 'FN06'
                                ORDER BY Faculty, plan, sub_plan, Project";
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
