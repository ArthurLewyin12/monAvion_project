<?php
// Layout: Recherche de vols
// Variables attendues: $depart, $arrivee, $date, $classe, $vols, $search_performed
?>
<div class="client-container">
    <div class="page-header">
        <h1 class="page-title">Rechercher un vol</h1>
        <p class="page-subtitle">Trouvez et réservez votre prochain vol en quelques clics</p>
    </div>

    <?php include __DIR__ . '/../components/search-form.php'; ?>

    <?php if ($search_performed): ?>
        <?php include __DIR__ . '/../components/vols-list.php'; ?>
    <?php else: ?>
        <div class="card">
            <div class="empty-state">
                <h3 class="empty-state-title">Commencez votre recherche</h3>
                <p class="empty-state-text">
                    Remplissez le formulaire ci-dessus pour trouver les vols disponibles.
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>
