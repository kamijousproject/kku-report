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
                $sql = "WITH t1 AS (
                            SELECT b.*, a.type
                            FROM budget_planning_annual_budget_plan b
                            LEFT JOIN account a ON b.Account = a.account
                        ),
                        t2 AS (
                            SELECT 
                                Faculty,
                                type,
                                Scenario,
                                Budget_Management_Year,
                                SUM(Total_Amount_Quantity) AS Total_Amount
                            FROM t1
                            GROUP BY Faculty, type, Scenario, Budget_Management_Year
                        ),
                        t3 AS (
                            SELECT 
                                Faculty,
                                Scenario,
                                Budget_Management_Year,
                                SUM(CASE WHEN type = '1.เงินอุดหนุนจากรัฐ' THEN Total_Amount ELSE 0 END) AS a1,
                                SUM(CASE WHEN type = '2.เงินและทรัพย์สินซึ่งมีผู้อุทิศให้แก่มหาวิทยาลัย' THEN Total_Amount ELSE 0 END) AS a2,
                                SUM(CASE WHEN type = '3.เงินกองทุนที่รัฐบาลหรือมหาวิทยาลัยจัดตั้งขึ้น...' THEN Total_Amount ELSE 0 END) AS a3,
                                SUM(CASE WHEN type = '4.ค่าธรรมเนียม ค่าบำรุง ...' THEN Total_Amount ELSE 0 END) AS a4,
                                SUM(CASE WHEN type = '5. รายได้หรือผลประโยชน์จากการลงทุน...' THEN Total_Amount ELSE 0 END) AS a5,
                                SUM(CASE WHEN type = '6. รายได้จากการใช้ที่ราชพัสดุ...' THEN Total_Amount ELSE 0 END) AS a6,
                                SUM(CASE WHEN type = '7.เงินอุดหนุนจากหน่วยงานภายนอก' THEN Total_Amount ELSE 0 END) AS a7,
                                SUM(CASE WHEN type = '8.รายได้จากการบริการวิชาการ...' THEN Total_Amount ELSE 0 END) AS a8,
                                SUM(CASE WHEN type = '9.รายได้ผลประโยชน์อย่างอื่น' THEN Total_Amount ELSE 0 END) AS a9
                            FROM t2
                            GROUP BY Faculty, Scenario, Budget_Management_Year
                        )

                        SELECT 
                            t.*,
                            f.Alias_Default,
                            f2.Alias_Default AS pname
                        FROM t3 t
                        LEFT JOIN (
                            SELECT DISTINCT Faculty COLLATE DATABASE_DEFAULT AS Faculty, Alias_Default, Parent
                            FROM Faculty
                            WHERE Parent COLLATE DATABASE_DEFAULT LIKE 'Faculty%'
                        ) f 
                            ON t.Faculty COLLATE DATABASE_DEFAULT = f.Faculty
                        LEFT JOIN (
                            SELECT Faculty COLLATE DATABASE_DEFAULT AS Faculty, Alias_Default
                            FROM Faculty
                        ) f2
                            ON f.Parent COLLATE DATABASE_DEFAULT = f2.Faculty
                        ORDER BY t.Budget_Management_Year, f.Alias_Default;";
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
                $sql = "WITH account_hierarchy AS (
                            SELECT 
                                a1.account, 
                                a1.account AS acc_id, 
                                a1.alias_default AS alias, 
                                a1.parent, 
                                1 AS level
                            FROM account a1
                            WHERE a1.parent IS NOT NULL

                            UNION ALL

                            SELECT 
                                ah.account, 
                                a2.account AS acc_id, 
                                a2.alias_default AS alias, 
                                a2.parent, 
                                ah.level + 1
                            FROM account_hierarchy ah
                            JOIN account a2 ON ah.parent = a2.account COLLATE Thai_CI_AS
                            WHERE a2.parent IS NOT NULL AND ah.level < 6
                        ),
                        max_levels AS (
                            SELECT 
                                account, 
                                MAX(level) AS max_level
                            FROM account_hierarchy
                            GROUP BY account
                        ),
                        cleaned_hierarchy AS (
                            SELECT 
                                h.*,
                                CAST(h.acc_id AS VARCHAR(100)) + ' : ' + 
                                ISNULL(
                                    CASE 
                                        WHEN PATINDEX('%[ก-๙A-Za-z]%', h.alias COLLATE Thai_CI_AS) > 0 THEN
                                            STUFF(h.alias COLLATE Thai_CI_AS, 1, 
                                                PATINDEX('%[ก-๙A-Za-z]%', h.alias COLLATE Thai_CI_AS) - 1, '')
                                        ELSE h.alias COLLATE Thai_CI_AS
                                    END, ''
                                ) AS clean_alias
                            FROM account_hierarchy h
                        ),
                        hierarchy_pivot AS (
                            SELECT 
                                h.account,
                                m.max_level,
                                MAX(CASE WHEN h.level = 1 THEN h.clean_alias ELSE NULL END) AS level1_value,
                                MAX(CASE WHEN h.level = 2 THEN h.clean_alias ELSE NULL END) AS level2_value,
                                MAX(CASE WHEN h.level = 3 THEN h.clean_alias ELSE NULL END) AS level3_value,
                                MAX(CASE WHEN h.level = 4 THEN h.clean_alias ELSE NULL END) AS level4_value,
                                MAX(CASE WHEN h.level = 5 THEN h.clean_alias ELSE NULL END) AS level5_value,
                                MAX(CASE WHEN h.level = 6 THEN h.clean_alias ELSE NULL END) AS level6_value
                            FROM cleaned_hierarchy h
                            JOIN max_levels m ON h.account = m.account
                            GROUP BY h.account, m.max_level
                        ),shifted_hierarchy AS (
                            SELECT
                                account AS current_acc,
                                max_level AS TotalLevels,

                                CASE max_level
                                    WHEN 1 THEN level1_value
                                    WHEN 2 THEN level2_value
                                    WHEN 3 THEN level3_value
                                    WHEN 4 THEN level4_value
                                    WHEN 5 THEN level5_value
                                    WHEN 6 THEN level6_value
                                END AS level6,

                                CASE max_level
                                    WHEN 2 THEN level1_value
                                    WHEN 3 THEN level2_value
                                    WHEN 4 THEN level3_value
                                    WHEN 5 THEN level4_value
                                    WHEN 6 THEN level5_value
                                    ELSE NULL
                                END AS level5,

                                CASE max_level
                                    WHEN 3 THEN level1_value
                                    WHEN 4 THEN level2_value
                                    WHEN 5 THEN level3_value
                                    WHEN 6 THEN level4_value
                                    ELSE NULL
                                END AS level4,

                                CASE max_level
                                    WHEN 4 THEN level1_value
                                    WHEN 5 THEN level2_value
                                    WHEN 6 THEN level3_value
                                    ELSE NULL
                                END AS level3,

                                CASE max_level
                                    WHEN 5 THEN level1_value
                                    WHEN 6 THEN level2_value
                                    ELSE NULL
                                END AS level2,

                                CASE max_level
                                    WHEN 6 THEN level1_value
                                    ELSE NULL
                                END AS level1
                            FROM hierarchy_pivot
                        ),t1 AS (
                            SELECT 
                                Faculty,
                                Alias_Default
                            FROM Faculty
                            WHERE Parent = 'Total KKU'
                            AND Faculty != 'Faculty-00'
                        ),t1_1 AS (
                            SELECT 
                                account,
                                alias_default,
                                type,
                                sub_type
                            FROM account
                            WHERE id > (
                                SELECT id
                                FROM account
                                WHERE parent = 'Expenses'
                            )
                        ),t2 AS (
                            SELECT 
                                b.*,
                                f.Parent,
                                REPLACE(f.Alias_Default, '-', ':') AS f2
                            FROM budget_planning_allocated_annual_budget_plan b
                            LEFT JOIN (
                                SELECT * FROM Faculty
                                WHERE Parent LIKE 'Faculty%'
                            ) f ON b.Faculty = f.Faculty
                            WHERE f.Parent NOT LIKE '%BU%'
                            AND b.Fund IN ('FN06', 'FN02')
                        ),t2_1 AS (
                            SELECT
                                t.*,
                                tt.alias_default AS account_name,
                                tt.type,
                                tt.sub_type
                            FROM t2 t
                            LEFT JOIN t1_1 tt ON t.Account COLLATE Thai_CI_AS = tt.account COLLATE Thai_CI_AS
                            WHERE tt.alias_default IS NOT NULL
                        )
                        ,t3 AS (
                            SELECT
                                t.Faculty,
                                t.Fund,
                                t.[Plan],
                                t.Sub_Plan,
                                t.Scenario,
                                t.KKU_Item_Name,
                                t.type,
                                t.sub_type,
                                t.Account,
                                t.Service,
                                t.Project,
                                t.YEAR AS year2,
                                SUM(t.Allocated_Total_Amount_Quantity) AS Allocated_Total_Amount_Quantity,
                                tt.Alias_Default AS f1,
                                t.f2
                            FROM t2_1 t
                            LEFT JOIN t1 tt ON t.Parent COLLATE Thai_CI_AS = tt.Faculty COLLATE Thai_CI_AS
                            GROUP BY
                                t.Faculty,
                                t.Fund,
                                t.[Plan],
                                t.Sub_Plan,
                                t.Scenario,
                                t.KKU_Item_Name,
                                t.type,
                                t.sub_type,
                                t.Account,
                                t.Project,
                                t.Service,
                                t.YEAR,
                                tt.Alias_Default,
                                t.f2
                        ),t4 AS (
                            SELECT
                                FACULTY,
                                FUND,
                                [PLAN],
                                SUBPLAN,
                                PROJECT,
                                ACCOUNT,
                                SERVICE,
                                CAST(CAST('25' + RIGHT(FISCAL_YEAR, 2) AS INT) AS VARCHAR(10)) AS FISCAL_YEAR,
                                SUM(COMMITMENTS) AS COMMITMENTS,
                                SUM(OBLIGATIONS) AS OBLIGATIONS,
                                SUM(EXPENDITURES) AS EXPENDITURES
                            FROM budget_planning_actual
                            GROUP BY
                                FACULTY,
                                FUND,
                                [PLAN],
                                SUBPLAN,
                                PROJECT,
                                ACCOUNT,
                                SERVICE,
                                CAST(CAST('25' + RIGHT(FISCAL_YEAR, 2) AS INT) AS VARCHAR(10))
                        ),t5 AS (
                            SELECT
                                t.*,
                                a.COMMITMENTS,
                                a.OBLIGATIONS,
                                a.EXPENDITURES,
                                a.FISCAL_YEAR
                            FROM t3 t
                            LEFT JOIN t4 a ON
                                t.Faculty COLLATE Thai_CI_AS = a.FACULTY COLLATE Thai_CI_AS AND
                                (t.Fund = 'FN' + a.FUND OR t.Fund = a.FUND) AND
                                t.[Plan] = a.[PLAN] AND
                                t.Sub_Plan = 'SP_' + a.SUBPLAN AND
                                t.Project = a.PROJECT AND
                                t.Account = a.ACCOUNT AND
                                REPLACE(t.Service, 'SR_', '') COLLATE Thai_CI_AS = a.SERVICE COLLATE Thai_CI_AS AND
                                t.year2 COLLATE Thai_CI_AS = a.FISCAL_YEAR COLLATE Thai_CI_AS
                        ),t6 AS (
                            SELECT
                                Faculty,
                                [Plan],
                                Scenario,
                                REPLACE(Sub_Plan, 'SP_', '') AS Sub_Plan,
                                Project,
                                f1,
                                f2,
                                KKU_Item_Name,
                                type AS type2,
                                sub_type AS sub_type2,
                                Account AS account2,
                                year2,
                                SUM(CASE WHEN Fund = 'FN02' THEN ISNULL(Allocated_Total_Amount_Quantity, 0) ELSE 0 END) AS a2,
                                SUM(CASE WHEN Fund = 'FN02' THEN ISNULL(COMMITMENTS, 0) ELSE 0 END) AS c2,
                                SUM(CASE WHEN Fund = 'FN02' THEN ISNULL(OBLIGATIONS, 0) ELSE 0 END) AS o2,
                                SUM(CASE WHEN Fund = 'FN02' THEN ISNULL(EXPENDITURES, 0) ELSE 0 END) AS e2,
                                SUM(CASE WHEN Fund = 'FN06' THEN ISNULL(Allocated_Total_Amount_Quantity, 0) ELSE 0 END) AS a6,
                                SUM(CASE WHEN Fund = 'FN06' THEN ISNULL(COMMITMENTS, 0) ELSE 0 END) AS c6,
                                SUM(CASE WHEN Fund = 'FN06' THEN ISNULL(OBLIGATIONS, 0) ELSE 0 END) AS o6,
                                SUM(CASE WHEN Fund = 'FN06' THEN ISNULL(EXPENDITURES, 0) ELSE 0 END) AS e6
                            FROM t5
                            GROUP BY
                                Faculty,
                                [Plan],
                                Scenario,
                                REPLACE(Sub_Plan, 'SP_', ''),
                                Project,
                                f1,
                                f2,
                                KKU_Item_Name,
                                type,
                                sub_type,
                                Account,
                                year2
                        ),t7 AS (
                            SELECT
                                t.*,
                                p.plan_name,
                                REPLACE(t.Sub_Plan, 'SP_', '') + ' : ' + sp.sub_plan_name AS sub_plan_name,
                                pr.project_name,
                                a.id AS account,
                                aa.id AS sub_account
                            FROM t6 t
                            LEFT JOIN [plan] p ON t.[Plan] = p.plan_id
                            LEFT JOIN sub_plan sp ON REPLACE(t.Sub_Plan, 'SP_', '') = REPLACE(sp.sub_plan_id, 'SP_', '')
                            LEFT JOIN project pr ON t.Project = pr.project_id
                            LEFT JOIN account a ON t.type2 COLLATE Thai_CI_AS = a.alias_default COLLATE Thai_CI_AS
                            LEFT JOIN account aa ON t.sub_type2 COLLATE Thai_CI_AS = aa.alias_default COLLATE Thai_CI_AS
                        ),t8 AS (
                            SELECT
                                t.*,
                                a.account AS atype,
                                a2.account AS asubtype,
                                a3.alias_default AS aname
                            FROM t7 t
                            LEFT JOIN account a ON t.type2 COLLATE Thai_CI_AS = a.alias_default COLLATE Thai_CI_AS
                            LEFT JOIN account a2 ON t.sub_type2 COLLATE Thai_CI_AS = a2.alias_default COLLATE Thai_CI_AS
                            LEFT JOIN account a3 ON t.account2 = a3.account COLLATE Thai_CI_AS
                        ),t9 AS (
                            SELECT
                                *,
                                atype + ' : ' + ISNULL(LTRIM(RTRIM(
                                    STUFF(type2, 1, PATINDEX('%[ก-๙A-Za-z]%', type2 COLLATE Thai_CI_AS) - 1, '')
                                )), type2) AS TYPE,
                                asubtype + ' : ' + ISNULL(LTRIM(RTRIM(
                                    STUFF(sub_type2, 1, PATINDEX('%[ก-๙A-Za-z]%', sub_type2 COLLATE Thai_CI_AS) - 1, '')
                                )), sub_type2) AS sub_type,
                                account2 + ' : ' + ISNULL(LTRIM(RTRIM(
                                    STUFF(aname, 1, PATINDEX('%[ก-๙A-Za-z]%', aname COLLATE Thai_CI_AS) - 1, '')
                                )), aname) AS accname,
                                CASE 
                                    WHEN KKU_Item_Name IS NOT NULL AND KKU_Item_Name != '' THEN
                                        account2 + ' : ' + ISNULL(LTRIM(RTRIM(
                                            STUFF(KKU_Item_Name, 1, PATINDEX('%[ก-๙A-Za-z]%', KKU_Item_Name COLLATE Thai_CI_AS) - 1, '')
                                        )), KKU_Item_Name)
                                    ELSE NULL
                                END AS KKU_Item_Name2
                            FROM t8
                        ),t10 AS (
                            SELECT
                                t.*,
                                h.*
                            FROM t9 t
                            LEFT JOIN shifted_hierarchy h ON t.account2 COLLATE Thai_CI_AS = h.current_acc COLLATE Thai_CI_AS
                        )

                        SELECT * FROM hierarchy_pivot";
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
                $sql = "WITH account_hierarchy AS (
                    SELECT 
                        a1.account, 
                        a1.account AS acc_id, 
                        a1.alias_default AS alias, 
                        a1.parent, 
                        1 AS level
                    FROM account a1
                    WHERE a1.parent IS NOT NULL

                    UNION ALL

                    SELECT 
                        ah.account, 
                        a2.account AS acc_id, 
                        a2.alias_default AS alias, 
                        a2.parent, 
                        ah.level + 1
                    FROM account_hierarchy ah
                    JOIN account a2 ON ah.parent = a2.account COLLATE Thai_CI_AS
                    WHERE a2.parent IS NOT NULL AND ah.level < 6
                ),
                max_levels AS (
                    SELECT account, MAX(level) AS max_level
                    FROM account_hierarchy
                    GROUP BY account
                ),
                cleaned_hierarchy AS (
                    SELECT 
                        h.*,
                        CAST(h.acc_id AS VARCHAR(100)) + ' : ' + 
                        ISNULL(
                            CASE 
                                WHEN PATINDEX('%[ก-๙A-Za-z]%', h.alias COLLATE Thai_CI_AS) > 0 THEN
                                    STUFF(h.alias COLLATE Thai_CI_AS, 1, PATINDEX('%[ก-๙A-Za-z]%', h.alias COLLATE Thai_CI_AS) - 1, '')
                                ELSE h.alias COLLATE Thai_CI_AS
                            END, '') AS clean_alias
                    FROM account_hierarchy h
                ),
                hierarchy_pivot AS (
                    SELECT 
                        h.account,
                        m.max_level,
                        MAX(CASE WHEN h.level = 1 THEN h.clean_alias END) AS level1_value,
                        MAX(CASE WHEN h.level = 2 THEN h.clean_alias END) AS level2_value,
                        MAX(CASE WHEN h.level = 3 THEN h.clean_alias END) AS level3_value,
                        MAX(CASE WHEN h.level = 4 THEN h.clean_alias END) AS level4_value,
                        MAX(CASE WHEN h.level = 5 THEN h.clean_alias END) AS level5_value,
                        MAX(CASE WHEN h.level = 6 THEN h.clean_alias END) AS level6_value
                    FROM cleaned_hierarchy h
                    JOIN max_levels m ON h.account = m.account
                    GROUP BY h.account, m.max_level
                ),
                shifted_hierarchy AS (
                    SELECT
                        account AS current_acc,
                        max_level AS TotalLevels,
                        CASE max_level WHEN 1 THEN level1_value WHEN 2 THEN level2_value WHEN 3 THEN level3_value
                                    WHEN 4 THEN level4_value WHEN 5 THEN level5_value WHEN 6 THEN level6_value END AS level6,
                        CASE max_level WHEN 2 THEN level1_value WHEN 3 THEN level2_value WHEN 4 THEN level3_value
                                    WHEN 5 THEN level4_value WHEN 6 THEN level5_value ELSE NULL END AS level5,
                        CASE max_level WHEN 3 THEN level1_value WHEN 4 THEN level2_value WHEN 5 THEN level3_value
                                    WHEN 6 THEN level4_value ELSE NULL END AS level4,
                        CASE max_level WHEN 4 THEN level1_value WHEN 5 THEN level2_value WHEN 6 THEN level3_value ELSE NULL END AS level3,
                        CASE max_level WHEN 5 THEN level1_value WHEN 6 THEN level2_value ELSE NULL END AS level2,
                        CASE max_level WHEN 6 THEN level1_value ELSE NULL END AS level1
                    FROM hierarchy_pivot
                ),
                t1 AS (
                    SELECT 
                        b.Faculty,
                        CASE 
                            WHEN b.Scenario LIKE '%Annual%' THEN 'งบประมาณประจำปี'
                            WHEN b.Scenario LIKE '%Midyear%' THEN 'งบประมาณกลางปี'
                            ELSE b.Scenario 
                        END AS scenario,
                        SUM(CASE WHEN REPLACE(b.Fund,'FN','') = '06' THEN b.Total_Amount_Quantity ELSE 0 END) AS t06,
                        SUM(CASE WHEN REPLACE(b.Fund,'FN','') = '02' THEN b.Total_Amount_Quantity ELSE 0 END) AS t02,
                        SUM(CASE WHEN REPLACE(b.Fund,'FN','') = '08' THEN b.Total_Amount_Quantity ELSE 0 END) AS t08,
                        SUM(CASE WHEN REPLACE(ba.FUND,'FN','') = '06' THEN ba.EXPENDITURES ELSE 0 END) AS e06,
                        SUM(CASE WHEN REPLACE(ba.FUND,'FN','') = '02' THEN ba.EXPENDITURES ELSE 0 END) AS e02,
                        SUM(CASE WHEN REPLACE(ba.FUND,'FN','') = '08' THEN ba.EXPENDITURES ELSE 0 END) AS e08,
                        b.Account AS account2,
                        b.KKU_Item_Name,
                        b.Budget_Management_Year,
                        b2.KKU_Strategic_Plan_LOV,
                        'SI' + RIGHT(p.pillar_id, 2) + ' : ' + p.pillar_name AS pillar_name,
                        a.type AS TYPE2,
                        a.sub_type AS sub_type2,
                        a.id AS p_id,
                        REPLACE(f.Alias_Default,'-',':') AS Alias_Default,
                        f2.Alias_Default AS pname
                    FROM budget_planning_annual_budget_plan b
                    LEFT JOIN budget_planning_project_kpi b2 ON b.Faculty = b2.Faculty AND b.Project = b2.Project
                    LEFT JOIN pilars2 p ON b2.KKU_Strategic_Plan_LOV = p.pillar_id
                    LEFT JOIN account a ON b.Account COLLATE Thai_CI_AS = a.account COLLATE Thai_CI_AS
                    LEFT JOIN (SELECT * FROM Faculty WHERE Parent LIKE 'Faculty%') f ON b.Faculty = f.Faculty
                    LEFT JOIN Faculty f2 ON f.Parent = f2.Faculty
                    LEFT JOIN budget_planning_actual ba ON 
                        b.Faculty = ba.FACULTY COLLATE Thai_CI_AS AND 
                        REPLACE(b.Fund, 'FN', '') = REPLACE(ba.FUND, 'FN', '') COLLATE Thai_CI_AS AND
                        b.Budget_Management_Year = CAST(CAST('25' + RIGHT(ba.FISCAL_YEAR, 2) AS INT) AS VARCHAR) COLLATE Thai_CI_AS AND
                        b.[Plan] = ba.[PLAN] COLLATE Thai_CI_AS AND 
                        REPLACE(b.Sub_Plan, 'SP_', '') = ba.SUBPLAN AND 
                        b.Project = ba.PROJECT COLLATE Thai_CI_AS AND 
                        REPLACE(b.Service, 'SR_', '') = ba.SERVICE COLLATE Thai_CI_AS AND
                        b.Account = ba.ACCOUNT COLLATE Thai_CI_AS
                    WHERE a.id > (SELECT id FROM account WHERE parent = 'Expenses')
                    GROUP BY
                    b.Faculty,
                    b.Account,
                    b.KKU_Item_Name,
                    b.Budget_Management_Year,
                    b2.KKU_Strategic_Plan_LOV,
                    p.pillar_id,
                    p.pillar_name,
                    a.type,
                    a.sub_type,
                    a.id,
                    f.Alias_Default,
                    f2.Alias_Default,
                    CASE 
                        WHEN b.Scenario LIKE '%Annual%' THEN 'งบประมาณประจำปี'
                        WHEN b.Scenario LIKE '%Midyear%' THEN 'งบประมาณกลางปี'
                        ELSE b.Scenario 
                    END

                ),
                t2 AS (
                    SELECT t.*, a.account AS atype, a2.account AS asubtype, a3.alias_default AS aname
                    FROM t1 t
                    LEFT JOIN account a ON t.TYPE2 COLLATE Thai_CI_AS = a.alias_default COLLATE Thai_CI_AS
                    LEFT JOIN account a2 ON t.sub_type2 COLLATE Thai_CI_AS = a2.alias_default COLLATE Thai_CI_AS
                    LEFT JOIN account a3 ON t.account2 = a3.account COLLATE Thai_CI_AS
                ),
                t3 AS (
                    SELECT *,
                        atype + ' : ' + ISNULL(STUFF(TYPE2, 1, PATINDEX('%[ก-๙A-Za-z]%', TYPE2 COLLATE Thai_CI_AS)-1, ''), TYPE2) AS TYPE,
                        asubtype COLLATE Thai_CI_AS + ' : ' + ISNULL(STUFF(sub_type2, 1, PATINDEX('%[ก-๙A-Za-z]%', sub_type2 COLLATE Thai_CI_AS)-1, ''), sub_type2 COLLATE Thai_CI_AS) AS sub_type,
                        account2 + ' : ' + ISNULL(STUFF(aname, 1, PATINDEX('%[ก-๙A-Za-z]%', aname COLLATE Thai_CI_AS)-1, ''), aname) AS accname,
                        CASE 
                            WHEN KKU_Item_Name IS NOT NULL AND KKU_Item_Name != '' THEN
                                account2 + ' : ' + ISNULL(STUFF(KKU_Item_Name, 1, PATINDEX('%[ก-๙A-Za-z]%', KKU_Item_Name COLLATE Thai_CI_AS)-1, ''), KKU_Item_Name)
                            ELSE NULL
                        END AS KKU_Item_Name2
                    FROM t2
                ),
                t4 AS (
                    SELECT t.*, h.*
                    FROM t3 t
                    LEFT JOIN shifted_hierarchy h ON t.account2 COLLATE Thai_CI_AS = h.current_acc COLLATE Thai_CI_AS
                )

                SELECT * FROM t4
                ORDER BY Faculty, KKU_Strategic_Plan_LOV, p_id";
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
        case "kku_bgp_budget-spending-status2":
            try {
                $db = new Database();
                $conn = $db->connect();

                // เชื่อมต่อฐานข้อมูล
                $sql = "SELECT 
                            ba.FACULTY,
                            
                            SUM(CASE WHEN REPLACE(ba.FUND, 'FN', '') = '06' THEN ba.EXPENDITURES ELSE 0 END) AS n06,
                            SUM(CASE WHEN REPLACE(ba.FUND, 'FN', '') = '02' THEN ba.EXPENDITURES ELSE 0 END) AS n02,
                            SUM(CASE WHEN REPLACE(ba.FUND, 'FN', '') = '08' THEN ba.EXPENDITURES ELSE 0 END) AS n08,

                            CAST('25' + RIGHT(CONVERT(VARCHAR(10), ba.FISCAL_YEAR), 2) AS VARCHAR(10)) AS FISCAL_YEAR,

                            REPLACE(f.Alias_Default, '-', ':') AS Alias_Default,
                            f2.Alias_Default AS pname

                        FROM budget_planning_actual ba

                        LEFT JOIN budget_planning_project_kpi b2
                            ON ba.FACULTY = b2.Faculty AND ba.PROJECT = b2.Project

                        LEFT JOIN account a
                            ON ba.ACCOUNT COLLATE Thai_CI_AS = a.account COLLATE Thai_CI_AS

                        LEFT JOIN (SELECT * FROM Faculty WHERE Parent LIKE 'Faculty%') f
                            ON ba.FACULTY = f.Faculty

                        LEFT JOIN Faculty f2
                            ON f.Parent = f2.Faculty

                        LEFT JOIN budget_planning_annual_budget_plan b
                            ON ba.FACULTY COLLATE Thai_CI_AS = b.Faculty COLLATE Thai_CI_AS
                            AND REPLACE(ba.FUND, 'FN', '') = REPLACE(b.Fund, 'FN', '')
                            AND CONVERT(VARCHAR(10), '25' + RIGHT(CONVERT(VARCHAR(10), ba.FISCAL_YEAR), 2)) 
                            = CONVERT(VARCHAR(10), b.Budget_Management_Year) COLLATE Thai_CI_AS

                            AND ba.[PLAN] COLLATE Thai_CI_AS = b.[Plan] COLLATE Thai_CI_AS
                            AND ba.SUBPLAN = REPLACE(b.Sub_Plan, 'SP_', '')
                            AND ba.PROJECT COLLATE Thai_CI_AS = b.Project COLLATE Thai_CI_AS
                            AND ba.SERVICE COLLATE Thai_CI_AS = REPLACE(b.Service, 'SR_', '') COLLATE Thai_CI_AS
                            AND ba.ACCOUNT COLLATE Thai_CI_AS = b.Account COLLATE Thai_CI_AS

                        WHERE a.id > (SELECT id FROM account WHERE parent = 'Expenses')
                        AND b.Faculty IS NULL

                        GROUP BY 
                            ba.FACULTY,
                            f.Alias_Default,
                            f2.Alias_Default,
                            b.Faculty,
                            CONVERT(VARCHAR(10), '25' + RIGHT(CONVERT(VARCHAR(10), ba.FISCAL_YEAR), 2))";
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
                            SELECT 
                                p.Faculty,
                                p.[Plan],
                                p.Sub_Plan,
                                p.Project,
                                REPLACE(p.Fund, 'FN', '') AS Fund,
                                SUM(p.Total_Amount_Quantity) AS Total_Amount_Quantity,
                                b.Proj_KPI_Name,
                                SUM(b.Proj_KPI_Target) AS Proj_KPI_Target,
                                b.UoM_for_Proj_KPI,
                                b.Objective,
                                b.Project_Output,
                                b.Project_Outcome,
                                b.Project_Impact,
                                b.KKU_Strategic_Plan_LOV,
                                b.OKRs_LOV,
                                b.Principles_of_good_governance,
                                b.SDGs,
                                p.Scenario,
                                p.Budget_Management_Year
                            FROM budget_planning_annual_budget_plan p
                            LEFT JOIN budget_planning_project_kpi b
                                ON p.Faculty = b.Faculty AND p.Project = b.Project
                            GROUP BY 
                                p.Faculty,
                                p.[Plan],
                                p.Sub_Plan,
                                p.Project,
                                REPLACE(p.Fund, 'FN', ''),
                                b.Proj_KPI_Name,
                                b.UoM_for_Proj_KPI,
                                b.Objective,
                                b.Project_Output,
                                b.Project_Outcome,
                                b.Project_Impact,
                                b.KKU_Strategic_Plan_LOV,
                                b.OKRs_LOV,
                                b.Principles_of_good_governance,
                                b.SDGs,
                                p.Scenario,
                                p.Budget_Management_Year
                        ),
                        t2 AS (
                            SELECT 
                                t.*, 
                                f.Alias_Default, 
                                f2.Alias_Default AS pname
                            FROM t1 t
                            LEFT JOIN (SELECT * FROM Faculty WHERE Parent LIKE 'Faculty%') f
                                ON t.Faculty = f.Faculty
                            LEFT JOIN Faculty f2
                                ON f.Parent = f2.Faculty
                        ),
                        t3 AS (
                            SELECT 
                                tt.*, 
                                pl.plan_name
                            FROM t2 tt
                            LEFT JOIN [plan] pl
                                ON tt.[Plan] = pl.plan_id
                        ),
                        t4 AS (
                            SELECT 
                                tt.*, 
                                REPLACE(tt.Sub_Plan, 'SP_', '') + ' : ' + s.sub_plan_name AS sub_plan_name
                            FROM t3 tt
                            LEFT JOIN sub_plan s
                                ON tt.Sub_Plan = s.sub_plan_id
                        ),
                        t5 AS (
                            SELECT 
                                tt.*, 
                                pr.project_name
                            FROM t4 tt
                            LEFT JOIN project pr
                                ON tt.Project = pr.project_id
                        ),
                        t6 AS (
                            SELECT 
                                tt.*, 
                                pil.pillar_name
                            FROM t5 tt
                            LEFT JOIN pilars2 pil
                                ON tt.KKU_Strategic_Plan_LOV = pil.pillar_id
                        ),
                        t7 AS (
                            SELECT 
                                tt.*, 
                                ok.okr_name
                            FROM t6 tt
                            LEFT JOIN okr ok
                                ON REPLACE(tt.OKRs_LOV, '_', '-') = ok.okr_id
                        )

                        SELECT DISTINCT *
                        FROM t7
                        ORDER BY 
                            Budget_Management_Year,
                            Faculty,
                            Fund,
                            [Plan],
                            Sub_Plan,
                            Project,
                            KKU_Strategic_Plan_LOV,
                            OKRs_LOV";
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
                $sql = "SELECT DISTINCT Fund AS f 
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
                        LEFT JOIN (SELECT * from Faculty WHERE Parent LIKE 'Faculty%') f
                        ON b.Faculty=f.Faculty
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
                        LEFT JOIN (SELECT * from Faculty WHERE Parent LIKE 'Faculty%') f
                        ON b.Faculty=f.Faculty
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
                $sql = "SELECT DISTINCT Scenario
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
                            SELECT 
                                b.*, 
                                f.Alias_Default
                            FROM budget_planning_annual_budget_plan b
                            LEFT JOIN (SELECT * FROM Faculty WHERE Parent LIKE 'Faculty%') f
                                ON b.Faculty = f.Faculty
                            LEFT JOIN account a
                                ON b.Account COLLATE Thai_CI_AS = a.account COLLATE Thai_CI_AS
                            WHERE 
                                a.id > (SELECT id FROM account WHERE parent = 'Expenses')
                                and b.Budget_Management_Year= :fyear AND b.fund= :fund AND f.Alias_Default= :faculty and b.scenario=:scenario
                        ),
                        t1_1 AS (
                            SELECT 
                                t.Faculty,
                                t.Fund,
                                t.[Plan],
                                t.Sub_Plan,
                                t.Project,
                                t.Alias_Default,
                                t.Budget_Management_Year,

                                SUM(CASE WHEN a.[type] = N'1.ค่าใช้จ่ายบุคลากร' THEN t.Total_Amount_Quantity ELSE 0 END) AS a1,
                                SUM(CASE WHEN a.[type] = N'2.ค่าใช้จ่ายดำเนินงาน' THEN t.Total_Amount_Quantity ELSE 0 END) AS a2,
                                SUM(CASE WHEN a.[type] = N'3.ค่าใช้จ่ายลงทุน' THEN t.Total_Amount_Quantity ELSE 0 END) AS a3,
                                SUM(CASE WHEN a.[type] = N'4.ค่าใช้จ่ายเงินอุดหนุนดำเนินงาน' THEN t.Total_Amount_Quantity ELSE 0 END) AS a4,
                                SUM(CASE WHEN a.[type] = N'5.ค่าใช้จ่ายอื่น' THEN t.Total_Amount_Quantity ELSE 0 END) AS a5,

                                SUM(t.Q1_Spending_Plan) AS q1,
                                SUM(t.Q2_Spending_Plan) AS q2,
                                SUM(t.Q3_Spending_Plan) AS q3,
                                SUM(t.Q4_Spending_Plan) AS q4

                            FROM t1 t
                            LEFT JOIN account a
                                ON t.Account COLLATE Thai_CI_AS = a.account COLLATE Thai_CI_AS
                            GROUP BY 
                                t.Faculty,
                                t.Fund,
                                t.[Plan],
                                t.Sub_Plan,
                                t.Project,
                                t.Alias_Default,
                                t.Budget_Management_Year
                        ),
                        t2 AS (
                            SELECT 
                                t.*, 
                                pr.project_name
                            FROM t1_1 t
                            LEFT JOIN project pr
                                ON t.Project = pr.project_id
                        ),
                        t3 AS (
                            SELECT 
                                t.*, 
                                p.pillar_name,
                                ok.okr_name
                            FROM t2 t
                            LEFT JOIN budget_planning_project_kpi b2
                                ON t.Project = b2.Project AND t.Faculty = b2.Faculty
                            LEFT JOIN pilars2 p
                                ON b2.KKU_Strategic_Plan_LOV = p.pillar_id
                            LEFT JOIN okr ok
                                ON REPLACE(b2.OKRs_LOV, '_', '-') = ok.okr_id
                        ),
                        t4 AS (
                            SELECT 
                                t.*, 
                                pl.plan_name,
                                REPLACE(t.Sub_Plan, 'SP_', '') + ' : ' + sp.sub_plan_name AS sub_plan_name
                            FROM t3 t
                            LEFT JOIN [plan] pl
                                ON t.[Plan] = pl.plan_id
                            LEFT JOIN sub_plan sp
                                ON t.Sub_Plan = sp.sub_plan_id
                        )

                        SELECT *
                        FROM t4
                        ORDER BY Project";
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
                $sql = "WITH t1 AS (
                            SELECT type
                            FROM account
                            WHERE id < (SELECT id FROM account WHERE account = 'Expenses')
                                AND type IS NOT NULL
                            GROUP BY type
                        ),
                        t2 AS (
                            SELECT 
                                b.Total_Amount_Quantity,
                                a.[type],
                                f.Alias_Default
                            FROM budget_planning_annual_budget_plan b
                            LEFT JOIN account a
                                ON b.Account COLLATE Thai_CI_AS = a.account COLLATE Thai_CI_AS
                            LEFT JOIN (SELECT * FROM Faculty WHERE Parent LIKE 'Faculty%') f
                                ON b.Faculty = f.Faculty
                            where b.Budget_Management_Year=:fyear and b.scenario=:scenario and f.Alias_Default=:faculty
                        ),
                        t3 AS (
                            SELECT 
                                t.[type],
                                COALESCE(SUM(tt.Total_Amount_Quantity), 0) AS Total_Amount_Quantity
                            FROM t1 t
                            LEFT JOIN t2 tt
                                ON t.[type] COLLATE Thai_CI_AS = tt.[type] COLLATE Thai_CI_AS
                            GROUP BY t.[type]
                        )

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
                SELECT type 
                FROM account 
                WHERE id > (SELECT id FROM account WHERE account = 'Expenses') AND type is not null
                GROUP BY type)
                ,t2 AS (
                SELECT b.Total_Amount_Quantity,a.type,f.Alias_Default
                FROM budget_planning_annual_budget_plan b
                LEFT JOIN account a
                ON b.Account=a.account
                LEFT JOIN (SELECT * from Faculty WHERE Parent LIKE 'Faculty%') f
                ON b.Faculty=f.Faculty
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
                            SELECT DISTINCT sub_type, 'expense' AS ftype
                            FROM account
                            WHERE id > (SELECT id FROM account WHERE parent = 'Expenses')
                                AND sub_type IS NOT NULL
                                AND sub_type NOT LIKE '%.%.%'
                        ),
                        t2 AS (
                            SELECT Faculty, [Plan], Sub_Plan, Project, Fund, Service, KKU_Item_Name, Account, Scenario
                            FROM budget_planning_allocated_annual_budget_plan
                            GROUP BY Faculty, [Plan], Sub_Plan, Project, Fund, Service, KKU_Item_Name, Account, Scenario
                        ),
                        t3 AS (
                            SELECT 
                                FACULTY, [PLAN], SUBPLAN, PROJECT, FUND, SERVICE, ACCOUNT, FISCAL_YEAR, BUDGET_PERIOD, SCENARIO,
                                SUM(COMMITMENTS) AS COMMITMENTS,
                                SUM(OBLIGATIONS) AS OBLIGATIONS,
                                SUM(EXPENDITURES) AS EXPENDITURES,
                                SUM(BUDGET_ADJUSTMENTS) AS BUDGET_ADJUSTMENTS,
                                SUM(INITIAL_BUDGET) AS INITIAL_BUDGET
                            FROM budget_planning_actual
                            GROUP BY FACULTY, [PLAN], SUBPLAN, PROJECT, FUND, SERVICE, ACCOUNT, FISCAL_YEAR, BUDGET_PERIOD, SCENARIO
                        ),
                        t4 AS (
                            SELECT tt.*, t.KKU_Item_Name
                            FROM t2 t
                            LEFT JOIN t3 tt ON
                                t.Faculty = tt.FACULTY
                                AND (REPLACE(t.Fund, 'FN', '') = tt.FUND OR t.Fund = tt.FUND)
                                AND t.[Plan] = tt.[PLAN]
                                AND REPLACE(t.Sub_Plan, 'SP_', '') = tt.SUBPLAN
                                AND t.Project = tt.PROJECT
                                AND REPLACE(t.Service, 'SR_', '') = tt.SERVICE
                                AND t.Account = tt.ACCOUNT
                        ),
                        t5 AS (
                            SELECT 
                                t.*, 
                                a.account AS aname,
                                a.sub_type,
                                a.parent,
                                CASE 
                                    WHEN aa.alias_default LIKE '%.%.%' THEN a.sub_type
                                    WHEN aa.alias_default IS NULL THEN a.sub_type
                                    ELSE aa.alias_default 
                                END AS pname
                            FROM t4 t
                            LEFT JOIN account a ON t.ACCOUNT COLLATE Thai_CI_AS = a.account COLLATE Thai_CI_AS
                            LEFT JOIN account aa ON a.parent = aa.account COLLATE Thai_CI_AS
                        ),
                        t6 AS (
                            SELECT 
                                FACULTY, [PLAN], SUBPLAN, PROJECT, FUND, SERVICE, FISCAL_YEAR, KKU_Item_Name, pname,
                                SUM(COMMITMENTS) AS COMMITMENTS,
                                SUM(OBLIGATIONS) AS OBLIGATIONS,
                                SUM(EXPENDITURES) AS EXPENDITURES,
                                SUM(BUDGET_ADJUSTMENTS) AS BUDGET_ADJUSTMENTS,
                                SUM(INITIAL_BUDGET) AS INITIAL_BUDGET
                            FROM t5
                            GROUP BY FACULTY, [PLAN], SUBPLAN, PROJECT, FUND, SERVICE, FISCAL_YEAR, KKU_Item_Name, pname
                        ),
                        t7 AS (
                            SELECT t.sub_type AS smain, t.ftype, tt.*
                            FROM t1 t
                            LEFT JOIN t5 tt ON t.sub_type COLLATE Thai_CI_AS = tt.pname COLLATE Thai_CI_AS
                        ),
                        t8 AS (
                            SELECT 
                                t.*, 
                                f.Alias_Default AS fname,
                                p.plan_name,
                                sp.sub_plan_name,
                                pro.project_name
                            FROM t7 t
                            LEFT JOIN (SELECT Faculty, Alias_Default FROM Faculty WHERE Parent LIKE 'Faculty%') f
                                ON t.FACULTY = f.Faculty
                            LEFT JOIN [plan] p
                                ON t.[PLAN] = p.plan_id
                            LEFT JOIN sub_plan sp
                                ON REPLACE(t.SUBPLAN, 'SP_', '') = REPLACE(sp.sub_plan_id, 'SP_', '')
                            LEFT JOIN project pro
                                ON t.PROJECT = pro.project_id
                        )

                        SELECT *,
                            CAST('25' + RIGHT(FISCAL_YEAR, 2) AS INT) AS fiscal_year2
                        FROM t8
                        ORDER BY FISCAL_YEAR";

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
                $sql = "WITH account_hierarchy AS (
                            SELECT 
                                a1.account,
                                a1.account AS acc_id,
                                a1.alias_default COLLATE Thai_CI_AS AS alias,
                                a1.parent,
                                1 AS level
                            FROM account a1
                            WHERE a1.parent IS NOT NULL

                            UNION ALL

                            SELECT 
                                ah.account,
                                a2.account AS acc_id,
                                a2.alias_default COLLATE Thai_CI_AS AS alias,
                                a2.parent,
                                ah.level + 1 AS level
                            FROM account_hierarchy ah
                            JOIN account a2 
                                ON ah.parent = a2.account COLLATE Thai_CI_AS
                            WHERE a2.parent IS NOT NULL
                            AND ah.level < 6
                        ),
                        max_levels AS (
                            SELECT account, MAX(level) AS max_level
                            FROM account_hierarchy
                            GROUP BY account
                        ),
                        hierarchy_pivot AS (
                            SELECT 
                                h.account,
                                m.max_level,
                                MAX(CASE WHEN h.level = 1 THEN h.acc_id + ' : ' + 
                                    LTRIM(STUFF(h.alias, 1, PATINDEX('%[0-9][ ]%', h.alias + ' '), '')) END) AS level1_value,
                                MAX(CASE WHEN h.level = 2 THEN h.acc_id + ' : ' + 
                                    LTRIM(STUFF(h.alias, 1, PATINDEX('%[0-9][ ]%', h.alias + ' '), '')) END) AS level2_value,
                                MAX(CASE WHEN h.level = 3 THEN h.acc_id + ' : ' + 
                                    LTRIM(STUFF(h.alias, 1, PATINDEX('%[0-9][ ]%', h.alias + ' '), '')) END) AS level3_value,
                                MAX(CASE WHEN h.level = 4 THEN h.acc_id + ' : ' + 
                                    LTRIM(STUFF(h.alias, 1, PATINDEX('%[0-9][ ]%', h.alias + ' '), '')) END) AS level4_value,
                                MAX(CASE WHEN h.level = 5 THEN h.acc_id + ' : ' + 
                                    LTRIM(STUFF(h.alias, 1, PATINDEX('%[0-9][ ]%', h.alias + ' '), '')) END) AS level5_value,
                                MAX(CASE WHEN h.level = 6 THEN h.acc_id + ' : ' + 
                                    LTRIM(STUFF(h.alias, 1, PATINDEX('%[0-9][ ]%', h.alias + ' '), '')) END) AS level6_value
                            FROM account_hierarchy h
                            JOIN max_levels m ON h.account = m.account
                            GROUP BY h.account, m.max_level
                        ),
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
                                    WHEN max_level <= 2 THEN NULL
                                    WHEN max_level = 3 THEN level1_value
                                    WHEN max_level = 4 THEN level2_value
                                    WHEN max_level = 5 THEN level3_value
                                    WHEN max_level = 6 THEN level4_value
                                END AS level4,
                                CASE 
                                    WHEN max_level <= 3 THEN NULL
                                    WHEN max_level = 4 THEN level1_value
                                    WHEN max_level = 5 THEN level2_value
                                    WHEN max_level = 6 THEN level3_value
                                END AS level3,
                                CASE 
                                    WHEN max_level <= 4 THEN NULL
                                    WHEN max_level = 5 THEN level1_value
                                    WHEN max_level = 6 THEN level2_value
                                END AS level2,
                                CASE 
                                    WHEN max_level <= 5 THEN NULL
                                    WHEN max_level = 6 THEN level1_value
                                END AS level1
                            FROM hierarchy_pivot
                        ),
                        t2 AS (
                            SELECT Faculty, [Plan], Sub_Plan, Project, Fund, Service,
                                CASE 
                                    WHEN KKU_Item_Name IS NOT NULL AND KKU_Item_Name != '' 
                                        THEN Account + ' : ' + LTRIM(STUFF(KKU_Item_Name, 1, PATINDEX('%[0-9][ ]%', KKU_Item_Name + ' '), ''))
                                    ELSE NULL 
                                END AS kku_item_name,
                                Account, Scenario 
                            FROM budget_planning_allocated_annual_budget_plan b
                            GROUP BY Faculty, [Plan], Sub_Plan, Project, Fund, Service,
                                CASE 
                                    WHEN KKU_Item_Name IS NOT NULL AND KKU_Item_Name != '' 
                                        THEN Account + ' : ' + LTRIM(STUFF(KKU_Item_Name, 1, PATINDEX('%[0-9][ ]%', KKU_Item_Name + ' '), ''))
                                    ELSE NULL 
                                END, Account, Scenario
                        ),
                        t3 AS (
                            SELECT FACULTY, [PLAN], SUBPLAN, PROJECT, FUND, SERVICE, ACCOUNT, FISCAL_YEAR, BUDGET_PERIOD, SCENARIO,
                                SUM(COMMITMENTS) AS COMMITMENTS,
                                SUM(OBLIGATIONS) AS OBLIGATIONS,
                                SUM(EXPENDITURES) AS EXPENDITURES,
                                SUM(CASE WHEN BUDGET_ADJUSTMENTS > 0 THEN BUDGET_ADJUSTMENTS ELSE 0 END) AS adj_in,
                                SUM(CASE WHEN BUDGET_ADJUSTMENTS < 0 THEN BUDGET_ADJUSTMENTS ELSE 0 END) AS adj_out,
                                SUM(INITIAL_BUDGET) AS INITIAL_BUDGET,
                                SUM(FUNDS_AVAILABLE_AMOUNT) AS FUNDS_AVAILABLE_AMOUNT
                            FROM budget_planning_actual b
                            GROUP BY FACULTY, [PLAN], SUBPLAN, PROJECT, FUND, SERVICE, ACCOUNT, FISCAL_YEAR, BUDGET_PERIOD, SCENARIO
                        ),
                        t4 AS (
                            SELECT tt.*, t.kku_item_name
                            FROM t2 t
                            LEFT JOIN t3 tt
                                ON t.Faculty = tt.FACULTY 
                                AND (REPLACE(t.Fund, 'FN', '') = tt.FUND OR t.Fund = tt.FUND)
                                AND t.[Plan] = tt.[PLAN] 
                                AND REPLACE(t.Sub_Plan, 'SP_', '') = tt.SUBPLAN
                                AND t.Project = tt.PROJECT 
                                AND REPLACE(t.Service, 'SR_', '') = tt.SERVICE
                                AND t.Account = tt.ACCOUNT
                        ),
                        t5 AS (
                            SELECT t.*, a.account AS aname, a.sub_type, a.parent,
                                aa.alias_default AS pname
                            FROM t4 t
                            LEFT JOIN account a ON t.ACCOUNT = a.account COLLATE Thai_CI_AS
                            LEFT JOIN account aa ON a.parent = aa.account COLLATE Thai_CI_AS
                        ),
                        t6 AS (
                            SELECT FACULTY, [PLAN], SUBPLAN, PROJECT, FUND, SERVICE, FISCAL_YEAR, kku_item_name, pname,
                                SUM(COMMITMENTS) AS COMMITMENTS,
                                SUM(OBLIGATIONS) AS OBLIGATIONS,
                                SUM(EXPENDITURES) AS EXPENDITURES,
                                SUM(adj_in) AS adj_in,
                                SUM(adj_out) AS adj_out,
                                SUM(INITIAL_BUDGET) AS INITIAL_BUDGET,
                                SUM(FUNDS_AVAILABLE_AMOUNT) AS FUNDS_AVAILABLE_AMOUNT
                            FROM t5
                            GROUP BY FACULTY, [PLAN], SUBPLAN, PROJECT, FUND, SERVICE, FISCAL_YEAR, kku_item_name, pname
                        ),
                        t7 AS (
                            SELECT tt.*, a.id
                            FROM t5 tt
                            LEFT JOIN account a ON tt.ACCOUNT = a.account COLLATE Thai_CI_AS
                            WHERE a.id > (SELECT id FROM account WHERE parent = 'Expenses')
                        ),
                        t8 AS (
                            SELECT t.*, f.Alias_Default AS fname, p.plan_name, sp.sub_plan_name, pro.project_name
                            FROM t7 t
                            LEFT JOIN (SELECT Faculty, Alias_Default FROM Faculty WHERE Parent LIKE 'Faculty%') f
                                ON t.FACULTY = f.Faculty
                            LEFT JOIN [plan] p ON t.[PLAN] = p.plan_id
                            LEFT JOIN sub_plan sp ON t.SUBPLAN = REPLACE(sp.sub_plan_id, 'SP_', '')
                            LEFT JOIN project pro ON t.PROJECT = pro.project_id
                        ),
                        t9 AS (
                            SELECT t.*, b.Release_Amount, 
                                CAST('20' + SUBSTRING(t.FISCAL_YEAR, 3, 2) AS INT) + 543 AS fiscal_year2
                            FROM t8 t
                            LEFT JOIN budget_planning_disbursement_budget_plan_anl_release b
                                ON t.FACULTY = b.Faculty 
                                AND t.[PLAN] = b.[Plan]
                                AND t.FUND = b.Fund 
                                AND t.SUBPLAN = REPLACE(b.Sub_Plan, 'SP_', '') 
                                AND t.PROJECT = b.Project
                                AND t.ACCOUNT = b.Account 
                                AND t.SERVICE = REPLACE(b.Service, 'SR_', '')
                        )

                        SELECT * 
                        FROM t9 t
                        LEFT JOIN shifted_hierarchy h ON t.ACCOUNT COLLATE Thai_CI_AS = h.current_acc
                        ORDER BY FISCAL_YEAR, id";

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
