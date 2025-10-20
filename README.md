# MonAvion - Plateforme de Réservation de Vols

## 📁 Structure du Projet

```
monAvion/
├── public/                          # Partie publique (landing page + formulaires)
│   ├── index.php                   # Page d'accueil landing
│   ├── connexion.php               # Page de connexion
│   ├── inscription.php             # Page d'inscription
│   ├── contact.php                 # Page de contact
│   ├── demande-agence.php          # Formulaire demande partenariat agence
│   ├── demande-compagnie.php       # Formulaire demande partenariat compagnie
│   ├── vols.php                    # Page recherche de vols (en dev)
│   ├── assets/                     # CSS, JS, images
│   ├── components/                 # Composants réutilisables landing
│   ├── layouts/                    # Layouts (header, footer)
│   └── main.css                    # CSS principal
│
├── app/                             # Application (après connexion)
│   ├── admin/                      # Dashboard administrateur
│   │   ├── home.php
│   │   ├── users.php
│   │   ├── agencies.php
│   │   ├── airlines.php
│   │   ├── flights.php
│   │   └── bookings.php
│   │
│   ├── agency/                     # Dashboard agences
│   │   ├── home.php
│   │   ├── search_flight.php
│   │   ├── create_booking.php
│   │   ├── my_bookings.php
│   │   └── client_requests.php
│   │
│   ├── airline/                    # Dashboard compagnies aériennes
│   │   ├── home.php
│   │   ├── add_aircraft.php
│   │   ├── add_flight.php
│   │   ├── fares.php
│   │   └── booking_reports.php
│   │
│   └── client/                     # Dashboard clients
│       ├── home.php
│       └── request_flight.php
│
├── src/                             # Code métier (logique)
│   ├── controllers/                # Contrôleurs
│   ├── functions/                  # Fonctions utilitaires
│   ├── views/                      # Vues partielles
│   └── PHPMailer-master/           # Bibliothèque email
│
├── config/                          # Configuration
│   └── db.php                      # Configuration base de données
│
├── db.sql                           # Schéma de base de données (FRANÇAIS)
├── CHANGELOG_DB.md                  # Historique des modifications BD
└── Documentation/                   # Fichiers de documentation
    ├── principe-du-site.md
    ├── feature-client-flight-request.md
    └── agence-compagnie-implementation.md
```

## 🚀 Installation

### 1. Configuration de la base de données

```bash
# Créer la base de données
mysql -u root -p < db.sql
```

### 2. Configuration

Mettre à jour `/config/db.php` avec vos informations :

```php
$host = 'localhost';
$dbname = 'dbAvion2';
$username = 'root';
$password = 'votre_mot_de_passe';
```

### 3. Accéder au site

- **Landing page** : `http://localhost/monAvion/public/`
- **Connexion** : `http://localhost/monAvion/public/connexion.php`
- **Admin dashboard** : `http://localhost/monAvion/app/admin/`

## 📋 Fonctionnalités

### Partie Publique (public/)

✅ **Landing page** : Présentation de la plateforme
✅ **Connexion/Inscription** : Authentification utilisateurs
✅ **Contact** : Formulaire de contact
✅ **Demandes de partenariat** : Formulaires pour agences et compagnies
⚠️ **Recherche de vols** : En développement

### Dashboards (app/)

#### Admin
- Gestion des utilisateurs
- Validation des demandes de partenariat
- Supervision des agences et compagnies
- Vue d'ensemble des vols et réservations

#### Agence
- Recherche de vols
- Création de réservations pour clients
- Gestion des demandes de vols clients
- Historique des réservations

#### Compagnie Aérienne
- Ajout d'avions et de vols
- Gestion des tarifs
- Rapports de réservations
- Suivi des performances

#### Client
- Demande de vols auprès d'une agence
- Suivi des réservations

## 🗄️ Base de Données

### Tables Principales

- **utilisateurs** : Tous les utilisateurs (clients, agences, compagnies, admins)
- **agences** : Profils des agences de voyage
- **compagnies_aeriennes** : Profils des compagnies
- **vols** : Catalogue des vols
- **reservations** : Réservations effectuées
- **demandes_agences** : Demandes de partenariat agences (en attente de validation)
- **demandes_compagnies** : Demandes de partenariat compagnies (en attente)
- **messages_contact** : Messages depuis le formulaire de contact

Voir `db.sql` pour le schéma complet.

## 🎨 Design

- **Style** : Moderne, responsive
- **Framework CSS** : Variables CSS personnalisées
- **Composants** : Modulaires et réutilisables
- **Animations** : Transitions fluides, effets de survol

## 📝 À Développer

### Priorité Haute
1. Page de recherche de vols (public/vols.php)
2. Scripts de traitement des formulaires (process-*.php)
3. Système d'authentification complet
4. Intégration paiements

### Priorité Moyenne
5. Notifications email (via PHPMailer)
6. Génération de billets PDF
7. Système de sessions sécurisé

### Priorité Basse
8. API de vols externe
9. Optimisations performances
10. Tests automatisés

## 🔐 Sécurité

**À implémenter :**
- Validation CSRF sur tous les formulaires
- Hachage des mots de passe avec `password_hash()`
- Protection contre les injections SQL (PDO préparé)
- Sessions sécurisées
- Protection des fichiers sensibles (.htaccess)

## 📚 Documentation

- `CHANGELOG_DB.md` : Historique des modifications de la base de données
- `principe-du-site.md` : Fonctionnement détaillé de la plateforme
- `feature-client-flight-request.md` : Système de demande de vols clients
- `public/DESCRIPTION_PROJET.md` : Description pour la présentation

## 👥 Contributeurs

Projet développé dans le cadre d'un projet de classe.

---

**Version** : 1.0
**Date** : Octobre 2025
