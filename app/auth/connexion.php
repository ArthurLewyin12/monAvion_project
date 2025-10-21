<?php
// Inclure la configuration
require_once __DIR__ . '/../../config/config.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur est déjà connecté, le rediriger vers son dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    $user_type = $_SESSION['user_type'] ?? null;
    switch ($user_type) {
        case 'ADMIN':
            header('Location: ' . url('app/admin/dashboard.php'));
            exit();
        case 'CLIENT':
            header('Location: ' . url('app/client/home.php'));
            exit();
        case 'AGENCE':
            header('Location: ' . url('app/agency/home.php'));
            exit();
        case 'COMPAGNIE':
            header('Location: ' . url('app/compagnie/home.php'));
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
    <link rel="stylesheet" href="<?= asset('main.css') ?>">
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

                <!-- Messages d'erreur -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error" style="margin-bottom: 1.5rem; padding: 1rem; background: #fee; border-left: 4px solid #dc2626; border-radius: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <svg style="width: 20px; height: 20px; color: #dc2626; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <div>
                                <?php foreach ($errors as $error): ?>
                                    <p style="margin: 0; color: #dc2626; font-size: 0.95rem;"><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulaire -->
                <form class="login-form" action="<?= url('src/controllers/connexion_process.php') ?>" method="POST" id="loginForm" novalidate>
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
                                value="<?= htmlspecialchars($email) ?>"
                                placeholder="votre.email@example.com"
                                autocomplete="email" />
                        </div>
                        <span class="field-error" id="email-error" style="display: none; color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;"></span>
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
                        <span class="field-error" id="password-error" style="display: none; color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;"></span>
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
                    <button type="submit" class="login-button" id="submitBtn">
                        <span class="login-button-text" id="btnText">Se connecter</span>
                        <svg class="login-button-icon" id="btnIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                        <svg class="login-button-loader" id="btnLoader" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none; animation: spin 1s linear infinite;">
                            <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                            <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="0.75"/>
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

    <script src="<?= asset('assets/js/login-form.js') ?>"></script>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .input-wrapper.error .form-input {
            border-color: #dc2626 !important;
        }
        .field-error {
            display: block !important;
        }
    </style>

    <script>
        // Validation en temps réel
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const btnLoader = document.getElementById('btnLoader');

        // Validation email
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            const emailWrapper = this.closest('.input-wrapper');

            if (!email) {
                emailError.textContent = 'L\'email est requis';
                emailError.style.display = 'block';
                emailWrapper.classList.add('error');
            } else if (!isValidEmail(email)) {
                emailError.textContent = 'Veuillez entrer une adresse email valide';
                emailError.style.display = 'block';
                emailWrapper.classList.add('error');
            } else {
                emailError.style.display = 'none';
                emailWrapper.classList.remove('error');
            }
        });

        emailInput.addEventListener('input', function() {
            if (emailError.style.display === 'block') {
                const email = this.value.trim();
                const emailWrapper = this.closest('.input-wrapper');
                if (email && isValidEmail(email)) {
                    emailError.style.display = 'none';
                    emailWrapper.classList.remove('error');
                }
            }
        });

        // Validation mot de passe
        passwordInput.addEventListener('blur', function() {
            const password = this.value;
            const passwordWrapper = this.closest('.input-wrapper');

            if (!password) {
                passwordError.textContent = 'Le mot de passe est requis';
                passwordError.style.display = 'block';
                passwordWrapper.classList.add('error');
            } else if (password.length < 6) {
                passwordError.textContent = 'Le mot de passe doit contenir au moins 6 caractères';
                passwordError.style.display = 'block';
                passwordWrapper.classList.add('error');
            } else {
                passwordError.style.display = 'none';
                passwordWrapper.classList.remove('error');
            }
        });

        passwordInput.addEventListener('input', function() {
            if (passwordError.style.display === 'block') {
                const password = this.value;
                const passwordWrapper = this.closest('.input-wrapper');
                if (password && password.length >= 6) {
                    passwordError.style.display = 'none';
                    passwordWrapper.classList.remove('error');
                }
            }
        });

        // Fonction de validation email
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Soumission du formulaire avec loader
        form.addEventListener('submit', function(e) {
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            let hasError = false;

            // Validation finale avant soumission
            if (!email) {
                emailError.textContent = 'L\'email est requis';
                emailError.style.display = 'block';
                emailInput.closest('.input-wrapper').classList.add('error');
                hasError = true;
            } else if (!isValidEmail(email)) {
                emailError.textContent = 'Veuillez entrer une adresse email valide';
                emailError.style.display = 'block';
                emailInput.closest('.input-wrapper').classList.add('error');
                hasError = true;
            }

            if (!password) {
                passwordError.textContent = 'Le mot de passe est requis';
                passwordError.style.display = 'block';
                passwordInput.closest('.input-wrapper').classList.add('error');
                hasError = true;
            } else if (password.length < 6) {
                passwordError.textContent = 'Le mot de passe doit contenir au moins 6 caractères';
                passwordError.style.display = 'block';
                passwordInput.closest('.input-wrapper').classList.add('error');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            // Afficher le loader et désactiver les champs
            submitBtn.disabled = true;
            emailInput.disabled = true;
            passwordInput.disabled = true;
            btnText.textContent = 'Connexion en cours...';
            btnIcon.style.display = 'none';
            btnLoader.style.display = 'inline-block';

            // Ajouter un style visuel pour indiquer que les champs sont désactivés
            emailInput.style.opacity = '0.6';
            passwordInput.style.opacity = '0.6';
            emailInput.style.cursor = 'not-allowed';
            passwordInput.style.cursor = 'not-allowed';
        });
    </script>
</body>

</html>