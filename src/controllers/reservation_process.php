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
    header('Location: ../../app/client/recherche-vols.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les données
$vol_id = $_POST['vol_id'] ?? null;
$siege_id = $_POST['siege_id'] ?? null;
$classe = $_POST['classe'] ?? null;
$mode_paiement = $_POST['mode_paiement'] ?? 'CARTE';

// Données passager
$prenom = nettoyer($_POST['prenom'] ?? '');
$nom = nettoyer($_POST['nom'] ?? '');
$email = nettoyer($_POST['email'] ?? '');
$telephone = nettoyer($_POST['telephone'] ?? '');
$passeport = nettoyer($_POST['passeport'] ?? '');
$nationalite = nettoyer($_POST['nationalite'] ?? '');

// Validation
$errors = [];

if (empty($vol_id)) {
    $errors[] = 'Vol non spécifié.';
}

if (empty($siege_id)) {
    $errors[] = 'Siège non sélectionné.';
}

if (empty($classe)) {
    $errors[] = 'Classe non spécifiée.';
}

if (empty($prenom)) {
    $errors[] = 'Le prénom est requis.';
}

if (empty($nom)) {
    $errors[] = 'Le nom est requis.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email invalide.';
}

if (empty($telephone)) {
    $errors[] = 'Le téléphone est requis.';
}

if (!empty($errors)) {
    $_SESSION['reservation_errors'] = $errors;
    header("Location: ../../app/client/reservation.php?vol_id=$vol_id&classe=" . strtolower($classe));
    exit;
}

try {
    // Démarrer une transaction
    $pdo->beginTransaction();

    // 1. Vérifier que le vol existe et est disponible
    $stmt = $pdo->prepare("
        SELECT v.*, t.prix
        FROM vols v
        JOIN tarifs t ON v.id = t.vol_id
        WHERE v.id = :vol_id
        AND t.type_classe = :classe
        AND v.statut = 'PROGRAMME'
        AND v.date_suppression IS NULL
    ");
    $stmt->execute([
        ':vol_id' => $vol_id,
        ':classe' => $classe
    ]);
    $vol = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vol) {
        throw new Exception('Vol non disponible.');
    }

    // 2. Vérifier que le siège est disponible
    $stmt = $pdo->prepare("
        SELECT * FROM sieges
        WHERE id = :siege_id
        AND vol_id = :vol_id
        AND type_classe = :classe
        AND statut = 'DISPONIBLE'
        FOR UPDATE
    ");
    $stmt->execute([
        ':siege_id' => $siege_id,
        ':vol_id' => $vol_id,
        ':classe' => $classe
    ]);
    $siege = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$siege) {
        throw new Exception('Ce siège n\'est plus disponible.');
    }

    // 3. Générer un numéro de réservation unique
    $numero_reservation = 'RES-' . strtoupper(uniqid());

    // 4. Créer la réservation (DIRECTE par le client)
    $stmt = $pdo->prepare("
        INSERT INTO reservations (
            numero_reservation,
            client_id,
            vol_id,
            siege_id,
            type_reservation,
            mode_paiement,
            statut_paiement,
            montant_total,
            devise,
            statut,
            cree_par
        ) VALUES (
            :numero_reservation,
            :client_id,
            :vol_id,
            :siege_id,
            'DIRECTE',
            :mode_paiement,
            'PAYE',
            :montant,
            'EUR',
            'CONFIRMEE',
            :cree_par
        )
    ");

    $stmt->execute([
        ':numero_reservation' => $numero_reservation,
        ':client_id' => $user_id,
        ':vol_id' => $vol_id,
        ':siege_id' => $siege_id,
        ':mode_paiement' => $mode_paiement,
        ':montant' => $vol['prix'],
        ':cree_par' => $user_id
    ]);

    $reservation_id = $pdo->lastInsertId();

    // 5. Créer le passager lié à la réservation
    $stmt = $pdo->prepare("
        INSERT INTO passagers (
            reservation_id,
            utilisateur_id,
            prenom,
            nom,
            email,
            telephone,
            numero_passeport,
            nationalite,
            cree_par
        ) VALUES (
            :reservation_id,
            :utilisateur_id,
            :prenom,
            :nom,
            :email,
            :telephone,
            :passeport,
            :nationalite,
            :cree_par
        )
    ");

    $stmt->execute([
        ':reservation_id' => $reservation_id,
        ':utilisateur_id' => $user_id,
        ':prenom' => $prenom,
        ':nom' => $nom,
        ':email' => $email,
        ':telephone' => $telephone,
        ':passeport' => !empty($passeport) ? $passeport : null,
        ':nationalite' => !empty($nationalite) ? $nationalite : null,
        ':cree_par' => $user_id
    ]);

    // 6. Mettre à jour le statut du siège
    $stmt = $pdo->prepare("
        UPDATE sieges
        SET statut = 'RESERVE', modifie_par = :user_id
        WHERE id = :siege_id
    ");
    $stmt->execute([
        ':siege_id' => $siege_id,
        ':user_id' => $user_id
    ]);

    // 7. Créer le billet électronique
    $numero_billet = 'BILL-' . strtoupper(uniqid());

    $stmt = $pdo->prepare("
        INSERT INTO billets (
            reservation_id,
            numero_billet,
            cree_par
        ) VALUES (
            :reservation_id,
            :numero_billet,
            :cree_par
        )
    ");

    $stmt->execute([
        ':reservation_id' => $reservation_id,
        ':numero_billet' => $numero_billet,
        ':cree_par' => $user_id
    ]);

    // 8. Ajouter à l'historique
    $stmt = $pdo->prepare("
        INSERT INTO historique_statuts_reservations (
            reservation_id,
            statut,
            commentaire,
            cree_par
        ) VALUES (
            :reservation_id,
            'CONFIRMEE',
            'Réservation directe confirmée et payée',
            :cree_par
        )
    ");

    $stmt->execute([
        ':reservation_id' => $reservation_id,
        ':cree_par' => $user_id
    ]);

    // Tout s'est bien passé, valider la transaction
    $pdo->commit();

    // Message de succès
    $_SESSION['success_message'] = "Votre réservation a été confirmée ! Numéro de réservation : $numero_reservation";

    // Rediriger vers les détails de la réservation
    header("Location: ../../app/client/detail-reservation.php?id=$reservation_id");
    exit;

} catch (Exception $e) {
    // Erreur : annuler la transaction
    $pdo->rollBack();

    error_log("Erreur réservation: " . $e->getMessage());

    $_SESSION['reservation_errors'] = [$e->getMessage()];
    header("Location: ../../app/client/reservation.php?vol_id=$vol_id&classe=" . strtolower($classe));
    exit;
}
