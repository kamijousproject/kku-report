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
file_path = os.path.join(current_dir, 'Allocated Annual Budget_05.csv')

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
        'KKU Item Code': '',
        'KKU Item Name': '',
        'GF Item Code': '',
        'GF Item Name': '',
        'Q1_Spending Plan': 0.0,
        'Q2_Spending Plan': 0.0,
        'Q3_Spending Plan': 0.0,
        'Q4_Spending Plan': 0.0,
        'Allocated Quantity': 0,
        'Allocated Unit Price': 0.0,
        'Allocated UOM': '',
        'Reason': '',
        'Objective': '',
        'Utility': '',
        'Place': '',
        'Account': '',
        'Scenario': '',
        'Version': '',
        'Allocated_Total_Amount_Quantity': 0.0,
        'Plan_Desc': '',
        'SubPlan_Desc': '',
        'Proj_Desc': ''
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

    # สร้างตารางหากยังไม่มี
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS budget_planning_allocated_annual_budget_plan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Service VARCHAR(50),
        Faculty VARCHAR(50),
        Fund VARCHAR(50),
        Project VARCHAR(50),
        Plan VARCHAR(50),
        Sub_Plan VARCHAR(50),
        KKU_Item_Code VARCHAR(50),
        KKU_Item_Name TEXT,
        GF_Item_Code VARCHAR(50),
        GF_Item_Name TEXT,
        Q1_Spending_Plan DECIMAL(15,2),
        Q2_Spending_Plan DECIMAL(15,2),
        Q3_Spending_Plan DECIMAL(15,2),
        Q4_Spending_Plan DECIMAL(15,2),
        Allocated_Quantity INT,
        Allocated_Unit_Price DECIMAL(15,2),
        Allocated_UOM VARCHAR(50),
        Reason TEXT,
        Objective TEXT,
        Utility TEXT,
        Place TEXT,
        Account VARCHAR(50),
        Scenario VARCHAR(50),
        Version VARCHAR(50),
        Allocated_Total_Amount_Quantity DECIMAL(15,2),
        Plan_Desc TEXT,
        SubPlan_Desc TEXT,
        Proj_Desc TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    '''
    cursor.execute(create_table_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO budget_planning_allocated_annual_budget_plan (
            Service, Faculty, Fund, Project, Plan, Sub_Plan, KKU_Item_Code, KKU_Item_Name,
            GF_Item_Code, GF_Item_Name, Q1_Spending_Plan, Q2_Spending_Plan, Q3_Spending_Plan,
            Q4_Spending_Plan, Allocated_Quantity, Allocated_Unit_Price, Allocated_UOM, Reason,
            Objective, Utility, Place, Account, Scenario, Version, Allocated_Total_Amount_Quantity,
            Plan_Desc, SubPlan_Desc, Proj_Desc
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Service'],
            row['Faculty'],
            row['Fund'],
            row['Project'],
            row['Plan'],
            row['Sub Plan'],
            row['KKU Item Code'],
            row['KKU Item Name'],
            row['GF Item Code'],
            row['GF Item Name'],
            row['Q1_Spending Plan'],
            row['Q2_Spending Plan'],
            row['Q3_Spending Plan'],
            row['Q4_Spending Plan'],
            row['Allocated Quantity'],
            row['Allocated Unit Price'],
            row['Allocated UOM'],
            row['Reason'],
            row['Objective'],
            row['Utility'],
            row['Place'],
            row['Account'],
            row['Scenario'],
            row['Version'],
            row['Allocated_Total_Amount_Quantity'],
            row['Plan_Desc'],
            row['SubPlan_Desc'],
            row['Proj_Desc']
        ))

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into budget_planning_allocated_annual_budget_plan table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
