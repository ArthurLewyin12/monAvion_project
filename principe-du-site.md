### PRINCIPE DU FONCTIONNEMENT DE LA PLATEFORME

Je vais te décrire le fonctionnement concret, les rôles de chaque acteur, le flux de réservation complet, et la logique de gestion dans ton système.

### 🧩 1. Les acteurs et leurs rôles

| Acteur                 | Rôle dans la plateforme                                                                                                             |
| :--------------------- | :---------------------------------------------------------------------------------------------------------------------------------- |
| **Client**             | Souhaite acheter un billet d’avion. Il s’adresse à une agence pour effectuer la réservation.                                        |
| **Agence de voyage**   | Intermédiaire entre le client et la compagnie aérienne. Elle recherche les vols, effectue la réservation et perçoit une commission. |
| **Compagnie aérienne** | Propose ses vols sur la plateforme via son tableau de bord. Elle valide les réservations et transporte les passagers.               |
| **Plateforme**         | Système central qui connecte tous les acteurs, gère les données, les paiements et les statuts de réservation.                       |

### 🚀 2. Déroulement concret d’une réservation via une agence

#### 🧱 Étape 1 — Configuration initiale

- Les compagnies aériennes s’inscrivent sur la plateforme.
- Elles créent leurs vols depuis leur tableau de bord (avec les informations : départ, arrivée, date, heure, prix, nombre de sièges, etc.).
- Les agences s’inscrivent également.
- Elles accèdent à un tableau de bord agence qui leur permet de :
  - consulter tous les vols publiés par les compagnies inscrites ; <span style="color:green;">IMPLÉMENTÉ</span>
  - effectuer des réservations pour leurs clients ; <span style="color:green;">IMPLÉMENTÉ</span>
  - suivre leurs ventes et leurs commissions.

#### 🧭 Étape 2 — Demande et Recherche de vol (Flux en ligne, voir `@feature-client-flight-request`)

- Le **client**, depuis son tableau de bord, accède à une page unique **"Faire une demande de vol"**.
- Il remplit un formulaire avec ses critères (destination, dates, etc.) et **choisit l'agence de destination** dans une liste déroulante.
- La plateforme enregistre cette demande dans la table `flight_requests`, **envoie un e-mail de confirmation au client et une notification à l'agence**, et la rend visible dans le tableau de bord de l'agence choisie.
- L'**agent** se connecte à son espace et voit la demande apparaître dans son tableau de bord.
- Il consulte la demande et utilise l'outil de recherche de la plateforme (pré-rempli avec les critères du client) pour trouver les vols correspondants.
- La plateforme interroge sa base interne et retourne les vols disponibles fournis par toutes les compagnies aériennes.
- L'agent contacte ensuite le client pour lui présenter les options trouvées.

#### 🧾 Étape 3 — Réservation du vol

- Le client choisit un vol parmi les propositions.
- L’agent confirme la réservation depuis son tableau de bord :
  - La plateforme crée une réservation dans sa base de données.
  - Elle enregistre : `client_id`, `agence_id`, `compagnie_id`, `vol_id`, `statut = "en attente de paiement"`.
  - Le nombre de places disponibles sur le vol est réduit automatiquement (ex : -1).

> 💡 Ici, la plateforme agit comme un mini système de distribution (GDS interne) : elle gère le lien entre client, agence et compagnie, et bloque la place temporairement.

#### 💳 Étape 4 — Paiement

- Le client paie directement à l’agence (en espèces, carte, mobile money, etc.).
- L’agence valide le paiement sur la plateforme :
  - Le statut de la réservation passe à `confirmée`.
  - La plateforme notifie la compagnie aérienne que la réservation est payée.
- Selon ton modèle économique :
  - soit la plateforme prélève une commission automatique sur la transaction ;
  - soit la compagnie verse plus tard une commission à l’agence (gérée en interne).

#### 🎫 Étape 5 — Émission du billet

- Une fois la réservation confirmée :
  - la plateforme génère un e-billet (PDF ou numérique) avec un numéro de billet unique, les informations du vol et un QR code.
  - le client reçoit le billet par e-mail ou via l’agence.
- La compagnie aérienne voit la réservation validée dans son tableau de bord (avec le nom du passager et le numéro de billet).
- L’agence conserve une trace de la transaction et la commission associée.

#### ✈️ Étape 6 — Après la réservation

- Le client peut utiliser le billet pour l’enregistrement le jour du vol.
- En cas de modification ou d’annulation, l’agence est responsable d’effectuer la demande auprès de la compagnie, toujours via la plateforme.

### 🔗 3. Relations entre les acteurs

| Relation                        | Description concrète                                                                                                |
| :------------------------------ | :------------------------------------------------------------------------------------------------------------------ |
| **Client ↔ Agence**             | Le client dépend de l’agence pour trouver, réserver et payer son billet.                                            |
| **Agence ↔ Compagnie aérienne** | L’agence vend les vols publiés par la compagnie. La compagnie reconnaît la réservation effectuée via la plateforme. |
| **Plateforme ↔ Tous**           | Fournit l’infrastructure technique, gère la logique métier, le suivi des paiements et la génération des billets.    |

### 🧠 4. Structure de base de données (voir `@db.sql`)

### ⚙️ 5. Schéma du flux de réservation (cas agence)

```mermaid
sequenceDiagram
    participant Client
    participant Agence
    participant Plateforme
    participant Compagnie

    Client->>Plateforme: Remplit le formulaire de demande de vol
    Plateforme-->>Client: E-mail de confirmation
    Plateforme->>Agence: Notifie (e-mail + dashboard)
    
    Agence->>Plateforme: Recherche vols disponibles (pré-rempli)
    Plateforme->>Compagnie: (interne) Récupère les vols
    Plateforme-->>Agence: Retourne les résultats
    Agence-->>Client: Propose les options (hors plateforme)

    Client->>Agence: Choisit un vol
    Agence->>Plateforme: Crée la réservation
    Plateforme->>Compagnie: Réduit le nombre de sièges
    Plateforme-->>Agence: Réservation en attente de paiement

    Client->>Agence: Effectue le paiement
    Agence->>Plateforme: Valide le paiement
    Plateforme->>Compagnie: Confirme la réservation
    Plateforme-->>Client: Génère et envoie le e-billet
```

### 💼 6. Points essentiels à gérer dans ton code

| Domaine                       | À implémenter                                                                        |
| :---------------------------- | :----------------------------------------------------------------------------------- |
| **Authentification par rôle** | Tableau de bord séparé : client / agence / compagnie.                                |
| **Gestion des vols**          | CRUD complet côté compagnie.                                                         |
| **Réservation**               | Création automatique du lien client–agence–compagnie.                                |
| **Disponibilité**             | Décrémentation atomique des sièges lors d’une réservation.                           |
| **Paiement**                  | Validation manuelle par l’agence ou automatique via intégration (Stripe, CinetPay…). |
| **Commission**                | Calcul automatique à chaque billet confirmé.                                         |
| **Billet**                    | Génération PDF + QR code.                                                            |
| **Notifications**             | E-mails ou SMS à chaque étape (réservation, paiement, confirmation).                 |

### 🧾 7. En résumé

| Étape | Action                                     | Acteur principal    |
| :---- | :----------------------------------------- | :------------------ |
| 1     | Le client contacte une agence              | Client              |
| 2     | L’agence cherche et propose un vol         | Agence              |
| 3     | L’agence réserve le vol choisi             | Agence + Plateforme |
| 4     | Le client paie via l’agence                | Client + Agence     |
| 5     | Le billet est généré                       | Plateforme          |
| 6     | La compagnie voit la réservation confirmée | Compagnie           |