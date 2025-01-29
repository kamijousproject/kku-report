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
        'Account': '',
        'Scenario': '',
        'Version': '',
        'Faculty': '',
        'Personnel_Type': '',
        'All_PositionTypes': '',
        'Position': '',
        'Position_Number': '',
        'Salary rate': 0,
        'Fund(FT)': 0,
        'Govt. Fund': 0,
        'Division Revenue': 0,
        'OOP Central Revenue': 0,
        'Continue Employement': '',
        'WF_Plan': '',
        'WF_SubPlan': '',
        'WF_Project': ''
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
        INSERT INTO workforce_current_positions_allocation (
            Account, Scenario, Version, Faculty, Personnel_Type, All_PositionTypes, Position,
            Position_Number, Salary_rate, Fund_FT, Govt_Fund, Division_Revenue,
            OOP_Central_Revenue, Continue_Employement, WF_Plan, WF_SubPlan, WF_Project
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
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
            row['Salary rate'],
            row['Fund(FT)'],
            row['Govt. Fund'],
            row['Division Revenue'],
            row['OOP Central Revenue'],
            row['Continue Employement'],
            row['WF_Plan'],
            row['WF_SubPlan'],
            row['WF_Project']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into workforce_current_positions_allocation table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
