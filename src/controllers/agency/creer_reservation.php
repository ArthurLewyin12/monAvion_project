<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../functions/agency_data.php';

// Vérifier si l'utilisateur est connecté et est une agence
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'AGENCE') {
    $_SESSION['error_message'] = "Accès refusé.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Vérifier si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Méthode non autorisée.";
    header("Location: /app/agency/recherche-vols.php");
    exit();
}

// Récupérer l'ID de l'agence
$user_id = $_SESSION['user_id'];
$agence_id = $_SESSION['agence_id'] ?? get_agency_id_from_user($pdo, $user_id);

if (!$agence_id) {
    $_SESSION['error_message'] = "Impossible de récupérer les informations de l'agence.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Récupérer et valider les données du formulaire
$vol_id = $_POST['vol_id'] ?? null;
$classe = $_POST['classe'] ?? null;
$tarif_id = $_POST['tarif_id'] ?? null;
$prenom = trim($_POST['prenom'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$date_naissance = $_POST['date_naissance'] ?? null;
$numero_passeport = trim($_POST['numero_passeport'] ?? '');
$nationalite = trim($_POST['nationalite'] ?? '');
$siege_id = $_POST['siege_id'] ?? null;
$mode_paiement = $_POST['mode_paiement'] ?? 'CARTE';

// Tableau pour stocker les erreurs
$errors = [];

// Validation des données
if (!$vol_id || !is_numeric($vol_id)) {
    $errors[] = "Vol invalide.";
}

if (!$classe || !in_array($classe, ['ECONOMIE', 'AFFAIRES', 'PREMIERE'])) {
    $errors[] = "Classe invalide.";
}

if (!$tarif_id || !is_numeric($tarif_id)) {
    $errors[] = "Tarif invalide.";
}

if (empty($prenom) || strlen($prenom) < 2) {
    $errors[] = "Le prénom doit contenir au moins 2 caractères.";
}

if (empty($nom) || strlen($nom) < 2) {
    $errors[] = "Le nom doit contenir au moins 2 caractères.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide.";
}

if (empty($date_naissance)) {
    $errors[] = "Date de naissance requise.";
} else {
    $date_obj = DateTime::createFromFormat('Y-m-d', $date_naissance);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $date_naissance) {
        $errors[] = "Format de date invalide.";
    } elseif ($date_obj >= new DateTime()) {
        $errors[] = "La date de naissance doit être dans le passé.";
    }
}

if (!in_array($mode_paiement, ['CARTE', 'AGENCE'])) {
    $errors[] = "Mode de paiement invalide.";
}

// Si des erreurs sont présentes, rediriger avec les erreurs
if (!empty($errors)) {
    $_SESSION['reservation_errors'] = $errors;
    header("Location: /app/agency/reserver.php?vol_id=$vol_id&classe=$classe");
    exit();
}

try {
    $pdo->beginTransaction();

    // Vérifier que le vol existe et est disponible
    $stmt = $pdo->prepare("
        SELECT v.*, t.prix
        FROM vols v
        JOIN tarifs t ON v.id = t.vol_id
        WHERE v.id = :vol_id AND t.id = :tarif_id AND t.classe = :classe
        AND v.statut = 'PREVU'
    ");
    $stmt->execute([
        'vol_id' => $vol_id,
        'tarif_id' => $tarif_id,
        'classe' => $classe
    ]);
    $vol = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vol) {
        throw new Exception("Vol non disponible.");
    }

    // Vérifier le nombre de places disponibles
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM reservations r
        JOIN passagers p ON r.id = p.reservation_id
        JOIN tarifs t ON r.tarif_id = t.id
        WHERE r.vol_id = :vol_id
        AND t.classe = :classe
        AND r.statut IN ('CONFIRMEE', 'EN_ATTENTE')
    ");
    $stmt->execute(['vol_id' => $vol_id, 'classe' => $classe]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Récupérer la capacité pour cette classe
    $stmt = $pdo->prepare("
        SELECT SUM(quantite) as capacite
        FROM sieges
        WHERE vol_id = :vol_id AND classe = :classe
    ");
    $stmt->execute(['vol_id' => $vol_id, 'classe' => $classe]);
    $capacite = $stmt->fetch(PDO::FETCH_ASSOC)['capacite'] ?? 0;

    if ($count >= $capacite) {
        throw new Exception("Plus de places disponibles pour cette classe.");
    }

    // Si un siège est sélectionné, vérifier qu'il est disponible
    if ($siege_id) {
        $stmt = $pdo->prepare("
            SELECT s.id
            FROM sieges s
            WHERE s.id = :siege_id
            AND s.vol_id = :vol_id
            AND s.classe = :classe
            AND s.id NOT IN (
                SELECT siege_id
                FROM passagers
                WHERE siege_id IS NOT NULL
                AND reservation_id IN (
                    SELECT id FROM reservations
                    WHERE vol_id = :vol_id
                    AND statut IN ('CONFIRMEE', 'EN_ATTENTE')
                )
            )
        ");
        $stmt->execute([
            'siege_id' => $siege_id,
            'vol_id' => $vol_id,
            'classe' => $classe
        ]);

        if (!$stmt->fetch()) {
            $siege_id = null; // Siège non disponible, on l'ignore
        }
    }

    // Créer la réservation
    $numero_reservation = 'RES' . strtoupper(uniqid());

    $stmt = $pdo->prepare("
        INSERT INTO reservations (
            numero_reservation,
            vol_id,
            tarif_id,
            agence_id,
            statut,
            mode_paiement,
            date_reservation
        ) VALUES (
            :numero_reservation,
            :vol_id,
            :tarif_id,
            :agence_id,
            'CONFIRMEE',
            :mode_paiement,
            NOW()
        )
    ");

    $stmt->execute([
        'numero_reservation' => $numero_reservation,
        'vol_id' => $vol_id,
        'tarif_id' => $tarif_id,
        'agence_id' => $agence_id,
        'mode_paiement' => $mode_paiement
    ]);

    $reservation_id = $pdo->lastInsertId();

    // Créer le passager
    $stmt = $pdo->prepare("
        INSERT INTO passagers (
            reservation_id,
            prenom,
            nom,
            email,
            telephone,
            date_naissance,
            numero_passeport,
            nationalite,
            siege_id
        ) VALUES (
            :reservation_id,
            :prenom,
            :nom,
            :email,
            :telephone,
            :date_naissance,
            :numero_passeport,
            :nationalite,
            :siege_id
        )
    ");

    $stmt->execute([
        'reservation_id' => $reservation_id,
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone ?: null,
        'date_naissance' => $date_naissance,
        'numero_passeport' => $numero_passeport ?: null,
        'nationalite' => $nationalite ?: null,
        'siege_id' => $siege_id
    ]);

    $pdo->commit();

    $_SESSION['success_message'] = "Réservation créée avec succès ! Numéro : $numero_reservation";
    header("Location: /app/agency/detail-reservation.php?id=$reservation_id");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = "Erreur lors de la création de la réservation : " . $e->getMessage();
    header("Location: /app/agency/reserver.php?vol_id=$vol_id&classe=$classe");
    exit();
}
