<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions/validation.php';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../app/forms/demande-agence.php');
    exit;
}

// Récupérer les données
$nom_agence = nettoyer($_POST['nom_agence'] ?? '');
$numero_licence = nettoyer($_POST['numero_licence'] ?? '');
$pays = nettoyer($_POST['pays'] ?? '');
$adresse = nettoyer($_POST['adresse'] ?? '');
$nom_contact = nettoyer($_POST['nom_contact'] ?? '');
$email = nettoyer($_POST['email'] ?? '');
$telephone = nettoyer($_POST['telephone'] ?? '');
$nombre_employes = nettoyer($_POST['nombre_employes'] ?? '');
$message = nettoyer($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($nom_agence)) {
    $errors['nom_agence'] = 'Le nom de l\'agence est requis.';
}

if (empty($numero_licence)) {
    $errors['numero_licence'] = 'Le numéro de licence est requis.';
}

if (empty($pays)) {
    $errors['pays'] = 'Le pays est requis.';
}

if (empty($adresse)) {
    $errors['adresse'] = 'L\'adresse est requise.';
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
    $_SESSION['agency_request_errors'] = $errors;
    $_SESSION['agency_request_data'] = $_POST;
    header('Location: ../../app/forms/demande-agence.php');
    exit;
}

try {
    // Insérer dans la table demandes_agences
    $stmt = $pdo->prepare("
        INSERT INTO demandes_agences (
            nom_agence, numero_licence, pays, adresse, nom_contact,
            email, telephone, nombre_employes, message, statut
        )
        VALUES (
            :nom_agence, :numero_licence, :pays, :adresse, :nom_contact,
            :email, :telephone, :nombre_employes, :message, 'EN_ATTENTE'
        )
    ");

    $stmt->execute([
        ':nom_agence' => $nom_agence,
        ':numero_licence' => $numero_licence,
        ':pays' => $pays,
        ':adresse' => $adresse,
        ':nom_contact' => $nom_contact,
        ':email' => $email,
        ':telephone' => $telephone,
        ':nombre_employes' => !empty($nombre_employes) ? (int)$nombre_employes : null,
        ':message' => $message
    ]);

    $_SESSION['agency_request_success'] = "Votre demande de partenariat a bien été enregistrée. Vous recevrez un code de validation pour terminer votre inscription.";
    header('Location: ../../app/landing/index.php');
    exit;

} catch (PDOException $e) {
    error_log("Erreur demande agence: " . $e->getMessage());
    $_SESSION['agency_request_errors'] = ['db' => 'Une erreur est survenue. Veuillez réessayer.'];
    $_SESSION['agency_request_data'] = $_POST;
    header('Location: ../../app/forms/demande-agence.php');
    exit;
}
