<?php
/**
 * Composant: Informations de l'agence
 * Variables attendues: $agence_info (array avec infos de l'agence)
 */
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Informations de l'agence</h2>
        <a href="profil.php" class="card-link">Modifier →</a>
    </div>
    <div class="card-body">
        <div class="info-list">
            <div class="info-item">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="info-content">
                    <p class="info-label">Nom de l'agence</p>
                    <p class="info-value"><?= htmlspecialchars($agence_info['nom_agence']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="info-content">
                    <p class="info-label">Numéro de licence</p>
                    <p class="info-value"><?= htmlspecialchars($agence_info['numero_licence']) ?></p>
                </div>
            </div>

            <?php if ($agence_info['telephone']): ?>
            <div class="info-item">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7293C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1469 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.7869 20.5341 11.19 18.85C8.77382 17.3147 6.72533 15.2662 5.18999 12.85C3.49997 10.2412 2.44824 7.27097 2.11999 4.17997C2.095 3.90344 2.12787 3.62474 2.21649 3.3616C2.30512 3.09846 2.44756 2.85666 2.63476 2.6516C2.82196 2.44653 3.0498 2.28268 3.30379 2.1705C3.55777 2.05831 3.83233 2.00024 4.10999 1.99997H7.10999C7.5953 1.9952 8.06579 2.16705 8.43376 2.48351C8.80173 2.79996 9.04207 3.23942 9.10999 3.71997C9.23662 4.68004 9.47144 5.6227 9.80999 6.52997C9.94454 6.88505 9.97366 7.27269 9.89384 7.64382C9.81401 8.01495 9.62886 8.35885 9.35999 8.62997L8.08999 9.89997C9.51355 12.4135 11.5864 14.4864 14.1 15.91L15.37 14.64C15.6411 14.3711 15.985 14.186 16.3561 14.1062C16.7273 14.0263 17.1149 14.0555 17.47 14.19C18.3773 14.5285 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="info-content">
                    <p class="info-label">Téléphone</p>
                    <p class="info-value"><?= htmlspecialchars($agence_info['telephone']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($agence_info['email']): ?>
            <div class="info-item">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="info-content">
                    <p class="info-label">Email</p>
                    <p class="info-value"><?= htmlspecialchars($agence_info['email']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-item">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <div class="info-content">
                    <p class="info-label">Membre depuis</p>
                    <p class="info-value">
                        <?= date('d/m/Y', strtotime($agence_info['date_creation'])) ?>
                    </p>
                </div>
            </div>

            <!-- Badge statut -->
            <div class="info-status">
                <span class="badge badge-<?= strtolower($agence_info['statut_actuel']) ?>">
                    <?= htmlspecialchars($agence_info['statut_actuel']) ?>
                </span>
            </div>
        </div>
    </div>
</div>
