<?php
/**
 * Composant: Liste des dernières réservations
 * Variables attendues: $reservations_recentes (array)
 */
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Réservations récentes</h2>
        <a href="mes-reservations.php" class="card-link">Voir tout →</a>
    </div>
    <div class="card-body">
        <?php if (empty($reservations_recentes)): ?>
            <div class="empty-state">
                <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h3 class="empty-title">Aucune réservation</h3>
                <p class="empty-text">Vous n'avez pas encore créé de réservation</p>
                <a href="recherche-vols.php" class="btn btn-primary">Rechercher un vol</a>
            </div>
        <?php else: ?>
            <div class="reservations-list">
                <?php foreach ($reservations_recentes as $reservation): ?>
                    <div class="reservation-item">
                        <div class="reservation-main">
                            <!-- Badge statut -->
                            <span class="badge badge-<?= strtolower($reservation['statut']) ?>">
                                <?= htmlspecialchars($reservation['statut']) ?>
                            </span>

                            <!-- Infos vol -->
                            <div class="reservation-flight">
                                <div class="flight-route">
                                    <span class="flight-airport"><?= htmlspecialchars($reservation['aeroport_depart']) ?></span>
                                    <svg class="flight-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="flight-airport"><?= htmlspecialchars($reservation['aeroport_arrivee']) ?></span>
                                </div>
                                <p class="flight-details">
                                    <span class="flight-number"><?= htmlspecialchars($reservation['numero_vol']) ?></span>
                                    •
                                    <span class="flight-company"><?= htmlspecialchars($reservation['nom_compagnie']) ?></span>
                                </p>
                            </div>

                            <!-- Passager -->
                            <div class="reservation-passenger">
                                <p class="passenger-label">Passager</p>
                                <p class="passenger-name">
                                    <?= htmlspecialchars($reservation['passager_prenom'] . ' ' . $reservation['passager_nom']) ?>
                                </p>
                                <p class="passenger-details">
                                    Siège <?= htmlspecialchars($reservation['numero_siege']) ?> •
                                    <?= htmlspecialchars($reservation['type_classe']) ?>
                                </p>
                            </div>

                            <!-- Date et montant -->
                            <div class="reservation-info">
                                <p class="reservation-date">
                                    <?= date('d/m/Y à H:i', strtotime($reservation['date_depart'])) ?>
                                </p>
                                <p class="reservation-amount">
                                    <?= number_format($reservation['montant_total'], 2, ',', ' ') ?>
                                    <?= htmlspecialchars($reservation['devise']) ?>
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="reservation-actions">
                            <a href="detail-reservation.php?id=<?= $reservation['id'] ?>" class="btn-icon" title="Voir détails">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8S1 12 1 12z" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
