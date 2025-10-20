<?php
/**
 * Header pour le module COMPAGNIE
 * Variables attendues: $page_title, $breadcrumb (optionnel), $compagnie_nom
 */

// Vérifier l'authentification
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'COMPAGNIE') {
    header("Location: /app/auth/connexion-compagnie.php");
    exit();
}

$compagnie_nom = $_SESSION['compagnie_nom'] ?? 'Ma Compagnie';
$user_prenom = $_SESSION['user_prenom'] ?? 'Utilisateur';
$user_nom = $_SESSION['user_nom'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Compagnie') ?> - FlyManager</title>
    <link rel="stylesheet" href="/public/main.css">
    <link rel="stylesheet" href="assets/css/base.css">
</head>
<body class="compagnie-layout">

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
                <!-- Nom de la compagnie -->
                <div class="topbar-compagnie">
                    <svg class="topbar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="topbar-compagnie-nom"><?= htmlspecialchars($compagnie_nom) ?></span>
                </div>

                <!-- User dropdown -->
                <div class="topbar-item dropdown" id="user-dropdown">
                    <button class="topbar-button" onclick="toggleDropdown('user-dropdown')">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1)) ?>
                        </div>
                        <span class="topbar-username"><?= htmlspecialchars($user_prenom) ?></span>
                        <svg class="dropdown-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu">
                        <a href="profil.php" class="dropdown-item">
                            <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span>Mon profil</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="/src/controllers/logout.php" class="dropdown-item">
                            <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-wrapper">
