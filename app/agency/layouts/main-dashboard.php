<?php
/**
 * Layout principal pour la page dashboard agence
 * Variables attendues:
 * - $success_message, $error_message
 * - $agence_info (informations de l'agence)
 * - $stats (statistiques)
 * - $reservations_recentes (dernières réservations)
 */
?>

<div class="agency-container">

    <!-- Messages de feedback -->
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-error">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <!-- En-tête de bienvenue -->
    <div class="dashboard-header">
        <div class="dashboard-welcome">
            <h1 class="dashboard-title">
                Bienvenue, <?= htmlspecialchars($agence_info['nom_agence']) ?> 👋
            </h1>
            <p class="dashboard-subtitle">
                Voici un aperçu de votre activité
            </p>
        </div>
    </div>

    <!-- Inclure les composants -->
    <?php include __DIR__ . '/../components/stats-dashboard.php'; ?>

    <div class="dashboard-grid">
        <div class="dashboard-col-main">
            <?php include __DIR__ . '/../components/reservations-recentes.php'; ?>
        </div>
        <div class="dashboard-col-sidebar">
            <?php include __DIR__ . '/../components/actions-rapides.php'; ?>
            <?php include __DIR__ . '/../components/info-agence.php'; ?>
        </div>
    </div>

</div>
