<?php
/**
 * Composant: Cartes de statistiques du dashboard agence
 * Variables attendues: $stats (array avec total_reservations, confirmees, en_attente, demandes_attente)
 */
?>

<div class="stats-grid">
    <!-- Total réservations -->
    <div class="stat-card stat-card-primary">
        <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Total Réservations</p>
            <p class="stat-value"><?= number_format($stats['total_reservations']) ?></p>
        </div>
    </div>

    <!-- Confirmées -->
    <div class="stat-card stat-card-success">
        <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Confirmées</p>
            <p class="stat-value"><?= number_format($stats['confirmees']) ?></p>
            <?php if ($stats['total_reservations'] > 0): ?>
                <p class="stat-percentage">
                    <?= round(($stats['confirmees'] / $stats['total_reservations']) * 100) ?>% du total
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- En attente -->
    <div class="stat-card stat-card-warning">
        <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">En attente</p>
            <p class="stat-value"><?= number_format($stats['en_attente']) ?></p>
        </div>
    </div>

    <!-- Demandes clients -->
    <div class="stat-card stat-card-info">
        <div class="stat-icon-wrapper">
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="stat-content">
            <p class="stat-label">Demandes en attente</p>
            <p class="stat-value"><?= number_format($stats['demandes_attente']) ?></p>
            <?php if ($stats['demandes_attente'] > 0): ?>
                <a href="demandes-clients.php" class="stat-link">Voir les demandes →</a>
            <?php endif; ?>
        </div>
    </div>
</div>
