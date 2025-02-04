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
        case "kku_wf_unit-personnel":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH ad1 AS (
                        SELECT 
                            faculty,
                            Personnel_Type,
                            all_position_types,
                            rate_status,
                            fund_ft,
                            COUNT(*) AS total_person
                        FROM actual_data_2
                        WHERE 
                            Faculty != '00000' 
                            AND all_position_types IS NOT NULL 
                            AND (fund_ft = 'เงินงบประมาณ' OR fund_ft = 'เงินรายได้') 
                            AND Personnel_Type = 'พนักงานมหาวิทยาลัย'
                        GROUP BY 
                            faculty, Personnel_Type, all_position_types, rate_status, fund_ft
                        ORDER BY Faculty
                    ),
                    ad2 AS (
                        SELECT 
                            faculty,
                            Personnel_Type,
                            all_position_types,
                            rate_status,
                            '' AS fund_ft,
                            COUNT(*) AS total_person
                        FROM actual_data_2
                        WHERE 
                            Faculty != '00000' 
                            AND all_position_types IS NOT NULL 
                            AND (fund_ft = 'เงินงบประมาณ' OR fund_ft = 'เงินรายได้') 
                            AND Personnel_Type != 'พนักงานมหาวิทยาลัย'
                        GROUP BY 
                            faculty, Personnel_Type, all_position_types, rate_status
                        ORDER BY Faculty
                    ), t1 AS(
                    SELECT * FROM ad1
                    UNION ALL
                    SELECT * FROM ad2)

                    SELECT f.Alias_Default
                    ,sum(case when t1.Personnel_Type='ข้าราชการ'AND t1.all_position_types='วิชาการ' then total_person ELSE 0 END) AS c1
                    ,sum(case when t1.Personnel_Type='ข้าราชการ'AND t1.all_position_types='สนับนสุน' then total_person ELSE 0 END) AS c2
                    ,sum(case when t1.Personnel_Type='ลูกจ้างประจำ'AND t1.all_position_types='สนับสนุน' then total_person ELSE 0 END) AS c3
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินงบประมาณ' AND t1.all_position_types='บริหาร' then t1.total_person ELSE 0 END) AS c4
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินงบประมาณ' AND t1.all_position_types='วิชาการ'AND t1.rate_status='คนครอง' then t1.total_person ELSE 0 END) AS c5
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินงบประมาณ' AND t1.all_position_types='วิชาการ'AND t1.rate_status='อัตราว่าง' then t1.total_person ELSE 0 END) AS c6
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินงบประมาณ' AND t1.all_position_types='วิจัย'AND t1.rate_status='คนครอง' then t1.total_person ELSE 0 END) AS c7
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินงบประมาณ' AND t1.all_position_types='วิจัย'AND t1.rate_status='อัตราว่าง' then t1.total_person ELSE 0 END) AS c8
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินงบประมาณ' AND t1.all_position_types='สนับสนุน'AND t1.rate_status='คนครอง' then t1.total_person ELSE 0 END) AS c9
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินงบประมาณ' AND t1.all_position_types='สนับสนุน'AND t1.rate_status='อัตราว่าง' then t1.total_person ELSE 0 END) AS c10
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินรายได้' AND t1.all_position_types='บริหาร' then t1.total_person ELSE 0 END) AS c11
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินรายได้' AND t1.all_position_types='วิชาการ'AND t1.rate_status='คนครอง' then t1.total_person ELSE 0 END) AS c12
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินรายได้' AND t1.all_position_types='วิชาการ'AND t1.rate_status='อัตราว่าง' then t1.total_person ELSE 0 END) AS c13
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินรายได้' AND t1.all_position_types='วิจัย'AND t1.rate_status='คนครอง' then t1.total_person ELSE 0 END) AS c14
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินรายได้' AND t1.all_position_types='วิจัย'AND t1.rate_status='อัตราว่าง' then t1.total_person ELSE 0 END) AS c15
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินรายได้' AND t1.all_position_types='สนับสนุน'AND t1.rate_status='คนครอง' then t1.total_person ELSE 0 END) AS c16
                    ,sum(case when t1.Personnel_Type='พนักงานมหาวิทยาลัย'AND t1.fund_ft='เงินรายได้' AND t1.all_position_types='สนับสนุน'AND t1.rate_status='อัตราว่าง' then t1.total_person ELSE 0 END) AS c17
                    ,sum(case when t1.Personnel_Type='ลูกจ้างของมหาวิทยาลัย'AND t1.all_position_types='วิจัย' AND t1.rate_status='คนครอง'then t1.total_person ELSE 0 END) AS c18
                    ,sum(case when t1.Personnel_Type='ลูกจ้างของมหาวิทยาลัย'AND t1.all_position_types='วิจัย' AND t1.rate_status='อัตราว่าง'then t1.total_person ELSE 0 END) AS c19
                    ,sum(case when t1.Personnel_Type='ลูกจ้างของมหาวิทยาลัย'AND t1.all_position_types='สนับสนุน' AND t1.rate_status='คนครอง'then t1.total_person ELSE 0 END) AS c20
                    ,sum(case when t1.Personnel_Type='ลูกจ้างของมหาวิทยาลัย'AND t1.all_position_types='สนับสนุน' AND t1.rate_status='อัตราว่าง'then t1.total_person ELSE 0 END) AS c21
                    FROM t1
                    LEFT JOIN (
                    SELECT DISTINCT Faculty, Alias_Default 
                    FROM Faculty
                    ) f ON t1.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                    GROUP BY f.Alias_Default
                    ORDER BY f.Alias_Default";
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
        case "kku_wf_vacant-personnel":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH ad2 AS (
                        SELECT Faculty 
                        ,personnel_type
                        ,all_position_types
                        ,POSITION
                        ,position_number 
                        FROM actual_data_2
                        WHERE rate_status='อัตราว่าง')

                        SELECT distinct act2.Faculty 
                        ,act2.personnel_type
                        ,act2.all_position_types
                        ,act2.POSITION
                        ,act2.position_number
                        ,act1.Personnel_Group
                        ,act1.Job_Family
                        ,act5.Location_Code
                        ,act4.Vacant_From_Which_Date
                        ,act4.Reason_For_Vacancy
                        ,act4.V_For_6_Months_On
                        FROM ad2 act2
                        LEFT JOIN actual_data_1 act1
                        ON act2.position_number=act1.Position_Number
                        LEFT JOIN actual_data_5 act5
                        ON act2.position_number=act5.Position_Number
                        LEFT JOIN actual_data_4 act4
                        ON act2.position_number=act4.Position_Number
                        order BY act2.position_number";
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
        case "kku_wf_retirement":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT distinct COALESCE(f.Alias_Default,act1.Faculty) AS faculty
                        ,act1.Personnel_Type
                        ,act1.Workers_Name_Surname
                        ,act1.`Position`
                        ,act1.Position_Number
                        ,act1.All_PositionTypes
                        ,act1.Job_Family
                        ,act4.Retirement_Date
                        FROM actual_data_1 act1
                        LEFT JOIN actual_data_4 act4
                        ON act1.Position_Number=act4.Position_Number
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default 
                        FROM Faculty
                        ) f ON act1.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        where act1.Faculty!='00000'
                        ORDER BY COALESCE(f.Alias_Default,act1.Faculty)";
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
        case "kku_wf_overview-framework":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH CURRENT AS (
                        SELECT 'อัตราเดิม' COLLATE utf8mb4_general_ci AS TYPE 
                        ,Personnel_Type COLLATE UTF8MB4_GENERAL_CI AS Personnel_Type
                        ,Faculty COLLATE UTF8MB4_GENERAL_CI AS Faculty
                        ,All_PositionTypes COLLATE UTF8MB4_GENERAL_CI AS All_PositionTypes
                        ,POSITION COLLATE UTF8MB4_GENERAL_CI AS POSITION
                        ,Position_Number COLLATE UTF8MB4_GENERAL_CI AS Position_Number
                        ,Fund_FT COLLATE UTF8MB4_GENERAL_CI AS Fund_FT
                        ,Salary_rate COLLATE UTF8MB4_GENERAL_CI AS Salary_rate
                        ,Govt_Fund COLLATE UTF8MB4_GENERAL_CI AS Govt_Fund
                        ,Division_Revenue COLLATE UTF8MB4_GENERAL_CI AS Division_Revenue
                        ,OOP_Central_Revenue COLLATE UTF8MB4_GENERAL_CI AS OOP_Central_Revenue
                        FROM workforce_current_positions_allocation)
                        , NEW AS (
                        SELECT 'อัตราใหม่'AS TYPE 
                        ,Personnel_Type
                        ,Faculty
                        ,All_PositionTypes
                        ,POSITION 
                        ,NULL AS Position_Number
                        ,Fund_FT
                        ,NULL AS Salary_rate
                        ,Govt_Fund
                        ,Division_Revenue
                        ,OOP_Central_Revenue
                        FROM workforce_new_positions_allocation)
                        , all_data AS (
                        SELECT * FROM CURRENT
                        UNION ALL 
                        SELECT * FROM NEW)

                        SELECT a.*
                        ,f.Alias_Default
                        ,act1.Employment_Type
                        ,act1.Workers_Name_Surname
                        ,act1.Personnel_Group
                        ,act1.Job_Family
                        ,act1.Position_Qualifications 
                        ,act1.Contract_Type
                        ,act1.Contract_Period_Short_Term
                        ,act5.Location_Code
                        FROM all_data a
                        LEFT JOIN actual_data_1 act1
                        ON a.Position_Number=act1.Position_Number
                        LEFT JOIN actual_data_5 act5
                        ON a.Position_Number=act5.Position_Number
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default 
                        FROM Faculty
                        ) f ON a.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        ORDER BY a.Faculty";
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
        case "kku_wf_annual-allocation":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH CURRENT AS (
                        SELECT 'อัตราเดิม' COLLATE utf8mb4_general_ci AS TYPE 
                        ,Personnel_Type COLLATE UTF8MB4_GENERAL_CI AS Personnel_Type
                        ,Faculty COLLATE UTF8MB4_GENERAL_CI AS Faculty
                        ,All_PositionTypes COLLATE UTF8MB4_GENERAL_CI AS All_PositionTypes
                        ,POSITION COLLATE UTF8MB4_GENERAL_CI AS POSITION
                        ,Position_Number COLLATE UTF8MB4_GENERAL_CI AS Position_Number
                        ,Fund_FT COLLATE UTF8MB4_GENERAL_CI AS Fund_FT
                        ,Salary_rate COLLATE UTF8MB4_GENERAL_CI AS Salary_rate
                        ,Govt_Fund COLLATE UTF8MB4_GENERAL_CI AS Govt_Fund
                        ,Division_Revenue COLLATE UTF8MB4_GENERAL_CI AS Division_Revenue
                        ,OOP_Central_Revenue COLLATE UTF8MB4_GENERAL_CI AS OOP_Central_Revenue
                        ,'1' AS num
                        FROM workforce_current_positions_allocation)
                        , NEW AS (
                        SELECT 'อัตราใหม่'AS TYPE 
                        ,Personnel_Type
                        ,Faculty
                        ,All_PositionTypes
                        ,POSITION 
                        ,NULL AS Position_Number
                        ,Fund_FT
                        ,NULL AS Salary_rate
                        ,Govt_Fund
                        ,Division_Revenue
                        ,OOP_Central_Revenue
                        ,University_Staff_Govt_Budget AS num
                        FROM workforce_new_positions_allocation)
                        , all_data AS (
                        SELECT * FROM CURRENT
                        UNION ALL 
                        SELECT * FROM NEW)
                        ,all_data2 AS (
                        SELECT ad1.*,f.Parent,p.Alias_Default AS parent_name ,f.Alias_Default AS fac FROM all_data ad1
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default,Parent 
                        FROM Faculty
                        WHERE parent NOT LIKE '%BU%'
                        ) f ON ad1.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
								LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default,Parent 
                        FROM Faculty
                        ) p ON f.parent = p.Faculty COLLATE UTF8MB4_GENERAL_CI
								)
                        SELECT a.*
                        ,act1.Employment_Type
                        ,act1.Workers_Name_Surname
                        ,act1.Personnel_Group
                        ,act1.Job_Family
                        ,act1.Position_Qualifications 
                        ,act1.Contract_Type
                        ,act1.Contract_Period_Short_Term
                        ,act5.Location_Code
                        FROM all_data2 a
                        LEFT JOIN actual_data_1 act1
                        ON a.Position_Number=act1.Position_Number
                        LEFT JOIN actual_data_5 act5
                        ON a.Position_Number=act5.Position_Number                        
                        ORDER BY a.Faculty";
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
        case "kku_wf_4year-workload":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH act1 AS (
                        SELECT Faculty,All_PositionTypes,COUNT(*) AS count_staff 
                        FROM actual_data_1 
                        WHERE All_PositionTypes!='No Position Type' AND Faculty!='00000'
                        GROUP BY Faculty,All_PositionTypes)
                        ,transform_data AS (
                        SELECT Faculty 
                        ,sum(CASE WHEN All_PositionTypes = 'บริหาร' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type1
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type2
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type3
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type4
                        FROM act1
                        GROUP BY Faculty)
                        ,4year AS (
                        SELECT Faculty 
                        ,sum(CASE WHEN All_PositionTypes = 'บริหาร' THEN COALESCE(wf, 0) ELSE 0 END) AS wf_type1
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(wf, 0) ELSE 0 END) AS wf_type2
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(wf, 0) ELSE 0 END) AS wf_type3
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN COALESCE(wf, 0) ELSE 0 END) AS wf_type4
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(FTES_criteria, 0) ELSE 0 END) AS sum_FTES
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(Research_Workload_Criteria, 0) ELSE 0 END) AS sum_RWC
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(Workload_Criteria_Academic_Services, 0) ELSE 0 END) AS sum_WCAS
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(Research_Workload_Criteria, 0) ELSE 0 END) AS sum_RWC2
                        FROM workforce_4year_plan
                        GROUP BY Faculty)
                        ,ty AS (
                        SELECT td.*,y.wf_type1,y.wf_type2,y.wf_type3,y.wf_type4,y.sum_FTES,y.sum_RWC,y.sum_WCAS,y.sum_RWC2
                        FROM transform_data td
                        LEFT JOIN 4year y
                        ON td.faculty = y.faculty COLLATE UTF8MB4_GENERAL_CI
                        )


                        SELECT ty.*,f.Alias_Default FROM ty
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default 
                        FROM Faculty
                        ) f ON ty.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE ty.wf_type1 IS NOT NULL 
                        ORDER BY ty.faculty";
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
        case "kku_wf_framework-summary":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH act1 AS (
                        SELECT Faculty,All_PositionTypes,COUNT(*) AS count_staff 
                        FROM actual_data_1 
                        WHERE All_PositionTypes!='No Position Type' AND Faculty!='00000'
                        GROUP BY Faculty,All_PositionTypes)
                        ,transform_data AS (
                        SELECT Faculty 
                        ,sum(CASE WHEN All_PositionTypes = 'บริหาร' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type1
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type2
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type3
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type4
                        FROM act1
                        GROUP BY Faculty)
                        ,4year AS (
                        SELECT Faculty AS f2
                        ,sum(CASE WHEN All_PositionTypes = 'บริหาร' THEN COALESCE(Year_1_Headcount, 0) ELSE 0 END) AS wf_type1_y1
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(Year_1_Headcount, 0) ELSE 0 END) AS wf_type2_y1
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(Year_1_Headcount, 0) ELSE 0 END) AS wf_type3_y1
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN COALESCE(Year_1_Headcount, 0) ELSE 0 END) AS wf_type4_y1
                        ,sum(CASE WHEN All_PositionTypes = 'บริหาร' THEN COALESCE(Year_2_Headcount, 0) ELSE 0 END) AS wf_type1_y2
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(Year_2_Headcount, 0) ELSE 0 END) AS wf_type2_y2
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(Year_2_Headcount, 0) ELSE 0 END) AS wf_type3_y2
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN COALESCE(Year_2_Headcount, 0) ELSE 0 END) AS wf_type4_y2
                        ,sum(CASE WHEN All_PositionTypes = 'บริหาร' THEN COALESCE(Year_3_Headcount, 0) ELSE 0 END) AS wf_type1_y3
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(Year_3_Headcount, 0) ELSE 0 END) AS wf_type2_y3
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(Year_3_Headcount, 0) ELSE 0 END) AS wf_type3_y3
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN COALESCE(Year_3_Headcount, 0) ELSE 0 END) AS wf_type4_y3
                        ,sum(CASE WHEN All_PositionTypes = 'บริหาร' THEN COALESCE(Year_4_Headcount, 0) ELSE 0 END) AS wf_type1_y4
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(Year_4_Headcount, 0) ELSE 0 END) AS wf_type2_y4
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(Year_4_Headcount, 0) ELSE 0 END) AS wf_type3_y4
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน' THEN COALESCE(Year_4_Headcount, 0) ELSE 0 END) AS wf_type4_y4
                        FROM workforce_4year_plan
                        GROUP BY Faculty)
                        ,ty AS (
                        SELECT td.*,y.*
                        FROM transform_data td
                        LEFT JOIN 4year y
                        ON td.faculty = y.f2 COLLATE UTF8MB4_GENERAL_CI
                        )


                        SELECT ty.*,f.Alias_Default FROM ty

                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default 
                        FROM Faculty
                        ) f ON ty.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE ty.wf_type1_y1 IS NOT NULL 
                        ORDER BY ty.faculty";
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
        case "kku_wf_positions-summary":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT w.*,f.Alias_Default
                        FROM workforce_new_position_request w
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default 
                        FROM Faculty
                        ) f ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI";
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