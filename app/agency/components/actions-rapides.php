<?php
/**
 * Composant: Actions rapides (liens vers les pages principales)
 * Variables attendues: aucune
 */
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Actions rapides</h2>
    </div>
    <div class="card-body">
        <div class="quick-actions">
            <a href="recherche-vols.php" class="quick-action-item">
                <div class="quick-action-icon quick-action-icon-primary">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="quick-action-content">
                    <h3 class="quick-action-title">Rechercher un vol</h3>
                    <p class="quick-action-text">Trouver des vols disponibles</p>
                </div>
                <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

            <a href="mes-reservations.php" class="quick-action-item">
                <div class="quick-action-icon quick-action-icon-success">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="quick-action-content">
                    <h3 class="quick-action-title">Mes réservations</h3>
                    <p class="quick-action-text">Gérer toutes les réservations</p>
                </div>
                <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

            <a href="demandes-clients.php" class="quick-action-item">
                <div class="quick-action-icon quick-action-icon-info">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="quick-action-content">
                    <h3 class="quick-action-title">Demandes clients</h3>
                    <p class="quick-action-text">Voir les nouvelles demandes</p>
                </div>
                <svg class="quick-action-arrow" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>
</div>
