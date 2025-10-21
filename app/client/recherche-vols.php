<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

$user_id = $_SESSION['user_id'];

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

    // Rechercher les vols
    $vols = search_vols($pdo, $depart, $arrivee, $date_depart, $classe);

    // Ajouter l'ID pour chaque vol (renommer vol_id en id pour le component)
    foreach ($vols as &$vol) {
        $vol['id'] = $vol['vol_id'];
    }
}

$page_title = "Recherche de vols";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - MonVolEnLigne</title>
    <link rel="stylesheet" href="<?= asset('main.css') ?>">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/recherche-modern.css">
</head>
<body>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="client-container">
    <div class="page-header" style="text-align: center; margin-bottom: 2rem;">
        <h1 class="page-title" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 0.5rem;">
            Trouvez votre vol idéal
        </h1>
        <p class="page-subtitle" style="font-size: 1.125rem; color: #718096;">
            Comparez les prix et réservez en quelques clics
        </p>
    </div>

    <?php
    // Inclure le formulaire moderne
    include __DIR__ . '/components/search-form.php';
    ?>

    <?php if ($search_performed): ?>
        <?php
        // Inclure la liste moderne des vols
        include __DIR__ . '/components/vols-list.php';
        ?>
    <?php else: ?>
        <div class="empty-state-modern">
            <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <h3 class="empty-state-title">Commencez votre recherche</h3>
            <p class="empty-state-text">
                Remplissez le formulaire ci-dessus pour découvrir les vols disponibles selon vos critères.
            </p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>

</body>
</html>
