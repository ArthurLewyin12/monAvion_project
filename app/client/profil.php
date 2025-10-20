<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/client_data.php';

$user_id = $_SESSION['user_id'];

// Préparer les données
$user = get_user_info($pdo, $user_id);

if (!$user) {
    header('Location: home.php');
    exit;
}

$errors = $_SESSION['profil_errors'] ?? [];
$success = $_SESSION['profil_success'] ?? null;
unset($_SESSION['profil_errors'], $_SESSION['profil_success']);
?>
<link rel="stylesheet" href="assets/css/profil.css">
<?php
include __DIR__ . '/layouts/header.php';
include __DIR__ . '/layouts/main-profil.php';
include __DIR__ . '/layouts/footer.php';
?>
