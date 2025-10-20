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

// Récupérer l'ID de la demande
$demande_id = $_GET['id'] ?? null;

if (!$demande_id) {
    $_SESSION['error_message'] = "Demande introuvable.";
    header("Location: demandes-clients.php");
    exit();
}

// Récupérer les détails de la demande
$demande = get_demande_details($pdo, $demande_id, $agence_id);

if (!$demande) {
    $_SESSION['error_message'] = "Demande introuvable ou accès refusé.";
    header("Location: demandes-clients.php");
    exit();
}

// Récupérer les informations de l'agence
$agence_info = get_agency_info($pdo, $agence_id);
$_SESSION['agency_nom'] = $agence_info['nom_agence'] ?? 'Mon Agence';

// Compter les nouvelles demandes pour le badge
$new_demandes_count = count_new_demandes($pdo, $agence_id);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Détail de la demande";
$current_page = "demandes-clients";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Demandes clients', 'url' => 'demandes-clients.php'],
    ['label' => 'Détail de la demande']
];
?>

<link rel="stylesheet" href="assets/css/detail-demande.css">

<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-detail-demande.php';
include __DIR__ . '/layouts/footer.php';
?>
