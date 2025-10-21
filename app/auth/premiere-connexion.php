<?php
session_start();

// Vérifier que l'utilisateur est connecté et doit changer son mot de passe
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location:connexion.php");
    exit();
}

if (!isset($_SESSION['must_change_password']) || !$_SESSION['must_change_password']) {
    // Rediriger vers le bon dashboard selon le type
    $redirect = match ($_SESSION['user_type']) {
        'CLIENT' => '../client/home.php',
        'AGENCE' => '/../agency/dashboard.php',
        'COMPAGNIE' => '/../compagnie/dashboard.php',
        'ADMIN' => '/../admin/dashboard.php',
        default => 'connexion.php'
    };
    header("Location: $redirect");
    exit();
}

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);

$user_type_label = match ($_SESSION['user_type']) {
    'AGENCE' => 'Agence',
    'COMPAGNIE' => 'Compagnie',
    default => 'Utilisateur'
};
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Première connexion - MonVolEnLigne</title>
    <link rel="stylesheet" href="/public/main.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="auth-page">

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <svg class="auth-logo" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 15V3M12 15L8 11M12 15L16 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M2 17L2 19C2 20.1046 2.89543 21 4 21L20 21C21.1046 21 22 20.1046 22 19L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <h1 class="auth-title">Première connexion</h1>
                <p class="auth-subtitle">Bienvenue ! Veuillez changer votre mot de passe temporaire</p>
            </div>

            <div class="alert alert-info" style="margin-bottom: 1.5rem;">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                    <path d="M12 16V12M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <div>
                    <strong>Changement obligatoire</strong><br>
                    Pour des raisons de sécurité, vous devez définir un nouveau mot de passe avant d'accéder à votre espace <?= htmlspecialchars($user_type_label) ?>.
                </div>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                        <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/src/controllers/premiere_connexion_process.php" class="auth-form">
                <div class="form-group">
                    <label for="current_password" class="form-label">Mot de passe temporaire *</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        class="form-input"
                        required
                        placeholder="Votre mot de passe temporaire">
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">Nouveau mot de passe *</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        class="form-input"
                        required
                        minlength="8"
                        placeholder="Minimum 8 caractères">
                    <p class="form-hint">
                        Le mot de passe doit contenir au moins 8 caractères avec au moins une majuscule, une minuscule et un chiffre.
                    </p>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirmer le mot de passe *</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-input"
                        required
                        placeholder="Confirmez votre mot de passe">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Définir mon mot de passe
                </button>
            </form>

            <div class="auth-footer">
                <p class="auth-note">
                    <svg class="note-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 15V3M12 15L8 11M12 15L16 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Une fois votre mot de passe changé, vous pourrez accéder à votre espace.
                </p>
            </div>
        </div>
    </div>

</body>

</html>