import mysql.connector
import csv
import sys
import os

if len(sys.argv) != 2:
    print("Usage: python3 insertdata.py <csv_file_path>")
    sys.exit(1)

csv_file_path = sys.argv[1]

if not os.path.exists(csv_file_path):
    print(f"Error: File {csv_file_path} does not exist.")
    sys.exit(1)

try:
    # Database connection settings
    host = 'localhost'
    db_name = 'epm_report'
    username = 'root'
    password = ''

    # Establishing connection to the database
    connection = mysql.connector.connect(
        host=host,
        user=username,
        password=password,
        database=db_name
    )

    cursor = connection.cursor()

    # Create table if not exists
    create_table_query = """
    CREATE TABLE IF NOT EXISTS epm_data (
        Account VARCHAR(255),
        Oct VARCHAR(255),
        Nov VARCHAR(255),
        `Dec` VARCHAR(255),
        Jan VARCHAR(255),
        Feb VARCHAR(255),
        Mar VARCHAR(255),
        Apr VARCHAR(255),
        May VARCHAR(255),
        Jun VARCHAR(255),
        Jul VARCHAR(255),
        Aug VARCHAR(255),
        Sep VARCHAR(255),
        Point_of_View TEXT,
        Data_Load_Cube_Name VARCHAR(255)
    );
    """
    cursor.execute(create_table_query)

    # Insert data into the table
    insert_query = """
    INSERT INTO epm_data (
        Account, Oct, Nov, `Dec`, Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Point_of_View, Data_Load_Cube_Name
    ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);
    """

    with open(csv_file_path, mode='r', encoding='utf-8') as file:
        csv_reader = csv.reader(file)
        header = next(csv_reader)

        for row in csv_reader:
            try:
                cursor.execute(insert_query, row)
            except Exception as e:
                print(f"Error inserting row {row}: {e}")

    # Commit changes and close the connection
    connection.commit()
    cursor.close()
    connection.close()

    print(
        f"Data from {csv_file_path} has been successfully imported into the database.")

except mysql.connector.Error as err:
    print(f"Database error: {err}")

except Exception as e:
    print(f"Unexpected error: {e}")
