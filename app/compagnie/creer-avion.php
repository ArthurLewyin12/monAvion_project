<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/functions/compagnie_data.php';

// Récupérer l'ID de la compagnie
$user_id = $_SESSION['user_id'];
$compagnie_id = $_SESSION['compagnie_id'] ?? get_compagnie_id_from_user($pdo, $user_id);

if (!$compagnie_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de la compagnie.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Récupérer les informations de la compagnie
$compagnie_info = get_compagnie_info($pdo, $compagnie_id);
$_SESSION['compagnie_nom'] = $compagnie_info['nom_compagnie'] ?? 'Ma Compagnie';

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Ajouter un avion";
$current_page = "ma-flotte";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Ma flotte', 'url' => 'ma-flotte.php'],
    ['label' => 'Ajouter un avion']
];
?>

<link rel="stylesheet" href="assets/css/creer-avion.css">

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="compagnie-container">

    <!-- Messages -->
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

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Ajouter un avion</h1>
            <p class="page-subtitle">Ajoutez un nouvel avion à votre flotte</p>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="form-layout">
        <div class="form-main">
            <form method="POST" action="/src/controllers/compagnie/creer_avion.php" class="avion-form">

                <!-- Informations générales -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Informations générales</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Modèle de l'avion *</label>
                            <input
                                type="text"
                                name="modele"
                                class="form-input"
                                placeholder="Ex: Boeing 737-800, Airbus A320neo"
                                required
                            >
                            <p class="form-hint">Nom commercial du modèle d'avion</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description (optionnel)</label>
                            <textarea
                                name="description"
                                class="form-textarea"
                                rows="4"
                                placeholder="Caractéristiques particulières, année de mise en service, équipements..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Configuration des sièges -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Configuration des sièges</h2>
                        <p class="card-subtitle">Définissez le nombre de sièges par classe</p>
                    </div>
                    <div class="card-body">
                        <div class="seats-config-grid">
                            <!-- Économique -->
                            <div class="seat-class-card">
                                <div class="seat-class-header">
                                    <div class="seat-class-icon" style="background: var(--color-info);">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="seat-class-title">Classe Économique</h3>
                                        <p class="seat-class-desc">Sièges standard</p>
                                    </div>
                                </div>
                                <input
                                    type="number"
                                    name="sieges_economique"
                                    class="seat-input"
                                    min="0"
                                    max="500"
                                    value="0"
                                    id="sieges_economique"
                                >
                            </div>

                            <!-- Affaires -->
                            <div class="seat-class-card">
                                <div class="seat-class-header">
                                    <div class="seat-class-icon" style="background: var(--color-warning);">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="seat-class-title">Classe Affaires</h3>
                                        <p class="seat-class-desc">Sièges premium</p>
                                    </div>
                                </div>
                                <input
                                    type="number"
                                    name="sieges_affaire"
                                    class="seat-input"
                                    min="0"
                                    max="200"
                                    value="0"
                                    id="sieges_affaire"
                                >
                            </div>

                            <!-- Première -->
                            <div class="seat-class-card">
                                <div class="seat-class-header">
                                    <div class="seat-class-icon" style="background: var(--color-secondary);">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="seat-class-title">Première Classe</h3>
                                        <p class="seat-class-desc">Sièges luxe</p>
                                    </div>
                                </div>
                                <input
                                    type="number"
                                    name="sieges_premiere"
                                    class="seat-input"
                                    min="0"
                                    max="100"
                                    value="0"
                                    id="sieges_premiere"
                                >
                            </div>
                        </div>

                        <div class="seats-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total de sièges :</span>
                                <span class="summary-value" id="total_sieges">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="ma-flotte.php" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Ajouter l'avion
                    </button>
                </div>

            </form>
        </div>

        <!-- Sidebar aide -->
        <div class="form-sidebar">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aide</h3>
                </div>
                <div class="card-body">
                    <div class="help-section">
                        <h4 class="help-title">Configuration typique</h4>
                        <ul class="help-list">
                            <li><strong>Boeing 737-800</strong> : 162-189 sièges (config 3-3)</li>
                            <li><strong>Airbus A320</strong> : 150-180 sièges</li>
                            <li><strong>Boeing 777-300ER</strong> : 300-400 sièges</li>
                            <li><strong>Airbus A380</strong> : 500-850 sièges</li>
                        </ul>
                    </div>

                    <div class="help-section">
                        <h4 class="help-title">Conseils</h4>
                        <ul class="help-list">
                            <li>Au moins une classe doit avoir des sièges</li>
                            <li>Maximum 1000 sièges par avion</li>
                            <li>Les sièges seront automatiquement générés (1A, 1B, 2A...)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Calculer le total de sièges en temps réel
const economique = document.getElementById('sieges_economique');
const affaire = document.getElementById('sieges_affaire');
const premiere = document.getElementById('sieges_premiere');
const totalElement = document.getElementById('total_sieges');

function updateTotal() {
    const total = parseInt(economique.value || 0) +
                  parseInt(affaire.value || 0) +
                  parseInt(premiere.value || 0);
    totalElement.textContent = total;

    // Changer la couleur si dépassement
    if (total > 1000) {
        totalElement.style.color = 'var(--color-error)';
    } else {
        totalElement.style.color = 'var(--color-primary)';
    }
}

economique.addEventListener('input', updateTotal);
affaire.addEventListener('input', updateTotal);
premiere.addEventListener('input', updateTotal);
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
