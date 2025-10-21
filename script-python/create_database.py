import mysql.connector
from mysql.connector import errorcode
import datetime

# --- Database Configuration ---
DB_HOST = "localhost"
DB_USER = "root"
DB_PASSWORD = ""
DB_NAME = "dbavion3"

# --- Error Log File ---
ERROR_LOG_FILE = "./database_errors.md"

def log_error(error_message):
    """Appends an error message to the markdown log file."""
    with open(ERROR_LOG_FILE, "a") as f:
        timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        f.write(f"## Error at {timestamp}\n")
        f.write("```\n")
        f.write(f"{error_message}\n")
        f.write("```\n\n")

# --- SQL Statements for creating tables (from db.sql) ---
TABLES = {}

TABLES['utilisateurs'] = (
    """CREATE TABLE `utilisateurs` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `prenom` VARCHAR(100) NOT NULL,
        `nom` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) UNIQUE NOT NULL,
        `telephone` VARCHAR(20),
        `mot_de_passe` VARCHAR(255) NOT NULL,
        `premiere_connexion` BOOLEAN DEFAULT FALSE,
        `avatar` VARCHAR(500),
        `abonnement_newsletter` BOOLEAN DEFAULT FALSE,
        `statut_actuel` ENUM('ACTIF', 'INACTIF', 'SUSPENDU') DEFAULT 'ACTIF',
        `type_utilisateur` ENUM('COMPAGNIE', 'AGENCE', 'ADMIN', 'CLIENT') DEFAULT 'CLIENT',
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_email` (`email`),
        INDEX `idx_telephone` (`telephone`),
        INDEX `idx_type_utilisateur` (`type_utilisateur`),
        INDEX `idx_statut` (`statut_actuel`),
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['compagnies_aeriennes'] = (
    """CREATE TABLE `compagnies_aeriennes` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `utilisateur_id` BIGINT UNIQUE NOT NULL,
        `nom_compagnie` VARCHAR(200) NOT NULL,
        `code_iata` VARCHAR(3) UNIQUE,
        `description` TEXT,
        `pays` VARCHAR(100) DEFAULT 'France',
        `taille_flotte` INT DEFAULT NULL,
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_utilisateur_id` (`utilisateur_id`),
        FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['agences'] = (
    """CREATE TABLE `agences` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `utilisateur_id` BIGINT UNIQUE NOT NULL,
        `nom_agence` VARCHAR(200) NOT NULL,
        `numero_licence` VARCHAR(50) UNIQUE,
        `adresse` TEXT,
        `telephone` VARCHAR(20),
        `pays` VARCHAR(100) DEFAULT 'France',
        `nombre_employes` INT DEFAULT NULL,
        `nombre_avertissements` INT DEFAULT 0,
        `statut_actuel` ENUM('ACTIF', 'INACTIF', 'SUSPENDU') DEFAULT 'ACTIF',
        `date_inscription` DATE NULL,
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_utilisateur_id` (`utilisateur_id`),
        INDEX `idx_statut` (`statut_actuel`),
        INDEX `idx_avertissements` (`nombre_avertissements`),
        FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['profils_admin'] = (
    """CREATE TABLE `profils_admin` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `utilisateur_id` BIGINT UNIQUE NOT NULL,
        `nom_complet` VARCHAR(200) NOT NULL,
        `departement` VARCHAR(100),
        `niveau_acces` ENUM('JUNIOR', 'SENIOR', 'SUPER') DEFAULT 'JUNIOR',
        `date_dernier_audit` TIMESTAMP NULL,
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_utilisateur_id` (`utilisateur_id`),
        INDEX `idx_niveau_acces` (`niveau_acces`),
        FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['avions'] = (
    """CREATE TABLE `avions` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `modele` VARCHAR(100) NOT NULL,
        `compagnie_id` BIGINT NOT NULL,
        `nombre_sieges_total` INT NOT NULL,
        `sieges_par_classe` JSON,
        `description` TEXT,
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_compagnie_id` (`compagnie_id`),
        FOREIGN KEY (`compagnie_id`) REFERENCES `compagnies_aeriennes`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['vols'] = (
    """CREATE TABLE `vols` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `numero_vol` VARCHAR(20) NOT NULL,
        `aeroport_depart` VARCHAR(3) NOT NULL,
        `aeroport_arrivee` VARCHAR(3) NOT NULL,
        `date_depart` DATETIME NOT NULL,
        `date_arrivee` DATETIME NOT NULL,
        `compagnie_id` BIGINT NOT NULL,
        `avion_id` BIGINT NOT NULL,
        `statut` ENUM('PROGRAMME', 'RETARDE', 'ANNULE') DEFAULT 'PROGRAMME',
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_numero_vol` (`numero_vol`),
        INDEX `idx_depart` (`aeroport_depart`, `date_depart`),
        INDEX `idx_arrivee` (`aeroport_arrivee`, `date_arrivee`),
        INDEX `idx_compagnie_id` (`compagnie_id`),
        INDEX `idx_avion_id` (`avion_id`),
        INDEX `idx_statut` (`statut`),
        FOREIGN KEY (`compagnie_id`) REFERENCES `compagnies_aeriennes`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`avion_id`) REFERENCES `avions`(`id`) ON DELETE RESTRICT,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['tarifs'] = (
    """CREATE TABLE `tarifs` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `vol_id` BIGINT NOT NULL,
        `type_classe` ENUM('ECONOMIQUE', 'AFFAIRE', 'PREMIERE') NOT NULL,
        `prix` DECIMAL(10, 2) NOT NULL,
        `devise` VARCHAR(3) DEFAULT 'EUR',
        `disponibilite` INT DEFAULT 0,
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_vol_id` (`vol_id`),
        INDEX `idx_classe` (`type_classe`),
        INDEX `idx_prix` (`prix`),
        FOREIGN KEY (`vol_id`) REFERENCES `vols`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['sieges'] = (
    """CREATE TABLE `sieges` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `vol_id` BIGINT NOT NULL,
        `numero_siege` VARCHAR(10) NOT NULL,
        `type_classe` ENUM('ECONOMIQUE', 'AFFAIRE', 'PREMIERE') NOT NULL,
        `statut` ENUM('DISPONIBLE', 'RESERVE', 'ANNULE') DEFAULT 'DISPONIBLE',
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        UNIQUE KEY `uk_siege_vol` (`vol_id`, `numero_siege`),
        INDEX `idx_vol_id` (`vol_id`),
        INDEX `idx_statut` (`statut`),
        INDEX `idx_classe` (`type_classe`),
        FOREIGN KEY (`vol_id`) REFERENCES `vols`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['reservations'] = (
    """CREATE TABLE `reservations` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `numero_reservation` VARCHAR(50) UNIQUE NOT NULL,
        `agence_id` BIGINT NULL,
        `client_id` BIGINT NULL,
        `type_reservation` ENUM('DIRECTE', 'PAR_AGENCE') NOT NULL,
        `vol_id` BIGINT NOT NULL,
        `siege_id` BIGINT NOT NULL,
        `statut` ENUM('EN_ATTENTE', 'CONFIRMEE', 'ANNULEE') DEFAULT 'EN_ATTENTE',
        `montant_total` DECIMAL(10, 2) NOT NULL,
        `devise` VARCHAR(3) DEFAULT 'EUR',
        `mode_paiement` ENUM('CARTE', 'PAYPAL', 'AGENCE') DEFAULT 'CARTE',
        `statut_paiement` ENUM('EN_ATTENTE', 'PAYE') DEFAULT 'EN_ATTENTE',
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_numero_reservation` (`numero_reservation`),
        INDEX `idx_agence_id` (`agence_id`),
        INDEX `idx_client_id` (`client_id`),
        INDEX `idx_vol_id` (`vol_id`),
        INDEX `idx_statut` (`statut`),
        INDEX `idx_type_reservation` (`type_reservation`),
        FOREIGN KEY (`agence_id`) REFERENCES `agences`(`id`) ON DELETE RESTRICT,
        FOREIGN KEY (`client_id`) REFERENCES `utilisateurs`(`id`) ON DELETE RESTRICT,
        FOREIGN KEY (`vol_id`) REFERENCES `vols`(`id`) ON DELETE RESTRICT,
        FOREIGN KEY (`siege_id`) REFERENCES `sieges`(`id`) ON DELETE RESTRICT,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`),
        CONSTRAINT `chk_reservation_type` CHECK (
            (`type_reservation` = 'PAR_AGENCE' AND `agence_id` IS NOT NULL AND `client_id` IS NULL) OR
            (`type_reservation` = 'DIRECTE' AND `client_id` IS NOT NULL AND `agence_id` IS NULL)
        )
    ) ENGINE=InnoDB"""
)

TABLES['passagers'] = (
    """CREATE TABLE `passagers` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `reservation_id` BIGINT NOT NULL,
        `utilisateur_id` BIGINT NULL,
        `prenom` VARCHAR(100) NOT NULL,
        `nom` VARCHAR(100) NOT NULL,
        `numero_passeport` VARCHAR(50),
        `date_naissance` DATE,
        `nationalite` VARCHAR(100),
        `telephone` VARCHAR(20),
        `email` VARCHAR(255),
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_reservation_id` (`reservation_id`),
        INDEX `idx_utilisateur_id` (`utilisateur_id`),
        FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['billets'] = (
    """CREATE TABLE `billets` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `reservation_id` BIGINT NOT NULL,
        `numero_billet` VARCHAR(50) UNIQUE NOT NULL,
        `url_pdf` VARCHAR(500),
        `date_emission` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `date_suppression` TIMESTAMP NULL,
        `cree_par` BIGINT NULL,
        `modifie_par` BIGINT NULL,
        `supprime_par` BIGINT NULL,
        INDEX `idx_reservation_id` (`reservation_id`),
        INDEX `idx_numero_billet` (`numero_billet`),
        FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs`(`id`),
        FOREIGN KEY (`supprime_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['historique_statuts_reservations'] = (
    """CREATE TABLE `historique_statuts_reservations` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `reservation_id` BIGINT NOT NULL,
        `statut` ENUM('EN_ATTENTE', 'CONFIRMEE', 'ANNULEE') NOT NULL,
        `statut_precedent` ENUM('EN_ATTENTE', 'CONFIRMEE', 'ANNULEE') NULL,
        `raison` TEXT NULL,
        `commentaire` TEXT NULL,
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `cree_par` BIGINT NULL,
        INDEX `idx_reservation_id` (`reservation_id`),
        INDEX `idx_statut` (`statut`),
        INDEX `idx_date_creation` (`date_creation`),
        FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['historique_statuts_admin'] = (
    """CREATE TABLE `historique_statuts_admin` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `profil_admin_id` BIGINT NOT NULL,
        `statut` ENUM('ACTIF', 'SUSPENDU', 'DEGRADE') NOT NULL,
        `statut_precedent` ENUM('ACTIF', 'SUSPENDU', 'DEGRADE') NULL,
        `raison` TEXT NULL,
        `commentaire` TEXT NULL,
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `cree_par` BIGINT NULL,
        INDEX `idx_profil_admin_id` (`profil_admin_id`),
        INDEX `idx_statut` (`statut`),
        INDEX `idx_date_creation` (`date_creation`),
        FOREIGN KEY (`profil_admin_id`) REFERENCES `profils_admin`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['demandes_vols'] = (
    """CREATE TABLE `demandes_vols` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `client_utilisateur_id` BIGINT NOT NULL,
        `agence_id` BIGINT NULL COMMENT 'NULL si pas encore assignée à une agence',
        `aeroport_depart` VARCHAR(10) NOT NULL,
        `aeroport_arrivee` VARCHAR(10) NOT NULL,
        `date_depart` DATE NOT NULL,
        `date_retour` DATE NULL,
        `nombre_passagers` INT NOT NULL,
        `classe_desiree` ENUM('ECONOMIQUE', 'AFFAIRE', 'PREMIERE') NOT NULL,
        `notes_supplementaires` TEXT,
        `statut` ENUM('NOUVELLE', 'VUE', 'TRAITEE', 'FERMEE') DEFAULT 'NOUVELLE',
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_client_id` (`client_utilisateur_id`),
        INDEX `idx_agence_id` (`agence_id`),
        INDEX `idx_statut` (`statut`),
        FOREIGN KEY (`client_utilisateur_id`) REFERENCES `utilisateurs`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`agence_id`) REFERENCES `agences`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB"""
)

TABLES['demandes_agences'] = (
    """CREATE TABLE `demandes_agences` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `nom_agence` VARCHAR(200) NOT NULL,
        `numero_licence` VARCHAR(50) NOT NULL,
        `pays` VARCHAR(100) NOT NULL,
        `adresse` TEXT NOT NULL,
        `nom_contact` VARCHAR(200) NOT NULL,
        `email` VARCHAR(191) NOT NULL,
        `telephone` VARCHAR(20) NOT NULL,
        `nombre_employes` INT NULL,
        `message` TEXT NOT NULL,
        `statut` ENUM('EN_ATTENTE', 'APPROUVEE', 'REJETEE') DEFAULT 'EN_ATTENTE',
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `examine_par` BIGINT NULL,
        `date_examen` TIMESTAMP NULL,
        `raison_rejet` TEXT NULL,
        INDEX `idx_statut` (`statut`),
        INDEX `idx_email` (`email`),
        INDEX `idx_date_creation` (`date_creation`),
        FOREIGN KEY (`examine_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['demandes_compagnies'] = (
    """CREATE TABLE `demandes_compagnies` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `nom_compagnie` VARCHAR(200) NOT NULL,
        `code_iata` VARCHAR(3) NOT NULL,
        `pays` VARCHAR(100) NOT NULL,
        `nom_contact` VARCHAR(200) NOT NULL,
        `email` VARCHAR(191) NOT NULL,
        `telephone` VARCHAR(20) NOT NULL,
        `taille_flotte` INT NULL,
        `message` TEXT NOT NULL,
        `statut` ENUM('EN_ATTENTE', 'APPROUVEE', 'REJETEE') DEFAULT 'EN_ATTENTE',
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `examine_par` BIGINT NULL,
        `date_examen` TIMESTAMP NULL,
        `raison_rejet` TEXT NULL,
        INDEX `idx_statut` (`statut`),
        INDEX `idx_email` (`email`),
        INDEX `idx_date_creation` (`date_creation`),
        FOREIGN KEY (`examine_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)

TABLES['messages_contact'] = (
    """CREATE TABLE `messages_contact` (
        `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
        `nom` VARCHAR(200) NOT NULL,
        `email` VARCHAR(191) NOT NULL,
        `telephone` VARCHAR(20) NULL,
        `sujet` ENUM('demo', 'information', 'partenariat', 'support', 'autre') NOT NULL,
        `message` TEXT NOT NULL,
        `statut` ENUM('NOUVEAU', 'LU', 'REPONDU', 'FERME') DEFAULT 'NOUVEAU',
        `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `repondu_par` BIGINT NULL,
        `date_reponse` TIMESTAMP NULL,
        `message_reponse` TEXT NULL,
        INDEX `idx_statut` (`statut`),
        INDEX `idx_email` (`email`),
        INDEX `idx_sujet` (`sujet`),
        INDEX `idx_date_creation` (`date_creation`),
        FOREIGN KEY (`repondu_par`) REFERENCES `utilisateurs`(`id`)
    ) ENGINE=InnoDB"""
)


def main():
    """Connects to the database, creates it if not exists, and creates all tables."""
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

        # Drop database if it exists for a clean setup
        try:
            cursor.execute(f"DROP DATABASE IF EXISTS `{DB_NAME}`")
            print(f"Database '{DB_NAME}' dropped.")
        except mysql.connector.Error as err:
            error_msg = f"Failed to drop database: {err}"
            print(error_msg)
            log_error(error_msg)
            raise

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
                    # We will not raise here to allow the script to continue with other tables
        
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