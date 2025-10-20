<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

$user_id = $_SESSION['user_id'];
$reservation_id = $_GET['id'] ?? null;

if (!$reservation_id) {
    header('Location: mes-reservations.php');
    exit;
}

// Préparer les données
$reservation = get_reservation_details($pdo, $reservation_id, $user_id);

if (!$reservation) {
    header('Location: mes-reservations.php');
    exit;
}

$passagers = get_reservation_passagers($pdo, $reservation_id);
$historique = get_reservation_historique($pdo, $reservation_id);

// Calculer la durée du vol
$duree_vol = (strtotime($reservation['date_arrivee']) - strtotime($reservation['date_depart'])) / 3600;
$duree_vol_heures = floor($duree_vol);
$duree_vol_minutes = round(($duree_vol - $duree_vol_heures) * 60);
?>
<link rel="stylesheet" href="assets/css/detail-reservation.css">
<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-detail-reservation.php';
include __DIR__ . '/layouts/footer.php';
?>
