<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/admin_data.php';

// Filtre statut
$statut_filter = $_GET['statut'] ?? 'EN_ATTENTE';

// Récupérer les demandes
$demandes = get_demandes_agences($pdo, $statut_filter);

// Statistiques
$stats = get_admin_stats($pdo);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Demandes d'agences";
$current_page = "demandes-agences";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Demandes Agences']
];
?>

<link rel="stylesheet" href="assets/css/demandes.css">

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
            <h1 class="page-title">Demandes d'agences</h1>
            <p class="page-subtitle">Valider ou rejeter les demandes d'inscription d'agences</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid-3">
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
                <p class="stat-value"><?= $stats['demandes_agences_attente'] ?></p>
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
                <p class="stat-label">Total Agences</p>
                <p class="stat-value"><?= $stats['total_agences'] ?></p>
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
                <p class="stat-label">Rejets</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM demandes_agence WHERE statut_demande = 'REJETEE'");
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
                <a href="?statut=EN_ATTENTE" class="filter-tab <?= $statut_filter === 'EN_ATTENTE' ? 'active' : '' ?>">
                    En attente
                    <?php if ($stats['demandes_agences_attente'] > 0): ?>
                        <span class="tab-badge"><?= $stats['demandes_agences_attente'] ?></span>
                    <?php endif; ?>
                </a>
                <a href="?statut=VALIDEE" class="filter-tab <?= $statut_filter === 'VALIDEE' ? 'active' : '' ?>">
                    Validées
                </a>
                <a href="?statut=REJETEE" class="filter-tab <?= $statut_filter === 'REJETEE' ? 'active' : '' ?>">
                    Rejetées
                </a>
                <a href="?statut=tous" class="filter-tab <?= $statut_filter === 'tous' ? 'active' : '' ?>">
                    Toutes
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des demandes -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Demandes (<?= count($demandes) ?>)</h2>
        </div>
        <div class="card-body">
            <?php if (empty($demandes)): ?>
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="empty-text">Aucune demande trouvée</p>
                </div>
            <?php else: ?>
                <div class="demandes-list">
                    <?php foreach ($demandes as $demande): ?>
                        <div class="demande-card">
                            <div class="demande-header">
                                <div class="demande-info">
                                    <h3 class="demande-nom"><?= htmlspecialchars($demande['nom_agence']) ?></h3>
                                    <p class="demande-meta">
                                        Par <strong><?= htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']) ?></strong>
                                        • <?= htmlspecialchars($demande['email']) ?>
                                        • Le <?= date('d/m/Y à H:i', strtotime($demande['date_demande'])) ?>
                                    </p>
                                </div>
                                <span class="status-badge status-<?= strtolower($demande['statut_demande']) ?>">
                                    <?= htmlspecialchars($demande['statut_demande']) ?>
                                </span>
                            </div>

                            <div class="demande-body">
                                <div class="demande-details">
                                    <div class="detail-item">
                                        <svg class="detail-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2"/>
                                            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        <div>
                                            <p class="detail-label">Adresse</p>
                                            <p class="detail-value"><?= htmlspecialchars($demande['adresse']) ?></p>
                                        </div>
                                    </div>

                                    <div class="detail-item">
                                        <svg class="detail-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27099 2.11999 4.18C2.095 3.90347 2.12787 3.62476 2.21649 3.36162C2.30512 3.09849 2.44756 2.85669 2.63476 2.65162C2.82196 2.44655 3.0498 2.28271 3.30379 2.17052C3.55777 2.05833 3.83233 2.00026 4.10999 2H7.10999C7.59524 1.99522 8.06572 2.16708 8.43369 2.48353C8.80166 2.79999 9.04201 3.23945 9.10999 3.72C9.23662 4.68007 9.47144 5.62273 9.80999 6.53C9.94454 6.88792 9.97366 7.27691 9.8939 7.65088C9.81415 8.02485 9.62886 8.36811 9.35999 8.64L8.08999 9.91C9.51355 12.4135 11.5864 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9751 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0555 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div>
                                            <p class="detail-label">Téléphone</p>
                                            <p class="detail-value"><?= htmlspecialchars($demande['telephone']) ?></p>
                                        </div>
                                    </div>

                                    <?php if ($demande['site_web']): ?>
                                        <div class="detail-item">
                                            <svg class="detail-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                                <path d="M2 12H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12 2C14.5013 4.73835 15.9228 8.29203 16 12C15.9228 15.708 14.5013 19.2616 12 22C9.49872 19.2616 8.07725 15.708 8 12C8.07725 8.29203 9.49872 4.73835 12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <div>
                                                <p class="detail-label">Site web</p>
                                                <p class="detail-value"><a href="<?= htmlspecialchars($demande['site_web']) ?>" target="_blank"><?= htmlspecialchars($demande['site_web']) ?></a></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($demande['numero_licence']): ?>
                                        <div class="detail-item">
                                            <svg class="detail-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <div>
                                                <p class="detail-label">Numéro de licence</p>
                                                <p class="detail-value"><?= htmlspecialchars($demande['numero_licence']) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($demande['statut_demande'] === 'REJETEE' && $demande['raison_rejet']): ?>
                                    <div class="demande-rejection">
                                        <strong>Raison du rejet:</strong>
                                        <p><?= htmlspecialchars($demande['raison_rejet']) ?></p>
                                        <?php if ($demande['date_traitement']): ?>
                                            <small>Rejetée le <?= date('d/m/Y à H:i', strtotime($demande['date_traitement'])) ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($demande['statut_demande'] === 'EN_ATTENTE'): ?>
                                <div class="demande-actions">
                                    <button class="btn btn-success" onclick="openValidateModal(<?= $demande['id_demande'] ?>, '<?= htmlspecialchars($demande['nom_agence'], ENT_QUOTES) ?>')">
                                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Valider
                                    </button>
                                    <button class="btn btn-error" onclick="openRejectModal(<?= $demande['id_demande'] ?>, '<?= htmlspecialchars($demande['nom_agence'], ENT_QUOTES) ?>')">
                                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                            <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        Rejeter
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal Validation -->
<div id="validateModal" class="modal">
    <div class="modal-content modal-content-sm">
        <div class="modal-header">
            <h3 class="modal-title">Valider la demande</h3>
            <button class="modal-close" onclick="closeModal('validateModal')">&times;</button>
        </div>
        <form method="POST" action="/src/controllers/admin/valider_demande_agence.php">
            <div class="modal-body">
                <input type="hidden" name="demande_id" id="validate_demande_id">
                <input type="hidden" name="action" value="valider">
                <div class="alert alert-success">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div>
                        En validant cette demande, vous allez créer une nouvelle agence et l'utilisateur recevra un email de confirmation.
                    </div>
                </div>
                <p>Confirmez la validation de <strong id="validate_demande_nom"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('validateModal')">Annuler</button>
                <button type="submit" class="btn btn-success">Valider la demande</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Rejet -->
<div id="rejectModal" class="modal">
    <div class="modal-content modal-content-sm">
        <div class="modal-header">
            <h3 class="modal-title">Rejeter la demande</h3>
            <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
        </div>
        <form method="POST" action="/src/controllers/admin/valider_demande_agence.php">
            <div class="modal-body">
                <input type="hidden" name="demande_id" id="reject_demande_id">
                <input type="hidden" name="action" value="rejeter">
                <p>Rejeter la demande de <strong id="reject_demande_nom"></strong></p>
                <div class="form-group">
                    <label for="raison_rejet" class="form-label">Raison du rejet <span class="required">*</span></label>
                    <textarea name="raison_rejet" id="raison_rejet" class="form-textarea" rows="4" required placeholder="Expliquez pourquoi cette demande est rejetée..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('rejectModal')">Annuler</button>
                <button type="submit" class="btn btn-error">Rejeter la demande</button>
            </div>
        </form>
    </div>
</div>

<script>
function openValidateModal(demandeId, demandeNom) {
    document.getElementById('validate_demande_id').value = demandeId;
    document.getElementById('validate_demande_nom').textContent = demandeNom;
    document.getElementById('validateModal').classList.add('active');
}

function openRejectModal(demandeId, demandeNom) {
    document.getElementById('reject_demande_id').value = demandeId;
    document.getElementById('reject_demande_nom').textContent = demandeNom;
    document.getElementById('rejectModal').classList.add('active');
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
