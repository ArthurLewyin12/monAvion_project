import mysql.connector
from mysql.connector import errorcode
import datetime

# --- Database Configuration ---
DB_HOST = "localhost"
DB_USER = "root"
DB_PASSWORD = ""
DB_NAME = "dbavion2"

# --- Error Log File ---
ERROR_LOG_FILE = "database_errors.md"

def log_error(error_message):
    """Appends an error message to the markdown log file."""
    with open(ERROR_LOG_FILE, "a") as f:
        timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        f.write(f"## Error at {timestamp}\n")
        f.write("```\n")
        f.write(f"{error_message}\n")
        f.write("```\n\n")

# --- SQL Statements for creating tables ---
TABLES = {}

TABLES['users'] = (
    """CREATE TABLE `users` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `firstname` VARCHAR(100) NOT NULL,
    `lastname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `contact` VARCHAR(20),
    `password` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(500),
    `current_status` ENUM('ACTIVE', 'INACTIVE', 'SUSPENDED') DEFAULT 'ACTIVE',
    `user_type` ENUM('AIRLINE', 'AGENCY', 'ADMIN', 'CLIENT') DEFAULT 'AGENCY',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    `created_by` BIGINT NULL,
    `updated_by` BIGINT NULL,
    `deleted_by` BIGINT NULL,
    INDEX `idx_email` (`email`),
    INDEX `idx_contact` (`contact`),
    INDEX `idx_user_type` (`user_type`),
    INDEX `idx_status` (`current_status`),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (deleted_by) REFERENCES users(id)
    ) ENGINE=InnoDB""")

TABLES['airlines'] = (
    """CREATE TABLE `airlines` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNIQUE NOT NULL,
    `company_name` VARCHAR(200) NOT NULL,
    `iata_code` VARCHAR(3) UNIQUE,
    `description` TEXT,
    `country` VARCHAR(100) DEFAULT 'France',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    `created_by` BIGINT NULL,
    `updated_by` BIGINT NULL,
    `deleted_by` BIGINT NULL,
    INDEX `idx_user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`deleted_by`) REFERENCES `users`(`id`)
    ) ENGINE=InnoDB""")

TABLES['agencies'] = (
    """CREATE TABLE `agencies` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNIQUE NOT NULL,
    `agency_name` VARCHAR(200) NOT NULL,
    `license_number` VARCHAR(50) UNIQUE,
    `address` TEXT,
    `phone` VARCHAR(20),
    `warnings_count` INT DEFAULT 0,
    `current_status` ENUM('ACTIVE', 'INACTIVE', 'SUSPENDED') DEFAULT 'ACTIVE',
    `registration_date` DATE DEFAULT (CURDATE()),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    `created_by` BIGINT NULL,
    `updated_by` BIGINT NULL,
    `deleted_by` BIGINT NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_status` (`current_status`),
    INDEX `idx_warnings` (`warnings_count`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`deleted_by`) REFERENCES `users`(`id`)
    ) ENGINE=InnoDB""")

TABLES['admin_profiles'] = (
    """CREATE TABLE `admin_profiles` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNIQUE NOT NULL,
    `full_name` VARCHAR(200) NOT NULL,
    `department` VARCHAR(100),
    `access_level` ENUM('JUNIOR', 'SENIOR', 'SUPER') DEFAULT 'JUNIOR',
    `last_audit_date` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    `created_by` BIGINT NULL,
    `updated_by` BIGINT NULL,
    `deleted_by` BIGINT NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_access_level` (`access_level`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`deleted_by`) REFERENCES `users`(`id`)
    ) ENGINE=InnoDB""")

TABLES['aircrafts'] = (
    """CREATE TABLE `aircrafts` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `model` VARCHAR(100) NOT NULL,
    `airline_id` BIGINT NOT NULL,
    `total_seats` INT NOT NULL,
    `seats_per_class` JSON,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    `created_by` BIGINT NULL,
    `updated_by` BIGINT NULL,
    `deleted_by` BIGINT NULL,
    INDEX `idx_airline_id` (`airline_id`),
    FOREIGN KEY (`airline_id`) REFERENCES `airlines`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`deleted_by`) REFERENCES `users`(`id`)
    ) ENGINE=InnoDB""")

TABLES['flights'] = (
    """CREATE TABLE `flights` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `flight_number` VARCHAR(20) NOT NULL,
    `departure_airport` VARCHAR(3) NOT NULL,
    `arrival_airport` VARCHAR(3) NOT NULL,
    `departure_date` DATETIME NOT NULL,
    `arrival_date` DATETIME NOT NULL,
    `airline_id` BIGINT NOT NULL,
    `aircraft_id` BIGINT NOT NULL,
    `status` ENUM('SCHEDULED', 'DELAYED', 'CANCELLED') DEFAULT 'SCHEDULED',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    `created_by` BIGINT NULL,
    `updated_by` BIGINT NULL,
    `deleted_by` BIGINT NULL,
    INDEX `idx_flight_number` (`flight_number`),
    INDEX `idx_departure` (`departure_airport`, `departure_date`),
    INDEX `idx_arrival` (`arrival_airport`, `arrival_date`),
    INDEX `idx_airline_id` (`airline_id`),
    INDEX `idx_aircraft_id` (`aircraft_id`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`airline_id`) REFERENCES `airlines`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`aircraft_id`) REFERENCES `aircrafts`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`),
    FOREIGN KEY (`deleted_by`) REFERENCES `users`(`id`)
    ) ENGINE=InnoDB""")


def main():
    """Connects to the database, creates it if not exists, and creates all tables and triggers."""
    cnx = None
    cursor = None
    try:
        # Connect to MySQL server
        cnx = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD
        )
        cursor = cnx.cursor()
        print("Successfully connected to MySQL server.")

        # Create and select the database
        try:
            cursor.execute(f"CREATE DATABASE IF NOT EXISTS `{DB_NAME}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")
            print(f"Database '{DB_NAME}' created or already exists.")
            cursor.execute(f"USE `{DB_NAME}`")
            print(f"Database changed to '{DB_NAME}'.")
        except mysql.connector.Error as err:
            error_msg = f"Failed to create or select database: {err}"
            print(error_msg)
            log_error(error_msg)
            raise

        # Create tables
        for table_name in TABLES:
            table_description = TABLES[table_name]
            try:
                print(f"Creating table `{table_name}`: ", end='')
                cursor.execute(table_description)
                print("OK")
            except mysql.connector.Error as err:
                if err.errno == errorcode.ER_TABLE_EXISTS_ERROR:
                    print("already exists.")
                else:
                    error_msg = f"Error creating table {table_name}: {err.msg}"
                    print(error_msg)
                    log_error(error_msg)
                    raise
        
        # Create Trigger
        try:
            print("Creating trigger `update_users_updated_at`: ", end='')
            trigger_core_command = "CREATE TRIGGER `update_users_updated_at` BEFORE UPDATE ON `users` FOR EACH ROW SET NEW.updated_at = CURRENT_TIMESTAMP"
            cursor.execute(trigger_core_command)
            print("OK")
        except mysql.connector.Error as err:
            if err.errno == 1359: # ER_TRG_ALREADY_EXISTS
                 print("already exists.")
            else:
                error_msg = f"Error creating trigger: {err.msg}"
                print(error_msg)
                log_error(error_msg)

        print("\nDatabase schema setup completed successfully.")

    except mysql.connector.Error as err:
        error_msg = f"\nDatabase error: {err}"
        print(error_msg)
        log_error(error_msg)
    finally:
        if cursor:
            cursor.close()
        if cnx and cnx.is_connected():
            cnx.close()
            print("MySQL connection is closed.")

if __name__ == "__main__":
    main()
