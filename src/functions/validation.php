<?php
/**
 * Fonctions de validation et nettoyage des données
 */

/**
 * Nettoie une chaîne de caractères en enlevant les espaces et les caractères HTML/PHP
 * @param string $data La donnée à nettoyer
 * @return string La donnée nettoyée
 */
function nettoyer($data) {
    if (!is_string($data)) {
        return $data;
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Valide les données d'inscription d'un utilisateur
 * @param PDO $pdo Instance PDO pour vérifier l'email unique
 * @param array $data Les données à valider
 * @return array Les erreurs de validation
 */
function validate_inscription_data($pdo, $data) {
    $errors = [];

    // Validation du prénom
    if (empty($data['prenom'])) {
        $errors['prenom'] = 'Le prénom est requis.';
    } elseif (strlen($data['prenom']) < 2) {
        $errors['prenom'] = 'Le prénom doit contenir au moins 2 caractères.';
    }

    // Validation du nom
    if (empty($data['nom'])) {
        $errors['nom'] = 'Le nom est requis.';
    } elseif (strlen($data['nom']) < 2) {
        $errors['nom'] = 'Le nom doit contenir au moins 2 caractères.';
    }

    // Validation de l'email
    if (empty($data['email'])) {
        $errors['email'] = 'L\'adresse email est requise.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Le format de l\'email est invalide.';
    } else {
        // Vérifier si l'email existe déjà
        try {
            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
            $stmt->execute([':email' => $data['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'Cette adresse email est déjà utilisée.';
            }
        } catch (PDOException $e) {
            $errors['email'] = 'Erreur lors de la vérification de l\'email.';
        }
    }

    // Validation du mot de passe
    if (empty($data['password'])) {
        $errors['password'] = 'Le mot de passe est requis.';
    } elseif (strlen($data['password']) < 8) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }

    // Validation de la confirmation du mot de passe
    if (empty($data['password_confirm'])) {
        $errors['password_confirm'] = 'La confirmation du mot de passe est requise.';
    } elseif ($data['password'] !== $data['password_confirm']) {
        $errors['password_confirm'] = 'Les mots de passe ne correspondent pas.';
    }

    // Validation du téléphone (optionnel, mais si fourni doit être valide)
    if (!empty($data['telephone'])) {
        // Format de base pour un numéro français ou international
        $phone_pattern = '/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/';
        if (!preg_match($phone_pattern, $data['telephone'])) {
            $errors['telephone'] = 'Le format du numéro de téléphone est invalide.';
        }
    }

    return $errors;
}

/**
 * Valide un email
 * @param string $email L'email à valider
 * @return bool True si valide, false sinon
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un numéro de téléphone
 * @param string $phone Le numéro à valider
 * @return bool True si valide, false sinon
 */
function validate_phone($phone) {
    $phone_pattern = '/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/';
    return preg_match($phone_pattern, $phone) === 1;
}

/**
 * Valide la force d'un mot de passe
 * @param string $password Le mot de passe à valider
 * @return array ['valid' => bool, 'message' => string, 'strength' => int]
 */
function validate_password_strength($password) {
    $result = [
        'valid' => false,
        'message' => '',
        'strength' => 0
    ];

    $length = strlen($password);

    if ($length < 8) {
        $result['message'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        return $result;
    }

    $strength = 0;

    // Longueur
    if ($length >= 8) $strength += 1;
    if ($length >= 12) $strength += 1;

    // Contient des minuscules
    if (preg_match('/[a-z]/', $password)) $strength += 1;

    // Contient des majuscules
    if (preg_match('/[A-Z]/', $password)) $strength += 1;

    // Contient des chiffres
    if (preg_match('/[0-9]/', $password)) $strength += 1;

    // Contient des caractères spéciaux
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $strength += 1;

    $result['strength'] = $strength;
    $result['valid'] = $strength >= 3;

    if ($strength < 3) {
        $result['message'] = 'Le mot de passe doit contenir au moins 3 des critères suivants : minuscules, majuscules, chiffres, caractères spéciaux.';
    } else {
        $result['message'] = 'Mot de passe accepté.';
    }

    return $result;
}
