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
file_path = os.path.join(current_dir, 'new_file.csv')

# อ่านไฟล์ CSV
try:
    data = pd.read_csv(file_path, dtype={'Faculty': str})

    # แก้ไข DataFrame ให้จัดการค่า NaN ก่อน
    data = data.fillna(value={
        'Faculty': '',
        'Project': '',
        "Proj's KPI Name": '',
        "Proj's KPI Target": 0,
        "UoM for Proj's KPI": '',
        'Reason': '',
        'Objective': '',
        'Project Output': '',
        'Project Outcome': '',
        'Project Impact': '',
        'Process Plan': '',
        'KKU_Strategic_Plan_LOV': '',
        'OKRs_LOV': '',
        'Principles of good governance': '',
        'SDGs': '',
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
        INSERT INTO budget_planning_project_kpi (
            Faculty, Project, Proj_KPI_Name, Proj_KPI_Target, UoM_for_Proj_KPI, Reason,
            Objective, Project_Output, Project_Outcome, Project_Impact, Process_Plan,
            KKU_Strategic_Plan_LOV, OKRs_LOV, Principles_of_good_governance, SDGs,
            KPI, Scenario, Version
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Faculty'],
            row['Project'],
            row["Proj's KPI Name"],
            row["Proj's KPI Target"],
            row["UoM for Proj's KPI"],
            row['Reason'],
            row['Objective'],
            row['Project Output'],
            row['Project Outcome'],
            row['Project Impact'],
            row['Process Plan'],
            row['KKU_Strategic_Plan_LOV'],
            row['OKRs_LOV'],
            row['Principles of good governance'],
            row['SDGs'],
            row['KPI'],
            row['Scenario'],
            row['Version']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into budget_planning_project_kpi table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
