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
                $sql = "SELECT f.Alias_Default,w.All_PositionTypes,w.Position,w.WF,w.Current_HC_of_the_Position
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
        case "kku_wf_approval-requests":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT * FROM workforce_new_position_request";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $wf = $cmd->fetchAll(PDO::FETCH_ASSOC);                     
                $conn = null;

                $response = array(
                    'wf' => $wf,
                    
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
        case "kku_wf_annual-allocation":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT w.*,f.Alias_Default FROM workforce_new_position_request w
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
        case "kku_wf_4year-framework":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH position_summary AS (
                        SELECT 
                            Faculty,
                            'บริหาร' AS TYPE1,
                            'วิชาการ' AS TYPE2,
                            'วิจัย' AS TYPE3,
                            'สนับสนุน' AS TYPE4,
                            -- Type 1 (บริหาร) headcounts
                            SUM(CASE WHEN All_PositionTypes = 'บริหาร' THEN Year_1_Headcount ELSE 0 END) AS TYPE1_year1,
                            SUM(CASE WHEN All_PositionTypes = 'บริหาร' THEN Year_2_Headcount ELSE 0 END) AS TYPE1_year2,
                            SUM(CASE WHEN All_PositionTypes = 'บริหาร' THEN Year_3_Headcount ELSE 0 END) AS TYPE1_year3,
                            SUM(CASE WHEN All_PositionTypes = 'บริหาร' THEN Year_4_Headcount ELSE 0 END) AS TYPE1_year4,
                            -- Type 2 (วิชาการ) headcounts
                            SUM(CASE WHEN All_PositionTypes = 'วิชาการ' THEN Year_1_Headcount ELSE 0 END) AS TYPE2_year1,
                            SUM(CASE WHEN All_PositionTypes = 'วิชาการ' THEN Year_2_Headcount ELSE 0 END) AS TYPE2_year2,
                            SUM(CASE WHEN All_PositionTypes = 'วิชาการ' THEN Year_3_Headcount ELSE 0 END) AS TYPE2_year3,
                            SUM(CASE WHEN All_PositionTypes = 'วิชาการ' THEN Year_4_Headcount ELSE 0 END) AS TYPE2_year4,
                            -- Type 3 (วิจัย) headcounts
                            SUM(CASE WHEN All_PositionTypes = 'วิจัย' THEN Year_1_Headcount ELSE 0 END) AS TYPE3_year1,
                            SUM(CASE WHEN All_PositionTypes = 'วิจัย' THEN Year_2_Headcount ELSE 0 END) AS TYPE3_year2,
                            SUM(CASE WHEN All_PositionTypes = 'วิจัย' THEN Year_3_Headcount ELSE 0 END) AS TYPE3_year3,
                            SUM(CASE WHEN All_PositionTypes = 'วิจัย' THEN Year_4_Headcount ELSE 0 END) AS TYPE3_year4,
                            -- Type 4 (สนับสนุน) headcounts
                            SUM(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN Year_1_Headcount ELSE 0 END) AS TYPE4_year1,
                            SUM(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN Year_2_Headcount ELSE 0 END) AS TYPE4_year2,
                            SUM(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN Year_3_Headcount ELSE 0 END) AS TYPE4_year3,
                            SUM(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN Year_4_Headcount ELSE 0 END) AS TYPE4_year4
                        FROM workforce_4year_plan
                        GROUP BY Faculty
                    ),

                    actual_counts AS (
                        SELECT 
                            Faculty,
                            SUM(CASE WHEN All_Position_Types = 'บริหาร' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type1,
                            SUM(CASE WHEN All_Position_Types = 'วิชาการ' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type2,
                            SUM(CASE WHEN All_Position_Types = 'วิจัย' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type3,
                            SUM(CASE WHEN All_Position_Types = 'สนับสนุน' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type4
                        FROM (
                            SELECT 
                                Faculty,
                                All_Position_Types,
                                COUNT(*) AS count_staff
                            FROM actual_data_2
                            WHERE All_Position_Types IS NOT NULL
                            GROUP BY Faculty, All_Position_Types
                        ) staff_counts
                        GROUP BY Faculty
                    )

                    SELECT 
                        ps.Faculty,
                        ps.TYPE1,
                        ps.TYPE2,
                        ps.TYPE3,
                        ps.TYPE4,
                        -- Planned headcounts by type and year
                        SUM(ps.TYPE1_year1) AS TYPE1_year1,
                        SUM(ps.TYPE1_year2) AS TYPE1_year2,
                        SUM(ps.TYPE1_year3) AS TYPE1_year3,
                        SUM(ps.TYPE1_year4) AS TYPE1_year4,
                        SUM(ps.TYPE2_year1) AS TYPE2_year1,
                        SUM(ps.TYPE2_year2) AS TYPE2_year2,
                        SUM(ps.TYPE2_year3) AS TYPE2_year3,
                        SUM(ps.TYPE2_year4) AS TYPE2_year4,
                        SUM(ps.TYPE3_year1) AS TYPE3_year1,
                        SUM(ps.TYPE3_year2) AS TYPE3_year2,
                        SUM(ps.TYPE3_year3) AS TYPE3_year3,
                        SUM(ps.TYPE3_year4) AS TYPE3_year4,
                        SUM(ps.TYPE4_year1) AS TYPE4_year1,
                        SUM(ps.TYPE4_year2) AS TYPE4_year2,
                        SUM(ps.TYPE4_year3) AS TYPE4_year3,
                        SUM(ps.TYPE4_year4) AS TYPE4_year4,
                        -- Actual counts
                        ac.Actual_type1,
                        ac.Actual_type2,
                        ac.Actual_type3,
                        ac.Actual_type4,
                        f.Alias_Default
                    FROM position_summary ps
                    LEFT JOIN actual_counts ac ON ps.Faculty = ac.Faculty COLLATE utf8mb4_general_ci
                    LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default 
                        FROM Faculty
                    ) f ON ps.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                    GROUP BY 
                        ps.Faculty,
                        ps.TYPE1,
                        ps.TYPE2,
                        ps.TYPE3,
                        ps.TYPE4,
                        f.Alias_Default,
                        ac.Actual_type1,
                        ac.Actual_type2,
                        ac.Actual_type3,
                        ac.Actual_type4
                    ORDER BY ps.Faculty";
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
        case "kku_wf_budget-framework":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH actual_data_2 AS(
                        SELECT Faculty
                        ,all_position_types
                        ,sum(salary_rate) AS salary_rate
                        ,COUNT(*) AS num_position_types
                        ,fund_ft
                        FROM actual_data_2
                        WHERE all_position_types IS NOT NULL and (fund_ft ='เงินงบประมาณ' OR fund_ft='เงินรายได้') AND Faculty !='00000'
                        GROUP BY Faculty
                        ,all_position_types
                        ,fund_ft
                        ORDER BY Faculty)

                        SELECT f.Alias_Default
                        ,sum(case when all_position_types='บริหาร' AND fund_ft='เงินงบประมาณ' then salary_rate ELSE 0 END) AS TYPE1_fund1
                        ,sum(case when all_position_types='บริหาร' AND fund_ft='เงินรายได้' then salary_rate ELSE 0 END) AS TYPE1_fund2
                        ,sum(case when all_position_types='วิชาการ' AND fund_ft='เงินงบประมาณ' then salary_rate ELSE 0 END) AS TYPE2_fund1
                        ,sum(case when all_position_types='วิชาการ' AND fund_ft='เงินรายได้' then salary_rate ELSE 0 END) AS TYPE2_fund2
                        ,sum(case when all_position_types='วิจัย' AND fund_ft='เงินงบประมาณ' then salary_rate ELSE 0 END) AS TYPE3_fund1
                        ,sum(case when all_position_types='วิจัย' AND fund_ft='เงินรายได้' then salary_rate ELSE 0 END) AS TYPE3_fund2
                        ,sum(case when all_position_types='สนับสนุน' AND fund_ft='เงินงบประมาณ' then salary_rate ELSE 0 END) AS TYPE4_fund1
                        ,sum(case when all_position_types='สนับสนุน' AND fund_ft='เงินรายได้' then salary_rate ELSE 0 END) AS TYPE4_fund2

                        ,sum(case when all_position_types='บริหาร' AND fund_ft='เงินงบประมาณ' then num_position_types ELSE 0 END) AS TYPE1_fund1_num
                        ,sum(case when all_position_types='บริหาร' AND fund_ft='เงินรายได้' then num_position_types ELSE 0 END) AS TYPE1_fund2_num
                        ,sum(case when all_position_types='วิชาการ' AND fund_ft='เงินงบประมาณ' then num_position_types ELSE 0 END) AS TYPE2_fund1_num
                        ,sum(case when all_position_types='วิชาการ' AND fund_ft='เงินรายได้' then num_position_types ELSE 0 END) AS TYPE2_fund2_num
                        ,sum(case when all_position_types='วิจัย' AND fund_ft='เงินงบประมาณ' then num_position_types ELSE 0 END) AS TYPE3_fund1_num
                        ,sum(case when all_position_types='วิจัย' AND fund_ft='เงินรายได้' then num_position_types ELSE 0 END) AS TYPE3_fund2_num
                        ,sum(case when all_position_types='สนับสนุน' AND fund_ft='เงินงบประมาณ' then num_position_types ELSE 0 END) AS TYPE4_fund1_num
                        ,sum(case when all_position_types='สนับสนุน' AND fund_ft='เงินรายได้' then num_position_types ELSE 0 END) AS TYPE4_fund2_num
                        FROM actual_data_2 ad
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default 
                        FROM Faculty
                        ) f ON ad.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        GROUP BY f.Alias_Default
                        ORDER BY f.Alias_Default

                        ";
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