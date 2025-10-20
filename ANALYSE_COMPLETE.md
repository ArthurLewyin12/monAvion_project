# ANALYSE COMPLÈTE DU PROJET MonAvion

## 📊 STATISTIQUES

- **Total fichiers PHP** : 135 (hors PHPMailer)
- **Fichiers utilisant table `users`** : 5
- **Fichiers utilisant `$_SESSION['user']`** : 13
- **Fichiers avec anciennes tables** : ~18 dans app/

## 🗂️ STRUCTURE DU PROJET

### 1. PUBLIC (Landing Page) - 14 fichiers
```
public/
├── index.php (page d'accueil)
├── inscription.php ✅ ADAPTÉ
├── connexion.php ✅ ADAPTÉ
├── contact.php ✅ ADAPTÉ
├── demande-agence.php ✅ ADAPTÉ
├── demande-compagnie.php ✅ ADAPTÉ
├── vols.php (recherche vols - À DÉVELOPPER)
├── logout.php ✅ NOUVEAU
├── privacy.php, terms.php
├── components/ (6 composants réutilisables)
└── layouts/ (header, footer, main)
```

### 2. APP (Dashboards) - 33 fichiers
```
app/
├── admin/ (9 fichiers) ❌ ANCIENNES TABLES + SESSION
│   ├── index.php
│   ├── home.php
│   ├── dashboard_data.php ❌ users, flights, airlines, bookings
│   ├── users.php ❌ users
│   ├── agencies.php ❌ agencies
│   ├── airlines.php ❌ airlines
│   ├── flights.php ❌ flights
│   ├── bookings.php ❌ bookings
│   └── user_details.php ❌ users
│
├── agency/ (8 fichiers) ❌ ANCIENNES TABLES + SESSION
│   ├── index.php
│   ├── home.php ❌ $_SESSION['user']
│   ├── dashboard_data.php ❌ agencies, flight_requests
│   ├── search_flight.php ❌ flights, airlines, fares
│   ├── create_booking.php ❌ bookings, seats
│   ├── my_bookings.php ❌ bookings
│   ├── client_requests.php ❌ flight_requests, users
│   └── request_details.php ❌ flight_requests
│
├── airline/ (10 fichiers) ❌ ANCIENNES TABLES + SESSION
│   ├── index.php
│   ├── home.php
│   ├── dashboard_data.php ❌ airlines, flights, bookings
│   ├── my_flights.php
│   ├── my_fleet.php
│   ├── add_aircraft.php
│   ├── add_flight.php
│   ├── edit_aircraft.php
│   ├── edit_flight.php
│   ├── fares.php ❌ fares, flights
│   └── booking_reports.php ❌ bookings, flights
│
└── client/ (6 fichiers) ❌ ANCIENNES TABLES + SESSION
    ├── index.php
    ├── home.php ❌ $_SESSION['user']
    ├── dashboard_data.php ❌ users, flight_requests
    ├── my_trips.php
    ├── my_profile.php
    └── my_requests.php ❌ flight_requests, agencies
```

### 3. SRC (Logique Métier) - 11 fichiers
```
src/
├── controllers/
│   ├── Auth.php ✅ ADAPTÉ (create_user, login)
│   ├── inscription_process.php ✅ NOUVEAU (français)
│   ├── connexion_process.php ✅ NOUVEAU (français)
│   ├── contact_process.php ✅ NOUVEAU (français)
│   ├── demande_agence_process.php ✅ NOUVEAU (français)
│   ├── demande_compagnie_process.php ✅ NOUVEAU (français)
│   ├── booking_process.php ❌ ANCIEN (agencies, seats, bookings)
│   ├── flight_process.php ❌ ANCIEN (flights, airlines)
│   ├── fleet_process.php ❌ ANCIEN (aircrafts, airlines)
│   ├── fare_process.php ❌ ANCIEN (fares, flights)
│   └── flight_request_process.php ❌ ANCIEN (flight_requests, users)
│
├── functions/
│   ├── auth_helpers.php ✅ ADAPTÉ (nouvelle structure session)
│   ├── validation.php ✅ ADAPTÉ (support français)
│   └── sendEmail.php ✅ OK
│
└── views/partials/
    ├── sidebar.php ❌ $_SESSION['user']['user_type']
    └── body.php
```

## ⚠️ CONFLITS MAJEURS IDENTIFIÉS

### 1. STRUCTURE SESSION
**Ancien** : `$_SESSION['user']['id']`, `$_SESSION['user']['user_type']`
**Nouveau** : `$_SESSION['user_id']`, `$_SESSION['user_type']`

**Fichiers concernés** : 13
- Tous les dashboard_data.php (4)
- Tous les index.php de dashboards (4)
- sidebar.php
- 5 controllers anciens

### 2. NOMS DE TABLES

| Ancien (Anglais) | Nouveau (Français) | Fichiers affectés |
|------------------|-------------------|------------------|
| users | utilisateurs | 5 |
| agencies | agences | 3 |
| airlines | compagnies_aeriennes | 5 |
| flights | vols | 8 |
| aircrafts | avions | 3 |
| bookings | reservations | 6 |
| seats | sieges | 2 |
| fares | tarifs | 3 |
| flight_requests | demandes_vols | 4 |

**Total fichiers avec anciennes tables** : ~25

### 3. NOMS DE COLONNES

| Ancien | Nouveau |
|--------|---------|
| firstname | prenom |
| lastname | nom |
| user_type | type_utilisateur |
| current_status | statut_actuel |
| contact | telephone |
| password | mot_de_passe |

## ✅ CE QUI EST FAIT

1. **Base de données** : Complète en français (17 tables)
2. **Landing page** : Tous les formulaires liés aux nouveaux controllers
3. **3 fichiers src/** : Auth.php, validation.php, auth_helpers.php adaptés
4. **6 nouveaux controllers** : inscription, connexion, contact, demandes (français)
5. **logout.php** : Créé

## ❌ CE QUI RESTE À FAIRE

### PRIORITÉ 1 : Dashboards (33 fichiers)
- Adapter TOUTES les requêtes SQL aux noms français
- Changer `$_SESSION['user']` → `$_SESSION['user_id']`, etc.
- Tester chaque dashboard

### PRIORITÉ 2 : Controllers dashboard (5 fichiers)
- booking_process.php
- flight_process.php
- fleet_process.php
- fare_process.php
- flight_request_process.php

### PRIORITÉ 3 : Views
- sidebar.php : Adapter structure session
- body.php : Vérifier

### PRIORITÉ 4 : Page vols.php
- À développer complètement

## 📋 PLAN D'ACTION PROPOSÉ

### Option A : TOUT EN FRANÇAIS (Recommandé)
1. Créer un script de remplacement global pour :
   - Noms de tables
   - Noms de colonnes
   - Structure session
2. Tester dashboard par dashboard
3. Avantages : Cohérence totale, facilité future
4. Inconvénient : ~30 fichiers à modifier

### Option B : TOUT EN ANGLAIS
1. Renommer les tables en BD
2. Re-générer le schéma en anglais
3. Avantages : Moins de modifications
4. Inconvénient : Incohérent avec demande initiale

## 🎯 RECOMMANDATION

**Adopter Option A** et faire le travail proprement :
1. Script automatique de remplacement
2. Tests systématiques
3. Documentation finale

**Estimation** : 2-3 heures de travail méthodique

