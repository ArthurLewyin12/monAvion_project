<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

$user_id = $_SESSION['user_id'];
$vol_id = $_GET['vol_id'] ?? null;
$classe_selectionnee = strtoupper($_GET['classe'] ?? '');

if (!$vol_id || !$classe_selectionnee) {
    header('Location: recherche-vols.php');
    exit;
}

// Préparer les données
$vol = get_vol_for_reservation($pdo, $vol_id, $classe_selectionnee);

if (!$vol || $vol['disponibilite'] <= 0) {
    $_SESSION['error_message'] = "Ce vol n'est plus disponible.";
    header('Location: recherche-vols.php');
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
?>
<link rel="stylesheet" href="assets/css/reservation.css">
<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-reservation.php';
include __DIR__ . '/layouts/footer.php';
?>
