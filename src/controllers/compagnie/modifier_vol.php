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
    header('Location: /app/compagnie/mes-vols.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$compagnie_id = $_SESSION['compagnie_id'] ?? get_compagnie_id_from_user($pdo, $user_id);

if (!$compagnie_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de la compagnie.";
    header('Location: /app/compagnie/mes-vols.php');
    exit;
}

$errors = [];

try {
    // Récupération des données
    $vol_id = intval($_POST['vol_id'] ?? 0);
    $date_depart = $_POST['date_depart'] ?? '';
    $date_arrivee = $_POST['date_arrivee'] ?? '';

    // Tarifs (optionnels pour la modification)
    $prix_economique = isset($_POST['prix_economique']) && $_POST['prix_economique'] !== '' ? floatval($_POST['prix_economique']) : null;
    $prix_affaire = isset($_POST['prix_affaire']) && $_POST['prix_affaire'] !== '' ? floatval($_POST['prix_affaire']) : null;
    $prix_premiere = isset($_POST['prix_premiere']) && $_POST['prix_premiere'] !== '' ? floatval($_POST['prix_premiere']) : null;

    // Validation
    if (!$vol_id) {
        $errors[] = 'Vol invalide.';
    }

    // Vérifier que le vol appartient à la compagnie
    if (empty($errors)) {
        $vol = get_vol_details_for_compagnie($pdo, $vol_id, $compagnie_id);

        if (!$vol) {
            $errors[] = 'Vol non trouvé ou vous n\'avez pas les droits pour le modifier.';
        }
    }

    // Vérifier le statut du vol
    if ($vol && $vol['statut'] === 'ANNULE') {
        $errors[] = 'Impossible de modifier un vol annulé.';
    }

    // Vérifier si le vol a déjà des réservations
    $has_reservations = false;
    if ($vol && empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM reservations
            WHERE vol_id = :vol_id
            AND statut IN ('CONFIRMEE', 'EN_ATTENTE')
            AND date_suppression IS NULL
        ");
        $stmt->execute([':vol_id' => $vol_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $has_reservations = $result['count'] > 0;
    }

    // Validation des dates
    if (empty($date_depart)) {
        $errors[] = 'La date de départ est requise.';
    }

    if (empty($date_arrivee)) {
        $errors[] = 'La date d\'arrivée est requise.';
    }

    if (!empty($date_depart) && !empty($date_arrivee)) {
        $depart_timestamp = strtotime($date_depart);
        $arrivee_timestamp = strtotime($date_arrivee);
        $now = time();

        if ($depart_timestamp < $now) {
            $errors[] = 'La date de départ doit être dans le futur.';
        }

        if ($arrivee_timestamp <= $depart_timestamp) {
            $errors[] = 'La date d\'arrivée doit être postérieure à la date de départ.';
        }

        if (($arrivee_timestamp - $depart_timestamp) > (24 * 3600)) {
            $errors[] = 'La durée du vol ne peut pas dépasser 24 heures.';
        }

        // Si le vol a des réservations, limiter les modifications de dates
        if ($has_reservations) {
            $original_depart = strtotime($vol['date_depart']);
            $diff_hours = abs($depart_timestamp - $original_depart) / 3600;

            // Autoriser max 6h de changement si réservations existantes
            if ($diff_hours > 6) {
                $errors[] = 'Ce vol a des réservations. Vous ne pouvez modifier la date de départ que de 6 heures maximum.';
            }
        }
    }

    // Validation des tarifs si fournis
    if ($prix_economique !== null && $prix_economique <= 0) {
        $errors[] = 'Le prix économique doit être positif.';
    }

    if ($prix_affaire !== null && $prix_affaire <= 0) {
        $errors[] = 'Le prix affaires doit être positif.';
    }

    if ($prix_premiere !== null && $prix_premiere <= 0) {
        $errors[] = 'Le prix première classe doit être positif.';
    }

    if (empty($errors)) {
        $pdo->beginTransaction();

        try {
            // Mettre à jour le vol
            $stmt = $pdo->prepare("
                UPDATE vols
                SET date_depart = :date_depart,
                    date_arrivee = :date_arrivee,
                    modifie_par = :modifie_par
                WHERE id = :vol_id
                AND compagnie_id = :compagnie_id
            ");

            $stmt->execute([
                ':date_depart' => $date_depart,
                ':date_arrivee' => $date_arrivee,
                ':modifie_par' => $user_id,
                ':vol_id' => $vol_id,
                ':compagnie_id' => $compagnie_id
            ]);

            // Mettre à jour les tarifs si fournis
            if ($prix_economique !== null || $prix_affaire !== null || $prix_premiere !== null) {
                $stmt_update_tarif = $pdo->prepare("
                    UPDATE tarifs
                    SET prix = :prix,
                        modifie_par = :modifie_par
                    WHERE vol_id = :vol_id
                    AND type_classe = :type_classe
                ");

                if ($prix_economique !== null) {
                    $stmt_update_tarif->execute([
                        ':prix' => $prix_economique,
                        ':modifie_par' => $user_id,
                        ':vol_id' => $vol_id,
                        ':type_classe' => 'ECONOMIQUE'
                    ]);
                }

                if ($prix_affaire !== null) {
                    $stmt_update_tarif->execute([
                        ':prix' => $prix_affaire,
                        ':modifie_par' => $user_id,
                        ':vol_id' => $vol_id,
                        ':type_classe' => 'AFFAIRE'
                    ]);
                }

                if ($prix_premiere !== null) {
                    $stmt_update_tarif->execute([
                        ':prix' => $prix_premiere,
                        ':modifie_par' => $user_id,
                        ':vol_id' => $vol_id,
                        ':type_classe' => 'PREMIERE'
                    ]);
                }
            }

            $pdo->commit();

            $message = "Le vol {$vol['numero_vol']} a été modifié avec succès.";
            if ($has_reservations) {
                $message .= " Les clients ayant réservé seront notifiés du changement.";
            }

            $_SESSION['success_message'] = $message;
            header('Location: /app/compagnie/mes-vols.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur modification vol: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors de la modification du vol.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur modification vol: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
}

// Redirection avec messages d'erreur
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/compagnie/mes-vols.php');
exit;
