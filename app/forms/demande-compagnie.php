<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devenir Compagnie Partenaire - MonVolEnLigne</title>
    <link rel="stylesheet" href="/public/main.css">
    <link rel="stylesheet" href="assets/css/demande-partner.css">
</head>

<body>
    <div class="partner-request-container">
        <!-- PARTIE GAUCHE : Formulaire -->
        <div class="partner-form-side">
            <!-- Bouton retour -->
            <a href="/app/landing/index.php" class="back-button" title="Retour à l'accueil">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
                Retour
            </a>

            <div class="partner-form-content">
                <!-- Logo -->
                <div class="partner-logo">
                    <div class="partner-logo-icon partner-logo-airline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <h1 class="partner-logo-text">MonVolEnLigne</h1>
                </div>

                <!-- En-tête -->
                <div class="partner-header">
                    <h2 class="partner-title">Devenez compagnie partenaire</h2>
                    <p class="partner-subtitle">Remplissez ce formulaire et notre équipe vous contactera sous 48h pour étudier votre demande de partenariat.</p>
                </div>

                <!-- Formulaire -->
                <form class="partner-form" id="partnerForm" action="/src/controllers/demande_compagnie_process.php" method="POST">
                    <!-- Nom de la compagnie -->
                    <div class="form-group">
                        <label class="form-label" for="company_name">Nom de la compagnie aérienne</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                <path d="M2 17l10 5 10-5M2 12l10 5 10-5" />
                            </svg>
                            <input
                                required
                                class="form-input"
                                type="text"
                                id="company_name"
                                name="company_name"
                                placeholder="Air France"
                                autocomplete="organization" />
                        </div>
                    </div>

                    <!-- Ligne Code IATA + Pays -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="iata_code">Code IATA</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                    <path d="M9 3v18M15 3v18M3 9h18M3 15h18" />
                                </svg>
                                <input
                                    required
                                    class="form-input"
                                    type="text"
                                    id="iata_code"
                                    name="iata_code"
                                    placeholder="AF"
                                    maxlength="2"
                                    style="text-transform: uppercase;" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="country">Pays d'origine</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="2" y1="12" x2="22" y2="12" />
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                </svg>
                                <input
                                    required
                                    class="form-input"
                                    type="text"
                                    id="country"
                                    name="country"
                                    placeholder="France"
                                    autocomplete="country-name" />
                            </div>
                        </div>
                    </div>

                    <!-- Contact principal -->
                    <div class="form-group">
                        <label class="form-label" for="contact_name">Nom du contact principal</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            <input
                                required
                                class="form-input"
                                type="text"
                                id="contact_name"
                                name="contact_name"
                                placeholder="Jean Dupont"
                                autocomplete="name" />
                        </div>
                    </div>

                    <!-- Ligne Email + Téléphone -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="email">Email professionnel</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                                <input
                                    required
                                    class="form-input"
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="contact@airfrance.fr"
                                    autocomplete="email" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Téléphone</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                                <input
                                    required
                                    class="form-input"
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    placeholder="+33 1 23 45 67 89"
                                    autocomplete="tel" />
                            </div>
                        </div>
                    </div>

                    <!-- Flotte -->
                    <div class="form-group">
                        <label class="form-label" for="fleet_size">Taille de la flotte <span class="optional-label">(nombre d'avions)</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23" />
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                            <input
                                class="form-input"
                                type="number"
                                id="fleet_size"
                                name="fleet_size"
                                placeholder="150"
                                min="1" />
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label class="form-label" for="message">Présentez votre projet de partenariat</label>
                        <div class="input-wrapper">
                            <svg class="input-icon input-icon-textarea" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                            <textarea
                                required
                                class="form-input form-textarea"
                                id="message"
                                name="message"
                                rows="5"
                                placeholder="Décrivez vos objectifs, vos destinations principales, et ce que vous attendez de ce partenariat..."></textarea>
                        </div>
                    </div>

                    <!-- Bouton de soumission -->
                    <button type="submit" class="partner-button partner-button-airline">
                        <span class="partner-button-text">Soumettre la demande</span>
                        <svg class="partner-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                    </button>

                    <!-- Message de succès -->
                    <div class="form-message form-message-success" id="successMessage" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <span>Votre demande a été envoyée avec succès ! Nous vous contacterons sous 48h.</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- PARTIE DROITE : Avantages -->
        <div class="partner-info-side partner-info-airline">
            <div class="partner-info-content">
                <!-- Decoration blobs -->
                <div class="info-blob info-blob-1"></div>
                <div class="info-blob info-blob-2"></div>

                <!-- Contenu -->
                <div class="info-text">
                    <h2 class="info-title">Pourquoi devenir partenaire ?</h2>
                    <p class="info-description">
                        Rejoignez notre réseau et bénéficiez d'une distribution élargie auprès de milliers d'agences de voyage partenaires.
                    </p>

                    <!-- Liste des avantages -->
                    <div class="info-benefits">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </div>
                            <div class="benefit-content">
                                <h3 class="benefit-title">Distribution étendue</h3>
                                <p class="benefit-text">Accédez à un réseau de 200+ agences partenaires</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                                </svg>
                            </div>
                            <div class="benefit-content">
                                <h3 class="benefit-title">Gestion simplifiée</h3>
                                <p class="benefit-text">Plateforme intuitive pour gérer vos tarifs et inventaire</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                                </svg>
                            </div>
                            <div class="benefit-content">
                                <h3 class="benefit-title">Analytics en temps réel</h3>
                                <p class="benefit-text">Suivez vos performances avec des rapports détaillés</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                            </div>
                            <div class="benefit-content">
                                <h3 class="benefit-title">Sécurité garantie</h3>
                                <p class="benefit-text">Paiements sécurisés et conformité réglementaire</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="info-stats">
                        <div class="info-stat">
                            <div class="info-stat-number">50+</div>
                            <div class="info-stat-label">Compagnies partenaires</div>
                        </div>
                        <div class="info-stat">
                            <div class="info-stat-number">200+</div>
                            <div class="info-stat-label">Agences connectées</div>
                        </div>
                        <div class="info-stat">
                            <div class="info-stat-number">1000+</div>
                            <div class="info-stat-label">Vols/jour</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/public/assets/js/partner-form.js"></script>
</body>

</html>