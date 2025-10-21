<?php // components/partners-cta-section.php ?>
<section class="partners-cta-section">
    <div class="container">
        <div class="partners-cta-grid">

            <!-- CTA Compagnies Aériennes -->
            <div class="partner-cta-card partner-cta-airline animate-on-scroll">
                <div class="partner-cta-background">
                    <div class="partner-blob partner-blob-1"></div>
                </div>

                <div class="partner-cta-content">
                    <div class="partner-cta-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>

                    <h3 class="partner-cta-title">Vous êtes une compagnie aérienne ?</h3>
                    <p class="partner-cta-description">
                        Rejoignez notre réseau de plus de 50 compagnies partenaires et augmentez votre visibilité auprès de milliers d'agences de voyage.
                    </p>

                    <ul class="partner-cta-features">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Distribution étendue</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Gestion simplifiée des tarifs</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Reporting en temps réel</span>
                        </li>
                    </ul>

                    <a href="<?= url('app/forms/demande-compagnie.php') ?>" class="partner-cta-btn partner-cta-btn-airline">
                        <span>Devenir partenaire</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- CTA Agences de Voyage -->
            <div class="partner-cta-card partner-cta-agency animate-on-scroll">
                <div class="partner-cta-background">
                    <div class="partner-blob partner-blob-2"></div>
                </div>

                <div class="partner-cta-content">
                    <div class="partner-cta-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>

                    <h3 class="partner-cta-title">Vous êtes une agence de voyage ?</h3>
                    <p class="partner-cta-description">
                        Accédez à l'inventaire de 50+ compagnies aériennes, simplifiez vos réservations et développez votre activité avec nos outils professionnels.
                    </p>

                    <ul class="partner-cta-features">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Accès aux meilleurs tarifs</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Plateforme de réservation intégrée</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Support dédié 24/7</span>
                        </li>
                    </ul>

                    <a href="<?= url('app/forms/demande-agence.php') ?>" class="partner-cta-btn partner-cta-btn-agency">
                        <span>Rejoignez-nous</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
