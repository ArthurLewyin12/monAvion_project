<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

$user_id = $_SESSION['user_id'];

// Récupérer les critères de recherche
$depart = $_GET['depart'] ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date = $_GET['date'] ?? '';
$classe = $_GET['classe'] ?? '';

$vols = [];
$search_performed = false;

// Si une recherche est lancée
if ($depart && $arrivee && $date) {
    $search_performed = true;
    $vols = search_vols($pdo, $depart, $arrivee, $date, $classe);
}
?>
<link rel="stylesheet" href="assets/css/recherche.css">
<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-recherche.php';
include __DIR__ . '/layouts/footer.php';
?>
