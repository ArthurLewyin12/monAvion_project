<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../functions/sendEmail.php';

// Vérifier que l'utilisateur est connecté en tant qu'admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'ADMIN') {
    header('Location: /app/auth/connexion.php');
    exit;
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /app/admin/demandes-compagnies.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$demande_id = intval($_POST['demande_id'] ?? 0);
$action = $_POST['action'] ?? '';
$motif_rejet = $_POST['motif_rejet'] ?? null;

$errors = [];

try {
    if (!$demande_id) {
        $errors[] = 'Demande invalide.';
    }

    if (!in_array($action, ['approuver', 'rejeter'])) {
        $errors[] = 'Action invalide.';
    }

    if ($action === 'rejeter' && empty($motif_rejet)) {
        $errors[] = 'Le motif de rejet est requis.';
    }

    // Récupérer la demande
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT * FROM demandes_compagnies
            WHERE id = :demande_id
            AND date_suppression IS NULL
        ");
        $stmt->execute(['demande_id' => $demande_id]);
        $demande = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$demande) {
            $errors[] = 'Demande introuvable.';
        } elseif ($demande['statut'] !== 'EN_ATTENTE') {
            $errors[] = 'Cette demande a déjà été traitée.';
        }
    }

    if (empty($errors)) {
        $pdo->beginTransaction();

        try {
            if ($action === 'approuver') {
                // Créer la compagnie
                $stmt = $pdo->prepare("
                    INSERT INTO compagnies_aeriennes (
                        utilisateur_id,
                        nom_compagnie,
                        code_iata,
                        pays,
                        numero_licence_exploitation,
                        description,
                        statut_verification,
                        cree_par
                    ) VALUES (
                        :utilisateur_id,
                        :nom_compagnie,
                        :code_iata,
                        :pays,
                        :numero_licence,
                        :description,
                        'ACTIVE',
                        :cree_par
                    )
                ");

                $stmt->execute([
                    ':utilisateur_id' => $demande['utilisateur_id'],
                    ':nom_compagnie' => $demande['nom_compagnie'],
                    ':code_iata' => $demande['code_iata'],
                    ':pays' => $demande['pays'],
                    ':numero_licence' => $demande['numero_licence_exploitation'],
                    ':description' => $demande['description'],
                    ':cree_par' => $user_id
                ]);

                // Mettre à jour le type d'utilisateur
                $stmt = $pdo->prepare("
                    UPDATE utilisateurs
                    SET type_utilisateur = 'COMPAGNIE',
                        modifie_par = :modifie_par
                    WHERE id = :utilisateur_id
                ");
                $stmt->execute([
                    ':modifie_par' => $user_id,
                    ':utilisateur_id' => $demande['utilisateur_id']
                ]);

                // Mettre à jour la demande
                $stmt = $pdo->prepare("
                    UPDATE demandes_compagnies
                    SET statut = 'APPROUVEE',
                        date_traitement = NOW(),
                        traite_par = :traite_par,
                        modifie_par = :modifie_par
                    WHERE id = :demande_id
                ");
                $stmt->execute([
                    ':traite_par' => $user_id,
                    ':modifie_par' => $user_id,
                    ':demande_id' => $demande_id
                ]);

                // Envoyer email de confirmation
                $stmt = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = :user_id");
                $stmt->execute(['user_id' => $demande['utilisateur_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    sendEmail(
                        $user['email'],
                        "Votre demande de compagnie a été approuvée",
                        "Félicitations {$user['prenom']},<br><br>Votre demande pour créer la compagnie aérienne <strong>{$demande['nom_compagnie']}</strong> ({$demande['code_iata']}) a été approuvée.<br><br>Vous pouvez maintenant vous connecter et commencer à gérer vos vols.",
                        "{$user['prenom']} {$user['nom']}"
                    );
                }

                $_SESSION['success_message'] = "La demande de {$demande['nom_compagnie']} a été approuvée avec succès.";

            } else {
                // Rejeter la demande
                $stmt = $pdo->prepare("
                    UPDATE demandes_compagnies
                    SET statut = 'REJETEE',
                        motif_rejet = :motif_rejet,
                        date_traitement = NOW(),
                        traite_par = :traite_par,
                        modifie_par = :modifie_par
                    WHERE id = :demande_id
                ");
                $stmt->execute([
                    ':motif_rejet' => $motif_rejet,
                    ':traite_par' => $user_id,
                    ':modifie_par' => $user_id,
                    ':demande_id' => $demande_id
                ]);

                // Envoyer email de rejet
                $stmt = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = :user_id");
                $stmt->execute(['user_id' => $demande['utilisateur_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    sendEmail(
                        $user['email'],
                        "Votre demande de compagnie a été rejetée",
                        "Bonjour {$user['prenom']},<br><br>Nous sommes désolés de vous informer que votre demande pour créer la compagnie <strong>{$demande['nom_compagnie']}</strong> a été rejetée.<br><br><strong>Motif :</strong> {$motif_rejet}",
                        "{$user['prenom']} {$user['nom']}"
                    );
                }

                $_SESSION['success_message'] = "La demande a été rejetée.";
            }

            $pdo->commit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur validation demande compagnie: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors du traitement.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur validation demande compagnie: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue.';
}

if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/admin/demandes-compagnies.php');
exit;
