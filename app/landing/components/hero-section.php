<?php // components/hero-section.php ?>
<section class="hero-section">
    <div class="hero-gradient-bg"></div>
    <div class="hero-particles" id="heroParticles"></div>

    <div class="container hero-content">
        <div class="hero-text animate-on-scroll is-visible">
            <span class="hero-badge">
                <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                Plateforme B2B Nouvelle Génération
            </span>

            <h1 class="hero-title">
                Toutes les compagnies aériennes,<br>
                <span class="hero-title-highlight">une seule interface.</span>
            </h1>

            <p class="hero-description">
                Gagnez du temps, optimisez vos réservations et offrez le meilleur service à vos clients en accédant à un inventaire de vols centralisé de <strong>dizaines de compagnies</strong>.
            </p>

            <div class="hero-cta-group">
                <a href="vols.php" class="btn-hero btn-hero-primary">
                    <svg class="btn-hero-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    Rechercher un vol
                </a>
                <a href="contact.php" class="btn-hero btn-hero-secondary">
                    <svg class="btn-hero-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    Demander une démo
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-number">50+</div>
                    <div class="hero-stat-label">Compagnies aériennes</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-number">1000+</div>
                    <div class="hero-stat-label">Vols par jour</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-number">24/7</div>
                    <div class="hero-stat-label">Support dédié</div>
                </div>
            </div>
        </div>

        <div class="hero-visual animate-on-scroll">
            <!-- Illustration SVG d'avion moderne -->
            <div class="hero-illustration">
                <svg class="hero-plane" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Avion stylisé -->
                    <g class="plane-body">
                        <ellipse cx="200" cy="150" rx="120" ry="40" fill="url(#planeGradient)"/>
                        <path d="M80 150 L60 180 L80 160 Z" fill="var(--color-primary-400)"/>
                        <path d="M320 150 L340 180 L320 160 Z" fill="var(--color-primary-400)"/>
                        <circle cx="200" cy="150" r="15" fill="var(--color-secondary-400)"/>
                        <rect x="180" y="140" width="40" height="8" rx="4" fill="var(--color-accent-300)"/>
                    </g>
                    <!-- Lignes de vol -->
                    <path class="flight-path flight-path-1" d="M50 100 Q150 80 250 120" stroke="var(--color-accent-300)" stroke-width="2" stroke-dasharray="5,5" opacity="0.6"/>
                    <path class="flight-path flight-path-2" d="M50 200 Q150 220 250 180" stroke="var(--color-secondary-300)" stroke-width="2" stroke-dasharray="5,5" opacity="0.6"/>
                    <path class="flight-path flight-path-3" d="M350 80 Q280 100 200 150" stroke="var(--color-primary-300)" stroke-width="2" stroke-dasharray="5,5" opacity="0.6"/>

                    <defs>
                        <linearGradient id="planeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:var(--color-primary-500);stop-opacity:1" />
                            <stop offset="100%" style="stop-color:var(--color-primary-300);stop-opacity:1" />
                        </linearGradient>
                    </defs>
                </svg>

                <!-- Cercles décoratifs flottants -->
                <div class="hero-floating-circle hero-circle-1"></div>
                <div class="hero-floating-circle hero-circle-2"></div>
                <div class="hero-floating-circle hero-circle-3"></div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="hero-scroll-indicator">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M19 12l-7 7-7-7"/>
        </svg>
    </div>
</section>
