# Nettoyage et Mise à Jour à Faire

## ✅ Fichiers Supprimés (Obsolètes)
- [x] `src/controllers/agency_registration_process.php` - Utilisait ancienne structure
- [x] `src/controllers/airline_registration_process.php` - Utilisait ancienne structure

## ⚠️ Fichiers à Mettre à Jour (Ancienne structure de session)

Ces fichiers utilisent `$_SESSION['user']` au lieu de la nouvelle structure de session :

### Controllers Dashboard
1. **src/controllers/booking_process.php** 
   - Ligne 13: `$user_id = $_SESSION['user']['id'];`
   - Doit utiliser: `$_SESSION['user_id']`
   - Requête ligne 16: `SELECT id FROM agencies` → `SELECT id FROM agences`

2. **src/controllers/fleet_process.php**
   - Utilise ancienne structure session
   - Utilise table `airlines` → doit être `compagnies_aeriennes`
   - Utilise table `aircrafts` → doit être `avions`

3. **src/controllers/flight_process.php**
   - Utilise ancienne structure session
   - Utilise table `flights` → doit être `vols`
   - Utilise table `airlines` → doit être `compagnies_aeriennes`

4. **src/controllers/flight_request_process.php**
   - Utilise ancienne structure session
   - Utilise table `flight_requests` → doit être `demandes_vols`
   - Utilise table `users` → doit être `utilisateurs`

5. **src/controllers/fare_process.php**
   - Utilise ancienne structure session
   - Utilise table `fares` → doit être `tarifs`
   - Utilise table `flights` → doit être `vols`

## 📋 Tables - Correspondance Français

| Ancien (Anglais) | Nouveau (Français) |
|------------------|-------------------|
| users | utilisateurs |
| agencies | agences |
| airlines | compagnies_aeriennes |
| flights | vols |
| aircrafts | avions |
| bookings | reservations |
| passengers | passagers |
| seats | sieges |
| fares | tarifs |
| tickets | billets |
| flight_requests | demandes_vols |
| agency_requests | demandes_agences |
| airline_requests | demandes_compagnies |
| contact_messages | messages_contact |

## 📝 Actions Prioritaires

1. Mettre à jour les 5 controllers dashboard pour :
   - Utiliser nouvelle structure session (`$_SESSION['user_id']` au lieu de `$_SESSION['user']['id']`)
   - Utiliser noms de tables en français
   - Utiliser noms de colonnes en français (prenom, nom, etc.)

2. Tester tous les formulaires de la landing page

3. Créer/Vérifier les pages dashboard de base (home.php pour chaque rôle)
