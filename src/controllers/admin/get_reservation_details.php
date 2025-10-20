<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';

header('Content-Type: application/json');

// Vérifier l'authentification admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'ADMIN') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Récupérer l'ID de la réservation
$reservation_id = intval($_GET['id'] ?? 0);

if ($reservation_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID réservation invalide']);
    exit();
}

try {
    // Récupérer la réservation
    $query = "
        SELECT
            r.*,
            v.numero_vol,
            v.aeroport_depart,
            v.aeroport_arrivee,
            v.date_depart,
            v.heure_depart,
            u.prenom AS client_prenom,
            u.nom AS client_nom,
            u.email AS client_email,
            u.telephone AS client_telephone,
            t.type_classe
        FROM reservations r
        INNER JOIN vols v ON r.vol_id = v.id_vol
        INNER JOIN utilisateurs u ON r.client_id = u.id_utilisateur
        INNER JOIN tarifs t ON r.tarif_id = t.id_tarif
        WHERE r.id_reservation = :reservation_id AND r.date_suppression IS NULL
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':reservation_id' => $reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        echo json_encode(['success' => false, 'message' => 'Réservation non trouvée']);
        exit();
    }

    // Récupérer les passagers
    $query_passagers = "
        SELECT p.*, s.numero_siege
        FROM passagers p
        LEFT JOIN sieges s ON p.siege_id = s.id_siege
        WHERE p.reservation_id = :reservation_id
        ORDER BY p.id_passager
    ";

    $stmt_passagers = $pdo->prepare($query_passagers);
    $stmt_passagers->execute([':reservation_id' => $reservation_id]);
    $passagers = $stmt_passagers->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'reservation' => $reservation,
        'passagers' => $passagers
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
