import pymysql
import csv

# ข้อมูลการเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_username = 'root'
str_password = 'TDyutdYdyudRTYDsEFOPI'

# ฟังก์ชันดึงค่า account ตามลำดับเงื่อนไขที่กำหนด


def get_account(row):
    # Column F (5) -> E (4) -> D (3) -> C (2) -> B (1) -> A (0)
    for col in [5, 4, 3, 2, 1, 0]:
        if row[col]:
            return row[col]
    return None


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
        with open('C:/xampp/htdocs/kku-report/server/meta_data/BUDGET_ACCOUNT_NEW_2568_V10.csv', newline='', encoding='utf-8') as csvfile:
            csv_reader = csv.reader(csvfile)

            # ข้าม 5 แถวแรก
            for _ in range(5):
                next(csv_reader)

            for row in csv_reader:
                if len(row) < 7:
                    continue  # ข้ามแถวที่ข้อมูลไม่ครบ

                account = get_account(row)
                description = row[6]  # Column G

                if account:
                    sql = """
                    INSERT INTO budget_account (account, description)
                    VALUES (%s, %s)
                    """
                    cursor.execute(sql, (account, description))

        connection.commit()
        print("Data inserted successfully.")
finally:
    connection.close()
