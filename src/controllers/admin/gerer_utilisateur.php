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
    header('Location: /app/admin/utilisateurs.php');
    exit;
}

$admin_id = $_SESSION['user_id'];
$target_user_id = intval($_POST['user_id'] ?? 0);
$action = $_POST['action'] ?? ''; // 'suspendre', 'activer', 'supprimer'

$errors = [];

try {
    if (!$target_user_id) {
        $errors[] = 'Utilisateur invalide.';
    }

    if (!in_array($action, ['suspendre', 'activer', 'supprimer'])) {
        $errors[] = 'Action invalide.';
    }

    // Vérifier que l'utilisateur existe
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT * FROM utilisateurs
            WHERE id = :user_id
            AND date_suppression IS NULL
        ");
        $stmt->execute(['user_id' => $target_user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = 'Utilisateur introuvable.';
        }

        // Ne pas permettre de modifier un autre admin
        if ($user && $user['type_utilisateur'] === 'ADMIN' && $action !== 'activer') {
            $errors[] = 'Vous ne pouvez pas modifier un autre administrateur.';
        }

        // Ne pas se supprimer soi-même
        if ($target_user_id == $admin_id) {
            $errors[] = 'Vous ne pouvez pas effectuer cette action sur votre propre compte.';
        }
    }

    if (empty($errors)) {
        $pdo->beginTransaction();

        try {
            switch ($action) {
                case 'suspendre':
                    $stmt = $pdo->prepare("
                        UPDATE utilisateurs
                        SET statut_compte = 'SUSPENDU',
                            modifie_par = :modifie_par
                        WHERE id = :user_id
                    ");
                    $stmt->execute([
                        ':modifie_par' => $admin_id,
                        ':user_id' => $target_user_id
                    ]);

                    // Suspendre aussi l'agence ou la compagnie si applicable
                    if ($user['type_utilisateur'] === 'AGENCY') {
                        $stmt = $pdo->prepare("
                            UPDATE agences
                            SET statut_verification = 'SUSPENDUE',
                                modifie_par = :modifie_par
                            WHERE utilisateur_id = :user_id
                        ");
                        $stmt->execute([
                            ':modifie_par' => $admin_id,
                            ':user_id' => $target_user_id
                        ]);
                    } elseif ($user['type_utilisateur'] === 'COMPAGNIE') {
                        $stmt = $pdo->prepare("
                            UPDATE compagnies_aeriennes
                            SET statut_verification = 'SUSPENDUE',
                                modifie_par = :modifie_par
                            WHERE utilisateur_id = :user_id
                        ");
                        $stmt->execute([
                            ':modifie_par' => $admin_id,
                            ':user_id' => $target_user_id
                        ]);
                    }

                    $_SESSION['success_message'] = "L'utilisateur a été suspendu avec succès.";
                    break;

                case 'activer':
                    $stmt = $pdo->prepare("
                        UPDATE utilisateurs
                        SET statut_compte = 'ACTIF',
                            modifie_par = :modifie_par
                        WHERE id = :user_id
                    ");
                    $stmt->execute([
                        ':modifie_par' => $admin_id,
                        ':user_id' => $target_user_id
                    ]);

                    // Activer aussi l'agence ou la compagnie
                    if ($user['type_utilisateur'] === 'AGENCY') {
                        $stmt = $pdo->prepare("
                            UPDATE agences
                            SET statut_verification = 'ACTIVE',
                                modifie_par = :modifie_par
                            WHERE utilisateur_id = :user_id
                        ");
                        $stmt->execute([
                            ':modifie_par' => $admin_id,
                            ':user_id' => $target_user_id
                        ]);
                    } elseif ($user['type_utilisateur'] === 'COMPAGNIE') {
                        $stmt = $pdo->prepare("
                            UPDATE compagnies_aeriennes
                            SET statut_verification = 'ACTIVE',
                                modifie_par = :modifie_par
                            WHERE utilisateur_id = :user_id
                        ");
                        $stmt->execute([
                            ':modifie_par' => $admin_id,
                            ':user_id' => $target_user_id
                        ]);
                    }

                    $_SESSION['success_message'] = "L'utilisateur a été activé avec succès.";
                    break;

                case 'supprimer':
                    // Soft delete
                    $stmt = $pdo->prepare("
                        UPDATE utilisateurs
                        SET date_suppression = NOW(),
                            supprime_par = :supprime_par
                        WHERE id = :user_id
                    ");
                    $stmt->execute([
                        ':supprime_par' => $admin_id,
                        ':user_id' => $target_user_id
                    ]);

                    // Soft delete agence ou compagnie
                    if ($user['type_utilisateur'] === 'AGENCY') {
                        $stmt = $pdo->prepare("
                            UPDATE agences
                            SET date_suppression = NOW(),
                                supprime_par = :supprime_par
                            WHERE utilisateur_id = :user_id
                        ");
                        $stmt->execute([
                            ':supprime_par' => $admin_id,
                            ':user_id' => $target_user_id
                        ]);
                    } elseif ($user['type_utilisateur'] === 'COMPAGNIE') {
                        $stmt = $pdo->prepare("
                            UPDATE compagnies_aeriennes
                            SET date_suppression = NOW(),
                                supprime_par = :supprime_par
                            WHERE utilisateur_id = :user_id
                        ");
                        $stmt->execute([
                            ':supprime_par' => $admin_id,
                            ':user_id' => $target_user_id
                        ]);
                    }

                    $_SESSION['success_message'] = "L'utilisateur a été supprimé avec succès.";
                    break;
            }

            $pdo->commit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur gestion utilisateur: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur gestion utilisateur: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue.';
}

if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/admin/utilisateurs.php');
exit;
