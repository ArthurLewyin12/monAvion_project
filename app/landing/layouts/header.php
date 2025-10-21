<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$user_type = $_SESSION['user_type'] ?? null;
$user_prenom = $_SESSION['user_prenom'] ?? '';

// Déterminer le lien vers le dashboard selon le type d'utilisateur
$dashboard_link = '';
$dashboard_text = 'Mon Espace';
if ($is_logged_in) {
    switch ($user_type) {
        case 'ADMIN':
            $dashboard_link = '../../app/admin/dashboard.php';
            $dashboard_text = 'Admin';
            break;
        case 'CLIENT':
            $dashboard_link = '../../app/client/home.php';
            $dashboard_text = 'Mon Espace';
            break;
        case 'AGENCE':
            $dashboard_link = '../../app/agency/home.php';
            $dashboard_text = 'Mon Agence';
            break;
        case 'COMPAGNIE':
            $dashboard_link = '../../app/compagnie/home.php';
            $dashboard_text = 'Ma Compagnie';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonVolEnLigne - Plateforme de Réservation</title>
    <link rel="stylesheet" href="../../public/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="../../public/assets/css/animations.css">

    <!-- Styles des composants -->
    <link rel="stylesheet" href="assets/css/hero-section.css">
    <link rel="stylesheet" href="assets/css/features-section.css">
    <link rel="stylesheet" href="assets/css/how-it-works-section.css">
    <link rel="stylesheet" href="assets/css/testimonial-section.css">
    <link rel="stylesheet" href="assets/css/partners-cta-section.css">
    <link rel="stylesheet" href="assets/css/cta-section.css">
</head>

<body class="bg-gray-50">
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <a href="index.php">MonVolEnLigne</a>
            </div>
            <nav class="main-nav">
                <button class="menu-toggle-button" aria-controls="main-navigation" aria-expanded="false">Menu</button>
                <ul class="nav-list" id="main-navigation">
                    <li class="nav-item"><a href="index.php" class="nav-link active">Accueil</a></li>
                    <li class="nav-item"><a href="vols.php" class="nav-link">Rechercher un Vol</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item"><a href="<?= htmlspecialchars($dashboard_link) ?>" class="nav-button"><?= htmlspecialchars($dashboard_text) ?></a></li>
                        <li class="nav-item"><a href="../../src/controllers/logout.php" class="nav-link">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="../../app/auth/connexion.php" class="nav-button">Connexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>