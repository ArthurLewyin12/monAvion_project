import mysql.connector
from mysql.connector import errorcode
import datetime
import random
import json

# --- Database Configuration ---
DB_HOST = "localhost"
DB_USER = "root"
DB_PASSWORD = ""
DB_NAME = "dbavion2"

# --- Error Log File ---
ERROR_LOG_FILE = "population_errors.md"

def log_error(error_message):
    """Appends an error message to the markdown log file."""
    with open(ERROR_LOG_FILE, "a") as f:
        timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        f.write(f"## Error at {timestamp}\n")
        f.write("```\n")
        f.write(f"{error_message}\n")
        f.write("```\n\n")

def main():
    """Connects to the database and populates it with sample data."""
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
        print(f"Successfully connected to database '{DB_NAME}'.")

        # --- Data Population ---
        print("Truncating tables to ensure a clean slate...")
        cursor.execute("SET FOREIGN_KEY_CHECKS = 0;")
        tables_to_truncate = [
            'flight_proposals', 'flight_requests', 'admin_status_histories', 
            'booking_status_histories', 'tickets', 'passengers', 'bookings', 
            'seats', 'fares', 'flights', 'aircrafts', 'admin_profiles', 
            'agencies', 'airlines', 'users'
        ]
        for table in tables_to_truncate:
            try:
                cursor.execute(f"TRUNCATE TABLE {table}")
            except mysql.connector.Error as err:
                if err.errno == errorcode.ER_NO_SUCH_TABLE:
                    print(f"Table {table} does not exist, skipping truncation.")
                else:
                    raise
        cursor.execute("SET FOREIGN_KEY_CHECKS = 1;")
        print("OK - Tables truncated.")

        print("Populating users...")
        users_data = []
        for i in range(5):
            users_data.append(('John', f'Doe{i}', f'client{i}@example.com', '123456789', 'hashed_password', 'CLIENT'))
            users_data.append(('Air', f'Line{i}', f'airline{i}@example.com', '987654321', 'hashed_password', 'AIRLINE'))
            users_data.append(('Travel', f'Agency{i}', f'agency{i}@example.com', '555555555', 'hashed_password', 'AGENCY'))
            users_data.append(('Super', f'Admin{i}', f'admin{i}@example.com', '111222333', 'hashed_password', 'ADMIN'))

        add_user = "INSERT INTO users (firstname, lastname, email, contact, password, user_type) VALUES (%s, %s, %s, %s, %s, %s)"
        cursor.executemany(add_user, users_data)
        cnx.commit() 
        
        cursor.execute("SELECT id, user_type FROM users")
        all_users = cursor.fetchall()
        user_ids = {
            'CLIENT': [u[0] for u in all_users if u[1] == 'CLIENT'],
            'AIRLINE': [u[0] for u in all_users if u[1] == 'AIRLINE'],
            'AGENCY': [u[0] for u in all_users if u[1] == 'AGENCY'],
            'ADMIN': [u[0] for u in all_users if u[1] == 'ADMIN']
        }
        print(f"OK - {len(all_users)} users inserted.")

        print("Populating airlines...")
        airlines_data = [(uid, f'Airline-{i}', f'A{i}', 'A great airline') for i, uid in enumerate(user_ids['AIRLINE'])]
        add_airline = "INSERT INTO airlines (user_id, company_name, iata_code, description) VALUES (%s, %s, %s, %s)"
        cursor.executemany(add_airline, airlines_data)
        cnx.commit()
        cursor.execute("SELECT id FROM airlines")
        airline_ids = [item[0] for item in cursor.fetchall()]
        print(f"OK - {len(airline_ids)} airlines inserted.")

        print("Populating agencies...")
        agencies_data = [(uid, f'Agency-{i}', f'LIC-{i}', '123 Travel Lane') for i, uid in enumerate(user_ids['AGENCY'])]
        add_agency = "INSERT INTO agencies (user_id, agency_name, license_number, address) VALUES (%s, %s, %s, %s)"
        cursor.executemany(add_agency, agencies_data)
        cnx.commit()
        cursor.execute("SELECT id FROM agencies")
        agency_ids = [item[0] for item in cursor.fetchall()]
        print(f"OK - {len(agency_ids)} agencies inserted.")

        print("Populating admin_profiles...")
        admin_profiles_data = [(uid, f'Admin Profile {i}', 'IT', 'JUNIOR') for i, uid in enumerate(user_ids['ADMIN'])]
        add_admin_profile = "INSERT INTO admin_profiles (user_id, full_name, department, access_level) VALUES (%s, %s, %s, %s)"
        cursor.executemany(add_admin_profile, admin_profiles_data)
        cnx.commit()
        cursor.execute("SELECT id FROM admin_profiles")
        admin_profile_ids = [item[0] for item in cursor.fetchall()]
        print(f"OK - {len(admin_profile_ids)} admin_profiles inserted.")

        print("Populating aircrafts...")
        aircraft_ids = []
        add_aircraft = "INSERT INTO aircrafts (model, airline_id, total_seats, seats_per_class) VALUES (%s, %s, %s, %s)"
        for i in range(10):
            data = (f'Boeing 7{random.randint(3,8)}7', random.choice(airline_ids), 180 + i, json.dumps({'economy': 150, 'business': 30}))
            cursor.execute(add_aircraft, data)
            aircraft_ids.append(cursor.lastrowid)
        cnx.commit()
        print(f"OK - {len(aircraft_ids)} aircrafts inserted.")

        print("Populating flights...")
        flight_ids = []
        add_flight = "INSERT INTO flights (flight_number, departure_airport, arrival_airport, departure_date, arrival_date, airline_id, aircraft_id) VALUES (%s, %s, %s, %s, %s, %s, %s)"
        airports = ['CDG', 'JFK', 'LHR', 'LAX', 'DXB', 'HND']
        for i in range(20):
            departure = datetime.datetime.now() + datetime.timedelta(days=i*2, hours=i*3)
            arrival = departure + datetime.timedelta(hours=random.randint(2, 12))
            dep_airport, arr_airport = random.sample(airports, 2)
            data = (f'FL{i:03}', dep_airport, arr_airport, departure, arrival, random.choice(airline_ids), random.choice(aircraft_ids))
            cursor.execute(add_flight, data)
            flight_ids.append(cursor.lastrowid)
        cnx.commit()
        print(f"OK - {len(flight_ids)} flights inserted.")

        print("Populating fares...")
        fare_ids = []
        add_fare = "INSERT INTO fares (flight_id, class_type, price, availability) VALUES (%s, %s, %s, %s)"
        for flight_id in flight_ids:
            cursor.execute(add_fare, (flight_id, 'ECONOMY', round(random.uniform(200, 800), 2), random.randint(50, 150)))
            fare_ids.append(cursor.lastrowid)
            cursor.execute(add_fare, (flight_id, 'BUSINESS', round(random.uniform(800, 2500), 2), random.randint(10, 30)))
            fare_ids.append(cursor.lastrowid)
            if random.random() > 0.5:
                 cursor.execute(add_fare, (flight_id, 'FIRST', round(random.uniform(2500, 8000), 2), random.randint(2, 10)))
                 fare_ids.append(cursor.lastrowid)
        cnx.commit()
        print(f"OK - {len(fare_ids)} fares inserted.")

        print("Populating seats...")
        add_seat = "INSERT INTO seats (flight_id, seat_number, class_type, status) VALUES (%s, %s, %s, %s)"
        for flight_id in flight_ids:
            for i in range(1, 31):
                seat_class = random.choice(['ECONOMY', 'ECONOMY', 'ECONOMY', 'BUSINESS', 'FIRST'])
                cursor.execute(add_seat, (flight_id, f'{random.randint(1,30)}{random.choice("ABCDEF")}', seat_class, 'AVAILABLE'))
        cnx.commit()
        print(f"OK - Seats inserted for all flights.")

        print("Populating bookings...")
        booking_ids = []
        add_booking = "INSERT INTO bookings (booking_number, agency_id, flight_id, fare_id, seat_id, status, total_amount) VALUES (%s, %s, %s, %s, %s, %s, %s)"
        
        for i in range(15):
            flight_id = random.choice(flight_ids)
            cursor.execute("SELECT id FROM seats WHERE flight_id = %s AND status = 'AVAILABLE' LIMIT 1", (flight_id,))
            seat_result = cursor.fetchone()
            if not seat_result:
                continue
            seat_id = seat_result[0]

            cursor.execute("SELECT id, price FROM fares WHERE flight_id = %s", (flight_id,))
            fares_for_flight = cursor.fetchall()
            if not fares_for_flight:
                continue
            fare_id, price = random.choice(fares_for_flight)

            data = (f'BKNG{i:05}', random.choice(agency_ids), flight_id, fare_id, seat_id, random.choice(['PENDING_PAYMENT', 'CONFIRMED', 'CANCELLED']), price)
            cursor.execute(add_booking, data)
            booking_ids.append(cursor.lastrowid)
            
            cursor.execute("UPDATE seats SET status = 'BOOKED' WHERE id = %s", (seat_id,))

        cnx.commit()
        print(f"OK - {len(booking_ids)} bookings inserted and seats updated.")

        print("Populating passengers...")
        passengers_data = []
        for i, bid in enumerate(booking_ids):
             passengers_data.append((bid, random.choice(user_ids['CLIENT']), f'PassengerFirst{i}', f'PassengerLast{i}', f'PASS{i:07}', datetime.date(random.randint(1950, 2010), random.randint(1,12), random.randint(1,28))))
        add_passenger = "INSERT INTO passengers (booking_id, user_id, firstname, lastname, passport_number, date_of_birth) VALUES (%s, %s, %s, %s, %s, %s)"
        cursor.executemany(add_passenger, passengers_data)
        cnx.commit()
        print(f"OK - {cursor.rowcount} passengers inserted.")

        print("Populating tickets...")
        tickets_data = [(bid, f'TKT{i:06}', f'http://example.com/tickets/tkt{i:06}.pdf') for i, bid in enumerate(booking_ids) if random.random() > 0.3]
        add_ticket = "INSERT INTO tickets (booking_id, ticket_number, pdf_url) VALUES (%s, %s, %s)"
        if tickets_data:
            cursor.executemany(add_ticket, tickets_data)
            cnx.commit()
        print(f"OK - {cursor.rowcount} tickets inserted.")

        print("Populating booking_status_histories...")
        history_data = [(bid, 'CONFIRMED', 'PENDING_PAYMENT', 'Payment received') for bid in booking_ids if random.random() > 0.5]
        add_history = "INSERT INTO booking_status_histories (booking_id, status, previous_status, reason) VALUES (%s, %s, %s, %s)"
        if history_data:
            cursor.executemany(add_history, history_data)
            cnx.commit()
        print(f"OK - {cursor.rowcount} booking histories inserted.")
        
        print("Populating admin_status_histories...")
        admin_history_data = [(pid, 'ACTIVE', None, 'Account created') for pid in admin_profile_ids]
        add_admin_history = "INSERT INTO admin_status_histories (admin_profile_id, status, previous_status, reason) VALUES (%s, %s, %s, %s)"
        cursor.executemany(add_admin_history, admin_history_data)
        cnx.commit()
        print(f"OK - {cursor.rowcount} admin histories inserted.")

        print("Populating flight_requests...")
        flight_requests_data = []
        client_ids = user_ids['CLIENT']
        for i in range(10):
            departure_date = datetime.date.today() + datetime.timedelta(days=random.randint(10, 90))
            return_date = departure_date + datetime.timedelta(days=random.randint(7, 21)) if random.random() > 0.3 else None
            dep_airport = random.choice(airports)
            arr_airport = random.choice([a for a in airports if a != dep_airport])
            flight_requests_data.append(
                (
                    random.choice(client_ids),
                    random.choice(agency_ids),
                    dep_airport,
                    arr_airport,
                    departure_date,
                    return_date,
                    random.randint(1, 8),
                    random.choice(['ECONOMY', 'BUSINESS', 'FIRST']),
                    'Looking for best deals and flexible options.',
                    random.choice(['NEW', 'VIEWED', 'PROCESSED', 'CLOSED'])
                )
            )
        
        add_flight_request = """
            INSERT INTO flight_requests 
            (client_user_id, agency_id, departure_airport, arrival_airport, departure_date, return_date, num_passengers, desired_class, additional_notes, status) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """
        cursor.executemany(add_flight_request, flight_requests_data)
        cnx.commit()
        cursor.execute("SELECT id FROM flight_requests")
        flight_request_ids = [item[0] for item in cursor.fetchall()]
        print(f"OK - {len(flight_request_ids)} flight requests inserted.")

        print("Populating flight_proposals...")
        flight_proposals_data = []
        for i in range(len(flight_request_ids)):
            for _ in range(random.randint(1, 3)):
                flight_proposals_data.append(
                    (
                        flight_request_ids[i],
                        random.choice(flight_ids),
                        random.choice(agency_ids),
                        random.choice(['proposed', 'selected', 'expired'])
                    )
                )

        add_flight_proposal = """
            INSERT INTO flight_proposals
            (request_id, flight_id, agency_id, status)
            VALUES (%s, %s, %s, %s)
        """
        if flight_proposals_data:
            cursor.executemany(add_flight_proposal, flight_proposals_data)
            cnx.commit()
        print(f"OK - {cursor.rowcount} flight proposals inserted.")


        print("\nDatabase population completed successfully.")

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
