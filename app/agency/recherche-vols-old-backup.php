<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/agency_data.php';

// Récupérer l'ID de l'agence
$user_id = $_SESSION['user_id'];
$agence_id = $_SESSION['agence_id'] ?? get_agency_id_from_user($pdo, $user_id);

if (!$agence_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de l'agence.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Récupérer les informations de l'agence
$agence_info = get_agency_info($pdo, $agence_id);
$_SESSION['agency_nom'] = $agence_info['nom_agence'] ?? 'Mon Agence';

// Compter les nouvelles demandes pour le badge
$new_demandes_count = count_new_demandes($pdo, $agence_id);

// Récupérer les paramètres de recherche
$depart = $_GET['depart'] ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date = $_GET['date'] ?? '';
$classe = $_GET['classe'] ?? '';

// Effectuer la recherche si les paramètres sont fournis
$vols_disponibles = [];
$search_performed = false;

if ($depart && $arrivee && $date) {
    $search_performed = true;
    $vols_disponibles = search_vols_for_agency($pdo, $depart, $arrivee, $date, $classe);
}

// Messages de succès/erreur
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Rechercher un vol";
$current_page = "recherche-vols";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Recherche de vols']
];
?>

<!-- Inclure le CSS spécifique -->
<link rel="stylesheet" href="assets/css/recherche-vols.css">

<?php
// Inclure le header (qui inclut la sidebar)
include __DIR__ . '/layouts/header.php';

// Inclure le layout principal
include __DIR__ . '/layouts/main-recherche-vols.php';

// Inclure le footer
include __DIR__ . '/layouts/footer.php';
?>
