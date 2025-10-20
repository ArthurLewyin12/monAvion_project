<?php
// Composant: Liste des réservations
// Variables attendues: $reservations (array)
?>
<?php if (empty($reservations)): ?>
    <div class="card">
        <div class="empty-state">
            <h3 class="empty-state-title">Aucune réservation trouvée</h3>
            <p class="empty-state-text">
                <?php if ($filtre_statut !== 'tous' || $filtre_type !== 'tous'): ?>
                    Aucune réservation ne correspond à vos critères de recherche.
                <?php else: ?>
                    Vous n'avez pas encore réservé de vol. Commencez votre aventure !
                <?php endif; ?>
            </p>
            <a href="recherche-vols.php" class="btn btn-primary">Rechercher un vol</a>
        </div>
    </div>
<?php else: ?>
    <div class="reservation-list">
        <?php foreach ($reservations as $reservation): ?>
            <div class="reservation-card">
                <div class="reservation-main">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <div style="font-size: 0.9rem; color: oklch(0.50 0.05 250); font-weight: 600;">
                            N° <?= htmlspecialchars($reservation['numero_reservation']) ?>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <span class="badge badge-<?= strtolower(str_replace('_', '-', $reservation['statut'])) ?>">
                                <?= htmlspecialchars($reservation['statut']) ?>
                            </span>
                            <span class="badge badge-<?= strtolower(str_replace('_', '-', $reservation['type_reservation'])) ?>">
                                <?= $reservation['type_reservation'] === 'DIRECTE' ? 'Directe' : 'Via agence' ?>
                            </span>
                            <?php if ($reservation['statut_paiement'] === 'PAYE'): ?>
                                <span class="badge badge-paye">Payé</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flight-info">
                        <div style="text-align: center;">
                            <div class="airport-code"><?= htmlspecialchars($reservation['aeroport_depart']) ?></div>
                            <div class="airport-time">
                                <?= date('d/m/Y', strtotime($reservation['date_depart'])) ?><br>
                                <?= date('H:i', strtotime($reservation['date_depart'])) ?>
                            </div>
                        </div>
                        <div class="flight-arrow">→</div>
                        <div style="text-align: center;">
                            <div class="airport-code"><?= htmlspecialchars($reservation['aeroport_arrivee']) ?></div>
                            <div class="airport-time">
                                <?= date('d/m/Y', strtotime($reservation['date_arrivee'])) ?><br>
                                <?= date('H:i', strtotime($reservation['date_arrivee'])) ?>
                            </div>
                        </div>
                    </div>

                    <div class="flight-details">
                        <div><strong>Vol:</strong> <?= htmlspecialchars($reservation['numero_vol']) ?></div>
                        <div><strong>Compagnie:</strong> <?= htmlspecialchars($reservation['nom_compagnie']) ?></div>
                        <div><strong>Siège:</strong> <?= htmlspecialchars($reservation['numero_siege']) ?></div>
                        <div><strong>Classe:</strong> <?= htmlspecialchars($reservation['type_classe']) ?></div>
                        <?php if ($reservation['nom_agence']): ?>
                            <div><strong>Agence:</strong> <?= htmlspecialchars($reservation['nom_agence']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; justify-content: center; align-items: flex-end;">
                    <div style="text-align: right;">
                        <div style="font-size: 1.75rem; font-weight: 700; color: oklch(0.35 0.15 250);">
                            <?= number_format($reservation['montant_total'], 2) ?> €
                        </div>
                        <div style="font-size: 0.85rem; color: oklch(0.50 0.05 250);">Prix total</div>
                    </div>
                    <a href="detail-reservation.php?id=<?= $reservation['id'] ?>" class="btn btn-primary">
                        Voir les détails
                    </a>
                    <?php if ($reservation['statut'] === 'CONFIRMEE'): ?>
                        <a href="detail-reservation.php?id=<?= $reservation['id'] ?>#billet" class="btn btn-secondary">
                            Télécharger billet
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
