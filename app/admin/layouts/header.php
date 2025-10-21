<?php

/**
 * Header pour le module ADMIN
 * Variables attendues: $page_title, $breadcrumb (optionnel)
 */

// Inclure la configuration
require_once __DIR__ . '/../../../config/config.php';

// Vérifier l'authentification
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'ADMIN') {
    header("Location: " . url('app/auth/connexion.php'));
    exit();
}

$user_prenom = $_SESSION['user_prenom'] ?? 'Admin';
$user_nom = $_SESSION['user_nom'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Admin') ?> - MonVolEnLigne</title>
    <link rel="stylesheet" href="<?= asset('main.css') ?>">
    <link rel="stylesheet" href="assets/css/base.css">
</head>

<body class="admin-layout">

    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">
        <header class="topbar">
            <div class="topbar-content">
                <div class="topbar-left">
                    <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
                        <nav class="breadcrumb">
                            <?php foreach ($breadcrumb as $index => $item): ?>
                                <?php if ($index > 0): ?>
                                    <span class="breadcrumb-separator">/</span>
                                <?php endif; ?>
                                <?php if (isset($item['url'])): ?>
                                    <a href="<?= htmlspecialchars($item['url']) ?>" class="breadcrumb-item">
                                        <?= htmlspecialchars($item['label']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="breadcrumb-item breadcrumb-current">
                                        <?= htmlspecialchars($item['label']) ?>
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </nav>
                    <?php else: ?>
                        <h1 class="topbar-title"><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h1>
                    <?php endif; ?>
                </div>

                <div class="topbar-right">
                    <!-- Badge Admin -->
                    <div class="topbar-admin-badge">
                        <svg class="topbar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 15C15.866 15 19 11.866 19 8C19 4.13401 15.866 1 12 1C8.13401 1 5 4.13401 5 8C5 11.866 8.13401 15 12 15Z" stroke="currentColor" stroke-width="2" />
                            <path d="M8.21 13.89L7 23L12 20L17 23L15.79 13.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="topbar-admin-text">Administrateur</span>
                    </div>

                    <!-- User dropdown -->
                    <div class="topbar-item dropdown" id="user-dropdown">
                        <button class="topbar-button" onclick="toggleDropdown('user-dropdown')">
                            <div class="user-avatar admin-avatar">
                                <?= strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1)) ?>
                            </div>
                            <span class="topbar-username"><?= htmlspecialchars($user_prenom) ?></span>
                            <svg class="dropdown-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="dropdown-menu">
                            <a href="<?= url('src/controllers/logout.php') ?>" class="dropdown-item">
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
        </header>

        <main class="main-wrapper">