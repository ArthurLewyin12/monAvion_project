# 📝 NOTES IMPORTANTES - Points à implémenter

## 🔐 CRITIQUE : Gestion des mots de passe AGENCE/COMPAGNIE

### Problème identifié

Les agences et compagnies font une demande via formulaire public SANS mot de passe.
Comment gèrent-ils leur connexion après validation par l'admin ?

### Solution recommandée

#### Workflow complet :

1. **Demande soumise** (agence/compagnie)

   - Formulaire public avec infos entreprise
   - PAS de mot de passe demandé
   - Statut : `EN_ATTENTE`

2. **Admin valide la demande**

   - Review des infos
   - Validation → Création compte utilisateur
   - **Génération automatique d'un mot de passe temporaire aléatoire** (12 caractères)
   - Stockage dans DB avec flag `premiere_connexion = TRUE`

3. **Email automatique envoyé** contenant :

   ```
   Sujet : Votre compte MonVolEnLigne a été activé

   Bonjour [Nom Agence/Compagnie],

   Votre demande a été validée !

   Identifiant : [email]
   Mot de passe temporaire : [mdp_temporaire_généré]

   🔗 Connexion : https://monVolEnLigne.com/app/auth/connexion.php

   ⚠️ IMPORTANT : Vous devrez changer votre mot de passe lors de votre première connexion.
   ```

4. **Première connexion**

   - Vérification du flag `premiere_connexion`
   - **Redirection FORCÉE** vers page changement de mot de passe
   - Impossible d'accéder au dashboard sans changer le MDP
   - Validation :
     - Minimum 8 caractères
     - 1 majuscule, 1 minuscule, 1 chiffre
   - Update flag `premiere_connexion = FALSE`

5. **Connexions suivantes**

   - Login normal
   - Option "Mot de passe oublié" disponible

6. **Changement ultérieur**
   - Page profil → Section "Sécurité"
   - Formulaire : Ancien MDP + Nouveau MDP + Confirmation

### Modifications DB nécessaires

```sql
ALTER TABLE utilisateurs
ADD COLUMN premiere_connexion BOOLEAN DEFAULT FALSE AFTER mot_de_passe;
```

### Fichiers à créer/modifier

#### Nouveaux fichiers :

- [ ] `/src/functions/generate_temp_password.php` - Génération MDP aléatoire sécurisé
- [ ] `/app/auth/premiere-connexion.php` - Page changement MDP obligatoire
- [ ] `/app/auth/layouts/main-premiere-connexion.php` - Layout
- [ ] `/src/controllers/premiere_connexion_process.php` - Controller changement

#### Fichiers à modifier :

- [ ] `/src/controllers/admin/valider_demande_agence.php` - Générer MDP + Email
- [ ] `/src/controllers/admin/valider_demande_compagnie.php` - Générer MDP + Email
- [ ] `/src/controllers/connexion_process.php` - Vérifier flag `premiere_connexion`
- [ ] `/app/agency/profil.php` - Ajouter section changement MDP
- [ ] `/app/compagnie/profil.php` - Ajouter section changement MDP

### Fonctions à implémenter

```php
// Dans /src/functions/generate_temp_password.php
function generate_temp_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle(str_repeat($chars, ceil($length/strlen($chars)))), 0, $length);
}

// Dans /src/controllers/connexion_process.php (à ajouter)
if ($user['premiere_connexion'] === 1) {
    $_SESSION['must_change_password'] = true;
    header("Location: /app/auth/premiere-connexion.php");
    exit();
}
```

### Templates email

#### Email activation agence :

```html
<h2>Bienvenue sur MonVolEnLigne !</h2>
<p>Bonjour <strong>[Nom Agence]</strong>,</p>
<p>Votre demande d'inscription a été validée par notre équipe.</p>

<div
  style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;"
>
  <h3>Vos identifiants de connexion :</h3>
  <p><strong>Email :</strong> [email]</p>
  <p>
    <strong>Mot de passe temporaire :</strong>
    <code style="background: #fff; padding: 5px 10px; border-radius: 4px;"
      >[mdp_temp]</code
    >
  </p>
</div>

<p>
  <a
    href="https://monVolEnLigne.com/app/auth/connexion.php"
    style="display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;"
  >
    Se connecter maintenant
  </a>
</p>

<p style="color: #dc2626; font-weight: 600;">
  ⚠️ Vous devrez changer ce mot de passe lors de votre première connexion pour
  des raisons de sécurité.
</p>
```

### Sécurité

✅ **Points de sécurité** :

1. Mot de passe temporaire aléatoire (non prévisible)
2. Changement forcé à la première connexion
3. Validation robuste du nouveau mot de passe
4. Pas de réutilisation du mot de passe temporaire
5. Email envoyé de manière sécurisée (PHPMailer)
6. Hash bcrypt pour tous les mots de passe

### Priorité

🔥 **HAUTE PRIORITÉ** - À implémenter AVANT mise en production

### Workflow de test

1. Créer demande agence/compagnie
2. Admin valide → Vérifier email reçu avec MDP temp
3. Connexion avec MDP temp → Redirection forcée
4. Changement MDP → Accès au dashboard
5. Déconnexion → Reconnexion avec nouveau MDP
6. Profil → Vérifier option changement MDP

---

## 📋 Autres points à vérifier

### Point 1 : Génération PDF des billets

**Statut** : ⏸️ En attente (Composer requis)
**Voir** : AVANCEMENT_PROJET.md section "Tâches en attente"

### Point 2 : Validation email unique

**Statut** : ✅ Implémenté dans les formulaires d'inscription
**Vérifier** : Tous les formulaires UPDATE aussi

### Point 3 : Soft delete

**Statut** : ✅ Prévu dans DB (champ `date_suppression`)
**TODO** : Implémenter les controllers de suppression

### Point 4 : Logs et audit trail

**Statut** : 📋 Prévu pour module ADMIN
**Tables** : `historique_statuts_admin` existe

---

**Dernière mise à jour** : 20 Octobre 2025
