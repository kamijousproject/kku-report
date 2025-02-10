import os
import sys
import pandas as pd
import pymysql

# ข้อมูลการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_password = 'TDyutdYdyudRTYDsEFOPI'
str_username = 'root'

# # กำหนดเส้นทางของไฟล์ CSV
# current_dir = os.path.dirname(__file__)
# file_path = os.path.join(current_dir, '68KKU_Project_20250125.csv')

if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]

# ย้อนกลับไป 1 path
file_path = os.path.abspath(os.path.join(file_path, os.pardir))

# รวม path กับชื่อไฟล์เดิม เพื่อให้ได้ path ของไฟล์ CSV ที่ถูกต้อง
file_path = os.path.join(file_path, os.path.basename(sys.argv[1]))

# อ่านไฟล์ CSV
try:
    data = pd.read_csv(file_path, dtype={'Faculty': str})

    # แก้ไข DataFrame ให้จัดการค่า NaN ก่อน
    data = data.fillna(value={
        'Strategic Object': '',
        'Strategic Project': '',
        'Faculty': 0,
        'Progress Status': '',
        'Allocated budget': 0,
        'Actual Spend Amount': 0,
        'Strategic Project Progress Details': '',
        'Obstacles': '',
        'Scenario': '',
        'Version': ''
    })

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
        charset='utf8mb4'
    )
    cursor = connection.cursor()

    # สร้างตาราง planing_project
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS planning_faculty_project_progress (
        Strategic_Object VARCHAR(50),
        Strategic_Project VARCHAR(50),
        Faculty INT,
        Progress_Status VARCHAR(50),
        Allocated_budget FLOAT,
        Actual_Spend_Amount FLOAT,
        Strategic_Project_Progress_Details TEXT,
        Obstacles TEXT,
        Scenario TEXT,
        Version TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    '''
    cursor.execute(create_table_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO planning_faculty_project_progress (
            Strategic_Object, Strategic_Project, Faculty, Progress_Status, Allocated_budget,
            Actual_Spend_Amount, Strategic_Project_Progress_Details, Obstacles, Scenario, Version
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Strategic Object'],
            row['Strategic Project'],
            row['Faculty'],
            row['Progress Status'],
            row['Allocated budget'],
            row['Actual Spend Amount'],
            row['Strategic Project Progress Details'],
            row['Obstacles'],
            row['Scenario'],
            row['Version']
        ))

    # บันทึกข้อมูล
    connection.commit()
    # print("Data inserted successfully into planing_project table.")
    print("SUCCESS")
except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
