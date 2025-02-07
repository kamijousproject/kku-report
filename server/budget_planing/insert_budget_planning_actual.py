import pandas as pd
import pymysql
import sys
import os

# ตรวจสอบว่ามีพารามิเตอร์ไฟล์ CSV ที่ส่งมาหรือไม่
if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]

# ตรวจสอบว่าไฟล์ CSV มีอยู่จริงหรือไม่
if not os.path.exists(file_path):
    print(f"Error: File '{file_path}' not found.")
    sys.exit(1)

# ตั้งค่าการเชื่อมต่อ MySQL
db_config = {
    'host': '110.164.146.250',
    'user': 'root',
    'password': 'TDyutdYdyudRTYDsEFOPI',
    'database': 'epm_report',
    'charset': 'utf8mb4'
}

# อ่านไฟล์ CSV
df = pd.read_csv(file_path, dtype=str)
df = df.where(pd.notna(df), None)

# เชื่อมต่อฐานข้อมูล
conn = pymysql.connect(**db_config)

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

        # คำสั่ง SQL สำหรับ Insert ข้อมูล
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

        for _, row in df.iterrows():
            cursor.execute(insert_sql, tuple(
                None if pd.isna(x) else x for x in row))

        conn.commit()
        print("Insert data completed successfully!")

finally:
    conn.close()
