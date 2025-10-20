<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions/generate_temp_password.php';

// Vérifier que l'utilisateur est connecté et doit changer son mot de passe
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['must_change_password'])) {
    header("Location: /app/auth/connexion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /app/auth/premiere-connexion.php");
    exit();
}

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validation
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
    header("Location: /app/auth/premiere-connexion.php");
    exit();
}

if ($new_password !== $confirm_password) {
    $_SESSION['error_message'] = "Les mots de passe ne correspondent pas.";
    header("Location: /app/auth/premiere-connexion.php");
    exit();
}

// Valider la force du mot de passe
$validation = validate_password_strength($new_password);
if (!$validation['valid']) {
    $_SESSION['error_message'] = implode(' ', $validation['errors']);
    header("Location: /app/auth/premiere-connexion.php");
    exit();
}

try {
    // Récupérer l'utilisateur
    $stmt = $pdo->prepare("
        SELECT id, mot_de_passe, premiere_connexion
        FROM utilisateurs
        WHERE id = :user_id
    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Utilisateur introuvable.");
    }

    // Vérifier le mot de passe actuel
    if (!password_verify($current_password, $user['mot_de_passe'])) {
        $_SESSION['error_message'] = "Mot de passe temporaire incorrect.";
        header("Location: /app/auth/premiere-connexion.php");
        exit();
    }

    // Vérifier que le nouveau mot de passe est différent de l'ancien
    if (password_verify($new_password, $user['mot_de_passe'])) {
        $_SESSION['error_message'] = "Le nouveau mot de passe doit être différent du mot de passe temporaire.";
        header("Location: /app/auth/premiere-connexion.php");
        exit();
    }

    // Mettre à jour le mot de passe et le flag premiere_connexion
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("
        UPDATE utilisateurs
        SET mot_de_passe = :mot_de_passe,
            premiere_connexion = FALSE,
            date_modification = NOW()
        WHERE id = :user_id
    ");
    $stmt->execute([
        'mot_de_passe' => $hashed_password,
        'user_id' => $_SESSION['user_id']
    ]);

    // Retirer le flag de changement obligatoire
    unset($_SESSION['must_change_password']);

    // Rediriger vers le bon dashboard
    $_SESSION['success_message'] = "Votre mot de passe a été changé avec succès !";

    $redirect = match($_SESSION['user_type']) {
        'CLIENT' => '/app/client/home.php',
        'AGENCE' => '/app/agency/dashboard.php',
        'COMPAGNIE' => '/app/compagnie/dashboard.php',
        'ADMIN' => '/app/admin/dashboard.php',
        default => '/app/auth/connexion.php'
    };

    header("Location: $redirect");
    exit();

} catch (Exception $e) {
    error_log("Erreur premiere_connexion_process: " . $e->getMessage());
    $_SESSION['error_message'] = "Une erreur est survenue. Veuillez réessayer.";
    header("Location: /app/auth/premiere-connexion.php");
    exit();
}
