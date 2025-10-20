<?php
/**
 * Composant: Formulaire de recherche de vol
 * Variables attendues: $depart, $arrivee, $date, $classe (valeurs pré-remplies)
 */
?>

<div class="card search-card">
    <div class="card-header">
        <div class="search-header-content">
            <div class="search-icon-wrapper">
                <svg class="search-header-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                    <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <h2 class="card-title">Rechercher un vol</h2>
                <p class="search-subtitle">Trouvez des vols disponibles pour vos clients</p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="recherche-vols.php" class="search-form">
            <div class="search-form-grid">
                <!-- Aéroport de départ -->
                <div class="form-group">
                    <label for="depart" class="form-label">
                        <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Aéroport de départ
                    </label>
                    <input
                        type="text"
                        id="depart"
                        name="depart"
                        class="form-input"
                        placeholder="Ex: CDG, ORY, NCE..."
                        value="<?= htmlspecialchars($depart) ?>"
                        required
                        maxlength="3"
                        pattern="[A-Za-z]{3}"
                        title="Code IATA à 3 lettres (ex: CDG)"
                    >
                    <p class="form-hint">Code IATA à 3 lettres</p>
                </div>

                <!-- Aéroport d'arrivée -->
                <div class="form-group">
                    <label for="arrivee" class="form-label">
                        <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 10C21 17 12 23 12 23S3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Aéroport d'arrivée
                    </label>
                    <input
                        type="text"
                        id="arrivee"
                        name="arrivee"
                        class="form-input"
                        placeholder="Ex: JFK, LAX, LHR..."
                        value="<?= htmlspecialchars($arrivee) ?>"
                        required
                        maxlength="3"
                        pattern="[A-Za-z]{3}"
                        title="Code IATA à 3 lettres (ex: JFK)"
                    >
                    <p class="form-hint">Code IATA à 3 lettres</p>
                </div>

                <!-- Date de départ -->
                <div class="form-group">
                    <label for="date" class="form-label">
                        <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Date de départ
                    </label>
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

                <!-- Classe -->
                <div class="form-group">
                    <label for="classe" class="form-label">
                        <svg class="form-label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Classe (optionnel)
                    </label>
                    <select id="classe" name="classe" class="form-select">
                        <option value="">Toutes les classes</option>
                        <option value="ECONOMIQUE" <?= $classe === 'ECONOMIQUE' ? 'selected' : '' ?>>Économique</option>
                        <option value="AFFAIRE" <?= $classe === 'AFFAIRE' ? 'selected' : '' ?>>Affaires</option>
                        <option value="PREMIERE" <?= $classe === 'PREMIERE' ? 'selected' : '' ?>>Première</option>
                    </select>
                </div>
            </div>

            <!-- Boutons -->
            <div class="search-form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Rechercher des vols
                </button>
                <?php if ($depart || $arrivee || $date || $classe): ?>
                    <a href="recherche-vols.php" class="btn btn-outline">
                        Réinitialiser
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
