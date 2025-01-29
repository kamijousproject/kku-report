import pymysql
import pandas as pd
import os

# Current directory and file path
current_dir = os.path.dirname(__file__)
file_path = os.path.join(current_dir, '68FA24_20250125.csv')

# Database connection information
str_hosting = '110.164.146.250'
str_database = 'epm_report'
str_username = 'root'
str_password = 'TDyutdYdyudRTYDsEFOPI'

# Connect to the database
connection = pymysql.connect(
    host=str_hosting,
    user=str_username,
    password=str_password,
    database=str_database
)

try:
    with connection.cursor() as cursor:
        # Create the table if it does not exist
        create_table_query = """
        CREATE TABLE IF NOT EXISTS planning_faculty_action_plan (
            Strategic_Object VARCHAR(255),
            Strategic_Project VARCHAR(255),
            Faculty VARCHAR(255),
            OKR VARCHAR(255),
            Target_OKR_Objective_and_Key_Result VARCHAR(255),
            UOM VARCHAR(255),
            Start_Date DATE,
            End_Date DATE,
            Budget_Amount DECIMAL(15, 2),
            Tiers_Deploy VARCHAR(255),
            KKU_Strategic_Plan_LOV VARCHAR(255),
            Dev_Plan_Proposed_to_Nomination_Co_LOV VARCHAR(255),
            Division_Noteworthy_Plan_LOV VARCHAR(255),
            Responsible_person VARCHAR(255),
            Scenario VARCHAR(255),
            Version VARCHAR(255)
        );
        """
        cursor.execute(create_table_query)

        # Read the CSV file using pandas
        df = pd.read_csv(file_path, dtype={'Faculty': str})

        # Replace NaN values with defaults
        df.fillna({
            'Strategic Object': '',
            'Strategic Project': '',
            'Faculty': '',
            'OKR': '',
            'Target (OKR : Objective and Key Result)': '',
            'UOM': '',
            'Start Date': '',  # Default date
            'End Date': '',  # Default date
            'Budget Amount': 0.0,
            'Tiers & Deploy': '',
            'KKU_Strategic_Plan_LOV': '',
            'Dev Plan Proposed to Nomination Co._LOV': '',
            'Division Noteworthy Plan LOV': '',
            'Responsible person': '',
            'Scenario': '',
            'Version': ''
        }, inplace=True)

        # Convert date columns from Thai Buddhist year to standard year
        def convert_date(thai_date):
            day, month, year = thai_date.split()
            year = int(year) - 543  # Convert year from Buddhist to Gregorian
            month = month.zfill(2)  # Ensure month is two digits
            day = day.zfill(2)      # Ensure day is two digits
            return f"{year}-{month}-{day}"

        df['Start Date'] = df['Start Date'].apply(convert_date)
        df['End Date'] = df['End Date'].apply(convert_date)

        # Prepare the insert query
        insert_query = """
        INSERT INTO planning_faculty_action_plan (
            Strategic_Object, Strategic_Project, Faculty, OKR, Target_OKR_Objective_and_Key_Result, UOM, 
            Start_Date, End_Date, Budget_Amount, Tiers_Deploy, KKU_Strategic_Plan_LOV, 
            Dev_Plan_Proposed_to_Nomination_Co_LOV, Division_Noteworthy_Plan_LOV, Responsible_person, Scenario, Version
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """

        # Insert rows into the database
        for _, row in df.iterrows():
            cursor.execute(insert_query, (
                row['Strategic Object'],
                row['Strategic Project'],
                row['Faculty'],
                row['OKR'],
                row['Target (OKR : Objective and Key Result)'],
                row['UOM'],
                row['Start Date'],
                row['End Date'],
                float(row['Budget Amount']),
                row['Tiers & Deploy'],
                row['KKU_Strategic_Plan_LOV'],
                row['Dev Plan Proposed to Nomination Co._LOV'],
                row['Division Noteworthy Plan LOV'],
                row['Responsible person'],
                row['Scenario'],
                row['Version']
            ))

        # Commit the transaction
        connection.commit()

finally:
    connection.close()

print("Data inserted successfully!")
