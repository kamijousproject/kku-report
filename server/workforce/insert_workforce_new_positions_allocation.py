import os
import sys
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
file_path = os.path.join(current_dir, 'test insert.csv')

if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]
file_path = os.path.abspath(os.path.join(file_path, os.pardir))
file_path = os.path.join(file_path, os.path.basename(sys.argv[1]))

try:
    data = pd.read_csv(file_path, dtype={'Faculty': str})

    # เติมค่า NaN ให้เป็นค่าดีฟอลต์
    data = data.fillna({
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
        'Approved Personnel Type': '',
        'New Position Number': '',
        'Analysis': '',
        'WF_Plan': '',
        'WF_SubPlan': '',
        'WF_Project': '',
        'Location Code': '',
        'Entry Grade Code': '',
        'Management Level': '',
        'Parent Position Code': ''
    })
except Exception as e:
    print(f"Error reading CSV file: {e}")
    exit()

# สร้างการเชื่อมต่อฐานข้อมูล
try:
    connection = pymysql.connect(
        host=str_hosting,
        user=str_username,
        password=str_password,
        database=str_database,
        charset='utf8mb4'
    )
    cursor = connection.cursor()

    # สร้างตารางถ้ายังไม่มี
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS workforce_new_positions_allocation (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Account VARCHAR(255),
        Scenario VARCHAR(255),
        Version VARCHAR(255),
        Faculty VARCHAR(255),
        NHR VARCHAR(255),
        Personnel_Type VARCHAR(255),
        All_PositionTypes VARCHAR(255),
        Position VARCHAR(255),
        Job_Code VARCHAR(255),
        Name_Surname_If_change VARCHAR(255),
        Current_HC_of_the_Position INT,
        Retiring_in_Current_Year INT,
        Retiring_in_Next_Year INT,
        WF VARCHAR(255),
        Allocation_Consideration VARCHAR(255),
        Salary_rate DECIMAL(15,2),
        Fund_FT VARCHAR(255),
        Govt_Fund VARCHAR(255),
        Division_Revenue DECIMAL(15,2),
        OOP_Central_Revenue DECIMAL(15,2),
        Allocation_Conditions VARCHAR(255),
        Approved_Personnel_Type VARCHAR(255),
        New_Position_Number VARCHAR(255),
        Analysis VARCHAR(255),
        WF_Plan VARCHAR(255),
        WF_SubPlan VARCHAR(255),
        WF_Project VARCHAR(255),
        Location_Code VARCHAR(255),
        Entry_Grade_Code VARCHAR(255),
        Management_Level VARCHAR(255),
        Parent_Position_Code VARCHAR(255)
    );
    '''
    cursor.execute(create_table_query)
    connection.commit()

    truncate_query = "TRUNCATE TABLE workforce_new_positions_allocation;"
    cursor.execute(truncate_query)
    connection.commit()

    for _, row in data.iterrows():
        insert_query = '''
        INSERT INTO workforce_new_positions_allocation (
            Account, Scenario, Version, Faculty, NHR, Personnel_Type, All_PositionTypes, Position,
            Job_Code, Name_Surname_If_change, Current_HC_of_the_Position, Retiring_in_Current_Year,
            Retiring_in_Next_Year, WF, Allocation_Consideration, Salary_rate, Fund_FT,
            Govt_Fund, Division_Revenue, OOP_Central_Revenue, Allocation_Conditions,
            Approved_Personnel_Type, New_Position_Number, Analysis,
            WF_Plan, WF_SubPlan, WF_Project, Location_Code, Entry_Grade_Code,
            Management_Level, Parent_Position_Code
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
                  %s, %s, %s, %s, %s, %s, %s, %s);
        '''
        cursor.execute(insert_query, (
            row['Account'], row['Scenario'], row['Version'], row['Faculty'], row['NHR'],
            row['Personnel_Type'], row['All_PositionTypes'], row['Position'], row['Job Code'],
            row['Name-Surname (If change)'], row['Current HC of the Position'],
            row['Retiring in Current Year'], row['Retiring in Next Year'], row['WF'],
            row['Allocation Consideration'], row['Salary rate'], row['Fund(FT)'],
            row['Govt. Fund'], row['Division Revenue'], row['OOP Central Revenue'],
            row['Allocation Conditions'], row['Approved Personnel Type'],
            row['New Position Number'], row['Analysis'], row['WF_Plan'], row['WF_SubPlan'],
            row['WF_Project'], row['Location Code'], row['Entry Grade Code'],
            row['Management Level'], row['Parent Position Code']
        ))

    connection.commit()
    print("SUCCESS")
except Exception as e:
    print(f"Error: {e}")
finally:
    if connection:
        cursor.close()
        connection.close()
