import pandas as pd
import os
import sys

if len(sys.argv) > 1:
    xlsx_file = sys.argv[1]
else:
    script_dir = os.path.dirname(os.path.abspath(__file__))
    xlsx_file = os.path.join(script_dir, "Bursting_EPM_Report_planning_kku_strategic_plan.xlsx")

csv_file = os.path.splitext(xlsx_file)[0] + ".csv"

if not os.path.exists(xlsx_file):
    print(f"ไม่พบไฟล์: {xlsx_file}")
    sys.exit(1)

try:
    df = pd.read_excel(xlsx_file, sheet_name=0, engine='openpyxl')
    df.to_csv(csv_file, index=False)
except Exception as e:
    print(f"เกิดข้อผิดพลาดในการแปลงไฟล์: {str(e)}")
    sys.exit(1)
