<?php
// Composant: Formulaire de recherche de vols MODERNE
// Variables attendues: $depart, $arrivee, $date_depart, $date_retour, $nombre_passagers, $classe, $type_vol
?>
<div class="search-form-modern">
    <form method="GET" action="recherche-vols.php" id="searchForm">

        <!-- Type de vol: Aller simple / Aller-retour -->
        <div class="trip-type-selector">
            <label class="trip-type-option">
                <input type="radio" name="type_vol" value="aller-simple" <?= ($type_vol ?? 'aller-simple') === 'aller-simple' ? 'checked' : '' ?> onchange="toggleReturnDate()">
                <span class="trip-type-label">
                    <svg class="trip-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    Aller simple
                </span>
            </label>
            <label class="trip-type-option">
                <input type="radio" name="type_vol" value="aller-retour" <?= ($type_vol ?? '') === 'aller-retour' ? 'checked' : '' ?> onchange="toggleReturnDate()">
                <span class="trip-type-label">
                    <svg class="trip-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Aller-retour
                </span>
            </label>
        </div>

        <div class="search-grid-modern">

            <!-- Départ -->
            <div class="form-group-modern">
                <label class="form-label-modern" for="depart">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="10" r="3"/>
                        <path d="M12 2v8M12 10v12"/>
                    </svg>
                    Départ
                </label>
                <div class="input-wrapper-modern">
                    <select id="depart" name="depart" class="form-select-modern" required>
                        <option value="">Sélectionnez un aéroport</option>
                        <optgroup label="France">
                            <option value="CDG" <?= ($depart ?? '') === 'CDG' ? 'selected' : '' ?>>Paris Charles de Gaulle (CDG)</option>
                            <option value="ORY" <?= ($depart ?? '') === 'ORY' ? 'selected' : '' ?>>Paris Orly (ORY)</option>
                            <option value="NCE" <?= ($depart ?? '') === 'NCE' ? 'selected' : '' ?>>Nice Côte d'Azur (NCE)</option>
                            <option value="LYS" <?= ($depart ?? '') === 'LYS' ? 'selected' : '' ?>>Lyon Saint-Exupéry (LYS)</option>
                            <option value="MRS" <?= ($depart ?? '') === 'MRS' ? 'selected' : '' ?>>Marseille Provence (MRS)</option>
                            <option value="TLS" <?= ($depart ?? '') === 'TLS' ? 'selected' : '' ?>>Toulouse-Blagnac (TLS)</option>
                            <option value="BOD" <?= ($depart ?? '') === 'BOD' ? 'selected' : '' ?>>Bordeaux-Mérignac (BOD)</option>
                            <option value="NTE" <?= ($depart ?? '') === 'NTE' ? 'selected' : '' ?>>Nantes Atlantique (NTE)</option>
                        </optgroup>
                        <optgroup label="Europe">
                            <option value="LHR" <?= ($depart ?? '') === 'LHR' ? 'selected' : '' ?>>London Heathrow (LHR)</option>
                            <option value="AMS" <?= ($depart ?? '') === 'AMS' ? 'selected' : '' ?>>Amsterdam Schiphol (AMS)</option>
                            <option value="FRA" <?= ($depart ?? '') === 'FRA' ? 'selected' : '' ?>>Frankfurt (FRA)</option>
                            <option value="MAD" <?= ($depart ?? '') === 'MAD' ? 'selected' : '' ?>>Madrid-Barajas (MAD)</option>
                            <option value="BCN" <?= ($depart ?? '') === 'BCN' ? 'selected' : '' ?>>Barcelona El Prat (BCN)</option>
                            <option value="FCO" <?= ($depart ?? '') === 'FCO' ? 'selected' : '' ?>>Rome Fiumicino (FCO)</option>
                            <option value="MXP" <?= ($depart ?? '') === 'MXP' ? 'selected' : '' ?>>Milan Malpensa (MXP)</option>
                            <option value="VCE" <?= ($depart ?? '') === 'VCE' ? 'selected' : '' ?>>Venice Marco Polo (VCE)</option>
                        </optgroup>
                        <optgroup label="Amérique">
                            <option value="JFK" <?= ($depart ?? '') === 'JFK' ? 'selected' : '' ?>>New York JFK (JFK)</option>
                            <option value="LAX" <?= ($depart ?? '') === 'LAX' ? 'selected' : '' ?>>Los Angeles (LAX)</option>
                            <option value="MIA" <?= ($depart ?? '') === 'MIA' ? 'selected' : '' ?>>Miami (MIA)</option>
                            <option value="YUL" <?= ($depart ?? '') === 'YUL' ? 'selected' : '' ?>>Montréal-Trudeau (YUL)</option>
                        </optgroup>
                        <optgroup label="Asie & Moyen-Orient">
                            <option value="DXB" <?= ($depart ?? '') === 'DXB' ? 'selected' : '' ?>>Dubai (DXB)</option>
                            <option value="DOH" <?= ($depart ?? '') === 'DOH' ? 'selected' : '' ?>>Doha (DOH)</option>
                            <option value="HND" <?= ($depart ?? '') === 'HND' ? 'selected' : '' ?>>Tokyo Haneda (HND)</option>
                            <option value="SIN" <?= ($depart ?? '') === 'SIN' ? 'selected' : '' ?>>Singapore (SIN)</option>
                        </optgroup>
                        <optgroup label="Afrique">
                            <option value="CMN" <?= ($depart ?? '') === 'CMN' ? 'selected' : '' ?>>Casablanca (CMN)</option>
                            <option value="TUN" <?= ($depart ?? '') === 'TUN' ? 'selected' : '' ?>>Tunis-Carthage (TUN)</option>
                            <option value="ALG" <?= ($depart ?? '') === 'ALG' ? 'selected' : '' ?>>Alger (ALG)</option>
                        </optgroup>
                    </select>
                    <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </div>
            </div>

            <!-- Arrivée -->
            <div class="form-group-modern">
                <label class="form-label-modern" for="arrivee">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                    </svg>
                    Arrivée
                </label>
                <div class="input-wrapper-modern">
                    <select id="arrivee" name="arrivee" class="form-select-modern" required>
                        <option value="">Sélectionnez un aéroport</option>
                        <optgroup label="France">
                            <option value="CDG" <?= ($arrivee ?? '') === 'CDG' ? 'selected' : '' ?>>Paris Charles de Gaulle (CDG)</option>
                            <option value="ORY" <?= ($arrivee ?? '') === 'ORY' ? 'selected' : '' ?>>Paris Orly (ORY)</option>
                            <option value="NCE" <?= ($arrivee ?? '') === 'NCE' ? 'selected' : '' ?>>Nice Côte d'Azur (NCE)</option>
                            <option value="LYS" <?= ($arrivee ?? '') === 'LYS' ? 'selected' : '' ?>>Lyon Saint-Exupéry (LYS)</option>
                            <option value="MRS" <?= ($arrivee ?? '') === 'MRS' ? 'selected' : '' ?>>Marseille Provence (MRS)</option>
                            <option value="TLS" <?= ($arrivee ?? '') === 'TLS' ? 'selected' : '' ?>>Toulouse-Blagnac (TLS)</option>
                            <option value="BOD" <?= ($arrivee ?? '') === 'BOD' ? 'selected' : '' ?>>Bordeaux-Mérignac (BOD)</option>
                            <option value="NTE" <?= ($arrivee ?? '') === 'NTE' ? 'selected' : '' ?>>Nantes Atlantique (NTE)</option>
                        </optgroup>
                        <optgroup label="Europe">
                            <option value="LHR" <?= ($arrivee ?? '') === 'LHR' ? 'selected' : '' ?>>London Heathrow (LHR)</option>
                            <option value="AMS" <?= ($arrivee ?? '') === 'AMS' ? 'selected' : '' ?>>Amsterdam Schiphol (AMS)</option>
                            <option value="FRA" <?= ($arrivee ?? '') === 'FRA' ? 'selected' : '' ?>>Frankfurt (FRA)</option>
                            <option value="MAD" <?= ($arrivee ?? '') === 'MAD' ? 'selected' : '' ?>>Madrid-Barajas (MAD)</option>
                            <option value="BCN" <?= ($arrivee ?? '') === 'BCN' ? 'selected' : '' ?>>Barcelona El Prat (BCN)</option>
                            <option value="FCO" <?= ($arrivee ?? '') === 'FCO' ? 'selected' : '' ?>>Rome Fiumicino (FCO)</option>
                            <option value="MXP" <?= ($arrivee ?? '') === 'MXP' ? 'selected' : '' ?>>Milan Malpensa (MXP)</option>
                            <option value="VCE" <?= ($arrivee ?? '') === 'VCE' ? 'selected' : '' ?>>Venice Marco Polo (VCE)</option>
                        </optgroup>
                        <optgroup label="Amérique">
                            <option value="JFK" <?= ($arrivee ?? '') === 'JFK' ? 'selected' : '' ?>>New York JFK (JFK)</option>
                            <option value="LAX" <?= ($arrivee ?? '') === 'LAX' ? 'selected' : '' ?>>Los Angeles (LAX)</option>
                            <option value="MIA" <?= ($arrivee ?? '') === 'MIA' ? 'selected' : '' ?>>Miami (MIA)</option>
                            <option value="YUL" <?= ($arrivee ?? '') === 'YUL' ? 'selected' : '' ?>>Montréal-Trudeau (YUL)</option>
                        </optgroup>
                        <optgroup label="Asie & Moyen-Orient">
                            <option value="DXB" <?= ($arrivee ?? '') === 'DXB' ? 'selected' : '' ?>>Dubai (DXB)</option>
                            <option value="DOH" <?= ($arrivee ?? '') === 'DOH' ? 'selected' : '' ?>>Doha (DOH)</option>
                            <option value="HND" <?= ($arrivee ?? '') === 'HND' ? 'selected' : '' ?>>Tokyo Haneda (HND)</option>
                            <option value="SIN" <?= ($arrivee ?? '') === 'SIN' ? 'selected' : '' ?>>Singapore (SIN)</option>
                        </optgroup>
                        <optgroup label="Afrique">
                            <option value="CMN" <?= ($arrivee ?? '') === 'CMN' ? 'selected' : '' ?>>Casablanca (CMN)</option>
                            <option value="TUN" <?= ($arrivee ?? '') === 'TUN' ? 'selected' : '' ?>>Tunis-Carthage (TUN)</option>
                            <option value="ALG" <?= ($arrivee ?? '') === 'ALG' ? 'selected' : '' ?>>Alger (ALG)</option>
                        </optgroup>
                    </select>
                    <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </div>
            </div>

            <!-- Date départ -->
            <div class="form-group-modern">
                <label class="form-label-modern" for="date_depart">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Date de départ
                </label>
                <input
                    type="date"
                    id="date_depart"
                    name="date_depart"
                    class="form-input-modern"
                    value="<?= htmlspecialchars($date_depart ?? '') ?>"
                    min="<?= date('Y-m-d') ?>"
                    required
                >
            </div>

            <!-- Date retour (optionnel) -->
            <div class="form-group-modern" id="returnDateGroup" style="display: none;">
                <label class="form-label-modern" for="date_retour">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Date de retour
                </label>
                <input
                    type="date"
                    id="date_retour"
                    name="date_retour"
                    class="form-input-modern"
                    value="<?= htmlspecialchars($date_retour ?? '') ?>"
                    min="<?= date('Y-m-d') ?>"
                >
            </div>

            <!-- Nombre de passagers -->
            <div class="form-group-modern">
                <label class="form-label-modern" for="nombre_passagers">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Passagers
                </label>
                <select id="nombre_passagers" name="nombre_passagers" class="form-select-modern">
                    <?php for ($i = 1; $i <= 9; $i++): ?>
                        <option value="<?= $i ?>" <?= ($nombre_passagers ?? 1) == $i ? 'selected' : '' ?>>
                            <?= $i ?> passager<?= $i > 1 ? 's' : '' ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 9l6 6 6-6"/>
                </svg>
            </div>

            <!-- Classe -->
            <div class="form-group-modern">
                <label class="form-label-modern" for="classe">
                    <svg class="label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    Classe
                </label>
                <div class="input-wrapper-modern">
                    <select id="classe" name="classe" class="form-select-modern">
                        <option value="">Toutes les classes</option>
                        <option value="ECONOMIQUE" <?= ($classe ?? '') === 'ECONOMIQUE' ? 'selected' : '' ?>>Économique</option>
                        <option value="AFFAIRE" <?= ($classe ?? '') === 'AFFAIRE' ? 'selected' : '' ?>>Affaire</option>
                        <option value="PREMIERE" <?= ($classe ?? '') === 'PREMIERE' ? 'selected' : '' ?>>Première</option>
                    </select>
                    <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </div>
            </div>

        </div>

        <button type="submit" class="btn-search-modern">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            <span>Rechercher des vols</span>
        </button>

    </form>
</div>

<script>
function toggleReturnDate() {
    const typeVol = document.querySelector('input[name="type_vol"]:checked').value;
    const returnDateGroup = document.getElementById('returnDateGroup');
    const dateRetourInput = document.getElementById('date_retour');

    if (typeVol === 'aller-retour') {
        returnDateGroup.style.display = 'block';
        dateRetourInput.required = true;
    } else {
        returnDateGroup.style.display = 'none';
        dateRetourInput.required = false;
        dateRetourInput.value = '';
    }
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    toggleReturnDate();

    // Mettre à jour min date de retour quand date départ change
    document.getElementById('date_depart').addEventListener('change', function() {
        document.getElementById('date_retour').min = this.value;
    });
});
</script>
