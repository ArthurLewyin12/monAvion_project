<?php
/**
 * Composant: Liste complète des réservations de l'agence
 * Variables attendues: $reservations (array)
 */
?>

<div class="reservations-table-container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= count($reservations) ?> réservation(s)</h2>
        </div>
        <div class="card-body card-body-table">
            <div class="table-wrapper">
                <table class="reservations-table">
                    <thead>
                        <tr>
                            <th>N° Réservation</th>
                            <th>Passager</th>
                            <th>Vol</th>
                            <th>Date départ</th>
                            <th>Classe</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr class="table-row">
                                <td>
                                    <div class="reservation-number">
                                        <?= htmlspecialchars($reservation['numero_reservation']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="passenger-cell">
                                        <p class="passenger-name">
                                            <?= htmlspecialchars($reservation['passager_prenom'] . ' ' . $reservation['passager_nom']) ?>
                                        </p>
                                        <?php if ($reservation['passager_email']): ?>
                                            <p class="passenger-email">
                                                <?= htmlspecialchars($reservation['passager_email']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="flight-cell">
                                        <p class="flight-number">
                                            <?= htmlspecialchars($reservation['numero_vol']) ?>
                                        </p>
                                        <p class="flight-route">
                                            <?= htmlspecialchars($reservation['aeroport_depart']) ?> →
                                            <?= htmlspecialchars($reservation['aeroport_arrivee']) ?>
                                        </p>
                                        <p class="flight-company">
                                            <?= htmlspecialchars($reservation['nom_compagnie']) ?>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-cell">
                                        <p class="date-value">
                                            <?= date('d/m/Y', strtotime($reservation['date_depart'])) ?>
                                        </p>
                                        <p class="time-value">
                                            <?= date('H:i', strtotime($reservation['date_depart'])) ?>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="class-cell">
                                        <span class="class-badge class-badge-<?= strtolower($reservation['type_classe']) ?>">
                                            <?= htmlspecialchars($reservation['type_classe']) ?>
                                        </span>
                                        <p class="seat-number">
                                            Siège <?= htmlspecialchars($reservation['numero_siege']) ?>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-cell">
                                        <p class="amount-value">
                                            <?= number_format($reservation['montant_total'], 2, ',', ' ') ?> €
                                        </p>
                                        <p class="payment-status payment-status-<?= strtolower($reservation['statut_paiement']) ?>">
                                            <?= htmlspecialchars($reservation['statut_paiement']) ?>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?= strtolower($reservation['statut']) ?>">
                                        <?= htmlspecialchars($reservation['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a
                                            href="detail-reservation.php?id=<?= $reservation['id'] ?>"
                                            class="btn-icon"
                                            title="Voir détails"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8S1 12 1 12z" stroke="currentColor" stroke-width="2"/>
                                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                        </a>

                                        <?php if ($reservation['statut'] === 'CONFIRMEE'): ?>
                                            <button
                                                class="btn-icon btn-icon-danger"
                                                onclick="confirmCancelReservation(<?= $reservation['id'] ?>, '<?= htmlspecialchars($reservation['numero_reservation']) ?>')"
                                                title="Annuler"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                                    <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'annulation (caché par défaut) -->
<div id="cancelModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeCancelModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Confirmer l'annulation</h3>
            <button class="modal-close" onclick="closeCancelModal()">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <p>Êtes-vous sûr de vouloir annuler la réservation <strong id="reservationNumber"></strong> ?</p>
            <p class="modal-warning">Cette action est irréversible.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeCancelModal()">Annuler</button>
            <form id="cancelForm" method="POST" action="/src/controllers/agency/annuler_reservation.php">
                <input type="hidden" name="reservation_id" id="cancelReservationId">
                <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmCancelReservation(reservationId, reservationNumber) {
    document.getElementById('cancelReservationId').value = reservationId;
    document.getElementById('reservationNumber').textContent = reservationNumber;
    document.getElementById('cancelModal').style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCancelModal();
    }
});
</script>
