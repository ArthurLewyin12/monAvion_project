<?php
// Layout: Liste des réservations
// Variables attendues: $reservations, $filtre_statut, $filtre_type
?>
<div class="client-container">
    <div class="page-header">
        <h1 class="page-title">Mes réservations</h1>
        <p class="page-subtitle">Consultez et gérez toutes vos réservations de vols</p>
    </div>

    <!-- Statistiques résumées -->
    <div class="stats-summary">
        <div class="stat-item">
            <div class="stat-value"><?= count($reservations) ?></div>
            <div class="stat-text">Réservation(s)</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">
                <?= count(array_filter($reservations, fn($r) => $r['statut'] === 'CONFIRMEE')) ?>
            </div>
            <div class="stat-text">Confirmée(s)</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">
                <?= count(array_filter($reservations, fn($r) => $r['type_reservation'] === 'DIRECTE')) ?>
            </div>
            <div class="stat-text">Réservation(s) directe(s)</div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters">
        <form method="GET" action="mes-reservations.php">
            <div class="filters-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="statut">Statut</label>
                    <select name="statut" id="statut" class="form-select" onchange="this.form.submit()">
                        <option value="tous" <?= $filtre_statut === 'tous' ? 'selected' : '' ?>>Tous les statuts</option>
                        <option value="confirmee" <?= $filtre_statut === 'confirmee' ? 'selected' : '' ?>>Confirmées</option>
                        <option value="en_attente" <?= $filtre_statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="annulee" <?= $filtre_statut === 'annulee' ? 'selected' : '' ?>>Annulées</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="type">Type de réservation</label>
                    <select name="type" id="type" class="form-select" onchange="this.form.submit()">
                        <option value="tous" <?= $filtre_type === 'tous' ? 'selected' : '' ?>>Tous les types</option>
                        <option value="directe" <?= $filtre_type === 'directe' ? 'selected' : '' ?>>Réservations directes</option>
                        <option value="par_agence" <?= $filtre_type === 'par_agence' ? 'selected' : '' ?>>Via agence</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="visibility: hidden;">Action</label>
                    <a href="mes-reservations.php" class="btn btn-outline" style="width: 100%; display: block;">Réinitialiser</a>
                </div>
            </div>
        </form>
    </div>

    <?php include __DIR__ . '/../components/reservations-list.php'; ?>
</div>
