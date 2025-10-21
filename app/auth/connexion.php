<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur est déjà connecté, le rediriger vers son dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    $user_type = $_SESSION['user_type'] ?? null;
    switch ($user_type) {
        case 'ADMIN':
            header('Location: ../admin/dashboard.php');
            exit();
        case 'CLIENT':
            header('Location: ../client/home.php');
            exit();
        case 'AGENCE':
            header('Location: ../agency/home.php');
            exit();
        case 'COMPAGNIE':
            header('Location: ../compagnie/home.php');
            exit();
    }
}

// Récupérer les erreurs et valeurs précédentes
$errors = $_SESSION['login_errors'] ?? [];
$email = $_SESSION['login_email'] ?? '';
unset($_SESSION['login_errors'], $_SESSION['login_email']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MonVolEnLigne</title>
    <link rel="stylesheet" href="../../public/main.css">
    <link rel="stylesheet" href="assets/css/connection.css">
</head>

<body>
    <div class="login-container">
        <!-- PARTIE GAUCHE : Formulaire -->
        <div class="login-form-side">
            <!-- Bouton retour -->
            <a href="../landing/index.php" class="back-button" title="Retour à l'accueil">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
                Retour
            </a>

            <div class="login-form-content">
                <!-- Logo -->
                <div class="login-logo">
                    <div class="login-logo-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <h1 class="login-logo-text">MonVolEnLigne</h1>
                </div>

                <!-- En-tête -->
                <div class="login-header">
                    <h2 class="login-title">Bon retour !</h2>
                    <p class="login-subtitle">Connectez-vous à votre espace professionnel</p>
                </div>

                <!-- Formulaire -->
                <form class="login-form" action="../../src/controllers/connexion_process.php" method="POST" id="loginForm">
                    <!-- Champ Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Adresse email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            <input
                                required
                                class="form-input"
                                type="email"
                                id="email"
                                name="email"
                                placeholder="votre.email@example.com"
                                autocomplete="email" />
                        </div>
                    </div>

                    <!-- Champ Mot de passe -->
                    <div class="form-group">
                        <label class="form-label" for="password">Mot de passe</label>
                        <div class="input-wrapper input-wrapper-password">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input
                                required
                                class="form-input form-input-password"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password" />
                            <button type="button" class="toggle-password" aria-label="Afficher le mot de passe" id="togglePassword">
                                <svg class="eye-icon eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg class="eye-icon eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkbox-label">Se souvenir de moi</span>
                        </label>
                        <a href="forgot-password.php" class="forgot-link">Mot de passe oublié ?</a>
                    </div>

                    <!-- Bouton de connexion -->
                    <button type="submit" class="login-button">
                        <span class="login-button-text">Se connecter</span>
                        <svg class="login-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Pied du formulaire -->
                    <div class="login-footer">
                        <p>Pas encore de compte ? <a href="inscription.php" class="signup-link">Créer un compte</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- PARTIE DROITE : Visuel/Texte -->
        <div class="login-visual-side">
            <div class="login-visual-content">
                <!-- Decoration blobs -->
                <div class="visual-blob visual-blob-1"></div>
                <div class="visual-blob visual-blob-2"></div>

                <!-- Contenu -->
                <div class="visual-text">
                    <h2 class="visual-title">Gérez vos réservations en toute simplicité</h2>
                    <p class="visual-description">
                        Accédez à votre tableau de bord et profitez de tous les outils dont vous avez besoin pour optimiser votre activité.
                    </p>

                    <!-- Features list -->
                    <div class="visual-features">
                        <div class="visual-feature">
                            <div class="visual-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <div class="visual-feature-text">
                                <strong>Recherche instantanée</strong> sur 50+ compagnies aériennes
                            </div>
                        </div>

                        <div class="visual-feature">
                            <div class="visual-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <div class="visual-feature-text">
                                <strong>Réservation en temps réel</strong> avec visualisation des sièges
                            </div>
                        </div>

                        <div class="visual-feature">
                            <div class="visual-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <div class="visual-feature-text">
                                <strong>Support 24/7</strong> pour vous accompagner
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="visual-stats">
                        <div class="visual-stat">
                            <div class="visual-stat-number">200+</div>
                            <div class="visual-stat-label">Agences partenaires</div>
                        </div>
                        <div class="visual-stat">
                            <div class="visual-stat-number">1000+</div>
                            <div class="visual-stat-label">Vols/jour</div>
                        </div>
                        <div class="visual-stat">
                            <div class="visual-stat-number">24/7</div>
                            <div class="visual-stat-label">Support</div>
                        </div>
                    </div>
                </div>

                <!-- Illustration SVG -->
                <div class="visual-illustration">
                    <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Avion -->
                        <g class="plane-animation">
                            <ellipse cx="200" cy="150" rx="100" ry="35" fill="url(#planeGrad)" opacity="0.9" />
                            <path d="M100 150 L80 170 L100 155 Z" fill="var(--color-accent-300)" />
                            <path d="M300 150 L320 170 L300 155 Z" fill="var(--color-accent-300)" />
                            <circle cx="200" cy="150" r="12" fill="var(--color-secondary-400)" />
                        </g>

                        <!-- Trajectoires -->
                        <path class="flight-line flight-line-1" d="M50 80 Q150 60 250 100" stroke="var(--color-accent-200)" stroke-width="2" stroke-dasharray="5,5" opacity="0.5" />
                        <path class="flight-line flight-line-2" d="M50 220 Q150 240 250 200" stroke="var(--color-secondary-200)" stroke-width="2" stroke-dasharray="5,5" opacity="0.5" />

                        <defs>
                            <linearGradient id="planeGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:var(--color-accent-400);stop-opacity:1" />
                                <stop offset="100%" style="stop-color:var(--color-secondary-400);stop-opacity:1" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <script src="/public/assets/js/login-form.js"></script>
</body>

</html>