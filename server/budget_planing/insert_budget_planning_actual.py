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

# อ่านไฟล์ CSV โดยบังคับให้ไม่มีค่าหาย
df = pd.read_csv(file_path, dtype=str, keep_default_na=False)

# ลบคอลัมน์ Unnamed ที่เกิดจาก `,` เกินมา
df = df.iloc[:, :25]  # เลือกเฉพาะ 25 คอลัมน์แรก

# ตรวจสอบว่าแก้ปัญหาได้หรือไม่
# print(f"Fixed CSV Columns: {df.columns}")
if len(df.columns) != 25:
    print(f"Error: CSV still has {len(df.columns)} columns, expected 25.")
    sys.exit(1)

# แทนที่ค่าที่ว่างเปล่าด้วย `None`
df = df.where(pd.notna(df), None)

# แปลงค่าคอลัมน์ที่เป็นตัวเลขให้เป็น 0.0 แทนค่า None
numeric_columns = [
    "TOTAL_BUDGET", "COMMITMENTS", "OBLIGATIONS", "OTHER_CONSUMPTION",
    "EXPENDITURES", "TOTAL_CONSUMPTION", "FUNDS_AVAILABLE_AMOUNT",
    "INITIAL_BUDGET", "BUDGET_ADJUSTMENTS", "UNRELEASED",
    "FUNDS_AVAILABLE_PERCENTAGE"
]

for col in numeric_columns:
    if col in df.columns:
        df[col] = df[col].apply(lambda x: float(
            x) if x not in [None, ""] else 0.0)

# เชื่อมต่อฐานข้อมูล
conn = pymysql.connect(**db_config)

try:
    with conn.cursor() as cursor:
        # สร้างตารางก่อน
        create_table_sql = """
        CREATE TABLE IF NOT EXISTS budget_planning_actual (
            id INT AUTO_INCREMENT PRIMARY KEY,
            PERIOD VARCHAR(10),
            FISCAL_YEAR VARCHAR(10),
            SCENARIO VARCHAR(50),
            STRATEGIC_PROJECT VARCHAR(255),
            FACULTY VARCHAR(50),
            FUND VARCHAR(50),
            OKR VARCHAR(255),
            PROJECT VARCHAR(50),
            PLAN VARCHAR(50),
            SUBPLAN VARCHAR(50),
            ACCOUNT VARCHAR(50),
            SERVICE VARCHAR(50),
            BUDGET_PERIOD VARCHAR(10),
            TOTAL_BUDGET DECIMAL(18,2) DEFAULT 0.00,
            COMMITMENTS DECIMAL(18,2) DEFAULT 0.00,
            OBLIGATIONS DECIMAL(18,2) DEFAULT 0.00,
            OTHER_CONSUMPTION DECIMAL(18,2) DEFAULT 0.00,
            EXPENDITURES DECIMAL(18,2) DEFAULT 0.00,
            TOTAL_CONSUMPTION DECIMAL(18,2) DEFAULT 0.00,
            FUNDS_AVAILABLE_AMOUNT DECIMAL(18,2) DEFAULT 0.00,
            INITIAL_BUDGET DECIMAL(18,2) DEFAULT 0.00,
            BUDGET_ADJUSTMENTS DECIMAL(18,2) DEFAULT 0.00,
            UNRELEASED DECIMAL(18,2) DEFAULT 0.00,
            VERSION VARCHAR(50),
            FUNDS_AVAILABLE_PERCENTAGE DECIMAL(5,2) DEFAULT 0.00
        );
        """
        cursor.execute(create_table_sql)
        conn.commit()

        # ลบข้อมูลเก่าก่อน (ใช้ TRUNCATE)
        truncate_query = "TRUNCATE TABLE budget_planning_actual;"
        cursor.execute(truncate_query)
        conn.commit()

        # คำสั่ง SQL สำหรับ Insert ข้อมูล
        insert_sql = """
        INSERT INTO budget_planning_actual (
            PERIOD, FISCAL_YEAR, SCENARIO, STRATEGIC_PROJECT, FACULTY, FUND, OKR, PROJECT,
            PLAN, SUBPLAN, ACCOUNT, SERVICE, BUDGET_PERIOD, TOTAL_BUDGET, COMMITMENTS, 
            OBLIGATIONS, OTHER_CONSUMPTION, EXPENDITURES, TOTAL_CONSUMPTION, 
            FUNDS_AVAILABLE_AMOUNT, INITIAL_BUDGET, BUDGET_ADJUSTMENTS, UNRELEASED, 
            VERSION, FUNDS_AVAILABLE_PERCENTAGE
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        """

        for _, row in df.iterrows():
            data_tuple = tuple(row[col] for col in df.columns)

            try:
                cursor.execute(insert_sql, data_tuple)
            except Exception as e:
                print(f"Error inserting row: {data_tuple}")
                print(f"Exception: {e}")

    conn.commit()
    print("SUCCESS")

finally:
    conn.close()
