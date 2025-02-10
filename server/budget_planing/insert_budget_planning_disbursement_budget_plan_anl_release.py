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
# file_path = os.path.join(current_dir, 'Disbursement Budget Plan-ANL-RELEASE-1.csv')
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
    data = pd.read_csv(file_path, dtype={'Faculty': str}, thousands=',')

    # แก้ไข DataFrame ให้จัดการค่า NaN ก่อน
    data = data.fillna(value={
        'Service': '',
        'Faculty': '',
        'Fund': '',
        'Project': '',
        'Plan': '',
        'Sub Plan': '',
        'Plan_Desc': '',
        'SubPlan_Desc': '',
        'Proj_Desc': '',
        'GF Item Code': '',
        'Release Amount': 0.0,
        'Pre_Release_Amount': 0.0,
        'Account': '',
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
    
    truncate_query = "TRUNCATE TABLE budget_planning_disbursement_budget_plan_anl_release;"
    cursor.execute(truncate_query)
    connection.commit()

    # สร้างตารางหากยังไม่มี
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS budget_planning_disbursement_budget_plan_anl_release (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Service VARCHAR(50),
        Faculty VARCHAR(50),
        Fund VARCHAR(50),
        Project VARCHAR(50),
        Plan VARCHAR(50),
        Sub_Plan VARCHAR(50),
        Plan_Desc TEXT,
        SubPlan_Desc TEXT,
        Proj_Desc TEXT,
        GF_Item_Code VARCHAR(50),
        Release_Amount DECIMAL(15,2),
        Pre_Release_Amount DECIMAL(15,2),
        Account VARCHAR(50),
        Scenario VARCHAR(50),
        Version VARCHAR(50)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    '''
    cursor.execute(create_table_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO budget_planning_disbursement_budget_plan_anl_release (
            Service, Faculty, Fund, Project, Plan, Sub_Plan, Plan_Desc, SubPlan_Desc,
            Proj_Desc, GF_Item_Code, Release_Amount, Pre_Release_Amount, Account,
            Scenario, Version
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Service'],
            row['Faculty'],
            row['Fund'],
            row['Project'],
            row['Plan'],
            row['Sub Plan'],
            row['Plan_Desc'],
            row['SubPlan_Desc'],
            row['Proj_Desc'],
            row['GF Item Code'],
            row['Release Amount'],
            row['Pre_Release_Amount'],
            row['Account'],
            row['Scenario'],
            row['Version']
        ))

    # บันทึกข้อมูล
    connection.commit()
    # print("Data inserted successfully into budget_planning_disbursement_budget_plan_anl_release table.")
    print("SUCCESS")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
