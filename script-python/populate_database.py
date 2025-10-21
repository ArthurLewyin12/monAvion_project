import mysql.connector
from mysql.connector import errorcode
import datetime
import random
import json
import uuid

# --- Database Configuration ---
DB_HOST = "localhost"
DB_USER = "root"
DB_PASSWORD = ""
DB_NAME = "dbavion3"

# --- Error Log File ---
ERROR_LOG_FILE = "./population_errors.md"

def log_error(error_message):
    """Appends an error message to the markdown log file."""
    with open(ERROR_LOG_FILE, "a") as f:
        timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        f.write(f"## Error at {timestamp}\n")
        f.write("```\n")
        f.write(f"{error_message}\n")
        f.write("```\n\n")

def main():
    """Connects to the database and populates it with sample data based on the French schema."""
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
        # Order is important for foreign key constraints
        tables_to_truncate = [
            'messages_contact', 'demandes_compagnies', 'demandes_agences', 'demandes_vols',
            'historique_statuts_admin', 'historique_statuts_reservations', 'billets', 'passagers',
            'reservations', 'sieges', 'tarifs', 'vols', 'avions', 'profils_admin',
            'agences', 'compagnies_aeriennes', 'utilisateurs'
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

        print("Populating utilisateurs...")
        users_data = []
        for i in range(5):
            users_data.append(('Jean', f'Client{i}', f'client{i}@example.com', '123456789', 'hashed_password', 'CLIENT'))
            users_data.append(('Marie', f'Compagnie{i}', f'airline{i}@example.com', '987654321', 'hashed_password', 'COMPAGNIE'))
            users_data.append(('Pierre', f'Agence{i}', f'agency{i}@example.com', '555555555', 'hashed_password', 'AGENCE'))
            users_data.append(('Admin', f'Super{i}', f'admin{i}@example.com', '111222333', 'hashed_password', 'ADMIN'))

        add_user = "INSERT INTO utilisateurs (prenom, nom, email, telephone, mot_de_passe, type_utilisateur) VALUES (%s, %s, %s, %s, %s, %s)"
        cursor.executemany(add_user, users_data)
        cnx.commit()

        cursor.execute("SELECT id, type_utilisateur FROM utilisateurs")
        all_users = cursor.fetchall()
        user_ids = {
            'CLIENT': [u[0] for u in all_users if u[1] == 'CLIENT'],
            'COMPAGNIE': [u[0] for u in all_users if u[1] == 'COMPAGNIE'],
            'AGENCE': [u[0] for u in all_users if u[1] == 'AGENCE'],
            'ADMIN': [u[0] for u in all_users if u[1] == 'ADMIN']
        }
        print(f"OK - {len(all_users)} utilisateurs inserted.")

        print("Populating compagnies_aeriennes...")
        compagnies_data = [(uid, f'Compagnie Air-{i}', f'C{i}A', 'Une compagnie formidable', 10 + i) for i, uid in enumerate(user_ids['COMPAGNIE'])]
        add_compagnie = "INSERT INTO compagnies_aeriennes (utilisateur_id, nom_compagnie, code_iata, description, taille_flotte) VALUES (%s, %s, %s, %s, %s)"
        cursor.executemany(add_compagnie, compagnies_data)
        cnx.commit()
        cursor.execute("SELECT id FROM compagnies_aeriennes")
        compagnie_ids = [item[0] for item in cursor.fetchall()]
        print(f"OK - {len(compagnie_ids)} compagnies inserted.")

        print("Populating agences...")
        agences_data = [(uid, f'Agence de Voyage-{i}', f'LIC-{i}', '123 Rue du Voyage', datetime.date.today()) for i, uid in enumerate(user_ids['AGENCE'])]
        add_agence = "INSERT INTO agences (utilisateur_id, nom_agence, numero_licence, adresse, date_inscription) VALUES (%s, %s, %s, %s, %s)"
        cursor.executemany(add_agence, agences_data)
        cnx.commit()
        cursor.execute("SELECT id FROM agences")
        agence_ids = [item[0] for item in cursor.fetchall()]
        print(f"OK - {len(agence_ids)} agences inserted.")

        print("Populating profils_admin...")
        profils_admin_data = [(uid, f'Admin Profile {i}', 'IT', 'JUNIOR') for i, uid in enumerate(user_ids['ADMIN'])]
        add_profil_admin = "INSERT INTO profils_admin (utilisateur_id, nom_complet, departement, niveau_acces) VALUES (%s, %s, %s, %s)"
        cursor.executemany(add_profil_admin, profils_admin_data)
        cnx.commit()
        cursor.execute("SELECT id FROM profils_admin")
        profil_admin_ids = [item[0] for item in cursor.fetchall()]
        print(f"OK - {len(profil_admin_ids)} profils_admin inserted.")

        print("Populating avions...")
        avion_ids = []
        add_avion = "INSERT INTO avions (modele, compagnie_id, nombre_sieges_total, sieges_par_classe) VALUES (%s, %s, %s, %s)"
        for i in range(10):
            data = (f'Airbus A3{random.randint(2,8)}0', random.choice(compagnie_ids), 220 + i, json.dumps({'ECONOMIQUE': 180, 'AFFAIRE': 40}))
            cursor.execute(add_avion, data)
            avion_ids.append(cursor.lastrowid)
        cnx.commit()
        print(f"OK - {len(avion_ids)} avions inserted.")

        print("Populating vols...")
        vol_ids = []
        add_vol = "INSERT INTO vols (numero_vol, aeroport_depart, aeroport_arrivee, date_depart, date_arrivee, compagnie_id, avion_id) VALUES (%s, %s, %s, %s, %s, %s, %s)"
        airports = ['CDG', 'JFK', 'LHR', 'LAX', 'DXB', 'HND']
        for i in range(20):
            departure = datetime.datetime.now() + datetime.timedelta(days=i*2, hours=i*3)
            arrival = departure + datetime.timedelta(hours=random.randint(2, 12))
            dep_airport, arr_airport = random.sample(airports, 2)
            data = (f'AF{i:03}', dep_airport, arr_airport, departure, arrival, random.choice(compagnie_ids), random.choice(avion_ids))
            cursor.execute(add_vol, data)
            vol_ids.append(cursor.lastrowid)
        cnx.commit()
        print(f"OK - {len(vol_ids)} vols inserted.")

        print("Populating tarifs...")
        add_tarif = "INSERT INTO tarifs (vol_id, type_classe, prix, disponibilite) VALUES (%s, %s, %s, %s)"
        for vol_id in vol_ids:
            cursor.execute(add_tarif, (vol_id, 'ECONOMIQUE', round(random.uniform(200, 800), 2), random.randint(50, 150)))
            cursor.execute(add_tarif, (vol_id, 'AFFAIRE', round(random.uniform(800, 2500), 2), random.randint(10, 30)))
            if random.random() > 0.5:
                 cursor.execute(add_tarif, (vol_id, 'PREMIERE', round(random.uniform(2500, 8000), 2), random.randint(2, 10)))
        cnx.commit()
        print(f"OK - Tarifs inserted for all flights.")

        print("Populating sieges...")
        add_siege = "INSERT INTO sieges (vol_id, numero_siege, type_classe, statut) VALUES (%s, %s, %s, %s)"
        for vol_id in vol_ids:
            # Generate unique seat numbers systematically
            for row in range(1, 31):
                for seat_letter in ['A', 'B', 'C', 'D', 'E', 'F']:
                    seat_number = f'{row}{seat_letter}'
                    seat_class = 'AFFAIRE' if row <= 5 else 'ECONOMIQUE' # Example logic
                    cursor.execute(add_siege, (vol_id, seat_number, seat_class, 'DISPONIBLE'))
        cnx.commit()
        print(f"OK - Sieges inserted for all flights.")

        print("Populating reservations...")
        reservation_ids = []
        add_reservation = "INSERT INTO reservations (numero_reservation, agence_id, client_id, type_reservation, vol_id, siege_id, statut, montant_total) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
        for i in range(15):
            vol_id = random.choice(vol_ids)
            cursor.execute("SELECT id FROM sieges WHERE vol_id = %s AND statut = 'DISPONIBLE' LIMIT 1", (vol_id,))
            seat_result = cursor.fetchone()
            if not seat_result:
                continue
            siege_id = seat_result[0]

            cursor.execute("SELECT prix FROM tarifs WHERE vol_id = %s", (vol_id,))
            prices = [p[0] for p in cursor.fetchall()]
            if not prices:
                continue
            price = random.choice(prices)

            is_direct = random.choice([True, False])
            agence_id_val = None if is_direct else random.choice(agence_ids)
            client_id_val = random.choice(user_ids['CLIENT']) if is_direct else None
            type_reservation_val = 'DIRECTE' if is_direct else 'PAR_AGENCE'

            data = (f'RES-{uuid.uuid4().hex[:10].upper()}', agence_id_val, client_id_val, type_reservation_val, vol_id, siege_id, random.choice(['EN_ATTENTE', 'CONFIRMEE', 'ANNULEE']), price)
            cursor.execute(add_reservation, data)
            reservation_ids.append(cursor.lastrowid)
            
            cursor.execute("UPDATE sieges SET statut = 'RESERVE' WHERE id = %s", (siege_id,))

        cnx.commit()
        print(f"OK - {len(reservation_ids)} reservations inserted and seats updated.")

        print("Populating passagers...")
        passagers_data = []
        for i, res_id in enumerate(reservation_ids):
             passagers_data.append((res_id, random.choice(user_ids['CLIENT']), f'PassagerPrenom{i}', f'PassagerNom{i}', f'PASS{i:07}', datetime.date(random.randint(1950, 2010), random.randint(1,12), random.randint(1,28))))
        add_passager = "INSERT INTO passagers (reservation_id, utilisateur_id, prenom, nom, numero_passeport, date_naissance) VALUES (%s, %s, %s, %s, %s, %s)"
        cursor.executemany(add_passager, passagers_data)
        cnx.commit()
        print(f"OK - {cursor.rowcount} passagers inserted.")

        print("Populating billets...")
        billets_data = [(res_id, f'Billet-{uuid.uuid4().hex[:8].upper()}', f'http://example.com/billets/billet{i:06}.pdf') for i, res_id in enumerate(reservation_ids) if random.random() > 0.3]
        add_billet = "INSERT INTO billets (reservation_id, numero_billet, url_pdf) VALUES (%s, %s, %s)"
        if billets_data:
            cursor.executemany(add_billet, billets_data)
            cnx.commit()
        print(f"OK - {cursor.rowcount} billets inserted.")

        print("Populating historique_statuts_reservations...")
        history_data = [(res_id, 'CONFIRMEE', 'EN_ATTENTE', 'Paiement reçu') for res_id in reservation_ids if random.random() > 0.5]
        add_history = "INSERT INTO historique_statuts_reservations (reservation_id, statut, statut_precedent, raison) VALUES (%s, %s, %s, %s)"
        if history_data:
            cursor.executemany(add_history, history_data)
            cnx.commit()
        print(f"OK - {cursor.rowcount} booking histories inserted.")
        
        print("Populating demandes_agences...")
        demandes_agences_data = [ (f'Future Agence {i}', f'LIC-DEM-{i}', 'France', '100 Rue de l\'Avenir', f'Contact {i}', f'contact.agence{i}@example.com', '0102030405', 'Nous sommes une agence dynamique.') for i in range(3)]
        add_demande_agence = "INSERT INTO demandes_agences (nom_agence, numero_licence, pays, adresse, nom_contact, email, telephone, message) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
        cursor.executemany(add_demande_agence, demandes_agences_data)
        cnx.commit()
        print(f"OK - {cursor.rowcount} demandes_agences inserted.")

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