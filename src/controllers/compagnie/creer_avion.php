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
    $modele = nettoyer($_POST['modele'] ?? '');
    $description = nettoyer($_POST['description'] ?? '');

    // Sièges par classe
    $sieges_economique = intval($_POST['sieges_economique'] ?? 0);
    $sieges_affaire = intval($_POST['sieges_affaire'] ?? 0);
    $sieges_premiere = intval($_POST['sieges_premiere'] ?? 0);

    // Validation
    if (empty($modele)) {
        $errors[] = 'Le modèle de l\'avion est requis.';
    }

    if ($sieges_economique < 0 || $sieges_affaire < 0 || $sieges_premiere < 0) {
        $errors[] = 'Le nombre de sièges ne peut pas être négatif.';
    }

    $total_sieges = $sieges_economique + $sieges_affaire + $sieges_premiere;

    if ($total_sieges === 0) {
        $errors[] = 'L\'avion doit avoir au moins un siège.';
    }

    if ($total_sieges > 1000) {
        $errors[] = 'Le nombre total de sièges ne peut pas dépasser 1000.';
    }

    if (empty($errors)) {
        $pdo->beginTransaction();

        try {
            // Construire le JSON des sièges par classe
            $sieges_par_classe = [];

            if ($sieges_economique > 0) {
                $sieges_par_classe['ECONOMIQUE'] = $sieges_economique;
            }

            if ($sieges_affaire > 0) {
                $sieges_par_classe['AFFAIRE'] = $sieges_affaire;
            }

            if ($sieges_premiere > 0) {
                $sieges_par_classe['PREMIERE'] = $sieges_premiere;
            }

            $sieges_json = json_encode($sieges_par_classe);

            // Insérer l'avion
            $stmt = $pdo->prepare("
                INSERT INTO avions (
                    modele,
                    compagnie_id,
                    nombre_sieges_total,
                    sieges_par_classe,
                    description,
                    cree_par
                ) VALUES (
                    :modele,
                    :compagnie_id,
                    :nombre_sieges_total,
                    :sieges_par_classe,
                    :description,
                    :cree_par
                )
            ");

            $stmt->execute([
                ':modele' => $modele,
                ':compagnie_id' => $compagnie_id,
                ':nombre_sieges_total' => $total_sieges,
                ':sieges_par_classe' => $sieges_json,
                ':description' => $description,
                ':cree_par' => $user_id
            ]);

            $avion_id = $pdo->lastInsertId();

            // Créer les sièges dans la table `sieges`
            $lettres_colonnes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K']; // Pas de 'I' pour éviter confusion
            $stmt_siege = $pdo->prepare("
                INSERT INTO sieges (
                    avion_id,
                    numero_siege,
                    type_classe
                ) VALUES (
                    :avion_id,
                    :numero_siege,
                    :type_classe
                )
            ");

            $numero_rangee = 1;

            // Créer sièges PREMIERE
            if ($sieges_premiere > 0) {
                $sieges_par_rangee = 4; // Généralement 4 sièges en première classe (2-2)
                $rangees_premiere = ceil($sieges_premiere / $sieges_par_rangee);

                for ($r = 0; $r < $rangees_premiere; $r++) {
                    for ($c = 0; $c < $sieges_par_rangee; $c++) {
                        if (($r * $sieges_par_rangee + $c) >= $sieges_premiere) break;

                        $numero_siege = $numero_rangee . $lettres_colonnes[$c];
                        $stmt_siege->execute([
                            ':avion_id' => $avion_id,
                            ':numero_siege' => $numero_siege,
                            ':type_classe' => 'PREMIERE'
                        ]);
                    }
                    $numero_rangee++;
                }
            }

            // Créer sièges AFFAIRE
            if ($sieges_affaire > 0) {
                $sieges_par_rangee = 6; // Généralement 6 sièges en affaires (2-2-2 ou 3-3)
                $rangees_affaire = ceil($sieges_affaire / $sieges_par_rangee);

                for ($r = 0; $r < $rangees_affaire; $r++) {
                    for ($c = 0; $c < $sieges_par_rangee; $c++) {
                        if (($r * $sieges_par_rangee + $c) >= $sieges_affaire) break;

                        $numero_siege = $numero_rangee . $lettres_colonnes[$c];
                        $stmt_siege->execute([
                            ':avion_id' => $avion_id,
                            ':numero_siege' => $numero_siege,
                            ':type_classe' => 'AFFAIRE'
                        ]);
                    }
                    $numero_rangee++;
                }
            }

            // Créer sièges ECONOMIQUE
            if ($sieges_economique > 0) {
                $sieges_par_rangee = 6; // Généralement 6 sièges en économique (3-3)
                $rangees_economique = ceil($sieges_economique / $sieges_par_rangee);

                for ($r = 0; $r < $rangees_economique; $r++) {
                    for ($c = 0; $c < $sieges_par_rangee; $c++) {
                        if (($r * $sieges_par_rangee + $c) >= $sieges_economique) break;

                        $numero_siege = $numero_rangee . $lettres_colonnes[$c];
                        $stmt_siege->execute([
                            ':avion_id' => $avion_id,
                            ':numero_siege' => $numero_siege,
                            ':type_classe' => 'ECONOMIQUE'
                        ]);
                    }
                    $numero_rangee++;
                }
            }

            $pdo->commit();

            $_SESSION['success_message'] = "L'avion {$modele} a été ajouté à votre flotte avec succès. {$total_sieges} sièges créés.";
            header('Location: /app/compagnie/ma-flotte.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur création avion: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors de la création de l\'avion.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur création avion: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue. Veuillez réessayer.';
}

// Redirection avec messages d'erreur
if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/compagnie/ma-flotte.php');
exit;
