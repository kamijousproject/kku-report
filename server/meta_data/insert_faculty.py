import os
import sys
import pandas as pd
import mysql.connector

# อ่าน CSV ไฟล์
# csv_file = "C:/xampp/htdocs/kku-report/server/meta_data/Faculty.csv"
# df = pd.read_csv(csv_file)

if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]

# ย้อนกลับไป 1 path
file_path = os.path.abspath(os.path.join(file_path, os.pardir))

# รวม path กับชื่อไฟล์เดิม เพื่อให้ได้ path ของไฟล์ CSV ที่ถูกต้อง
df = os.path.join(file_path, os.path.basename(sys.argv[1]))

# ลบช่องว่างออกจากชื่อคอลัมน์
df.columns = df.columns.str.strip()

# แก้ไขชื่อคอลัมน์ผิดพลาด
actual_col_names = df.columns.tolist()
print("Actual Columns in CSV:", actual_col_names)

# ถ้าคอลัมน์มีเว้นวรรคที่มองไม่เห็น ใช้ rename
df = df.rename(columns={"Alias: Default": "Alias_Default"})

# เลือกเฉพาะคอลัมน์ที่ต้องการ
df = df[['Faculty', 'Parent', 'Alias_Default']]

# แก้ไขค่า NaN เป็นค่าว่าง
df = df.fillna('')

# เชื่อมต่อ MySQL
conn = mysql.connector.connect(
    host="110.164.146.250",
    user="root",
    password="TDyutdYdyudRTYDsEFOPI",
    database="epm_report"
)
cursor = conn.cursor()

# สร้างตาราง Faculty
create_table_query = """
CREATE TABLE IF NOT EXISTS Faculty (
    Faculty VARCHAR(50),
    Parent VARCHAR(50),
    Alias_Default VARCHAR(255)
);
"""
cursor.execute(create_table_query)

# Insert ข้อมูลเข้าไปใน Table
insert_query = "INSERT INTO Faculty (Faculty, Parent, Alias_Default) VALUES (%s, %s, %s)"

for index, row in df.iterrows():
    cursor.execute(
        insert_query, (row['Faculty'], row['Parent'], row['Alias_Default']))

# Commit และปิดการเชื่อมต่อ
conn.commit()
cursor.close()
conn.close()

# print("✅ Insert Data สำเร็จ!")
print("SUCCESS")
