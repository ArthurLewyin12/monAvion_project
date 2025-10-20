<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CLIENT') {
    $_SESSION['error_message'] = "Vous devez être connecté pour accéder à cette page.";
    header("Location: /app/auth/connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les agences actives
$agences = get_active_agencies($pdo);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
$demande_errors = $_SESSION['demande_errors'] ?? [];
unset($_SESSION['success_message'], $_SESSION['error_message'], $_SESSION['demande_errors']);

// Variables pour le header
$page_title = "Demander l'aide d'une agence";
?>

<link rel="stylesheet" href="assets/css/demander-assistance.css">

<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-demander-assistance.php';
include __DIR__ . '/layouts/footer.php';
?>
