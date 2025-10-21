<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

$user_id = $_SESSION['user_id'];
$vol_id = $_GET['vol_id'] ?? null;
$classe_selectionnee = strtoupper($_GET['classe'] ?? '');

if (!$vol_id || !$classe_selectionnee) {
    header('Location: recherche-vols-modern.php');
    exit;
}

// Préparer les données
$vol = get_vol_for_reservation($pdo, $vol_id, $classe_selectionnee);

if (!$vol || $vol['disponibilite'] <= 0) {
    $_SESSION['error_message'] = "Ce vol n'est plus disponible.";
    header('Location: recherche-vols-modern.php');
    exit;
}

$sieges = get_sieges_disponibles($pdo, $vol_id, $classe_selectionnee);

// Pré-remplir avec les infos du client
$prenom = $_SESSION['user_prenom'];
$nom = $_SESSION['user_nom'];
$email = $_SESSION['user_email'];
$telephone = $_SESSION['user_telephone'] ?? '';

$errors = $_SESSION['reservation_errors'] ?? [];
unset($_SESSION['reservation_errors']);

$duree_vol = (strtotime($vol['date_arrivee']) - strtotime($vol['date_depart'])) / 3600;
$duree_heures = floor($duree_vol);
$duree_minutes = round(($duree_vol - $duree_heures) * 60);

$page_title = "Réservation";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - MonVolEnLigne</title>
    <link rel="stylesheet" href="<?= asset('main.css') ?>">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/seat-selection-modern.css">
    <link rel="stylesheet" href="assets/css/reservation-modern.css">
</head>
<body>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="client-container reservation-modern-container">

    <!-- Page header -->
    <div class="page-header-modern">
        <a href="recherche-vols-modern.php" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Retour à la recherche
        </a>
        <h1 class="page-title-modern">Finaliser votre réservation</h1>
        <p class="page-subtitle-modern">Plus que quelques étapes avant de confirmer votre vol</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom: 2rem;">
            <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <div>
                <strong>Erreurs :</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('src/controllers/reservation_process.php') ?>" id="reservationForm">
        <input type="hidden" name="vol_id" value="<?= $vol['id'] ?>">
        <input type="hidden" name="classe" value="<?= htmlspecialchars($classe_selectionnee) ?>">

        <div class="reservation-grid">

            <!-- Colonne gauche: Détails et formulaire -->
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
                        <?php include __DIR__ . '/components/seat-selection-modern.php'; ?>
                    </div>
                </div>

                <!-- Card: Informations passager -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <h2 class="card-title-modern">Informations du passager</h2>
                    </div>
                    <div class="card-body-modern">
                        <div class="form-grid-modern">
                            <div class="form-group-input">
                                <label class="form-label-input" for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" class="form-input-field" value="<?= htmlspecialchars($prenom) ?>" required>
                            </div>
                            <div class="form-group-input">
                                <label class="form-label-input" for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" class="form-input-field" value="<?= htmlspecialchars($nom) ?>" required>
                            </div>
                            <div class="form-group-input">
                                <label class="form-label-input" for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-input-field" value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <div class="form-group-input">
                                <label class="form-label-input" for="telephone">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" class="form-input-field" value="<?= htmlspecialchars($telephone) ?>" required>
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

            <!-- Colonne droite: Récapitulatif et paiement -->
            <div class="reservation-sidebar">

                <!-- Card: Récapitulatif -->
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
                            <p class="payment-label">Mode de paiement (simulé)</p>
                            <div class="payment-methods-modern">
                                <label class="payment-option-modern">
                                    <input type="radio" name="mode_paiement" value="CARTE" checked>
                                    <span class="payment-label-modern">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                            <line x1="1" y1="10" x2="23" y2="10"/>
                                        </svg>
                                        Carte bancaire
                                    </span>
                                </label>
                                <label class="payment-option-modern">
                                    <input type="radio" name="mode_paiement" value="PAYPAL">
                                    <span class="payment-label-modern">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M12 6v6l4 2"/>
                                        </svg>
                                        PayPal
                                    </span>
                                </label>
                            </div>
                            <p class="payment-notice">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                Paiement simulé - Aucune transaction réelle
                            </p>
                        </div>

                        <button type="submit" class="btn-confirm-modern">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirmer et payer
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

</body>
</html>
