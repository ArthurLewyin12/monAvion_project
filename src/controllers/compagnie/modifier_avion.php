<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../functions/validation.php';
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
    // Récupération des données
    $avion_id = intval($_POST['avion_id'] ?? 0);
    $modele = nettoyer($_POST['modele'] ?? '');
    $description = nettoyer($_POST['description'] ?? '');

    // Validation
    if (!$avion_id) {
        $errors[] = 'Avion invalide.';
    }

    if (empty($modele)) {
        $errors[] = 'Le modèle de l\'avion est requis.';
    }

    // Vérifier que l'avion appartient bien à cette compagnie
    if (empty($errors)) {
        $avion = get_avion_details($pdo, $avion_id, $compagnie_id);

        if (!$avion) {
            $errors[] = 'Avion non trouvé ou vous n\'avez pas les droits pour le modifier.';
        }
    }

    // Vérifier si l'avion est utilisé dans des vols futurs
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM vols
            WHERE avion_id = :avion_id
            AND date_depart > NOW()
            AND statut = 'PROGRAMME'
            AND date_suppression IS NULL
        ");
        $stmt->execute([':avion_id' => $avion_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            $_SESSION['error_message'] = "Cet avion est utilisé dans {$result['count']} vol(s) futur(s). Seule la description peut être modifiée.";
            // Permettre uniquement la modification de la description
            $only_description = true;
        } else {
            $only_description = false;
        }
    }

    if (empty($errors)) {
        $pdo->beginTransaction();

        try {
            if ($only_description) {
                // Mise à jour uniquement de la description
                $stmt = $pdo->prepare("
                    UPDATE avions
                    SET description = :description,
                        modifie_par = :modifie_par
                    WHERE id = :avion_id
                    AND compagnie_id = :compagnie_id
                ");

                $stmt->execute([
                    ':description' => $description,
                    ':modifie_par' => $user_id,
                    ':avion_id' => $avion_id,
                    ':compagnie_id' => $compagnie_id
                ]);

                $_SESSION['success_message'] = "La description de l'avion a été mise à jour. Le modèle ne peut être modifié car cet avion est utilisé dans des vols programmés.";

            } else {
                // Mise à jour complète (modèle + description)
                // Note: On ne modifie PAS le nombre de sièges car cela affecterait les vols existants
                $stmt = $pdo->prepare("
                    UPDATE avions
                    SET modele = :modele,
                        description = :description,
                        modifie_par = :modifie_par
                    WHERE id = :avion_id
                    AND compagnie_id = :compagnie_id
                ");

                $stmt->execute([
                    ':modele' => $modele,
                    ':description' => $description,
                    ':modifie_par' => $user_id,
                    ':avion_id' => $avion_id,
                    ':compagnie_id' => $compagnie_id
                ]);

                $_SESSION['success_message'] = "L'avion a été modifié avec succès.";
            }

            $pdo->commit();
            header('Location: /app/compagnie/ma-flotte.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur modification avion: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors de la modification de l\'avion.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur modification avion: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
}

// Redirection avec messages d'erreur
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/compagnie/ma-flotte.php');
exit;
