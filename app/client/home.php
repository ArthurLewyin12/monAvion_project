<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

$user_id = $_SESSION['user_id'];
$prenom = $_SESSION['user_prenom'];
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);

// Préparer les données
$stats = get_client_stats($pdo, $user_id);
$reservations = get_client_recent_reservations($pdo, $user_id, 3);
?>
<link rel="stylesheet" href="assets/css/home.css">
<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-home.php';
include __DIR__ . '/layouts/footer.php';
?>
