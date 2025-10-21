<?php // components/how-it-works-section.php 
?>
<section class="how-it-works-section">
    <div class="container animate-on-scroll">
        <!-- En-tête -->
        <div class="hiw-header">
            <span class="hiw-subtitle">Processus</span>
            <h2 class="hiw-title">Comment <span class="text-gradient">ça marche</span> ?</h2>
            <p class="hiw-description">
                Un flux simple et efficace pour connecter compagnies aériennes, agences de voyage et clients.
            </p>
        </div>

        <!-- Schéma du flux -->
        <div class="hiw-flow">
            <!-- Étape 1: Compagnies Aériennes -->
            <div class="hiw-step">
                <div class="hiw-step-number">01</div>
                <div class="hiw-step-icon hiw-icon-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                        <line x1="12" y1="22.08" x2="12" y2="12" />
                    </svg>
                </div>
                <h3 class="hiw-step-title">Compagnies Aériennes</h3>
                <p class="hiw-step-description">
                    Les compagnies alimentent la plateforme avec leurs vols, avions, tarifs et disponibilités en temps réel.
                </p>
                <div class="hiw-step-badge">Fournisseurs</div>
            </div>

            <!-- Flèche -->
            <div class="hiw-arrow">
                <svg viewBox="0 0 100 100" fill="none">
                    <path d="M10 50 L90 50" stroke="url(#arrowGradient)" stroke-width="3" stroke-dasharray="5,5" />
                    <polygon points="85,45 95,50 85,55" fill="var(--color-secondary-400)" />
                </svg>
            </div>

            <!-- Étape 2: Plateforme MonVolEnLigne -->
            <div class="hiw-step hiw-step-highlight">
                <div class="hiw-step-number">02</div>
                <div class="hiw-step-icon hiw-icon-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="3" width="20" height="14" rx="2" />
                        <line x1="8" y1="21" x2="16" y2="21" />
                        <line x1="12" y1="17" x2="12" y2="21" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                </div>
                <h3 class="hiw-step-title">MonVolEnLigne</h3>
                <p class="hiw-step-description">
                    La plateforme centralise toutes les données et offre une interface unique pour rechercher et réserver.
                </p>
                <div class="hiw-step-badge hiw-badge-highlight">Hub Central</div>
            </div>

            <!-- Flèche -->
            <div class="hiw-arrow">
                <svg viewBox="0 0 100 100" fill="none">
                    <path d="M10 50 L90 50" stroke="url(#arrowGradient)" stroke-width="3" stroke-dasharray="5,5" />
                    <polygon points="85,45 95,50 85,55" fill="var(--color-secondary-400)" />
                </svg>
            </div>

            <!-- Étape 3: Agences de Voyage -->
            <div class="hiw-step">
                <div class="hiw-step-number">03</div>
                <div class="hiw-step-icon hiw-icon-accent">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                        <path d="M19 8v6M16 11h6" />
                    </svg>
                </div>
                <h3 class="hiw-step-title">Agences de Voyage</h3>
                <p class="hiw-step-description">
                    Les agences recherchent, comparent et réservent les meilleurs vols pour leurs clients en quelques clics.
                </p>
                <div class="hiw-step-badge">Utilisateurs</div>
            </div>

            <!-- Flèche -->
            <div class="hiw-arrow">
                <svg viewBox="0 0 100 100" fill="none">
                    <path d="M10 50 L90 50" stroke="url(#arrowGradient)" stroke-width="3" stroke-dasharray="5,5" />
                    <polygon points="85,45 95,50 85,55" fill="var(--color-secondary-400)" />
                </svg>
            </div>

            <!-- Étape 4: Clients Finaux -->
            <div class="hiw-step">
                <div class="hiw-step-number">04</div>
                <div class="hiw-step-icon hiw-icon-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <polyline points="17 11 19 13 23 9" />
                    </svg>
                </div>
                <h3 class="hiw-step-title">Clients Finaux</h3>
                <p class="hiw-step-description">
                    Les voyageurs reçoivent leur billet électronique et profitent du meilleur service au meilleur prix.
                </p>
                <div class="hiw-step-badge">Bénéficiaires</div>
            </div>
        </div>

        <!-- Définitions SVG -->
        <svg width="0" height="0" style="position: absolute;">
            <defs>
                <linearGradient id="arrowGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:var(--color-primary-400);stop-opacity:0.5" />
                    <stop offset="100%" style="stop-color:var(--color-secondary-400);stop-opacity:1" />
                </linearGradient>
            </defs>
        </svg>

        <!-- Chiffres clés -->
        <div class="hiw-stats">
            <div class="hiw-stat-card">
                <div class="hiw-stat-icon">⚡</div>
                <div class="hiw-stat-number">
                    < 2s</div>
                        <div class="hiw-stat-label">Temps de recherche</div>
                </div>
                <div class="hiw-stat-card">
                    <div class="hiw-stat-icon">🎯</div>
                    <div class="hiw-stat-number">100%</div>
                    <div class="hiw-stat-label">Disponibilité temps réel</div>
                </div>
                <div class="hiw-stat-card">
                    <div class="hiw-stat-icon">🔒</div>
                    <div class="hiw-stat-number">Sécurisé</div>
                    <div class="hiw-stat-label">Paiements cryptés</div>
                </div>
            </div>
        </div>
</section>