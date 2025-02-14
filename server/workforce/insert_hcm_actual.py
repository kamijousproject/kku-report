import os
import sys
import pandas as pd
import pymysql
import numpy as np

# กำหนดค่าเชื่อมต่อฐานข้อมูล
str_hosting = '110.164.146.250'  # แก้ไขตามจริง
str_database = 'epm_report'      # แก้ไขตามจริง
str_username = 'root'           # แก้ไขตามจริง
str_password = 'TDyutdYdyudRTYDsEFOPI'  # แก้ไขตามจริง

# หาตำแหน่งไฟล์ CSV
# current_dir = os.path.dirname(__file__)
# file_path = os.path.join(current_dir, 'HCM_Actual_Data.csv')

if len(sys.argv) < 2:
    print("Error: No CSV file provided.")
    sys.exit(1)

file_path = sys.argv[1]

# ย้อนกลับไป 1 path
file_path = os.path.abspath(os.path.join(file_path, os.pardir))

# รวม path กับชื่อไฟล์เดิม เพื่อให้ได้ path ของไฟล์ CSV ที่ถูกต้อง
file_path = os.path.join(file_path, os.path.basename(sys.argv[1]))

# อ่านไฟล์ CSV โดยกำหนด dtype เป็น str
try:
    df = pd.read_csv(file_path, encoding='utf-8', dtype=str)
    df = df.where(pd.notna(df), None)  # แปลง NaN เป็น None สำหรับ MySQL
except Exception as e:
    print(f"Error reading CSV file: {e}")
    exit()

# เชื่อมต่อฐานข้อมูล MySQL
try:
    connection = pymysql.connect(
        host=str_hosting,
        user=str_username,
        password=str_password,
        database=str_database,
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )
    cursor = connection.cursor()

    truncate_query = "TRUNCATE TABLE workforce_hcm_actual;"
    cursor.execute(truncate_query)
    connection.commit()

    # สร้างตาราง workforce_Hhcm_actual หากยังไม่มีอยู่
    create_table_query = """
    CREATE TABLE IF NOT EXISTS workforce_hcm_actual (
        SERIAL VARCHAR(50) PRIMARY KEY,
        PERSONNEL_TYPE VARCHAR(100),
        POSITION_NUMBER VARCHAR(50),
        POSITION VARCHAR(150),
        ALL_POSITIONTYPES VARCHAR(100),
        FACULTY TEXT,
        WORKERS_NAME_SURNAME VARCHAR(150),
        EMPLOYEE_ID VARCHAR(50),
        NATIONAL_ID VARCHAR(50),
        EMPLOYMENT_TYPE VARCHAR(100),
        POSITION_LEVEL VARCHAR(100),
        JOB_FAMILY VARCHAR(100),
        PERSONNEL_GROUP VARCHAR(100),
        MANAGEMENT_POSITION_NAME VARCHAR(100),
        CONTRACT_TYPE VARCHAR(100),
        CONTRACT_PERIOD_SHORT_TERM VARCHAR(100),
        POSITION_QUALIFIFCATIONS TEXT,
        EMPLOYEE_QUALIFICATIONS TEXT,
        RATE_STATUS VARCHAR(50),
        SALARY_RATE VARCHAR(50),
        FUND_FT VARCHAR(100),
        GOVT_FUND VARCHAR(50),
        DIVISION_REVENUE VARCHAR(50),
        OOP_CENTRAL_REVENUE VARCHAR(50),
        ACADEMIC_POSITION_ALLOWANCE VARCHAR(50),
        POSITION_COMPENSATION_MNGT_ACADEMIC VARCHAR(50),
        LEVEL_8_COMPENSATION VARCHAR(50),
        FULL_SALARY_COMPENSATION VARCHAR(50),
        EXECUTIVE_ALLOWANCE VARCHAR(50),
        EXECUTIVE_COMPENSATION VARCHAR(50),
        DOWN_PAYMENT_ACCORDING_TO_CABINET_RESOLUTION VARCHAR(50),
        PTK_COMPENSATION VARCHAR(50),
        PTS_COMPENSATION VARCHAR(50),
        OTHER_SPECIAL_COMPENSATION VARCHAR(50),
        PROVIDENT_FUND VARCHAR(50),
        SOCIAL_SECURITY_FUND VARCHAR(50),
        WORKERS_COMPENSATION_FUND VARCHAR(50),
        SEVERANCE_FUND VARCHAR(50),
        GOVERNMENT_PENSION_FUND_GPF VARCHAR(50),
        GOVERNMENT_SERVICE_INSURANCE_FUND_GSIF VARCHAR(50),
        HOUSING_ALLOWANCE VARCHAR(50),
        POSITION_CAR_ALLOWANCE VARCHAR(50),
        OTHER_BENEFITS TEXT,
        LEVEL_3 VARCHAR(100),
        LEVEL_4 VARCHAR(100),
        LEVEL_5 VARCHAR(100),
        LEVEL_6 VARCHAR(100),
        WORKING_STATUS VARCHAR(100),
        VACANT_FROM_WHICH_DATE VARCHAR(50),
        V_FOR_6_MONTHS_ON VARCHAR(50),
        REASON_FOR_VACANCY TEXT,
        APPOINTMENT_DATE VARCHAR(50),
        RETIREMENT_DATE VARCHAR(50),
        PERFOMANCE_EVALUAION VARCHAR(100),
        PERFORMANCE_EVALUATION_IN_PERCENTAGE VARCHAR(50),
        HIRING_START_END_DATE TEXT,
        POSITION_STATUS VARCHAR(100),
        POSITION_APPOINTMENT_DATE VARCHAR(50),
        END_OF_TERM_DATE VARCHAR(50),
        APPOINTMENT_ORDER_DOCUMENT TEXT,
        POSITION_NAME_ACADEMIC_SUPPORT_MAPPED_TO_POSITION_NAME TEXT,
        DATE_OF_APPOINTMENT_POSITION VARCHAR(50),
        LETTER_OF_APPOINTMENT_ORDER TEXT,
        POSITION_NAME_PENDING_PROMOTION_HIGHER_LEVEL TEXT,
        DATE_OF_PROMOTION_REQUEST_HIGHER_LEVEL VARCHAR(50),
        JOB_CODE VARCHAR(50),
        LOCATION_CODE VARCHAR(100),
        ENTRY_GRADE_CODE VARCHAR(100),
        MANAGEMENT_LEVEL VARCHAR(100),
        PARENT_POSITION_CODE VARCHAR(100),
        PROFILE_CODE VARCHAR(100),
        PROFILE_NAME VARCHAR(150),
        WF_PLAN VARCHAR(100),
        WF_SUBPLAN VARCHAR(100),
        WF_PROJECT VARCHAR(100)
    );
    """
    cursor.execute(create_table_query)
    connection.commit()

    # Insert ข้อมูลเข้าไปในตาราง
    for _, row in df.iterrows():
        insert_query = """
        INSERT INTO workforce_hcm_actual VALUES ({})
        """.format(", ".join(["%s"] * len(row)))
        cursor.execute(insert_query, tuple(row.where(pd.notna(row), None)))

    connection.commit()
    print("SUCCESS")

except Exception as e:
    print(f"Error: {e}")

finally:
    cursor.close()
    connection.close()
