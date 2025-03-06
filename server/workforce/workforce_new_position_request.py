import os
import sys
import pandas as pd
import pymysql

# ข้อมูลการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_password = 'TDyutdYdyudRTYDsEFOPI'
str_username = 'root'

if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]
file_path = os.path.abspath(os.path.join(file_path, os.pardir))
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
        'NHR': '',
        'Personnel_Type': '',
        'All_PositionTypes': '',
        'Position': '',
        'Job Code': '',
        'Position Qualififcations': '',
        'Fund(FT)': 0,
        'Job Family': '',
        'Requested HC (unit)': '',
        'Personnel Group': '',
        'Field of Study': '',
        'Employment Type': '',
        'Contract Type': '',
        'Salary / Wages (Baht per month)': 0,
        'Specific reasons': '',
        'Worker\'s Name - Surname': '',
        'Age (years)': 0,  # คอลัมน์ใหม่
        'Academic Position': '',
        'Hiring Start-End Date': '',
        'Nationality (only foreigners)': '',
        'Additional information (other)': '',  # คอลัมน์ใหม่
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

    truncate_query = "TRUNCATE TABLE workforce_new_position_request;"
    cursor.execute(truncate_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO workforce_new_position_request (
            Account, Scenario, Version, Faculty, NHR, Personnel_Type, All_PositionTypes,
            Position, Job_Code, Position_Qualififcations, Fund_FT, Job_Family, Requested_HC_unit, Personnel_Group,
            Field_of_Study, Employment_Type, Contract_Type, Salary_Wages_Baht_per_month,
            Specific_reasons, Workers_Name_Surname, Age_years, Academic_Position,
            Hiring_Start_End_Date, Nationality_only_foreigners, Additional_information_other,
            WF_Plan, WF_SubPlan, WF_Project
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Account'],
            row['Scenario'],
            row['Version'],
            row['Faculty'],
            row['NHR'],
            row['Personnel_Type'],
            row['All_PositionTypes'],
            row['Position'],
            row['Job Code'],
            row['Position Qualififcations'],
            row['Fund(FT)'],
            row['Job Family'],
            row['Requested_HC_unit'],
            row['Personnel Group'],
            row['Field of Study'],
            row['Employment Type'],
            row['Contract Type'],
            row['Salary / Wages (Baht per month)'],
            row['Specific reasons'],
            row["Worker's Name - Surname"],
            row['Age (years)'],  # คอลัมน์ใหม่
            row['Academic Position'],
            row['Hiring Start-End Date'],
            row['Nationality (only foreigners)'],
            row['Additional information (other)'],  # คอลัมน์ใหม่
            row['WF_Plan'],
            row['WF_SubPlan'],
            row['WF_Project']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("SUCCESS")
except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
