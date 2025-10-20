<?php
/**
 * Layout principal pour la page recherche de vols
 * Variables attendues:
 * - $depart, $arrivee, $date, $classe (critères de recherche)
 * - $vols_disponibles (résultats)
 * - $search_performed (boolean)
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

    <!-- Formulaire de recherche -->
    <?php include __DIR__ . '/../components/formulaire-recherche-vol.php'; ?>

    <!-- Résultats de recherche -->
    <?php if ($search_performed): ?>
        <div class="search-results">
            <?php if (empty($vols_disponibles)): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="empty-state">
                            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <h3 class="empty-title">Aucun vol trouvé</h3>
                            <p class="empty-text">
                                Aucun vol disponible ne correspond à vos critères de recherche.
                                Essayez de modifier les dates ou les aéroports.
                            </p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="results-header">
                    <h2 class="results-title">
                        <?= count($vols_disponibles) ?> vol(s) trouvé(s)
                    </h2>
                    <p class="results-subtitle">
                        <?= htmlspecialchars($depart) ?> → <?= htmlspecialchars($arrivee) ?>
                        le <?= date('d/m/Y', strtotime($date)) ?>
                    </p>
                </div>

                <?php include __DIR__ . '/../components/liste-vols-disponibles.php'; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>
