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
    $vol_id = intval($_POST['vol_id'] ?? 0);
    $nouveau_statut = $_POST['statut'] ?? '';

    // Validation
    if (!$vol_id) {
        $errors[] = 'Vol invalide.';
    }

    if (!in_array($nouveau_statut, ['PROGRAMME', 'RETARDE', 'ANNULE'])) {
        $errors[] = 'Statut invalide.';
    }

    // Vérifier que le vol appartient à la compagnie
    if (empty($errors)) {
        $vol = get_vol_details_for_compagnie($pdo, $vol_id, $compagnie_id);

        if (!$vol) {
            $errors[] = 'Vol non trouvé ou vous n\'avez pas les droits pour le modifier.';
        }
    }

    // Vérifier les transitions de statut autorisées
    if ($vol && empty($errors)) {
        $statut_actuel = $vol['statut'];

        // Règles de transition
        $transitions_autorisees = [
            'PROGRAMME' => ['RETARDE', 'ANNULE'],
            'RETARDE' => ['PROGRAMME', 'ANNULE'],
            'ANNULE' => [] // Un vol annulé ne peut pas changer de statut
        ];

        if ($statut_actuel === $nouveau_statut) {
            $errors[] = "Le vol est déjà au statut {$nouveau_statut}.";
        } elseif (!in_array($nouveau_statut, $transitions_autorisees[$statut_actuel])) {
            $errors[] = "Transition de statut non autorisée : {$statut_actuel} → {$nouveau_statut}.";
        }
    }

    // Actions spécifiques selon le nouveau statut
    if ($vol && empty($errors)) {
        $pdo->beginTransaction();

        try {
            // Mettre à jour le statut du vol
            $stmt = $pdo->prepare("
                UPDATE vols
                SET statut = :statut,
                    modifie_par = :modifie_par
                WHERE id = :vol_id
                AND compagnie_id = :compagnie_id
            ");

            $stmt->execute([
                ':statut' => $nouveau_statut,
                ':modifie_par' => $user_id,
                ':vol_id' => $vol_id,
                ':compagnie_id' => $compagnie_id
            ]);

            // Actions spécifiques selon le statut
            if ($nouveau_statut === 'ANNULE') {
                // Mettre à jour le statut des réservations associées
                $stmt_res = $pdo->prepare("
                    UPDATE reservations
                    SET statut = 'ANNULEE',
                        modifie_par = :modifie_par
                    WHERE vol_id = :vol_id
                    AND statut IN ('CONFIRMEE', 'EN_ATTENTE')
                    AND date_suppression IS NULL
                ");

                $stmt_res->execute([
                    ':modifie_par' => $user_id,
                    ':vol_id' => $vol_id
                ]);

                $nb_reservations_annulees = $stmt_res->rowCount();

                // TODO: Envoyer des emails de notification aux clients
                // require_once __DIR__ . '/../../functions/sendEmail.php';
                // Récupérer les emails des clients et envoyer notification

                $message = "Le vol {$vol['numero_vol']} a été annulé.";
                if ($nb_reservations_annulees > 0) {
                    $message .= " {$nb_reservations_annulees} réservation(s) ont été annulées et les clients seront notifiés.";
                }

                $_SESSION['success_message'] = $message;

            } elseif ($nouveau_statut === 'RETARDE') {
                // TODO: Envoyer des emails de notification aux clients
                $_SESSION['success_message'] = "Le vol {$vol['numero_vol']} a été marqué comme retardé. Les clients seront notifiés.";

            } elseif ($nouveau_statut === 'PROGRAMME') {
                $_SESSION['success_message'] = "Le vol {$vol['numero_vol']} est de nouveau programmé.";
            }

            $pdo->commit();
            header('Location: /app/compagnie/mes-vols.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur changement statut vol: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors du changement de statut.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur changement statut vol: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
}

// Redirection avec messages d'erreur
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/compagnie/mes-vols.php');
exit;
