import os
import sys
import pandas as pd
import pymysql
import re

# กำหนดพาธของไฟล์ CSV
# file_path = "c:\\Users\\suttipongk\\Downloads\\ExportedMetadataFile\\epm-service-admin@kku.ac.th_ExportedMetadata_Pillar.csv"

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

# อ่านข้อมูลจากไฟล์ CSV
df = pd.read_csv(file_path, encoding="utf-8-sig", delimiter=",")

# ล้างชื่อคอลัมน์จากช่องว่างที่อาจติดมา
df.columns = df.columns.str.strip()

# ตรวจสอบชื่อคอลัมน์
print("Columns in CSV:", df.columns.tolist())

# ตรวจสอบว่าคอลัมน์ที่ต้องการมีอยู่จริง
if "Pillar" in df.columns and "Alias: Default" in df.columns:
    df_filtered = df[["Pillar", "Alias: Default"]].copy()

    # ฟังก์ชันตัดรหัสออกจาก Alias: Default
    def clean_alias(alias):
        return re.sub(r'^.*?:', '', str(alias)).strip()

    df_filtered["Alias: Default"] = df_filtered["Alias: Default"].apply(
        clean_alias)

    # เชื่อมต่อฐานข้อมูล
    connection = pymysql.connect(host=str_hosting,
                                 user=str_username,
                                 password=str_password,
                                 database=str_database,
                                 charset='utf8mb4')

    try:
        with connection.cursor() as cursor:
            truncate_query = "TRUNCATE TABLE pilar;"
            cursor.execute(truncate_query)
            connection.commit()
            
            for _, row in df_filtered.iterrows():
                sql = """
                INSERT INTO pilar (pilar_id, pilar_name) 
                VALUES (%s, %s)
                """
                cursor.execute(sql, (row["Pillar"], row["Alias: Default"]))

        connection.commit()
        # print("Data inserted successfully!")
        print("SUCCESS")

    except Exception as e:
        print("Error:", e)

    finally:
        connection.close()

else:
    print("Error: Required columns not found in CSV file.")
