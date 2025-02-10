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
# # เปลี่ยนชื่อไฟล์ CSV ตามต้องการ
# file_path = os.path.join(current_dir, '4year Workforce Plan.csv')

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
        'Account': '',
        'Scenario': '',
        'Version': '',
        'Faculty': '',
        'All_PositionTypes': '',
        'Position': '',
        'Job Family': '',
        'Sub Job': '',
        'Govt Officer': 0,
        'Uni Staff': 0,
        'Permanent Employee': 0,
        'Uni Employee': 0,
        'FTES criteria': '',
        'Research Workload Criteria': '',
        'Workload Criteria Academic Services': '',
        'WF': '',
        'Year 1 Headcount': 0,
        'Year 2 Headcount': 0,
        'Year 3 Headcount': 0,
        'Year 4 Headcount': 0,
        'Outsource Year 1': 0,
        'Outsource Year 2': 0,
        'Outsource Year 3': 0,
        'Outsource Year 4': 0,
        'Remark': ''
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
        INSERT INTO workforce_4year_plan (
            Account, Scenario, Version, Faculty, All_PositionTypes, Position, Job_Family, Sub_Job,
            Govt_Officer, Uni_Staff, Permanent_Employee, Uni_Employee, FTES_criteria,
            Research_Workload_Criteria, Workload_Criteria_Academic_Services, WF,
            Year_1_Headcount, Year_2_Headcount, Year_3_Headcount, Year_4_Headcount,
            Outsource_Year_1, Outsource_Year_2, Outsource_Year_3, Outsource_Year_4, Remark
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Account'],
            row['Scenario'],
            row['Version'],
            row['Faculty'],
            row['All_PositionTypes'],
            row['Position'],
            row['Job Family'],
            row['Sub Job'],
            row['Govt Officer'],
            row['Uni Staff'],
            row['Permanent Employee'],
            row['Uni Employee'],
            row['FTES criteria'],
            row['Research Workload Criteria'],
            row['Workload Criteria Academic Services'],
            row['WF'],
            row['Year 1 Headcount'],
            row['Year 2 Headcount'],
            row['Year 3 Headcount'],
            row['Year 4 Headcount'],
            row['Outsource Year 1'],
            row['Outsource Year 2'],
            row['Outsource Year 3'],
            row['Outsource Year 4'],
            row['Remark']
        ))

    # บันทึกข้อมูล
    connection.commit()
    # print("Data inserted successfully into workforce_4year_plan table.")
    print("SUCCESS")
except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
