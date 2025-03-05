import pymysql
import csv

# กำหนดค่าการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_username = 'root'
str_password = 'TDyutdYdyudRTYDsEFOPI'

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
        # เปิดไฟล์ CSV และอ่านข้อมูล
        with open('C:/xampp/htdocs/kku-report/server/meta_data/Budget Account.csv', encoding='utf-8') as file:
            csv_reader = csv.reader(file)
            next(csv_reader)  # ข้าม header

            for row in csv_reader:
                account = row[0].strip()  # Column: Child
                parent = row[1].strip() if row[1] else None  # Column: Parent
                # Column: Alias: Default
                alias_default = row[3].strip() if row[3] else None

                # ตรวจสอบค่าก่อน INSERT
                if account and alias_default:
                    sql = """
                        INSERT INTO account (account, parent, alias_default) 
                        VALUES (%s, %s, %s)
                    """
                    cursor.execute(sql, (account, parent, alias_default))

        # ยืนยันการบันทึกข้อมูล
        connection.commit()

finally:
    connection.close()

print("Insert Data Success!")
