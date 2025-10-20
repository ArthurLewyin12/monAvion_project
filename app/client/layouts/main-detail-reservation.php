<?php
// Layout: Détails d'une réservation
// Variables attendues: $reservation, $passagers, $historique, $duree_vol_heures, $duree_vol_minutes
?>
<div class="client-container detail-container">
    <!-- En-tête -->
    <div class="detail-header">
        <div class="detail-number">Réservation N° <?= htmlspecialchars($reservation['numero_reservation']) ?></div>
        <div class="detail-route">
            <?= htmlspecialchars($reservation['aeroport_depart']) ?> → <?= htmlspecialchars($reservation['aeroport_arrivee']) ?>
        </div>
        <div class="detail-badges">
            <span class="badge"><?= htmlspecialchars($reservation['statut']) ?></span>
            <span class="badge">
                <?= $reservation['type_reservation'] === 'DIRECTE' ? 'Réservation directe' : 'Via agence' ?>
            </span>
            <span class="badge">
                <?= $reservation['statut_paiement'] === 'PAYE' ? 'Payé' : 'En attente de paiement' ?>
            </span>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="info-grid">
        <div class="info-card">
            <div class="info-card-title">Informations du vol</div>
            <div class="info-item">
                <span class="info-label">Numéro de vol</span>
                <span class="info-value"><?= htmlspecialchars($reservation['numero_vol']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Compagnie</span>
                <span class="info-value"><?= htmlspecialchars($reservation['nom_compagnie']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Départ</span>
                <span class="info-value"><?= date('d/m/Y à H:i', strtotime($reservation['date_depart'])) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Arrivée</span>
                <span class="info-value"><?= date('d/m/Y à H:i', strtotime($reservation['date_arrivee'])) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Durée</span>
                <span class="info-value"><?= $duree_vol_heures ?>h <?= $duree_vol_minutes ?>min</span>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card-title">Siège & Classe</div>
            <div class="info-item">
                <span class="info-label">Numéro de siège</span>
                <span class="info-value"><?= htmlspecialchars($reservation['numero_siege']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Classe</span>
                <span class="info-value"><?= htmlspecialchars($reservation['type_classe']) ?></span>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card-title">Paiement</div>
            <div class="info-item">
                <span class="info-label">Montant total</span>
                <span class="info-value" style="font-size: 1.25rem;">
                    <?= number_format($reservation['montant_total'], 2) ?> €
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Mode de paiement</span>
                <span class="info-value"><?= htmlspecialchars($reservation['mode_paiement']) ?></span>
            </div>
        </div>
    </div>

    <!-- Passagers -->
    <?php if (!empty($passagers)): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Passager(s)</h2>
        </div>
        <div class="card-body">
            <?php foreach ($passagers as $passager): ?>
            <div class="passager-card">
                <div class="passager-name">
                    <?= htmlspecialchars($passager['prenom']) ?> <?= htmlspecialchars($passager['nom']) ?>
                </div>
                <div style="font-size: 0.9rem; color: oklch(0.50 0.05 250); display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem;">
                    <?php if ($passager['email']): ?>
                    <div>Email: <?= htmlspecialchars($passager['email']) ?></div>
                    <?php endif; ?>
                    <?php if ($passager['telephone']): ?>
                    <div>Tél: <?= htmlspecialchars($passager['telephone']) ?></div>
                    <?php endif; ?>
                    <?php if ($passager['numero_passeport']): ?>
                    <div>Passeport: <?= htmlspecialchars($passager['numero_passeport']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historique -->
    <?php if (!empty($historique)): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Historique de la réservation</h2>
        </div>
        <div class="card-body">
            <div class="timeline">
                <?php foreach ($historique as $item): ?>
                <div class="timeline-item">
                    <div class="timeline-date">
                        <?= date('d/m/Y à H:i', strtotime($item['date_creation'])) ?>
                    </div>
                    <div style="font-size: 0.95rem; color: oklch(0.30 0.15 250);">
                        <?php if ($item['statut_precedent']): ?>
                            Changement de statut: <?= htmlspecialchars($item['statut_precedent']) ?> → <?= htmlspecialchars($item['statut']) ?>
                        <?php else: ?>
                            Réservation créée avec le statut: <?= htmlspecialchars($item['statut']) ?>
                        <?php endif; ?>
                        <?php if ($item['commentaire']): ?>
                            <br><em><?= htmlspecialchars($item['commentaire']) ?></em>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="actions-bar">
        <a href="mes-reservations.php" class="btn btn-outline">Retour aux réservations</a>
        <button class="btn btn-primary" onclick="window.print()">Imprimer</button>
    </div>
</div>
