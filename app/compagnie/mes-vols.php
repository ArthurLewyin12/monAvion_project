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

// Filtres
$filtre_statut = $_GET['statut'] ?? null;
$filtre_date = $_GET['date'] ?? null;

// Récupérer les vols
$vols = get_compagnie_vols($pdo, $compagnie_id, $filtre_statut, $filtre_date);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Mes vols";
$current_page = "mes-vols";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Mes vols']
];
?>

<link rel="stylesheet" href="assets/css/mes-vols.css">

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
            <h1 class="page-title">Mes vols</h1>
            <p class="page-subtitle"><?= count($vols) ?> vol(s) total</p>
        </div>
        <a href="creer-vol.php" class="btn btn-primary">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Créer un vol
        </a>
    </div>

    <!-- Filtres -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-body">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">Tous les statuts</option>
                        <option value="PROGRAMME" <?= $filtre_statut === 'PROGRAMME' ? 'selected' : '' ?>>Programmé</option>
                        <option value="RETARDE" <?= $filtre_statut === 'RETARDE' ? 'selected' : '' ?>>Retardé</option>
                        <option value="ANNULE" <?= $filtre_statut === 'ANNULE' ? 'selected' : '' ?>>Annulé</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Date</label>
                    <input type="date" name="date" class="filter-input" value="<?= htmlspecialchars($filtre_date ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <?php if ($filtre_statut || $filtre_date): ?>
                    <a href="mes-vols.php" class="btn btn-outline">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Table des vols -->
    <?php if (empty($vols)): ?>
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <h3 class="empty-title">Aucun vol trouvé</h3>
                    <p class="empty-text">Créez votre premier vol pour commencer.</p>
                    <a href="creer-vol.php" class="btn btn-primary" style="margin-top: 1.5rem;">Créer un vol</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Vol</th>
                            <th>Route</th>
                            <th>Départ</th>
                            <th>Arrivée</th>
                            <th>Avion</th>
                            <th>Réservations</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vols as $vol): ?>
                            <tr>
                                <td class="td-numero"><?= htmlspecialchars($vol['numero_vol']) ?></td>
                                <td class="td-route">
                                    <?= htmlspecialchars($vol['aeroport_depart']) ?> → <?= htmlspecialchars($vol['aeroport_arrivee']) ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($vol['date_depart'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($vol['date_arrivee'])) ?></td>
                                <td><?= htmlspecialchars($vol['avion_modele']) ?></td>
                                <td class="td-center">
                                    <?= $vol['nombre_reservations'] ?> / <?= $vol['nombre_sieges_total'] ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= strtolower($vol['statut']) ?>">
                                        <?= htmlspecialchars($vol['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail-vol.php?id=<?= $vol['id'] ?>" class="btn btn-sm btn-outline">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
