<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

// Récupérer l'ID de l'agence
$user_id = $_SESSION['user_id'];
$agence_id = $_SESSION['agence_id'] ?? null;

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

$vol_id = $_GET['vol_id'] ?? null;
$classe_selectionnee = strtoupper($_GET['classe'] ?? '');

if (!$vol_id || !$classe_selectionnee) {
    header('Location: recherche-vols.php');
    exit;
}

// Préparer les données du vol
$vol = get_vol_for_reservation($pdo, $vol_id, $classe_selectionnee);

if (!$vol || $vol['disponibilite'] <= 0) {
    $_SESSION['error_message'] = "Ce vol n'est plus disponible.";
    header('Location: recherche-vols.php');
    exit;
}

$sieges = get_sieges_disponibles($pdo, $vol_id, $classe_selectionnee);

$errors = $_SESSION['reservation_errors'] ?? [];
unset($_SESSION['reservation_errors']);

$duree_vol = (strtotime($vol['date_arrivee']) - strtotime($vol['date_depart'])) / 3600;
$duree_heures = floor($duree_vol);
$duree_minutes = round(($duree_vol - $duree_heures) * 60);

$page_title = "Créer une réservation";
$current_page = "recherche-vols";
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<!-- Inclure les CSS modernes -->
<link rel="stylesheet" href="<?= asset('app/client/assets/css/seat-selection-modern.css') ?>">
<link rel="stylesheet" href="<?= asset('app/client/assets/css/reservation-modern.css') ?>">

<div class="agency-content reservation-modern-container">

    <!-- Page header -->
    <div class="page-header-modern">
        <a href="recherche-vols.php" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Retour à la recherche
        </a>
        <h1 class="page-title-modern">Créer une réservation pour un client</h1>
        <p class="page-subtitle-modern">Remplissez les informations du passager et confirmez</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom: 2rem;">
            <strong>Erreurs :</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('src/controllers/agency/creer_reservation.php') ?>" id="reservationForm">
        <input type="hidden" name="vol_id" value="<?= $vol['id'] ?>">
        <input type="hidden" name="classe" value="<?= htmlspecialchars($classe_selectionnee) ?>">
        <input type="hidden" name="agence_id" value="<?= $agence_id ?>">

        <div class="reservation-grid">

            <!-- Colonne gauche -->
            <div class="reservation-main">

                <!-- Card: Détails du vol -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                        </svg>
                        <h2 class="card-title-modern">Détails du vol</h2>
                    </div>
                    <div class="card-body-modern">
                        <div class="flight-summary-modern">
                            <div class="flight-route-modern">
                                <div class="route-point">
                                    <div class="route-time"><?= date('H:i', strtotime($vol['date_depart'])) ?></div>
                                    <div class="route-airport"><?= htmlspecialchars($vol['aeroport_depart']) ?></div>
                                    <div class="route-date"><?= date('d M Y', strtotime($vol['date_depart'])) ?></div>
                                </div>
                                <div class="route-visual-modern">
                                    <div class="route-line-modern"></div>
                                    <div class="route-info">
                                        <svg class="route-plane-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                        </svg>
                                        <span class="route-duration-modern"><?= $duree_heures ?>h <?= $duree_minutes ?>min</span>
                                    </div>
                                    <div class="route-line-modern"></div>
                                </div>
                                <div class="route-point">
                                    <div class="route-time"><?= date('H:i', strtotime($vol['date_arrivee'])) ?></div>
                                    <div class="route-airport"><?= htmlspecialchars($vol['aeroport_arrivee']) ?></div>
                                    <div class="route-date"><?= date('d M Y', strtotime($vol['date_arrivee'])) ?></div>
                                </div>
                            </div>
                            <div class="flight-meta-modern">
                                <div class="meta-item">
                                    <span class="meta-label">Vol</span>
                                    <span class="meta-value"><?= htmlspecialchars($vol['numero_vol']) ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Compagnie</span>
                                    <span class="meta-value"><?= htmlspecialchars($vol['nom_compagnie']) ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Classe</span>
                                    <span class="meta-value"><?= htmlspecialchars($classe_selectionnee) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Sélection du siège -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V7H1v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7z"/>
                        </svg>
                        <h2 class="card-title-modern">Choix du siège</h2>
                    </div>
                    <div class="card-body-modern">
                        <?php include __DIR__ . '/../client/components/seat-selection-modern.php'; ?>
                    </div>
                </div>

                <!-- Card: Informations du client -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <h2 class="card-title-modern">Informations du client</h2>
                    </div>
                    <div class="card-body-modern">
                        <div class="form-grid-modern">
                            <div class="form-group-input">
                                <label class="form-label-input" for="prenom">Prénom du client *</label>
                                <input type="text" id="prenom" name="prenom" class="form-input-field" required>
                            </div>
                            <div class="form-group-input">
                                <label class="form-label-input" for="nom">Nom du client *</label>
                                <input type="text" id="nom" name="nom" class="form-input-field" required>
                            </div>
                            <div class="form-group-input">
                                <label class="form-label-input" for="email">Email du client *</label>
                                <input type="email" id="email" name="email" class="form-input-field" required>
                            </div>
                            <div class="form-group-input">
                                <label class="form-label-input" for="telephone">Téléphone du client *</label>
                                <input type="tel" id="telephone" name="telephone" class="form-input-field" required>
                            </div>
                            <div class="form-group-input">
                                <label class="form-label-input" for="passeport">N° Passeport (optionnel)</label>
                                <input type="text" id="passeport" name="passeport" class="form-input-field">
                            </div>
                            <div class="form-group-input">
                                <label class="form-label-input" for="nationalite">Nationalité (optionnel)</label>
                                <input type="text" id="nationalite" name="nationalite" class="form-input-field" value="Française">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Colonne droite: Récapitulatif -->
            <div class="reservation-sidebar">

                <div class="card-modern sticky-card">
                    <div class="card-header-modern">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        <h3 class="card-title-modern">Récapitulatif</h3>
                    </div>
                    <div class="card-body-modern">
                        <div class="price-breakdown">
                            <div class="price-line">
                                <span>Vol <?= htmlspecialchars($classe_selectionnee) ?></span>
                                <span><?= number_format($vol['prix'], 2, ',', ' ') ?> €</span>
                            </div>
                            <div class="price-line">
                                <span>Taxes et frais</span>
                                <span>0,00 €</span>
                            </div>
                            <div class="price-divider"></div>
                            <div class="price-total">
                                <span>Total</span>
                                <span><?= number_format($vol['prix'], 2, ',', ' ') ?> €</span>
                            </div>
                        </div>

                        <div class="payment-info">
                            <p class="payment-label">Mode de paiement</p>
                            <div class="payment-methods-modern">
                                <label class="payment-option-modern">
                                    <input type="radio" name="mode_paiement" value="AGENCE" checked>
                                    <span class="payment-label-modern">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                            <polyline points="9 22 9 12 15 12 15 22"/>
                                        </svg>
                                        Par l'agence
                                    </span>
                                </label>
                            </div>
                            <p class="payment-notice">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                Réservation effectuée par votre agence
                            </p>
                        </div>

                        <button type="submit" class="btn-confirm-modern">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirmer la réservation
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>

<script>
document.getElementById('reservationForm').addEventListener('submit', function(e) {
    const siegeSelected = document.querySelector('input[name="siege_id"]:checked');
    if (!siegeSelected) {
        e.preventDefault();
        alert('Veuillez sélectionner un siège avant de continuer.');
        document.querySelector('.seat-selection-container').scrollIntoView({ behavior: 'smooth' });
        return false;
    }
});
</script>
