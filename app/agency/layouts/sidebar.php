<?php
/**
 * Sidebar de navigation pour l'espace agence
 * Variables attendues: $current_page (défini dans chaque page)
 */

$current_page = $current_page ?? basename($_SERVER["PHP_SELF"], ".php");
?>

<!-- Sidebar verticale avec navigation -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <svg class="logo-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 16V8C21 6.9 20.1 6 19 6H5C3.9 6 3 6.9 3 8V16C3 17.1 3.9 18 5 18H19C20.1 18 21 17.1 21 16Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 6V18M12 6L8 10M12 6L16 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="logo-text">FlyManager</span>
        </div>
        <span class="sidebar-badge">Agence</span>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link <?= $current_page === 'dashboard' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="recherche-vols.php" class="sidebar-link <?= $current_page === 'recherche-vols' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span class="sidebar-text">Rechercher un vol</span>
        </a>

        <a href="mes-reservations.php" class="sidebar-link <?= $current_page === 'mes-reservations' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sidebar-text">Mes réservations</span>
        </a>

        <a href="demandes-clients.php" class="sidebar-link <?= $current_page === 'demandes-clients' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sidebar-text">Demandes clients</span>
            <?php if (isset($new_demandes_count) && $new_demandes_count > 0): ?>
                <span class="sidebar-badge-notif"><?= $new_demandes_count ?></span>
            <?php endif; ?>
        </a>

        <div class="sidebar-divider"></div>

        <a href="profil.php" class="sidebar-link <?= $current_page === 'profil' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
            </svg>
            <span class="sidebar-text">Profil</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="/src/controllers/logout.php" class="sidebar-link-danger">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="sidebar-text">Déconnexion</span>
        </a>
    </div>
</aside>
