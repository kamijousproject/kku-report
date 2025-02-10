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
        'Account': '',
        'Scenario': '',
        'Version': '',
        'Faculty': '',
        'Personnel_Type': '',
        'All_PositionTypes': '',
        'Position': '',
        'Position_Number': '',
        'Wish to Continue Employement': '',
        'Reason for not rehiring': '',
        'Performance Evaluation (In Percentage)': 0,
        'Performance Evaluation': ''
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
    
    truncate_query = "TRUNCATE TABLE workforce_current_position_request;"
    cursor.execute(truncate_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO workforce_current_position_request (
            Account, Scenario, Version, Faculty, Personnel_Type, All_PositionTypes, Position,
            Position_Number, Wish_to_Continue_Employement, Reason_for_not_rehiring,
            Performance_Evaluation_Percentage, Performance_Evaluation
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Account'],
            row['Scenario'],
            row['Version'],
            row['Faculty'],
            row['Personnel_Type'],
            row['All_PositionTypes'],
            row['Position'],
            row['Position_Number'],
            row['Wish to Continue Employement'],
            row['Reason for not rehiring'],
            row['Performance Evaluation (In Percentage)'],
            row['Performance Evaluation']
        ))

    # บันทึกข้อมูล
    connection.commit()
    # print("Data inserted successfully into workforce_current_position_request table.")
    print("SUCCESS")
except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
