<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/admin_data.php';

// Filtres
$statut_filter = $_GET['statut'] ?? 'tous';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

// Récupérer les vols
$vols = get_all_vols($pdo, $statut_filter, $limit, $offset);

// Compter le total pour pagination
$count_query = "SELECT COUNT(*) FROM vols WHERE date_suppression IS NULL";
$count_params = [];

if ($statut_filter !== 'tous') {
    $count_query .= " AND statut = :statut";
    $count_params[':statut'] = $statut_filter;
}

$stmt = $pdo->prepare($count_query);
$stmt->execute($count_params);
$total_vols = $stmt->fetchColumn();
$total_pages = ceil($total_vols / $limit);

// Statistiques
$stats = get_admin_stats($pdo);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Supervision des vols";
$current_page = "vols";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Vols']
];
?>

<link rel="stylesheet" href="assets/css/vols.css">

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="admin-container">

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
            <h1 class="page-title">Supervision des vols</h1>
            <p class="page-subtitle">Vue d'ensemble de tous les vols de la plateforme</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid-4">
        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total vols</p>
                <p class="stat-value"><?= $stats['total_vols'] ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Programmés</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM vols WHERE statut = 'PROGRAMME' AND date_suppression IS NULL");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-warning">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="16" r="1" fill="currentColor"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Retardés</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM vols WHERE statut = 'RETARDE' AND date_suppression IS NULL");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-error">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Annulés</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM vols WHERE statut = 'ANNULE' AND date_suppression IS NULL");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card">
        <div class="card-body">
            <div class="filters-tabs">
                <a href="?statut=tous" class="filter-tab <?= $statut_filter === 'tous' ? 'active' : '' ?>">
                    Tous
                </a>
                <a href="?statut=PROGRAMME" class="filter-tab <?= $statut_filter === 'PROGRAMME' ? 'active' : '' ?>">
                    Programmés
                </a>
                <a href="?statut=RETARDE" class="filter-tab <?= $statut_filter === 'RETARDE' ? 'active' : '' ?>">
                    Retardés
                </a>
                <a href="?statut=ANNULE" class="filter-tab <?= $statut_filter === 'ANNULE' ? 'active' : '' ?>">
                    Annulés
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des vols -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Vols (<?= $total_vols ?>)</h2>
        </div>
        <div class="card-body card-body-table">
            <?php if (empty($vols)): ?>
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <p class="empty-text">Aucun vol trouvé</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numéro de vol</th>
                                <th>Route</th>
                                <th>Compagnie</th>
                                <th>Avion</th>
                                <th>Date & Heure</th>
                                <th>Durée</th>
                                <th>Statut</th>
                                <th>Réservations</th>
                                <th class="table-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vols as $vol): ?>
                                <tr>
                                    <td>
                                        <strong class="vol-numero"><?= htmlspecialchars($vol['numero_vol']) ?></strong>
                                    </td>
                                    <td>
                                        <div class="vol-route">
                                            <span class="route-code"><?= htmlspecialchars($vol['aeroport_depart']) ?></span>
                                            <svg class="route-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span class="route-code"><?= htmlspecialchars($vol['aeroport_arrivee']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($vol['compagnie_nom']) ?></td>
                                    <td>
                                        <div class="avion-info">
                                            <strong><?= htmlspecialchars($vol['avion_nom']) ?></strong>
                                            <small><?= htmlspecialchars($vol['avion_immatriculation']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="vol-datetime">
                                            <strong><?= date('d/m/Y', strtotime($vol['date_depart'])) ?></strong>
                                            <small><?= date('H:i', strtotime($vol['heure_depart'])) ?> → <?= date('H:i', strtotime($vol['heure_arrivee'])) ?></small>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($vol['duree_vol']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($vol['statut']) ?>">
                                            <?= htmlspecialchars($vol['statut']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="reservations-info">
                                            <strong><?= $vol['total_reservations'] ?></strong>
                                            <small>réservation<?= $vol['total_reservations'] > 1 ? 's' : '' ?></small>
                                        </div>
                                    </td>
                                    <td class="table-actions">
                                        <button class="btn-icon" onclick="openVolModal(<?= $vol['id_vol'] ?>)" title="Voir détails">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 12S5 4 12 4C19 4 23 12 23 12S19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor" stroke-width="2"/>
                                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&statut=<?= urlencode($statut_filter) ?>"
                               class="pagination-link">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Précédent
                            </a>
                        <?php endif; ?>

                        <span class="pagination-info">Page <?= $page ?> sur <?= $total_pages ?></span>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&statut=<?= urlencode($statut_filter) ?>"
                               class="pagination-link">
                                Suivant
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal Détails Vol -->
<div id="volModal" class="modal">
    <div class="modal-content modal-content-lg">
        <div class="modal-header">
            <h3 class="modal-title">Détails du vol</h3>
            <button class="modal-close" onclick="closeModal('volModal')">&times;</button>
        </div>
        <div id="volModalBody" class="modal-body">
            <div class="loading">Chargement...</div>
        </div>
    </div>
</div>

<script>
function openVolModal(volId) {
    const modal = document.getElementById('volModal');
    const body = document.getElementById('volModalBody');
    modal.classList.add('active');
    body.innerHTML = '<div class="loading">Chargement...</div>';

    // Charger les détails via fetch
    fetch(`/src/controllers/admin/get_vol_details.php?id=${volId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                body.innerHTML = generateVolDetailsHTML(data.vol);
            } else {
                body.innerHTML = '<p class="error-text">Erreur lors du chargement des détails</p>';
            }
        })
        .catch(error => {
            body.innerHTML = '<p class="error-text">Erreur lors du chargement des détails</p>';
        });
}

function generateVolDetailsHTML(vol) {
    let html = '<div class="vol-details">';

    html += '<div class="detail-section">';
    html += '<h4 class="detail-section-title">Informations générales</h4>';
    html += '<div class="detail-grid">';
    html += `<div class="detail-row"><span class="detail-label">Numéro de vol:</span><span><strong>${vol.numero_vol}</strong></span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Compagnie:</span><span>${vol.compagnie_nom}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Avion:</span><span>${vol.avion_nom} (${vol.avion_immatriculation})</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Statut:</span><span><span class="badge badge-${vol.statut.toLowerCase()}">${vol.statut}</span></span></div>`;
    html += '</div></div>';

    html += '<div class="detail-section">';
    html += '<h4 class="detail-section-title">Itinéraire</h4>';
    html += '<div class="detail-grid">';
    html += `<div class="detail-row"><span class="detail-label">Départ:</span><span>${vol.aeroport_depart} le ${new Date(vol.date_depart).toLocaleDateString('fr-FR')} à ${vol.heure_depart}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Arrivée:</span><span>${vol.aeroport_arrivee} à ${vol.heure_arrivee}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Durée:</span><span>${vol.duree_vol}</span></div>`;
    html += '</div></div>';

    html += '<div class="detail-section">';
    html += '<h4 class="detail-section-title">Réservations</h4>';
    html += '<div class="detail-grid">';
    html += `<div class="detail-row"><span class="detail-label">Total réservations:</span><span><strong>${vol.total_reservations}</strong></span></div>`;
    html += '</div></div>';

    html += '</div>';
    return html;
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Fermer modal en cliquant en dehors
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
