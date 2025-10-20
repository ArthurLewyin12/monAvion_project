<?php
// Composant: Statistiques du dashboard
// Variables attendues: $stats (array avec total, confirmees, en_attente)
?>
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-number"><?= $stats['total'] ?></div>
        <div class="stat-label">Réservation(s) totale(s)</div>
    </div>
    <div class="stat-card secondary">
        <div class="stat-number"><?= $stats['confirmees'] ?></div>
        <div class="stat-label">Réservation(s) confirmée(s)</div>
    </div>
    <div class="stat-card accent">
        <div class="stat-number"><?= $stats['en_attente'] ?></div>
        <div class="stat-label">En attente de confirmation</div>
    </div>
</div>
