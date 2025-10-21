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
$errors = $_SESSION['inscription_errors'] ?? [];
$old_values = $_SESSION['inscription_old'] ?? [];
unset($_SESSION['inscription_errors'], $_SESSION['inscription_old']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - MonVolEnLigne</title>
    <link rel="stylesheet" href="../../public/main.css">
    <link rel="stylesheet" href="assets/css/inscription.css">
</head>

<body>
    <div class="signup-container">
        <!-- PARTIE GAUCHE : Formulaire -->
        <div class="signup-form-side">
            <!-- Bouton retour -->
            <a href="/app/landing/index.php" class="back-button" title="Retour à l'accueil">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
                Retour
            </a>

            <div class="signup-form-content">
                <!-- Logo -->
                <div class="signup-logo">
                    <div class="signup-logo-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <h1 class="signup-logo-text">MonVolEnLigne</h1>
                </div>

                <!-- En-tête -->
                <div class="signup-header">
                    <h2 class="signup-title">Créez votre compte</h2>
                    <p class="signup-subtitle">Rejoignez des milliers de voyageurs satisfaits</p>
                </div>

                <!-- Formulaire -->
                <form class="signup-form" action="../../src/controllers/inscription_process.php" method="POST" id="signupForm">
                    <!-- Champ Prénom -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="prenom">Prénom</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <input
                                    required
                                    class="form-input"
                                    type="text"
                                    id="prenom"
                                    name="prenom"
                                    placeholder="Jean"
                                    autocomplete="given-name" />
                            </div>
                        </div>

                        <!-- Champ Nom -->
                        <div class="form-group">
                            <label class="form-label" for="nom">Nom</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <input
                                    required
                                    class="form-input"
                                    type="text"
                                    id="nom"
                                    name="nom"
                                    placeholder="Dupont"
                                    autocomplete="family-name" />
                            </div>
                        </div>
                    </div>

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

                    <!-- Champ Téléphone -->
                    <div class="form-group">
                        <label class="form-label" for="telephone">Téléphone <span class="optional-label">(optionnel)</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <input
                                class="form-input"
                                type="tel"
                                id="telephone"
                                name="telephone"
                                placeholder="+33 6 12 34 56 78"
                                autocomplete="tel" />
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
                                autocomplete="new-password" />
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
                        <div class="password-strength" id="passwordStrength">
                            <div class="strength-bar">
                                <div class="strength-bar-fill" id="strengthBarFill"></div>
                            </div>
                            <p class="strength-text" id="strengthText"></p>
                        </div>
                    </div>

                    <!-- Champ Confirmation Mot de passe -->
                    <div class="form-group">
                        <label class="form-label" for="password_confirm">Confirmer le mot de passe</label>
                        <div class="input-wrapper input-wrapper-password">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input
                                required
                                class="form-input form-input-password"
                                type="password"
                                id="password_confirm"
                                name="password_confirm"
                                placeholder="••••••••"
                                autocomplete="new-password" />
                            <button type="button" class="toggle-password" aria-label="Afficher le mot de passe" id="togglePasswordConfirm">
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

                    <!-- CGU Checkbox -->
                    <div class="form-group">
                        <label class="checkbox-wrapper checkbox-wrapper-terms">
                            <input required type="checkbox" name="terms" id="terms">
                            <span class="checkbox-label">
                                J'accepte les <a href="/public/terms.php" target="_blank" class="terms-link">conditions générales d'utilisation</a> et la <a href="/public/privacy.php" target="_blank" class="terms-link">politique de confidentialité</a>
                            </span>
                        </label>
                    </div>

                    <!-- Newsletter Checkbox -->
                    <div class="form-group">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="newsletter" id="newsletter">
                            <span class="checkbox-label">Je souhaite recevoir les offres et actualités par email</span>
                        </label>
                    </div>

                    <!-- Bouton d'inscription -->
                    <button type="submit" class="signup-button">
                        <span class="signup-button-text">Créer mon compte</span>
                        <svg class="signup-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Pied du formulaire -->
                    <div class="signup-footer">
                        <p>Vous avez déjà un compte ? <a href="connexion.php" class="login-link">Se connecter</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- PARTIE DROITE : Visuel/Texte -->
        <div class="signup-visual-side">
            <div class="signup-visual-content">
                <!-- Decoration blobs -->
                <div class="visual-blob visual-blob-1"></div>
                <div class="visual-blob visual-blob-2"></div>

                <!-- Contenu -->
                <div class="visual-text">
                    <h2 class="visual-title">Voyagez en toute sérénité</h2>
                    <p class="visual-description">
                        Créez votre compte et accédez aux meilleurs tarifs sur des milliers de vols. Réservez en quelques clics et profitez d'une expérience de voyage exceptionnelle.
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
                                <strong>Comparaison instantanée</strong> des prix sur 50+ compagnies
                            </div>
                        </div>

                        <div class="visual-feature">
                            <div class="visual-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <div class="visual-feature-text">
                                <strong>Réservation sécurisée</strong> avec paiement 100% protégé
                            </div>
                        </div>

                        <div class="visual-feature">
                            <div class="visual-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <div class="visual-feature-text">
                                <strong>Support client</strong> disponible 24/7 pour vous aider
                            </div>
                        </div>

                        <div class="visual-feature">
                            <div class="visual-feature-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                            <div class="visual-feature-text">
                                <strong>Offres exclusives</strong> réservées aux membres
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="visual-stats">
                        <div class="visual-stat">
                            <div class="visual-stat-number">10K+</div>
                            <div class="visual-stat-label">Clients satisfaits</div>
                        </div>
                        <div class="visual-stat">
                            <div class="visual-stat-number">500+</div>
                            <div class="visual-stat-label">Destinations</div>
                        </div>
                        <div class="visual-stat">
                            <div class="visual-stat-number">4.9/5</div>
                            <div class="visual-stat-label">Note moyenne</div>
                        </div>
                    </div>
                </div>

                <!-- Illustration SVG -->
                <div class="visual-illustration">
                    <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Mappemonde stylisée -->
                        <circle cx="200" cy="150" r="100" stroke="var(--color-accent-300)" stroke-width="2" opacity="0.3" />
                        <ellipse cx="200" cy="150" rx="100" ry="40" stroke="var(--color-accent-300)" stroke-width="2" opacity="0.3" />
                        <path d="M200 50 L200 250" stroke="var(--color-accent-300)" stroke-width="2" opacity="0.3" />

                        <!-- Avions qui tournent -->
                        <g class="plane-orbit plane-orbit-1">
                            <circle cx="280" cy="100" r="15" fill="var(--color-secondary-400)" opacity="0.9" />
                            <path d="M275 100 L265 105 L275 103 Z" fill="var(--color-accent-300)" />
                        </g>

                        <g class="plane-orbit plane-orbit-2">
                            <circle cx="120" cy="180" r="12" fill="var(--color-accent-400)" opacity="0.9" />
                            <path d="M115 180 L108 183 L115 181 Z" fill="var(--color-secondary-300)" />
                        </g>

                        <!-- Points de destination -->
                        <circle cx="150" cy="120" r="5" fill="var(--color-secondary-400)">
                            <animate attributeName="opacity" values="0.5;1;0.5" dur="2s" repeatCount="indefinite" />
                        </circle>
                        <circle cx="250" cy="170" r="5" fill="var(--color-accent-400)">
                            <animate attributeName="opacity" values="0.5;1;0.5" dur="2s" begin="0.5s" repeatCount="indefinite" />
                        </circle>
                        <circle cx="220" cy="130" r="5" fill="var(--color-secondary-300)">
                            <animate attributeName="opacity" values="0.5;1;0.5" dur="2s" begin="1s" repeatCount="indefinite" />
                        </circle>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <script src="../../public/assets/js/inscription-form.js"></script>
</body>

</html>