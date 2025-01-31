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
file_path = os.path.join(current_dir, 'Plan.csv')

# อ่านไฟล์ CSV
try:
    data = pd.read_csv(file_path, dtype={'Plan': str}, thousands=',')

    # ล้างช่องว่างในชื่อคอลัมน์
    data.columns = data.columns.str.strip()

    # ตรวจสอบชื่อคอลัมน์
    print("CSV Columns:", data.columns.tolist())

    # เลือกเฉพาะคอลัมน์ที่ต้องการ
    data = data[['Plan', 'Alias: Default']]

    # แก้ไขค่า NaN
    data = data.fillna('')

    # ตรวจสอบข้อมูลหลังจากการแปลง
    print("Sample data:")
    print(data.head())

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
        charset='utf8mb4',
        autocommit=True
    )
    cursor = connection.cursor()

    # สร้างตารางหากยังไม่มี
    create_table_query = '''
    CREATE TABLE IF NOT EXISTS plan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Plan VARCHAR(50),
        Alias_Default TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    '''
    cursor.execute(create_table_query)
    connection.commit()

    # เตรียมข้อมูลสำหรับการ INSERT
    for _, row in data.iterrows():
        try:
            insert_query = '''
            INSERT INTO plan (Plan, Alias_Default)
            VALUES (%s, %s);
            '''
            cursor.execute(insert_query, (
                row['Plan'], row['Alias: Default']
            ))
        except Exception as e:
            print(f"Error inserting row: {e}")

    # บันทึกข้อมูล
    connection.commit()
    print("Data inserted successfully into plan table.")

except Exception as e:
    print(f"Error: {e}")

finally:
    if connection:
        cursor.close()
        connection.close()
