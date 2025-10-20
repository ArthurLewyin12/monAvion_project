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

// Récupérer l'ID du vol
$vol_id = intval($_GET['id'] ?? 0);

if (!$vol_id) {
    $_SESSION['error_message'] = "Vol introuvable.";
    header("Location: mes-vols.php");
    exit();
}

// Récupérer les informations de la compagnie
$compagnie_info = get_compagnie_info($pdo, $compagnie_id);
$_SESSION['compagnie_nom'] = $compagnie_info['nom_compagnie'] ?? 'Ma Compagnie';

// Récupérer les détails du vol
$vol = get_vol_details_for_compagnie($pdo, $vol_id, $compagnie_id);

if (!$vol) {
    $_SESSION['error_message'] = "Vol introuvable ou vous n'avez pas les droits pour le consulter.";
    header("Location: mes-vols.php");
    exit();
}

// Récupérer les réservations du vol
$reservations = get_vol_reservations($pdo, $vol_id, $compagnie_id);

// Calculer les statistiques
$total_reservations = count($reservations);
$reservations_confirmees = count(array_filter($reservations, fn($r) => $r['statut'] === 'CONFIRMEE'));
$reservations_en_attente = count(array_filter($reservations, fn($r) => $r['statut'] === 'EN_ATTENTE'));

// Calculer le taux de remplissage
$taux_remplissage = $vol['nombre_sieges_total'] > 0
    ? round(($total_reservations / $vol['nombre_sieges_total']) * 100, 1)
    : 0;

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Vol " . htmlspecialchars($vol['numero_vol']);
$current_page = "mes-vols";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Mes vols', 'url' => 'mes-vols.php'],
    ['label' => $vol['numero_vol']]
];
?>

<link rel="stylesheet" href="assets/css/detail-vol.css">

<?php include __DIR__ . '/layouts/header.php'; ?>

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

    <!-- Header du vol -->
    <div class="vol-header">
        <div class="vol-header-main">
            <div class="vol-numero-badge">
                <svg class="vol-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="vol-numero"><?= htmlspecialchars($vol['numero_vol']) ?></span>
            </div>
            <div class="vol-route">
                <span class="vol-airport"><?= htmlspecialchars($vol['aeroport_depart']) ?></span>
                <svg class="vol-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="vol-airport"><?= htmlspecialchars($vol['aeroport_arrivee']) ?></span>
            </div>
            <span class="badge badge-<?= strtolower($vol['statut']) ?>">
                <?= htmlspecialchars($vol['statut']) ?>
            </span>
        </div>
        <div class="vol-header-actions">
            <?php if ($vol['statut'] !== 'ANNULE'): ?>
                <button class="btn btn-sm btn-outline" onclick="openStatusModal()">
                    Changer statut
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Réservations</p>
                <p class="stat-value"><?= $total_reservations ?> / <?= $vol['nombre_sieges_total'] ?></p>
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
                <p class="stat-value"><?= $reservations_confirmees ?></p>
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
                <p class="stat-value"><?= $reservations_en_attente ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 16V12M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Taux de remplissage</p>
                <p class="stat-value"><?= $taux_remplissage ?>%</p>
            </div>
        </div>
    </div>

    <!-- Détails du vol et tarifs -->
    <div class="details-grid">
        <!-- Informations du vol -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Informations du vol</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Avion</span>
                        <span class="info-value"><?= htmlspecialchars($vol['avion_modele']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Capacité totale</span>
                        <span class="info-value"><?= $vol['nombre_sieges_total'] ?> sièges</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de départ</span>
                        <span class="info-value"><?= date('d/m/Y à H:i', strtotime($vol['date_depart'])) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date d'arrivée</span>
                        <span class="info-value"><?= date('d/m/Y à H:i', strtotime($vol['date_arrivee'])) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarifs -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Tarifs</h2>
            </div>
            <div class="card-body">
                <div class="tarifs-list">
                    <?php foreach ($vol['tarifs'] as $tarif): ?>
                        <div class="tarif-item">
                            <span class="tarif-classe"><?= htmlspecialchars($tarif['type_classe']) ?></span>
                            <span class="tarif-prix"><?= number_format($tarif['prix'], 2, ',', ' ') ?> €</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des réservations -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Réservations (<?= $total_reservations ?>)</h2>
        </div>
        <div class="card-body">
            <?php if (empty($reservations)): ?>
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <p class="empty-text">Aucune réservation pour ce vol</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>N° Réservation</th>
                                <th>Passager</th>
                                <th>Email</th>
                                <th>Classe</th>
                                <th>Siège</th>
                                <th>Prix</th>
                                <th>Source</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td class="td-numero"><?= htmlspecialchars($reservation['numero_reservation']) ?></td>
                                    <td><?= htmlspecialchars($reservation['passager_prenom'] . ' ' . $reservation['passager_nom']) ?></td>
                                    <td><?= htmlspecialchars($reservation['passager_email']) ?></td>
                                    <td><?= htmlspecialchars($reservation['type_classe']) ?></td>
                                    <td class="td-center"><?= htmlspecialchars($reservation['numero_siege'] ?? 'N/A') ?></td>
                                    <td><?= number_format($reservation['prix'], 2, ',', ' ') ?> €</td>
                                    <td><?= htmlspecialchars($reservation['source_reservation']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($reservation['statut']) ?>">
                                            <?= htmlspecialchars($reservation['statut']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal changement de statut -->
<div id="statusModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeStatusModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Changer le statut du vol</h3>
            <button class="modal-close" onclick="closeStatusModal()">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="/src/controllers/compagnie/changer_statut_vol.php">
            <input type="hidden" name="vol_id" value="<?= $vol_id ?>">
            <div class="modal-body">
                <p>Statut actuel : <strong><?= htmlspecialchars($vol['statut']) ?></strong></p>
                <div class="form-group">
                    <label class="form-label">Nouveau statut</label>
                    <select name="statut" class="form-select" required>
                        <option value="">Sélectionnez un statut</option>
                        <?php if ($vol['statut'] === 'PROGRAMME'): ?>
                            <option value="RETARDE">RETARDÉ</option>
                            <option value="ANNULE">ANNULÉ</option>
                        <?php elseif ($vol['statut'] === 'RETARDE'): ?>
                            <option value="PROGRAMME">PROGRAMMÉ</option>
                            <option value="ANNULE">ANNULÉ</option>
                        <?php endif; ?>
                    </select>
                </div>
                <?php if ($total_reservations > 0): ?>
                    <div class="modal-warning">
                        <svg class="warning-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="12" cy="16" r="1" fill="currentColor"/>
                        </svg>
                        <span>Ce vol a <?= $total_reservations ?> réservation(s). Les clients seront notifiés du changement.</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeStatusModal()">Annuler</button>
                <button type="submit" class="btn btn-primary">Confirmer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatusModal() {
    document.getElementById('statusModal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStatusModal();
    }
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
