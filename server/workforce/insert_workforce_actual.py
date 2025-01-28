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
file_path = os.path.join(current_dir, 'Form0 Actual_2025011900.csv')

# อ่านไฟล์ CSV
try:
    
    data = pd.read_csv(file_path, dtype={'Faculty': str})

    # แก้ไข DataFrame ให้จัดการค่า NaN ก่อน
    data = data.fillna(value={
        'Account': '',
        'Scenario': '',
        'Version': '',
        'Faculty': '',
        'Fund': '',
        'NHR': '',
        'Personnel_Type': '',
        'All_PositionTypes': '',
        'Position': '',
        'Position_Number': '',
        "Worker's Name - Surname": '',
        'Employment Type': '',
        'Employee ID': '',
        'National ID': '',
        'Position Level': '',
        'Job Family': '',
        'Personnel Group': '',
        'Management Position Name': '',
        'Contract Type': '',
        'Contract Period (short term)': '',
        'Position Qualififcations': '',
        'Employee Qualifications': ''
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

    # สร้างตาราง workforce_actual
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS workforce_actual (
        Account VARCHAR(50),
        Scenario VARCHAR(50),
        Version VARCHAR(50),
        Faculty VARCHAR(50),
        Fund VARCHAR(50),
        NHR VARCHAR(50),
        Personnel_Type VARCHAR(50),
        All_PositionTypes VARCHAR(50),
        Position VARCHAR(50),
        Position_Number VARCHAR(50),
        Workers_Name_Surname VARCHAR(100),
        Employment_Type VARCHAR(50),
        Employee_ID VARCHAR(50),
        National_ID VARCHAR(50),
        Position_Level VARCHAR(50),
        Job_Family VARCHAR(50),
        Personnel_Group VARCHAR(50),
        Management_Position_Name VARCHAR(50),
        Contract_Type VARCHAR(50),
        Contract_Period_Short_Term VARCHAR(50),
        Position_Qualififcations VARCHAR(50),
        Employee_Qualifications VARCHAR(50)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    '''
    cursor.execute(create_table_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO workforce_actual (
            Account, Scenario, Version, Faculty, Fund, NHR, Personnel_Type,
            All_PositionTypes, Position, Position_Number, Workers_Name_Surname,
            Employment_Type, Employee_ID, National_ID, Position_Level, Job_Family,
            Personnel_Group, Management_Position_Name, Contract_Type, Contract_Period_Short_Term,
            Position_Qualififcations, Employee_Qualifications
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Account'],
            row['Scenario'],
            row['Version'],
            row['Faculty'],
            row['Fund'],
            row['NHR'],
            row['Personnel_Type'],
            row['All_PositionTypes'],
            row['Position'],
            row['Position_Number'],
            row["Worker's Name - Surname"],
            row['Employment Type'],
            row['Employee ID'],
            row['National ID'],
            row['Position Level'],
            row['Job Family'],
            row['Personnel Group'],
            row['Management Position Name'],
            row['Contract Type'],
            row['Contract Period (short term)'],
            row['Position Qualififcations'],
            row['Employee Qualifications']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into workforce_actual table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
