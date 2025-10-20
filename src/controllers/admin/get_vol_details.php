<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';

header('Content-Type: application/json');

// Vérifier l'authentification admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'ADMIN') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Récupérer l'ID du vol
$vol_id = intval($_GET['id'] ?? 0);

if ($vol_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID vol invalide']);
    exit();
}

try {
    $query = "
        SELECT
            v.*,
            c.nom_compagnie AS compagnie_nom,
            a.nom AS avion_nom,
            a.immatriculation AS avion_immatriculation,
            COUNT(DISTINCT r.id_reservation) AS total_reservations
        FROM vols v
        INNER JOIN compagnies c ON v.compagnie_id = c.id_compagnie
        INNER JOIN avions a ON v.avion_id = a.id_avion
        LEFT JOIN reservations r ON v.id_vol = r.vol_id AND r.date_suppression IS NULL
        WHERE v.id_vol = :vol_id AND v.date_suppression IS NULL
        GROUP BY v.id_vol
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':vol_id' => $vol_id]);
    $vol = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vol) {
        echo json_encode(['success' => false, 'message' => 'Vol non trouvé']);
        exit();
    }

    echo json_encode([
        'success' => true,
        'vol' => $vol
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
