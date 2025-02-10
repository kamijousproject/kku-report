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
# file_path = os.path.join(current_dir, '68KS00_20250125.csv')

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
        'Faculty': '',
        'OKR': '',
        'Y1': 0,
        'Y2': 0,
        'Y3': 0,
        'Y4': 0,
        'Budget Amount': 0,
        'Tiers & Deploy': '',
        'Responsible person': '',
        'Start Date': '',
        'End Date': '',
        'UOM': '',
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
    
    truncate_query = "TRUNCATE TABLE planning_kku_strategic_plan;"
    cursor.execute(truncate_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO planning_kku_strategic_plan (
            Strategic_Object, Strategic_Project, Faculty, OKR, Y1, Y2, Y3, Y4,
            Budget_Amount, Tiers_Deploy, Responsible_person, Start_Date, End_Date, UOM,
            Scenario, Version
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Strategic Object'],
            row['Strategic Project'],
            row['Faculty'],
            row['OKR'],
            row['Y1'],
            row['Y2'],
            row['Y3'],
            row['Y4'],
            row['Budget Amount'],
            row['Tiers & Deploy'],
            row['Responsible person'],
            row['Start Date'],
            row['End Date'],
            row['UOM'],
            row['Scenario'],
            row['Version']
        ))

    # บันทึกข้อมูล
    connection.commit()
    # print("Data inserted successfully into planning_kku_strategic_plan table.")
    print("SUCCESS")
except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
