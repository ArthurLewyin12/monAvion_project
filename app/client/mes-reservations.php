<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

$user_id = $_SESSION['user_id'];

// Filtres
$filtre_statut = $_GET['statut'] ?? 'tous';
$filtre_type = $_GET['type'] ?? 'tous';

// Préparer les données
$reservations = get_client_reservations($pdo, $user_id, $filtre_statut, $filtre_type);
?>
<link rel="stylesheet" href="assets/css/reservations.css">
<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-reservations.php';
include __DIR__ . '/layouts/footer.php';
?>
