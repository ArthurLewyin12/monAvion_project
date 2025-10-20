<?php
// Composant: Dernières réservations
// Variables attendues: $reservations (array)
?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Vos dernières réservations</h2>
        <a href="mes-reservations.php" class="btn btn-outline">Voir tout</a>
    </div>
    <div class="card-body">
        <?php if (empty($reservations)): ?>
            <div class="empty-state">
                <h3 class="empty-state-title">Aucune réservation</h3>
                <p class="empty-state-text">Vous n'avez pas encore réservé de vol</p>
                <a href="recherche-vols.php" class="btn btn-primary">Rechercher un vol</a>
            </div>
        <?php else: ?>
            <?php foreach ($reservations as $reservation): ?>
                <div class="reservation-item">
                    <div class="reservation-info">
                        <div class="reservation-route">
                            <?= htmlspecialchars($reservation['aeroport_depart']) ?> → <?= htmlspecialchars($reservation['aeroport_arrivee']) ?>
                        </div>
                        <div class="reservation-details">
                            Vol <?= htmlspecialchars($reservation['numero_vol']) ?> •
                            <?= htmlspecialchars($reservation['nom_compagnie']) ?> •
                            <?= date('d/m/Y', strtotime($reservation['date_depart'])) ?>
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <span class="badge badge-<?= strtolower(str_replace('_', '-', $reservation['statut'])) ?>">
                                <?= htmlspecialchars($reservation['statut']) ?>
                            </span>
                            <span class="badge badge-<?= strtolower(str_replace('_', '-', $reservation['type_reservation'])) ?>">
                                <?= $reservation['type_reservation'] === 'DIRECTE' ? 'Directe' : 'Via agence' ?>
                            </span>
                        </div>
                    </div>
                    <div class="reservation-meta">
                        <div style="font-size: 1.25rem; font-weight: 600; color: oklch(0.35 0.15 250); margin-bottom: 0.5rem;">
                            <?= number_format($reservation['montant_total'], 2) ?> €
                        </div>
                        <a href="detail-reservation.php?id=<?= $reservation['id'] ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                            Voir détails
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
