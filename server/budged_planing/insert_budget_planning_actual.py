import pandas as pd
import pymysql
import os

# ตั้งค่าการเชื่อมต่อ MySQL
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_username = 'root'
str_password = 'TDyutdYdyudRTYDsEFOPI'

# กำหนด path ของไฟล์ CSV
current_dir = os.path.dirname(__file__)
file_path = os.path.join(
    current_dir, 'KKU_INT_EPM_18_ACTUALS_REP_RPT_T1 (1).csv')

# อ่านไฟล์ CSV และบังคับให้คอลัมน์ที่อาจมีเลขศูนย์นำหน้าเป็น string
df = pd.read_csv(file_path, dtype=str)

# แปลงค่า NaN เป็น None
df = df.where(pd.notna(df), None)

# เชื่อมต่อฐานข้อมูล MySQL
conn = pymysql.connect(
    host=str_hosting,
    user=str_username,
    password=str_password,
    database=str_database,
    charset='utf8mb4',
    cursorclass=pymysql.cursors.DictCursor
)

try:
    with conn.cursor() as cursor:
        # สร้างตารางถ้ายังไม่มี
        create_table_sql = """
        CREATE TABLE IF NOT EXISTS budget_planning_actual (
            PERIOD VARCHAR(10),
            FISCAL_YEAR VARCHAR(10),
            PILLAR VARCHAR(255),
            STRATEGIC_PROJECT VARCHAR(255),
            FACULTY VARCHAR(50),
            FUND VARCHAR(50),
            OKR VARCHAR(255),
            PROJECT VARCHAR(50),
            PLAN VARCHAR(50),
            SUBPLAN VARCHAR(50),
            SERVICE VARCHAR(50),
            ACCOUNT VARCHAR(50),
            BUDGET_PERIOD VARCHAR(10),
            TOTAL_BUDGET DECIMAL(18,2),
            COMMITMENTS DECIMAL(18,2),
            OBLIGATIONS DECIMAL(18,2),
            OTHER_CONSUMPTION DECIMAL(18,2),
            EXPENDITURES DECIMAL(18,2),
            TOTAL_CONSUMPTION DECIMAL(18,2),
            FUNDS_AVAILABLE_AMOUNT DECIMAL(18,2),
            INITIAL_BUDGET DECIMAL(18,2),
            BUDGET_ADJUSTMENTS DECIMAL(18,2),
            UNRELEASED DECIMAL(18,2),
            TOTAL_REVENUE_BUDGET DECIMAL(18,2),
            ACTUAL_REVENUE DECIMAL(18,2),
            VARIATION DECIMAL(18,2),
            SCENARIO VARCHAR(50),
            VERSION VARCHAR(50),
            FUNDS_AVAILABLE_PERCENTAGE DECIMAL(5,2)
        );
        """
        cursor.execute(create_table_sql)
        conn.commit()

        # สร้างคำสั่ง SQL สำหรับการ Insert ข้อมูล
        insert_sql = """
        INSERT INTO budget_planning_actual (
            PERIOD, FISCAL_YEAR, PILLAR, STRATEGIC_PROJECT, FACULTY, FUND, OKR, PROJECT,
            PLAN, SUBPLAN, SERVICE, ACCOUNT, BUDGET_PERIOD, TOTAL_BUDGET, COMMITMENTS, 
            OBLIGATIONS, OTHER_CONSUMPTION, EXPENDITURES, TOTAL_CONSUMPTION, 
            FUNDS_AVAILABLE_AMOUNT, INITIAL_BUDGET, BUDGET_ADJUSTMENTS, UNRELEASED, 
            TOTAL_REVENUE_BUDGET, ACTUAL_REVENUE, VARIATION, SCENARIO, VERSION, 
            FUNDS_AVAILABLE_PERCENTAGE
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """

        # แปลงค่า NaN ที่อาจเหลืออยู่เป็น None อีกครั้ง
        for _, row in df.iterrows():
            cursor.execute(insert_sql, tuple(
                None if pd.isna(x) else x for x in row))

        conn.commit()
        print("Insert data completed successfully!")

finally:
    conn.close()
