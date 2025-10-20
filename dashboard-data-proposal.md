# Propositions de Données pour les Tableaux de Bord

Basé sur l'analyse des fichiers `Agences-voyages.md`, `description.md`, et `db.sql`, voici des suggestions de données et fonctionnalités à afficher sur les différents tableaux de bord.

## 1. Tableau de Bord Administrateur (`ADMIN`)

L'admin a une vue globale et de contrôle sur toute la plateforme.

**Page d'accueil (Dashboard Principal):**

- **Statistiques Clés:**
  - Nombre total d'utilisateurs (ventilé par rôle: Agences, Compagnies, Clients).
  - Nombre de vols programmés pour les 7 prochains jours.
  - Nombre de réservations des dernières 24 heures.
  - Dernières inscriptions (agences et compagnies) en attente de validation.
- **Graphiques:**
  - Évolution des inscriptions sur les 30, 90 et 180 derniers jours.
  - Répartition des réservations par compagnie aérienne.

**Page `Utilisateurs`:** <A PRIORIE DEJA IMPLEMENTE>

- Tableau de tous les utilisateurs (`users`).
- Colonnes: `Nom`, `Prénom`, `Email`, `Rôle`, `Statut` (`ACTIVE`, `INACTIVE`, `SUSPENDED`), `Date de création`.
- Actions:
  - **Activer/Suspendre** un compte utilisateur.
  - Voir les détails de l'utilisateur (mène à `user_details.php`).
  - Filtrer par rôle et statut.

**Page `Compagnies`:**

- Tableau des compagnies aériennes (`airlines`).
- Colonnes: `Nom de la compagnie`, `Code IATA`, `Pays`, `Nombre de vols`, `Statut`.
- Actions:
  - Approuver les nouvelles inscriptions.
  - Voir le profil complet de la compagnie (avions, vols, etc.).

**Page `Agences`:**

- Tableau des agences de voyages (`agencies`).
- Colonnes: `Nom de l'agence`, `N° de licence`, `Nombre d'avertissements`, `Statut`.
- Actions:
  - Approuver les nouvelles inscriptions.
  - Gérer les avertissements (`warnings_count`).

**Page `Vols`:**

- Vue globale de tous les vols (`flights`).
- Recherche et filtres par compagnie, aéroport de départ/arrivée, date.
- Statut des vols (`SCHEDULED`, `DELAYED`, `CANCELLED`).

**Page `Réservations`:**

- Vue de toutes les réservations (`bookings`).
- Recherche par numéro de réservation, agence, ou passager.

## 2. Tableau de Bord Compagnie Aérienne (`AIRLINE`)

La compagnie gère ses propres ressources.

**Page d'accueil:**

- **Statistiques:**
  - Nombre de vols actifs.
  - Taux de remplissage moyen de ses avions.
  - Prochains départs (24h).
- **Graphique:**
  - Nombre de sièges réservés par jour pour la semaine à venir.

**Page `Mes Vols`:** <span style="color:green;">IMPLÉMENTÉ (Actions Backend)</span>

- Tableau de tous ses vols (`flights`).
- Actions:
  - **Ajouter/Modifier/Annuler** un vol.
  - Mettre à jour le statut d'un vol (`DELAYED`, `CANCELLED`).

**Page `Ma Flotte`:** <span style="color:green;">IMPLÉMENTÉ (Actions Backend)</span>

- Tableau de ses avions (`aircrafts`).
- Colonnes: `Modèle`, `Nombre de sièges total`, `Répartition par classe`.
- Actions:
  - **Ajouter/Modifier** un avion.

**Page `Tarifs`:** <span style="color:green;">IMPLÉMENTÉ</span>

- Interface pour gérer les tarifs (`fares`) par vol et par classe.
- Actions:
  - Définir/Mettre à jour les prix.

**Page `Rapports de Réservation`:** <span style="color:green;">IMPLÉMENTÉ (Vue)</span>

- Voir toutes les réservations pour ses vols.
- Exporter des listes de passagers par vol.

## 3. Tableau de Bord Agence de Voyage (`AGENCY`)

L'agence est l'utilisateur principal pour la réservation.

**Page d'accueil (Moteur de Recherche):**

- Le formulaire de **recherche de vols** (départ, arrivée, date) est central.
- Affichage des promotions ou des vols populaires.

**Page `Mes Réservations`:** <span style="color:green;">IMPLÉMENTÉ (Annulation)</span>

- Tableau de toutes les réservations (`bookings`) effectuées par l'agence.
- Colonnes: `N° de réservation`, `Passager`, `Vol`, `Statut` (`CONFIRMED`, `CANCELLED`), `Date`.
- Actions:

  - **Annuler** une réservation.
  - **Imprimer** le billet (`tickets`). <span style="color:orange;">(À FAIRE)</span>
    **Page `Recherche de Vols`:**

- Le moteur de recherche principal, menant à une page de résultats où l'agence peut visualiser les sièges et réserver.

**Page `Mon Compte`:**

- Voir les informations de l'agence.
- Consulter le nombre d'avertissements.

## 4. Tableau de Bord Client (`CLIENT`)

Le client a une vue simplifiée de ses propres voyages.

**Page `Mes Prochains Voyages`:**

- Liste des réservations à venir associées à son compte (`bookings` via `passengers.user_id`).
- Détails du vol et du siège.

**Page `Historique de Voyages`:**

- Liste de tous les voyages passés.

**Page `Mon Profil`:**

- Mettre à jour ses informations personnelles (`users`).
