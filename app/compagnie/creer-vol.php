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

// Récupérer les avions de la compagnie
$avions = get_compagnie_avions($pdo, $compagnie_id);

// Messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Variables pour le header
$page_title = "Créer un vol";
$current_page = "mes-vols";
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php'],
    ['label' => 'Mes vols', 'url' => 'mes-vols.php'],
    ['label' => 'Créer un vol']
];
?>

<link rel="stylesheet" href="assets/css/creer-vol.css">

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
            <h1 class="page-title">Créer un vol</h1>
            <p class="page-subtitle">Programmez un nouveau vol pour votre compagnie</p>
        </div>
    </div>

    <?php if (empty($avions)): ?>
        <!-- Pas d'avions -->
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <h3 class="empty-title">Aucun avion disponible</h3>
                    <p class="empty-text">Vous devez d'abord ajouter un avion à votre flotte pour créer un vol.</p>
                    <a href="creer-avion.php" class="btn btn-primary" style="margin-top: 1.5rem;">Ajouter un avion</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Formulaire -->
        <form method="POST" action="/src/controllers/compagnie/creer_vol.php" class="vol-form">

            <div class="form-grid">
                <!-- Colonne principale -->
                <div class="form-main">

                    <!-- Informations du vol -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Informations du vol</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Numéro de vol *</label>
                                    <input
                                        type="text"
                                        name="numero_vol"
                                        class="form-input"
                                        placeholder="Ex: AF1234"
                                        required
                                        maxlength="20"
                                    >
                                    <p class="form-hint">Code unique du vol (2-20 caractères)</p>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Avion *</label>
                                    <select name="avion_id" class="form-select" required id="avion_select">
                                        <option value="">Sélectionnez un avion</option>
                                        <?php foreach ($avions as $avion): ?>
                                            <option
                                                value="<?= $avion['id'] ?>"
                                                data-sieges='<?= htmlspecialchars($avion['sieges_par_classe']) ?>'
                                            >
                                                <?= htmlspecialchars($avion['modele']) ?> (<?= $avion['nombre_sieges_total'] ?> sièges)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Aéroport de départ (IATA) *</label>
                                    <input
                                        type="text"
                                        name="aeroport_depart"
                                        class="form-input"
                                        placeholder="Ex: CDG"
                                        required
                                        maxlength="3"
                                        pattern="[A-Z]{3}"
                                        style="text-transform: uppercase;"
                                    >
                                    <p class="form-hint">Code IATA 3 lettres (ex: CDG, JFK, DXB)</p>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Aéroport d'arrivée (IATA) *</label>
                                    <input
                                        type="text"
                                        name="aeroport_arrivee"
                                        class="form-input"
                                        placeholder="Ex: JFK"
                                        required
                                        maxlength="3"
                                        pattern="[A-Z]{3}"
                                        style="text-transform: uppercase;"
                                    >
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Date et heure de départ *</label>
                                    <input
                                        type="datetime-local"
                                        name="date_depart"
                                        class="form-input"
                                        required
                                        min="<?= date('Y-m-d\TH:i') ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Date et heure d'arrivée *</label>
                                    <input
                                        type="datetime-local"
                                        name="date_arrivee"
                                        class="form-input"
                                        required
                                        min="<?= date('Y-m-d\TH:i') ?>"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarifs -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Tarifs par classe</h2>
                            <p class="card-subtitle">Définissez les prix selon les classes disponibles de l'avion</p>
                        </div>
                        <div class="card-body">
                            <div id="tarifs_container">
                                <p class="tarifs-placeholder">Sélectionnez d'abord un avion pour définir les tarifs</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sidebar résumé -->
                <div class="form-sidebar">
                    <div class="card summary-card">
                        <div class="card-header">
                            <h3 class="card-title">Résumé</h3>
                        </div>
                        <div class="card-body">
                            <div class="summary-section">
                                <h4 class="summary-title">Avion sélectionné</h4>
                                <p class="summary-value" id="summary_avion">Aucun</p>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="summary-section">
                                <h4 class="summary-title">Capacité totale</h4>
                                <p class="summary-value" id="summary_capacite">-</p>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="summary-section" id="summary_classes">
                                <h4 class="summary-title">Classes disponibles</h4>
                                <p class="summary-placeholder">-</p>
                            </div>

                            <div class="summary-note">
                                <svg class="note-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="12" cy="16" r="1" fill="currentColor"/>
                                </svg>
                                <span>Au moins un tarif doit être défini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <a href="mes-vols.php" class="btn btn-outline">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Créer le vol
                </button>
            </div>

        </form>
    <?php endif; ?>

</div>

<script>
// Gestion dynamique des tarifs selon l'avion sélectionné
const avionSelect = document.getElementById('avion_select');
const tarifsContainer = document.getElementById('tarifs_container');
const summaryAvion = document.getElementById('summary_avion');
const summaryCapacite = document.getElementById('summary_capacite');
const summaryClasses = document.getElementById('summary_classes');

avionSelect?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];

    if (!selectedOption.value) {
        tarifsContainer.innerHTML = '<p class="tarifs-placeholder">Sélectionnez d\'abord un avion pour définir les tarifs</p>';
        summaryAvion.textContent = 'Aucun';
        summaryCapacite.textContent = '-';
        summaryClasses.innerHTML = '<p class="summary-placeholder">-</p>';
        return;
    }

    const avionNom = selectedOption.textContent;
    const siegesData = JSON.parse(selectedOption.dataset.sieges || '{}');

    // Mettre à jour le résumé
    summaryAvion.textContent = avionNom.split('(')[0].trim();
    summaryCapacite.textContent = avionNom.match(/\((\d+) sièges\)/)?.[1] + ' sièges' || '-';

    // Afficher les classes disponibles
    let classesHTML = '<div class="summary-classes-list">';
    for (const [classe, sieges] of Object.entries(siegesData)) {
        classesHTML += `<div class="summary-class-item">
            <span class="class-name">${classe}</span>
            <span class="class-seats">${sieges} sièges</span>
        </div>`;
    }
    classesHTML += '</div>';
    summaryClasses.innerHTML = classesHTML;

    // Générer les champs de tarifs
    let tarifsHTML = '<div class="tarifs-grid">';

    const classIcons = {
        'ECONOMIQUE': '<path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'AFFAIRE': '<path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2"/>',
        'PREMIERE': '<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
    };

    const classColors = {
        'ECONOMIQUE': 'var(--color-info)',
        'AFFAIRE': 'var(--color-warning)',
        'PREMIERE': 'var(--color-secondary)'
    };

    const classLabels = {
        'ECONOMIQUE': 'Économique',
        'AFFAIRE': 'Affaires',
        'PREMIERE': 'Première Classe'
    };

    for (const [classe, sieges] of Object.entries(siegesData)) {
        const inputName = `prix_${classe.toLowerCase()}`;
        tarifsHTML += `
            <div class="tarif-card">
                <div class="tarif-header">
                    <div class="tarif-icon" style="background: ${classColors[classe]};">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            ${classIcons[classe]}
                        </svg>
                    </div>
                    <div>
                        <h4 class="tarif-title">${classLabels[classe]}</h4>
                        <p class="tarif-subtitle">${sieges} sièges disponibles</p>
                    </div>
                </div>
                <div class="tarif-input-group">
                    <input
                        type="number"
                        name="${inputName}"
                        class="tarif-input"
                        placeholder="0.00"
                        min="0"
                        step="0.01"
                    >
                    <span class="tarif-currency">€</span>
                </div>
            </div>
        `;
    }

    tarifsHTML += '</div>';
    tarifsContainer.innerHTML = tarifsHTML;
});
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
