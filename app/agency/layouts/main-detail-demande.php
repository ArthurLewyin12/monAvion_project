<?php
/**
 * Layout principal pour la page détail demande
 * Variables attendues: $demande
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

    <div class="detail-grid">
        <!-- Colonne principale -->
        <div class="detail-main">
            <!-- En-tête demande -->
            <div class="card detail-header-card">
                <div class="card-body">
                    <div class="detail-header">
                        <div>
                            <h1 class="detail-title">Demande de vol</h1>
                            <p class="detail-subtitle">
                                Reçue le <?= date('d/m/Y à H:i', strtotime($demande['date_creation'])) ?>
                            </p>
                        </div>
                        <span class="badge badge-<?= strtolower($demande['statut']) ?> badge-lg">
                            <?= htmlspecialchars($demande['statut']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informations client -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Informations client</h2>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <div>
                                <p class="info-label">Nom complet</p>
                                <p class="info-value">
                                    <?= htmlspecialchars($demande['client_prenom'] . ' ' . $demande['client_nom']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="info-item">
                            <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <p class="info-label">Email</p>
                                <p class="info-value">
                                    <a href="mailto:<?= htmlspecialchars($demande['client_email']) ?>" class="info-link">
                                        <?= htmlspecialchars($demande['client_email']) ?>
                                    </a>
                                </p>
                            </div>
                        </div>

                        <?php if ($demande['client_telephone']): ?>
                        <div class="info-item">
                            <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1469 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.7869 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27097 2.11999 4.17997C2.095 3.90344 2.12787 3.62474 2.21649 3.3616C2.30512 3.09846 2.44756 2.85666 2.63476 2.6516C2.82196 2.44653 3.0498 2.28268 3.30379 2.1705C3.55777 2.05831 3.83233 2.00024 4.10999 1.99997H7.10999C7.5953 1.9952 8.06579 2.16705 8.43376 2.48351C8.80173 2.79996 9.04207 3.23942 9.10999 3.71997C9.23662 4.68004 9.47144 5.6227 9.80999 6.52997C9.94454 6.88505 9.97366 7.27269 9.89384 7.64382C9.81401 8.01495 9.62886 8.35885 9.35999 8.62997L8.08999 9.89997C9.51355 12.4135 11.5864 14.4864 14.1 15.91L15.37 14.64C15.6411 14.3711 15.985 14.186 16.3561 14.1062C16.7273 14.0263 17.1149 14.0555 17.47 14.19C18.3773 14.5285 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <p class="info-label">Téléphone</p>
                                <p class="info-value">
                                    <a href="tel:<?= htmlspecialchars($demande['client_telephone']) ?>" class="info-link">
                                        <?= htmlspecialchars($demande['client_telephone']) ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Détails du voyage -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Détails du voyage</h2>
                </div>
                <div class="card-body">
                    <div class="travel-route">
                        <div class="travel-point">
                            <p class="travel-label">Départ</p>
                            <p class="travel-airport"><?= htmlspecialchars($demande['aeroport_depart']) ?></p>
                            <p class="travel-date"><?= date('d/m/Y', strtotime($demande['date_depart'])) ?></p>
                        </div>
                        <div class="travel-arrow">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="travel-point">
                            <p class="travel-label">Arrivée</p>
                            <p class="travel-airport"><?= htmlspecialchars($demande['aeroport_arrivee']) ?></p>
                            <?php if ($demande['date_retour']): ?>
                                <p class="travel-date"><?= date('d/m/Y', strtotime($demande['date_retour'])) ?></p>
                            <?php else: ?>
                                <p class="travel-date">Aller simple</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="travel-details">
                        <div class="travel-detail-item">
                            <span class="travel-detail-label">Passagers</span>
                            <span class="travel-detail-value"><?= $demande['nombre_passagers'] ?></span>
                        </div>
                        <div class="travel-detail-item">
                            <span class="travel-detail-label">Classe</span>
                            <span class="travel-detail-value"><?= htmlspecialchars($demande['classe_desiree']) ?></span>
                        </div>
                    </div>

                    <?php if ($demande['notes_supplementaires']): ?>
                        <div class="travel-notes">
                            <p class="notes-label">Notes supplémentaires</p>
                            <p class="notes-content">
                                <?= nl2br(htmlspecialchars($demande['notes_supplementaires'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Colonne sidebar actions -->
        <div class="detail-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/src/controllers/agency/update_demande.php" class="actions-form">
                        <input type="hidden" name="demande_id" value="<?= $demande['id'] ?>">

                        <?php if ($demande['statut'] === 'NOUVELLE'): ?>
                            <button type="submit" name="statut" value="VUE" class="btn btn-primary btn-block">
                                Marquer comme VUE
                            </button>
                        <?php endif; ?>

                        <?php if ($demande['statut'] === 'VUE' || $demande['statut'] === 'NOUVELLE'): ?>
                            <button type="submit" name="statut" value="TRAITEE" class="btn btn-secondary btn-block">
                                Marquer comme TRAITÉE
                            </button>
                        <?php endif; ?>

                        <?php if ($demande['statut'] !== 'FERMEE'): ?>
                            <button type="submit" name="statut" value="FERMEE" class="btn btn-outline btn-block">
                                Fermer la demande
                            </button>
                        <?php endif; ?>

                        <a href="recherche-vols.php?depart=<?= urlencode($demande['aeroport_depart']) ?>&arrivee=<?= urlencode($demande['aeroport_arrivee']) ?>&date=<?= date('Y-m-d', strtotime($demande['date_depart'])) ?>&classe=<?= urlencode($demande['classe_desiree']) ?>" class="btn btn-primary btn-block">
                            Rechercher des vols
                        </a>

                        <a href="demandes-clients.php" class="btn btn-outline btn-block">
                            Retour à la liste
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
