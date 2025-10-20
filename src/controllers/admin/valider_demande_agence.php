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
    header('Location: /app/admin/demandes-agences.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$demande_id = intval($_POST['demande_id'] ?? 0);
$action = $_POST['action'] ?? ''; // 'approuver' ou 'rejeter'
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
            SELECT * FROM demandes_agences
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
                // Créer l'agence
                $stmt = $pdo->prepare("
                    INSERT INTO agences (
                        utilisateur_id,
                        nom_agence,
                        numero_licence,
                        adresse,
                        ville,
                        code_postal,
                        pays,
                        telephone_agence,
                        statut_verification,
                        cree_par
                    ) VALUES (
                        :utilisateur_id,
                        :nom_agence,
                        :numero_licence,
                        :adresse,
                        :ville,
                        :code_postal,
                        :pays,
                        :telephone,
                        'ACTIVE',
                        :cree_par
                    )
                ");

                $stmt->execute([
                    ':utilisateur_id' => $demande['utilisateur_id'],
                    ':nom_agence' => $demande['nom_agence'],
                    ':numero_licence' => $demande['numero_licence'],
                    ':adresse' => $demande['adresse'],
                    ':ville' => $demande['ville'],
                    ':code_postal' => $demande['code_postal'],
                    ':pays' => $demande['pays'],
                    ':telephone' => $demande['telephone'],
                    ':cree_par' => $user_id
                ]);

                // Mettre à jour le type d'utilisateur
                $stmt = $pdo->prepare("
                    UPDATE utilisateurs
                    SET type_utilisateur = 'AGENCY',
                        modifie_par = :modifie_par
                    WHERE id = :utilisateur_id
                ");
                $stmt->execute([
                    ':modifie_par' => $user_id,
                    ':utilisateur_id' => $demande['utilisateur_id']
                ]);

                // Mettre à jour la demande
                $stmt = $pdo->prepare("
                    UPDATE demandes_agences
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
                        "Votre demande d'agence a été approuvée",
                        "Félicitations {$user['prenom']},<br><br>Votre demande pour créer l'agence <strong>{$demande['nom_agence']}</strong> a été approuvée.<br><br>Vous pouvez maintenant vous connecter avec vos identifiants.",
                        "{$user['prenom']} {$user['nom']}"
                    );
                }

                $_SESSION['success_message'] = "La demande de {$demande['nom_agence']} a été approuvée avec succès.";

            } else {
                // Rejeter la demande
                $stmt = $pdo->prepare("
                    UPDATE demandes_agences
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
                        "Votre demande d'agence a été rejetée",
                        "Bonjour {$user['prenom']},<br><br>Nous sommes désolés de vous informer que votre demande pour créer l'agence <strong>{$demande['nom_agence']}</strong> a été rejetée.<br><br><strong>Motif :</strong> {$motif_rejet}",
                        "{$user['prenom']} {$user['nom']}"
                    );
                }

                $_SESSION['success_message'] = "La demande a été rejetée.";
            }

            $pdo->commit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erreur validation demande agence: " . $e->getMessage());
            $errors[] = 'Une erreur est survenue lors du traitement.';
        }
    }

} catch (Exception $e) {
    error_log("Erreur validation demande agence: " . $e->getMessage());
    $errors[] = 'Une erreur est survenue.';
}

if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
}

header('Location: /app/admin/demandes-agences.php');
exit;
