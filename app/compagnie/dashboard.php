<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/compagnie_data.php';

// Récupérer l'ID de la compagnie
$user_id = $_SESSION['user_id'];
$compagnie_id = $_SESSION['compagnie_id'] ?? get_compagnie_id_from_user($pdo, $user_id);

if (!$compagnie_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de la compagnie.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Récupérer les informations de la compagnie
$compagnie_info = get_compagnie_info($pdo, $compagnie_id);
$_SESSION['compagnie_nom'] = $compagnie_info['nom_compagnie'] ?? 'Ma Compagnie';

// Récupérer les stats et données
$stats = get_compagnie_stats($pdo, $compagnie_id);
$vols_recents = get_compagnie_recent_vols($pdo, $compagnie_id, 5);
$prochains_departs = get_compagnie_prochains_departs($pdo, $compagnie_id, 5);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Dashboard";
$current_page = "dashboard";
?>

<link rel="stylesheet" href="assets/css/dashboard.css">

<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-dashboard.php';
include __DIR__ . '/layouts/footer.php';
?>
