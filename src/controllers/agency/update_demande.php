<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../functions/agency_data.php';

// Vérifier si l'utilisateur est connecté et est une agence
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'AGENCE') {
    $_SESSION['error_message'] = "Accès refusé.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Vérifier si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Méthode non autorisée.";
    header("Location: /app/agency/demandes-clients.php");
    exit();
}

// Récupérer l'ID de l'agence
$user_id = $_SESSION['user_id'];
$agence_id = $_SESSION['agence_id'] ?? get_agency_id_from_user($pdo, $user_id);

if (!$agence_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de l'agence.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Récupérer les données du formulaire
$demande_id = $_POST['demande_id'] ?? null;
$nouveau_statut = $_POST['statut'] ?? null;

if (!$demande_id || !is_numeric($demande_id)) {
    $_SESSION['error_message'] = "Demande invalide.";
    header("Location: /app/agency/demandes-clients.php");
    exit();
}

// Valider le statut
$statuts_valides = ['VUE', 'TRAITEE', 'FERMEE'];
if (!$nouveau_statut || !in_array($nouveau_statut, $statuts_valides)) {
    $_SESSION['error_message'] = "Statut invalide.";
    header("Location: /app/agency/detail-demande.php?id=$demande_id");
    exit();
}

try {
    // Vérifier que la demande appartient bien à cette agence
    $stmt = $pdo->prepare("
        SELECT statut
        FROM demandes_vols
        WHERE id = :demande_id AND agence_id = :agence_id
    ");
    $stmt->execute([
        'demande_id' => $demande_id,
        'agence_id' => $agence_id
    ]);
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$demande) {
        throw new Exception("Demande introuvable ou accès refusé.");
    }

    // Vérifier la logique de transition de statut
    $statut_actuel = $demande['statut'];

    // Une demande fermée ne peut plus être modifiée
    if ($statut_actuel === 'FERMEE') {
        throw new Exception("Impossible de modifier une demande fermée.");
    }

    // Validation des transitions de statut
    $transitions_valides = [
        'NOUVELLE' => ['VUE', 'TRAITEE', 'FERMEE'],
        'VUE' => ['TRAITEE', 'FERMEE'],
        'TRAITEE' => ['FERMEE']
    ];

    if (!isset($transitions_valides[$statut_actuel]) ||
        !in_array($nouveau_statut, $transitions_valides[$statut_actuel])) {
        throw new Exception("Transition de statut non autorisée.");
    }

    // Mettre à jour le statut
    $stmt = $pdo->prepare("
        UPDATE demandes_vols
        SET statut = :statut,
            date_mise_a_jour = NOW()
        WHERE id = :demande_id
    ");
    $stmt->execute([
        'statut' => $nouveau_statut,
        'demande_id' => $demande_id
    ]);

    $messages = [
        'VUE' => "La demande a été marquée comme vue.",
        'TRAITEE' => "La demande a été marquée comme traitée.",
        'FERMEE' => "La demande a été fermée."
    ];

    $_SESSION['success_message'] = $messages[$nouveau_statut] ?? "Statut mis à jour avec succès.";
    header("Location: /app/agency/detail-demande.php?id=$demande_id");
    exit();

} catch (Exception $e) {
    $_SESSION['error_message'] = "Erreur lors de la mise à jour : " . $e->getMessage();
    header("Location: /app/agency/detail-demande.php?id=$demande_id");
    exit();
}
