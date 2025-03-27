import pandas as pd
import os
import sys

# ตรวจสอบว่ามีการส่งพาธไฟล์มาเป็นอาร์กิวเมนต์หรือไม่
if len(sys.argv) > 1:
    xlsx_file = sys.argv[1]  # ใช้พาธที่ได้รับจาก .cmd script
else:
    # ใช้ไฟล์ที่อยู่ในโฟลเดอร์เดียวกันกับสคริปต์
    script_dir = os.path.dirname(os.path.abspath(__file__))
    xlsx_file = os.path.join(
        script_dir, "Bursting_EPM_Report_workforce_new_positions_allocation.xlsx")

# สร้างชื่อไฟล์ CSV โดยเปลี่ยนนามสกุลไฟล์จาก .xlsx เป็น .csv
csv_file = os.path.splitext(xlsx_file)[0] + ".csv"

# ตรวจสอบว่าไฟล์ XLSX มีอยู่จริงหรือไม่
if not os.path.exists(xlsx_file):
    print(f"ไม่พบไฟล์: {xlsx_file}")
    sys.exit(1)

try:
    # อ่านไฟล์ Excel โดยใช้ openpyxl
    df = pd.read_excel(xlsx_file, sheet_name=0, engine='openpyxl')

    # แปลงเป็น CSV พร้อมกำหนด encoding เป็น UTF-8-SIG
    df.to_csv(csv_file, index=False, encoding='utf-8-sig')

    print(
        f"ไฟล์ {xlsx_file} ถูกแปลงเป็น {csv_file} สำเร็จ พร้อมรองรับภาษาไทย!")

except Exception as e:
    print(f"เกิดข้อผิดพลาดในการแปลงไฟล์: {str(e)}")
    sys.exit(1)
