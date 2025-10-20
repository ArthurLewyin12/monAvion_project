<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions/validation.php';
require_once __DIR__ . '/Auth.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../app/auth/connexion.php');
    exit;
}

// Récupérer les données
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validation simple
if (empty($email) || empty($password)) {
    $_SESSION['login_errors'] = ['Veuillez remplir tous les champs.'];
    $_SESSION['login_email'] = $email;
    header('Location: ../../app/auth/connexion.php');
    exit;
}

// Tentative de connexion
$result = login($pdo, $email, $password);

if ($result['success']) {
    $user = $result['user'];

    // Créer la session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_prenom'] = $user['prenom'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_type'] = $user['type_utilisateur'];
    $_SESSION['logged_in'] = true;

    // Gérer "Se souvenir de moi" avec un cookie sécurisé
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);

        $hashed_token = password_hash($token, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utilisateurs SET remember_token = :token WHERE id = :id");
        $stmt->execute([':token' => $hashed_token, ':id' => $user['id']]);
    }

    // Rediriger selon le type d'utilisateur
    switch ($user['type_utilisateur']) {
        case 'ADMIN':
            header('Location: ../../app/admin/home.php');
            break;
        case 'AGENCE':
            header('Location: ../../app/agency/home.php');
            break;
        case 'COMPAGNIE':
            header('Location: ../../app/airline/home.php');
            break;
        case 'CLIENT':
            header('Location: ../../app/client/home.php');
            break;
        default:
            header('Location: ../../app/landing/index.php');
    }
    exit;
} else {
    $_SESSION['login_errors'] = [$result['error']];
    $_SESSION['login_email'] = $email;
    header('Location: ../../app/auth/connexion.php');
    exit;
}
