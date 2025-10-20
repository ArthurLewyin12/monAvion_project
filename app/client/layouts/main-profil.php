<?php
// Layout: Profil utilisateur
// Variables attendues: $user, $errors, $success
?>
<div class="client-container profil-container">
    <div class="profil-header">
        <div class="avatar-container">
            <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
        </div>
        <div class="profil-name"><?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></div>
        <div class="profil-email"><?= htmlspecialchars($user['email']) ?></div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

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

    <!-- Onglets -->
    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('informations')">Informations</button>
        <button class="tab-btn" onclick="switchTab('securite')">Sécurité</button>
        <button class="tab-btn" onclick="switchTab('preferences')">Préférences</button>
    </div>

    <!-- Onglet Informations -->
    <div class="tab-content active" id="tab-informations">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Informations personnelles</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="/src/controllers/profil_process.php">
                    <input type="hidden" name="action" value="update_info">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label" for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-input" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" class="form-input" value="<?= htmlspecialchars($user['nom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" class="form-input" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Onglet Sécurité -->
    <div class="tab-content" id="tab-securite">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Modifier le mot de passe</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="/src/controllers/profil_process.php">
                    <input type="hidden" name="action" value="update_password">
                    <div class="form-group">
                        <label class="form-label" for="current_password">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="new_password">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" class="form-input" required minlength="8">
                        <small style="color: oklch(0.60 0.05 250);">Minimum 8 caractères</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Modifier le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Onglet Préférences -->
    <div class="tab-content" id="tab-preferences">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Préférences du compte</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="/src/controllers/profil_process.php">
                    <input type="hidden" name="action" value="update_preferences">

                    <div class="info-item-display">
                        <div>
                            <div class="info-item-label">Newsletter</div>
                            <div style="font-size: 0.9rem; color: oklch(0.60 0.05 250); margin-top: 0.25rem;">
                                Recevoir les offres et actualités
                            </div>
                        </div>
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="newsletter" <?= $user['abonnement_newsletter'] ? 'checked' : '' ?>
                                    style="width: 20px; height: 20px; cursor: pointer;">
                                <span><?= $user['abonnement_newsletter'] ? 'Activé' : 'Désactivé' ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="info-item-display">
                        <div>
                            <div class="info-item-label">Statut du compte</div>
                            <div style="font-size: 0.9rem; color: oklch(0.60 0.05 250); margin-top: 0.25rem;">
                                État actuel de votre compte
                            </div>
                        </div>
                        <div>
                            <span class="badge badge-<?= strtolower($user['statut_actuel']) ?>">
                                <?= htmlspecialchars($user['statut_actuel']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="info-item-display">
                        <div>
                            <div class="info-item-label">Membre depuis</div>
                            <div style="font-size: 0.9rem; color: oklch(0.60 0.05 250); margin-top: 0.25rem;">
                                Date de création du compte
                            </div>
                        </div>
                        <div class="info-item-value">
                            <?= date('d/m/Y', strtotime($user['date_creation'])) ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Enregistrer les préférences
                    </button>
                </form>
            </div>
        </div>

        <!-- Zone dangereuse -->
        <div class="danger-zone">
            <div class="danger-zone-title">Zone de danger</div>
            <p style="color: oklch(0.40 0.10 25); margin-bottom: 1rem;">
                La suppression de votre compte est irréversible. Toutes vos données seront perdues.
            </p>
            <button class="btn btn-danger" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) { alert('Fonctionnalité en cours de développement'); }">
                Supprimer mon compte
            </button>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById('tab-' + tabName).classList.add('active');
    event.target.classList.add('active');
}
</script>
