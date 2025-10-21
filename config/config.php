<?php

/**
 * Configuration globale de l'application
 *
 * Ce fichier détecte automatiquement le chemin de base du projet
 * Fonctionne avec n'importe quel nom de dossier (monAvion, MonVolEnligne, etc.)
 */

// Détection automatique du BASE_URL
$scriptName = $_SERVER['SCRIPT_NAME']; // Ex: /monAvion/app/landing/index.php
$scriptDir = dirname($scriptName); // Ex: /monAvion/app/landing

// Trouver la racine du projet (remonter jusqu'à trouver le dossier qui contient 'app')
$pathParts = explode('/', trim($scriptDir, '/'));
$baseUrlParts = [];

foreach ($pathParts as $part) {
    if ($part === 'app' || $part === 'public' || $part === 'src') {
        break;
    }
    if ($part !== '') {
        $baseUrlParts[] = $part;
    }
}

// Construction du BASE_URL
if (empty($baseUrlParts)) {
    define('BASE_URL', ''); // Projet à la racine
} else {
    define('BASE_URL', '/' . implode('/', $baseUrlParts));
}

// Nom du site
define('SITE_NAME', 'MonVolEnLigne');

// Helpers pour générer les URLs facilement
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset($path) {
    return BASE_URL . '/public/' . ltrim($path, '/');
}
