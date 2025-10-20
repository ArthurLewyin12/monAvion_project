# STRUCTURE ORIGINALE DES DASHBOARDS

Ce document archive la structure complète des dashboards avant refonte.

---

## 📊 DASHBOARD ADMIN

### **Pages disponibles**
1. **home.php** - Page d'accueil
2. **users.php** - Gestion des utilisateurs
3. **user_details.php** - Détails et modification d'un utilisateur
4. **agencies.php** - Liste des agences
5. **airlines.php** - Liste des compagnies aériennes
6. **flights.php** - Liste de tous les vols
7. **bookings.php** - Liste de toutes les réservations
8. **index.php** - Point d'entrée (routing)
9. **dashboard_data.php** - Collecte des données

### **Fonctionnalités**

#### **home.php**
```
Statistiques affichées :
- Nombre d'utilisateurs par rôle (CLIENT, AGENCY, AIRLINE, ADMIN)
- Vols programmés dans les 7 prochains jours
- Réservations des dernières 24 heures
- Liste des inscriptions en attente (status = INACTIVE)

Graphiques prévus :
- Évolution des inscriptions (30 derniers jours)
- Répartitions des réservations par compagnie
```

#### **users.php**
```sql
SELECT id, firstname, lastname, email, user_type, current_status
FROM users
ORDER BY created_at DESC
```
Actions : Voir détails de l'utilisateur

#### **user_details.php**
```
Affichage :
- Toutes les informations utilisateur
- Profil agence (si AGENCY)
- Profil compagnie (si AIRLINE)

Actions :
- Activer un compte (INACTIVE → ACTIVE)
- Suspendre un compte (ACTIVE → SUSPENDED)
- Envoyer un email de notification
```

#### **agencies.php**
```sql
SELECT 
    a.id, a.agency_name, a.license_number, 
    a.warnings_count, a.current_status, 
    u.email, a.created_at
FROM agencies a
JOIN users u ON a.user_id = u.id
ORDER BY a.created_at DESC
```
Actions : Voir détails de l'agence

#### **airlines.php**
```sql
SELECT 
    al.id, al.company_name, al.iata_code, al.country,
    u.current_status, al.created_at,
    COUNT(DISTINCT f.id) as flight_count
FROM airlines al
JOIN users u ON al.user_id = u.id
LEFT JOIN flights f ON al.id = f.airline_id
GROUP BY al.id
ORDER BY al.created_at DESC
```
Actions : Voir détails de la compagnie

#### **flights.php**
```sql
SELECT 
    f.id, f.flight_number, 
    f.departure_airport, f.arrival_airport,
    f.departure_date, f.arrival_date, f.status,
    al.company_name
FROM flights f
JOIN airlines al ON f.airline_id = al.id
ORDER BY f.departure_date ASC
```

#### **bookings.php**
```sql
SELECT 
    b.id, b.booking_number, b.status, b.total_amount, b.created_at,
    ag.agency_name,
    f.flight_number,
    CONCAT(p.firstname, ' ', p.lastname) as passenger_name
FROM bookings b
LEFT JOIN agencies ag ON b.agency_id = ag.id
LEFT JOIN flights f ON b.flight_id = f.id
LEFT JOIN passengers p ON b.id = p.booking_id
ORDER BY b.created_at DESC
```

---

## 🏢 DASHBOARD AGENCE

### **Pages disponibles**
1. **home.php** - Accueil avec formulaire de recherche rapide
2. **search_flight.php** - Recherche avancée de vols
3. **create_booking.php** - Création de réservation
4. **my_bookings.php** - Liste des réservations
5. **client_requests.php** - Demandes de vols des clients
6. **request_details.php** - Détails d'une demande client
7. **index.php** - Point d'entrée (routing)
8. **dashboard_data.php** - Collecte des données

### **Fonctionnalités**

#### **home.php**
```
Affichage :
- Message de bienvenue avec nom de l'agence
- Formulaire de recherche rapide (départ, arrivée, date)
- Statistiques de l'agence (via dashboard_data.php)
```

#### **dashboard_data.php**
```sql
-- Profil agence
SELECT id, agency_name, license_number, address, phone, warnings_count
FROM agencies
WHERE user_id = :user_id

-- Réservations de l'agence
SELECT 
    b.id, b.booking_number, b.status, b.created_at,
    CONCAT(p.firstname, ' ', p.lastname) as passenger_name,
    f.flight_number
FROM bookings b
JOIN passengers p ON b.booking_id = p.id
JOIN flights f ON b.flight_id = f.id
WHERE b.agency_id = :agency_id
ORDER BY b.created_at DESC
LIMIT 10
```

#### **search_flight.php**
```
Formulaire de recherche :
- Aéroport de départ
- Aéroport d'arrivée
- Date de départ
- Nombre de passagers

Résultats affichés :
- Numéro de vol
- Compagnie aérienne
- Horaires (départ → arrivée)
- Durée
- Prix par classe
- Disponibilités par classe (ECONOMY, BUSINESS, FIRST)
- Bouton "Réserver"
```

#### **create_booking.php**
```
Formulaire de réservation :
- ID du vol (pré-rempli)
- Classe choisie
- Informations passager :
  * Prénom
  * Nom
  * Email
  * Téléphone
  * Date de naissance

Processus :
1. Vérifier disponibilité siège
2. Créer booking
3. Créer passenger
4. Marquer siège comme BOOKED
5. Décrémenter availability dans fares
```

#### **my_bookings.php**
```
Liste des réservations :
- Numéro de réservation
- Passager
- Vol
- Statut (CONFIRMED, PENDING, CANCELLED)
- Date de réservation

Actions :
- Voir détails
- Annuler réservation (si CONFIRMED)
```

#### **client_requests.php**
```sql
SELECT 
    fr.id, fr.departure_city, fr.arrival_city, 
    fr.departure_date, fr.return_date,
    fr.number_of_passengers, fr.class_preference,
    fr.status, fr.created_at,
    CONCAT(u.firstname, ' ', u.lastname) as client_name,
    u.email as client_email
FROM flight_requests fr
JOIN users u ON fr.client_user_id = u.id
WHERE fr.agency_id = :agency_id
ORDER BY fr.created_at DESC
```

#### **request_details.php**
```
Affichage :
- Toutes les infos de la demande
- Coordonnées du client
- Budget
- Notes spéciales

Actions :
- Marquer comme EN_COURS
- Marquer comme COMPLETED
- Marquer comme CANCELLED
```

---

## ✈️ DASHBOARD COMPAGNIE AÉRIENNE

### **Pages disponibles**
1. **home.php** - Dashboard avec statistiques
2. **my_flights.php** - Liste des vols
3. **add_flight.php** - Ajout de vol
4. **edit_flight.php** - Modification de vol
5. **my_fleet.php** - Liste de la flotte
6. **add_aircraft.php** - Ajout d'appareil
7. **edit_aircraft.php** - Modification d'appareil
8. **fares.php** - Gestion des tarifs
9. **booking_reports.php** - Rapports de réservations
10. **index.php** - Point d'entrée (routing)
11. **dashboard_data.php** - Collecte des données

### **Fonctionnalités**

#### **dashboard_data.php**
```sql
-- Profil compagnie
SELECT id FROM airlines WHERE user_id = :user_id

-- Liste des vols
SELECT 
    f.id, f.flight_number, f.departure_airport, f.arrival_airport,
    f.departure_date, f.arrival_date, f.status,
    a.model as aircraft_model
FROM flights f
LEFT JOIN aircrafts a ON f.aircraft_id = a.id
WHERE f.airline_id = :airline_id
ORDER BY f.departure_date ASC

-- Flotte d'avions
SELECT id, model, total_seats, seats_per_class
FROM aircrafts
WHERE airline_id = :airline_id
ORDER BY model ASC

-- Statistiques de réservations
SELECT 
    DATE(b.created_at) as booking_date,
    COUNT(*) as booking_count
FROM bookings b
JOIN flights f ON b.flight_id = f.id
WHERE f.airline_id = :airline_id
  AND b.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(b.created_at)
ORDER BY booking_date ASC

-- Tarifs par vol
SELECT flight_id, class_type, price, availability
FROM fares
WHERE flight_id IN (SELECT id FROM flights WHERE airline_id = :airline_id)
```

#### **home.php**
```
Statistiques affichées :
- Nombre de vols actifs
- Taux de remplissage moyen
- Prochains départs (24h)
- Graphique des réservations (30 derniers jours)
```

#### **my_flights.php**
```
Liste des vols :
- Numéro de vol
- Route (départ → arrivée)
- Date et heure
- Appareil utilisé
- Statut (SCHEDULED, DELAYED, CANCELLED, COMPLETED)

Actions :
- Modifier vol
- Annuler vol
- Ajouter nouveau vol
```

#### **add_flight.php**
```
Formulaire :
- Numéro de vol
- Appareil (sélection depuis la flotte)
- Aéroport de départ
- Aéroport d'arrivée
- Date et heure de départ
- Date et heure d'arrivée
- Escales éventuelles

Processus :
1. Créer le vol
2. Créer automatiquement les sièges selon la config de l'avion
3. Créer les tarifs par défaut pour chaque classe
```

#### **edit_flight.php**
```
Modification :
- Horaires
- Statut (SCHEDULED, DELAYED, CANCELLED)
- Appareil assigné

Restrictions :
- Ne peut pas modifier un vol avec réservations confirmées
```

#### **my_fleet.php**
```
Liste des avions :
- Modèle
- Nombre total de sièges
- Répartition par classe :
  * First Class (sièges)
  * Business Class (sièges)
  * Economy Class (sièges)

Actions :
- Modifier avion
- Supprimer avion (si pas de vols actifs)
- Ajouter nouvel avion
```

#### **add_aircraft.php**
```
Formulaire :
- Modèle d'avion
- Nombre de sièges First Class
- Nombre de sièges Business Class
- Nombre de sièges Economy Class

Calcul automatique : Total des sièges
```

#### **edit_aircraft.php**
```
Modification :
- Configuration des sièges par classe

Restrictions :
- Ne peut pas modifier si vols actifs avec cet appareil
```

#### **fares.php**
```
Gestion des tarifs par vol et par classe :

Pour chaque vol :
- First Class : Prix, Disponibilité
- Business Class : Prix, Disponibilité
- Economy Class : Prix, Disponibilité

Actions :
- Mettre à jour les prix
- Ajuster les disponibilités
```

#### **booking_reports.php**
```sql
SELECT 
    b.booking_number, b.status,
    f.flight_number, f.departure_date,
    s.seat_number, s.class_type,
    CONCAT(p.firstname, ' ', p.lastname) as passenger_name
FROM bookings b
JOIN flights f ON b.flight_id = f.id
JOIN seats s ON b.seat_id = s.id
JOIN passengers p ON b.id = p.booking_id
WHERE f.airline_id = :airline_id
ORDER BY f.departure_date ASC, s.seat_number ASC
```

Affichage :
- Liste complète des passagers par vol
- Filtres par date, vol, statut
- Export possible (prévu)

---

## 🧳 DASHBOARD CLIENT

### **Pages disponibles**
1. **home.php** - Accueil avec prochains voyages
2. **my_trips.php** - Tous les voyages (passés et futurs)
3. **my_profile.php** - Profil personnel
4. **my_requests.php** - Demandes de vols auprès d'agences
5. **index.php** - Point d'entrée (routing)
6. **dashboard_data.php** - Collecte des données

### **Fonctionnalités**

#### **dashboard_data.php**
```sql
-- Profil utilisateur
SELECT firstname, lastname, email, contact
FROM users
WHERE id = :user_id

-- Prochains voyages
SELECT 
    f.flight_number, f.departure_airport, f.arrival_airport,
    f.departure_date, f.arrival_date,
    s.seat_number, s.class_type,
    b.status
FROM passengers p
JOIN bookings b ON p.booking_id = b.id
JOIN flights f ON b.flight_id = f.id
JOIN seats s ON b.seat_id = s.id
WHERE p.user_id = :user_id
  AND f.departure_date >= NOW()
  AND b.status != 'CANCELLED'
ORDER BY f.departure_date ASC

-- Voyages passés
SELECT ... (même requête avec f.departure_date < NOW())
```

#### **home.php**
```
Affichage :
- Message de bienvenue
- Prochains voyages (max 5)
  * Vol
  * Route
  * Date
  * Siège
  * Statut

Action :
- Voir tous mes voyages
```

#### **my_trips.php**
```
2 sections :
1. Voyages à venir
2. Historique des voyages

Pour chaque voyage :
- Numéro de vol
- Compagnie
- Route (départ → arrivée)
- Date et heure
- Siège et classe
- Statut de la réservation
```

#### **my_profile.php**
```
Affichage :
- Prénom
- Nom
- Email
- Téléphone

Action :
- Modifier ses informations (prévu)
```

#### **my_requests.php**
```sql
SELECT 
    fr.id, fr.departure_city, fr.arrival_city,
    fr.departure_date, fr.return_date,
    fr.number_of_passengers, fr.class_preference,
    fr.status, fr.created_at,
    a.agency_name
FROM flight_requests fr
LEFT JOIN agencies a ON fr.agency_id = a.id
WHERE fr.client_user_id = :user_id
ORDER BY fr.created_at DESC
```

Formulaire de nouvelle demande :
- Agence choisie
- Ville de départ
- Ville d'arrivée
- Date de départ
- Date de retour (optionnel)
- Nombre de passagers
- Classe préférée (ECONOMY, BUSINESS, FIRST)
- Budget approximatif
- Notes spéciales

---

## 🔧 CONTROLLERS (src/controllers/)

### **booking_process.php**
```php
Actions :
- create_booking
  1. Récupère agency_id depuis user_id
  2. Trouve un siège disponible (FOR UPDATE)
  3. Récupère le prix depuis fares
  4. Crée le booking
  5. Crée le passenger
  6. Update siège → BOOKED
  7. Décrémente availability dans fares
  8. Commit transaction

- cancel_booking (commenté)
```

### **flight_process.php**
```php
Actions :
- add_flight
  1. Vérifie que airline_id appartient à l'utilisateur
  2. Vérifie que aircraft existe
  3. Crée le vol
  4. Crée automatiquement les sièges selon aircraft.seats_per_class
  5. Crée les fares par défaut pour chaque classe

- edit_flight
  1. Update horaires, statut, etc.

- cancel_flight
  1. Update status → CANCELLED
```

### **fleet_process.php**
```php
Actions :
- add_aircraft
  1. Crée l'avion avec config sièges

- edit_aircraft
  1. Update configuration sièges
```

### **fare_process.php**
```php
Actions :
- update_fares
  1. Update prix et disponibilités par classe pour un vol
```

### **flight_request_process.php**
```php
Actions :
- submit_request
  1. Crée la demande de vol
  2. Envoie un email à l'agence choisie
```

---

## 📐 STRUCTURE MVC

### **Point d'entrée (index.php)**
```php
1. Vérifie authentification (isAdmin(), isAgency(), etc.)
2. Charge dashboard_data.php
3. Détermine la page via $_GET['page']
4. Inclut sidebar.php et body.php
```

### **Sidebar (src/views/partials/sidebar.php)**
```php
Navigation dynamique selon user_type :
- ADMIN : home, users, airlines, agencies, flights, bookings
- AIRLINE : home, my_flights, my_fleet, fares, booking_reports
- AGENCY : home, search_flight, client_requests, my_bookings
- CLIENT : home, my_trips, my_profile, my_requests
```

### **Body (src/views/partials/body.php)**
```php
Inclusion dynamique de la page selon $_GET['page']
Sécurité : Vérifie que la page est dans $allowed_pages
```

---

## 📊 WORKFLOW COMPLET

### **Inscription et Activation**

1. **Client s'inscrit** (public/inscription.php)
   - Créé dans `users` avec status = ACTIVE
   - Connecté automatiquement

2. **Agence demande partenariat** (public/demande-agence.php)
   - Enregistré dans `agency_requests` avec status = PENDING
   - Admin voit la demande
   - Admin approuve → Créé dans `users` (AGENCY) + `agencies`
   - Agence reçoit identifiants par email

3. **Compagnie demande partenariat** (public/demande-compagnie.php)
   - Même processus que agence
   - Créé dans `airlines`

### **Workflow de Réservation**

#### **Via Agence (Recommandé)**
1. Client contacte agence (hors plateforme ou via flight_request)
2. Agence se connecte
3. Agence cherche un vol (search_flight.php)
4. Agence crée réservation (create_booking.php)
5. booking_process.php traite :
   - Trouve siège disponible
   - Crée booking et passenger
   - Marque siège BOOKED
6. Agence donne billet au client

#### **Via Demande de Vol (Client → Agence)**
1. Client se connecte
2. Client crée une demande (my_requests.php)
3. Agence reçoit la demande (client_requests.php)
4. Agence traite la demande
5. Agence contacte le client (hors plateforme)
6. Suite du processus normal

### **Gestion des Vols (Compagnie)**

1. Compagnie ajoute un avion (add_aircraft.php)
2. Compagnie crée un vol (add_flight.php)
   - Sièges créés automatiquement
   - Tarifs initialisés
3. Compagnie ajuste les tarifs (fares.php)
4. Vol visible dans recherche des agences
5. Réservations arrivent
6. Compagnie consulte les rapports (booking_reports.php)

### **Administration**

1. Admin voit tableau de bord (statistiques)
2. Admin valide les demandes d'agences/compagnies
3. Admin peut suspendre un utilisateur
4. Admin voit tous les vols et réservations

---

## 🗄️ TABLES UTILISÉES (Structure Anglaise Originale)

```sql
users (
    id, firstname, lastname, email, contact, password, 
    user_type ENUM('CLIENT','AGENCY','AIRLINE','ADMIN'),
    current_status ENUM('ACTIVE','INACTIVE','SUSPENDED'),
    created_at, last_login
)

agencies (
    id, user_id, agency_name, license_number, address, 
    phone, warnings_count, current_status, created_at
)

airlines (
    id, user_id, company_name, iata_code, country, 
    description, logo_url, created_at
)

aircrafts (
    id, airline_id, model, total_seats, seats_per_class (JSON),
    created_at
)

flights (
    id, airline_id, aircraft_id, flight_number,
    departure_airport, arrival_airport,
    departure_date, arrival_date,
    status ENUM('SCHEDULED','DELAYED','CANCELLED','COMPLETED'),
    created_at
)

seats (
    id, flight_id, seat_number, class_type ENUM('ECONOMY','BUSINESS','FIRST'),
    status ENUM('AVAILABLE','BOOKED','BLOCKED')
)

fares (
    id, flight_id, class_type, price, availability
)

bookings (
    id, booking_number, agency_id, flight_id, seat_id,
    status ENUM('PENDING','CONFIRMED','CANCELLED'),
    total_amount, created_at
)

passengers (
    id, booking_id, user_id, firstname, lastname,
    email, phone, date_of_birth
)

flight_requests (
    id, client_user_id, agency_id,
    departure_city, arrival_city,
    departure_date, return_date,
    number_of_passengers, class_preference,
    budget, special_notes,
    status ENUM('PENDING','IN_PROGRESS','COMPLETED','CANCELLED'),
    created_at
)
```

---

## 🎨 STYLE CSS (Manquant)

Le fichier `public/css/dashboard.css` était référencé mais **n'existait pas**.

Tous les dashboards n'avaient donc **aucun style appliqué**.

---

## 📝 NOTES IMPORTANTES

### **Points forts de l'architecture**
- Structure MVC partielle bien organisée
- Séparation des rôles claire
- Workflow logique de réservation
- Gestion des sièges en temps réel (FOR UPDATE)
- Transactions pour éviter double-booking

### **Points faibles identifiés**
- **Conflit de langue BD/Code** (français vs anglais)
- **Conflit de structure session** ($_SESSION['user'] vs $_SESSION['user_type'])
- **Pas de CSS** pour les dashboards
- **Fichiers obsolètes** (Auth.php, validation.php)
- **Credentials en dur** dans sendEmail.php
- **Fonctionnalités incomplètes** (cancel_booking commenté)

### **Fonctionnalités non implémentées**
- Édition du profil utilisateur
- Impression de billets (PDF)
- Export de rapports
- Notifications email automatiques
- Système de paiement
- Upload de logo compagnie
- Système de reviews/notes
- Historique des modifications

---

**Date de documentation** : 17 octobre 2025  
**Raison** : Refonte complète avec style de landing page unifié et structure française
