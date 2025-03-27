import os
import sys
import pandas as pd
import pymysql

# ข้อมูลการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_password = 'TDyutdYdyudRTYDsEFOPI'
str_username = 'root'

# if len(sys.argv) < 2:
#     print("Error: No CSV file provided.")
#     sys.exit(1)

# file_path = sys.argv[1]
# file_path = os.path.abspath(os.path.join(file_path, os.pardir))
# file_path = os.path.join(file_path, os.path.basename(sys.argv[1]))

current_dir = os.path.dirname(__file__)
file_path = os.path.join(current_dir, 'Bursting_EPM_Report_workforce_new_position_request.csv')

# กำหนดชื่อคอลัมน์ให้ตรงกับฐานข้อมูล
column_names = [
    'NHR', 'Faculty', 'Personnel_Type', 'All_PositionTypes', 'Position',
    'Job_Code', 'Position_Qualififcations', 'Fund_FT', 'Job_Family',
    'Personnel_Group', 'Field_of_Study', 'Employment_Type', 'Contract_Type',
    'Salary_Wages_Baht_per_month', 'Specific_reasons', "Workers_Name_Surname",
    'Age_years', 'Academic_Position', 'Hiring_Start_End_Date',
    'Nationality_only_foreigners', 'Additional_information_other',
    'WF_Plan', 'WF_SubPlan', 'WF_Project','Account', 'Scenario', 'Version'
]

# อ่านไฟล์ CSV โดยข้ามบรรทัดที่ไม่จำเป็น และกำหนดคอลัมน์เอง
try:
    data = pd.read_csv(file_path, skiprows=3, names=column_names, dtype=str)

    # ตรวจสอบว่าคอลัมน์ครบถ้วน
    missing_columns = [col for col in column_names if col not in data.columns]
    if missing_columns:
        print(f"Error: CSV file is missing the following columns: {missing_columns}")
        sys.exit(1)

    # แทนค่า NaN ด้วยค่าที่เหมาะสม
    data = data.fillna({
        'Salary_Wages_Baht_per_month': 0,
        'Age_years': 0,
        'Specific_reasons': '',
        'Workers_Name_Surname': '',
        'Nationality_only_foreigners': '',
        'Additional_information_other': '',
        'Account': 'New Position Details',
        'Scenario': 'NEW HIRE ANNUAL PLAN',
        'Version': 'Original'
    })

    # **ปรับปรุง Faculty ให้ใช้แค่ 5 หลักแรก**
    data['Faculty'] = data['Faculty'].apply(lambda x: x[:5] if isinstance(x, str) else x)

except Exception as e:
    print(f"Error reading CSV file: {e}")
    sys.exit(1)

# เชื่อมต่อฐานข้อมูลและบันทึกข้อมูล
try:
    connection = pymysql.connect(
        host=str_hosting,
        user=str_username,
        password=str_password,
        database=str_database,
        charset='utf8mb4'
    )
    cursor = connection.cursor()

    # เตรียม SQL สำหรับ INSERT
    insert_query = '''
    INSERT INTO workforce_new_position_request (
        NHR, Faculty, Personnel_Type, All_PositionTypes, Position,
        Job_Code, Position_Qualififcations, Fund_FT, Job_Family,
        Personnel_Group, Field_of_Study, Employment_Type, Contract_Type,
        Salary_Wages_Baht_per_month, Specific_reasons, Workers_Name_Surname,
        Age_years, Academic_Position, Hiring_Start_End_Date,
        Nationality_only_foreigners, Additional_information_other,
        WF_Plan, WF_SubPlan, WF_Project, Account, Scenario, Version
    ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
    '''

    # วนลูปเพิ่มข้อมูลลงฐานข้อมูล
    for _, row in data.iterrows():
        cursor.execute(insert_query, (
            row['NHR'], row['Faculty'], row['Personnel_Type'], row['All_PositionTypes'],
            row['Position'], row['Job_Code'], row['Position_Qualififcations'], row['Fund_FT'],
            row['Job_Family'], row['Personnel_Group'], row['Field_of_Study'], row['Employment_Type'],
            row['Contract_Type'], row['Salary_Wages_Baht_per_month'], row['Specific_reasons'],
            row['Workers_Name_Surname'], row['Age_years'], row['Academic_Position'],
            row['Hiring_Start_End_Date'], row['Nationality_only_foreigners'],
            row['Additional_information_other'], row['WF_Plan'], row['WF_SubPlan'],
            row['WF_Project'], row['Account'], row['Scenario'], row['Version']
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
