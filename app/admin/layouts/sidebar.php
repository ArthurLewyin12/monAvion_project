<?php
$current_page = $current_page ?? '';
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?= url('app/landing/index.php') ?>" class="sidebar-logo">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 32px; height: 32px;">
                <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2" />
            </svg>
            <span class="logo-text">MonVolEnLigne</span>
        </a>
        <span class="sidebar-badge">Admin</span>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link <?= $current_page === 'dashboard' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
                <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2" />
            </svg>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="utilisateurs.php" class="sidebar-link <?= $current_page === 'utilisateurs' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sidebar-text">Utilisateurs</span>
        </a>

        <a href="demandes-agences.php" class="sidebar-link <?= $current_page === 'demandes-agences' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sidebar-text">Demandes Agences</span>
            <?php
            if (isset($stats) && $stats['demandes_agences_attente'] > 0) {
                echo '<span class="sidebar-badge-count">' . $stats['demandes_agences_attente'] . '</span>';
            }
            ?>
        </a>

        <a href="demandes-compagnies.php" class="sidebar-link <?= $current_page === 'demandes-compagnies' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2" />
            </svg>
            <span class="sidebar-text">Demandes Compagnies</span>
            <?php
            if (isset($stats) && $stats['demandes_compagnies_attente'] > 0) {
                echo '<span class="sidebar-badge-count">' . $stats['demandes_compagnies_attente'] . '</span>';
            }
            ?>
        </a>

        <a href="vols.php" class="sidebar-link <?= $current_page === 'vols' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 16V8C21 6.89543 20.1046 6 19 6H5C3.89543 6 3 6.89543 3 8V16C3 17.1046 3.89543 18 5 18H19C20.1046 18 21 17.1046 21 16Z" stroke="currentColor" stroke-width="2" />
            </svg>
            <span class="sidebar-text">Vols</span>
        </a>

        <a href="reservations.php" class="sidebar-link <?= $current_page === 'reservations' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M21 12V19C21 19.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V8C3 7.46957 3.21071 6.96086 3.58579 6.58579C3.96086 6.21071 4.46957 6 5 6H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sidebar-text">Réservations</span>
        </a>

        <a href="messages-contact.php" class="sidebar-link <?= $current_page === 'messages' ? 'active' : '' ?>">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sidebar-text">Messages</span>
            <?php
            if (isset($stats) && $stats['messages_non_traites'] > 0) {
                echo '<span class="sidebar-badge-count">' . $stats['messages_non_traites'] . '</span>';
            }
            ?>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= url('src/controllers/logout.php') ?>" class="sidebar-link">
            <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="sidebar-text">Déconnexion</span>
        </a>
    </div>
</aside>