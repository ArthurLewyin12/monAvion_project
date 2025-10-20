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
    // Récupération des données du vol
    $numero_vol = strtoupper(nettoyer($_POST['numero_vol'] ?? ''));
    $aeroport_depart = strtoupper(nettoyer($_POST['aeroport_depart'] ?? ''));
    $aeroport_arrivee = strtoupper(nettoyer($_POST['aeroport_arrivee'] ?? ''));
    $date_depart = $_POST['date_depart'] ?? '';
    $date_arrivee = $_POST['date_arrivee'] ?? '';
    $avion_id = intval($_POST['avion_id'] ?? 0);

    // Récupération des tarifs
    $prix_economique = isset($_POST['prix_economique']) && $_POST['prix_economique'] !== '' ? floatval($_POST['prix_economique']) : null;
    $prix_affaire = isset($_POST['prix_affaire']) && $_POST['prix_affaire'] !== '' ? floatval($_POST['prix_affaire']) : null;
    $prix_premiere = isset($_POST['prix_premiere']) && $_POST['prix_premiere'] !== '' ? floatval($_POST['prix_premiere']) : null;

    // Validation du vol
    if (empty($numero_vol)) {
        $errors[] = 'Le numéro de vol est requis.';
    } elseif (strlen($numero_vol) < 2 || strlen($numero_vol) > 20) {
        $errors[] = 'Le numéro de vol doit contenir entre 2 et 20 caractères.';
    }

    if (empty($aeroport_depart)) {
        $errors[] = 'L\'aéroport de départ est requis.';
    } elseif (strlen($aeroport_depart) !== 3) {
        $errors[] = 'Le code IATA de départ doit contenir 3 lettres (ex: CDG).';
    }

    if (empty($aeroport_arrivee)) {
        $errors[] = 'L\'aéroport d\'arrivée est requis.';
    } elseif (strlen($aeroport_arrivee) !== 3) {
        $errors[] = 'Le code IATA d\'arrivée doit contenir 3 lettres (ex: JFK).';
    }

    if ($aeroport_depart === $aeroport_arrivee) {
        $errors[] = 'Les aéroports de départ et d\'arrivée doivent être différents.';
    }

    if (empty($date_depart)) {
        $errors[] = 'La date de départ est requise.';
    }

    if (empty($date_arrivee)) {
        $errors[] = 'La date d\'arrivée est requise.';
    }

    // Vérifier que les dates sont cohérentes
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

        // Vérifier la durée maximale du vol (24h)
        if (($arrivee_timestamp - $depart_timestamp) > (24 * 3600)) {
            $errors[] = 'La durée du vol ne peut pas dépasser 24 heures.';
        }
    }

    if (!$avion_id) {
        $errors[] = 'Vous devez sélectionner un avion.';
    }

    // Vérifier que l'avion appartient à la compagnie
    if ($avion_id && empty($errors)) {
        $avion = get_avion_details($pdo, $avion_id, $compagnie_id);

        if (!$avion) {
            $errors[] = 'Avion non trouvé ou vous n\'avez pas les droits pour l\'utiliser.';
        }
    }

    // Vérifier que le numéro de vol n'existe pas déjà
    if (!empty($numero_vol) && empty($errors)) {
        if (check_numero_vol_exists($pdo, $numero_vol, $compagnie_id)) {
            $errors[] = "Le numéro de vol {$numero_vol} existe déjà pour votre compagnie.";
        }
    }

    // Validation des tarifs - au moins un prix doit être défini
    if ($prix_economique === null && $prix_affaire === null && $prix_premiere === null) {
        $errors[] = 'Vous devez définir au moins un tarif (économique, affaires ou première classe).';
    }

    // Vérifier que les prix sont positifs
    if ($prix_economique !== null && $prix_economique <= 0) {
        $errors[] = 'Le prix économique doit être positif.';
    }

    if ($prix_affaire !== null && $prix_affaire <= 0) {
        $errors[] = 'Le prix affaires doit être positif.';
    }

    if ($prix_premiere !== null && $prix_premiere <= 0) {
        $errors[] = 'Le prix première classe doit être positif.';
    }

    // Récupérer les sièges disponibles par classe de l'avion
    if ($avion && empty($errors)) {
        $sieges_par_classe = json_decode($avion['sieges_par_classe'], true);

        // Vérifier que les tarifs correspondent aux classes disponibles dans l'avion
        if ($prix_economique !== null && !isset($sieges_par_classe['ECONOMIQUE'])) {
            $errors[] = 'L\'avion sélectionné ne dispose pas de sièges en classe économique.';
        }

        if ($prix_affaire !== null && !isset($sieges_par_classe['AFFAIRE'])) {
            $errors[] = 'L\'avion sélectionné ne dispose pas de sièges en classe affaires.';
        }

        if ($prix_premiere !== null && !isset($sieges_par_classe['PREMIERE'])) {
            $errors[] = 'L\'avion sélectionné ne dispose pas de sièges en première classe.';
        }
    }

    if (empty($errors)) {
        $pdo->beginTransaction();

        try {
            // Créer le vol
            $stmt = $pdo->prepare("
                INSERT INTO vols (
                    numero_vol,
                    aeroport_depart,
                    aeroport_arrivee,
                    date_depart,
                    date_arrivee,
                    compagnie_id,
                    avion_id,
                    statut,
                    cree_par
                ) VALUES (
                    :numero_vol,
                    :aeroport_depart,
                    :aeroport_arrivee,
                    :date_depart,
                    :date_arrivee,
                    :compagnie_id,
                    :avion_id,
                    'PROGRAMME',
                    :cree_par
                )
            ");

            $stmt->execute([
                ':numero_vol' => $numero_vol,
                ':aeroport_depart' => $aeroport_depart,
                ':aeroport_arrivee' => $aeroport_arrivee,
                ':date_depart' => $date_depart,
                ':date_arrivee' => $date_arrivee,
                ':compagnie_id' => $compagnie_id,
                ':avion_id' => $avion_id,
                ':cree_par' => $user_id
            ]);

            $vol_id = $pdo->lastInsertId();

            // Créer les tarifs
            $stmt_tarif = $pdo->prepare("
                INSERT INTO tarifs (
                    vol_id,
                    type_classe,
                    prix,
                    devise,
                    disponibilite,
                    cree_par
                ) VALUES (
                    :vol_id,
                    :type_classe,
                    :prix,
                    'EUR',
                    :disponibilite,
                    :cree_par
                )
            ");

            // Tarif économique
            if ($prix_economique !== null) {
                $stmt_tarif->execute([
                    ':vol_id' => $vol_id,
                    ':type_classe' => 'ECONOMIQUE',
                    ':prix' => $prix_economique,
                    ':disponibilite' => $sieges_par_classe['ECONOMIQUE'] ?? 0,
                    ':cree_par' => $user_id
                ]);
            }

            // Tarif affaires
            if ($prix_affaire !== null) {
                $stmt_tarif->execute([
                    ':vol_id' => $vol_id,
                    ':type_classe' => 'AFFAIRE',
                    ':prix' => $prix_affaire,
                    ':disponibilite' => $sieges_par_classe['AFFAIRE'] ?? 0,
                    ':cree_par' => $user_id
                ]);
            }

            // Tarif première classe
            if ($prix_premiere !== null) {
                $stmt_tarif->execute([
                    ':vol_id' => $vol_id,
                    ':type_classe' => 'PREMIERE',
                    ':prix' => $prix_premiere,
                    ':disponibilite' => $sieges_par_classe['PREMIERE'] ?? 0,
                    ':cree_par' => $user_id
                ]);
            }

            $pdo->commit();

            $_SESSION['success_message'] = "Le vol {$numero_vol} a été créé avec succès.";
            header('Location: /app/compagnie/mes-vols.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur création vol: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors de la création du vol.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur création vol: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
}

// Redirection avec messages d'erreur
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/compagnie/mes-vols.php');
exit;
