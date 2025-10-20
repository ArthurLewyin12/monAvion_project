<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../functions/compagnie_data.php';

// Vérifier que l'utilisateur est connecté en tant que compagnie
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'COMPAGNIE') {
    header('Location: /app/auth/connexion.php');
    exit;
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /app/compagnie/ma-flotte.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$compagnie_id = $_SESSION['compagnie_id'] ?? get_compagnie_id_from_user($pdo, $user_id);

if (!$compagnie_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de la compagnie.";
    header('Location: /app/compagnie/ma-flotte.php');
    exit;
}

$errors = [];

try {
    $avion_id = intval($_POST['avion_id'] ?? 0);

    // Validation
    if (!$avion_id) {
        $errors[] = 'Avion invalide.';
    }

    // Vérifier que l'avion appartient bien à cette compagnie
    if (empty($errors)) {
        $avion = get_avion_details($pdo, $avion_id, $compagnie_id);

        if (!$avion) {
            $errors[] = 'Avion non trouvé ou vous n\'avez pas les droits pour le supprimer.';
        }
    }

    // Vérifier si l'avion est utilisé dans des vols futurs ou actifs
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM vols
            WHERE avion_id = :avion_id
            AND (
                (date_depart > NOW() AND statut IN ('PROGRAMME', 'RETARDE'))
                OR statut = 'EN_VOL'
            )
            AND date_suppression IS NULL
        ");
        $stmt->execute([':avion_id' => $avion_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            $errors[] = "Impossible de supprimer cet avion. Il est utilisé dans {$result['count']} vol(s) programmé(s) ou en cours.";
        }
    }

    if (empty($errors)) {
        $pdo->beginTransaction();

        try {
            // Soft delete de l'avion
            $stmt = $pdo->prepare("
                UPDATE avions
                SET date_suppression = NOW(),
                    supprime_par = :supprime_par
                WHERE id = :avion_id
                AND compagnie_id = :compagnie_id
                AND date_suppression IS NULL
            ");

            $stmt->execute([
                ':supprime_par' => $user_id,
                ':avion_id' => $avion_id,
                ':compagnie_id' => $compagnie_id
            ]);

            if ($stmt->rowCount() > 0) {
                $pdo->commit();
                $_SESSION['success_message'] = "L'avion {$avion['modele']} a été supprimé avec succès.";
            } else {
                $pdo->rollBack();
                $errors[] = 'Impossible de supprimer cet avion.';
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur suppression avion: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors de la suppression de l\'avion.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur suppression avion: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
}

// Redirection avec messages
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/compagnie/ma-flotte.php');
exit;
