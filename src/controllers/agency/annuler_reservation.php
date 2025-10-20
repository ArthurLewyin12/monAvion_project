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
    header("Location: /app/agency/mes-reservations.php");
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

// Récupérer l'ID de la réservation
$reservation_id = $_POST['reservation_id'] ?? null;

if (!$reservation_id || !is_numeric($reservation_id)) {
    $_SESSION['error_message'] = "Réservation invalide.";
    header("Location: /app/agency/mes-reservations.php");
    exit();
}

try {
    $pdo->beginTransaction();

    // Vérifier que la réservation appartient bien à cette agence
    $stmt = $pdo->prepare("
        SELECT r.*, v.date_depart
        FROM reservations r
        JOIN vols v ON r.vol_id = v.id
        WHERE r.id = :reservation_id AND r.agence_id = :agence_id
    ");
    $stmt->execute([
        'reservation_id' => $reservation_id,
        'agence_id' => $agence_id
    ]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        throw new Exception("Réservation introuvable ou accès refusé.");
    }

    // Vérifier que la réservation peut être annulée
    if ($reservation['statut'] === 'ANNULEE') {
        throw new Exception("Cette réservation est déjà annulée.");
    }

    if ($reservation['statut'] === 'TERMINEE') {
        throw new Exception("Impossible d'annuler une réservation terminée.");
    }

    // Vérifier que le vol n'est pas dans le passé
    $date_depart = new DateTime($reservation['date_depart']);
    $now = new DateTime();

    if ($date_depart < $now) {
        throw new Exception("Impossible d'annuler une réservation pour un vol déjà parti.");
    }

    // Mettre à jour le statut de la réservation
    $stmt = $pdo->prepare("
        UPDATE reservations
        SET statut = 'ANNULEE',
            date_annulation = NOW()
        WHERE id = :reservation_id
    ");
    $stmt->execute(['reservation_id' => $reservation_id]);

    $pdo->commit();

    $_SESSION['success_message'] = "Réservation annulée avec succès.";
    header("Location: /app/agency/mes-reservations.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = "Erreur lors de l'annulation : " . $e->getMessage();
    header("Location: /app/agency/mes-reservations.php");
    exit();
}
