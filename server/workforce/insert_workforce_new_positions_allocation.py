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
# file_path = os.path.join(current_dir, 'New Positions Allocation.csv')

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
        'NHR': '',
        'Personnel_Type': '',
        'All_PositionTypes': '',
        'Position': '',
        'Job Code': '',
        'Name-Surname (If change)': '',
        'Current HC of the Position': 0,
        'Retiring in Current Year': 0,
        'Retiring in Next Year': 0,
        'WF': '',
        'Allocation Consideration': '',
        'Salary rate': 0,
        'Fund(FT)': 0,
        'Govt. Fund': 0,
        'Division Revenue': 0,
        'OOP Central Revenue': 0,
        'Allocation Conditions': '',
        'University Staff (Govt Budget)': 0,
        'New_Position_No.of Uni Staff (Gov)': 0
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
        INSERT INTO workforce_new_positions_allocation (
            Account, Scenario, Version, Faculty, NHR, Personnel_Type, All_PositionTypes, Position,
            Job_Code, Name_Surname_If_change, Current_HC_of_the_Position, Retiring_in_Current_Year,
            Retiring_in_Next_Year, WF, Allocation_Consideration, Salary_rate, Fund_FT,
            Govt_Fund, Division_Revenue, OOP_Central_Revenue, Allocation_Conditions,
            University_Staff_Govt_Budget, New_Position_No_of_Uni_Staff_Gov
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
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
            row['Name-Surname (If change)'],
            row['Current HC of the Position'],
            row['Retiring in Current Year'],
            row['Retiring in Next Year'],
            row['WF'],
            row['Allocation Consideration'],
            row['Salary rate'],
            row['Fund(FT)'],
            row['Govt. Fund'],
            row['Division Revenue'],
            row['OOP Central Revenue'],
            row['Allocation Conditions'],
            row['University Staff (Govt Budget)'],
            row['New_Position_No.of Uni Staff (Gov)']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into workforce_new_positions_allocation table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
