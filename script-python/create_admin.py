#!/usr/bin/env python3
"""
Script pour créer un utilisateur admin avec mot de passe hashé
Usage: python3 create_admin.py

Dépendances requises:
    pip3 install bcrypt mysql-connector-python
"""

import sys

# Vérifier et installer les dépendances si nécessaire
try:
    import bcrypt
except ImportError:
    print("⚠️  Module 'bcrypt' non trouvé. Installation en cours...")
    import subprocess
    subprocess.check_call([sys.executable, "-m", "pip", "install", "bcrypt"])
    import bcrypt
    print("✓ Module 'bcrypt' installé avec succès\n")

try:
    import mysql.connector
except ImportError:
    print("⚠️  Module 'mysql-connector-python' non trouvé. Installation en cours...")
    import subprocess
    subprocess.check_call([sys.executable, "-m", "pip", "install", "mysql-connector-python"])
    import mysql.connector
    print("✓ Module 'mysql-connector-python' installé avec succès\n")

# Configuration de la base de données
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',  # Modifier si nécessaire
    'database': 'monvolenlignedb'
}

def hash_password(password):
    """Hash un mot de passe avec bcrypt"""
    # Générer le salt et hasher le mot de passe
    salt = bcrypt.gensalt()
    hashed = bcrypt.hashpw(password.encode('utf-8'), salt)
    return hashed.decode('utf-8')

def create_admin_user():
    """Créer un utilisateur admin avec mot de passe hashé"""

    print("=" * 60)
    print("CRÉATION D'UN COMPTE ADMINISTRATEUR")
    print("=" * 60)

    # Données de l'admin
    admin_data = {
        'prenom': 'Admin',
        'nom': 'Principal',
        'email': 'admin@monvolenlignedb.com',
        'telephone': '0123456789',
        'password': 'admin123',  # Mot de passe par défaut
        'type': 'ADMIN'
    }

    print(f"\nCréation du compte admin avec les informations suivantes:")
    print(f"  - Prénom: {admin_data['prenom']}")
    print(f"  - Nom: {admin_data['nom']}")
    print(f"  - Email: {admin_data['email']}")
    print(f"  - Téléphone: {admin_data['telephone']}")
    print(f"  - Mot de passe: {admin_data['password']}")
    print(f"  - Type: {admin_data['type']}")

    try:
        # Connexion à la base de données
        print("\n[1/4] Connexion à la base de données...")
        cnx = mysql.connector.connect(**DB_CONFIG)
        cursor = cnx.cursor()
        print("✓ Connexion réussie")

        # Vérifier si l'email existe déjà
        print(f"\n[2/4] Vérification de l'existence de l'email {admin_data['email']}...")
        cursor.execute("SELECT id FROM utilisateurs WHERE email = %s", (admin_data['email'],))
        existing_user = cursor.fetchone()

        if existing_user:
            print(f"✗ ERREUR: Un utilisateur avec l'email {admin_data['email']} existe déjà (ID: {existing_user[0]})")
            print("\nSolutions:")
            print("  1. Supprimer l'utilisateur existant: DELETE FROM utilisateurs WHERE email = 'admin@monvolenlignedb.com';")
            print("  2. Modifier l'email dans ce script (ligne 29)")
            cursor.close()
            cnx.close()
            sys.exit(1)

        print("✓ Email disponible")

        # Hasher le mot de passe
        print(f"\n[3/4] Hashage du mot de passe avec bcrypt...")
        hashed_password = hash_password(admin_data['password'])
        print(f"✓ Mot de passe hashé: {hashed_password[:30]}...")

        # Insérer l'utilisateur
        print(f"\n[4/4] Insertion de l'utilisateur dans la base de données...")
        insert_user_query = """
            INSERT INTO utilisateurs (prenom, nom, email, telephone, mot_de_passe, type_utilisateur, date_creation)
            VALUES (%s, %s, %s, %s, %s, %s, NOW())
        """
        user_values = (
            admin_data['prenom'],
            admin_data['nom'],
            admin_data['email'],
            admin_data['telephone'],
            hashed_password,
            admin_data['type']
        )
        cursor.execute(insert_user_query, user_values)
        user_id = cursor.lastrowid
        print(f"✓ Utilisateur créé avec l'ID: {user_id}")

        # Créer le profil admin
        print(f"\n[5/5] Création du profil admin...")
        insert_profile_query = """
            INSERT INTO profils_admin (utilisateur_id, nom_complet, departement, niveau_acces, date_creation)
            VALUES (%s, %s, %s, %s, NOW())
        """
        profile_values = (
            user_id,
            f"{admin_data['prenom']} {admin_data['nom']}",
            'Administration',
            'SUPER_ADMIN'
        )
        cursor.execute(insert_profile_query, profile_values)
        profile_id = cursor.lastrowid
        print(f"✓ Profil admin créé avec l'ID: {profile_id}")

        # Commit des changements
        cnx.commit()

        print("\n" + "=" * 60)
        print("✓ COMPTE ADMINISTRATEUR CRÉÉ AVEC SUCCÈS !")
        print("=" * 60)
        print("\nInformations de connexion:")
        print(f"  URL de connexion: /app/auth/connexion.php")
        print(f"  Email: {admin_data['email']}")
        print(f"  Mot de passe: {admin_data['password']}")
        print("\n⚠️  IMPORTANT: Changez ce mot de passe après votre première connexion !")
        print("=" * 60)

        # Fermer la connexion
        cursor.close()
        cnx.close()

    except mysql.connector.Error as err:
        print(f"\n✗ ERREUR MySQL: {err}")
        print(f"  Code d'erreur: {err.errno}")
        print(f"  Message: {err.msg}")
        sys.exit(1)
    except Exception as e:
        print(f"\n✗ ERREUR: {e}")
        sys.exit(1)

if __name__ == "__main__":
    create_admin_user()
