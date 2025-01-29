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
file_path = os.path.join(current_dir, '68KKU_OKR_20250125.csv')

# อ่านไฟล์ CSV
try:
    data = pd.read_csv(file_path, dtype={'Faculty': str})

    # แก้ไข DataFrame ให้จัดการค่า NaN ก่อน
    data = data.fillna(value={
        'Strategic Object': '',
        'Faculty': '',
        'OKR': '',
        'Quarter Progress Value': 0,
        'OKR Progress Details': '',
        'Prob/Solution/Suggestion': '',
        'Responsible person': '',
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

    # สร้างตาราง planing_okr
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS planning_faculty_okr_progress (
        Strategic_Object VARCHAR(50),
        Faculty TEXT,
        OKR VARCHAR(50),
        Quarter_Progress_Value FLOAT,
        OKR_Progress_Details TEXT,
        Prob_Solution_Suggestion TEXT,
        Responsible_person TEXT,
        Scenario TEXT,
        Version TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    '''
    cursor.execute(create_table_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO planning_faculty_okr_progress (
            Strategic_Object, Faculty, OKR, Quarter_Progress_Value, OKR_Progress_Details,
            Prob_Solution_Suggestion, Responsible_person, Scenario, Version
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Strategic Object'],
            row['Faculty'],
            row['OKR'],
            row['Quarter Progress Value'],
            row['OKR Progress Details'],
            row['Prob/Solution/Suggestion'],
            row['Responsible person'],
            row['Scenario'],
            row['Version']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into planing_okr table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
