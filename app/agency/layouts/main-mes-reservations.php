<?php
/**
 * Layout principal pour la page mes réservations
 * Variables attendues:
 * - $reservations (array des réservations)
 * - $stats_reservations (statistiques)
 * - $filtre_statut (filtre actuel)
 */
?>

<div class="agency-container">

    <!-- Messages de feedback -->
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

    <!-- Stats résumé -->
    <div class="reservations-stats">
        <div class="stat-card-mini">
            <div class="stat-mini-label">Total</div>
            <div class="stat-mini-value"><?= $stats_reservations['total'] ?></div>
        </div>
        <div class="stat-card-mini stat-card-mini-success">
            <div class="stat-mini-label">Confirmées</div>
            <div class="stat-mini-value"><?= $stats_reservations['confirmees'] ?></div>
        </div>
        <div class="stat-card-mini stat-card-mini-warning">
            <div class="stat-mini-label">En attente</div>
            <div class="stat-mini-value"><?= $stats_reservations['en_attente'] ?></div>
        </div>
        <div class="stat-card-mini stat-card-mini-error">
            <div class="stat-mini-label">Annulées</div>
            <div class="stat-mini-value"><?= $stats_reservations['annulees'] ?></div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card filters-card">
        <div class="card-body">
            <div class="filters-wrapper">
                <div class="filters-label">
                    <svg class="filters-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Filtrer par statut :
                </div>
                <div class="filters-buttons">
                    <a href="mes-reservations.php" class="filter-btn <?= !$filtre_statut ? 'active' : '' ?>">
                        Toutes
                    </a>
                    <a href="mes-reservations.php?statut=CONFIRMEE" class="filter-btn <?= $filtre_statut === 'CONFIRMEE' ? 'active' : '' ?>">
                        Confirmées
                    </a>
                    <a href="mes-reservations.php?statut=EN_ATTENTE" class="filter-btn <?= $filtre_statut === 'EN_ATTENTE' ? 'active' : '' ?>">
                        En attente
                    </a>
                    <a href="mes-reservations.php?statut=ANNULEE" class="filter-btn <?= $filtre_statut === 'ANNULEE' ? 'active' : '' ?>">
                        Annulées
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des réservations -->
    <?php if (empty($reservations)): ?>
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h3 class="empty-title">Aucune réservation trouvée</h3>
                    <p class="empty-text">
                        <?php if ($filtre_statut): ?>
                            Aucune réservation avec le statut "<?= htmlspecialchars($filtre_statut) ?>".
                        <?php else: ?>
                            Vous n'avez pas encore créé de réservation pour vos clients.
                        <?php endif; ?>
                    </p>
                    <a href="recherche-vols.php" class="btn btn-primary">Rechercher un vol</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php include __DIR__ . '/../components/liste-reservations-agency.php'; ?>
    <?php endif; ?>

</div>
