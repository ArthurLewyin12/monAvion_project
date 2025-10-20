<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/admin_data.php';

// Filtres
$statut_filter = $_GET['statut'] ?? 'tous';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

// Récupérer les réservations
$reservations = get_all_reservations($pdo, $statut_filter, $limit, $offset);

// Compter le total pour pagination
$count_query = "SELECT COUNT(*) FROM reservations WHERE date_suppression IS NULL";
$count_params = [];

if ($statut_filter !== 'tous') {
    $count_query .= " AND statut = :statut";
    $count_params[':statut'] = $statut_filter;
}

$stmt = $pdo->prepare($count_query);
$stmt->execute($count_params);
$total_reservations = $stmt->fetchColumn();
$total_pages = ceil($total_reservations / $limit);

// Statistiques
$stats = get_admin_stats($pdo);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Supervision des réservations";
$current_page = "reservations";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Réservations']
];
?>

<link rel="stylesheet" href="assets/css/reservations.css">

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
            <h1 class="page-title">Supervision des réservations</h1>
            <p class="page-subtitle">Vue d'ensemble de toutes les réservations de la plateforme</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid-4">
        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12V19C21 19.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V8C3 7.46957 3.21071 6.96086 3.58579 6.58579C3.96086 6.21071 4.46957 6 5 6H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total</p>
                <p class="stat-value"><?= $stats['total_reservations'] ?></p>
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
                <p class="stat-label">Confirmées</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'CONFIRMEE' AND date_suppression IS NULL");
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
                <p class="stat-label">En attente</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'EN_ATTENTE' AND date_suppression IS NULL");
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
                <p class="stat-label">Annulées</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'ANNULEE' AND date_suppression IS NULL");
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
                    Toutes
                </a>
                <a href="?statut=CONFIRMEE" class="filter-tab <?= $statut_filter === 'CONFIRMEE' ? 'active' : '' ?>">
                    Confirmées
                </a>
                <a href="?statut=EN_ATTENTE" class="filter-tab <?= $statut_filter === 'EN_ATTENTE' ? 'active' : '' ?>">
                    En attente
                </a>
                <a href="?statut=ANNULEE" class="filter-tab <?= $statut_filter === 'ANNULEE' ? 'active' : '' ?>">
                    Annulées
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des réservations -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Réservations (<?= $total_reservations ?>)</h2>
        </div>
        <div class="card-body card-body-table">
            <?php if (empty($reservations)): ?>
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12V19C21 19.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V8C3 7.46957 3.21071 6.96086 3.58579 6.58579C3.96086 6.21071 4.46957 6 5 6H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="empty-text">Aucune réservation trouvée</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Vol</th>
                                <th>Client</th>
                                <th>Passagers</th>
                                <th>Classe</th>
                                <th>Prix total</th>
                                <th>Réservé le</th>
                                <th>Statut</th>
                                <th class="table-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td>
                                        <strong class="reservation-ref"><?= htmlspecialchars($reservation['numero_reference']) ?></strong>
                                    </td>
                                    <td>
                                        <div class="vol-info">
                                            <strong><?= htmlspecialchars($reservation['numero_vol']) ?></strong>
                                            <small><?= htmlspecialchars($reservation['aeroport_depart']) ?> → <?= htmlspecialchars($reservation['aeroport_arrivee']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="client-info">
                                            <strong><?= htmlspecialchars($reservation['client_prenom'] . ' ' . $reservation['client_nom']) ?></strong>
                                            <small><?= htmlspecialchars($reservation['client_email']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= $reservation['nombre_passagers'] ?></td>
                                    <td>
                                        <span class="badge badge-classe-<?= strtolower($reservation['type_classe']) ?>">
                                            <?= htmlspecialchars($reservation['type_classe']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="prix"><?= number_format($reservation['prix_total'], 2, ',', ' ') ?> €</strong>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($reservation['date_reservation'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($reservation['statut']) ?>">
                                            <?= htmlspecialchars($reservation['statut']) ?>
                                        </span>
                                    </td>
                                    <td class="table-actions">
                                        <button class="btn-icon" onclick="openReservationModal(<?= $reservation['id_reservation'] ?>)" title="Voir détails">
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

<!-- Modal Détails Réservation -->
<div id="reservationModal" class="modal">
    <div class="modal-content modal-content-lg">
        <div class="modal-header">
            <h3 class="modal-title">Détails de la réservation</h3>
            <button class="modal-close" onclick="closeModal('reservationModal')">&times;</button>
        </div>
        <div id="reservationModalBody" class="modal-body">
            <div class="loading">Chargement...</div>
        </div>
    </div>
</div>

<script>
function openReservationModal(reservationId) {
    const modal = document.getElementById('reservationModal');
    const body = document.getElementById('reservationModalBody');
    modal.classList.add('active');
    body.innerHTML = '<div class="loading">Chargement...</div>';

    // Charger les détails via fetch
    fetch(`/src/controllers/admin/get_reservation_details.php?id=${reservationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                body.innerHTML = generateReservationDetailsHTML(data.reservation, data.passagers);
            } else {
                body.innerHTML = '<p class="error-text">Erreur lors du chargement des détails</p>';
            }
        })
        .catch(error => {
            body.innerHTML = '<p class="error-text">Erreur lors du chargement des détails</p>';
        });
}

function generateReservationDetailsHTML(reservation, passagers) {
    let html = '<div class="reservation-details">';

    html += '<div class="detail-section">';
    html += '<h4 class="detail-section-title">Informations générales</h4>';
    html += '<div class="detail-grid">';
    html += `<div class="detail-row"><span class="detail-label">Référence:</span><span><strong>${reservation.numero_reference}</strong></span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Statut:</span><span><span class="badge badge-${reservation.statut.toLowerCase()}">${reservation.statut}</span></span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Date de réservation:</span><span>${new Date(reservation.date_reservation).toLocaleString('fr-FR')}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Prix total:</span><span><strong>${parseFloat(reservation.prix_total).toFixed(2)} €</strong></span></div>`;
    html += '</div></div>';

    html += '<div class="detail-section">';
    html += '<h4 class="detail-section-title">Vol</h4>';
    html += '<div class="detail-grid">';
    html += `<div class="detail-row"><span class="detail-label">Numéro de vol:</span><span><strong>${reservation.numero_vol}</strong></span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Route:</span><span>${reservation.aeroport_depart} → ${reservation.aeroport_arrivee}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Date départ:</span><span>${new Date(reservation.date_depart).toLocaleDateString('fr-FR')} à ${reservation.heure_depart}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Classe:</span><span><span class="badge badge-classe-${reservation.type_classe.toLowerCase()}">${reservation.type_classe}</span></span></div>`;
    html += '</div></div>';

    html += '<div class="detail-section">';
    html += '<h4 class="detail-section-title">Client</h4>';
    html += '<div class="detail-grid">';
    html += `<div class="detail-row"><span class="detail-label">Nom:</span><span>${reservation.client_prenom} ${reservation.client_nom}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Email:</span><span>${reservation.client_email}</span></div>`;
    if (reservation.client_telephone) {
        html += `<div class="detail-row"><span class="detail-label">Téléphone:</span><span>${reservation.client_telephone}</span></div>`;
    }
    html += '</div></div>';

    if (passagers && passagers.length > 0) {
        html += '<div class="detail-section">';
        html += '<h4 class="detail-section-title">Passagers</h4>';
        html += '<div class="passagers-list">';
        passagers.forEach((passager, index) => {
            html += `<div class="passager-card">`;
            html += `<strong>Passager ${index + 1}</strong>`;
            html += `<p>${passager.nom} ${passager.prenom}</p>`;
            if (passager.numero_siege) {
                html += `<p>Siège: <strong>${passager.numero_siege}</strong></p>`;
            }
            html += `</div>`;
        });
        html += '</div></div>';
    }

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
