<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';

// Vérifier que l'utilisateur est connecté en tant qu'admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'ADMIN') {
    header('Location: /app/auth/connexion.php');
    exit;
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /app/admin/messages-contact.php');
    exit;
}

$admin_id = $_SESSION['user_id'];
$message_id = intval($_POST['message_id'] ?? 0);
$nouveau_statut = $_POST['statut'] ?? '';

$errors = [];

try {
    if (!$message_id) {
        $errors[] = 'Message invalide.';
    }

    if (!in_array($nouveau_statut, ['EN_COURS', 'RESOLU'])) {
        $errors[] = 'Statut invalide.';
    }

    // Vérifier que le message existe
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT * FROM messages_contact
            WHERE id = :message_id
            AND date_suppression IS NULL
        ");
        $stmt->execute(['message_id' => $message_id]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$message) {
            $errors[] = 'Message introuvable.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE messages_contact
            SET statut = :statut,
                date_traitement = NOW(),
                traite_par = :traite_par,
                modifie_par = :modifie_par
            WHERE id = :message_id
        ");

        $stmt->execute([
            ':statut' => $nouveau_statut,
            ':traite_par' => $admin_id,
            ':modifie_par' => $admin_id,
            ':message_id' => $message_id
        ]);

        $statut_label = $nouveau_statut === 'EN_COURS' ? 'en cours de traitement' : 'résolu';
        $_SESSION['success_message'] = "Le message a été marqué comme {$statut_label}.";
    }

} catch (PDOException $e) {
    error_log("Erreur traitement message: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue.';
}

if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/admin/messages-contact.php');
exit;
