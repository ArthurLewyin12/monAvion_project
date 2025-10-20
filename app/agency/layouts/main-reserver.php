<?php
/**
 * Layout principal pour la page réserver un vol
 * Variables attendues: $vol, $classe, $sieges_disponibles
 */
?>

<div class="agency-container">

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

    <?php if (!empty($reservation_errors)): ?>
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 1.5rem;">
                <?php foreach ($reservation_errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="booking-grid">
        <!-- Formulaire principal -->
        <div class="booking-main">
            <form method="POST" action="/src/controllers/agency/creer_reservation.php" class="booking-form" id="bookingForm">
                <input type="hidden" name="vol_id" value="<?= $vol['id'] ?>">
                <input type="hidden" name="classe" value="<?= htmlspecialchars($classe) ?>">
                <input type="hidden" name="tarif_id" value="<?= $vol['tarif_id'] ?>">

                <!-- Informations passager -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Informations du passager</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input
                                    type="text"
                                    id="prenom"
                                    name="prenom"
                                    class="form-input"
                                    required
                                    placeholder="Jean"
                                >
                            </div>

                            <div class="form-group">
                                <label for="nom" class="form-label">Nom *</label>
                                <input
                                    type="text"
                                    id="nom"
                                    name="nom"
                                    class="form-input"
                                    required
                                    placeholder="Dupont"
                                >
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-input"
                                    required
                                    placeholder="jean.dupont@example.com"
                                >
                            </div>

                            <div class="form-group">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input
                                    type="tel"
                                    id="telephone"
                                    name="telephone"
                                    class="form-input"
                                    placeholder="+33 6 12 34 56 78"
                                >
                            </div>

                            <div class="form-group">
                                <label for="date_naissance" class="form-label">Date de naissance *</label>
                                <input
                                    type="date"
                                    id="date_naissance"
                                    name="date_naissance"
                                    class="form-input"
                                    required
                                    max="<?= date('Y-m-d') ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label for="numero_passeport" class="form-label">N° Passeport</label>
                                <input
                                    type="text"
                                    id="numero_passeport"
                                    name="numero_passeport"
                                    class="form-input"
                                    placeholder="12AB34567"
                                >
                            </div>

                            <div class="form-group form-group-full">
                                <label for="nationalite" class="form-label">Nationalité</label>
                                <input
                                    type="text"
                                    id="nationalite"
                                    name="nationalite"
                                    class="form-input"
                                    placeholder="Française"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sélection de siège -->
                <?php if (!empty($sieges_disponibles)): ?>
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Sélection du siège (optionnel)</h2>
                            <p class="card-subtitle"><?= count($sieges_disponibles) ?> siège(s) disponible(s)</p>
                        </div>
                        <div class="card-body">
                            <div class="seats-grid">
                                <?php foreach ($sieges_disponibles as $siege): ?>
                                    <label class="seat-option">
                                        <input
                                            type="radio"
                                            name="siege_id"
                                            value="<?= $siege['id'] ?>"
                                            class="seat-radio"
                                        >
                                        <div class="seat-box">
                                            <svg class="seat-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span class="seat-number"><?= htmlspecialchars($siege['numero_siege']) ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <p class="seats-note">Si aucun siège n'est sélectionné, un siège sera attribué automatiquement.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Paiement simulé -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Mode de paiement</h2>
                    </div>
                    <div class="card-body">
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="mode_paiement" value="CARTE" class="payment-radio" checked>
                                <div class="payment-box">
                                    <svg class="payment-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="1" y="4" width="22" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M1 10H23" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    <span>Carte bancaire</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="mode_paiement" value="AGENCE" class="payment-radio">
                                <div class="payment-box">
                                    <svg class="payment-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Paiement agence</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Confirmer la réservation
                </button>
            </form>
        </div>

        <!-- Résumé (sidebar) -->
        <div class="booking-sidebar">
            <div class="card summary-card">
                <div class="card-header">
                    <h3 class="card-title">Récapitulatif</h3>
                </div>
                <div class="card-body">
                    <!-- Vol -->
                    <div class="summary-section">
                        <h4 class="summary-title">Vol</h4>
                        <div class="summary-flight">
                            <div class="summary-route">
                                <span class="summary-airport"><?= htmlspecialchars($vol['aeroport_depart']) ?></span>
                                <svg class="summary-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="summary-airport"><?= htmlspecialchars($vol['aeroport_arrivee']) ?></span>
                            </div>
                            <p class="summary-detail">
                                <strong><?= htmlspecialchars($vol['numero_vol']) ?></strong>
                                <br><?= htmlspecialchars($vol['nom_compagnie']) ?>
                            </p>
                            <p class="summary-detail">
                                <?= date('d/m/Y à H:i', strtotime($vol['date_depart'])) ?>
                            </p>
                        </div>
                    </div>

                    <div class="summary-divider"></div>

                    <!-- Classe -->
                    <div class="summary-section">
                        <h4 class="summary-title">Classe</h4>
                        <p class="summary-value"><?= htmlspecialchars($classe) ?></p>
                    </div>

                    <div class="summary-divider"></div>

                    <!-- Prix -->
                    <div class="summary-section">
                        <h4 class="summary-title">Prix total</h4>
                        <p class="summary-price">
                            <?= number_format($vol['prix'], 2, ',', ' ') ?> €
                        </p>
                    </div>

                    <?php if ($vol['disponibilite'] <= 5): ?>
                        <div class="summary-warning">
                            <svg class="warning-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.29 3.86L1.82 18C1.64537 18.3024 1.55296 18.6453 1.55199 18.9945C1.55101 19.3437 1.64151 19.6871 1.81442 19.9905C1.98733 20.2939 2.23672 20.5467 2.53771 20.7239C2.83869 20.9011 3.18082 20.9962 3.53 21H20.47C20.8192 20.9962 21.1613 20.9011 21.4623 20.7239C21.7633 20.5467 22.0127 20.2939 22.1856 19.9905C22.3585 19.6871 22.449 19.3437 22.448 18.9945C22.447 18.6453 22.3546 18.3024 22.18 18L13.71 3.86C13.5317 3.56611 13.2807 3.32312 12.9812 3.15448C12.6817 2.98585 12.3437 2.89725 12 2.89725C11.6563 2.89725 11.3183 2.98585 11.0188 3.15448C10.7193 3.32312 10.4683 3.56611 10.29 3.86Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 9V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="12" cy="17" r="1" fill="currentColor"/>
                            </svg>
                            <span>Plus que <?= $vol['disponibilite'] ?> place(s) disponible(s)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
