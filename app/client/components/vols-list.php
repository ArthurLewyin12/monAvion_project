<?php
// Composant: Liste des vols trouvés
// Variables attendues: $vols
?>
<?php if (empty($vols)): ?>
    <div class="card">
        <div class="empty-state">
            <h3 class="empty-state-title">Aucun vol trouvé</h3>
            <p class="empty-state-text">
                Aucun vol ne correspond à vos critères de recherche.<br>
                Essayez avec d'autres dates ou destinations.
            </p>
            <div style="margin-top: 2rem;">
                <a href="demander-assistance.php?depart=<?= urlencode($depart ?? '') ?>&arrivee=<?= urlencode($arrivee ?? '') ?>&date=<?= urlencode($date ?? '') ?>&classe=<?= urlencode($classe ?? '') ?>" class="btn btn-primary">
                    <svg style="width: 20px; height: 20px; margin-right: 0.5rem;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Demander l'aide d'une agence
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div style="margin-bottom: 1rem;">
        <strong><?= count($vols) ?></strong> vol(s) trouvé(s)
    </div>

    <?php foreach ($vols as $vol): ?>
        <?php
            $duree_vol = (strtotime($vol['date_arrivee']) - strtotime($vol['date_depart'])) / 3600;
            $heures = floor($duree_vol);
            $minutes = round(($duree_vol - $heures) * 60);
        ?>
        <div class="vol-card">
            <div class="vol-header">
                <div class="compagnie-info">
                    <div class="compagnie-logo">
                        <?= htmlspecialchars($vol['code_iata']) ?>
                    </div>
                    <div>
                        <div class="compagnie-name"><?= htmlspecialchars($vol['nom_compagnie']) ?></div>
                        <div style="font-size: 0.9rem; color: oklch(0.50 0.05 250);">
                            Vol <?= htmlspecialchars($vol['numero_vol']) ?>
                        </div>
                    </div>
                </div>
                <span class="badge badge-<?= strtolower($vol['statut']) ?>">
                    <?= htmlspecialchars($vol['statut']) ?>
                </span>
            </div>

            <div class="vol-timing">
                <div style="text-align: center;">
                    <div class="time"><?= date('H:i', strtotime($vol['date_depart'])) ?></div>
                    <div style="font-size: 1.1rem; color: oklch(0.50 0.05 250);">
                        <?= htmlspecialchars($vol['aeroport_depart']) ?>
                    </div>
                    <div style="font-size: 0.9rem; color: oklch(0.60 0.05 250); margin-top: 0.25rem;">
                        <?= date('d/m/Y', strtotime($vol['date_depart'])) ?>
                    </div>
                </div>

                <div style="text-align: center; color: oklch(0.50 0.05 250);">
                    <div style="font-weight: 600; color: oklch(0.35 0.15 250);"><?= $heures ?>h <?= $minutes ?>min</div>
                    <div style="width: 100%; height: 2px; background: oklch(0.90 0.02 250); margin: 0.5rem 0;"></div>
                    <div style="font-size: 0.85rem;">Direct</div>
                </div>

                <div style="text-align: center;">
                    <div class="time"><?= date('H:i', strtotime($vol['date_arrivee'])) ?></div>
                    <div style="font-size: 1.1rem; color: oklch(0.50 0.05 250);">
                        <?= htmlspecialchars($vol['aeroport_arrivee']) ?>
                    </div>
                    <div style="font-size: 0.9rem; color: oklch(0.60 0.05 250); margin-top: 0.25rem;">
                        <?= date('d/m/Y', strtotime($vol['date_arrivee'])) ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($vol['tarifs'])): ?>
                <div class="tarifs-section">
                    <div style="margin-bottom: 1rem; font-weight: 600; color: oklch(0.35 0.15 250);">
                        Sélectionnez votre classe :
                    </div>
                    <div class="tarifs-grid">
                        <?php foreach (['ECONOMIQUE', 'AFFAIRE', 'PREMIERE'] as $classe_type): ?>
                            <?php if (isset($vol['tarifs'][$classe_type])): ?>
                                <?php
                                    $tarif = $vol['tarifs'][$classe_type];
                                    $disponible = $tarif['disponibilite'] > 0;
                                ?>
                                <a
                                    href="<?= $disponible ? 'reservation.php?vol_id=' . $vol['vol_id'] . '&classe=' . strtolower($classe_type) : '#' ?>"
                                    class="tarif-card"
                                    <?= !$disponible ? 'style="opacity: 0.5; cursor: not-allowed;" onclick="return false;"' : '' ?>
                                >
                                    <div class="tarif-classe"><?= $classe_type ?></div>
                                    <div class="tarif-prix"><?= number_format($tarif['prix'], 0) ?>€</div>
                                    <div style="font-size: 0.85rem; color: oklch(0.60 0.05 250);">
                                        <?= $disponible ? $tarif['disponibilite'] . ' place(s)' : 'Complet' ?>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    Aucun tarif disponible pour ce vol.
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
