<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/admin_data.php';

// Filtres
$type_filter = $_GET['type'] ?? 'tous';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Récupérer les utilisateurs
$users = get_all_users($pdo, $type_filter, $search, $limit, $offset);

// Compter le total pour la pagination
$count_query = "SELECT COUNT(*) FROM utilisateurs WHERE date_suppression IS NULL";
$count_params = [];

if ($type_filter !== 'tous') {
    $count_query .= " AND type_utilisateur = :type";
    $count_params[':type'] = $type_filter;
}

if (!empty($search)) {
    $count_query .= " AND (nom LIKE :search OR prenom LIKE :search OR email LIKE :search)";
    $count_params[':search'] = '%' . $search . '%';
}

$stmt = $pdo->prepare($count_query);
$stmt->execute($count_params);
$total_users = $stmt->fetchColumn();
$total_pages = ceil($total_users / $limit);

// Statistiques globales
$stats = get_admin_stats($pdo);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Gestion des utilisateurs";
$current_page = "utilisateurs";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Utilisateurs']
];
?>

<link rel="stylesheet" href="assets/css/utilisateurs.css">

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
            <h1 class="page-title">Gestion des utilisateurs</h1>
            <p class="page-subtitle">Gérer tous les utilisateurs de la plateforme</p>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Clients</p>
                <p class="stat-value"><?= $stats['total_clients'] ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Agences</p>
                <p class="stat-value"><?= $stats['total_agences'] ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-warning">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Compagnies</p>
                <p class="stat-value"><?= $stats['total_compagnies'] ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total</p>
                <p class="stat-value"><?= $stats['total_utilisateurs'] ?></p>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card">
        <div class="card-body">
            <form method="GET" class="filters-form">
                <div class="filters-row">
                    <div class="form-group">
                        <label for="type" class="form-label">Type d'utilisateur</label>
                        <select name="type" id="type" class="form-select">
                            <option value="tous" <?= $type_filter === 'tous' ? 'selected' : '' ?>>Tous</option>
                            <option value="CLIENT" <?= $type_filter === 'CLIENT' ? 'selected' : '' ?>>Clients</option>
                            <option value="AGENCY" <?= $type_filter === 'AGENCY' ? 'selected' : '' ?>>Agences</option>
                            <option value="COMPAGNIE" <?= $type_filter === 'COMPAGNIE' ? 'selected' : '' ?>>Compagnies</option>
                            <option value="ADMIN" <?= $type_filter === 'ADMIN' ? 'selected' : '' ?>>Administrateurs</option>
                        </select>
                    </div>

                    <div class="form-group form-group-search">
                        <label for="search" class="form-label">Rechercher</label>
                        <input type="text" name="search" id="search" class="form-input"
                               placeholder="Nom, prénom, email..." value="<?= htmlspecialchars($search) ?>">
                    </div>

                    <div class="form-group form-group-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Rechercher
                        </button>
                        <?php if ($type_filter !== 'tous' || !empty($search)): ?>
                            <a href="utilisateurs.php" class="btn btn-outline">Réinitialiser</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Utilisateurs (<?= $total_users ?>)</h2>
        </div>
        <div class="card-body card-body-table">
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <p class="empty-text">Aucun utilisateur trouvé</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Inscription</th>
                                <th class="table-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar-sm">
                                                <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p class="user-name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></p>
                                                <?php if ($user['entity_name']): ?>
                                                    <p class="user-entity"><?= htmlspecialchars($user['entity_name']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($user['type_utilisateur']) ?>">
                                            <?= htmlspecialchars($user['type_utilisateur']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['date_suspension']): ?>
                                            <span class="status-badge status-suspended">Suspendu</span>
                                        <?php else: ?>
                                            <span class="status-badge status-active">Actif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></td>
                                    <td class="table-actions">
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="openUserModal(<?= $user['id_utilisateur'] ?>)" title="Voir détails">
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 12S5 4 12 4C19 4 23 12 23 12S19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor" stroke-width="2"/>
                                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </button>
                                            <?php if ($user['type_utilisateur'] !== 'ADMIN'): ?>
                                                <?php if ($user['date_suspension']): ?>
                                                    <form method="POST" action="/src/controllers/admin/gerer_utilisateur.php" style="display: inline;">
                                                        <input type="hidden" name="user_id" value="<?= $user['id_utilisateur'] ?>">
                                                        <input type="hidden" name="action" value="activer">
                                                        <button type="submit" class="btn-icon btn-icon-success" title="Activer">
                                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn-icon btn-icon-warning" onclick="openSuspendModal(<?= $user['id_utilisateur'] ?>, '<?= htmlspecialchars($user['prenom'] . ' ' . $user['nom'], ENT_QUOTES) ?>')" title="Suspendre">
                                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                                            <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn-icon btn-icon-error" onclick="openDeleteModal(<?= $user['id_utilisateur'] ?>, '<?= htmlspecialchars($user['prenom'] . ' ' . $user['nom'], ENT_QUOTES) ?>')" title="Supprimer">
                                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                        </div>
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
                            <a href="?page=<?= $page - 1 ?>&type=<?= urlencode($type_filter) ?>&search=<?= urlencode($search) ?>"
                               class="pagination-link">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Précédent
                            </a>
                        <?php endif; ?>

                        <span class="pagination-info">Page <?= $page ?> sur <?= $total_pages ?></span>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&type=<?= urlencode($type_filter) ?>&search=<?= urlencode($search) ?>"
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

<!-- Modal Détails utilisateur -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Détails de l'utilisateur</h3>
            <button class="modal-close" onclick="closeModal('userModal')">&times;</button>
        </div>
        <div id="userModalBody" class="modal-body">
            <div class="loading">Chargement...</div>
        </div>
    </div>
</div>

<!-- Modal Suspension -->
<div id="suspendModal" class="modal">
    <div class="modal-content modal-content-sm">
        <div class="modal-header">
            <h3 class="modal-title">Suspendre l'utilisateur</h3>
            <button class="modal-close" onclick="closeModal('suspendModal')">&times;</button>
        </div>
        <form method="POST" action="/src/controllers/admin/gerer_utilisateur.php">
            <div class="modal-body">
                <input type="hidden" name="user_id" id="suspend_user_id">
                <input type="hidden" name="action" value="suspendre">
                <p>Êtes-vous sûr de vouloir suspendre <strong id="suspend_user_name"></strong> ?</p>
                <div class="form-group">
                    <label for="raison_suspension" class="form-label">Raison de la suspension</label>
                    <textarea name="raison_suspension" id="raison_suspension" class="form-textarea" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('suspendModal')">Annuler</button>
                <button type="submit" class="btn btn-warning">Suspendre</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Suppression -->
<div id="deleteModal" class="modal">
    <div class="modal-content modal-content-sm">
        <div class="modal-header">
            <h3 class="modal-title">Supprimer l'utilisateur</h3>
            <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
        </div>
        <form method="POST" action="/src/controllers/admin/gerer_utilisateur.php">
            <div class="modal-body">
                <input type="hidden" name="user_id" id="delete_user_id">
                <input type="hidden" name="action" value="supprimer">
                <div class="alert alert-error">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="16" r="1" fill="currentColor"/>
                    </svg>
                    <div>
                        <strong>Action irréversible</strong><br>
                        Cette action supprimera définitivement l'utilisateur et toutes ses données associées.
                    </div>
                </div>
                <p>Confirmez la suppression de <strong id="delete_user_name"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('deleteModal')">Annuler</button>
                <button type="submit" class="btn btn-error">Supprimer définitivement</button>
            </div>
        </form>
    </div>
</div>

<script>
// Gestion des modales
function openUserModal(userId) {
    const modal = document.getElementById('userModal');
    const body = document.getElementById('userModalBody');
    modal.classList.add('active');
    body.innerHTML = '<div class="loading">Chargement...</div>';

    // Charger les détails via fetch
    fetch(`/src/controllers/admin/get_user_details.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                body.innerHTML = generateUserDetailsHTML(data.user);
            } else {
                body.innerHTML = '<p class="error-text">Erreur lors du chargement des détails</p>';
            }
        })
        .catch(error => {
            body.innerHTML = '<p class="error-text">Erreur lors du chargement des détails</p>';
        });
}

function generateUserDetailsHTML(user) {
    let html = '<div class="user-details">';
    html += `<div class="detail-row"><span class="detail-label">Nom complet:</span><span>${user.prenom} ${user.nom}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Email:</span><span>${user.email}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Téléphone:</span><span>${user.telephone || 'Non renseigné'}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Type:</span><span><span class="badge badge-${user.type_utilisateur.toLowerCase()}">${user.type_utilisateur}</span></span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Statut:</span><span>${user.date_suspension ? '<span class="status-badge status-suspended">Suspendu</span>' : '<span class="status-badge status-active">Actif</span>'}</span></div>`;
    html += `<div class="detail-row"><span class="detail-label">Inscription:</span><span>${new Date(user.date_inscription).toLocaleDateString('fr-FR')}</span></div>`;

    if (user.entity_name) {
        html += `<div class="detail-row"><span class="detail-label">${user.type_utilisateur === 'AGENCY' ? 'Agence' : 'Compagnie'}:</span><span>${user.entity_name}</span></div>`;
    }

    if (user.date_suspension) {
        html += `<div class="detail-row"><span class="detail-label">Suspendu le:</span><span>${new Date(user.date_suspension).toLocaleDateString('fr-FR')}</span></div>`;
        if (user.raison_suspension) {
            html += `<div class="detail-row"><span class="detail-label">Raison:</span><span>${user.raison_suspension}</span></div>`;
        }
    }

    html += '</div>';
    return html;
}

function openSuspendModal(userId, userName) {
    document.getElementById('suspend_user_id').value = userId;
    document.getElementById('suspend_user_name').textContent = userName;
    document.getElementById('suspendModal').classList.add('active');
}

function openDeleteModal(userId, userName) {
    document.getElementById('delete_user_id').value = userId;
    document.getElementById('delete_user_name').textContent = userName;
    document.getElementById('deleteModal').classList.add('active');
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
