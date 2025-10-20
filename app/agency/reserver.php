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

// Récupérer les paramètres
$vol_id = $_GET['vol_id'] ?? null;
$classe = $_GET['classe'] ?? null;

if (!$vol_id || !$classe) {
    $_SESSION['error_message'] = "Vol ou classe manquant.";
    header("Location: recherche-vols.php");
    exit();
}

// Récupérer les détails du vol
$vol = get_vol_for_agency_booking($pdo, $vol_id, $classe);

if (!$vol) {
    $_SESSION['error_message'] = "Vol introuvable ou non disponible.";
    header("Location: recherche-vols.php");
    exit();
}

// Récupérer les sièges disponibles
$sieges_disponibles = get_sieges_disponibles_for_agency($pdo, $vol_id, $classe);

// Récupérer les informations de l'agence
$agence_info = get_agency_info($pdo, $agence_id);
$_SESSION['agency_nom'] = $agence_info['nom_agence'] ?? 'Mon Agence';

// Compter les nouvelles demandes pour le badge
$new_demandes_count = count_new_demandes($pdo, $agence_id);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
$reservation_errors = $_SESSION['reservation_errors'] ?? [];
unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['reservation_errors']);

// Variables pour le header
$page_title = "Créer une réservation";
$current_page = "recherche-vols";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Recherche', 'url' => 'recherche-vols.php'],
    ['label' => 'Réserver']
];
?>

<link rel="stylesheet" href="assets/css/reserver.css">

<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-reserver.php';
include __DIR__ . '/layouts/footer.php';
?>
