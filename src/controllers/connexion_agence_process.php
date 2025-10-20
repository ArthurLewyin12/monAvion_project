<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /app/auth/connexion-agence.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

if (empty($email) || empty($mot_de_passe)) {
    $_SESSION['error_message'] = "Veuillez remplir tous les champs.";
    header("Location: /app/auth/connexion-agence.php");
    exit();
}

try {
    // Vérifier que l'utilisateur existe et est de type AGENCE
    $stmt = $pdo->prepare("
        SELECT u.*, a.id as agence_id, a.nom_agence
        FROM utilisateurs u
        JOIN agences a ON u.id = a.utilisateur_id
        WHERE u.email = :email
        AND u.type_utilisateur = 'AGENCE'
        AND u.statut_actuel = 'ACTIF'
        AND u.date_suppression IS NULL
        AND a.date_suppression IS NULL
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['error_message'] = "Email ou mot de passe incorrect.";
        header("Location: /app/auth/connexion-agence.php");
        exit();
    }

    // Connexion réussie
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = 'AGENCE';
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_prenom'] = $user['prenom'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['agence_id'] = $user['agence_id'];
    $_SESSION['agency_nom'] = $user['nom_agence'];

    // Vérifier si première connexion
    if ($user['premiere_connexion'] == 1) {
        $_SESSION['must_change_password'] = true;
        header("Location: /app/auth/premiere-connexion.php");
        exit();
    }

    header("Location: /app/agency/dashboard.php");
    exit();

} catch (PDOException $e) {
    error_log("Erreur connexion agence: " . $e->getMessage());
    $_SESSION['error_message'] = "Une erreur est survenue. Veuillez réessayer.";
    header("Location: /app/auth/connexion-agence.php");
    exit();
}
