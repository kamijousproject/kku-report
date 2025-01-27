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
file_path = os.path.join(current_dir, '68KA00_20250125.csv')

# อ่านไฟล์ CSV
try:
    data = pd.read_csv(file_path, dtype={'Faculty': str})

    # แก้ไข DataFrame ให้จัดการค่า NaN ก่อน
    data = data.fillna(value={
        'Strategic Object': '',
        'Strategic Project': '',
        'Faculty': '',
        'OKR': '',
        'Target (OKR : Objective and Key Result)': 0,
        'UOM': '',
        'Start Date': '',
        'End Date': '',
        'Budget Amount': 0,
        'Tiers & Deploy': '',
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

    # สร้างตาราง planing_ka
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS planing_ka (
        Strategic_Object VARCHAR(50),
        Strategic_Project VARCHAR(50),
        Faculty TEXT,
        OKR VARCHAR(50),
        Target_OKR_Objective_and_Key_Result FLOAT,
        UOM VARCHAR(50),
        Start_Date VARCHAR(50),
        End_Date VARCHAR(50),
        Budget_Amount FLOAT,
        Tiers_Deploy TEXT,
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
        INSERT INTO planing_ka (
            Strategic_Object, Strategic_Project, Faculty, OKR, Target_OKR_Objective_and_Key_Result,
            UOM, Start_Date, End_Date, Budget_Amount, Tiers_Deploy,
            Responsible_person, Scenario, Version
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Strategic Object'],
            row['Strategic Project'],
            row['Faculty'],
            row['OKR'],
            row['Target (OKR : Objective and Key Result)'],
            row['UOM'],
            row['Start Date'],
            row['End Date'],
            row['Budget Amount'],
            row['Tiers & Deploy'],
            row['Responsible person'],
            row['Scenario'],
            row['Version']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into planing_ka table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
