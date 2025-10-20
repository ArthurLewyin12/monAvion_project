<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions/validation.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/contact.php');
    exit;
}

// Récupérer les données
$nom = nettoyer($_POST['name'] ?? '');
$email = nettoyer($_POST['email'] ?? '');
$telephone = nettoyer($_POST['phone'] ?? '');
$sujet = nettoyer($_POST['subject'] ?? '');
$message = nettoyer($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($nom)) {
    $errors['name'] = 'Le nom est requis.';
}

if (empty($email)) {
    $errors['email'] = 'L\'email est requis.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Le format de l\'email est invalide.';
}

if (empty($sujet)) {
    $errors['subject'] = 'Le sujet est requis.';
}

if (empty($message)) {
    $errors['message'] = 'Le message est requis.';
}

// Si des erreurs, rediriger
if (!empty($errors)) {
    $_SESSION['contact_errors'] = $errors;
    $_SESSION['contact_data'] = [
        'name' => $nom,
        'email' => $email,
        'phone' => $telephone,
        'subject' => $sujet,
        'message' => $message
    ];
    header('Location: ../../public/contact.php');
    exit;
}

try {
    // Insérer dans la table messages_contact
    $stmt = $pdo->prepare("
        INSERT INTO messages_contact (nom, email, telephone, sujet, message, statut)
        VALUES (:nom, :email, :telephone, :sujet, :message, 'NOUVEAU')
    ");

    $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':telephone' => !empty($telephone) ? $telephone : null,
        ':sujet' => $sujet,
        ':message' => $message
    ]);

    $_SESSION['contact_success'] = "Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.";
    header('Location: ../../public/contact.php');
    exit;

} catch (PDOException $e) {
    error_log("Erreur contact: " . $e->getMessage());
    $_SESSION['contact_errors'] = ['db' => 'Une erreur est survenue. Veuillez réessayer.'];
    $_SESSION['contact_data'] = [
        'name' => $nom,
        'email' => $email,
        'phone' => $telephone,
        'subject' => $sujet,
        'message' => $message
    ];
    header('Location: ../../public/contact.php');
    exit;
}
