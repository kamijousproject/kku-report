import sys
import os
import pandas as pd
import pymysql

# ข้อมูลการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_password = 'TDyutdYdyudRTYDsEFOPI'
str_username = 'root'

# # กำหนดเส้นทางของไฟล์ CSV
# current_dir = os.path.dirname(__file__)
# # เปลี่ยนชื่อไฟล์ CSV ตามต้องการ
# file_path = os.path.join(current_dir, 'new_file.csv')

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
        'Faculty': '',
        'Plan': '',
        'Sub Plan': '',
        'Prog Q1': 0,
        'Prog Q2': 0,
        'Prog Q3': 0,
        'Prog Q4': 0,
        'Remark': '',
        'KPI': '',
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

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO budget_planning_sub_plan_kpi_progress (
            Faculty, Plan, Sub_Plan, Prog_Q1, Prog_Q2, Prog_Q3, Prog_Q4, Remark,
            KPI, Scenario, Version
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Faculty'],
            row['Plan'],
            row['Sub Plan'],
            row['Prog Q1'],
            row['Prog Q2'],
            row['Prog Q3'],
            row['Prog Q4'],
            row['Remark'],
            row['KPI'],
            row['Scenario'],
            row['Version']
        ))

    # บันทึกข้อมูล
    connection.commit()
    # print("Data inserted successfully into budget_planning_sub_plan_kpi_progress table.")
    print("SUCCESS")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
