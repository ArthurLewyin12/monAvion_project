<?php
/**
 * Layout principal pour la page demandes clients
 * Variables attendues:
 * - $demandes (array)
 * - $filtre_statut
 */
?>

<div class="agency-container">

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
                    <a href="demandes-clients.php" class="filter-btn <?= !$filtre_statut ? 'active' : '' ?>">
                        Toutes
                    </a>
                    <a href="demandes-clients.php?statut=NOUVELLE" class="filter-btn <?= $filtre_statut === 'NOUVELLE' ? 'active' : '' ?>">
                        Nouvelles
                    </a>
                    <a href="demandes-clients.php?statut=VUE" class="filter-btn <?= $filtre_statut === 'VUE' ? 'active' : '' ?>">
                        Vues
                    </a>
                    <a href="demandes-clients.php?statut=TRAITEE" class="filter-btn <?= $filtre_statut === 'TRAITEE' ? 'active' : '' ?>">
                        Traitées
                    </a>
                    <a href="demandes-clients.php?statut=FERMEE" class="filter-btn <?= $filtre_statut === 'FERMEE' ? 'active' : '' ?>">
                        Fermées
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des demandes -->
    <?php if (empty($demandes)): ?>
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h3 class="empty-title">Aucune demande</h3>
                    <p class="empty-text">
                        <?php if ($filtre_statut): ?>
                            Aucune demande avec le statut "<?= htmlspecialchars($filtre_statut) ?>".
                        <?php else: ?>
                            Vous n'avez pas encore reçu de demande de la part de clients.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="demandes-grid">
            <?php foreach ($demandes as $demande): ?>
                <div class="demande-card">
                    <div class="demande-header">
                        <span class="badge badge-<?= strtolower($demande['statut']) ?>">
                            <?= htmlspecialchars($demande['statut']) ?>
                        </span>
                        <span class="demande-date">
                            <?= date('d/m/Y', strtotime($demande['date_creation'])) ?>
                        </span>
                    </div>

                    <div class="demande-body">
                        <div class="demande-client">
                            <svg class="client-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <div>
                                <p class="client-name">
                                    <?= htmlspecialchars($demande['client_prenom'] . ' ' . $demande['client_nom']) ?>
                                </p>
                                <p class="client-contact"><?= htmlspecialchars($demande['client_email']) ?></p>
                            </div>
                        </div>

                        <div class="demande-route">
                            <div class="route-info">
                                <span class="route-airport"><?= htmlspecialchars($demande['aeroport_depart']) ?></span>
                                <svg class="route-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="route-airport"><?= htmlspecialchars($demande['aeroport_arrivee']) ?></span>
                            </div>
                            <p class="route-date">
                                Départ: <?= date('d/m/Y', strtotime($demande['date_depart'])) ?>
                                <?php if ($demande['date_retour']): ?>
                                    • Retour: <?= date('d/m/Y', strtotime($demande['date_retour'])) ?>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="demande-details">
                            <div class="detail-item">
                                <svg class="detail-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                    <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <?= $demande['nombre_passagers'] ?> passager(s)
                            </div>
                            <div class="detail-item">
                                <svg class="detail-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <?= htmlspecialchars($demande['classe_desiree']) ?>
                            </div>
                        </div>
                    </div>

                    <div class="demande-footer">
                        <a href="detail-demande.php?id=<?= $demande['id'] ?>" class="btn btn-primary btn-sm btn-block">
                            Voir détails
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
