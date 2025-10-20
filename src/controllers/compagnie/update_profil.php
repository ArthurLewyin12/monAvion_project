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
    header('Location: /app/compagnie/profil.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$compagnie_id = $_SESSION['compagnie_id'] ?? get_compagnie_id_from_user($pdo, $user_id);

if (!$compagnie_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de la compagnie.";
    header('Location: /app/compagnie/profil.php');
    exit;
}

$action = $_POST['action'] ?? '';
$errors = [];

try {
    switch ($action) {
        case 'update_info':
            // Mise à jour des informations de la compagnie
            $nom_compagnie = nettoyer($_POST['nom_compagnie'] ?? '');
            $pays = nettoyer($_POST['pays'] ?? '');
            $description = nettoyer($_POST['description'] ?? '');

            // Validation
            if (empty($nom_compagnie)) {
                $errors[] = 'Le nom de la compagnie est requis.';
            }

            if (empty($pays)) {
                $errors[] = 'Le pays est requis.';
            }

            if (empty($errors)) {
                $pdo->beginTransaction();

                try {
                    // Mettre à jour les informations de la compagnie
                    $stmt = $pdo->prepare("
                        UPDATE compagnies_aeriennes
                        SET nom_compagnie = :nom_compagnie,
                            pays = :pays,
                            description = :description,
                            modifie_par = :modifie_par
                        WHERE id = :compagnie_id
                    ");

                    $stmt->execute([
                        ':nom_compagnie' => $nom_compagnie,
                        ':pays' => $pays,
                        ':description' => $description,
                        ':modifie_par' => $user_id,
                        ':compagnie_id' => $compagnie_id
                    ]);

                    $pdo->commit();

                    // Mettre à jour la session
                    $_SESSION['compagnie_nom'] = $nom_compagnie;

                    $_SESSION['success_message'] = 'Les informations de la compagnie ont été mises à jour avec succès.';

                } catch (PDOException $e) {
                    $pdo->rollBack();
                    error_log("Erreur update compagnie info: " . $e->getMessage());
                    $errors[] = 'Une erreur est survenue lors de la mise à jour.';
                }
            }
            break;

        case 'update_password':
            // Modification du mot de passe
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($current_password)) {
                $errors[] = 'Le mot de passe actuel est requis.';
            }

            if (empty($new_password)) {
                $errors[] = 'Le nouveau mot de passe est requis.';
            } elseif (strlen($new_password) < 8) {
                $errors[] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
            }

            if ($new_password !== $confirm_password) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }

            // Vérifier le mot de passe actuel
            if (empty($errors)) {
                $stmt = $pdo->prepare("
                    SELECT mot_de_passe FROM utilisateurs
                    WHERE id = :user_id
                ");
                $stmt->execute([':user_id' => $user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user || !password_verify($current_password, $user['mot_de_passe'])) {
                    $errors[] = 'Le mot de passe actuel est incorrect.';
                }
            }

            // Mettre à jour le mot de passe
            if (empty($errors)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    UPDATE utilisateurs
                    SET mot_de_passe = :password,
                        modifie_par = :modifie_par
                    WHERE id = :user_id
                ");

                $stmt->execute([
                    ':password' => $hashed_password,
                    ':modifie_par' => $user_id,
                    ':user_id' => $user_id
                ]);

                $_SESSION['success_message'] = 'Votre mot de passe a été modifié avec succès.';
            }
            break;

        default:
            $errors[] = 'Action invalide.';
            break;
    }

} catch (PDOException $e) {
    error_log("Erreur modification profil compagnie: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
}

// Redirection avec messages
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/compagnie/profil.php');
exit;
