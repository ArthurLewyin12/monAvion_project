<?php
/**
 * Fonction pour générer un mot de passe temporaire sécurisé
 */

/**
 * Génère un mot de passe temporaire aléatoire sécurisé
 *
 * @param int $length Longueur du mot de passe (défaut: 12)
 * @return string Mot de passe temporaire généré
 */
function generate_temp_password($length = 12) {
    // Caractères utilisables (majuscules, minuscules, chiffres, symboles sûrs)
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $symbols = '!@#$%&*';

    $all_chars = $lowercase . $uppercase . $numbers . $symbols;

    // S'assurer qu'on a au moins un caractère de chaque type
    $password = '';
    $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
    $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
    $password .= $numbers[random_int(0, strlen($numbers) - 1)];
    $password .= $symbols[random_int(0, strlen($symbols) - 1)];

    // Compléter avec des caractères aléatoires
    for ($i = 4; $i < $length; $i++) {
        $password .= $all_chars[random_int(0, strlen($all_chars) - 1)];
    }

    // Mélanger les caractères
    $password = str_shuffle($password);

    return $password;
}

/**
 * Valide qu'un mot de passe respecte les critères de sécurité
 *
 * @param string $password Mot de passe à valider
 * @return array ['valid' => bool, 'errors' => array]
 */
function validate_password_strength($password) {
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une minuscule.";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}
