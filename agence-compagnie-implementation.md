# INSCRIPTION DES AGENCES ET COMPAGNIES AERIENNES

le but pour ces deux cas est de sécuriser l'access aux insertions dans leur différents dashboard

voici les étapes à mettre en place:

# 1 - `@agences/inscription.php` et `@compagnies/inscription.php`

l'agence ou la compagnie s'inscrive en renseignant leur informations (check `@db.sql` pour les tables users, agencies et airlines), sauf le password

2 - Apres la validation de cette inscription, l'utilisateur vois un message (une dialogue) pour lui dire ceci: Votre formulaire a bien été pris en compte,vous recevrez un code de validation pour terminer votre inscription. puis est redirigé vers la page d'accueil.

# 3 - `@bg25/admin/dashboard.php`

Ensuite ces données seront enregistrées dans les tables citées pour etre analysé par l'administrateur dans `@bg25/admin/dashboard.php`, laba prepare un tableau qui affiche tous les utilisateur, avec pour chacun un button 'affichez plus'.Elle doit rediriger vers une page du dashboard où l'on a toutes les informations de la compagnie ou l'agence. Dans cette page, on doit avoir un boutton activer et suspendre. le bouton activer doit s'afficher seulement si le user['current_status'] != 'ACTIVE' et l'inverse pour le bouton suspendre.
