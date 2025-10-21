<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devenir Agence Partenaire - MonVolEnLigne</title>
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
                    <div class="partner-logo-icon partner-logo-agency">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <h1 class="partner-logo-text">MonVolEnLigne</h1>
                </div>

                <!-- En-tête -->
                <div class="partner-header">
                    <h2 class="partner-title">Rejoignez notre réseau d'agences</h2>
                    <p class="partner-subtitle">Complétez ce formulaire et notre équipe commerciale vous contactera sous 48h pour finaliser votre inscription.</p>
                </div>

                <!-- Formulaire -->
                <form class="partner-form" id="partnerForm" action="/src/controllers/demande_agence_process.php" method="POST">
                    <!-- Nom de l'agence -->
                    <div class="form-group">
                        <label class="form-label" for="agency_name">Nom de l'agence de voyage</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                            <input
                                required
                                class="form-input"
                                type="text"
                                id="agency_name"
                                name="agency_name"
                                placeholder="Voyages Évasion"
                                autocomplete="organization" />
                        </div>
                    </div>

                    <!-- Ligne Numéro de licence + Pays -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="license_number">Numéro de licence</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                                <input
                                    required
                                    class="form-input"
                                    type="text"
                                    id="license_number"
                                    name="license_number"
                                    placeholder="IM075123456" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="country">Pays</label>
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

                    <!-- Adresse complète -->
                    <div class="form-group">
                        <label class="form-label" for="address">Adresse complète</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <input
                                required
                                class="form-input"
                                type="text"
                                id="address"
                                name="address"
                                placeholder="15 Rue de la Paix, 75002 Paris"
                                autocomplete="street-address" />
                        </div>
                    </div>

                    <!-- Contact principal -->
                    <div class="form-group">
                        <label class="form-label" for="contact_name">Nom du gérant / contact principal</label>
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
                                placeholder="Marie Martin"
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
                                    placeholder="contact@voyagesevasion.fr"
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

                    <!-- Nombre d'employés -->
                    <div class="form-group">
                        <label class="form-label" for="employees_count">Nombre d'employés <span class="optional-label">(optionnel)</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            <input
                                class="form-input"
                                type="number"
                                id="employees_count"
                                name="employees_count"
                                placeholder="5"
                                min="1" />
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label class="form-label" for="message">Parlez-nous de votre agence</label>
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
                                placeholder="Décrivez votre activité, vos spécialités (tourisme d'affaires, loisirs...), votre clientèle, et vos objectifs avec MonVolEnLigne..."></textarea>
                        </div>
                    </div>

                    <!-- Bouton de soumission -->
                    <button type="submit" class="partner-button partner-button-agency">
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
        <div class="partner-info-side partner-info-agency">
            <div class="partner-info-content">
                <!-- Decoration blobs -->
                <div class="info-blob info-blob-1"></div>
                <div class="info-blob info-blob-2"></div>

                <!-- Contenu -->
                <div class="info-text">
                    <h2 class="info-title">Pourquoi nous rejoindre ?</h2>
                    <p class="info-description">
                        Accédez à l'inventaire de plus de 50 compagnies aériennes et simplifiez la gestion de vos réservations avec notre plateforme professionnelle.
                    </p>

                    <!-- Liste des avantages -->
                    <div class="info-benefits">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </div>
                            <div class="benefit-content">
                                <h3 class="benefit-title">Réservation instantanée</h3>
                                <p class="benefit-text">Réservez en temps réel sur 50+ compagnies aériennes</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="1" x2="12" y2="23" />
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </div>
                            <div class="benefit-content">
                                <h3 class="benefit-title">Meilleurs tarifs</h3>
                                <p class="benefit-text">Accès aux tarifs négociés et offres exclusives</p>
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
                                <h3 class="benefit-title">Gestion centralisée</h3>
                                <p class="benefit-text">Toutes vos réservations sur une seule plateforme</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <div class="benefit-content">
                                <h3 class="benefit-title">Support dédié</h3>
                                <p class="benefit-text">Une équipe à votre écoute 24/7 pour vous accompagner</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="info-stats">
                        <div class="info-stat">
                            <div class="info-stat-number">200+</div>
                            <div class="info-stat-label">Agences partenaires</div>
                        </div>
                        <div class="info-stat">
                            <div class="info-stat-number">50+</div>
                            <div class="info-stat-label">Compagnies aériennes</div>
                        </div>
                        <div class="info-stat">
                            <div class="info-stat-number">24/7</div>
                            <div class="info-stat-label">Support client</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/public/assets/js/partner-form.js"></script>
</body>

</html>