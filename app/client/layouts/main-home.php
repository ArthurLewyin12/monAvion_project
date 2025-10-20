<?php
// Layout principal pour la page d'accueil
// Variables attendues: $prenom, $success_message, $stats, $reservations
?>

<div class="client-container">
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <div class="page-header">
        <h1 class="page-title">Bonjour, <?= htmlspecialchars($prenom) ?> !</h1>
        <p class="page-subtitle">Bienvenue sur votre espace personnel FlyManager</p>
    </div>

    <?php include __DIR__ . '/../components/dashboard-stats.php'; ?>
    <?php include __DIR__ . '/../components/recent-reservations.php'; ?>
    <?php include __DIR__ . '/../components/quick-actions.php'; ?>
</div>
