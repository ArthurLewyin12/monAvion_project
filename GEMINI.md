# Gemini Project Summary

This file summarizes the actions taken by the Gemini agent.

## Project Setup

- The project structure has been created according to `tree.md`.
- The following directories have been created: `public`, `src`, `src/controllers`, `src/models`, `src/views`, `config`.
- The following files have been created:
  - `public/index.php`
  - `public/.htaccess`
  - `config/db.php`
  - `public/inscription.php`
  - `public/client`
  - `public/agency`
  - `public/airline`
  - `public/request`
  - `public/connexion.php`
  - `src/controllers/airline_registration_process.php`
  - `src/controllers/agency_registration_process.php`

## Authentication

- `Auth.php` has been moved to `src/controllers/Auth.php`.
- `db.sample.php` has been moved to `config/db.php`.
- Client registration and login have been implemented in `public/inscription.php` and `public/connexion.php`.
- Placeholder files for airline and agency registration have been created.
- Voici comment j'enregistre mes sessions:

```php
$_SESSION["user"] // sachant que user est un tableau, tu peux check @db.sql pour avoir le nom des colonnes
```

## Agency and Airline Registration

- Implemented the registration process for agencies and airlines.
- Created `agences/inscription.php` and `compagnies/inscription.php` with the necessary forms.
- Created `src/controllers/agency_registration_process.php` and `src/controllers/airline_registration_process.php` to handle form submissions.
- New users are created with an 'INACTIVE' status and a temporary password.
- Added `validate_agency_inscription_data` and `validate_airline_inscription_data` to `src/functions/validation.php`.
- Implemented an admin dashboard to view all users and a user details page to activate or suspend users.

## Data Implementation & Database

- The `db.sql` file contains the full database schema. This file should be considered the source of truth for all data structures and relationships when implementing features that interact with the database.
- The admin dashboard has been enhanced with data visualizations as specified in `dashboard-data-proposals.md`. The queries have been verified against `db.sql`.

## Dashboards

- Implemented dashboards for all user roles: `ADMIN`, `AGENCY`, `AIRLINE`, and `CLIENT`.
- Each dashboard is built with a reusable sidebar and body structure.
- The data displayed on each dashboard is dynamically loaded based on the user's role and is aligned with the proposals in `dashboard-data-proposals.md`.

### Architecture des Dashboards

Pour standardiser et sécuriser les tableaux de bord, une architecture unifiée a été mise en place :

1.  **Point d'Entrée Unique** : Chaque rôle (`ADMIN`, `AGENCY`, etc.) possède un unique point d'entrée : `index.php` (par exemple, `/public/airline/index.php`). C'est le seul fichier à appeler directement.

2.  **Authentification Centralisée** : Ce fichier `index.php` est responsable de la vérification de la session et du rôle de l'utilisateur. Si l'utilisateur n'est pas authentifié ou n'a pas le bon rôle, il est redirigé vers la page de connexion. Aucune autre page du dashboard ne doit répéter cette vérification.

3.  **Chargement des Données** : Le fichier `index.php` inclut ensuite le script `dashboard_data.php` correspondant pour charger toutes les données nécessaires pour ce rôle.

4.  **Structure Modulaire** : Le `index.php` inclut ensuite les deux parties principales de la vue :

    - `src/views/partials/sidebar.php` : La barre de navigation latérale. Elle génère les liens de la forme `index.php?page=nom_de_la_page`.
    - `src/views/partials/body.php` : Le conteneur principal qui, en fonction du paramètre d'URL `?page=...`, inclut le fichier de contenu correspondant (ex: `my_flights.php`, `fares.php`).

5.  **Pages de Contenu** : Les fichiers comme `my_flights.php`, `add_aircraft.php`, etc., ne contiennent que le code HTML et PHP de leur contenu spécifique. Ils n'ont plus de balises `<html>`, `<head>`, `<body>` ni de vérification de session.

Cette approche garantit que la sécurité est gérée en un seul point et que les pages sont légères et réutilisables.

## Email Notifications

- Implemented email notifications for the following events:
  - **Agency/Airline Registration:** When an agency or airline submits their registration, they receive a confirmation email.
  - **User Activation:** When an administrator activates a user's account, the user receives an email containing their login credentials (email and a temporary password).
  - `@src/functions/sendEmail.php` for send emails

## STACK BACKEND

concernant les outils que j'utilise jai `@src/functions/sendEmail.php` de `@src/PHPMailer-master` pour l'envoi d'email, sinon j'utilise à 90% php pour la logique backend, pour le html, pose des bases simple pour tester le backend

## Next Steps

- Secure the application and add more features as needed.
