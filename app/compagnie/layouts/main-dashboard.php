<?php
/**
 * Layout principal pour le dashboard COMPAGNIE
 * Variables attendues: $stats, $vols_recents, $prochains_departs
 */
?>

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

    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">Vue d'ensemble</h1>
            <p class="dashboard-subtitle">Gérez vos vols et votre flotte en temps réel</p>
        </div>
        <div class="dashboard-actions">
            <a href="creer-vol.php" class="btn btn-primary">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Créer un vol
            </a>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
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
                <p class="stat-value"><?= $stats['vols_programmes'] ?></p>
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
                <p class="stat-value"><?= $stats['vols_retardes'] ?></p>
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
                <p class="stat-value"><?= $stats['vols_annules'] ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Avions</p>
                <p class="stat-value"><?= $stats['total_avions'] ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Réservations</p>
                <p class="stat-value"><?= $stats['total_reservations'] ?></p>
            </div>
        </div>
    </div>

    <!-- Grid principal -->
    <div class="dashboard-grid">
        <!-- Prochains départs -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Prochains départs</h2>
                <a href="mes-vols.php" class="card-link">Voir tous</a>
            </div>
            <div class="card-body">
                <?php if (empty($prochains_departs)): ?>
                    <div class="empty-state">
                        <p class="empty-state-text">Aucun vol programmé pour le moment.</p>
                        <a href="creer-vol.php" class="btn btn-sm btn-primary" style="margin-top: 1rem;">Créer un vol</a>
                    </div>
                <?php else: ?>
                    <div class="vols-list">
                        <?php foreach ($prochains_departs as $vol): ?>
                            <a href="detail-vol.php?id=<?= $vol['id'] ?>" class="vol-item">
                                <div class="vol-item-left">
                                    <div class="vol-numero"><?= htmlspecialchars($vol['numero_vol']) ?></div>
                                    <div class="vol-route">
                                        <?= htmlspecialchars($vol['aeroport_depart']) ?> → <?= htmlspecialchars($vol['aeroport_arrivee']) ?>
                                    </div>
                                </div>
                                <div class="vol-item-center">
                                    <div class="vol-date"><?= date('d/m/Y à H:i', strtotime($vol['date_depart'])) ?></div>
                                    <div class="vol-avion"><?= htmlspecialchars($vol['avion_modele']) ?></div>
                                </div>
                                <div class="vol-item-right">
                                    <span class="badge badge-<?= strtolower($vol['statut']) ?>">
                                        <?= htmlspecialchars($vol['statut']) ?>
                                    </span>
                                    <div class="vol-reservations">
                                        <?= $vol['nombre_reservations'] ?> réservation(s)
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Vols récents -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Vols créés récemment</h2>
            </div>
            <div class="card-body">
                <?php if (empty($vols_recents)): ?>
                    <div class="empty-state">
                        <p class="empty-state-text">Aucun vol créé récemment.</p>
                    </div>
                <?php else: ?>
                    <div class="vols-list">
                        <?php foreach ($vols_recents as $vol): ?>
                            <a href="detail-vol.php?id=<?= $vol['id'] ?>" class="vol-item">
                                <div class="vol-item-left">
                                    <div class="vol-numero"><?= htmlspecialchars($vol['numero_vol']) ?></div>
                                    <div class="vol-route">
                                        <?= htmlspecialchars($vol['aeroport_depart']) ?> → <?= htmlspecialchars($vol['aeroport_arrivee']) ?>
                                    </div>
                                </div>
                                <div class="vol-item-center">
                                    <div class="vol-date"><?= date('d/m/Y à H:i', strtotime($vol['date_depart'])) ?></div>
                                    <div class="vol-avion"><?= htmlspecialchars($vol['avion_modele']) ?></div>
                                </div>
                                <div class="vol-item-right">
                                    <span class="badge badge-<?= strtolower($vol['statut']) ?>">
                                        <?= htmlspecialchars($vol['statut']) ?>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
