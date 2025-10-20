# Modifications du Schéma de Base de Données

## Date : 17 Octobre 2025

Ce document liste tous les ajustements effectués sur le schéma de base de données pour assurer la cohérence avec les formulaires de la landing page.

---

## ✅ Modifications Effectuées

### 1. Table `users`

**Ajouts :**
- `newsletter_subscription BOOLEAN DEFAULT FALSE` : Pour stocker la préférence newsletter du formulaire d'inscription

**Modifications :**
- `user_type` : Valeur par défaut changée de `'AGENCY'` à `'CLIENT'` (car l'inscription landing est pour les clients)

**Justification :** Le formulaire `inscription.php` demande si l'utilisateur veut s'abonner à la newsletter.

---

### 2. Table `airlines`

**Ajouts :**
- `fleet_size INT DEFAULT NULL` : Taille de la flotte (nombre d'avions)

**Justification :** Le formulaire `demande-compagnie.php` demande cette information.

---

### 3. Table `agencies`

**Ajouts :**
- `country VARCHAR(100) DEFAULT 'France'` : Pays de l'agence
- `employees_count INT DEFAULT NULL` : Nombre d'employés

**Justification :** Le formulaire `demande-agence.php` demande ces deux informations.

---

### 4. Nouvelle Table : `agency_requests`

**Création complète** pour gérer les demandes de partenariat des agences.

```sql
CREATE TABLE agency_requests (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    agency_name VARCHAR(200) NOT NULL,
    license_number VARCHAR(50) NOT NULL,
    country VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    contact_name VARCHAR(200) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    employees_count INT NULL,
    message TEXT NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    reviewed_by BIGINT NULL,
    reviewed_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    ...
);
```

**Justification :**
- Le formulaire `demande-agence.php` crée une **demande de partenariat** (pas une agence validée).
- Les demandes doivent être en attente d'approbation admin.
- Une fois approuvées, un admin crée l'agence dans la table `agencies`.

**Workflow :**
1. Agence remplit le formulaire → Insert dans `agency_requests` (status = PENDING)
2. Admin examine la demande → Modifie le status (APPROVED/REJECTED)
3. Si APPROVED → Admin crée un compte dans `users` + `agencies`

---

### 5. Nouvelle Table : `airline_requests`

**Création complète** pour gérer les demandes de partenariat des compagnies aériennes.

```sql
CREATE TABLE airline_requests (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(200) NOT NULL,
    iata_code VARCHAR(3) NOT NULL,
    country VARCHAR(100) NOT NULL,
    contact_name VARCHAR(200) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    fleet_size INT NULL,
    message TEXT NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    reviewed_by BIGINT NULL,
    reviewed_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    ...
);
```

**Justification :**
- Même logique que `agency_requests`.
- Le formulaire `demande-compagnie.php` crée une demande de partenariat.

**Workflow :**
1. Compagnie remplit le formulaire → Insert dans `airline_requests` (status = PENDING)
2. Admin examine → Modifie status (APPROVED/REJECTED)
3. Si APPROVED → Admin crée un compte dans `users` + `airlines`

---

### 6. Nouvelle Table : `contact_messages`

**Création complète** pour gérer les messages de contact depuis la landing page.

```sql
CREATE TABLE contact_messages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    subject ENUM('demo', 'information', 'partnership', 'support', 'other') NOT NULL,
    message TEXT NOT NULL,
    status ENUM('NEW', 'READ', 'REPLIED', 'CLOSED') DEFAULT 'NEW',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    replied_by BIGINT NULL,
    replied_at TIMESTAMP NULL,
    reply_message TEXT NULL,
    ...
);
```

**Justification :**
- Le formulaire `contact.php` permet d'envoyer des messages.
- Aucune table n'existait pour stocker ces messages.

**Workflow :**
1. Visiteur remplit le formulaire contact → Insert dans `contact_messages` (status = NEW)
2. Admin voit le message dans son dashboard → Status passe à READ
3. Admin répond → Status passe à REPLIED, `reply_message` rempli
4. Admin clôture → Status passe à CLOSED

---

## 📋 Correspondance Formulaires ↔ Tables

| Formulaire Landing | Table BD | État |
|---|---|---|
| **connexion.php** | `users` | ✅ Compatible |
| **inscription.php** | `users` | ✅ Ajusté (newsletter_subscription) |
| **demande-agence.php** | `agency_requests` | ✅ Nouvelle table créée |
| **demande-compagnie.php** | `airline_requests` | ✅ Nouvelle table créée |
| **contact.php** | `contact_messages` | ✅ Nouvelle table créée |
| **vols.php** | `flights`, `fares`, `seats` | ✅ Déjà conforme |

---

## 🎯 Tables Finales du Schéma

### Tables Utilisateurs & Profils
- `users` ✅ (modifié)
- `agencies` ✅ (modifié)
- `airlines` ✅ (modifié)
- `admin_profiles` ✅

### Tables Demandes/Requêtes
- `agency_requests` ✅ (nouveau)
- `airline_requests` ✅ (nouveau)
- `contact_messages` ✅ (nouveau)
- `flight_requests` ✅

### Tables Vols & Réservations
- `aircrafts` ✅
- `flights` ✅
- `fares` ✅
- `seats` ✅
- `bookings` ✅
- `passengers` ✅
- `tickets` ✅

### Tables Historiques
- `booking_status_histories` ✅
- `admin_status_histories` ✅

---

## 🚀 Prochaines Étapes

### Backend PHP à développer :
1. **Script `process-inscription.php`** → Insert dans `users`
2. **Script `process-demande-agence.php`** → Insert dans `agency_requests`
3. **Script `process-demande-compagnie.php`** → Insert dans `airline_requests`
4. **Script `process-contact.php`** → Insert dans `contact_messages`
5. **Script `process-connexion.php`** → Authentification depuis `users`

### Dashboard Admin :
- Interface pour gérer `agency_requests` (approuver/rejeter)
- Interface pour gérer `airline_requests` (approuver/rejeter)
- Interface pour gérer `contact_messages` (lire/répondre)

---

## ⚠️ Points d'Attention

1. **Type d'utilisateur lors de l'inscription** : Le formulaire `inscription.php` ne demande pas le type (CLIENT/AGENCY/AIRLINE). Par défaut, les inscriptions landing sont pour les CLIENTS.

2. **Validation des demandes** : Les formulaires `demande-agence.php` et `demande-compagnie.php` doivent clairement indiquer que c'est une **demande** en attente de validation, pas une création de compte immédiate.

3. **Sécurité** :
   - Hacher les mots de passe avec `password_hash()` avant insertion
   - Valider tous les emails avec `filter_var()`
   - Protection CSRF sur tous les formulaires

---

**Version** : 1.0
**Dernière mise à jour** : 17 octobre 2025
