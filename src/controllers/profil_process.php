<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions/validation.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'CLIENT') {
    header('Location: ../../app/auth/connexion.php');
    exit;
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../app/client/profil.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

$errors = [];

try {
    switch ($action) {
        case 'update_info':
            // Mise à jour des informations personnelles
            $prenom = nettoyer($_POST['prenom'] ?? '');
            $nom = nettoyer($_POST['nom'] ?? '');
            $email = nettoyer($_POST['email'] ?? '');
            $telephone = nettoyer($_POST['telephone'] ?? '');

            // Validation
            if (empty($prenom)) {
                $errors[] = 'Le prénom est requis.';
            }

            if (empty($nom)) {
                $errors[] = 'Le nom est requis.';
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email invalide.';
            }

            // Vérifier si l'email est déjà utilisé par un autre utilisateur
            if (empty($errors)) {
                $stmt = $pdo->prepare("
                    SELECT id FROM utilisateurs
                    WHERE email = :email AND id != :user_id
                ");
                $stmt->execute([
                    ':email' => $email,
                    ':user_id' => $user_id
                ]);

                if ($stmt->fetch()) {
                    $errors[] = 'Cet email est déjà utilisé.';
                }
            }

            if (empty($errors)) {
                $stmt = $pdo->prepare("
                    UPDATE utilisateurs
                    SET prenom = :prenom,
                        nom = :nom,
                        email = :email,
                        telephone = :telephone,
                        modifie_par = :modifie_par
                    WHERE id = :user_id
                ");

                $stmt->execute([
                    ':prenom' => $prenom,
                    ':nom' => $nom,
                    ':email' => $email,
                    ':telephone' => $telephone,
                    ':modifie_par' => $user_id,
                    ':user_id' => $user_id
                ]);

                // Mettre à jour la session
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_telephone'] = $telephone;

                $_SESSION['profil_success'] = 'Vos informations ont été mises à jour avec succès.';
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

                $_SESSION['profil_success'] = 'Votre mot de passe a été modifié avec succès.';
            }
            break;

        case 'update_preferences':
            // Mise à jour des préférences
            $newsletter = isset($_POST['newsletter']) ? 1 : 0;

            $stmt = $pdo->prepare("
                UPDATE utilisateurs
                SET abonnement_newsletter = :newsletter,
                    modifie_par = :modifie_par
                WHERE id = :user_id
            ");

            $stmt->execute([
                ':newsletter' => $newsletter,
                ':modifie_par' => $user_id,
                ':user_id' => $user_id
            ]);

            $_SESSION['profil_success'] = 'Vos préférences ont été mises à jour avec succès.';
            break;

        default:
            $errors[] = 'Action invalide.';
            break;
    }

} catch (PDOException $e) {
    error_log("Erreur modification profil: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
}

// Redirection avec messages
if (!empty($errors)) {
    $_SESSION['profil_errors'] = $errors;
}

header('Location: ../../app/client/profil.php');
exit;
