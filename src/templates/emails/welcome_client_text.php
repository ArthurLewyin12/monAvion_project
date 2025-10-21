<?php

/**
 * Template d'email de bienvenue pour les nouveaux clients (version texte)
 * Variables disponibles: $prenom, $dashboard_url
 */
?>
Bonjour <?php echo $prenom; ?>,

Nous sommes ravis de vous accueillir sur MonVolEnLigne !

Votre compte a été créé avec succès. Vous pouvez maintenant profiter de tous nos services :

• Recherche de vols en temps réel sur plus de 50 compagnies
• Comparaison des prix pour trouver les meilleurs tarifs
• Réservation simplifiée en quelques clics
• Suivi de vos réservations depuis votre espace personnel

Pour commencer, accédez à votre espace personnel :
<?php echo $dashboard_url; ?>


Si vous avez des questions, n'hésitez pas à nous contacter.

À bientôt sur MonVolEnLigne !
L'équipe MonVolEnLigne

---
© <?php echo date('Y'); ?> MonVolEnLigne - Tous droits réservés