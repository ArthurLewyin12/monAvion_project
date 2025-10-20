<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/admin_data.php';

// Filtre statut
$statut_filter = $_GET['statut'] ?? 'NON_TRAITE';

// Récupérer les messages
$messages = get_messages_contact($pdo, $statut_filter);

// Statistiques
$stats = get_admin_stats($pdo);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Messages de contact";
$current_page = "messages-contact";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Messages']
];
?>

<link rel="stylesheet" href="assets/css/messages.css">

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
            <h1 class="page-title">Messages de contact</h1>
            <p class="page-subtitle">Gérer les messages reçus depuis le formulaire de contact</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid-3">
        <div class="stat-card">
            <div class="stat-icon stat-icon-error">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Non traités</p>
                <p class="stat-value"><?= $stats['messages_non_traites'] ?></p>
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
                <p class="stat-label">Traités</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM messages_contact WHERE statut = 'TRAITE'");
                    echo $stmt->fetchColumn();
                    ?>
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total</p>
                <p class="stat-value">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM messages_contact");
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
                <a href="?statut=NON_TRAITE" class="filter-tab <?= $statut_filter === 'NON_TRAITE' ? 'active' : '' ?>">
                    Non traités
                    <?php if ($stats['messages_non_traites'] > 0): ?>
                        <span class="tab-badge"><?= $stats['messages_non_traites'] ?></span>
                    <?php endif; ?>
                </a>
                <a href="?statut=TRAITE" class="filter-tab <?= $statut_filter === 'TRAITE' ? 'active' : '' ?>">
                    Traités
                </a>
                <a href="?statut=tous" class="filter-tab <?= $statut_filter === 'tous' ? 'active' : '' ?>">
                    Tous
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des messages -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Messages (<?= count($messages) ?>)</h2>
        </div>
        <div class="card-body">
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="empty-text">Aucun message trouvé</p>
                </div>
            <?php else: ?>
                <div class="messages-list">
                    <?php foreach ($messages as $message): ?>
                        <div class="message-card <?= $message['statut'] === 'NON_TRAITE' ? 'message-unread' : '' ?>">
                            <div class="message-header">
                                <div class="message-info">
                                    <h3 class="message-nom"><?= htmlspecialchars($message['nom']) ?></h3>
                                    <p class="message-meta">
                                        <?= htmlspecialchars($message['email']) ?>
                                        <?php if ($message['telephone']): ?>
                                            • <?= htmlspecialchars($message['telephone']) ?>
                                        <?php endif; ?>
                                        • Le <?= date('d/m/Y à H:i', strtotime($message['date_envoi'])) ?>
                                    </p>
                                </div>
                                <span class="status-badge status-<?= strtolower($message['statut']) ?>">
                                    <?= $message['statut'] === 'NON_TRAITE' ? 'Non traité' : 'Traité' ?>
                                </span>
                            </div>

                            <div class="message-body">
                                <div class="message-sujet">
                                    <strong>Sujet:</strong> <?= htmlspecialchars($message['sujet']) ?>
                                </div>
                                <div class="message-content">
                                    <?= nl2br(htmlspecialchars($message['message'])) ?>
                                </div>

                                <?php if ($message['statut'] === 'TRAITE' && $message['date_traitement']): ?>
                                    <div class="message-traitement">
                                        <svg class="message-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Traité le <?= date('d/m/Y à H:i', strtotime($message['date_traitement'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="message-actions">
                                <?php if ($message['statut'] === 'NON_TRAITE'): ?>
                                    <form method="POST" action="/src/controllers/admin/traiter_message.php" style="display: inline;">
                                        <input type="hidden" name="message_id" value="<?= $message['id_message'] ?>">
                                        <input type="hidden" name="action" value="marquer_traite">
                                        <button type="submit" class="btn btn-success">
                                            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Marquer comme traité
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="/src/controllers/admin/traiter_message.php" style="display: inline;">
                                        <input type="hidden" name="message_id" value="<?= $message['id_message'] ?>">
                                        <input type="hidden" name="action" value="marquer_non_traite">
                                        <button type="submit" class="btn btn-outline">
                                            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 8L10.89 13.26C11.2187 13.4793 11.6049 13.5963 12 13.5963C12.3951 13.5963 12.7813 13.4793 13.11 13.26L21 8M5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7C21 6.46957 20.7893 5.96086 20.4142 5.58579C20.0391 5.21071 19.5304 5 19 5H5C4.46957 5 3.96086 5.21071 3.58579 5.58579C3.21071 5.96086 3 6.46957 3 7V17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Marquer comme non traité
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="btn btn-outline">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 8L10.89 13.26C11.2187 13.4793 11.6049 13.5963 12 13.5963C12.3951 13.5963 12.7813 13.4793 13.11 13.26L21 8M5 19H19C19.5304 19 20.0391 18.7893 20.4142 18.4142C20.7893 18.0391 21 17.5304 21 17V7C21 6.46957 20.7893 5.96086 20.4142 5.58579C20.0391 5.21071 19.5304 5 19 5H5C4.46957 5 3.96086 5.21071 3.58579 5.58579C3.21071 5.96086 3 6.46957 3 7V17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Répondre par email
                                </a>

                                <?php if ($message['telephone']): ?>
                                    <a href="tel:<?= htmlspecialchars($message['telephone']) ?>" class="btn btn-outline">
                                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27099 2.11999 4.18C2.095 3.90347 2.12787 3.62476 2.21649 3.36162C2.30512 3.09849 2.44756 2.85669 2.63476 2.65162C2.82196 2.44655 3.0498 2.28271 3.30379 2.17052C3.55777 2.05833 3.83233 2.00026 4.10999 2H7.10999C7.59524 1.99522 8.06572 2.16708 8.43369 2.48353C8.80166 2.79999 9.04201 3.23945 9.10999 3.72C9.23662 4.68007 9.47144 5.62273 9.80999 6.53C9.94454 6.88792 9.97366 7.27691 9.8939 7.65088C9.81415 8.02485 9.62886 8.36811 9.35999 8.64L8.08999 9.91C9.51355 12.4135 11.5864 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9751 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0555 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Appeler
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
