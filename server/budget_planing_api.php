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
                /* $sql = "WITH t1 AS(
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
                        ON t.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        order by f.Alias_Default"; */
                        $sql = "WITH t1 AS(
                        SELECT b.*,a.type FROM budget_planning_annual_budget_plan b
                        LEFT JOIN account a
                        ON b.Account=a.account)
                        , t2 AS(
                        SELECT Faculty
                        ,type
                        ,SUM(Total_Amount_Quantity) AS Total_Amount
                        FROM t1
                        GROUP BY Faculty
                        ,type)
                        ,t3 AS(
                        SELECT Faculty
                        ,sum(case when type='1.เงินอุดหนุนจากรัฐ' then Total_Amount ELSE 0 END) AS a1
                        ,sum(case when type='2.เงินและทรัพย์สินซึ่งมีผู้อุทิศให้แก่มหาวิทยาลัย' then Total_Amount ELSE 0 END) AS a2
                        ,sum(case when type='3.เงินกองทุนที่รัฐบาลหรือมหาวิทยาลัยจัดตั้งขึ้นและรายได้หรือผลประโยชน์จากกองทุน' then Total_Amount ELSE 0 END) AS a3
                        ,sum(case when type='4.ค่าธรรมเนียม ค่าบำรุง ค่าตอบแทน เบี้ยปรับ และค่าบริการต่างๆของมหาวิทยาลัย' then Total_Amount ELSE 0 END) AS a4
                        ,sum(case when type='5. รายได้หรือผลประโยชน์ที่ได้มาจากการลงทุนหรือการร่วมลงทุนจากทรัพย์สินของมหาวิทยาลัย' then Total_Amount ELSE 0 END) AS a5
                        ,sum(case when type='6. รายได้หรือผลประโยชน์ที่ได้มาจากการใช้ที่ราชพัสดุหรือจัดหาประโยชน์ในที่ราชพัสดุที่มหาวิทยาลัยปกครอง ดูแล ใช้ หรือจัดหาประโยชน์' then Total_Amount ELSE 0 END) AS a6
                        ,sum(case when type='7.เงินอุดหนุนจากหน่วยงานภายนอก' then Total_Amount ELSE 0 END) AS a7
                        ,sum(case when type='8.เงินและผลประโยชน์ที่ได้รับจากการบริการวิชาการ การวิจัย และนำทรัพย์สินทางปัญญาไปหาประโยชน์' then Total_Amount ELSE 0 END) AS a8
                        ,sum(case when type='9.รายได้ผลประโยชน์อย่างอื่น' then Total_Amount ELSE 0 END) AS a9
                        FROM t2
                        GROUP BY Faculty)

                        SELECT t.*,f.Alias_Default,f2.Alias_Default AS pname
								FROM t3 t
                        LEFT JOIN (
                        SELECT DISTINCT Faculty, Alias_Default,parent 
                        FROM Faculty
								WHERE parent LIKE 'Faculty%') f 
                        ON t.Faculty = f.Faculty COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN Faculty f2
                        ON f.parent=f2.Faculty COLLATE UTF8MB4_GENERAL_CI
                        order by f.Alias_Default";
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
                $sql = "WITH RECURSIVE account_hierarchy AS (
    -- Anchor member: Start with all accounts that have a parent
    SELECT 
        a1.account,
        a1.account AS acc_id,
        a1.alias_default AS alias,
        a1.parent,
        1 AS level
    FROM account a1
    WHERE a1.parent IS NOT NULL
    
    UNION ALL
    
    -- Recursive member: Find parent accounts
    SELECT 
        ah.account,
        a2.account AS acc_id,
        a2.alias_default AS alias,
        a2.parent,
        ah.level + 1 AS level
    FROM account_hierarchy ah
    JOIN account a2 
        ON ah.parent = a2.account COLLATE UTF8MB4_GENERAL_CI
    WHERE a2.parent IS NOT NULL
    AND ah.level < 6 -- Maximum 6 levels (increased from 5)
),

-- Get the maximum level for each account to determine total depth
max_levels AS (
    SELECT account, MAX(level) AS max_level
    FROM account_hierarchy
    GROUP BY account
),

-- Create a pivot table with all levels for each account
hierarchy_pivot AS (
    SELECT 
        h.account,
        m.max_level,
        MAX(CASE WHEN h.level = 1 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level1_value,
        MAX(CASE WHEN h.level = 2 THEN CONCAT(h.acc_id, ' : ',REGEXP_REPLACE( h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level2_value,
        MAX(CASE WHEN h.level = 3 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level3_value,
        MAX(CASE WHEN h.level = 4 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level4_value,
        MAX(CASE WHEN h.level = 5 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level5_value,
        MAX(CASE WHEN h.level = 6 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level6_value
    FROM account_hierarchy h
    JOIN max_levels m ON h.account = m.account
    GROUP BY h.account, m.max_level
),

-- Shift the hierarchy to the left (compact it)
shifted_hierarchy AS (
    SELECT
        account AS current_acc,
        max_level AS TotalLevels,
        CASE 
            WHEN max_level = 1 THEN level1_value
            WHEN max_level = 2 THEN level2_value
            WHEN max_level = 3 THEN level3_value
            WHEN max_level = 4 THEN level4_value
            WHEN max_level = 5 THEN level5_value
            WHEN max_level = 6 THEN level6_value
        END AS level6,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN level1_value
            WHEN max_level = 3 THEN level2_value
            WHEN max_level = 4 THEN level3_value
            WHEN max_level = 5 THEN level4_value
            WHEN max_level = 6 THEN level5_value
        END AS level5,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN level1_value
            WHEN max_level = 4 THEN level2_value
            WHEN max_level = 5 THEN level3_value
            WHEN max_level = 6 THEN level4_value
        END AS level4,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN level1_value
            WHEN max_level = 5 THEN level2_value
            WHEN max_level = 6 THEN level3_value
        END AS level3,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN NULL
            WHEN max_level = 5 THEN level1_value
            WHEN max_level = 6 THEN level2_value
        END AS level2,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN NULL
            WHEN max_level = 5 THEN NULL
            WHEN max_level = 6 THEN level1_value
        END AS level1
    FROM hierarchy_pivot
)
,t1 AS (
                        SELECT faculty,Alias_Default FROM Faculty 
                        WHERE parent='Total KKU' AND Faculty !='Faculty-00')
                        ,t1_1 AS (
                        SELECT account,alias_default,TYPE,sub_type
                        FROM account
                            where id > (SELECT id FROM account WHERE parent = 'Expenses'))
                        ,t2 AS (
                        SELECT b.*,f.parent,replace(f.Alias_Default,'-',':') AS f2
                        FROM budget_planning_allocated_annual_budget_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
                        WHERE f.parent NOT LIKE '%BU%' AND b.fund IN ('FN06','FN02'))
                        ,t2_1 AS (
                        SELECT t.*,tt.alias_default AS account_name,tt.type,tt.sub_type
                        FROM t2 t
                        LEFT JOIN t1_1 tt
                        ON t.account=tt.account
                            WHERE tt.alias_default IS NOT NULL )
                        ,t3 AS (
                        SELECT t.faculty,t.fund,t.plan,t.sub_plan,t.KKU_Item_Name,t.type,t.sub_type,t.account,t.service
                        ,t.project
                        ,SUM(Allocated_Total_Amount_Quantity) AS Allocated_Total_Amount_Quantity
                        ,tt.Alias_Default AS f1
                        ,t.f2
                        FROM t2_1 t
                        LEFT JOIN t1 tt
                        ON t.parent=tt.faculty
                        GROUP BY t.faculty,t.fund,t.plan,t.sub_plan,t.KKU_Item_Name,t.type,t.sub_type,t.account
                        ,t.project,tt.Alias_Default,t.f2,t.service)
                        ,t4 AS (
                        SELECT Faculty,fund,plan,subplan,project,account,service
                        ,SUM(COMMITMENTS) AS COMMITMENTS
                        ,SUM(OBLIGATIONS) AS OBLIGATIONS
                        ,SUM(EXPENDITURES) AS EXPENDITURES
                        FROM budget_planning_actual
                        GROUP BY Faculty,fund,plan,subplan,project,account,service)
                        ,t5 AS (
                        SELECT t.*,a.COMMITMENTS,a.OBLIGATIONS,a.EXPENDITURES
                        FROM t3 t
                        LEFT JOIN t4 a
                        ON t.faculty=a.FACULTY 
                            AND (t.fund= CONCAT('FN',a.Fund) or t.fund=a.Fund)
                            AND t.plan=a.plan 
                        AND t.sub_plan=CONCAT('SP_',a.SUBPLAN) 
                        AND t.project=a.project 
                            AND t.account=a.account
                            AND replace(t.service,'SR_','')=a.service)
                        , t6 AS (
                        SELECT Faculty,plan,replace(sub_plan,'SP_','') AS sub_plan,project,f1,f2,KKU_Item_Name,TYPE AS TYPE2,sub_type AS sub_type2,account AS account2
                        ,sum(case when fund='FN02' then COALESCE(Allocated_Total_Amount_Quantity,0) ELSE 0 END) AS a2
                        ,sum(case when fund='FN02' then COALESCE(COMMITMENTS,0) ELSE 0 END) AS c2
                        ,sum(case when fund='FN02' then COALESCE(OBLIGATIONS,0) ELSE 0 END) AS o2
                        ,sum(case when fund='FN02' then COALESCE(EXPENDITURES,0) ELSE 0 END) AS e2
                        ,sum(case when fund='FN06' then COALESCE(Allocated_Total_Amount_Quantity,0) ELSE 0 END) AS a6
                        ,sum(case when fund='FN06' then COALESCE(COMMITMENTS,0) ELSE 0 END) AS c6
                        ,sum(case when fund='FN06' then COALESCE(OBLIGATIONS,0) ELSE 0 END) AS o6
                        ,sum(case when fund='FN06' then COALESCE(EXPENDITURES,0) ELSE 0 END) AS e6
                        FROM t5
                        GROUP BY Faculty,plan,replace(sub_plan,'SP_',''),project,f1,f2,KKU_Item_Name,type,sub_type,account)
                        ,t7 AS (
                        SELECT t.*,p.plan_name,CONCAT(t.sub_plan,' : ',sp.sub_plan_name) AS sub_plan_name,pr.project_name ,a.id AS account,aa.id AS sub_account
                        FROM t6 t
                        LEFT JOIN plan p
                        ON t.plan = p.plan_id
                        LEFT JOIN sub_plan sp
                        on t.sub_plan=replace(sp.sub_plan_id,'SP_','')
                        LEFT JOIN project pr
                        ON t.project=pr.project_id
                        LEFT JOIN account a
                        ON t.type2=a.alias_default COLLATE UTF8MB4_GENERAL_CI
                        LEFT JOIN account aa
                        ON t.sub_type2=aa.alias_default COLLATE UTF8MB4_GENERAL_CI)
								,t8 AS (
								SELECT t.*,a.account AS atype,a2.account AS asubtype, a3.alias_default AS aname
								FROM t7 t
								LEFT JOIN account a
								ON t.type2 =a.alias_default COLLATE UTF8MB4_GENERAL_CI
								LEFT JOIN account a2
								ON t.sub_type2 =a2.alias_default COLLATE UTF8MB4_GENERAL_CI
								LEFT JOIN account a3
								ON t.account2 =a3.account COLLATE UTF8MB4_GENERAL_CI)
								,t9 AS(
                        SELECT *,CONCAT(atype,' : ',REGEXP_REPLACE(TYPE2, '^[0-9]+(\\.[0-9]+)*[\\.\\s]+', '')) AS TYPE
								,CONCAT(asubtype,' : ',REGEXP_REPLACE(sub_type2, '^[0-9]+(\\.[0-9]+)*[\\.\\s]+', '')) AS sub_type
								,CONCAT(account2,' : ',REGEXP_REPLACE(aname, '^[0-9]+(\\.[0-9]+)*[\\.\\s]+', '')) AS accname 
								,CASE 
							        WHEN KKU_Item_Name IS NOT NULL AND KKU_Item_Name != '' 
							        THEN CONCAT(account2, ' : ', REGEXP_REPLACE(KKU_Item_Name, '^[0-9]+(\\.[0-9]+)*[\\.\\s]+', ''))
							        ELSE NULL 
							    END AS KKU_Item_Name2 
								FROM t8
                        ORDER BY Faculty,plan,sub_plan,project,account,sub_account)
                     	,t10 AS (
								SELECT t.*,h.*
								FROM t9 t
								LEFT JOIN shifted_hierarchy h
								ON t.account2=h.current_acc)
								
								
								SELECT * FROM t10";
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
                $sql = "WITH RECURSIVE account_hierarchy AS (
    -- Anchor member: Start with all accounts that have a parent
    SELECT 
        a1.account,
        a1.account AS acc_id,
        a1.alias_default AS alias,
        a1.parent,
        1 AS level
    FROM account a1
    WHERE a1.parent IS NOT NULL
    
    UNION ALL
    
    -- Recursive member: Find parent accounts
    SELECT 
        ah.account,
        a2.account AS acc_id,
        a2.alias_default AS alias,
        a2.parent,
        ah.level + 1 AS level
    FROM account_hierarchy ah
    JOIN account a2 
        ON ah.parent = a2.account COLLATE UTF8MB4_GENERAL_CI
    WHERE a2.parent IS NOT NULL
    AND ah.level < 6 -- Maximum 6 levels (increased from 5)
),

-- Get the maximum level for each account to determine total depth
max_levels AS (
    SELECT account, MAX(level) AS max_level
    FROM account_hierarchy
    GROUP BY account
),

-- Create a pivot table with all levels for each account
hierarchy_pivot AS (
    SELECT 
        h.account,
        m.max_level,
        MAX(CASE WHEN h.level = 1 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level1_value,
        MAX(CASE WHEN h.level = 2 THEN CONCAT(h.acc_id, ' : ',REGEXP_REPLACE( h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level2_value,
        MAX(CASE WHEN h.level = 3 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level3_value,
        MAX(CASE WHEN h.level = 4 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level4_value,
        MAX(CASE WHEN h.level = 5 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level5_value,
        MAX(CASE WHEN h.level = 6 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level6_value
    FROM account_hierarchy h
    JOIN max_levels m ON h.account = m.account
    GROUP BY h.account, m.max_level
),

-- Shift the hierarchy to the left (compact it)
shifted_hierarchy AS (
    SELECT
        account AS current_acc,
        max_level AS TotalLevels,
        CASE 
            WHEN max_level = 1 THEN level1_value
            WHEN max_level = 2 THEN level2_value
            WHEN max_level = 3 THEN level3_value
            WHEN max_level = 4 THEN level4_value
            WHEN max_level = 5 THEN level5_value
            WHEN max_level = 6 THEN level6_value
        END AS level6,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN level1_value
            WHEN max_level = 3 THEN level2_value
            WHEN max_level = 4 THEN level3_value
            WHEN max_level = 5 THEN level4_value
            WHEN max_level = 6 THEN level5_value
        END AS level5,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN level1_value
            WHEN max_level = 4 THEN level2_value
            WHEN max_level = 5 THEN level3_value
            WHEN max_level = 6 THEN level4_value
        END AS level4,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN level1_value
            WHEN max_level = 5 THEN level2_value
            WHEN max_level = 6 THEN level3_value
        END AS level3,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN NULL
            WHEN max_level = 5 THEN level1_value
            WHEN max_level = 6 THEN level2_value
        END AS level2,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN NULL
            WHEN max_level = 5 THEN NULL
            WHEN max_level = 6 THEN level1_value
        END AS level1
    FROM hierarchy_pivot
),t1 AS (
                        SELECT b.Faculty
                        ,sum(case when b.Fund='FN06' then b.Total_Amount_Quantity ELSE 0 END) AS t06
                        ,sum(case when b.Fund='FN02' then b.Total_Amount_Quantity ELSE 0 END) AS t02
                        ,sum(case when b.Fund='FN08' then b.Total_Amount_Quantity ELSE 0 END) AS t08
                        ,b.Account AS account2
                        ,b.KKU_Item_Name
                        ,b.Budget_Management_Year
                        ,b2.KKU_Strategic_Plan_LOV
                        ,REGEXP_REPLACE(p.pillar_name, '(SI[0-9]+) ', '$1 : ') as pillar_name
                        ,a.`type` AS TYPE2
                        ,a.sub_type AS sub_type2
                        ,a.id AS p_id
                        ,replace(f.Alias_Default,'-',':') as Alias_Default
                        ,f2.Alias_Default AS pname
                        FROM budget_planning_annual_budget_plan b
                        LEFT JOIN budget_planning_project_kpi b2
                        ON b.Faculty=b2.Faculty AND b.Project=b2.Project
                        LEFT JOIN pilars2 p
                        ON b2.KKU_Strategic_Plan_LOV=p.pillar_id
                        LEFT JOIN account a
                        ON b.Account=a.account
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
                        LEFT JOIN Faculty f2
                        ON f.parent=f2.Faculty
                        WHERE a.id > (SELECT id FROM account WHERE parent = 'Expenses')
                        GROUP BY b.Faculty
                        ,b.Account
                        ,b.KKU_Item_Name
                        ,b.Budget_Management_Year
                        ,b2.KKU_Strategic_Plan_LOV
                        ,REGEXP_REPLACE(p.pillar_name, '(SI[0-9]+) ', '$1 : ')
                        ,a.`type`
                        ,a.sub_type
                        ,a.id
                        ,f.Alias_Default
								,f2.Alias_Default)
,t2 AS (
								SELECT t.*,a.account AS atype,a2.account AS asubtype, a3.alias_default AS aname
								FROM t1 t
								LEFT JOIN account a
								ON t.type2 =a.alias_default COLLATE UTF8MB4_GENERAL_CI
								LEFT JOIN account a2
								ON t.sub_type2 =a2.alias_default COLLATE UTF8MB4_GENERAL_CI
								LEFT JOIN account a3
								ON t.account2 =a3.account COLLATE UTF8MB4_GENERAL_CI)
								,t3 AS (
                        SELECT *
								,CONCAT(atype,' : ',REGEXP_REPLACE(TYPE2, '^[0-9]+(\\.[0-9]+)*\\.\\s*', '')) AS TYPE
								,CONCAT(asubtype,' : ',REGEXP_REPLACE(sub_type2, '^[0-9]+(\\.[0-9]+)*\\.\\s*', '')) AS sub_type
								,CONCAT(account2,' : ',REGEXP_REPLACE(aname, '^[0-9]+(\\.[0-9]+)*\\.\\s*', '')) AS accname 
								,CASE 
							        WHEN KKU_Item_Name IS NOT NULL AND KKU_Item_Name != '' 
							        THEN CONCAT(account2, ' : ', REGEXP_REPLACE(KKU_Item_Name, '^[0-9]+(\\.[0-9]+)*\\.\\s*', ''))
							        ELSE NULL 
							    END AS KKU_Item_Name2 
								FROM t2
                        ORDER BY Faculty,KKU_Strategic_Plan_LOV,p_id)
                        ,t4 AS (SELECT t.*,h.*
								FROM t3 t
								LEFT JOIN shifted_hierarchy h
								ON t.account2=h.current_acc)
								
								
								SELECT * FROM t4
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
                        SELECT t.* ,f.Alias_Default,f2.Alias_Default AS pname
                        FROM t1 t
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON t.faculty=f.faculty
								left JOIN Faculty f2
								ON f.parent=f2.Faculty)
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
                        ON replace(tt.OKRs_LOV,'_','-')=ok.okr_id)

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
                $scenario = $_POST["scenario"];
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT fund AS f 
                        FROM budget_planning_annual_budget_plan 
                        WHERE Budget_Management_Year=:fyear and scenario=:scenario";
                $cmd = $conn->prepare($sql);
                $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->bindParam(':scenario', $scenario, PDO::PARAM_STR);
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
                $scenario = $_POST["scenario"];
                $fund = $_POST["fund"];
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT f.Alias_Default AS faculty 
                        FROM budget_planning_annual_budget_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
                        WHERE b.Budget_Management_Year = :fyear
                        AND b.fund = :fund
                        and b.scenario=:scenario";

                $cmd = $conn->prepare($sql);
                $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->bindParam(':fund', $fund, PDO::PARAM_STR);
                $cmd->bindParam(':scenario', $scenario, PDO::PARAM_STR);
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
                $scenario = $_POST["scenario"];
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT f.Alias_Default AS faculty 
                        FROM budget_planning_annual_budget_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
                        WHERE b.Budget_Management_Year = :fyear and b.scenario=:scenario";

                $cmd = $conn->prepare($sql);
                $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->bindParam(':scenario', $scenario, PDO::PARAM_STR);
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
        case "get_scenario":
            try {
                $fyear = $_POST["fiscal_year"];
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT DISTINCT scenario
                        FROM budget_planning_annual_budget_plan b                   
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
                $scenario = $_POST["scenario"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS (
                        SELECT b.*,f.Alias_Default
                        FROM budget_planning_annual_budget_plan b
                        LEFT JOIN (SELECT * from Faculty WHERE parent LIKE 'Faculty%') f
                        ON b.faculty=f.faculty
                        
                        WHERE b.Budget_Management_Year= :fyear AND b.fund= :fund AND f.Alias_Default= :faculty and b.scenario=:scenario)
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
                        ON replace(b2.OKRs_LOV,'_','-')=ok.okr_id)
                        ,t4 AS (
                        SELECT t.*,pl.plan_name,CONCAT(replace(t.sub_plan,'SP_',''),' : ',sp.sub_plan_name) AS sub_plan_name
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
                $cmd->bindParam(':scenario', $scenario, PDO::PARAM_STR);
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
                $scenario = $_POST["scenario"];
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
                where b.Budget_Management_Year=:fyear and b.scenario=:scenario and f.Alias_Default=:faculty)
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
                $cmd->bindParam(':scenario', $scenario, PDO::PARAM_STR);
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
                $scenario = $_POST["scenario"];
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
                where b.Budget_Management_Year=:fyear and b.scenario=:scenario and f.Alias_Default=:faculty)
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
                $cmd->bindParam(':scenario', $scenario, PDO::PARAM_STR);
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
        case "get_budget-remaining":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH t1 AS (

                SELECT distinct sub_type,'expense' AS ftype FROM account
                WHERE id > (SELECT id FROM account WHERE parent='Expenses') AND sub_type IS NOT NULL AND sub_type NOT LIKE '%.%.%')
                ,t2 AS (
                SELECT Faculty,plan,sub_plan,project,fund,service,kku_item_name,account,scenario 
                FROM budget_planning_allocated_annual_budget_plan b
                GROUP BY Faculty,plan,sub_plan,project,fund,service,kku_item_name,account,scenario )
                ,t3 AS (
                SELECT Faculty,plan,subplan,project,fund,service,account,fiscal_year,BUDGET_PERIOD,scenario 
                ,sum(COMMITMENTS) AS COMMITMENTS
                ,sum(OBLIGATIONS) AS OBLIGATIONS
                ,sum(EXPENDITURES) AS EXPENDITURES
                ,sum(BUDGET_ADJUSTMENTS) AS BUDGET_ADJUSTMENTS
                ,sum(INITIAL_BUDGET) AS INITIAL_BUDGET
                FROM budget_planning_actual b
                GROUP BY Faculty,plan,subplan,project,fund,service,account,fiscal_year,BUDGET_PERIOD,scenario )
                ,t4 AS ( 
                SELECT tt.*,t.kku_item_name
                FROM t2 t
                LEFT JOIN t3 tt
                ON t.Faculty=tt.FACULTY AND (REPLACE(t.Fund,'FN','')= tt.FUND or t.Fund= tt.FUND)
                AND t.Plan=tt.PLAN AND replace(t.Sub_Plan,'SP_','')=tt.SUBPLAN
                AND t.Project=tt.PROJECT AND replace(t.Service,'SR_','')=tt.SERVICE
                AND t.Account=tt.ACCOUNT)
                ,t5 AS (
                SELECT t.*,a.account AS aname,a.sub_type,a.parent
                ,case when aa.alias_default LIKE '%.%.%' then a.sub_type 
                when aa.alias_default is null then a.sub_type
                ELSE aa.alias_default END  AS pname
                FROM t4 t
                LEFT JOIN account a
                ON t.account=a.account
                LEFT JOIN account aa
                ON a.parent=aa.account COLLATE UTF8MB4_GENERAL_CI)
                ,t6 AS (
                SELECT Faculty,plan,subplan,project,fund,service,fiscal_year,kku_item_name,pname
                ,sum(COMMITMENTS) AS COMMITMENTS
                ,sum(OBLIGATIONS) AS OBLIGATIONS
                ,sum(EXPENDITURES) AS EXPENDITURES
                ,sum(BUDGET_ADJUSTMENTS) AS BUDGET_ADJUSTMENTS
                ,sum(INITIAL_BUDGET) AS INITIAL_BUDGET
                FROM t5
                GROUP BY Faculty,plan,subplan,project,fund,service,fiscal_year,kku_item_name,pname)
                ,t7 AS (
                SELECT t.sub_type AS smain,t.ftype ,tt.*
                FROM t1 t
                LEFT JOIN t5 tt
                ON t.sub_type=tt.pname COLLATE UTF8MB4_GENERAL_CI)
                ,t8 AS (
                SELECT t.*,f.Alias_Default AS fname,p.plan_name,sp.sub_plan_name,pro.project_name
                FROM t7 t
                LEFT JOIN (SELECT faculty,Alias_Default FROM Faculty WHERE parent LIKE'Faculty%') f
                ON t.faculty=f.faculty
                LEFT JOIN plan p
                on t.plan=p.plan_id
                LEFT JOIN sub_plan sp
                ON t.subplan=replace(sp.sub_plan_id,'SP_','')
                LEFT JOIN project pro
                ON t.project=pro.project_id
					 ORDER BY t.smain)
                SELECT* FROM t8";

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
        case "report_budget-remaining":
            try {
                $db = new Database();
                $conn = $db->connect();

                $fyear = $_POST["fiscal_year"];
                $faculty = $_POST["faculty"];
                $scenario = $_POST["scenario"];
                $fund = $_POST["fund"];
                $plan = $_POST["plan"];
                $subplan = $_POST["subplan"];
                $project = $_POST["project"];
                $bgyear = $_POST["bgyear"];
                // เชื่อมต่อฐานข้อมูล
                $sql = "WITH RECURSIVE account_hierarchy AS (
    -- Anchor member: Start with all accounts that have a parent
    SELECT 
        a1.account,
        a1.account AS acc_id,
        a1.alias_default AS alias,
        a1.parent,
        1 AS level
    FROM account a1
    WHERE a1.parent IS NOT NULL
    
    UNION ALL
    
    -- Recursive member: Find parent accounts
    SELECT 
        ah.account,
        a2.account AS acc_id,
        a2.alias_default AS alias,
        a2.parent,
        ah.level + 1 AS level
    FROM account_hierarchy ah
    JOIN account a2 
        ON ah.parent = a2.account COLLATE UTF8MB4_GENERAL_CI
    WHERE a2.parent IS NOT NULL
    AND ah.level < 6 -- Maximum 6 levels (increased from 5)
),

-- Get the maximum level for each account to determine total depth
max_levels AS (
    SELECT account, MAX(level) AS max_level
    FROM account_hierarchy
    GROUP BY account
),

-- Create a pivot table with all levels for each account
hierarchy_pivot AS (
    SELECT 
        h.account,
        m.max_level,
        MAX(CASE WHEN h.level = 1 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level1_value,
        MAX(CASE WHEN h.level = 2 THEN CONCAT(h.acc_id, ' : ',REGEXP_REPLACE( h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level2_value,
        MAX(CASE WHEN h.level = 3 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level3_value,
        MAX(CASE WHEN h.level = 4 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level4_value,
        MAX(CASE WHEN h.level = 5 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level5_value,
        MAX(CASE WHEN h.level = 6 THEN CONCAT(h.acc_id, ' : ', REGEXP_REPLACE(h.alias, '^[0-9]+(.[0-9]+)*[. ]+', '')) END) AS level6_value
    FROM account_hierarchy h
    JOIN max_levels m ON h.account = m.account
    GROUP BY h.account, m.max_level
),

-- Shift the hierarchy to the left (compact it)
shifted_hierarchy AS (
    SELECT
        account AS current_acc,
        max_level AS TotalLevels,
        CASE 
            WHEN max_level = 1 THEN level1_value
            WHEN max_level = 2 THEN level2_value
            WHEN max_level = 3 THEN level3_value
            WHEN max_level = 4 THEN level4_value
            WHEN max_level = 5 THEN level5_value
            WHEN max_level = 6 THEN level6_value
        END AS level6,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN level1_value
            WHEN max_level = 3 THEN level2_value
            WHEN max_level = 4 THEN level3_value
            WHEN max_level = 5 THEN level4_value
            WHEN max_level = 6 THEN level5_value
        END AS level5,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN level1_value
            WHEN max_level = 4 THEN level2_value
            WHEN max_level = 5 THEN level3_value
            WHEN max_level = 6 THEN level4_value
        END AS level4,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN level1_value
            WHEN max_level = 5 THEN level2_value
            WHEN max_level = 6 THEN level3_value
        END AS level3,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN NULL
            WHEN max_level = 5 THEN level1_value
            WHEN max_level = 6 THEN level2_value
        END AS level2,
        CASE 
            WHEN max_level = 1 THEN NULL
            WHEN max_level = 2 THEN NULL
            WHEN max_level = 3 THEN NULL
            WHEN max_level = 4 THEN NULL
            WHEN max_level = 5 THEN NULL
            WHEN max_level = 6 THEN level1_value
        END AS level1
    FROM hierarchy_pivot
)

                ,t2 AS (
                SELECT Faculty,plan,sub_plan,project,fund,service,CASE 
							        WHEN KKU_Item_Name IS NOT NULL AND KKU_Item_Name != '' 
							        THEN CONCAT(account, ' : ', REGEXP_REPLACE(KKU_Item_Name, '^[0-9]+(\\.[0-9]+)*[\\.\\s]+', ''))
							        ELSE NULL 
							    END AS kku_item_name,account,scenario 
                FROM budget_planning_allocated_annual_budget_plan b
                GROUP BY Faculty,plan,sub_plan,project,fund,service,CASE 
							        WHEN KKU_Item_Name IS NOT NULL AND KKU_Item_Name != '' 
							        THEN CONCAT(account, ' : ', REGEXP_REPLACE(KKU_Item_Name, '^[0-9]+(\\.[0-9]+)*[\\.\\s]+', ''))
							        ELSE NULL 
							    END,account,scenario )
                ,t3 AS (
                SELECT Faculty,plan,subplan,project,fund,service,account,fiscal_year,BUDGET_PERIOD,scenario 
                ,SUM(COMMITMENTS) AS COMMITMENTS
                ,SUM(OBLIGATIONS) AS OBLIGATIONS
                ,SUM(EXPENDITURES) AS EXPENDITURES
                ,SUM(CASE WHEN BUDGET_ADJUSTMENTS > 0 THEN BUDGET_ADJUSTMENTS ELSE 0 END) AS adj_in
                ,SUM(CASE WHEN BUDGET_ADJUSTMENTS < 0 THEN BUDGET_ADJUSTMENTS ELSE 0 END) AS adj_out
                ,SUM(INITIAL_BUDGET) AS INITIAL_BUDGET
                ,SUM(FUNDS_AVAILABLE_AMOUNT) AS FUNDS_AVAILABLE_AMOUNT
                FROM budget_planning_actual b
                GROUP BY Faculty,plan,subplan,project,fund,service,account,fiscal_year,BUDGET_PERIOD,scenario)
                ,t4 AS ( 
                SELECT tt.*,t.kku_item_name
                FROM t2 t
                LEFT JOIN t3 tt
                ON t.Faculty=tt.FACULTY AND (REPLACE(t.Fund,'FN','')= tt.FUND or t.Fund= tt.FUND)
                AND t.Plan=tt.PLAN AND replace(t.Sub_Plan,'SP_','')=tt.SUBPLAN
                AND t.Project=tt.PROJECT AND replace(t.Service,'SR_','')=tt.SERVICE
                AND t.Account=tt.ACCOUNT)
                ,t5 AS (
                SELECT t.*,a.account AS aname,a.sub_type,a.parent
                ,aa.alias_default AS pname
                FROM t4 t
                LEFT JOIN account a
                ON t.account=a.account
                LEFT JOIN account aa
                ON a.parent=aa.account COLLATE UTF8MB4_GENERAL_CI)
                ,t6 AS (
                SELECT Faculty,plan,subplan,project,fund,service,fiscal_year,kku_item_name,pname
                ,sum(COMMITMENTS) AS COMMITMENTS
                ,sum(OBLIGATIONS) AS OBLIGATIONS
                ,sum(EXPENDITURES) AS EXPENDITURES
                ,sum(adj_in) AS adj_in
                ,sum(adj_out) AS adj_out
                ,sum(INITIAL_BUDGET) AS INITIAL_BUDGET
                ,SUM(FUNDS_AVAILABLE_AMOUNT) AS FUNDS_AVAILABLE_AMOUNT
                FROM t5
                GROUP BY Faculty,plan,subplan,project,fund,service,fiscal_year,kku_item_name,pname)
                ,t7 AS (
                SELECT tt.*,a.id
                FROM  t5 tt
                LEFT JOIN account a
                ON tt.account=a.account COLLATE UTF8MB4_GENERAL_CI
					 WHERE a.id>(SELECT id FROM account WHERE parent='Expenses'))
                ,t8 AS (
                SELECT t.*,f.Alias_Default AS fname,p.plan_name,sp.sub_plan_name,pro.project_name
                FROM t7 t
                LEFT JOIN (SELECT faculty,Alias_Default FROM Faculty WHERE parent LIKE'Faculty%') f
                ON t.faculty=f.faculty
                LEFT JOIN plan p
                on t.plan=p.plan_id
                LEFT JOIN sub_plan sp
                ON t.subplan=replace(sp.sub_plan_id,'SP_','')
                LEFT JOIN project pro
                ON t.project=pro.project_id)
                ,t9 AS(
					 SELECT t.*,b.Release_Amount
					 FROM t8 t
					 LEFT JOIN budget_planning_disbursement_budget_plan_anl_release b
					 ON t.faculty=b.Faculty AND t.plan=b.plan
					 AND t.fund=b.fund AND t.subplan=replace(b.sub_plan,'SP_','') AND t.project=b.project
					 AND t.account=b.account AND t.service=replace(b.service,'SR_',''))
                
                SELECT * FROM t9 t
					 LEFT JOIN shifted_hierarchy h
					 ON t.account=h.current_acc
                
                ORDER BY id";

                $cmd = $conn->prepare($sql);
               /*  $cmd->bindParam(':fyear', $fyear, PDO::PARAM_STR);
                $cmd->bindParam(':faculty', $faculty, PDO::PARAM_STR);
                $cmd->bindParam(':scenario', $scenario, PDO::PARAM_STR);
                $cmd->bindParam(':fund', $fund, PDO::PARAM_STR);
                $cmd->bindParam(':plan', $plan, PDO::PARAM_STR);
                $cmd->bindParam(':subplan', $subplan, PDO::PARAM_STR);
                $cmd->bindParam(':project', $project, PDO::PARAM_STR);
                $cmd->bindParam(':bgyear', $bgyear, PDO::PARAM_STR); */
                
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