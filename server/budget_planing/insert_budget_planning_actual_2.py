import os
import sys
import pandas as pd
import pymysql

# ตรวจสอบว่ามีพารามิเตอร์ไฟล์ CSV ที่ส่งมาหรือไม่
if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]

# ตรวจสอบว่าไฟล์ CSV มีอยู่จริงหรือไม่
if not os.path.exists(file_path):
    print(f"Error: File '{file_path}' not found.")
    sys.exit(1)

# กำหนดค่าการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_username = 'root'
str_password = 'TDyutdYdyudRTYDsEFOPI'

# อ่านไฟล์ CSV
df = pd.read_csv(file_path, skiprows=6, dtype={
                 'account_description': str})  # ข้าม header 6 แถวแรก

# เลือกเฉพาะข้อมูลที่ต้องการเก็บ
df_filtered = df.iloc[:, [0, 3, 13, 14, 16, 17, 18, 20]]

# เปลี่ยนชื่อคอลัมน์ให้ตรงกับ Database
df_filtered.columns = ['account', 'account_description', 'prior_periods_debit', 'prior_periods_credit',
                       'period_activity_debit', 'period_activity_credit', 'ending_balances_debit', 'ending_balances_credit']

# กรอกค่า NaN ด้วย 0
df_filtered = df_filtered.fillna(0)

# เชื่อมต่อฐานข้อมูล
conn = pymysql.connect(host=str_hosting, user=str_username,
                       password=str_password, database=str_database)
cursor = conn.cursor()

# ล้างข้อมูลในตารางก่อนทำการ INSERT
truncate_query = "TRUNCATE TABLE budget_planning_actual_2;"
cursor.execute(truncate_query)
conn.commit()

# คำสั่งสร้างตารางถ้ายังไม่มี
create_table_query = """
CREATE TABLE IF NOT EXISTS budget_planning_actual_2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account VARCHAR(255),
    account_description TEXT,
    prior_periods_debit VARCHAR(50),
    prior_periods_credit VARCHAR(50),
    period_activity_debit VARCHAR(50),
    period_activity_credit VARCHAR(50),
    ending_balances_debit VARCHAR(50),
    ending_balances_credit VARCHAR(50)
);
"""
cursor.execute(create_table_query)
conn.commit()

# เตรียมคำสั่ง SQL สำหรับ Insert ข้อมูล
insert_query = """
INSERT INTO budget_planning_actual_2 (account, account_description, prior_periods_debit, prior_periods_credit,
                                      period_activity_debit, period_activity_credit, ending_balances_debit, ending_balances_credit)
VALUES (%s, %s, %s, %s, %s, %s, %s, %s);
"""

# วนลูป insert ข้อมูลลงใน database
for index, row in df_filtered.iterrows():
    cursor.execute(insert_query, tuple(row))

# Commit ข้อมูลที่ Insert
conn.commit()

# ลบ Record ที่ไม่ต้องการ
delete_query = """
DELETE FROM budget_planning_actual_2
WHERE account IN ('0', '', 'Account Segment', 'Account', 'Total for Account Segment', 'End of Report');
"""
cursor.execute(delete_query)
conn.commit()

# ปิดการเชื่อมต่อ
cursor.close()
conn.close()

print("SUCCESS")
