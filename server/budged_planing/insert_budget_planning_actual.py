import os
import pandas as pd
import pymysql

# ข้อมูลการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_password = 'TDyutdYdyudRTYDsEFOPI'
str_username = 'root'

# กำหนดเส้นทางของไฟล์ CSV
current_dir = os.path.dirname(__file__)
# เปลี่ยนชื่อไฟล์ CSV ตามต้องการ
file_path = os.path.join(
    current_dir, 'KKU_INT_EPM_18_ACTUALS_REP_RPT_T1 (1).csv')

# อ่านไฟล์ CSV
try:
    data = pd.read_csv(file_path, dtype={'FACULTY': str}, thousands=',')

    # ตรวจสอบชื่อคอลัมน์
    print("CSV Columns:", data.columns.tolist())

    # แปลงค่าตัวเลขให้เป็น float และจัดการค่า NaN
    numeric_columns = [
        'FISCAL_YEAR', 'TOTAL_BUDGET', 'COMMITMENTS', 'OBLIGATIONS', 'OTHER_CONSUMPTION', 'EXPENDITURES',
        'TOTAL_CONSUMPTION', 'FUNDS_AVAILABLE_AMOUNT', 'INITIAL_BUDGET', 'BUDGET_ADJUSTMENTS',
        'UNRELEASED', 'TOTAL_REVENUE_BUDGET', 'ACTUAL_REVENUE', 'VARIATION', 'FUNDS_AVAILABLE_PERCENTAGE'
    ]
    for col in numeric_columns:
        data[col] = pd.to_numeric(data[col], errors='coerce').fillna(0)

    # ตรวจสอบข้อมูลหลังจากการแปลง
    print("Sample data:")
    print(data.head())

except Exception as e:
    print(f"Error reading CSV file: {e}")
    exit()

# สร้างการเชื่อมต่อกับฐานข้อมูล
try:
    connection = pymysql.connect(
        host=str_hosting,
        user=str_username,
        password=str_password,
        database=str_database,
        charset='utf8mb4',
        autocommit=True
    )
    cursor = connection.cursor()

    # สร้างตารางหากยังไม่มี
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS budget_planning_actual (
        id INT AUTO_INCREMENT PRIMARY KEY,
        PERIOD VARCHAR(50),
        FISCAL_YEAR INT,
        PILLAR VARCHAR(255),
        STRATEGIC_PROJECT TEXT,
        FACULTY VARCHAR(255),
        FUND VARCHAR(255),
        OKR TEXT,
        PROJECT TEXT,
        PLAN TEXT,
        SUBPLAN TEXT,
        SERVICE TEXT,
        ACCOUNT VARCHAR(255),
        BUDGET_PERIOD VARCHAR(50),
        TOTAL_BUDGET DECIMAL(15,2),
        COMMITMENTS DECIMAL(15,2),
        OBLIGATIONS DECIMAL(15,2),
        OTHER_CONSUMPTION DECIMAL(15,2),
        EXPENDITURES DECIMAL(15,2),
        TOTAL_CONSUMPTION DECIMAL(15,2),
        FUNDS_AVAILABLE_AMOUNT DECIMAL(15,2),
        INITIAL_BUDGET DECIMAL(15,2),
        BUDGET_ADJUSTMENTS DECIMAL(15,2),
        UNRELEASED DECIMAL(15,2),
        TOTAL_REVENUE_BUDGET DECIMAL(15,2),
        ACTUAL_REVENUE DECIMAL(15,2),
        VARIATION DECIMAL(15,2),
        SCENARIO VARCHAR(255),
        VERSION VARCHAR(255),
        FUNDS_AVAILABLE_PERCENTAGE DECIMAL(15,2)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    '''
    cursor.execute(create_table_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        try:
            insert_query = '''
            INSERT INTO budget_planning_actual (
                PERIOD, FISCAL_YEAR, PILLAR, STRATEGIC_PROJECT, FACULTY, FUND, OKR, PROJECT, PLAN,
                SUBPLAN, SERVICE, ACCOUNT, BUDGET_PERIOD, TOTAL_BUDGET, COMMITMENTS, OBLIGATIONS,
                OTHER_CONSUMPTION, EXPENDITURES, TOTAL_CONSUMPTION, FUNDS_AVAILABLE_AMOUNT, INITIAL_BUDGET,
                BUDGET_ADJUSTMENTS, UNRELEASED, TOTAL_REVENUE_BUDGET, ACTUAL_REVENUE, VARIATION,
                SCENARIO, VERSION, FUNDS_AVAILABLE_PERCENTAGE
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
            '''
            cursor.execute(insert_query, (
                row['PERIOD'], row['FISCAL_YEAR'], row['PILLAR'], row['STRATEGIC_PROJECT'],
                row['FACULTY'], row['FUND'], row['OKR'], row['PROJECT'], row['PLAN'],
                row['SUBPLAN'], row['SERVICE'], row['ACCOUNT'], row['BUDGET_PERIOD'],
                row['TOTAL_BUDGET'], row['COMMITMENTS'], row['OBLIGATIONS'], row['OTHER_CONSUMPTION'],
                row['EXPENDITURES'], row['TOTAL_CONSUMPTION'], row['FUNDS_AVAILABLE_AMOUNT'],
                row['INITIAL_BUDGET'], row['BUDGET_ADJUSTMENTS'], row['UNRELEASED'],
                row['TOTAL_REVENUE_BUDGET'], row['ACTUAL_REVENUE'], row['VARIATION'],
                row['SCENARIO'], row['VERSION'], row['FUNDS_AVAILABLE_PERCENTAGE']
            ))
        except Exception as e:
            print(f"Error inserting row: {e}")

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into budget_planning_actual table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
