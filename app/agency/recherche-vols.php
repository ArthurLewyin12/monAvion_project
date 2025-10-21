<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php'; // Réutiliser la fonction search_vols

// Récupérer l'ID de l'agence
$user_id = $_SESSION['user_id'];
$agence_id = $_SESSION['agence_id'] ?? null;

// Si pas d'agence_id en session, le récupérer
if (!$agence_id) {
    $stmt = $pdo->prepare("SELECT id FROM agences WHERE utilisateur_id = ?");
    $stmt->execute([$user_id]);
    $agence = $stmt->fetch(PDO::FETCH_ASSOC);
    $agence_id = $agence['id'] ?? null;
    $_SESSION['agence_id'] = $agence_id;
}

if (!$agence_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de l'agence.";
    header("Location: " . url('app/auth/connexion-agence.php'));
    exit();
}

// Récupérer les critères de recherche
$depart = $_GET['depart'] ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date_depart = $_GET['date_depart'] ?? '';
$date_retour = $_GET['date_retour'] ?? '';
$type_vol = $_GET['type_vol'] ?? 'aller-simple';
$nombre_passagers = $_GET['nombre_passagers'] ?? 1;
$classe = $_GET['classe'] ?? '';

$vols = [];
$search_performed = false;

// Si une recherche est lancée
if ($depart && $arrivee && $date_depart) {
    $search_performed = true;

    // Rechercher les vols (réutiliser la même fonction que client)
    $vols = search_vols($pdo, $depart, $arrivee, $date_depart, $classe);

    // Ajouter l'ID pour chaque vol
    foreach ($vols as &$vol) {
        $vol['id'] = $vol['vol_id'];
    }
}

$page_title = "Recherche de vols";
$current_page = "recherche-vols";
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="agency-content">
    <div class="page-header-modern" style="text-align: center; margin-bottom: 2rem;">
        <h1 class="page-title" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 0.5rem;">
            Rechercher un vol pour un client
        </h1>
        <p class="page-subtitle" style="font-size: 1.125rem; color: #718096;">
            Trouvez le meilleur vol et créez une réservation
        </p>
    </div>

    <?php
    // Inclure le formulaire moderne (même que client)
    include __DIR__ . '/../client/components/search-form.php';
    ?>

    <?php if ($search_performed): ?>
        <?php
        // Inclure la liste moderne des vols mais avec lien vers reserver.php de l'agence
        ?>
        <?php if (empty($vols)): ?>
            <div class="empty-state-modern">
                <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <h3 class="empty-state-title">Aucun vol trouvé</h3>
                <p class="empty-state-text">
                    Aucun vol ne correspond à vos critères de recherche. Essayez de modifier vos dates ou votre destination.
                </p>
            </div>
        <?php else: ?>
            <!-- Header des résultats -->
            <div class="results-header">
                <div class="results-count">
                    <span class="results-count-number"><?= count($vols) ?></span>
                    vol<?= count($vols) > 1 ? 's' : '' ?> trouvé<?= count($vols) > 1 ? 's' : '' ?>
                </div>
                <div class="results-filters">
                    <button class="filter-btn active" onclick="sortFlights('price')">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        Prix
                    </button>
                    <button class="filter-btn" onclick="sortFlights('duration')">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Durée
                    </button>
                    <button class="filter-btn" onclick="sortFlights('departure')">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Départ
                    </button>
                </div>
            </div>

            <!-- Grid des vols -->
            <div class="flights-grid" id="flightsGrid">
                <?php foreach ($vols as $vol): ?>
                    <?php
                    $depart_timestamp = strtotime($vol['date_depart']);
                    $arrivee_timestamp = strtotime($vol['date_arrivee']);
                    $duree_secondes = $arrivee_timestamp - $depart_timestamp;
                    $duree_heures = floor($duree_secondes / 3600);
                    $duree_minutes = floor(($duree_secondes % 3600) / 60);
                    $initiales = strtoupper(substr($vol['nom_compagnie'], 0, 2));
                    $tarifs = $vol['tarifs'] ?? [];
                    ?>

                    <div class="flight-card"
                         data-price="<?= isset($tarifs['ECONOMIQUE']) ? $tarifs['ECONOMIQUE']['prix'] : 999999 ?>"
                         data-duration="<?= $duree_secondes ?>"
                         data-departure="<?= $depart_timestamp ?>">

                        <div class="flight-company">
                            <div class="company-logo"><?= htmlspecialchars($initiales) ?></div>
                            <div class="company-name"><?= htmlspecialchars($vol['nom_compagnie']) ?></div>
                            <div class="flight-number">Vol <?= htmlspecialchars($vol['numero_vol']) ?></div>
                        </div>

                        <div class="flight-details">
                            <div class="flight-time">
                                <div class="time-large"><?= date('H:i', $depart_timestamp) ?></div>
                                <div class="airport-code"><?= htmlspecialchars($vol['aeroport_depart']) ?></div>
                                <div class="date-small"><?= date('d M', $depart_timestamp) ?></div>
                            </div>

                            <div class="flight-route">
                                <div class="route-visual">
                                    <div class="route-line">
                                        <svg class="route-plane" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                            <path d="M2 17l10 5 10-5"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="route-duration"><?= $duree_heures ?>h <?= $duree_minutes ?>min</div>
                                <div class="route-type">Direct</div>
                            </div>

                            <div class="flight-time">
                                <div class="time-large"><?= date('H:i', $arrivee_timestamp) ?></div>
                                <div class="airport-code"><?= htmlspecialchars($vol['aeroport_arrivee']) ?></div>
                                <div class="date-small"><?= date('d M', $arrivee_timestamp) ?></div>
                            </div>
                        </div>

                        <div class="flight-action">
                            <div class="flight-classes">
                                <?php if (isset($tarifs['ECONOMIQUE']) && $tarifs['ECONOMIQUE']['disponibilite'] > 0): ?>
                                    <div class="class-option">
                                        <span class="class-name">Économique</span>
                                        <span class="class-price"><?= number_format($tarifs['ECONOMIQUE']['prix'], 0, ',', ' ') ?> €</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($tarifs['AFFAIRE']) && $tarifs['AFFAIRE']['disponibilite'] > 0): ?>
                                    <div class="class-option">
                                        <span class="class-name">Affaire</span>
                                        <span class="class-price"><?= number_format($tarifs['AFFAIRE']['prix'], 0, ',', ' ') ?> €</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($tarifs['PREMIERE']) && $tarifs['PREMIERE']['disponibilite'] > 0): ?>
                                    <div class="class-option">
                                        <span class="class-name">Première</span>
                                        <span class="class-price"><?= number_format($tarifs['PREMIERE']['prix'], 0, ',', ' ') ?> €</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($tarifs) && array_filter($tarifs, fn($t) => $t['disponibilite'] > 0)): ?>
                                <?php
                                $premiere_classe_dispo = null;
                                foreach (['ECONOMIQUE', 'AFFAIRE', 'PREMIERE'] as $classe_type) {
                                    if (isset($tarifs[$classe_type]) && $tarifs[$classe_type]['disponibilite'] > 0) {
                                        $premiere_classe_dispo = $classe_type;
                                        break;
                                    }
                                }
                                ?>
                                <a href="reserver.php?vol_id=<?= $vol['id'] ?>&classe=<?= $premiere_classe_dispo ?>" class="btn-book">
                                    Réserver pour un client
                                </a>
                            <?php else: ?>
                                <button class="btn-book" disabled style="opacity: 0.5; cursor: not-allowed;">
                                    Complet
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <script>
            function sortFlights(criteria) {
                const grid = document.getElementById('flightsGrid');
                const cards = Array.from(grid.querySelectorAll('.flight-card'));
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                event.target.closest('.filter-btn').classList.add('active');

                cards.sort((a, b) => {
                    if (criteria === 'price') return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    if (criteria === 'duration') return parseFloat(a.dataset.duration) - parseFloat(b.dataset.duration);
                    if (criteria === 'departure') return parseFloat(a.dataset.departure) - parseFloat(b.dataset.departure);
                    return 0;
                });

                cards.forEach(card => grid.appendChild(card));
            }
            </script>
        <?php endif; ?>
    <?php else: ?>
        <div class="empty-state-modern">
            <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <h3 class="empty-state-title">Commencez votre recherche</h3>
            <p class="empty-state-text">
                Remplissez le formulaire ci-dessus pour découvrir les vols disponibles pour votre client.
            </p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>

<!-- Inclure les CSS modernes -->
<link rel="stylesheet" href="<?= asset('app/client/assets/css/recherche-modern.css') ?>">
