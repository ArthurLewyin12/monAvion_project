<?php

/**
 * Layout principal pour la page Demander l'assistance d'une agence
 * Variables attendues: $agences, $error_message, $success_message
 */
?>

<div class="client-container">

    <!-- Messages -->
    <?php if (isset($success_message) && $success_message): ?>
        <div class="alert alert-success">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.76489 14.1003 1.98232 16.07 2.86" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message) && $error_message): ?>
        <div class="alert alert-error">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($demande_errors)): ?>
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 1.5rem;">
                <?php foreach ($demande_errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- En-tête -->
    <div class="page-header">
        <h1 class="page-title">Demander l'aide d'une agence</h1>
        <p class="page-subtitle">
            Vous n'avez pas trouvé de vol correspondant à vos critères ? Nos agences partenaires peuvent vous aider à organiser votre voyage.
        </p>
    </div>

    <div class="assistance-grid">
        <!-- Formulaire principal -->
        <div class="assistance-main">
            <form method="POST" action="../../src/controllers/client/creer_demande_vol.php" class="assistance-form">

                <!-- Sélection de l'agence -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Choisir une agence</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($agences)): ?>
                            <div class="alert alert-warning">
                                <svg class="alert-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.29 3.86L1.82 18C1.64537 18.3024 1.55296 18.6453 1.55199 18.9945C1.55101 19.3437 1.64151 19.6871 1.81442 19.9905C1.98733 20.2939 2.23672 20.5467 2.53771 20.7239C2.83869 20.9011 3.18082 20.9962 3.53 21H20.47C20.8192 20.9962 21.1613 20.9011 21.4623 20.7239C21.7633 20.5467 22.0127 20.2939 22.1856 19.9905C22.3585 19.6871 22.449 19.3437 22.448 18.9945C22.447 18.6453 22.3546 18.3024 22.18 18L13.71 3.86C13.5317 3.56611 13.2807 3.32312 12.9812 3.15448C12.6817 2.98585 12.3437 2.89725 12 2.89725C11.6563 2.89725 11.3183 2.98585 11.0188 3.15448C10.7193 3.32312 10.4683 3.56611 10.29 3.86Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12 9V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    <circle cx="12" cy="17" r="1" fill="currentColor" />
                                </svg>
                                Aucune agence disponible pour le moment. Veuillez réessayer plus tard.
                            </div>
                        <?php else: ?>
                            <div class="agences-grid">
                                <?php foreach ($agences as $agence): ?>
                                    <label class="agence-card">
                                        <input
                                            type="radio"
                                            name="agence_id"
                                            value="<?= $agence['id'] ?>"
                                            class="agence-radio"
                                            required>
                                        <div class="agence-content">
                                            <div class="agence-header">
                                                <svg class="agence-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <h3 class="agence-name"><?= htmlspecialchars($agence['nom_agence']) ?></h3>
                                            </div>
                                            <?php if ($agence['adresse']): ?>
                                                <p class="agence-detail">
                                                    <svg class="detail-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" />
                                                    </svg>
                                                    <?= htmlspecialchars($agence['pays']) ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if ($agence['telephone']): ?>
                                                <p class="agence-detail">
                                                    <svg class="detail-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1469 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.7869 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27097 2.11999 4.17997C2.095 3.90344 2.12787 3.62474 2.21649 3.3616C2.30512 3.09846 2.44756 2.85666 2.63476 2.6516C2.82196 2.44653 3.0498 2.28268 3.30379 2.1705C3.55777 2.05831 3.83233 2.00024 4.10999 1.99997H7.10999C7.5953 1.9952 8.06579 2.16705 8.43376 2.48351C8.80173 2.79996 9.04207 3.23942 9.10999 3.71997C9.23662 4.68004 9.47144 5.6227 9.80999 6.52997C9.94454 6.88505 9.97366 7.27269 9.89384 7.64382C9.81401 8.01495 9.62886 8.35885 9.35999 8.62997L8.08999 9.89997C9.51355 12.4135 11.5864 14.4864 14.1 15.91L15.37 14.64C15.6411 14.3711 15.985 14.186 16.3561 14.1062C16.7273 14.0263 17.1149 14.0555 17.47 14.19C18.3773 14.5285 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <?= htmlspecialchars($agence['telephone']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($agences)): ?>
                    <!-- Détails du voyage -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Détails de votre voyage</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="aeroport_depart" class="form-label">Aéroport de départ *</label>
                                    <input
                                        type="text"
                                        id="aeroport_depart"
                                        name="aeroport_depart"
                                        class="form-input"
                                        required
                                        placeholder="ex: CDG, ORY..."
                                        pattern="[A-Z]{3}"
                                        title="Code IATA à 3 lettres (ex: CDG)"
                                        value="<?= htmlspecialchars($_GET['depart'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label for="aeroport_arrivee" class="form-label">Aéroport d'arrivée *</label>
                                    <input
                                        type="text"
                                        id="aeroport_arrivee"
                                        name="aeroport_arrivee"
                                        class="form-input"
                                        required
                                        placeholder="ex: JFK, LAX..."
                                        pattern="[A-Z]{3}"
                                        title="Code IATA à 3 lettres (ex: JFK)"
                                        value="<?= htmlspecialchars($_GET['arrivee'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label for="date_depart" class="form-label">Date de départ *</label>
                                    <input
                                        type="date"
                                        id="date_depart"
                                        name="date_depart"
                                        class="form-input"
                                        required
                                        min="<?= date('Y-m-d') ?>"
                                        value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label for="date_retour" class="form-label">Date de retour (optionnel)</label>
                                    <input
                                        type="date"
                                        id="date_retour"
                                        name="date_retour"
                                        class="form-input"
                                        min="<?= date('Y-m-d') ?>">
                                </div>

                                <div class="form-group">
                                    <label for="nombre_passagers" class="form-label">Nombre de passagers *</label>
                                    <input
                                        type="number"
                                        id="nombre_passagers"
                                        name="nombre_passagers"
                                        class="form-input"
                                        required
                                        min="1"
                                        max="20"
                                        value="1">
                                </div>

                                <div class="form-group">
                                    <label for="classe_desiree" class="form-label">Classe souhaitée *</label>
                                    <select id="classe_desiree" name="classe_desiree" class="form-input" required>
                                        <option value="ECONOMIQUE" <?= ($_GET['classe'] ?? '') === 'ECONOMIE' ? 'selected' : '' ?>>Économique</option>
                                        <option value="AFFAIRE" <?= ($_GET['classe'] ?? '') === 'AFFAIRES' ? 'selected' : '' ?>>Affaires</option>
                                        <option value="PREMIERE" <?= ($_GET['classe'] ?? '') === 'PREMIERE' ? 'selected' : '' ?>>Première classe</option>
                                    </select>
                                </div>

                                <div class="form-group form-group-full">
                                    <label for="notes_supplementaires" class="form-label">Notes supplémentaires</label>
                                    <textarea
                                        id="notes_supplementaires"
                                        name="notes_supplementaires"
                                        class="form-textarea"
                                        rows="4"
                                        placeholder="Précisez vos besoins particuliers, préférences, budget, etc."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        Envoyer ma demande
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Sidebar informations -->
        <div class="assistance-sidebar">
            <div class="card info-card">
                <div class="card-header">
                    <h3 class="card-title">Comment ça fonctionne ?</h3>
                </div>
                <div class="card-body">
                    <div class="info-steps">
                        <div class="info-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4 class="step-title">Choisissez une agence</h4>
                                <p class="step-text">Sélectionnez l'agence partenaire qui vous convient.</p>
                            </div>
                        </div>
                        <div class="info-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4 class="step-title">Décrivez votre voyage</h4>
                                <p class="step-text">Remplissez les détails de votre voyage souhaité.</p>
                            </div>
                        </div>
                        <div class="info-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4 class="step-title">Recevez une réponse</h4>
                                <p class="step-text">L'agence vous contactera avec des propositions adaptées.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card info-card">
                <div class="card-body">
                    <div class="info-highlight">
                        <svg class="highlight-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                            <path d="M12 16V12M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <p class="highlight-text">
                            <strong>Service gratuit</strong><br>
                            La demande est gratuite. Vous ne payez que si vous réservez un vol via l'agence.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>