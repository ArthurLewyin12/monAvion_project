<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../functions/admin_data.php';

header('Content-Type: application/json');

// Vérifier l'authentification admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'ADMIN') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Récupérer l'ID utilisateur
$user_id = intval($_GET['id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide']);
    exit();
}

try {
    $user = get_user_details($pdo, $user_id);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
        exit();
    }

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
