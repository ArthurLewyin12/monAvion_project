<?php

/**
 * Template d'email de bienvenue pour les nouveaux clients
 * Variables disponibles: $prenom, $dashboard_url
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Couleurs converties depuis OKLCH pour compatibilité email */
        /* Primary (Bleu royal): oklch(0.35 0.15 250) → #1e3a8a */
        /* Secondary (Orange): oklch(0.70 0.22 45) → #f97316 */
        /* Accent (Turquoise): oklch(0.65 0.15 195) → #06b6d4 */

        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #06b6d4 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #ffffff;
        }

        .content {
            background: #ffffff;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .content p {
            line-height: 1.6;
            margin: 15px 0;
            color: #333333;
        }

        .content ul {
            background: #fafafa;
            padding: 20px 20px 20px 40px;
            border-left: 4px solid #1e3a8a;
            border-radius: 5px;
        }

        .content ul li {
            margin: 10px 0;
            line-height: 1.5;
            color: #333333;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background: #1e3a8a;
            color: #ffffff !important;
            padding: 14px 35px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }

        .button:hover {
            background: #1e40af;
        }

        .footer {
            text-align: center;
            color: #999999;
            font-size: 12px;
            margin-top: 30px;
            padding: 20px;
        }

        .footer a {
            color: #1e3a8a;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Bienvenue sur MonVolEnLigne !</h1>
        </div>
        <div class="content">
            <p>Bonjour <strong><?php echo htmlspecialchars(
                                    $prenom,
                                ); ?></strong>,</p>

            <p>Nous sommes ravis de vous accueillir parmi nos utilisateurs !</p>

            <p>Votre compte a été créé avec succès. Vous pouvez maintenant profiter de tous nos services :</p>

            <ul>
                <li><strong>Recherche de vols</strong> en temps réel sur plus de 50 compagnies</li>
                <li><strong>Comparaison des prix</strong> pour trouver les meilleurs tarifs</li>
                <li><strong>Réservation simplifiée</strong> en quelques clics</li>
                <li><strong>Suivi de vos réservations</strong> depuis votre espace personnel</li>
            </ul>

            <p>Pour commencer, accédez à votre espace personnel et découvrez toutes les fonctionnalités qui vous sont réservées.</p>

            <div class="button-container">
                <a href="<?php echo htmlspecialchars(
                                $dashboard_url,
                            ); ?>" class="button">
                    Accéder à mon espace
                </a>
            </div>

            <p style="margin-top: 30px; color: #666;">
                Si vous avez des questions ou besoin d'aide, n'hésitez pas à nous contacter.
                Notre équipe est à votre disposition pour vous accompagner.
            </p>

            <p style="margin-top: 20px;">
                À bientôt sur MonVolEnLigne !<br>
                <strong>L'équipe MonVolEnLigne</strong>
            </p>
        </div>
        <div class="footer">
            <p>© <?php echo date("Y"); ?> MonVolEnLigne - Tous droits réservés</p>
            <p>
                <a href="#">Politique de confidentialité</a> •
                <a href="#">Conditions d'utilisation</a>
            </p>
        </div>
    </div>
</body>

</html>