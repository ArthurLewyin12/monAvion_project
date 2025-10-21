<?php

/**
 * Header (topbar) pour l'espace agence
 * Contient: breadcrumb, notifications, user dropdown
 * Variables attendues: $page_title, $breadcrumb (optionnel)
 */

// Vérifier que l'utilisateur est connecté et est une AGENCE
if (
    !isset($_SESSION["logged_in"]) ||
    !$_SESSION["logged_in"] ||
    $_SESSION["user_type"] !== "AGENCE"
) {
    header("Location: /app/auth/connexion-agence.php");
    exit();
}

$prenom = $_SESSION["user_prenom"] ?? "Agence";
$nom = $_SESSION["user_nom"] ?? "";
$email = $_SESSION["user_email"] ?? "";
$avatar = $_SESSION["user_avatar"] ?? null;
$nom_agence = $_SESSION["agency_nom"] ?? "Mon Agence";

// Breadcrumb par défaut basé sur la page
$page_title = $page_title ?? "Dashboard";
$breadcrumb = $breadcrumb ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - MonVolEnLigne Agence</title>

    <!-- CSS Global -->
    <link rel="stylesheet" href="/public/main.css">
    <link rel="stylesheet" href="/public/assets/css/animations.css">

    <!-- CSS Agency -->
    <link rel="stylesheet" href="assets/css/base.css">
</head>

<body class="agency-layout">

    <?php
    // Inclure la sidebar
    include __DIR__ . '/sidebar.php';
    ?>

    <!-- Main wrapper (contenu à droite de la sidebar) -->
    <div class="main-wrapper">

        <!-- Header/Topbar avec backdrop-blur -->
        <header class="topbar">
            <div class="topbar-content">

                <!-- Breadcrumb / Page title -->
                <div class="topbar-left">
                    <?php if (!empty($breadcrumb)): ?>
                        <nav class="breadcrumb">
                            <?php foreach ($breadcrumb as $index => $item): ?>
                                <?php if ($index > 0): ?>
                                    <span class="breadcrumb-separator">/</span>
                                <?php endif; ?>

                                <?php if (isset($item['url'])): ?>
                                    <a href="<?= htmlspecialchars($item['url']) ?>" class="breadcrumb-link">
                                        <?= htmlspecialchars($item['label']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="breadcrumb-current">
                                        <?= htmlspecialchars($item['label']) ?>
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </nav>
                    <?php else: ?>
                        <h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
                    <?php endif; ?>
                </div>

                <!-- Actions à droite: notifications + user menu -->
                <div class="topbar-right">

                    <!-- Notifications -->
                    <div class="topbar-item dropdown" id="notif-dropdown">
                        <button class="topbar-btn" onclick="toggleDropdown('notif-dropdown')">
                            <svg class="topbar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <?php if (isset($new_demandes_count) && $new_demandes_count > 0): ?>
                                <span class="notif-badge"><?= $new_demandes_count ?></span>
                            <?php endif; ?>
                        </button>

                        <!-- Dropdown notifications -->
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <h3 class="dropdown-title">Notifications</h3>
                            </div>
                            <div class="dropdown-body">
                                <?php if (isset($new_demandes_count) && $new_demandes_count > 0): ?>
                                    <a href="demandes-clients.php" class="notif-item">
                                        <div class="notif-icon notif-icon-info">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="notif-content">
                                            <p class="notif-text">
                                                <strong><?= $new_demandes_count ?> nouvelle(s) demande(s)</strong> de clients
                                            </p>
                                            <span class="notif-time">À traiter</span>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <div class="dropdown-empty">
                                        <p>Aucune nouvelle notification</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- User menu -->
                    <div class="topbar-item dropdown" id="user-dropdown">
                        <button class="topbar-user-btn" onclick="toggleDropdown('user-dropdown')">
                            <?php if ($avatar): ?>
                                <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="user-avatar">
                            <?php else: ?>
                                <div class="user-avatar-placeholder">
                                    <?= strtoupper(substr($prenom, 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($prenom . ' ' . $nom) ?></span>
                                <span class="user-role"><?= htmlspecialchars($nom_agence) ?></span>
                            </div>
                            <svg class="dropdown-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <!-- Dropdown user menu -->
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header">
                                <p class="dropdown-user-email"><?= htmlspecialchars($email) ?></p>
                            </div>
                            <div class="dropdown-body">
                                <a href="profil.php" class="dropdown-link">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                                    </svg>
                                    Mon profil
                                </a>
                                <a href="dashboard.php" class="dropdown-link">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                                        <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                                        <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                                        <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                                    </svg>
                                    Dashboard
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="/src/controllers/logout.php" class="dropdown-link dropdown-link-danger">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    Déconnexion
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- Main content area -->
        <main class="main-content">