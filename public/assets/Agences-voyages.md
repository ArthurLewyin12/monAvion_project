# Logique de fonctionnement pour la plateforme de réservation

Ce document décrit la logique de fonctionnement des agences de voyages telle qu'elle sera implémentée sur cette plateforme. La plateforme agit comme un intermédiaire technique (un agrégateur) entre les compagnies aériennes et les agences de voyages.

## Le Rôle de la Plateforme

La plateforme centralise les offres de vols de plusieurs compagnies pour les rendre accessibles aux agences de voyages via une interface unique. Elle remplace le besoin pour une agence de se connecter à de multiples systèmes.

## Le Flux de Réservation

Le processus se déroule en 4 étapes principales :

### 1. Alimentation des données par les Compagnies Aériennes

*   **Quoi ?** Les compagnies aériennes fournissent à la plateforme toutes leurs informations :
    *   La liste de leurs vols (itinéraires, horaires).
    *   Les caractéristiques de leurs avions (nombre de sièges par classe).
    *   Les tarifs pour chaque vol et chaque classe.
*   **Comment ?** Via un "Espace Compagnie Aérienne" dédié sur la plateforme.

### 2. Recherche par l'Agence de Voyage

*   **Quoi ?** L'agence de voyage, pour le compte d'un client, recherche un vol.
*   **Comment ?** Elle utilise le moteur de recherche de la plateforme en spécifiant des critères (par exemple : départ, arrivée, date). Le système interroge sa base de données centralisée et affiche tous les résultats pertinents de toutes les compagnies.

### 3. Processus de Réservation

*   **Quoi ?** L'agence sélectionne un vol parmi les résultats, puis choisit un siège spécifique pour son client.
*   **Comment ?**
    1.  La plateforme affiche une carte visuelle de l'avion avec les sièges disponibles.
    2.  L'agence clique sur un siège et remplit les informations du passager.
    3.  En cliquant sur "Réserver", la plateforme effectue deux actions critiques :
        *   **Mise à jour de l'inventaire :** Le siège est instantanément marqué comme "indisponible" pour éviter les doubles réservations.
        *   **Création du dossier :** Un dossier de réservation est créé avec un numéro de référence unique.

### 4. Génération du Billet

*   **Quoi ?** Un billet électronique est généré.
*   **Comment ?** La plateforme utilise les informations du dossier de réservation pour remplir un modèle de billet prédéfini. L'agence peut alors le remettre au client.

## En résumé

Dans ce modèle, l'agence de voyage n'est pas seulement un "vendeur". Elle est l'**utilisateur principal** d'un outil (la plateforme) qui lui donne le pouvoir de comparer, de choisir et de réserver la meilleure offre pour son client parmi un large éventail de fournisseurs.
