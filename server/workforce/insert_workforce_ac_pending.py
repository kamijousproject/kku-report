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
file_path = os.path.join(current_dir, 'Form0 Actual_PENDING_COLUMN_FILE2_2025011900.csv')

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
        'Rate status': '',
        'Salary rate': 0,
        'Fund(FT)': '',
        'Govt. Fund': '',
        'Division Revenue': '',
        'OOP Central Revenue': '',
        'Academic Position Allowance': '',
        'Position Compensation (Mngt/Academic)': '',
        'Level 8 Compensation': '',
        'Full Salary Compensation': '',
        'Executive Allowance': '',
        'Executive Compensation': '',
        'Down Payment According to Cabinet Resolution': '',
        'PTK Compensation': '',
        'PTS Compensation': ''
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

    # สร้างตาราง workforce_ac_pending
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS workforce_ac_pending (
        Account VARCHAR(50),
        Scenario VARCHAR(50),
        Version VARCHAR(50),
        Faculty VARCHAR(50),
        Fund VARCHAR(50),
        NHR VARCHAR(50),
        Personnel_Type VARCHAR(100),
        All_PositionTypes VARCHAR(100),
        Position VARCHAR(100),
        Position_Number VARCHAR(100),
        Rate_status VARCHAR(50),
        Salary_rate FLOAT,
        Fund_FT VARCHAR(50),
        Govt_Fund VARCHAR(50),
        Division_Revenue VARCHAR(50),
        OOP_Central_Revenue VARCHAR(50),
        Academic_Position_Allowance VARCHAR(50),
        Position_Compensation_Mngt_Academic VARCHAR(50),
        Level_8_Compensation VARCHAR(50),
        Full_Salary_Compensation VARCHAR(50),
        Executive_Allowance VARCHAR(50),
        Executive_Compensation VARCHAR(50),
        Down_Payment_According_to_Cabinet_Resolution VARCHAR(50),
        PTK_Compensation VARCHAR(50),
        PTS_Compensation VARCHAR(50)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    '''
    cursor.execute(create_table_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO workforce_ac_pending (
            Account, Scenario, Version, Faculty, Fund, NHR, Personnel_Type,
            All_PositionTypes, Position, Position_Number, Rate_status, Salary_rate,
            Fund_FT, Govt_Fund, Division_Revenue, OOP_Central_Revenue,
            Academic_Position_Allowance, Position_Compensation_Mngt_Academic,
            Level_8_Compensation, Full_Salary_Compensation, Executive_Allowance,
            Executive_Compensation, Down_Payment_According_to_Cabinet_Resolution,
            PTK_Compensation, PTS_Compensation
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
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
            row['Rate status'],
            row['Salary rate'],
            row['Fund(FT)'],
            row['Govt. Fund'],
            row['Division Revenue'],
            row['OOP Central Revenue'],
            row['Academic Position Allowance'],
            row['Position Compensation (Mngt/Academic)'],
            row['Level 8 Compensation'],
            row['Full Salary Compensation'],
            row['Executive Allowance'],
            row['Executive Compensation'],
            row['Down Payment According to Cabinet Resolution'],
            row['PTK Compensation'],
            row['PTS Compensation']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into workforce_ac_pending table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
