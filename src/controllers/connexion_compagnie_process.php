<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /app/auth/connexion-compagnie.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$mot_de_passe = $_POST['mot_de_passe'] ?? '';

if (empty($email) || empty($mot_de_passe)) {
    $_SESSION['error_message'] = "Veuillez remplir tous les champs.";
    header("Location: /app/auth/connexion-compagnie.php");
    exit();
}

try {
    // Vérifier que l'utilisateur existe et est de type COMPAGNIE
    $stmt = $pdo->prepare("
        SELECT u.*, c.id as compagnie_id, c.nom_compagnie
        FROM utilisateurs u
        JOIN compagnies_aeriennes c ON u.id = c.utilisateur_id
        WHERE u.email = :email
        AND u.type_utilisateur = 'COMPAGNIE'
        AND u.statut_actuel = 'ACTIF'
        AND u.date_suppression IS NULL
        AND c.date_suppression IS NULL
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['error_message'] = "Email ou mot de passe incorrect.";
        header("Location: /app/auth/connexion-compagnie.php");
        exit();
    }

    // Connexion réussie
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = 'COMPAGNIE';
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_prenom'] = $user['prenom'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['compagnie_id'] = $user['compagnie_id'];
    $_SESSION['compagnie_nom'] = $user['nom_compagnie'];

    // Vérifier si première connexion
    if ($user['premiere_connexion'] == 1) {
        $_SESSION['must_change_password'] = true;
        header("Location: /app/auth/premiere-connexion.php");
        exit();
    }

    header("Location: /app/compagnie/dashboard.php");
    exit();

} catch (PDOException $e) {
    error_log("Erreur connexion compagnie: " . $e->getMessage());
    $_SESSION['error_message'] = "Une erreur est survenue. Veuillez réessayer.";
    header("Location: /app/auth/connexion-compagnie.php");
    exit();
}
