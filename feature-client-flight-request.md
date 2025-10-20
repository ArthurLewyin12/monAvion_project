# Plan d'Implémentation : Demande de Vol par le Client (Approche Hybride)

Ce document détaille les étapes nécessaires pour implémenter la fonctionnalité permettant à un client de soumettre une demande de vol personnalisée à une agence via la plateforme.

## Concept Général (Mis à jour)

Le client, depuis son tableau de bord, accède à une **page unique de demande de vol**. Sur cette page, il remplit un formulaire unique et intelligent avec ses critères de voyage (destination, dates, classe, etc.). Une partie cruciale de ce formulaire est une liste déroulante lui permettant de **choisir directement l'agence** à qui il souhaite adresser sa demande.

Une fois le formulaire soumis, une nouvelle entrée est créée dans la table `flight_requests`, associant la demande au client et à l'agence choisie. L'agence reçoit alors cette demande sur son propre tableau de bord pour la traiter.

---

### Étape 1 : Modification de la Base de Données

La première étape consiste à ajouter une nouvelle table à la base de données pour stocker les demandes.

**Nom de la table :** `flight_requests`

**Colonnes proposées :**

- `id` : BIGINT, AUTO_INCREMENT, PRIMARY KEY
- `client_user_id` : BIGINT, FOREIGN KEY vers `users(id)`
- `agency_id` : BIGINT, FOREIGN KEY vers `agencies(id)`
- `departure_airport` : VARCHAR(10)
- `arrival_airport` : VARCHAR(10)
- `departure_date` : DATE
- `return_date` : DATE (NULLable)
- `num_passengers` : INT
- `desired_class` : ENUM('ECONOMY', 'BUSINESS', 'FIRST')
- `additional_notes` : TEXT
- `status` : ENUM('NEW', 'VIEWED', 'PROCESSED', 'CLOSED') DEFAULT 'NEW'
- `created_at` : TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`: TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

---

### Étape 2 : Implémentation Côté Client (Dashboard CLIENT) (Mis à jour)

1.  **Page de Demande de Vol :**

    - Créer une nouvelle page `public/create_request.php`.
    - Cette page contiendra un formulaire unique permettant au client de saisir tous les détails de son voyage.
    - Le formulaire inclura une **liste déroulante (`<select>`) des agences actives** pour que le client puisse choisir le destinataire de sa demande.
    - La soumission du formulaire enverra les données au script `src/controllers/flight_request_process.php` qui créera l'entrée dans la table `flight_requests`.

2.  **Suivi des Demandes :**
    - Créer une page `public/client/my_requests.php`.
    - Cette page affichera un historique de toutes les demandes envoyées par le client, avec leur statut actuel (`NEW`, `VIEWED`, `PROCESSED`, etc.), en lisant les informations depuis la table `flight_requests`.

---

### Étape 3 : Implémentation Côté Agence (Dashboard AGENCY)

Le tableau de bord agence sera mis à jour pour gérer le flux entrant de demandes :

1.  **Listing des Demandes Clients :**

    - Créer une nouvelle section dans le menu et une page `public/agency/client_requests.php`.
    - Cette page listera toutes les demandes reçues par l'agence (`WHERE agency_id = ...`), en mettant en évidence les nouvelles (`status = 'NEW'`).

2.  **Détail et Traitement de la Demande :**

    - L'agent pourra cliquer sur une demande pour en voir les détails complets.
    - Sur la page de détail, un bouton "Chercher les vols correspondants" sera présent.

3.  **Intégration avec la Recherche de Vols :**
    - Cliquer sur ce bouton redirigera l'agent vers la page `search_flight.php`.
    - Les champs du formulaire de recherche (départ, arrivée, date) seront **automatiquement pré-remplis** avec les informations de la demande du client, optimisant ainsi le temps de travail de l'agent.
