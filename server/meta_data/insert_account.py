import os
import sys
import pandas as pd
import mysql.connector
import re

# กำหนดพาธของไฟล์ CSV
# current_dir = os.path.dirname(__file__)
# file_path = os.path.join(current_dir, 'Book1.csv')

if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]

# ย้อนกลับไป 1 path
file_path = os.path.abspath(os.path.join(file_path, os.pardir))

# รวม path กับชื่อไฟล์เดิม เพื่อให้ได้ path ของไฟล์ CSV ที่ถูกต้อง
file_path = os.path.join(file_path, os.path.basename(sys.argv[1]))

# ข้อมูลการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_username = 'root'
str_password = 'TDyutdYdyudRTYDsEFOPI'

# อ่านไฟล์ CSV
try:
    df = pd.read_csv(file_path, encoding='utf-8')
    df.columns = df.columns.str.strip()
except Exception as e:
    print(f"Error reading CSV file: {e}")
    exit()

# เลือกเฉพาะคอลัมน์ที่ต้องการ
columns = ['Account', 'Alias: Default']
df = df[columns]

# แก้ไขชื่อคอลัมน์ให้เหมาะสม
column_mapping = {'Account': 'account', 'Alias: Default': 'alias_default'}
df.rename(columns=column_mapping, inplace=True)

# แก้ไขค่า NaN
df = df.fillna('')

# เอาเฉพาะตัวหนังสือจากคอลัมน์ alias_default
df['alias_default'] = df['alias_default'].apply(
    lambda x: re.sub(r'^\d+-', '', x).strip())

# สร้างการเชื่อมต่อฐานข้อมูล
try:
    conn = mysql.connector.connect(
        host=str_hosting,
        user=str_username,
        password=str_password,
        database=str_database,
        charset='utf8mb4',
        autocommit=True
    )
    cursor = conn.cursor()
except Exception as e:
    print(f"Error connecting to database: {e}")
    exit()

# ตรวจสอบและสร้างตารางหากไม่มีอยู่
create_table_query = '''
CREATE TABLE IF NOT EXISTS account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account VARCHAR(50),
    alias_default TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
'''
try:
    cursor.execute(create_table_query)
    conn.commit()
    print("Table 'account' is ready.")
except Exception as e:
    print(f"Error creating table: {e}")
    cursor.close()
    conn.close()
    exit()

# แทรกข้อมูลลงฐานข้อมูล
try:
    for _, row in df.iterrows():
        insert_query = '''
        INSERT INTO account (account, alias_default)
        VALUES (%s, %s);
        '''
        cursor.execute(insert_query, (row['account'], row['alias_default']))
    conn.commit()
    print("Data inserted successfully into account table.")
except Exception as e:
    print(f"Error inserting data: {e}")
finally:
    cursor.close()
    conn.close()
