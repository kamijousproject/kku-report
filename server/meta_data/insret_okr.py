import os
import sys
import pandas as pd
import pymysql

# กำหนดพาธไฟล์ CSV
# current_dir = os.path.dirname(__file__)
# file_path = os.path.join(current_dir, 'warissarac_ExportedMetadata_OKR.csv')

if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]

# ย้อนกลับไป 1 path
file_path = os.path.abspath(os.path.join(file_path, os.pardir))

# รวม path กับชื่อไฟล์เดิม เพื่อให้ได้ path ของไฟล์ CSV ที่ถูกต้อง
file_path = os.path.join(file_path, os.path.basename(sys.argv[1]))

# โหลดไฟล์ CSV โดยใช้ , เป็นตัวคั่น
df = pd.read_csv(file_path, encoding='utf-8', delimiter=',')

# ลบช่องว่างจากชื่อคอลัมน์ (ป้องกันปัญหา column name mismatch)
df.columns = df.columns.str.strip()

# ตรวจสอบชื่อคอลัมน์
print("Column names:", df.columns)

# เปลี่ยนชื่อคอลัมน์เพื่อความสะดวก
df = df.rename(columns={"OKR": "okr_id", "Alias: Default": "okr_name"})

# ตัดรหัสออกจากคอลัมน์ 'okr_name' (ถ้ามี ':')
df['okr_name'] = df['okr_name'].astype(str).str.split(':').str[-1].str.strip()

# ข้อมูลเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_username = 'root'
str_password = 'TDyutdYdyudRTYDsEFOPI'

# ฟังก์ชัน Insert ข้อมูลเข้า SQL


def insert_data_to_db(df):
    connection = pymysql.connect(
        host=str_hosting,
        user=str_username,
        password=str_password,
        database=str_database,
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )
    try:
        with connection.cursor() as cursor:
            for _, row in df.iterrows():
                sql = "INSERT INTO okr (okr_id, okr_name) VALUES (%s, %s)"
                cursor.execute(sql, (row['okr_id'], row['okr_name']))
        connection.commit()
    finally:
        connection.close()


# เรียกใช้ฟังก์ชัน Insert
insert_data_to_db(df)

print("Data inserted successfully!")
