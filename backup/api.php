<?php
header('Content-Type: application/json');
class Database
{
    private $host = "202.28.118.192,14333";
    private $dbname = "epm_report";
    private $username = "root";
    private $password = "TDyutdYdyudRTYDsEFOPI";
    private $conn;

    public function connect()
    {
        try {
            $this->conn = new PDO("sqlsrv:Server={$this->host};Database={$this->dbname}", $this->username, $this->password);
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
                $sqlPlan = "SELECT  pkap.*,REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,si.pillar_name AS si_name, so.pillar_name AS so_name,ksp.ksp_name , okr.okr_name
                            FROM planning_kku_action_plan AS pkap
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI')
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
        case "get_report_planing_change":
            try {
                $db = new Database();
                $conn = $db->connect();

                // KKU Action Plan/ Faculty Action Plan
                // $sqlPlan = "SELECT REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,  
                //             pkap.Strategic_Object,
                //             pkap.Strategic_Project,
                //             pkap.Faculty,
                //             pkap.Budget_Amount,
                //             pkap.Responsible_person,
                //             si.pillar_name AS si_name, 
                //             so.pillar_name AS so_name, 
                //             ksp.ksp_name,
                //             Faculty.Alias_Default As fa_name
                //             FROM planning_kku_action_plan AS pkap
                //             LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI')
                //             LEFT JOIN pilars2 AS so ON so.pillar_id = pkap.Strategic_Object
                //             LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pkap.Strategic_Project))
                //             LEFT JOIN Faculty ON Faculty.Faculty = pkap.Faculty

                //             UNION ALL SELECT REPLACE(LEFT(pkrap.Strategic_Object, CHARINDEX('-', pkrap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,  
                //             pkrap.Strategic_Object,
                //             pkrap.Strategic_Project,
                //             pkrap.Faculty,
                //             pkrap.Budget_Amount,
                //             pkrap.Responsible_person,
                //             si.pillar_name AS si_name, 
                //             so.pillar_name AS so_name, 
                //             ksp.ksp_name,
                //             Faculty.Alias_Default As fa_name
                //             FROM planning_kku_revised_action_plan AS pkrap
                //             LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pkrap.Strategic_Object, CHARINDEX('-', pkrap.Strategic_Object) - 1), 'SO', 'SI')
                //             LEFT JOIN pilars2 AS so ON so.pillar_id = pkrap.Strategic_Object
                //             LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pkrap.Strategic_Project))
                //             LEFT JOIN Faculty ON Faculty.Faculty = pkrap.Faculty

                //             UNION ALL SELECT REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,  
                //             pfap.Strategic_Object,
                //             pfap.Strategic_Project,
                //             pfap.Faculty,
                //             pfap.Budget_Amount,
                //             pfap.Responsible_person,
                //             si.pillar_name AS si_name, 
                //             so.pillar_name AS so_name, 
                //             ksp.ksp_name,
                //             f.Alias_Default As fa_name
                //             FROM planning_faculty_action_plan AS pfap
                //             LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI')
                //             LEFT JOIN pilars2 AS so ON so.pillar_id = pfap.Strategic_Object
                //             LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pfap.Strategic_Project))
                //             LEFT JOIN (
                //                                         SELECT DISTINCT Faculty, Alias_Default
                //                                         FROM Faculty
                //                                     ) AS f ON pfap.Faculty = f.Faculty

                //             UNION ALL SELECT REPLACE(LEFT(pfrap.Strategic_Object, CHARINDEX('-', pfrap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,  
                //             pfrap.Strategic_Object,
                //             pfrap.Strategic_Project,
                //             pfrap.Faculty,
                //             pfrap.Budget_Amount,
                //             pfrap.Responsible_person,
                //             si.pillar_name AS si_name, 
                //             so.pillar_name AS so_name, 
                //             ksp.ksp_name,
                //             f.Alias_Default As fa_name
                //             FROM planning_faculty_revised_action_plan AS pfrap
                //             LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pfrap.Strategic_Object, CHARINDEX('-', pfrap.Strategic_Object) - 1), 'SO', 'SI')
                //             LEFT JOIN pilars2 AS so ON so.pillar_id = pfrap.Strategic_Object
                //             LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pfrap.Strategic_Project))
                //             LEFT JOIN (
                //                                         SELECT DISTINCT Faculty, Alias_Default
                //                                         FROM Faculty
                //                                     ) AS f ON pfrap.Faculty = f.Faculty

                //             ORDER BY Faculty desc,si_code, Strategic_Object, Strategic_Project,Budget_Amount";
                $sqlPlan = "SELECT REPLACE(LEFT(pkrap.Strategic_Object, CHARINDEX('-', pkrap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,  
                                pkrap.Strategic_Object,
                                pkrap.Strategic_Project,
                                pkrap.Faculty,
                                pkrap.Budget_Amount,
                                pkrap.Responsible_person,
                                si.pillar_name AS si_name, 
                                so.pillar_name AS so_name, 
                                ksp.ksp_name,
                                Faculty.Alias_Default As fa_name
                            FROM planning_kku_revised_action_plan AS pkrap
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pkrap.Strategic_Object, CHARINDEX('-', pkrap.Strategic_Object) - 1), 'SO', 'SI')
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pkrap.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pkrap.Strategic_Project))
                            LEFT JOIN Faculty ON Faculty.Faculty = pkrap.Faculty

                            UNION ALL

                            SELECT REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,  
                                pkap.Strategic_Object,
                                pkap.Strategic_Project,
                                pkap.Faculty,
                                pkap.Budget_Amount,
                                pkap.Responsible_person,
                                si.pillar_name AS si_name, 
                                so.pillar_name AS so_name, 
                                ksp.ksp_name,
                                Faculty.Alias_Default As fa_name
                            FROM planning_kku_action_plan AS pkap
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI')
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pkap.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pkap.Strategic_Project))
                            LEFT JOIN Faculty ON Faculty.Faculty = pkap.Faculty
                            WHERE NOT EXISTS (
                                SELECT 1 FROM planning_kku_revised_action_plan WHERE planning_kku_revised_action_plan.Faculty = pkap.Faculty
                            )

                            UNION ALL

                            SELECT REPLACE(LEFT(pfrap.Strategic_Object, CHARINDEX('-', pfrap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,  
                                pfrap.Strategic_Object,
                                pfrap.Strategic_Project,
                                pfrap.Faculty,
                                pfrap.Budget_Amount,
                                pfrap.Responsible_person,
                                si.pillar_name AS si_name, 
                                so.pillar_name AS so_name, 
                                ksp.ksp_name,
                                f.Alias_Default As fa_name
                            FROM planning_faculty_revised_action_plan AS pfrap
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pfrap.Strategic_Object, CHARINDEX('-', pfrap.Strategic_Object) - 1), 'SO', 'SI')
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pfrap.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pfrap.Strategic_Project))
                            LEFT JOIN (
                                SELECT DISTINCT Faculty, Alias_Default FROM Faculty
                            ) AS f ON pfrap.Faculty = f.Faculty

                            UNION ALL

                            SELECT REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,  
                                pfap.Strategic_Object,
                                pfap.Strategic_Project,
                                pfap.Faculty,
                                pfap.Budget_Amount,
                                pfap.Responsible_person,
                                si.pillar_name AS si_name, 
                                so.pillar_name AS so_name, 
                                ksp.ksp_name,
                                f.Alias_Default As fa_name
                            FROM planning_faculty_action_plan AS pfap
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI')
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pfap.Strategic_Object
                            LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pfap.Strategic_Project))
                            LEFT JOIN (
                                SELECT DISTINCT Faculty, Alias_Default FROM Faculty
                            ) AS f ON pfap.Faculty = f.Faculty
                            WHERE NOT EXISTS (
                                SELECT 1 FROM planning_faculty_revised_action_plan WHERE planning_faculty_revised_action_plan.Faculty = pfap.Faculty
                            )

                            ORDER BY Faculty DESC, si_code, Strategic_Object, Strategic_Project, Budget_Amount;
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
        case "get_kku_planing_status":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                            pkpp.Faculty,
                            Faculty.Alias_Default AS fa_name,
                            REPLACE(LEFT(pkpp.Strategic_Object, CHARINDEX('-', pkpp.Strategic_Object) - 1), 'SO', 'SI') AS si_code,
                            si.pillar_name AS si_name,
                            pkpp.Strategic_Object,
                            pkpp.Strategic_Project,
                            ksp.ksp_name,
                            pkpp.Progress_Status,
                            pkpp.Strategic_Project_Progress_Details,
                            pkpp.Obstacles

                        FROM planning_kku_project_progress AS pkpp
                        LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pkpp.Strategic_Project))
                        LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pkpp.Strategic_Object, CHARINDEX('-', pkpp.Strategic_Object) - 1), 'SO', 'SI')
                        LEFT JOIN Faculty ON Faculty.Faculty = pkpp.Faculty

                        UNION ALL 

                        SELECT 
                            pfpp.Faculty,
                            f.Alias_Default AS fa_name,
                            REPLACE(LEFT(pfpp.Strategic_Object, CHARINDEX('-', pfpp.Strategic_Object) - 1), 'SO', 'SI') AS si_code,
                            si.pillar_name AS si_name,
                            pfpp.Strategic_Object,
                            pfpp.Strategic_Project,
                            ksp.ksp_name,
                            pfpp.Progress_Status,
                            pfpp.Strategic_Project_Progress_Details,
                            pfpp.Obstacles
                        FROM planning_faculty_project_progress AS pfpp
                        LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pfpp.Strategic_Project))
                        LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pfpp.Strategic_Object, CHARINDEX('-', pfpp.Strategic_Object) - 1), 'SO', 'SI')
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

        case "get_indicator_comparison":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT pfap.*,REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI') AS si_code,si.pillar_name AS si_name, so.pillar_name AS so_name,ksp.ksp_name , okr.okr_name,
                            f.Alias_Default AS fa_name
                            FROM planning_faculty_action_plan AS pfap
                            LEFT JOIN ksp ON ksp.ksp_id = LTRIM(RTRIM(pfap.Strategic_Project))
                            LEFT JOIN pilars2 AS si ON si.pillar_id = REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI')
                            LEFT JOIN pilars2 AS so ON so.pillar_id = pfap.Strategic_Object
                            LEFT JOIN okr ON okr.okr_id = pfap.OKR
                             LEFT JOIN (
                            SELECT DISTINCT Faculty, Alias_Default
                            FROM Faculty
                            ) AS f ON pfap.Faculty = f.Faculty
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
                                Faculty.Alias_Default AS fa_name,
                                LEFT(
                                    LEFT(pksp.Strategic_Object, CHARINDEX('-', pksp.Strategic_Object) - 1),
                                    CHARINDEX('SO', pksp.Strategic_Object) - 1
                                )
                                + 'P' +
                                SUBSTRING(
                                    LEFT(pksp.Strategic_Object, CHARINDEX('-', pksp.Strategic_Object) - 1),
                                    CHARINDEX('SO', pksp.Strategic_Object) + 2,
                                    2
                                ) AS pilar_code,
                                
                                p.pillar_name,
                                REPLACE(
                                    LEFT(pksp.Strategic_Object, CHARINDEX('-', pksp.Strategic_Object) - 1),
                                    'SO',
                                    'SI'
                                ) AS si_code,
                                
                                si.pillar_name AS si_name,
                                so.pillar_name AS so_name,
                                ksp.ksp_name,
                                okr.okr_name,
                                pkop.Quarter_Progress_Value

                            FROM planning_kku_strategic_plan AS pksp

                            LEFT JOIN Faculty 
                                ON pksp.Faculty = Faculty.Faculty

                            LEFT JOIN pilars2 AS p 
                                ON p.pillar_id = 
                                    LEFT(
                                        LEFT(pksp.Strategic_Object, CHARINDEX('-', pksp.Strategic_Object) - 1),
                                        CHARINDEX('SO', pksp.Strategic_Object) - 1
                                    )
                                    + 'P' +
                                    SUBSTRING(
                                        LEFT(pksp.Strategic_Object, CHARINDEX('-', pksp.Strategic_Object) - 1),
                                        CHARINDEX('SO', pksp.Strategic_Object) + 2,
                                        2
                                    )

                            LEFT JOIN pilars2 AS si 
                                ON si.pillar_id = REPLACE(LEFT(pksp.Strategic_Object, CHARINDEX('-', pksp.Strategic_Object) - 1), 'SO', 'SI')

                            LEFT JOIN pilars2 AS so 
                                ON so.pillar_id = pksp.Strategic_Object

                            LEFT JOIN ksp 
                                ON ksp.ksp_id = LTRIM(RTRIM(pksp.Strategic_Project))

                            LEFT JOIN okr 
                                ON okr.okr_id = pksp.OKR

                            LEFT JOIN planning_kku_okr_progress AS pkop 
                                ON pkop.OKR = pksp.OKR

                            ORDER BY pksp.Faculty, pilar_code, si_code, pksp.Strategic_Object, pksp.Strategic_Project, pkop.OKR;
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

        case "get_kku_budget_expenses":
            try {
                $db = new Database();
                $conn = $db->connect();
                $faculty = $_POST["faculty"];

                $sqlPlan = "WITH t1 AS (
                                SELECT p2.pillar_id, p2.parent, p2.pillar_name_en, p2.pillar_name
                                FROM pilars2 p2
                                JOIN planning_kku_action_plan pkap 
                                    ON p2.pillar_id LIKE LEFT(pkap.Strategic_Object, 3) + 'SI%'
                                    OR p2.pillar_id LIKE LEFT(pkap.Strategic_Object, 3) + 'P%'
                                GROUP BY p2.pillar_id, p2.parent, p2.pillar_name_en, p2.pillar_name
                            ),
                            t2 AS (
                                SELECT 
                                    REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI') AS Pillar_type,
                                    SUM(Budget_Amount) AS Budget_Amount
                                FROM planning_kku_action_plan pkap
                                GROUP BY REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI')
                            ),
                            t3 AS (
                                SELECT 
                                    REPLACE(
                                        LEFT(REPLACE(pkpp.Strategic_Object, '_', ''), CHARINDEX('-', REPLACE(pkpp.Strategic_Object, '_', '')) - 1),
                                        'SO', 'SI'
                                    ) AS Pillar_type, 
                                    SUM(pkpp.Allocated_budget) AS Allocated_budget, 
                                    SUM(pkpp.Actual_Spend_Amount) AS Actual_Spend_Amount
                                FROM planning_kku_project_progress AS pkpp
                                GROUP BY 
                                    REPLACE(
                                        LEFT(REPLACE(pkpp.Strategic_Object, '_', ''), CHARINDEX('-', REPLACE(pkpp.Strategic_Object, '_', '')) - 1),
                                        'SO', 'SI'
                                    )
                            )
                            SELECT t1.*, t2.Budget_Amount, t3.Allocated_budget, t3.Actual_Spend_Amount
                            FROM t1
                            LEFT JOIN t2 ON t1.pillar_id = t2.Pillar_type
                            LEFT JOIN t3 ON t1.pillar_id = t3.Pillar_type;";
                $stmtPlan = $conn->prepare($sqlPlan);
                // $stmtPlan->bindParam(':faculty', $faculty, PDO::PARAM_STR);
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
        case "get_fac_budget_expenses":
            try {
                $db = new Database();
                $conn = $db->connect();
                // เชื่อมต่อฐานข้อมูล

                $sqlPlan = "WITH t1 AS (
                                SELECT p2.pillar_id, p2.parent, p2.pillar_name_en, p2.pillar_name,
                                    f.Alias_Default AS fa_name
                                FROM pilars2 p2
                                JOIN planning_faculty_action_plan pfap 
                                    ON p2.pillar_id LIKE LEFT(pfap.Strategic_Object, 3) + 'SI%'
                                    OR p2.pillar_id LIKE LEFT(pfap.Strategic_Object, 3) + 'P%'
                                LEFT JOIN Faculty f ON f.Faculty = pfap.Faculty
                                GROUP BY p2.pillar_id, p2.parent, p2.pillar_name_en, p2.pillar_name, f.Alias_Default
                            ),
                            t2 AS (
                                SELECT 
                                    REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI') AS Pillar_type,
                                    SUM(Budget_Amount) AS Budget_Amount
                                FROM planning_faculty_action_plan pfap
                                GROUP BY REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI')
                            ),
                            t3 AS (
                                SELECT 
                                    REPLACE(
                                        LEFT(REPLACE(pfpp.Strategic_Object, '_', ''), CHARINDEX('-', REPLACE(pfpp.Strategic_Object, '_', '')) - 1),
                                        'SO', 'SI'
                                    ) AS Pillar_type,
                                    SUM(pfpp.Allocated_budget) AS Allocated_budget, 
                                    SUM(pfpp.Actual_Spend_Amount) AS Actual_Spend_Amount
                                FROM planning_faculty_project_progress AS pfpp
                                GROUP BY 
                                    REPLACE(
                                        LEFT(REPLACE(pfpp.Strategic_Object, '_', ''), CHARINDEX('-', REPLACE(pfpp.Strategic_Object, '_', '')) - 1),
                                        'SO', 'SI'
                                    )
                            ),
                            t4 AS (
                                SELECT p2.pillar_id, p2.parent, p2.pillar_name_en, p2.pillar_name,
                                    f.Alias_Default AS fa_name
                                FROM pilars2 p2
                                JOIN planning_kku_action_plan pkap 
                                    ON p2.pillar_id LIKE LEFT(pkap.Strategic_Object, 3) + 'SI%'
                                    OR p2.pillar_id LIKE LEFT(pkap.Strategic_Object, 3) + 'P%'
                                LEFT JOIN Faculty f ON f.Faculty = pkap.Faculty
                                GROUP BY p2.pillar_id, p2.parent, p2.pillar_name_en, p2.pillar_name, f.Alias_Default
                            ),
                            t5 AS (
                                SELECT 
                                    REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI') AS Pillar_type,
                                    SUM(Budget_Amount) AS Budget_Amount
                                FROM planning_kku_action_plan pkap
                                GROUP BY REPLACE(LEFT(pkap.Strategic_Object, CHARINDEX('-', pkap.Strategic_Object) - 1), 'SO', 'SI')
                            ),
                            t6 AS (
                                SELECT 
                                    REPLACE(
                                        LEFT(REPLACE(pkpp.Strategic_Object, '_', ''), CHARINDEX('-', REPLACE(pkpp.Strategic_Object, '_', '')) - 1),
                                        'SO', 'SI'
                                    ) AS Pillar_type,
                                    SUM(pkpp.Allocated_budget) AS Allocated_budget, 
                                    SUM(pkpp.Actual_Spend_Amount) AS Actual_Spend_Amount
                                FROM planning_kku_project_progress AS pkpp
                                GROUP BY 
                                    REPLACE(
                                        LEFT(REPLACE(pkpp.Strategic_Object, '_', ''), CHARINDEX('-', REPLACE(pkpp.Strategic_Object, '_', '')) - 1),
                                        'SO', 'SI'
                                    )
                            )

                            SELECT t1.*, t2.Budget_Amount, t3.Allocated_budget, t3.Actual_Spend_Amount, t1.fa_name
                            FROM t1
                            LEFT JOIN t2 ON t1.pillar_id = t2.Pillar_type
                            LEFT JOIN t3 ON t1.pillar_id = t3.Pillar_type

                            UNION ALL

                            SELECT t4.*, t5.Budget_Amount, t6.Allocated_budget, t6.Actual_Spend_Amount, t4.fa_name
                            FROM t4
                            LEFT JOIN t5 ON t4.pillar_id = t5.Pillar_type
                            LEFT JOIN t6 ON t4.pillar_id = t6.Pillar_type;";
                // $sqlPlan = "WITH t1 AS (
                //             SELECT p2.pillar_id,p2.parent,p2.pillar_name_en,p2.pillar_name
                //             FROM pilars2 p2
                //             JOIN planning_faculty_action_plan pfap 
                //             ON p2.pillar_id LIKE CONCAT(LEFT(pfap.Strategic_Object, 3), 'SI%')
                //             OR p2.pillar_id LIKE CONCAT(LEFT(pfap.Strategic_Object, 3), 'P%')
                //             GROUP BY p2.pillar_id ,p2.parent,p2.pillar_name_en,p2.pillar_name
                //             ),
                //             t2 AS (
                //             SELECT 
                //             REPLACE(LEFT(pfap.Strategic_Object, CHARINDEX('-', pfap.Strategic_Object) - 1), 'SO', 'SI') AS Pillar_type,
                //             SUM(Budget_Amount) AS Budget_Amount
                //             FROM planning_faculty_action_plan pfap
                //             GROUP BY Pillar_type
                //             ),
                //             t3 AS (
                //             SELECT 
                //             REPLACE(
                //             SUBSTRING_INDEX(REPLACE(pfpp.Strategic_Object, '_', ''), '-', 1), 
                //             'SO', 'SI'
                //             ) AS Pillar_type, 
                //             SUM(pfpp.Allocated_budget) AS Allocated_budget, 
                //             SUM(pfpp.Actual_Spend_Amount) AS Actual_Spend_Amount
                //             FROM planning_faculty_project_progress AS pfpp
                //             GROUP BY Pillar_type
                //             )
                //             SELECT t1.*, t2.Budget_Amount, t3.Allocated_budget, t3.Actual_Spend_Amount
                //             FROM t1
                //             LEFT JOIN t2 ON t1.pillar_id = t2.Pillar_type
                //             LEFT JOIN t3 ON t1.pillar_id = t3.Pillar_type";
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
                                LEFT(
                                    LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pkop.Strategic_Object) - 1
                                ) + 'P' +
                                SUBSTRING(
                                    LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pkop.Strategic_Object) + 2,
                                    2
                                ) AS pilar_code,

                                p.pillar_name AS pilar_name,

                                REPLACE(
                                    LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                ) AS si_code,

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

                            LEFT JOIN okr 
                                ON okr.okr_id = pkop.OKR

                            LEFT JOIN pilars2 AS so 
                                ON so.pillar_id = pkop.Strategic_Object

                            LEFT JOIN pilars2 AS p 
                                ON p.pillar_id = 
                                    LEFT(
                                        LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pkop.Strategic_Object) - 1
                                    ) + 'P' +
                                    SUBSTRING(
                                        LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pkop.Strategic_Object) + 2,
                                        2
                                    )

                            LEFT JOIN pilars2 AS si 
                                ON si.pillar_id = REPLACE(
                                    LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                )

                            LEFT JOIN planning_kku_action_plan AS pkap 
                                ON pkap.OKR = pkop.OKR

                            LEFT JOIN planning_kku_project_progress AS pkpp 
                                ON LTRIM(RTRIM(pkpp.Strategic_Project)) = LTRIM(RTRIM(pkap.Strategic_Project))

                            LEFT JOIN Faculty 
                                ON Faculty.Faculty = pkop.Faculty

                            LEFT JOIN ksp 
                                ON ksp.ksp_id = pkap.Strategic_Project

                            ORDER BY Faculty, si_code, pkop.Strategic_Object, okr.okr_id, ksp.ksp_id;";
                $stmtPlan = $conn->prepare($sqlPlan);
                $stmtPlan->execute();
                $plan = $stmtPlan->fetchAll(PDO::FETCH_ASSOC);

                $sqlQuarter = "SELECT p.OKR,p.Version,p.Quarter_Progress_Value FROM planning_kku_okr_progress as p";
                $stmtQuarter = $conn->prepare($sqlQuarter);
                $stmtQuarter->execute();
                $quarter = $stmtQuarter->fetchAll(PDO::FETCH_ASSOC);
                $conn = null;

                $response = array(
                    'plan' => $plan,
                    'quarter' => $quarter
                );
                $jsonResponse = json_encode($response);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo 'JSON encoding error: ' . json_last_error_msg();
                }
                echo json_encode($response);
            } catch (PDOException $e) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                );
                echo json_encode($response);
            }
            break;

        case "get_department_action_summary":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                                pfop.*,
                                LEFT(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pfop.Strategic_Object) - 1
                                ) + 'P' +
                                SUBSTRING(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pfop.Strategic_Object) + 2,
                                    2
                                ) AS pilar_code,

                                p.pillar_name AS pilar_name,

                                REPLACE(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                ) AS si_code,

                                si.pillar_name AS si_name,
                                pfop.Strategic_Object,
                                so.pillar_name AS so_name,
                                pfop.OKR,
                                okr.okr_name,
                                pfap.Target_OKR_Objective_and_Key_Result,
                                pfap.UOM,
                                pfap.Budget_Amount,
                                ISNULL(pfpp.Allocated_budget, 0) AS Allocated_budget,
                                ISNULL(pfpp.Actual_Spend_Amount, 0) AS Actual_Spend_Amount,
                                pfap.Responsible_person,
                                f.Alias_Default AS fa_name,
                                ksp.ksp_name

                            FROM planning_faculty_okr_progress AS pfop

                            LEFT JOIN okr 
                                ON okr.okr_id = pfop.OKR

                            LEFT JOIN pilars2 AS so 
                                ON so.pillar_id = pfop.Strategic_Object

                            LEFT JOIN pilars2 AS p 
                                ON p.pillar_id = 
                                    LEFT(
                                        LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pfop.Strategic_Object) - 1
                                    ) + 'P' +
                                    SUBSTRING(
                                        LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pfop.Strategic_Object) + 2,
                                        2
                                    )

                            LEFT JOIN pilars2 AS si 
                                ON si.pillar_id = REPLACE(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                )

                            LEFT JOIN planning_faculty_action_plan AS pfap 
                                ON pfap.OKR = pfop.OKR

                            LEFT JOIN planning_faculty_project_progress AS pfpp 
                                ON LTRIM(RTRIM(pfpp.Strategic_Project)) = LTRIM(RTRIM(pfap.Strategic_Project))

                            LEFT JOIN (
                                SELECT DISTINCT Faculty, Alias_Default
                                FROM Faculty
                            ) AS f 
                                ON pfop.Faculty = f.Faculty

                            LEFT JOIN ksp 
                                ON ksp.ksp_id = pfap.Strategic_Project

                            ORDER BY pfop.Faculty, si_code, pfop.Strategic_Object, okr.okr_id, ksp.ksp_id;";
                $stmtPlan = $conn->prepare($sqlPlan);
                $stmtPlan->execute();
                $plan = $stmtPlan->fetchAll(PDO::FETCH_ASSOC);


                $sqlQuarter = "SELECT p.OKR,p.Version,p.Quarter_Progress_Value FROM planning_faculty_okr_progress as p";
                $stmtQuarter = $conn->prepare($sqlQuarter);
                $stmtQuarter->execute();
                $quarter = $stmtQuarter->fetchAll(PDO::FETCH_ASSOC);
                $conn = null;

                $response = array(
                    'plan' => $plan,
                    'quarter' => $quarter
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

                                LEFT(
                                    LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pkop.Strategic_Object) - 1
                                ) + 'P' +
                                SUBSTRING(
                                    LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pkop.Strategic_Object) + 2,
                                    2
                                ) AS pilar_code,

                                p.pillar_name AS pilar_name,

                                REPLACE(
                                    LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                ) AS si_code,

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

                            LEFT JOIN okr 
                                ON okr.okr_id = pkop.OKR

                            LEFT JOIN pilars2 AS so 
                                ON so.pillar_id = pkop.Strategic_Object

                            LEFT JOIN pilars2 AS p 
                                ON p.pillar_id = 
                                    LEFT(
                                        LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pkop.Strategic_Object) - 1
                                    ) + 'P' +
                                    SUBSTRING(
                                        LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pkop.Strategic_Object) + 2,
                                        2
                                    )

                            LEFT JOIN pilars2 AS si 
                                ON si.pillar_id = REPLACE(
                                    LEFT(pkop.Strategic_Object, CHARINDEX('-', pkop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                )

                            LEFT JOIN planning_kku_action_plan AS pkap 
                                ON pkap.OKR = pkop.OKR

                            LEFT JOIN planning_kku_project_progress AS pkpp 
                                ON LTRIM(RTRIM(pkpp.Strategic_Project)) = LTRIM(RTRIM(pkap.Strategic_Project))

                            LEFT JOIN Faculty 
                                ON Faculty.Faculty = pkop.Faculty

                            LEFT JOIN ksp 
                                ON ksp.ksp_id = pkap.Strategic_Project

                            ORDER BY Faculty, si_code, pkop.Strategic_Object, okr.okr_id, ksp.ksp_id;";
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

                                LEFT(
                                    LEFT(pfsp.Strategic_Object, CHARINDEX('-', pfsp.Strategic_Object) - 1),
                                    CHARINDEX('SO', pfsp.Strategic_Object) - 1
                                ) + 'P' +
                                SUBSTRING(
                                    LEFT(pfsp.Strategic_Object, CHARINDEX('-', pfsp.Strategic_Object) - 1),
                                    CHARINDEX('SO', pfsp.Strategic_Object) + 2,
                                    2
                                ) AS pilar_code,

                                p.pillar_name AS pilar_name,

                                REPLACE(
                                    LEFT(pfsp.Strategic_Object, CHARINDEX('-', pfsp.Strategic_Object) - 1),
                                    'SO', 'SI'
                                ) AS si_code,

                                si.pillar_name AS si_name,
                                so.pillar_name AS so_name,
                                ksp.ksp_name,
                                okr.okr_name,
                                pfop.Quarter_Progress_Value

                            FROM planning_faculty_strategic_plan AS pfsp

                            LEFT JOIN (
                                SELECT DISTINCT Faculty, Alias_Default FROM Faculty
                            ) AS f ON pfsp.Faculty = f.Faculty

                            LEFT JOIN pilars2 AS so ON so.pillar_id = pfsp.Strategic_Object

                            LEFT JOIN pilars2 AS p 
                                ON p.pillar_id = 
                                    LEFT(
                                        LEFT(pfsp.Strategic_Object, CHARINDEX('-', pfsp.Strategic_Object) - 1),
                                        CHARINDEX('SO', pfsp.Strategic_Object) - 1
                                    ) + 'P' +
                                    SUBSTRING(
                                        LEFT(pfsp.Strategic_Object, CHARINDEX('-', pfsp.Strategic_Object) - 1),
                                        CHARINDEX('SO', pfsp.Strategic_Object) + 2,
                                        2
                                    )

                            LEFT JOIN pilars2 AS si 
                                ON si.pillar_id = REPLACE(
                                    LEFT(pfsp.Strategic_Object, CHARINDEX('-', pfsp.Strategic_Object) - 1),
                                    'SO', 'SI'
                                )

                            LEFT JOIN ksp ON ksp.ksp_id = pfsp.Strategic_Project
                            LEFT JOIN okr ON okr.okr_id = pfsp.OKR
                            LEFT JOIN planning_faculty_okr_progress AS pfop ON pfop.OKR = pfsp.OKR

                            ORDER BY pfsp.Faculty, pilar_code, si_code, pfsp.Strategic_Object, pfsp.Strategic_Project, pfop.OKR;";
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
                $sql = "SELECT DISTINCT f.Alias_Default AS faculty ,b.Faculty as fcode
                        FROM planning_faculty_strategic_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE Parent LIKE 'Faculty%') f
                        ON b.Faculty=f.Faculty";

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
                $sqlPlan = "WITH t1 AS (
                            SELECT Faculty,COUNT(*) AS count_okr
                            FROM planning_faculty_strategic_plan
                            where Faculty=:faculty
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
                            ON t.Faculty=tt.Faculty
                            LEFT JOIN t5 ttt
                            ON t.Faculty=tt.Faculty
                            LEFT JOIN Faculty f
                            ON t.Faculty = f.Faculty)
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
                            where Faculty=:faculty
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
                            ON t.Faculty=tt.Faculty
                            LEFT JOIN t5 ttt
                            ON t.Faculty=tt.Faculty
                            LEFT JOIN Faculty f
                            ON t.Faculty = f.Faculty)
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
        case "get_department-action-summary":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sqlPlan = "SELECT 
                                pfop.*,

                                LEFT(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pfop.Strategic_Object) - 1
                                ) + 'P' +
                                SUBSTRING(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pfop.Strategic_Object) + 2,
                                    2
                                ) AS pilar_code,

                                p.pilar_name, -- ตรวจสอบว่า pilar_name สะกดถูกเป็น pillar_name หรือไม่

                                REPLACE(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                ) AS si_code,

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

                            LEFT JOIN okr 
                                ON okr.okr_id = pfop.OKR

                            LEFT JOIN pilar AS so 
                                ON so.pilar_id = pfop.Strategic_Object

                            LEFT JOIN pilar AS p 
                                ON p.pilar_id = 
                                    LEFT(
                                        LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pfop.Strategic_Object) - 1
                                    ) + 'P' +
                                    SUBSTRING(
                                        LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pfop.Strategic_Object) + 2,
                                        2
                                    )

                            LEFT JOIN pilar AS si 
                                ON si.pilar_id = REPLACE(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                )

                            LEFT JOIN planning_faculty_action_plan AS pfap 
                                ON pfap.OKR = pfop.OKR

                            LEFT JOIN planning_faculty_project_progress AS pfpp 
                                ON LTRIM(RTRIM(pfpp.Strategic_Project)) = LTRIM(RTRIM(pfap.Strategic_Project))

                            LEFT JOIN Faculty 
                                ON Faculty.Faculty = pfop.Faculty

                            LEFT JOIN ksp 
                                ON ksp.ksp_id = pfap.Strategic_Project

                            ORDER BY pfop.Faculty, si_code, pfop.Strategic_Object, okr.okr_id, ksp.ksp_id;";
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

                                LEFT(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pfop.Strategic_Object) - 1
                                ) + 'P' +
                                SUBSTRING(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    CHARINDEX('SO', pfop.Strategic_Object) + 2,
                                    2
                                ) AS pilar_code,

                                p.pillar_name AS pilar_name,

                                REPLACE(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                ) AS si_code,

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

                            LEFT JOIN okr 
                                ON okr.okr_id = pfop.OKR

                            LEFT JOIN pilars2 AS so 
                                ON so.pillar_id = pfop.Strategic_Object

                            LEFT JOIN pilars2 AS p 
                                ON p.pillar_id = 
                                    LEFT(
                                        LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pfop.Strategic_Object) - 1
                                    ) + 'P' +
                                    SUBSTRING(
                                        LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                        CHARINDEX('SO', pfop.Strategic_Object) + 2,
                                        2
                                    )

                            LEFT JOIN pilars2 AS si 
                                ON si.pillar_id = REPLACE(
                                    LEFT(pfop.Strategic_Object, CHARINDEX('-', pfop.Strategic_Object) - 1),
                                    'SO', 'SI'
                                )

                            LEFT JOIN planning_faculty_action_plan AS pfap 
                                ON pfap.OKR = pfop.OKR

                            LEFT JOIN planning_faculty_project_progress AS pfpp 
                                ON LTRIM(RTRIM(pfpp.Strategic_Project)) = LTRIM(RTRIM(pfap.Strategic_Project))

                            LEFT JOIN (
                                SELECT DISTINCT Faculty, Alias_Default FROM Faculty
                            ) AS Faculty 
                                ON Faculty.Faculty = pfop.Faculty

                            LEFT JOIN ksp 
                                ON ksp.ksp_id = pfap.Strategic_Project

                            ORDER BY pfop.Faculty, si_code, pfop.Strategic_Object, okr.okr_id, ksp.ksp_id;";
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
                                    [plan].plan_name
                                FROM
                                    budget_planning_annual_budget_plan abp
                                    LEFT JOIN account acc ON abp.[Account] COLLATE Thai_CI_AS = acc.[account]
                                    LEFT JOIN project pj ON pj.project_id = abp.Project
                                    LEFT JOIN sub_plan sp ON sp.sub_plan_id = abp.Sub_Plan
                                    LEFT JOIN [plan] ON [plan].plan_id = abp.[Plan]
                                WHERE
                                    abp.Budget_Management_Year = :yearselect
                                    AND acc.parent LIKE '4%';";

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
                                [plan].plan_name,
                                sp.sub_plan_name,
                                pj.project_name,
                                acc.alias_default,
                                acc.[type],
                                abp.KKU_Item_Name,
                                abp.Fund,
                                abp.Total_Amount_Quantity,
                                Faculty.Faculty,
                                Faculty.Alias_Default,
                                (
                                    SELECT TOP 1
                                        Faculty_Parent.Alias_Default
                                    FROM
                                        Faculty Faculty_Parent
                                    WHERE
                                        Faculty_Parent.Faculty COLLATE DATABASE_DEFAULT = 
                                            (LEFT(Faculty.Faculty COLLATE DATABASE_DEFAULT, 2) + '000') COLLATE DATABASE_DEFAULT
                                ) AS Alias_Default_Parent

                            FROM
                                budget_planning_annual_budget_plan abp
                                LEFT JOIN account acc ON abp.[Account] COLLATE DATABASE_DEFAULT = acc.[account] COLLATE DATABASE_DEFAULT
                                LEFT JOIN [plan] ON abp.[Plan] = [plan].plan_id
                                LEFT JOIN sub_plan sp ON sp.sub_plan_id = abp.Sub_Plan
                                LEFT JOIN project pj ON pj.project_id = abp.Project
                                LEFT JOIN Faculty ON Faculty.Faculty = abp.Faculty

                            WHERE
                                abp.Fund IN ('FN06', 'FN02', 'FN08')
                                AND acc.[type] LIKE '%ค่าใช้จ่าย%'
                                AND abp.Budget_Management_Year = :yearselect;";

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
                $sql = "SELECT DISTINCT f.Alias_Default AS faculty ,b.Faculty as fcode
                        FROM planning_faculty_action_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE Parent LIKE 'Faculty%') f
                        ON b.Faculty=f.Faculty";

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
                            acc.[type],
                            acc.sub_type,
                            bpanbp.Service,
                            bpanbp.Account,
                            bpanbp.Faculty,
                            bpanbp.[Plan],
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
                                SELECT TOP 1
                                    Faculty_Parent.Alias_Default
                                FROM Faculty Faculty_Parent
                                WHERE Faculty_Parent.Faculty COLLATE DATABASE_DEFAULT = 
                                    (LEFT(f.Faculty COLLATE DATABASE_DEFAULT, 2) + '000') COLLATE DATABASE_DEFAULT
                            ) AS Alias_Default_Parent,

                            p.plan_name AS Plan_Name,
                            sp.sub_plan_name AS Sub_Plan_Name,
                            pr.project_name AS Project_Name

                        FROM budget_planning_allocated_annual_budget_plan bpanbp

                        LEFT JOIN (
                            SELECT DISTINCT Account, [Plan], Sub_Plan, Project, Total_Amount_Quantity, Fund
                            FROM budget_planning_annual_budget_plan
                        ) AS bpabp
                            ON bpanbp.Account = bpabp.Account
                            AND bpanbp.[Plan] = bpabp.[Plan]
                            AND bpanbp.Sub_Plan = bpabp.Sub_Plan
                            AND bpanbp.Project = bpabp.Project

                        LEFT JOIN (
                            SELECT DISTINCT [Plan], Sub_Plan, Faculty, UoM_for_Sub_plan_KPI, Sub_plan_KPI_Name
                            FROM budget_planning_subplan_kpi
                        ) AS sp_kpi
                            ON bpanbp.[Plan] = sp_kpi.[Plan]
                            AND bpanbp.Sub_Plan = sp_kpi.Sub_Plan
                            AND bpanbp.Faculty = sp_kpi.Faculty

                        LEFT JOIN (
                            SELECT DISTINCT Faculty, Project, UoM_for_Proj_KPI, Proj_KPI_Name
                            FROM budget_planning_project_kpi
                        ) AS pj_kpi
                            ON bpanbp.Faculty = pj_kpi.Faculty
                            AND bpanbp.Project = pj_kpi.Project

                        LEFT JOIN account acc ON bpanbp.Account = acc.account
                        LEFT JOIN Faculty f ON bpanbp.Faculty = f.Faculty
                        LEFT JOIN [plan] p ON bpanbp.[Plan] = p.plan_id
                        LEFT JOIN sub_plan sp ON bpanbp.Sub_Plan = sp.sub_plan_id
                        LEFT JOIN project pr ON bpanbp.Project = pr.project_id

                        WHERE acc.[type] LIKE N'%ค่าใช้จ่าย%';";

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
