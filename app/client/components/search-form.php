<?php
// Composant: Formulaire de recherche de vols
// Variables attendues: $depart, $arrivee, $date, $classe
?>
<div class="search-form">
    <form method="GET" action="recherche-vols.php">
        <div class="search-grid">
            <div class="form-group">
                <label class="form-label" for="depart">Départ</label>
                <input
                    type="text"
                    id="depart"
                    name="depart"
                    class="form-input"
                    placeholder="CDG"
                    value="<?= htmlspecialchars($depart) ?>"
                    maxlength="3"
                    required
                    style="text-transform: uppercase;"
                >
                <small style="color: oklch(0.60 0.05 250);">Code IATA (ex: CDG)</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="arrivee">Arrivée</label>
                <input
                    type="text"
                    id="arrivee"
                    name="arrivee"
                    class="form-input"
                    placeholder="JFK"
                    value="<?= htmlspecialchars($arrivee) ?>"
                    maxlength="3"
                    required
                    style="text-transform: uppercase;"
                >
                <small style="color: oklch(0.60 0.05 250);">Code IATA (ex: JFK)</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="date">Date de départ</label>
                <input
                    type="date"
                    id="date"
                    name="date"
                    class="form-input"
                    value="<?= htmlspecialchars($date) ?>"
                    min="<?= date('Y-m-d') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="classe">Classe (optionnel)</label>
                <select id="classe" name="classe" class="form-select">
                    <option value="">Toutes les classes</option>
                    <option value="economique" <?= $classe === 'economique' ? 'selected' : '' ?>>Économique</option>
                    <option value="affaire" <?= $classe === 'affaire' ? 'selected' : '' ?>>Affaire</option>
                    <option value="premiere" <?= $classe === 'premiere' ? 'selected' : '' ?>>Première</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            Rechercher des vols
        </button>
    </form>
</div>
