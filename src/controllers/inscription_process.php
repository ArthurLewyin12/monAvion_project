<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions/validation.php';
require_once __DIR__ . '/../functions/email_templates.php';
require_once __DIR__ . '/Auth.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../app/auth/inscription.php');
    exit;
}

// Récupérer les données du formulaire
$data = [
    'prenom' => $_POST['prenom'] ?? '',
    'nom' => $_POST['nom'] ?? '',
    'email' => $_POST['email'] ?? '',
    'telephone' => $_POST['telephone'] ?? '',
    'password' => $_POST['password'] ?? '',
    'password_confirm' => $_POST['password_confirm'] ?? ''
];

$newsletter = isset($_POST['newsletter']) ? true : false;
$terms = isset($_POST['terms']);

// Validation
$errors = validate_inscription_data($pdo, $data);

if (!$terms) {
    $errors['terms'] = 'Vous devez accepter les conditions générales d\'utilisation.';
}

// Si des erreurs, rediriger avec les erreurs
if (!empty($errors)) {
    $_SESSION['signup_errors'] = $errors;
    $_SESSION['signup_data'] = [
        'prenom' => $data['prenom'],
        'nom' => $data['nom'],
        'email' => $data['email'],
        'telephone' => $data['telephone'],
        'newsletter' => $newsletter
    ];
    header('Location: ../../app/auth/inscription.php');
    exit;
}

// Créer l'utilisateur
$result = create_user(
    $pdo,
    nettoyer($data['prenom']),
    nettoyer($data['nom']),
    nettoyer($data['email']),
    nettoyer($data['telephone']),
    $data['password'],
    'CLIENT',
    $newsletter
);

if ($result['success']) {
    // Créer la session
    $_SESSION['user_id'] = $result['user_id'];
    $_SESSION['user_prenom'] = nettoyer($data['prenom']);
    $_SESSION['user_nom'] = nettoyer($data['nom']);
    $_SESSION['user_email'] = nettoyer($data['email']);
    $_SESSION['user_type'] = 'CLIENT';
    $_SESSION['logged_in'] = true;

    // Envoyer l'email de bienvenue
    $prenom = nettoyer($data['prenom']);
    $email = nettoyer($data['email']);
    $dashboard_url = 'http://localhost/app/client/home.php'; // TODO: Utiliser la vraie URL du serveur

    // Envoyer l'email (ne pas bloquer l'inscription si l'email échoue)
    send_welcome_email($email, $prenom, $dashboard_url);

    $_SESSION['success_message'] = "Votre compte a été créé avec succès ! Bienvenue sur MonVolEnLigne.";

    // Rediriger vers le dashboard client
    header('Location: ../../app/client/home.php');
    exit;
} else {
    $_SESSION['signup_errors'] = $result['errors'];
    $_SESSION['signup_data'] = [
        'prenom' => $data['prenom'],
        'nom' => $data['nom'],
        'email' => $data['email'],
        'telephone' => $data['telephone'],
        'newsletter' => $newsletter
    ];
    header('Location: ../../app/auth/inscription.php');
    exit;
}
