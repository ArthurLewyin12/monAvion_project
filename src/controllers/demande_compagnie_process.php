<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions/validation.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../app/forms/demande-compagnie.php');
    exit;
}

// Récupérer les données
$nom_compagnie = nettoyer($_POST['nom_compagnie'] ?? '');
$code_iata = nettoyer($_POST['code_iata'] ?? '');
$pays = nettoyer($_POST['pays'] ?? '');
$nom_contact = nettoyer($_POST['nom_contact'] ?? '');
$email = nettoyer($_POST['email'] ?? '');
$telephone = nettoyer($_POST['telephone'] ?? '');
$taille_flotte = nettoyer($_POST['taille_flotte'] ?? '');
$message = nettoyer($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($nom_compagnie)) {
    $errors['nom_compagnie'] = 'Le nom de la compagnie est requis.';
}

if (empty($code_iata)) {
    $errors['code_iata'] = 'Le code IATA est requis.';
} elseif (strlen($code_iata) < 2 || strlen($code_iata) > 3) {
    $errors['code_iata'] = 'Le code IATA doit contenir 2 ou 3 caractères.';
}

if (empty($pays)) {
    $errors['pays'] = 'Le pays est requis.';
}

if (empty($nom_contact)) {
    $errors['nom_contact'] = 'Le nom de contact est requis.';
}

if (empty($email)) {
    $errors['email'] = 'L\'email est requis.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Le format de l\'email est invalide.';
}

if (empty($telephone)) {
    $errors['telephone'] = 'Le téléphone est requis.';
}

if (empty($message)) {
    $errors['message'] = 'Le message est requis.';
}

// Si des erreurs, rediriger
if (!empty($errors)) {
    $_SESSION['airline_request_errors'] = $errors;
    $_SESSION['airline_request_data'] = $_POST;
    header('Location: ../../app/forms/demande-compagnie.php');
    exit;
}

try {
    // Insérer dans la table demandes_compagnies
    $stmt = $pdo->prepare("
        INSERT INTO demandes_compagnies (
            nom_compagnie, code_iata, pays, nom_contact,
            email, telephone, taille_flotte, message, statut
        )
        VALUES (
            :nom_compagnie, :code_iata, :pays, :nom_contact,
            :email, :telephone, :taille_flotte, :message, 'EN_ATTENTE'
        )
    ");

    $stmt->execute([
        ':nom_compagnie' => $nom_compagnie,
        ':code_iata' => strtoupper($code_iata),
        ':pays' => $pays,
        ':nom_contact' => $nom_contact,
        ':email' => $email,
        ':telephone' => $telephone,
        ':taille_flotte' => !empty($taille_flotte) ? (int)$taille_flotte : null,
        ':message' => $message
    ]);

    $_SESSION['airline_request_success'] = "Votre demande de partenariat a bien été enregistrée. Vous recevrez un code de validation pour terminer votre inscription.";
    header('Location: ../../app/landing/index.php');
    exit;

} catch (PDOException $e) {
    error_log("Erreur demande compagnie: " . $e->getMessage());
    $_SESSION['airline_request_errors'] = ['db' => 'Une erreur est survenue. Veuillez réessayer.'];
    $_SESSION['airline_request_data'] = $_POST;
    header('Location: ../../app/forms/demande-compagnie.php');
    exit;
}
