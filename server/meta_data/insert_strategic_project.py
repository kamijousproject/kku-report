import os
import sys
import pandas as pd
import pymysql

# กำหนดค่าการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_username = 'root'
str_password = 'TDyutdYdyudRTYDsEFOPI'

# กำหนด path ของไฟล์ CSV
# current_dir = os.path.dirname(__file__)
# file_path = os.path.join(
#     current_dir, 'epm-service-admin@kku.ac.th_ExportedMetadata_Strategic Project.csv')

if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]

# ย้อนกลับไป 1 path
file_path = os.path.abspath(os.path.join(file_path, os.pardir))

# รวม path กับชื่อไฟล์เดิม เพื่อให้ได้ path ของไฟล์ CSV ที่ถูกต้อง
file_path = os.path.join(file_path, os.path.basename(sys.argv[1]))

# อ่านข้อมูลจาก CSV พร้อมตรวจสอบชื่อคอลัมน์
try:
    df = pd.read_csv(file_path, encoding='utf-8')
except UnicodeDecodeError:
    df = pd.read_csv(file_path, encoding='ISO-8859-1')

# ลบช่องว่างออกจากชื่อคอลัมน์
df.columns = df.columns.str.strip()

# ตรวจสอบชื่อคอลัมน์ทั้งหมด
print("Column names in CSV:", df.columns.tolist())

# ค้นหาคอลัมน์ที่มีคำว่า "Alias" ในชื่อ
for col in df.columns:
    if "Alias" in col:
        print("Potential Alias Column:", col)

# ระบุชื่อคอลัมน์ที่ถูกต้อง
alias_column = "Alias: Default"
if alias_column not in df.columns:
    # ค้นหาคอลัมน์ที่ใกล้เคียง
    alias_column = [col for col in df.columns if "Alias: Default" in col][0]

# เลือกเฉพาะคอลัมน์ที่ต้องการและทำความสะอาดข้อมูล
insert_data = df[['Strategic Project', alias_column]].copy()
insert_data.columns = ['ksp_id', 'ksp_name']
insert_data['ksp_name'] = insert_data['ksp_name'].fillna(
    "")

# แสดงตัวอย่างข้อมูลก่อน insert
print(insert_data.head())

# เชื่อมต่อฐานข้อมูล
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
        truncate_query = "TRUNCATE TABLE ksp;"
        cursor.execute(truncate_query)
        connection.commit()
        # สร้างคำสั่ง SQL สำหรับ INSERT
        sql = "INSERT INTO ksp (ksp_id, ksp_name) VALUES (%s, %s)"

        # แปลงข้อมูลเป็น list ของ tuple
        values = [tuple(row)
                  for row in insert_data.itertuples(index=False, name=None)]

        # ใช้ executemany เพื่อ insert หลายแถวพร้อมกัน
        cursor.executemany(sql, values)

        # Commit การเปลี่ยนแปลง
        connection.commit()
        # print("Insert data successfully!")
        print("SUCCESS")
finally:
    connection.close()
