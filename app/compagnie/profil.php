<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/compagnie_data.php';

$user_id = $_SESSION['user_id'];
$compagnie_id = $_SESSION['compagnie_id'] ?? get_compagnie_id_from_user($pdo, $user_id);

if (!$compagnie_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de la compagnie.";
    header("Location: /app/auth/connexion.php");
    exit();
}

$compagnie_info = get_compagnie_info($pdo, $compagnie_id);
$_SESSION['compagnie_nom'] = $compagnie_info['nom_compagnie'] ?? 'Ma Compagnie';

$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

$page_title = "Profil";
$current_page = "profil";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Profil']
];
?>

<link rel="stylesheet" href="assets/css/profil.css">

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="compagnie-container">
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-error">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <h1 class="page-title">Profil de la compagnie</h1>

    <div class="profil-grid">
        <!-- Informations -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Informations</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="/src/controllers/compagnie/update_profil.php">
                    <input type="hidden" name="action" value="update_info">
                    <div class="form-group">
                        <label class="form-label">Nom de la compagnie *</label>
                        <input type="text" name="nom_compagnie" class="form-input" value="<?= htmlspecialchars($compagnie_info['nom_compagnie']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Code IATA</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($compagnie_info['code_iata'] ?? 'N/A') ?>" disabled>
                        <p class="form-hint">Le code IATA ne peut pas être modifié</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pays</label>
                        <input type="text" name="pays" class="form-input" value="<?= htmlspecialchars($compagnie_info['pays']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea" rows="4"><?= htmlspecialchars($compagnie_info['description'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            </div>
        </div>

        <!-- Sécurité -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Sécurité</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="/src/controllers/compagnie/update_profil.php">
                    <input type="hidden" name="action" value="update_password">
                    <div class="form-group">
                        <label class="form-label">Mot de passe actuel *</label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nouveau mot de passe *</label>
                        <input type="password" name="new_password" class="form-input" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" name="confirm_password" class="form-input" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
