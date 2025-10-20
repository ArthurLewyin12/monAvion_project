<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/compagnie_data.php';

// Récupérer l'ID de la compagnie
$user_id = $_SESSION['user_id'];
$compagnie_id = $_SESSION['compagnie_id'] ?? get_compagnie_id_from_user($pdo, $user_id);

if (!$compagnie_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de la compagnie.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Récupérer les informations de la compagnie
$compagnie_info = get_compagnie_info($pdo, $compagnie_id);
$_SESSION['compagnie_nom'] = $compagnie_info['nom_compagnie'] ?? 'Ma Compagnie';

// Récupérer les avions
$avions = get_compagnie_avions($pdo, $compagnie_id);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Ma flotte";
$current_page = "ma-flotte";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Ma flotte']
];
?>

<link rel="stylesheet" href="assets/css/ma-flotte.css">

<?php
include __DIR__ . '/layouts/header.php';
?>

<div class="compagnie-container">

    <!-- Messages -->
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

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Ma flotte</h1>
            <p class="page-subtitle"><?= count($avions) ?> avion(s) dans votre flotte</p>
        </div>
        <a href="creer-avion.php" class="btn btn-primary">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Ajouter un avion
        </a>
    </div>

    <!-- Liste des avions -->
    <?php if (empty($avions)): ?>
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <h3 class="empty-title">Aucun avion dans votre flotte</h3>
                    <p class="empty-text">Commencez par ajouter un avion pour créer vos premiers vols.</p>
                    <a href="creer-avion.php" class="btn btn-primary" style="margin-top: 1.5rem;">Ajouter un avion</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="avions-grid">
            <?php foreach ($avions as $avion): ?>
                <?php
                    $sieges_data = json_decode($avion['sieges_par_classe'], true);
                ?>
                <div class="avion-card">
                    <div class="avion-header">
                        <div class="avion-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <h3 class="avion-modele"><?= htmlspecialchars($avion['modele']) ?></h3>
                    </div>
                    <div class="avion-body">
                        <div class="avion-info">
                            <div class="info-row">
                                <span class="info-label">Sièges total</span>
                                <span class="info-value"><?= $avion['nombre_sieges_total'] ?></span>
                            </div>
                            <?php if ($sieges_data): ?>
                                <?php if (isset($sieges_data['ECONOMIQUE'])): ?>
                                    <div class="info-row">
                                        <span class="info-label">Économique</span>
                                        <span class="info-value"><?= $sieges_data['ECONOMIQUE'] ?> sièges</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($sieges_data['AFFAIRE'])): ?>
                                    <div class="info-row">
                                        <span class="info-label">Affaires</span>
                                        <span class="info-value"><?= $sieges_data['AFFAIRE'] ?> sièges</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($sieges_data['PREMIERE'])): ?>
                                    <div class="info-row">
                                        <span class="info-label">Première</span>
                                        <span class="info-value"><?= $sieges_data['PREMIERE'] ?> sièges</span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="info-row">
                                <span class="info-label">Vols associés</span>
                                <span class="info-value"><?= $avion['nombre_vols'] ?? 0 ?> vol(s)</span>
                            </div>
                        </div>
                        <?php if ($avion['description']): ?>
                            <p class="avion-description"><?= htmlspecialchars($avion['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="avion-footer">
                        <button class="btn btn-sm btn-outline">Voir les détails</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
