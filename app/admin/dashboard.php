<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/admin_data.php';

// Récupérer les statistiques
$stats = get_admin_stats($pdo);
$recent_activities = get_recent_activities($pdo, 10);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Dashboard Administrateur";
$current_page = "dashboard";
?>

<link rel="stylesheet" href="assets/css/dashboard.css">

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
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">Vue d'ensemble de la plateforme</h1>
            <p class="dashboard-subtitle">Statistiques et activités récentes</p>
        </div>
    </div>

    <!-- Stats utilisateurs -->
    <div class="section-title">Utilisateurs</div>
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
                <p class="stat-label">Total Utilisateurs</p>
                <p class="stat-value"><?= $stats['total_utilisateurs'] ?></p>
            </div>
        </div>
    </div>

    <!-- Stats demandes et activités -->
    <div class="section-title">Demandes en attente</div>
    <div class="stats-grid-2">
        <div class="stat-card">
            <div class="stat-icon stat-icon-error">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="16" r="1" fill="currentColor"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Demandes Agences</p>
                <p class="stat-value"><?= $stats['demandes_agences_attente'] ?></p>
                <?php if ($stats['demandes_agences_attente'] > 0): ?>
                    <a href="demandes-agences.php" class="stat-link">Traiter →</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-error">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="16" r="1" fill="currentColor"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Demandes Compagnies</p>
                <p class="stat-value"><?= $stats['demandes_compagnies_attente'] ?></p>
                <?php if ($stats['demandes_compagnies_attente'] > 0): ?>
                    <a href="demandes-compagnies.php" class="stat-link">Traiter →</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-error">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Messages non traités</p>
                <p class="stat-value"><?= $stats['messages_non_traites'] ?></p>
                <?php if ($stats['messages_non_traites'] > 0): ?>
                    <a href="messages-contact.php" class="stat-link">Voir →</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats plateforme -->
    <div class="section-title">Activité de la plateforme</div>
    <div class="stats-grid-2">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Vols</p>
                <p class="stat-value"><?= $stats['total_vols'] ?></p>
                <a href="vols.php" class="stat-link">Voir tous →</a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12V19C21 19.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V8C3 7.46957 3.21071 6.96086 3.58579 6.58579C3.96086 6.21071 4.46957 6 5 6H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Réservations</p>
                <p class="stat-value"><?= $stats['total_reservations'] ?></p>
                <a href="reservations.php" class="stat-link">Voir toutes →</a>
            </div>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="section-title">Activité récente</div>
    <div class="card">
        <div class="card-body">
            <?php if (empty($recent_activities)): ?>
                <div class="empty-state">
                    <p class="empty-text">Aucune activité récente</p>
                </div>
            <?php else: ?>
                <div class="activities-list">
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon activity-icon-<?= $activity['type'] ?>">
                                <?php if ($activity['type'] === 'reservation'): ?>
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="activity-content">
                                <p class="activity-text">
                                    <strong><?= htmlspecialchars($activity['user_name']) ?></strong>
                                    <?= $activity['type'] === 'reservation' ? 'a réservé le vol' : 'a créé le vol' ?>
                                    <strong><?= htmlspecialchars($activity['details']) ?></strong>
                                </p>
                                <p class="activity-time"><?= date('d/m/Y à H:i', strtotime($activity['date_action'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
