<?php
session_start();

// Si déjà connecté en tant que compagnie, rediriger
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && $_SESSION['user_type'] === 'COMPAGNIE') {
    header("Location: /app/compagnie/dashboard.php");
    exit();
}

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Compagnie - FlyManager</title>
    <link rel="stylesheet" href="/public/main.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-page">

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <svg class="auth-logo" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 11L3 6L12 2L21 6L12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1 class="auth-title">Espace Compagnie</h1>
            <p class="auth-subtitle">Connectez-vous à votre compte compagnie aérienne</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/src/controllers/connexion_compagnie_process.php" class="auth-form">
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    required
                    placeholder="votre.compagnie@example.com"
                >
            </div>

            <div class="form-group">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    class="form-input"
                    required
                    placeholder="••••••••"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Se connecter
            </button>
        </form>

        <div class="auth-footer">
            <p class="auth-note">
                <svg class="note-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 16V12M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Cet espace est réservé aux compagnies aériennes partenaires. Si vous êtes un client,
                <a href="/app/auth/connexion.php" class="auth-link">connectez-vous ici</a>.
            </p>
        </div>
    </div>
</div>

</body>
</html>
