<?php
// Inclure la configuration
require_once __DIR__ . '/../../../config/config.php';

// Vérifier que l'utilisateur est connecté et est un CLIENT
if (
    !isset($_SESSION["logged_in"]) ||
    !$_SESSION["logged_in"] ||
    $_SESSION["user_type"] !== "CLIENT"
) {
    header("Location:" . url('app/auth/connexion.php'));
    exit();
}

$prenom = $_SESSION["user_prenom"] ?? "Client";
$nom = $_SESSION["user_nom"] ?? "";
$avatar = $_SESSION["user_avatar"] ?? null;
$current_page = basename($_SERVER["PHP_SELF"], ".php");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonVolEnLigne - Espace Client</title>
    <link rel="stylesheet" href="<?= asset('main.css') ?>">
    <link rel="stylesheet" href="../landing/assets/css/header.css">
    <link rel="stylesheet" href="../landing/assets/css/footer.css">
    <link rel="stylesheet" href="<?= asset('assets/css/animations.css') ?>">
    <link rel="stylesheet" href="assets/css/base.css">
</head>

<body class="bg-gray-50">
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <a href="<?= url('app/landing/index.php') ?>">MonVolEnLigne</a>
            </div>
            <nav class="main-nav">
                <button class="menu-toggle-button" aria-controls="main-navigation" aria-expanded="false">Menu</button>
                <ul class="nav-list" id="main-navigation">
                    <li class="nav-item">
                        <a href="home.php" class="nav-link <?= $current_page ===
                                                                "home"
                                                                ? "active"
                                                                : "" ?>">
                            Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="recherche-vols.php" class="nav-link <?= $current_page ===
                                                                            "recherche-vols"
                                                                            ? "active"
                                                                            : "" ?>">
                            Rechercher un vol
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="mes-reservations.php" class="nav-link <?= $current_page ===
                                                                            "mes-reservations"
                                                                            ? "active"
                                                                            : "" ?>">
                            Mes réservations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profil.php" class="nav-link <?= $current_page ===
                                                                    "profil"
                                                                    ? "active"
                                                                    : "" ?>">
                            Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('src/controllers/logout.php') ?>" class="nav-button">
                            Déconnexion
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>