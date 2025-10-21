<?php
// Composant: Sélection de sièges moderne et interactive
// Variables attendues: $sieges (array), $classe_selectionnee (string)

// Organiser les sièges par rangée
$sieges_par_rangee = [];
foreach ($sieges as $siege) {
    // Extraire le numéro de rangée (ex: "12A" -> "12")
    preg_match('/^(\d+)([A-F])$/', $siege['numero_siege'], $matches);
    if ($matches) {
        $rangee = $matches[1];
        $lettre = $matches[2];

        if (!isset($sieges_par_rangee[$rangee])) {
            $sieges_par_rangee[$rangee] = [];
        }
        $sieges_par_rangee[$rangee][$lettre] = $siege;
    }
}

ksort($sieges_par_rangee); // Trier par numéro de rangée
?>

<div class="seat-selection-container">

    <!-- En-tête avec légende -->
    <div class="seat-selection-header">
        <h3 class="seat-selection-title">Choisissez votre siège</h3>
        <div class="seat-legend">
            <div class="legend-item">
                <div class="legend-seat seat-available"></div>
                <span>Disponible</span>
            </div>
            <div class="legend-item">
                <div class="legend-seat seat-selected"></div>
                <span>Sélectionné</span>
            </div>
            <div class="legend-item">
                <div class="legend-seat seat-occupied"></div>
                <span>Occupé</span>
            </div>
        </div>
    </div>

    <?php if (empty($sieges)): ?>
        <div class="alert alert-warning">
            <svg style="width: 20px; height: 20px; margin-right: 0.75rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Aucun siège disponible pour cette classe.
        </div>
    <?php else: ?>

        <!-- Vue de l'avion -->
        <div class="airplane-view">
            <!-- Cockpit -->
            <div class="airplane-cockpit">
                <svg viewBox="0 0 100 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 40 Q50 0, 100 40" fill="#667eea" opacity="0.1"/>
                    <path d="M20 40 Q50 10, 80 40" stroke="#667eea" stroke-width="2" fill="none"/>
                </svg>
            </div>

            <!-- Cabin layout -->
            <div class="cabin-layout">
                <!-- Colonnes des lettres (A B C - Allée - D E F) -->
                <div class="seat-columns-header">
                    <div class="column-letter">A</div>
                    <div class="column-letter">B</div>
                    <div class="column-letter">C</div>
                    <div class="aisle-marker">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"/>
                        </svg>
                    </div>
                    <div class="column-letter">D</div>
                    <div class="column-letter">E</div>
                    <div class="column-letter">F</div>
                </div>

                <!-- Rangées de sièges -->
                <div class="seat-rows">
                    <?php foreach ($sieges_par_rangee as $num_rangee => $sieges_rangee): ?>
                        <div class="seat-row" data-row="<?= htmlspecialchars($num_rangee) ?>">
                            <!-- Numéro de rangée à gauche -->
                            <div class="row-number"><?= htmlspecialchars($num_rangee) ?></div>

                            <!-- Sièges gauche (A, B, C) -->
                            <div class="seat-group">
                                <?php foreach (['A', 'B', 'C'] as $lettre): ?>
                                    <?php if (isset($sieges_rangee[$lettre])): ?>
                                        <?php $siege = $sieges_rangee[$lettre]; ?>
                                        <label class="seat-item <?= $siege['statut'] === 'RESERVE' ? 'seat-occupied' : 'seat-available' ?>"
                                               data-seat-id="<?= $siege['id'] ?>"
                                               data-seat-number="<?= htmlspecialchars($siege['numero_siege']) ?>"
                                               title="Siège <?= htmlspecialchars($siege['numero_siege']) ?>">
                                            <?php if ($siege['statut'] === 'DISPONIBLE'): ?>
                                                <input type="radio"
                                                       name="siege_id"
                                                       value="<?= $siege['id'] ?>"
                                                       required
                                                       onchange="selectSeat(this)">
                                                <div class="seat-visual">
                                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V7H1v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7z"/>
                                                    </svg>
                                                </div>
                                                <span class="seat-label"><?= $lettre ?></span>
                                            <?php else: ?>
                                                <div class="seat-visual">
                                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V7H1v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7z"/>
                                                    </svg>
                                                </div>
                                                <span class="seat-label"><?= $lettre ?></span>
                                            <?php endif; ?>
                                        </label>
                                    <?php else: ?>
                                        <div class="seat-item seat-empty"></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <!-- Allée centrale -->
                            <div class="aisle"></div>

                            <!-- Sièges droite (D, E, F) -->
                            <div class="seat-group">
                                <?php foreach (['D', 'E', 'F'] as $lettre): ?>
                                    <?php if (isset($sieges_rangee[$lettre])): ?>
                                        <?php $siege = $sieges_rangee[$lettre]; ?>
                                        <label class="seat-item <?= $siege['statut'] === 'RESERVE' ? 'seat-occupied' : 'seat-available' ?>"
                                               data-seat-id="<?= $siege['id'] ?>"
                                               data-seat-number="<?= htmlspecialchars($siege['numero_siege']) ?>"
                                               title="Siège <?= htmlspecialchars($siege['numero_siege']) ?>">
                                            <?php if ($siege['statut'] === 'DISPONIBLE'): ?>
                                                <input type="radio"
                                                       name="siege_id"
                                                       value="<?= $siege['id'] ?>"
                                                       required
                                                       onchange="selectSeat(this)">
                                                <div class="seat-visual">
                                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V7H1v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7z"/>
                                                    </svg>
                                                </div>
                                                <span class="seat-label"><?= $lettre ?></span>
                                            <?php else: ?>
                                                <div class="seat-visual">
                                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V7H1v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7z"/>
                                                    </svg>
                                                </div>
                                                <span class="seat-label"><?= $lettre ?></span>
                                            <?php endif; ?>
                                        </label>
                                    <?php else: ?>
                                        <div class="seat-item seat-empty"></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <!-- Numéro de rangée à droite -->
                            <div class="row-number"><?= htmlspecialchars($num_rangee) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Queue de l'avion -->
            <div class="airplane-tail">
                <svg viewBox="0 0 100 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0 Q50 20, 100 0" fill="#667eea" opacity="0.05"/>
                </svg>
            </div>
        </div>

        <!-- Résumé de la sélection -->
        <div class="seat-selection-summary" id="seatSummary" style="display: none;">
            <div class="summary-content">
                <svg class="summary-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <div class="summary-text">
                    <strong>Siège sélectionné :</strong>
                    <span id="selectedSeatNumber"></span>
                </div>
            </div>
        </div>

        <p class="seat-info-text">
            <?= count($sieges) ?> siège(s) disponible(s) en classe <?= htmlspecialchars($classe_selectionnee) ?>
        </p>
    <?php endif; ?>
</div>

<script>
function selectSeat(radio) {
    // Retirer la classe selected de tous les sièges
    document.querySelectorAll('.seat-item').forEach(seat => {
        seat.classList.remove('seat-selected');
    });

    // Ajouter la classe selected au siège choisi
    const label = radio.closest('.seat-item');
    label.classList.add('seat-selected');

    // Afficher le résumé
    const seatNumber = label.dataset.seatNumber;
    document.getElementById('selectedSeatNumber').textContent = seatNumber;
    document.getElementById('seatSummary').style.display = 'flex';

    // Scroll smooth vers le résumé
    document.getElementById('seatSummary').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>
