<?php
/**
 * Composant: Liste des vols disponibles (résultats de recherche)
 * Variables attendues: $vols_disponibles (array)
 */
?>

<div class="vols-list">
    <?php foreach ($vols_disponibles as $vol): ?>
        <div class="vol-card">
            <div class="vol-header">
                <!-- Compagnie -->
                <div class="vol-company">
                    <div class="company-logo">
                        <?= htmlspecialchars(substr($vol['code_iata'], 0, 2)) ?>
                    </div>
                    <div>
                        <p class="company-name"><?= htmlspecialchars($vol['nom_compagnie']) ?></p>
                        <p class="flight-number"><?= htmlspecialchars($vol['numero_vol']) ?></p>
                    </div>
                </div>

                <!-- Badge statut -->
                <span class="badge badge-<?= strtolower($vol['statut']) ?>">
                    <?= htmlspecialchars($vol['statut']) ?>
                </span>
            </div>

            <div class="vol-body">
                <!-- Itinéraire -->
                <div class="vol-route">
                    <div class="route-point">
                        <p class="route-time">
                            <?= date('H:i', strtotime($vol['date_depart'])) ?>
                        </p>
                        <p class="route-airport">
                            <?= htmlspecialchars($vol['aeroport_depart']) ?>
                        </p>
                    </div>

                    <div class="route-line">
                        <div class="route-duration">
                            <?php
                            $depart_time = strtotime($vol['date_depart']);
                            $arrivee_time = strtotime($vol['date_arrivee']);
                            $duree_minutes = ($arrivee_time - $depart_time) / 60;
                            $heures = floor($duree_minutes / 60);
                            $minutes = $duree_minutes % 60;
                            ?>
                            <?= $heures ?>h<?= sprintf('%02d', $minutes) ?>
                        </div>
                        <div class="route-bar">
                            <div class="route-dot"></div>
                            <div class="route-line-solid"></div>
                            <svg class="route-plane" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 16V8C21 6.9 20.1 6 19 6H5C3.9 6 3 6.9 3 8V16C3 17.1 3.9 18 5 18H19C20.1 18 21 17.1 21 16Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 6V18" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <div class="route-line-solid"></div>
                            <div class="route-dot"></div>
                        </div>
                    </div>

                    <div class="route-point">
                        <p class="route-time">
                            <?= date('H:i', strtotime($vol['date_arrivee'])) ?>
                        </p>
                        <p class="route-airport">
                            <?= htmlspecialchars($vol['aeroport_arrivee']) ?>
                        </p>
                    </div>
                </div>

                <!-- Tarifs par classe -->
                <div class="vol-fares">
                    <?php if (!empty($vol['tarifs'])): ?>
                        <?php foreach ($vol['tarifs'] as $classe_type => $tarif_data): ?>
                            <div class="fare-card">
                                <div class="fare-header">
                                    <h4 class="fare-class">
                                        <?php
                                        $classe_labels = [
                                            'ECONOMIQUE' => 'Économique',
                                            'AFFAIRE' => 'Affaires',
                                            'PREMIERE' => 'Première'
                                        ];
                                        echo $classe_labels[$classe_type] ?? $classe_type;
                                        ?>
                                    </h4>
                                    <p class="fare-price">
                                        <?= number_format($tarif_data['prix'], 2, ',', ' ') ?> €
                                    </p>
                                </div>
                                <div class="fare-body">
                                    <?php if ($tarif_data['disponibilite'] > 0): ?>
                                        <p class="fare-availability">
                                            <svg class="fare-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <?= $tarif_data['disponibilite'] ?> siège(s) disponible(s)
                                        </p>
                                        <a
                                            href="reserver.php?vol_id=<?= $vol['vol_id'] ?>&classe=<?= $classe_type ?>"
                                            class="btn btn-primary btn-sm btn-block"
                                        >
                                            Réserver
                                        </a>
                                    <?php else: ?>
                                        <p class="fare-unavailable">
                                            <svg class="fare-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                                <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                            Complet
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-fares">Aucun tarif disponible</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Infos supplémentaires -->
            <div class="vol-footer">
                <div class="vol-info-items">
                    <div class="vol-info-item">
                        <svg class="vol-info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span><?= date('d/m/Y', strtotime($vol['date_depart'])) ?></span>
                    </div>
                    <div class="vol-info-item">
                        <svg class="vol-info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 16V8C21 6.9 20.1 6 19 6H5C3.9 6 3 6.9 3 8V16C3 17.1 3.9 18 5 18H19C20.1 18 21 17.1 21 16Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span><?= htmlspecialchars($vol['avion_modele']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
