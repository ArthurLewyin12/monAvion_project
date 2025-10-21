<?php
$page_title = "Contact - MonVolEnLigne";
include 'layouts/header.php';
?>

<link rel="stylesheet" href="assets/css/contact.css">

<section class="contact-section">
    <div class="contact-container">
        <!-- PARTIE GAUCHE : Formulaire -->
        <div class="contact-form-side">
            <div class="contact-form-content">
                <!-- En-tête -->
                <div class="contact-header">
                    <span class="contact-badge">
                        <svg class="badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        Contactez-nous
                    </span>
                    <h1 class="contact-title">Parlons de votre projet</h1>
                    <p class="contact-subtitle">
                        Notre équipe est à votre disposition pour répondre à toutes vos questions et vous accompagner dans vos besoins.
                    </p>
                </div>

                <!-- Formulaire -->
                <form class="contact-form" id="contactForm" action="../src/controllers/contact_process.php" method="POST">
                    <!-- Ligne Nom + Email -->
                    <div class="form-row">
                        <!-- Nom -->
                        <div class="form-group">
                            <label class="form-label" for="name">Nom complet</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <input
                                    required
                                    class="form-input"
                                    type="text"
                                    id="name"
                                    name="name"
                                    placeholder="Jean Dupont"
                                    autocomplete="name" />
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
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
                                    placeholder="jean.dupont@example.com"
                                    autocomplete="email" />
                            </div>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="form-group">
                        <label class="form-label" for="phone">Téléphone <span class="optional-label">(optionnel)</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <input
                                class="form-input"
                                type="tel"
                                id="phone"
                                name="phone"
                                placeholder="+33 6 12 34 56 78"
                                autocomplete="tel" />
                        </div>
                    </div>

                    <!-- Sujet -->
                    <div class="form-group">
                        <label class="form-label" for="subject">Sujet</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            <select
                                required
                                class="form-input form-select"
                                id="subject"
                                name="subject">
                                <option value="" disabled selected>Choisissez un sujet</option>
                                <option value="demo">Demande de démo</option>
                                <option value="information">Demande d'information</option>
                                <option value="partnership">Partenariat</option>
                                <option value="support">Support technique</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label class="form-label" for="message">Message</label>
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
                                placeholder="Décrivez-nous votre besoin..."></textarea>
                        </div>
                        <div class="character-count">
                            <span id="charCount">0</span> / 500 caractères
                        </div>
                    </div>

                    <!-- Bouton d'envoi -->
                    <button type="submit" class="contact-button">
                        <span class="contact-button-text">Envoyer le message</span>
                        <svg class="contact-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                    </button>

                    <!-- Message de succès/erreur (caché par défaut) -->
                    <div class="form-message form-message-success" id="successMessage" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <span>Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- PARTIE DROITE : Informations -->
        <div class="contact-info-side">
            <div class="contact-info-content">
                <!-- Decoration blobs -->
                <div class="info-blob info-blob-1"></div>
                <div class="info-blob info-blob-2"></div>

                <!-- Titre -->
                <div class="info-header">
                    <h2 class="info-title">Nos coordonnées</h2>
                    <p class="info-description">
                        Vous préférez nous joindre directement ? Voici toutes nos informations de contact.
                    </p>
                </div>

                <!-- Cartes d'info -->
                <div class="info-cards">
                    <!-- Email -->
                    <div class="info-card">
                        <div class="info-card-icon info-icon-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Email</h3>
                            <a href="mailto:contact@monVolEnLigne.com" class="info-card-link">contact@monVolEnLigne.com</a>
                            <p class="info-card-subtitle">Réponse sous 24h</p>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="info-card">
                        <div class="info-card-icon info-icon-secondary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                        </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Téléphone</h3>
                            <a href="tel:+33123456789" class="info-card-link">+33 1 23 45 67 89</a>
                            <p class="info-card-subtitle">Lun-Ven 9h-18h</p>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="info-card">
                        <div class="info-card-icon info-icon-accent">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Adresse</h3>
                            <p class="info-card-text">123 Avenue des Champs-Élysées<br>75008 Paris, France</p>
                        </div>
                    </div>

                    <!-- Horaires -->
                    <div class="info-card">
                        <div class="info-card-icon info-icon-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <div class="info-card-content">
                            <h3 class="info-card-title">Horaires d'ouverture</h3>
                            <p class="info-card-text">Lundi - Vendredi : 9h - 18h<br>Samedi : 10h - 16h<br>Dimanche : Fermé</p>
                        </div>
                    </div>
                </div>

                <!-- Réseaux sociaux -->
                <div class="social-links">
                    <h3 class="social-title">Suivez-nous</h3>
                    <div class="social-icons">
                        <a href="#" class="social-icon" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#" class="social-icon" aria-label="Twitter">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                        <a href="#" class="social-icon" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                        <a href="#" class="social-icon" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/contact-form.js"></script>

<?php
include 'layouts/footer.php';
?>