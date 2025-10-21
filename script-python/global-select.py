import mysql.connector
from mysql.connector import errorcode

# --- Database Configuration ---
DB_HOST = "localhost"
DB_USER = "root"
DB_PASSWORD = ""
DB_NAME = "dbavion3"

# --- List of all tables from db.sql (in French) ---
TABLE_NAMES = [
    "utilisateurs",
    "compagnies_aeriennes",
    "agences",
    "profils_admin",
    "avions",
    "vols",
    "tarifs",
    "sieges",
    "reservations",
    "passagers",
    "billets",
    "historique_statuts_reservations",
    "historique_statuts_admin",
    "demandes_vols",
    "demandes_agences",
    "demandes_compagnies",
    "messages_contact",
]

def print_table(cursor, table_name):
    """Executes a SELECT * query on a table and prints the results."""
    try:
        print(f"--- Data from table: `{table_name}` ---")
        # Limit to 20 rows for brevity
        cursor.execute(f"SELECT * FROM `{table_name}` LIMIT 20")
        
        rows = cursor.fetchall()
        
        if not rows:
            print("Table is empty or does not exist.\n")
            return

        # Get column names from cursor.description
        column_names = [i[0] for i in cursor.description]
        
        # --- Pretty-print table ---
        # Calculate column widths
        col_widths = [len(col) for col in column_names]
        for row in rows:
            for i, cell in enumerate(row):
                col_widths[i] = max(col_widths[i], len(str(cell)))

        # Print header
        header = " | ".join(f"{col:<{col_widths[i]}}" for i, col in enumerate(column_names))
        print(header)
        print("-" * len(header))

        # Print rows
        for row in rows:
            row_str = " | ".join(f"{str(cell):<{col_widths[i]}}" for i, cell in enumerate(row))
            print(row_str)
        
        print(f"(Showing up to 20 rows)\n")

    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_NO_SUCH_TABLE:
            print(f"Table `{table_name}` not found.\n")
        else:
            print(f"Error fetching data from `{table_name}`: {err}\n")

def main():
    """Connects to the database and displays data from all specified tables."""
    cnx = None
    cursor = None
    try:
        cnx = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD,
            database=DB_NAME
        )
        cursor = cnx.cursor()
        print(f"Successfully connected to database '{DB_NAME}'.\n")

        for table_name in TABLE_NAMES:
            print_table(cursor, table_name)

    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_BAD_DB_ERROR:
            print(f"Database '{DB_NAME}' does not exist.")
        else:
            print(f"Database connection error: {err}")
    finally:
        if cursor:
            cursor.close()
        if cnx and cnx.is_connected():
            cnx.close()
            print("MySQL connection is closed.")

if __name__ == "__main__":
    main()