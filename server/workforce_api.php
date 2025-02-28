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
        case "kku_wf_approval-requests":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "with t1 as(SELECT faculty 
                        FROM workforce_new_positions_allocation
                        union all
                        SELECT faculty 
                        FROM workforce_current_positions_allocation)
                        ,t2 as(
                        SELECT f2.Alias_Default AS pname
                        from t1 t
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON t.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI)
                        SELECT distinct * FROM t2 ORDER BY pname";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $all_fac = $cmd->fetchAll(PDO::FETCH_ASSOC);


                $sql = "SELECT w.*  ,f2.Alias_Default AS pname
                        FROM workforce_new_positions_allocation w
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $f4 = $cmd->fetchAll(PDO::FETCH_ASSOC);                  
                
                $sql = "SELECT act1.* ,w.Fund_FT,w.Salary_rate,act1.rate_status,f2.Alias_Default AS pname
                        FROM workforce_current_positions_allocation w
                        LEFT JOIN workforce_hcm_actual act1
                        ON replace(w.Position_Number,'PN_','')=act1.POSITION_NUMBER
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE act1.Personnel_Type='ลูกจ้างของมหาวิทยาลัย'";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $c1 = $cmd->fetchAll(PDO::FETCH_ASSOC);

                

                $sql = "SELECT act1.* ,w.Fund_FT,w.Salary_rate,act1.rate_status,f2.Alias_Default AS pname
                        FROM workforce_current_positions_allocation w
                        LEFT JOIN workforce_hcm_actual act1
                        ON replace(w.Position_Number,'PN_','')=act1.Position_Number
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE act1.Personnel_Type='พนักงานมหาวิทยาลัย' and act1.Contract_Type='วิชาการระยะสั้น'";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $c2 = $cmd->fetchAll(PDO::FETCH_ASSOC);

                $sql = "SELECT act1.* ,w.Fund_FT,w.Salary_rate,act1.rate_status,f2.Alias_Default AS pname
                        FROM workforce_current_positions_allocation w
                        LEFT JOIN workforce_hcm_actual act1
                        ON replace(w.Position_Number,'PN_','')=act1.Position_Number
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE act1.Personnel_Type='พนักงานมหาวิทยาลัย'and act1.Contract_Type='ผู้เกษียณอายุราชการ' ";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $c3 = $cmd->fetchAll(PDO::FETCH_ASSOC);

                $sql = "SELECT act1.* ,w.Fund_FT,w.Salary_rate,act1.rate_status,f2.Alias_Default AS pname
                        FROM workforce_current_positions_allocation w
                        LEFT JOIN workforce_hcm_actual act1
                        ON replace(w.Position_Number,'PN_','')=act1.Position_Number
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE act1.Personnel_Type='พนักงานมหาวิทยาลัย' and act1.Contract_Type='ชาวต่างประเทศ' ";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $c4 = $cmd->fetchAll(PDO::FETCH_ASSOC);

                $sql = "SELECT act1.* ,w.Fund_FT,w.Salary_rate,act1.rate_status,f2.Alias_Default AS pname
                        FROM workforce_current_positions_allocation w
                        LEFT JOIN workforce_hcm_actual act1
                        ON replace(w.Position_Number,'PN_','')=act1.Position_Number
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE act1.Personnel_Type='พนักงานมหาวิทยาลัย' and act1.Contract_Type='ผู้ปฏิบัติงานในมหาวิทยาลัย'";
                $cmd = $conn->prepare($sql);
                $cmd->execute();
                $c5 = $cmd->fetchAll(PDO::FETCH_ASSOC);


                $conn = null;

                $response = array(
                    'all_fac'=>$all_fac,
                    'f4' => $f4,
                    'c1' => $c1,
                    'c2' => $c2,
                    'c3' => $c3,
                    'c4' => $c4,
                    'c5' => $c5,
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
                                w.Faculty,
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
                            FROM workforce_4year_plan w
                            LEFT JOIN Faculty f
                            ON w.Faculty=f.Faculty COLLATE UTF8MB4_GENERAL_CI 
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
                                FROM workforce_hcm_actual
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
                            f.Alias_Default,
                            f2.Alias_Default AS pname
                        FROM position_summary ps
                        LEFT JOIN actual_counts ac ON ps.Faculty = ac.Faculty COLLATE utf8mb4_general_ci
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON ps.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
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
                            ac.Actual_type4,
                            f2.Alias_Default
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
                //$slt = $_POST["slt"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS(
                        SELECT w.Faculty
                        ,all_position_types
                        ,sum(salary_rate) AS salary_rate
                        ,COUNT(*) AS num_position_types
                        ,fund_ft
                        FROM workforce_hcm_actual w
                        LEFT JOIN Faculty f
                        ON w.Faculty=f.Faculty COLLATE UTF8MB4_GENERAL_CI 
                        WHERE all_position_types IS NOT NULL and (fund_ft ='เงินงบประมาณ' OR fund_ft='เงินรายได้') AND w.Faculty !='00000'
                        GROUP BY Faculty
                        ,all_position_types
                        ,fund_ft
                        ORDER BY Faculty)

                        SELECT f.Alias_Default,f2.Alias_Default as pname
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
                        FROM t1 ad
                        LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON ad.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE f.Alias_Default IS NOT null
                        GROUP BY f.Alias_Default,f2.Alias_Default
                        
                        ORDER BY f.Alias_Default";
                $cmd = $conn->prepare($sql);
                //$cmd->bindParam(':slt', $slt, PDO::PARAM_STR);
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
                        FROM workforce_hcm_actual
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
                        FROM workforce_hcm_actual
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

                    SELECT f.Alias_Default,f.parent,f2.Alias_Default as pname
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
                    LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default ,parent
                    FROM Faculty
                    where parent like 'Faculty%') f 
                    ON t1.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                    LEFT JOIN (
                    SELECT DISTINCT Faculty, Alias_Default
                    FROM Faculty) f2 
                    ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                    WHERE f.Alias_Default IS NOT null
                    GROUP BY f.Alias_Default,f.parent,f2.Alias_Default
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
                $sql = "WITH t1 AS (
                        SELECT Faculty
                        ,Personnel_Type
                        ,PERSONNEL_GROUP
                        ,all_position_types
                        ,POSITION_NUMBER
                        ,POSITION
                        ,LOCATION_CODE
                        ,JOB_FAMILY
                        ,VACANT_FROM_WHICH_DATE
                        ,REASON_FOR_VACANCY
                        ,V_FOR_6_MONTHS_ON
                        FROM workforce_hcm_actual
                        WHERE rate_status='อัตราว่าง')

                        SELECT * FROM t1
                        order by POSITION_NUMBER";
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
                //$slt = $_POST["slt"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT distinct COALESCE(f.Alias_Default,act1.Faculty) AS faculty
                        ,act1.Personnel_Type
                        ,act1.Workers_Name_Surname
                        ,act1.`Position`
                        ,Replace(act1.Position_Number,'PN_','') AS Position_Number
                        ,act1.all_position_types
                        ,act1.Job_Family
                        ,act4.Retirement_Date
                        ,f2.Alias_Default as pname
                        FROM workforce_hcm_actual act1
                        LEFT JOIN workforce_hcm_actual act4
                        ON act1.Position_Number=act4.Position_Number
                        LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f 
                        ON act1.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE act1.Faculty!='00000' and f.Alias_Default IS NOT null
                        ORDER BY COALESCE(f.Alias_Default,act1.Faculty),Replace(act1.Position_Number,'PN_','')";
                $cmd = $conn->prepare($sql);
                //$cmd->bindParam(':slt', $slt, PDO::PARAM_STR);
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
                        ,replace(Position_Number,'PN_','') COLLATE UTF8MB4_GENERAL_CI AS Position_Number
                        ,Fund_FT COLLATE UTF8MB4_GENERAL_CI AS Fund_FT
                        ,COALESCE(Salary_rate,0) COLLATE UTF8MB4_GENERAL_CI AS Salary_rate
                        ,COALESCE(Govt_Fund,0) COLLATE UTF8MB4_GENERAL_CI AS Govt_Fund
                        ,COALESCE(Division_Revenue,0) COLLATE UTF8MB4_GENERAL_CI AS Division_Revenue
                        ,COALESCE(OOP_Central_Revenue,0) COLLATE UTF8MB4_GENERAL_CI AS OOP_Central_Revenue
                        FROM workforce_current_positions_allocation)
                        , NEW AS (
                        SELECT 'อัตราใหม่'AS TYPE 
                        ,Personnel_Type
                        ,Faculty
                        ,All_PositionTypes
                        ,POSITION 
                        ,NULL AS Position_Number
                        ,Fund_FT
                        ,0 AS Salary_rate
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
                        ,f.parent
                        ,act1.Employment_Type
                        ,act1.Workers_Name_Surname
                        ,act1.Personnel_Group
                        ,act1.Job_Family
                        ,act1.POSITION_QUALIFIFCATIONS AS Position_Qualifications
                        ,act1.Contract_Type
                        ,act1.Contract_Period_Short_Term
                        ,act1.Location_Code
                        ,f2.Alias_Default as pname
                        FROM all_data a
                        LEFT JOIN workforce_hcm_actual act1
                        ON replace(a.Position_Number,'PN_','')=act1.Position_Number
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f 
                        ON a.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        ORDER BY a.Faculty,a.Position_Number";
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
        case "kku_wf_overview-framework_dropdown":
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
                        ,replace(Position_Number,'PN_','') COLLATE UTF8MB4_GENERAL_CI AS Position_Number
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

                        SELECT distinct f.parent
                        ,f2.Alias_Default as pname
                        FROM all_data a
                        LEFT JOIN workforce_hcm_actual act1
                        ON replace(a.Position_Number,'PN_','')=act1.Position_Number
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f ON a.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        
                        ORDER BY f.parent";
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
                        ,NULL AS Name_Surname_If_change 
                        FROM workforce_current_positions_allocation)
                        , NEW AS (
                        SELECT 'อัตราใหม่'AS TYPE 
                        ,w1.Approved_Personnel_Type
                        ,w1.Faculty
                        ,w1.All_PositionTypes
                        ,w1.POSITION 
                        ,w1.New_Position_Number AS Position_Number
                        ,w1.Fund_FT
                        ,NULL AS Salary_rate
                        ,w1.Govt_Fund
                        ,w1.Division_Revenue
                        ,w1.OOP_Central_Revenue
                        ,'1' AS num
                        ,w1.Name_Surname_If_change
                        FROM workforce_new_positions_allocation w1
                        LEFT JOIN workforce_new_positions_allocation_2 w2
                        ON w1.Account=w2.Account COLLATE utf8mb4_general_ci AND w1.Scenario=w2.Scenario COLLATE utf8mb4_general_ci AND w1.Version=w2.Version COLLATE utf8mb4_general_ci
                        AND w1.Faculty=w2.Faculty COLLATE utf8mb4_general_ci AND w1.NHR=w2.NHR COLLATE utf8mb4_general_ci AND w1.Personnel_Type=w2.Personnel_Type COLLATE utf8mb4_general_ci
                        AND w1.All_PositionTypes=w2.All_PositionTypes COLLATE utf8mb4_general_ci AND w1.Position=w2.Position COLLATE utf8mb4_general_ci)
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
                        ,IFNULL(NULLIF(a.Name_Surname_If_change, ''), act1.Workers_Name_Surname) AS Workers_Name_Surname
                        ,act1.Personnel_Group
                        ,act1.Job_Family
                        ,act1.POSITION_QUALIFIFCATIONS AS Position_Qualifications
                        ,act1.Contract_Type
                        ,act1.Contract_Period_Short_Term
                        ,act1.Location_Code
                        ,replace(a.Position_Number,'PN_','') AS Position_Number2
                        FROM all_data2 a
                        LEFT JOIN workforce_hcm_actual act1
                        ON replace(a.Position_Number,'PN_','')=act1.Position_Number                     
                        ORDER BY a.Faculty,a.Position_Number";
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
        case "kku_wf_annual-allocation_dropdown":
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
                        ,w1.Personnel_Type
                        ,w1.Faculty
                        ,w1.All_PositionTypes
                        ,w1.POSITION 
                        ,NULL AS Position_Number
                        ,w1.Fund_FT
                        ,NULL AS Salary_rate
                        ,w1.Govt_Fund
                        ,w1.Division_Revenue
                        ,w1.OOP_Central_Revenue
                        ,(CAST(w1.University_Staff_Govt_Budget AS SIGNED)+w2.University_Staff_Rev_Budget+CAST(w2.University_Employees AS SIGNED)) AS num
                        FROM workforce_new_positions_allocation w1
                        LEFT JOIN workforce_new_positions_allocation_2 w2
                        ON w1.Account=w2.Account COLLATE utf8mb4_general_ci AND w1.Scenario=w2.Scenario COLLATE utf8mb4_general_ci AND w1.Version=w2.Version COLLATE utf8mb4_general_ci
                        AND w1.Faculty=w2.Faculty COLLATE utf8mb4_general_ci AND w1.NHR=w2.NHR COLLATE utf8mb4_general_ci AND w1.Personnel_Type=w2.Personnel_Type COLLATE utf8mb4_general_ci
                        AND w1.All_PositionTypes=w2.All_PositionTypes COLLATE utf8mb4_general_ci AND w1.Position=w2.Position COLLATE utf8mb4_general_ci)
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
                        LEFT JOIN workforce_hcm_actual act1
                        ON a.Position_Number=act1.Position_Number                     
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
                        SELECT Faculty,all_position_types,COUNT(*) AS count_staff 
                        FROM workforce_hcm_actual 
                        WHERE all_position_types!='No Position Type' AND Faculty!='00000'
                        GROUP BY Faculty,all_position_types)
                        ,transform_data AS (
                        SELECT Faculty 
                        ,sum(CASE WHEN all_position_types = 'บริหาร' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type1
                        ,sum(CASE WHEN all_position_types = 'วิชาการ' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type2
                        ,sum(CASE WHEN all_position_types = 'วิจัย' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type3
                        ,sum(CASE WHEN all_position_types = 'สนับสนุน' THEN COALESCE(count_staff, 0) ELSE 0 END) AS Actual_type4
                        FROM act1
                        GROUP BY Faculty)
                        ,4year AS (
                        SELECT Faculty AS fac2
                        ,sum(CASE WHEN All_PositionTypes = 'บริหาร' THEN COALESCE(wf, 0) ELSE 0 END) AS wf_type1
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(wf, 0) ELSE 0 END) AS wf_type2
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(wf, 0) ELSE 0 END) AS wf_type3
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Healthcare Services' THEN COALESCE(wf, 0) ELSE 0 END) AS j1
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Student and Faculty Services' THEN COALESCE(wf, 0) ELSE 0 END) AS j2
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Technical and Research services' THEN COALESCE(wf, 0) ELSE 0 END) AS j3
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Internationalization' THEN COALESCE(wf, 0) ELSE 0 END) AS j4
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Human Resources' THEN COALESCE(wf, 0) ELSE 0 END) AS j5
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Administration' THEN COALESCE(wf, 0) ELSE 0 END) AS j6
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Legal, Compliance and Protection' THEN COALESCE(wf, 0) ELSE 0 END) AS j7
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Strategic Management' THEN COALESCE(wf, 0) ELSE 0 END) AS j8
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='information Technology' THEN COALESCE(wf, 0) ELSE 0 END) AS j9
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Infrastructure and Facility Services' THEN COALESCE(wf, 0) ELSE 0 END) AS j10
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Communication and Relation Management' THEN COALESCE(wf, 0) ELSE 0 END) AS j11
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Cultural Affair' THEN COALESCE(wf, 0) ELSE 0 END) AS j12
                        ,sum(CASE WHEN All_PositionTypes = 'สนับสนุน'AND Job_Family='Financial Services' THEN COALESCE(wf, 0) ELSE 0 END) AS j13
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(FTES_criteria, 0) ELSE 0 END) AS sum_FTES
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(Research_Workload_Criteria, 0) ELSE 0 END) AS sum_RWC
                        ,sum(CASE WHEN All_PositionTypes = 'วิชาการ' THEN COALESCE(Workload_Criteria_Academic_Services, 0) ELSE 0 END) AS sum_WCAS
                        ,sum(CASE WHEN All_PositionTypes = 'วิจัย' THEN COALESCE(Research_Workload_Criteria, 0) ELSE 0 END) AS sum_RWC2
                        FROM workforce_4year_plan
                        GROUP BY Faculty)
                        ,ty AS (
                        SELECT td.*,y.*
                        FROM transform_data td
                        LEFT JOIN 4year y
                        ON td.faculty = y.fac2 COLLATE UTF8MB4_GENERAL_CI
                        )


                        SELECT ty.*,f.Alias_Default,f2.Alias_Default AS pname FROM ty
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        WHERE parent LIKE 'Faculty%'
                        ) f ON ty.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN Faculty f2
                        ON f.parent=f2.Faculty
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
                $sql = "SELECT w.*,f.Alias_Default,f2.Alias_Default as pname
                        FROM workforce_new_position_request w
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%'
                        ) f ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        left join Faculty f2
                        on f.parent=f2.faculty";
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
        case "kku_wf_new-vs-old-positions":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH w1 AS (
                        SELECT 'อัตราเดิม' AS TYPE ,Position_Number
                        ,case when Wish_to_Continue_Employement='ประสงค์จ้างต่อ' then 'ใช่'
                        when Wish_to_Continue_Employement='ไม่ประสงค์จ้างต่อ' then 'ไม่ใช่'
                        ELSE NULL END AS Wish_to_Continue_Employement
                        ,Performance_Evaluation_Percentage,Performance_Evaluation
                        FROM workforce_current_position_request)
                        ,act1 AS (
                        SELECT a1.faculty,a1.Position_Number,a1.Workers_Name_Surname,a1.Personnel_Type,a1.Employment_Type,a1.`Position`
								,a1.Job_Family,a1.all_position_types as All_PositionTypes,a1.Personnel_Group
								,a1.Contract_Type,a1.Contract_Period_Short_Term
								,a1.POSITION_QUALIFIFCATIONS as Position_Qualifications,w1.Wish_to_Continue_Employement
								,w1.Performance_Evaluation_Percentage,w1.Performance_Evaluation,w1.type
								,a1.rate_status,a1.salary_rate,a1.fund_ft,a1.govt_fund,a1.division_revenue,a1.oop_central_revenue
								,a1.Vacant_From_Which_Date,a1.Hiring_Start_End_Date,a1.Position_Status
								,a1.Location_Code,NULL AS Requested_HC_unit
                        FROM w1
                        LEFT JOIN workforce_hcm_actual a1
                        ON replace(w1.Position_Number,'PN_','')=a1.Position_Number)
                        
                        ,w2 AS (
                        SELECT 
                            Faculty COLLATE utf8mb4_general_ci AS faculty,
                            NULL AS Position_Number,
                            Workers_Name_Surname COLLATE utf8mb4_general_ci,
                            Personnel_Type COLLATE utf8mb4_general_ci,
                            Employment_Type COLLATE utf8mb4_general_ci,
                            POSITION COLLATE utf8mb4_general_ci AS Position,
                            Job_Family COLLATE utf8mb4_general_ci,
                            All_PositionTypes COLLATE utf8mb4_general_ci,
                            Personnel_Group COLLATE utf8mb4_general_ci,
                            Contract_Type COLLATE utf8mb4_general_ci,
                            Hiring_Start_End_Date COLLATE utf8mb4_general_ci AS Contract_Period_Short_Term,
                            Position_Qualififcations COLLATE utf8mb4_general_ci AS Position_Qualifications,
                            NULL AS Wish_to_Continue_Employement,
                            NULL AS Performance_Evaluation_Percentage,
                            NULL AS Performance_Evaluation,
                            'อัตราใหม่'COLLATE utf8mb4_general_ci AS type,
                            NULL AS rate_status,
                            Salary_Wages_Baht_per_month COLLATE utf8mb4_general_ci AS salary_rate,
                            Fund_FT COLLATE utf8mb4_general_ci AS fund_ft,
                            NULL AS govt_fund,
                            NULL AS division_revenue,
                            NULL AS oop_central_revenue,
                            NULL AS Vacant_From_Which_Date,
                            NULL AS Hiring_Start_End_Date,
                            NULL AS Position_Status,
                            Field_of_Study COLLATE utf8mb4_general_ci AS Location_Code,
                            Requested_HC_unit
                        FROM workforce_new_position_request)
                        ,all_data AS (
                        SELECT * FROM act1
                        UNION ALL
                        SELECT * FROM w2
                        )
                        SELECT a.*,f.Alias_Default ,f2.Alias_Default as pname FROM all_data a
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%'
                        ) f ON a.faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        left join Faculty f2
                        on f.parent=f2.faculty
                        ORDER BY faculty";
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
        case "list-faculty":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH f AS (
                        SELECT parent FROM Faculty
                        WHERE parent LIKE 'Faculty-%' AND parent!='Faculty-00'
                        GROUP BY parent)
                        ,t1 AS (
                        SELECT f2.Alias_Default,f.Parent,f2.faculty
                        FROM f
                        LEFT JOIN Faculty f2
                        ON f.Parent=f2.Faculty)

                        SELECT * FROM t1
                        ORDER BY t1.Alias_Default";
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
        case "kku_wf_current-vs-ideal":
            try {
                $db = new Database();
                $conn = $db->connect();
                $slt = $_POST["slt"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH f AS (
                        SELECT parent FROM Faculty
                        WHERE parent ='".$slt."'
                        GROUP BY parent)
                        ,t1 AS (
                        SELECT f2.Alias_Default,f2.faculty
                        FROM f
                        LEFT JOIN Faculty f2
                        ON f.Parent=f2.Faculty)
                        ,t2 AS (
                        SELECT t.Alias_Default,j.code,j.name,t.faculty FROM t1 t
                        CROSS JOIN job_families j)
                        ,t3 AS (
                        SELECT *
                        FROM t2
                        CROSS JOIN (SELECT position 
                        FROM positions) p)
                        ,t4 AS (
                        SELECT w.*,f.parent 
                        FROM workforce_4year_plan w
                        LEFT JOIN Faculty f
                        ON w.Faculty=f.Faculty COLLATE UTF8MB4_GENERAL_CI 
                        WHERE f.parent ='".$slt."'
                        )
                        ,t5 AS (
                        SELECT t.*,COALESCE(tt.WF,0) as wf
                        FROM t3 t
                        LEFT JOIN t4 tt
                        ON t.name=tt.Job_Family COLLATE utf8mb4_general_ci AND t.position=tt.`Position` COLLATE utf8mb4_general_ci AND t.faculty=tt.parent COLLATE UTF8MB4_GENERAL_CI)
                        ,t6 AS (
                        SELECT a.POSITION,a.Job_Family,COUNT(*) AS count_person,f.parent
                        FROM workforce_hcm_actual a
                        LEFT JOIN Faculty f
                        ON a.Faculty=f.Faculty COLLATE UTF8MB4_GENERAL_CI 
                        WHERE f.parent ='".$slt."'
                        GROUP BY a.POSITION,a.Job_Family,f.parent)
                        ,t7 AS (
                        SELECT t.*,COALESCE(tt.count_person,0) as count_person
                        FROM t5 t
                        LEFT JOIN t6 tt
                        ON t.name=tt.Job_Family COLLATE utf8mb4_general_ci AND t.position=tt.`POSITION` COLLATE utf8mb4_general_ci AND t.faculty=tt.parent COLLATE UTF8MB4_GENERAL_CI)

                        SELECT * FROM t7
                        where (t7.count_person >0 or t7.wf!='0')
                        order BY  t7.Alias_Default,t7.code";
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
        case "kku_wf_staff-requests_current":
            try {
                $db = new Database();
                $conn = $db->connect();
                //$slt = $_POST["slt"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS (
                        SELECT faculty,Position_Number
                        FROM workforce_current_position_request)
                        , t2 AS (
                        SELECT t.*,a.Personnel_Type,a.all_position_types AS All_PositionTypes,a.Position,a.Employment_Type,a.Contract_Type,a.fund_ft
                        FROM t1 t
                        LEFT JOIN workforce_hcm_actual a
                        ON replace(t.Position_Number,'PN_','')=a.Position_Number)
                        , t3 AS (
                        SELECT faculty,Position_Number,All_PositionTypes,POSITION,Employment_Type,Contract_Type
                        ,case when Personnel_Type='พนักงานมหาวิทยาลัย' AND fund_ft='เงินรายได้' then 'พนักงานมหาวิทยาลัยงบประมาณเงินรายได้'
                        ELSE Personnel_Type END AS Personnel_Type
                        FROM t2
                        )
                        SELECT t3.*,f2.parent,f3.Alias_Default AS pname FROM t3
                        LEFT JOIN (SELECT * from Faculty
								WHERE parent LIKE 'Faculty%') f2
                        ON f2.Faculty=t3.faculty
                        LEFT JOIN Faculty f3
                        ON f2.parent=f3.Faculty";
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
        case "kku_wf_staff-requests_new":
            try {
                $db = new Database();
                $conn = $db->connect();
                //$slt = $_POST["slt"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT w.faculty,NULL AS Position_Number,All_PositionTypes,position,Employment_Type,Contract_Type
                        ,case when Personnel_Type='พนักงานมหาวิทยาลัย' AND Fund_FT='เงินงบประมาณ' then 'พนักงานมหาวิทยาลัยงบประมาณเงินงบประมาณ'
                        when Personnel_Type='พนักงานมหาวิทยาลัย' AND Fund_FT='เงินรายได้' then 'พนักงานมหาวิทยาลัยงบประมาณเงินรายได้'
                        ELSE Personnel_Type END AS Personnel_Type
                        ,f2.parent,f3.Alias_Default AS pname
                        FROM workforce_new_position_request w
                        LEFT JOIN (SELECT * from Faculty
								WHERE parent LIKE 'Faculty%') f2
                        ON f2.Faculty=w.faculty COLLATE utf8mb4_general_ci
                        LEFT JOIN Faculty f3
                        ON f2.parent=f3.Faculty ";
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
        case "kku_wf_retirement-fiscal-year":
            try {
                $db = new Database();
                $conn = $db->connect();
                //$slt = $_POST["slt"];
                // เชื่อมต่อฐานข้อมูล
                /* $sql = "WITH act1 AS(
                        SELECT w.Faculty
                        ,Personnel_Type
                        ,POSITION
                        ,Job_Family
                        ,Position_Number
                        ,all_position_types
                        FROM workforce_hcm_actual w
                        LEFT JOIN Faculty f
                            ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE w.Faculty!='00000' AND (Personnel_Type='ข้าราชการ'OR Personnel_Type='ลูกจ้างของมหาวิทยาลัย'OR Personnel_Type='ลูกจ้างประจำ'OR Personnel_Type='พนักงานมหาวิทยาลัย')and f.parent =:slt) 
                        ,t2 AS (
                        SELECT distinct t1.*,act4.Retirement_Date,f2.Alias_Default
                        FROM act1 t1
                        LEFT JOIN workforce_hcm_actual act4
                        ON t1.Position_Number=act4.Position_Number
                        LEFT JOIN Faculty f2
                        ON f2.Faculty=t1.Faculty COLLATE utf8mb4_general_ci
                        )
                        ,t3 AS (
                        SELECT distinct tt.*,a.fund_ft
                        FROM t2 tt
                        LEFT JOIN workforce_hcm_actual a
                        ON tt.Position_Number=a.position_number)
                        ,t4 AS (
                        SELECT Faculty,POSITION,Job_Family,Position_Number,all_position_types,Retirement_Date,Alias_Default
                        ,case when Personnel_Type='พนักงานมหาวิทยาลัย' AND fund_ft='เงินรายได้' then 'พนักงานมหาวิทยาลัยเงินรายได้'
                        when Personnel_Type='พนักงานมหาวิทยาลัย' AND fund_ft='เงินงบประมาณ' then 'พนักงานมหาวิทยาลัยเงินงบประมาณ'
                        ELSE Personnel_Type END AS Personnel_Type
                        FROM t3)
                        ,t5 AS (
                        SELECT Faculty,POSITION,Job_Family,Position_Number,all_position_types,Personnel_Type,Alias_Default
                        ,CASE 
                                WHEN STR_TO_DATE(CONCAT(SUBSTRING_INDEX(Retirement_Date, '-', 2), '-', 
                                        CAST(SUBSTRING_INDEX(Retirement_Date, '-', -1) - 543 AS UNSIGNED)), '%d-%m-%Y') 
                                    BETWEEN '2024-01-01' AND '2024-12-31' THEN 'y1'
                                WHEN STR_TO_DATE(CONCAT(SUBSTRING_INDEX(Retirement_Date, '-', 2), '-', 
                                        CAST(SUBSTRING_INDEX(Retirement_Date, '-', -1) - 543 AS UNSIGNED)), '%d-%m-%Y') 
                                    BETWEEN '2025-01-01' AND '2025-12-31' THEN 'y2'
                                WHEN STR_TO_DATE(CONCAT(SUBSTRING_INDEX(Retirement_Date, '-', 2), '-', 
                                        CAST(SUBSTRING_INDEX(Retirement_Date, '-', -1) - 543 AS UNSIGNED)), '%d-%m-%Y') 
                                    BETWEEN '2026-01-01' AND '2026-12-31' THEN 'y3'
                                WHEN STR_TO_DATE(CONCAT(SUBSTRING_INDEX(Retirement_Date, '-', 2), '-', 
                                        CAST(SUBSTRING_INDEX(Retirement_Date, '-', -1) - 543 AS UNSIGNED)), '%d-%m-%Y') 
                                    BETWEEN '2027-01-01' AND '2027-12-31' THEN 'y4'
                                ELSE 'yy' 
                            END AS y
                        FROM t4)
                        ,t6 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,y,Personnel_Type,Alias_Default FROM t5 WHERE Y !='yy')
                        ,t7 AS (
                        SELECT *,COUNT(*) AS all_p FROM t6 GROUP BY Faculty,POSITION,Job_Family,all_position_types,y,Personnel_Type,Alias_Default)
                        ,t8 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,Alias_Default,y
                        ,sum(case when Personnel_Type='ข้าราชการ' then all_p ELSE '0' END) AS p1
                        ,sum(case when Personnel_Type='ลูกจ้างของมหาวิทยาลัย' then all_p ELSE '0' END) AS p5
                        ,sum(case when Personnel_Type='ลูกจ้างประจำ' then all_p ELSE '0' END) AS p4
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได้' then all_p ELSE '0' END) AS p3
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินงบประมาณ' then all_p ELSE '0' END) AS p2
                        FROM t7 
                        WHERE Y='y1'
                        GROUP BY Faculty,POSITION,Job_Family,all_position_types,Alias_Default,Y)
                        ,t9 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default
                        ,sum(case when Personnel_Type='ข้าราชการ' then all_p ELSE '0' END) AS p1
                        ,sum(case when Personnel_Type='ลูกจ้างของมหาวิทยาลัย' then all_p ELSE '0' END) AS p5
                        ,sum(case when Personnel_Type='ลูกจ้างประจำ' then all_p ELSE '0' END) AS p4
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได้' then all_p ELSE '0' END) AS p3
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินงบประมาณ' then all_p ELSE '0' END) AS p2
                        FROM t7 
                        WHERE Y='y2'
                        GROUP BY Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default)
                        ,t10 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default
                        ,sum(case when Personnel_Type='ข้าราชการ' then all_p ELSE '0' END) AS p1
                        ,sum(case when Personnel_Type='ลูกจ้างของมหาวิทยาลัย' then all_p ELSE '0' END) AS p5
                        ,sum(case when Personnel_Type='ลูกจ้างประจำ' then all_p ELSE '0' END) AS p4
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได้' then all_p ELSE '0' END) AS p3
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินงบประมาณ' then all_p ELSE '0' END) AS p2
                        FROM t7 
                        WHERE Y='y3'
                        GROUP BY Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default)
                        ,t11 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default
                        ,sum(case when Personnel_Type='ข้าราชการ' then all_p ELSE '0' END) AS p1
                        ,sum(case when Personnel_Type='ลูกจ้างของมหาวิทยาลัย' then all_p ELSE '0' END) AS p5
                        ,sum(case when Personnel_Type='ลูกจ้างประจำ' then all_p ELSE '0' END) AS p4
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได้' then all_p ELSE '0' END) AS p3
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินงบประมาณ' then all_p ELSE '0' END) AS p2
                        FROM t7 
                        WHERE Y='y4'
                        GROUP BY Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default)
                        ,t12 AS (
                        SELECT t.*,tt.Alias_Default as Alias_Default_y2,tt.Faculty as Faculty_y2 , tt.POSITION as POSITION_y2,tt.Job_Family as Job_Family_y2
                        ,tt.all_position_types as all_position_types_y2,tt.y AS y2,tt.p1 AS p1_y2,tt.p2 AS p2_y2,tt.p3 AS p3_y2,tt.p4 AS p4_y2,tt.p5 AS p5_y2
                        FROM t8 t
                        LEFT JOIN t9 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        UNION
                        SELECT t.*,tt.Alias_Default as Alias_Default_y2,tt.Faculty as Faculty_y2 , tt.POSITION as POSITION_y2,tt.Job_Family as Job_Family_y2
                        ,tt.all_position_types as all_position_types_y2,tt.y AS y2,tt.p1 AS p1_y2,tt.p2 AS p2_y2,tt.p3 AS p3_y2,tt.p4 AS p4_y2,tt.p5 AS p5_y2
                        FROM t8 t
                        RIGHT JOIN t9 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        WHERE t.Faculty IS NULL)
                        ,t13 AS (
                        SELECT t.*,tt.Alias_Default as Alias_Default_y3,tt.Faculty as Faculty_y3 , tt.POSITION as POSITION_y3,tt.Job_Family as Job_Family_y3
                        ,tt.all_position_types as all_position_types_y3,tt.y AS y3,tt.p1 AS p1_y3,tt.p2 AS p2_y3,tt.p3 AS p3_y3,tt.p4 AS p4_y3,tt.p5 AS p5_y3
                        FROM t12 t
                        LEFT JOIN t10 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        UNION
                        SELECT t.*,tt.Alias_Default as Alias_Default_y3,tt.Faculty as Faculty_y3 , tt.POSITION as POSITION_y3,tt.Job_Family as Job_Family_y3
                        ,tt.all_position_types as all_position_types_y3,tt.y AS y3,tt.p1 AS p1_y3,tt.p2 AS p2_y3,tt.p3 AS p3_y3,tt.p4 AS p4_y3,tt.p5 AS p5_y3
                        FROM t12 t
                        RIGHT JOIN t10 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        WHERE t.Faculty IS NULL)
                        ,t14 AS (
                        SELECT t.*,tt.Alias_Default as Alias_Default_y4,tt.Faculty as Faculty_y4 , tt.POSITION as POSITION_y4,tt.Job_Family as Job_Family_y4
                        ,tt.all_position_types as all_position_types_y4,tt.y AS y4,tt.p1 AS p1_y4,tt.p2 AS p2_y4,tt.p3 AS p3_y4,tt.p4 AS p4_y4,tt.p5 AS p5_y4
                        FROM t13 t
                        LEFT JOIN t11 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        UNION
                        SELECT t.*,tt.Alias_Default as Alias_Default_y4,tt.Faculty as Faculty_y4 , tt.POSITION as POSITION_y4,tt.Job_Family as Job_Family_y4
                        ,tt.all_position_types as all_position_types_y4,tt.y AS y4,tt.p1 AS p1_y4,tt.p2 AS p2_y4,tt.p3 AS p3_y4,tt.p4 AS p4_y4,tt.p5 AS p5_y4
                        FROM t13 t
                        RIGHT JOIN t11 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        WHERE t.Faculty IS NULL)


                        SELECT * FROM t14
                        ORDER BY t14.faculty,t14.y"; */
                        $sql="WITH act1 AS(SELECT w.Faculty
                        ,Personnel_Type
                        ,POSITION
                        ,Job_Family
                        ,Position_Number
                        ,all_position_types
                        FROM workforce_hcm_actual w
                        LEFT JOIN Faculty f
                            ON w.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        WHERE w.Faculty!='00000' AND (Personnel_Type='ข้าราชการ'OR Personnel_Type='ลูกจ้างของมหาวิทยาลัย'OR Personnel_Type='ลูกจ้างประจำ'OR Personnel_Type='พนักงานมหาวิทยาลัย')) 
                        ,t2 AS (
                        SELECT distinct t1.*,act4.Retirement_Date,f2.Alias_Default
                        FROM act1 t1
                        LEFT JOIN workforce_hcm_actual act4
                        ON t1.Position_Number=act4.Position_Number
                        LEFT JOIN Faculty f2
                        ON f2.Faculty=t1.Faculty COLLATE utf8mb4_general_ci
                        )
                        ,t3 AS (
                        SELECT distinct tt.*,a.fund_ft
                        FROM t2 tt
                        LEFT JOIN workforce_hcm_actual a
                        ON tt.Position_Number=a.position_number)
                        ,t4 AS (
                        SELECT Faculty,POSITION,Job_Family,Position_Number,all_position_types,Retirement_Date,Alias_Default
                        ,case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได' then 'พนักงานมหาวิทยาลัยเงินรายได้'
                        when Personnel_Type='พนักงานมหาวิทยาลัย'then 'พนักงานมหาวิทยาลัยเงินงบประมาณ'
                        ELSE Personnel_Type END AS Personnel_Type
                        FROM t3)
                        ,t5 AS (
                        SELECT Faculty,POSITION,Job_Family,Position_Number,all_position_types,Personnel_Type,Alias_Default
                        ,CASE 
                                WHEN STR_TO_DATE(CONCAT(SUBSTRING_INDEX(Retirement_Date, '-', 2), '-', 
                                        CAST(SUBSTRING_INDEX(Retirement_Date, '-', -1) - 543 AS UNSIGNED)), '%d-%m-%Y') 
                                    BETWEEN '2024-01-01' AND '2024-12-31' THEN 'y1'
                                WHEN STR_TO_DATE(CONCAT(SUBSTRING_INDEX(Retirement_Date, '-', 2), '-', 
                                        CAST(SUBSTRING_INDEX(Retirement_Date, '-', -1) - 543 AS UNSIGNED)), '%d-%m-%Y') 
                                    BETWEEN '2025-01-01' AND '2025-12-31' THEN 'y2'
                                WHEN STR_TO_DATE(CONCAT(SUBSTRING_INDEX(Retirement_Date, '-', 2), '-', 
                                        CAST(SUBSTRING_INDEX(Retirement_Date, '-', -1) - 543 AS UNSIGNED)), '%d-%m-%Y') 
                                    BETWEEN '2026-01-01' AND '2026-12-31' THEN 'y3'
                                WHEN STR_TO_DATE(CONCAT(SUBSTRING_INDEX(Retirement_Date, '-', 2), '-', 
                                        CAST(SUBSTRING_INDEX(Retirement_Date, '-', -1) - 543 AS UNSIGNED)), '%d-%m-%Y') 
                                    BETWEEN '2027-01-01' AND '2027-12-31' THEN 'y4'
                                ELSE 'yy' 
                            END AS y
                        FROM t4)
                        ,t6 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,y,Personnel_Type,Alias_Default FROM t5 WHERE Y !='yy')
                        ,t7 AS (
                        SELECT *,COUNT(*) AS all_p FROM t6 GROUP BY Faculty,POSITION,Job_Family,all_position_types,y,Personnel_Type,Alias_Default)
                        ,t8 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,Alias_Default,y
                        ,sum(case when Personnel_Type='ข้าราชการ' then all_p ELSE '0' END) AS p1
                        ,sum(case when Personnel_Type='ลูกจ้างของมหาวิทยาลัย' then all_p ELSE '0' END) AS p5
                        ,sum(case when Personnel_Type='ลูกจ้างประจำ' then all_p ELSE '0' END) AS p4
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได้' then all_p ELSE '0' END) AS p3
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินงบประมาณ' then all_p ELSE '0' END) AS p2
                        FROM t7 
                        WHERE Y='y1'
                        GROUP BY Faculty,POSITION,Job_Family,all_position_types,Alias_Default,Y)
                        ,t9 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default
                        ,sum(case when Personnel_Type='ข้าราชการ' then all_p ELSE '0' END) AS p1
                        ,sum(case when Personnel_Type='ลูกจ้างของมหาวิทยาลัย' then all_p ELSE '0' END) AS p5
                        ,sum(case when Personnel_Type='ลูกจ้างประจำ' then all_p ELSE '0' END) AS p4
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได้' then all_p ELSE '0' END) AS p3
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินงบประมาณ' then all_p ELSE '0' END) AS p2
                        FROM t7 
                        WHERE Y='y2'
                        GROUP BY Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default)
                        ,t10 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default
                        ,sum(case when Personnel_Type='ข้าราชการ' then all_p ELSE '0' END) AS p1
                        ,sum(case when Personnel_Type='ลูกจ้างของมหาวิทยาลัย' then all_p ELSE '0' END) AS p5
                        ,sum(case when Personnel_Type='ลูกจ้างประจำ' then all_p ELSE '0' END) AS p4
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได้' then all_p ELSE '0' END) AS p3
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินงบประมาณ' then all_p ELSE '0' END) AS p2
                        FROM t7 
                        WHERE Y='y3'
                        GROUP BY Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default)
                        ,t11 AS (
                        SELECT Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default
                        ,sum(case when Personnel_Type='ข้าราชการ' then all_p ELSE '0' END) AS p1
                        ,sum(case when Personnel_Type='ลูกจ้างของมหาวิทยาลัย' then all_p ELSE '0' END) AS p5
                        ,sum(case when Personnel_Type='ลูกจ้างประจำ' then all_p ELSE '0' END) AS p4
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินรายได้' then all_p ELSE '0' END) AS p3
                        ,sum(case when Personnel_Type='พนักงานมหาวิทยาลัยเงินงบประมาณ' then all_p ELSE '0' END) AS p2
                        FROM t7 
                        WHERE Y='y4'
                        GROUP BY Faculty,POSITION,Job_Family,all_position_types,y,Alias_Default)
                        ,t12 AS (
                        SELECT t.*,tt.Alias_Default as Alias_Default_y2,tt.Faculty as Faculty_y2 , tt.POSITION as POSITION_y2,tt.Job_Family as Job_Family_y2
                        ,tt.all_position_types as all_position_types_y2,tt.y AS y2,tt.p1 AS p1_y2,tt.p2 AS p2_y2,tt.p3 AS p3_y2,tt.p4 AS p4_y2,tt.p5 AS p5_y2
                        FROM t8 t
                        LEFT JOIN t9 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        UNION
                        SELECT t.*,tt.Alias_Default as Alias_Default_y2,tt.Faculty as Faculty_y2 , tt.POSITION as POSITION_y2,tt.Job_Family as Job_Family_y2
                        ,tt.all_position_types as all_position_types_y2,tt.y AS y2,tt.p1 AS p1_y2,tt.p2 AS p2_y2,tt.p3 AS p3_y2,tt.p4 AS p4_y2,tt.p5 AS p5_y2
                        FROM t8 t
                        RIGHT JOIN t9 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        WHERE t.Faculty IS NULL)
                        ,t13 AS (
                        SELECT t.*,tt.Alias_Default as Alias_Default_y3,tt.Faculty as Faculty_y3 , tt.POSITION as POSITION_y3,tt.Job_Family as Job_Family_y3
                        ,tt.all_position_types as all_position_types_y3,tt.y AS y3,tt.p1 AS p1_y3,tt.p2 AS p2_y3,tt.p3 AS p3_y3,tt.p4 AS p4_y3,tt.p5 AS p5_y3
                        FROM t12 t
                        LEFT JOIN t10 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        UNION
                        SELECT t.*,tt.Alias_Default as Alias_Default_y3,tt.Faculty as Faculty_y3 , tt.POSITION as POSITION_y3,tt.Job_Family as Job_Family_y3
                        ,tt.all_position_types as all_position_types_y3,tt.y AS y3,tt.p1 AS p1_y3,tt.p2 AS p2_y3,tt.p3 AS p3_y3,tt.p4 AS p4_y3,tt.p5 AS p5_y3
                        FROM t12 t
                        RIGHT JOIN t10 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        WHERE t.Faculty IS NULL)
                        ,t14 AS (
                        SELECT t.*,tt.Alias_Default as Alias_Default_y4,tt.Faculty as Faculty_y4 , tt.POSITION as POSITION_y4,tt.Job_Family as Job_Family_y4
                        ,tt.all_position_types as all_position_types_y4,tt.y AS y4,tt.p1 AS p1_y4,tt.p2 AS p2_y4,tt.p3 AS p3_y4,tt.p4 AS p4_y4,tt.p5 AS p5_y4
                        FROM t13 t
                        LEFT JOIN t11 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        UNION
                        SELECT t.*,tt.Alias_Default as Alias_Default_y4,tt.Faculty as Faculty_y4 , tt.POSITION as POSITION_y4,tt.Job_Family as Job_Family_y4
                        ,tt.all_position_types as all_position_types_y4,tt.y AS y4,tt.p1 AS p1_y4,tt.p2 AS p2_y4,tt.p3 AS p3_y4,tt.p4 AS p4_y4,tt.p5 AS p5_y4
                        FROM t13 t
                        RIGHT JOIN t11 tt
                        ON t.Faculty = tt.Faculty 
                        AND t.POSITION = tt.POSITION 
                        AND t.all_position_types = tt.all_position_types 
                        WHERE t.Faculty IS NULL)


                        SELECT t.* ,f2.Alias_Default AS pname
								FROM t14 t
                        LEFT JOIN (SELECT DISTINCT Faculty, Alias_Default ,parent
                        FROM Faculty
                        where parent like 'Faculty%') f 
								ON (t.Faculty = f.Faculty OR t.Faculty_y2 = f.Faculty OR t.Faculty_y3 = f.Faculty OR t.Faculty_y4 = f.Faculty  ) COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default
                        FROM Faculty) f2 
                        ON f.parent = f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        ORDER BY f2.Alias_Default,t.faculty,t.Faculty_y2,t.Faculty_y3,t.Faculty_y4,t.y";
                $cmd = $conn->prepare($sql);
                //$cmd->bindParam(':slt', $slt, PDO::PARAM_STR);
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
        case "expense-prediction-wf":
            try {
                $db = new Database();
                $conn = $db->connect();
                //$slt = $_POST["slt"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS(
                        SELECT w.*,f.Alias_Default,f2.Alias_Default AS pname
                        FROM workforce_hcm_actual w
                        LEFT JOIN (SELECT * from Faculty 
                        WHERE Parent LIKE 'Faculty%') f
                        ON w.faculty=f.faculty
                        LEFT JOIN Faculty f2
                        ON f.parent=f2.faculty)
                        ,t2 AS (
                        SELECT COALESCE(pname,'No Personnel Type') AS pname
                        ,faculty
								,Alias_Default
                        ,position
                        ,COUNT(*) AS position_count
                        ,sum(COALESCE(SALARY_RATE,0)) AS SALARY_RATE
                        ,sum(COALESCE(POSITION_COMPENSATION_MNGT_ACADEMIC,0)) AS pc
                        ,sum(COALESCE(FULL_SALARY_COMPENSATION,0)) AS fa
                        ,sum(COALESCE(EXECUTIVE_COMPENSATION,0)) AS ec
                        ,sum(COALESCE(POSITION_CAR_ALLOWANCE,0)) AS pca
                        FROM t1
                        GROUP BY  COALESCE(pname,'No Personnel Type')
                        ,faculty
								,Alias_Default
                        ,position)
                        SELECT * FROM t2
                        WHERE Alias_Default IS NOT null
                        ORDER BY pname,Alias_Default";
                $cmd = $conn->prepare($sql);
                //$cmd->bindParam(':slt', $slt, PDO::PARAM_STR);
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