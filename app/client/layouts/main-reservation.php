<?php
// Layout: Processus de réservation
// Variables attendues: $vol, $sieges, $classe_selectionnee, $prenom, $nom, $email, $telephone, $errors, $duree_heures, $duree_minutes
?>
<div class="client-container reservation-container">
    <div class="page-header">
        <h1 class="page-title">Réservation de vol</h1>
        <p class="page-subtitle">Complétez votre réservation</p>
    </div>

    <!-- Résumé du vol -->
    <div class="vol-summary">
        <div class="vol-summary-route">
            <?= htmlspecialchars($vol['aeroport_depart']) ?> → <?= htmlspecialchars($vol['aeroport_arrivee']) ?>
        </div>
        <div class="vol-summary-details">
            <div>Vol <?= htmlspecialchars($vol['numero_vol']) ?></div>
            <div><?= htmlspecialchars($vol['nom_compagnie']) ?></div>
            <div><?= date('d/m/Y à H:i', strtotime($vol['date_depart'])) ?></div>
            <div>Classe: <?= htmlspecialchars($classe_selectionnee) ?></div>
            <div>Durée: <?= $duree_heures ?>h <?= $duree_minutes ?>min</div>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Erreurs :</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/src/controllers/reservation_process.php" id="reservationForm">
        <input type="hidden" name="vol_id" value="<?= $vol['id'] ?>">
        <input type="hidden" name="classe" value="<?= htmlspecialchars($classe_selectionnee) ?>">

        <!-- Sélection du siège -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Sélectionnez votre siège</h2>
            </div>
            <div class="card-body">
                <?php if (empty($sieges)): ?>
                    <div class="alert alert-warning">Aucun siège disponible pour cette classe.</div>
                <?php else: ?>
                    <div class="sieges-grid">
                        <?php foreach ($sieges as $siege): ?>
                            <label class="siege-btn" title="Siège <?= htmlspecialchars($siege['numero_siege']) ?>">
                                <input
                                    type="radio"
                                    name="siege_id"
                                    value="<?= $siege['id'] ?>"
                                    required
                                    style="display: none;"
                                    onchange="this.parentElement.classList.add('selected'); document.querySelectorAll('.siege-btn').forEach(el => { if(el !== this.parentElement) el.classList.remove('selected'); });"
                                >
                                <?= htmlspecialchars($siege['numero_siege']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p style="margin-top: 1rem; color: oklch(0.60 0.05 250); font-size: 0.9rem;">
                        Cliquez sur un siège pour le sélectionner (<?= count($sieges) ?> disponible(s))
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informations passager -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Informations du passager</h2>
            </div>
            <div class="card-body">
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label" for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" class="form-input" value="<?= htmlspecialchars($prenom) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" class="form-input" value="<?= htmlspecialchars($nom) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="telephone">Téléphone *</label>
                        <input type="tel" id="telephone" name="telephone" class="form-input" value="<?= htmlspecialchars($telephone) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="passeport">Numéro de passeport (optionnel)</label>
                        <input type="text" id="passeport" name="passeport" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="nationalite">Nationalité (optionnel)</label>
                        <input type="text" id="nationalite" name="nationalite" class="form-input" value="Française">
                    </div>
                </div>
            </div>
        </div>

        <!-- Paiement (simulé) -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Mode de paiement (simulé)</h2>
            </div>
            <div class="card-body">
                <div class="payment-methods">
                    <div class="payment-method">
                        <input type="radio" name="mode_paiement" value="CARTE" id="carte" checked>
                        <label for="carte">Carte bancaire</label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" name="mode_paiement" value="PAYPAL" id="paypal">
                        <label for="paypal">PayPal</label>
                    </div>
                </div>

                <div class="alert alert-info" style="margin-top: 1rem;">
                    Le paiement est simulé dans le cadre de ce projet. Aucune transaction réelle ne sera effectuée.
                </div>

                <div class="prix-total">
                    <div class="prix-ligne">
                        <span>Vol <?= htmlspecialchars($classe_selectionnee) ?></span>
                        <span><?= number_format($vol['prix'], 2) ?> €</span>
                    </div>
                    <div class="prix-ligne">
                        <span>Taxes et frais</span>
                        <span>0.00 €</span>
                    </div>
                    <div class="prix-ligne">
                        <strong>TOTAL À PAYER</strong>
                        <strong><?= number_format($vol['prix'], 2) ?> €</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; gap: 1rem; justify-content: space-between; margin-top: 2rem;">
            <a href="recherche-vols.php" class="btn btn-outline">Retour à la recherche</a>
            <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem;">
                Confirmer et payer
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('reservationForm').addEventListener('submit', function(e) {
    const siegeSelected = document.querySelector('input[name="siege_id"]:checked');
    if (!siegeSelected) {
        e.preventDefault();
        alert('Veuillez sélectionner un siège avant de continuer.');
        return false;
    }
});
</script>
