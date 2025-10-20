<?php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../functions/client_data.php';
require_once __DIR__ . '/../../functions/sendEmail.php';

// Vérifier si l'utilisateur est connecté et est un client
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CLIENT') {
    $_SESSION['error_message'] = "Accès refusé.";
    header("Location: /app/auth/connexion.php");
    exit();
}

// Vérifier si c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Méthode non autorisée.";
    header("Location: /app/client/demander-assistance.php");
    exit();
}

// Récupérer l'ID du client
$user_id = $_SESSION['user_id'];

// Récupérer et valider les données du formulaire
$agence_id = $_POST['agence_id'] ?? null;
$aeroport_depart = strtoupper(trim($_POST['aeroport_depart'] ?? ''));
$aeroport_arrivee = strtoupper(trim($_POST['aeroport_arrivee'] ?? ''));
$date_depart = $_POST['date_depart'] ?? null;
$date_retour = $_POST['date_retour'] ?? null;
$nombre_passagers = $_POST['nombre_passagers'] ?? null;
$classe_desiree = $_POST['classe_desiree'] ?? null;
$notes_supplementaires = trim($_POST['notes_supplementaires'] ?? '');

// Tableau pour stocker les erreurs
$errors = [];

// Validation des données
if (!$agence_id || !is_numeric($agence_id)) {
    $errors[] = "Veuillez sélectionner une agence.";
}

if (empty($aeroport_depart) || !preg_match('/^[A-Z]{3}$/', $aeroport_depart)) {
    $errors[] = "Code IATA de départ invalide (3 lettres majuscules).";
}

if (empty($aeroport_arrivee) || !preg_match('/^[A-Z]{3}$/', $aeroport_arrivee)) {
    $errors[] = "Code IATA d'arrivée invalide (3 lettres majuscules).";
}

if ($aeroport_depart === $aeroport_arrivee) {
    $errors[] = "L'aéroport de départ et d'arrivée ne peuvent pas être identiques.";
}

if (empty($date_depart)) {
    $errors[] = "Date de départ requise.";
} else {
    $date_depart_obj = DateTime::createFromFormat('Y-m-d', $date_depart);
    if (!$date_depart_obj || $date_depart_obj->format('Y-m-d') !== $date_depart) {
        $errors[] = "Format de date de départ invalide.";
    } elseif ($date_depart_obj < new DateTime('today')) {
        $errors[] = "La date de départ doit être aujourd'hui ou dans le futur.";
    }
}

// Validation de la date de retour si présente
if (!empty($date_retour)) {
    $date_retour_obj = DateTime::createFromFormat('Y-m-d', $date_retour);
    if (!$date_retour_obj || $date_retour_obj->format('Y-m-d') !== $date_retour) {
        $errors[] = "Format de date de retour invalide.";
    } elseif (isset($date_depart_obj) && $date_retour_obj < $date_depart_obj) {
        $errors[] = "La date de retour doit être après la date de départ.";
    }
}

if (empty($nombre_passagers) || !is_numeric($nombre_passagers) || $nombre_passagers < 1 || $nombre_passagers > 20) {
    $errors[] = "Nombre de passagers invalide (1-20).";
}

if (!$classe_desiree || !in_array($classe_desiree, ['ECONOMIQUE', 'AFFAIRE', 'PREMIERE'])) {
    $errors[] = "Classe invalide.";
}

// Si des erreurs sont présentes, rediriger avec les erreurs
if (!empty($errors)) {
    $_SESSION['demande_errors'] = $errors;
    header("Location: /app/client/demander-assistance.php");
    exit();
}

try {
    $pdo->beginTransaction();

    // Vérifier que l'agence existe et est active
    $stmt = $pdo->prepare("
        SELECT a.id, a.nom_agence, u.email as agence_email
        FROM agences a
        JOIN utilisateurs u ON a.utilisateur_id = u.id
        WHERE a.id = :agence_id
        AND a.statut_actuel = 'ACTIF'
        AND u.statut_actuel = 'ACTIF'
        AND a.date_suppression IS NULL
    ");
    $stmt->execute(['agence_id' => $agence_id]);
    $agence = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agence) {
        throw new Exception("Agence non disponible.");
    }

    // Récupérer les infos du client
    $stmt = $pdo->prepare("
        SELECT prenom, nom, email
        FROM utilisateurs
        WHERE id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        throw new Exception("Informations client introuvables.");
    }

    // Créer la demande de vol
    $stmt = $pdo->prepare("
        INSERT INTO demandes_vols (
            client_utilisateur_id,
            agence_id,
            aeroport_depart,
            aeroport_arrivee,
            date_depart,
            date_retour,
            nombre_passagers,
            classe_desiree,
            notes_supplementaires,
            statut
        ) VALUES (
            :client_id,
            :agence_id,
            :aeroport_depart,
            :aeroport_arrivee,
            :date_depart,
            :date_retour,
            :nombre_passagers,
            :classe_desiree,
            :notes_supplementaires,
            'NOUVELLE'
        )
    ");

    $stmt->execute([
        'client_id' => $user_id,
        'agence_id' => $agence_id,
        'aeroport_depart' => $aeroport_depart,
        'aeroport_arrivee' => $aeroport_arrivee,
        'date_depart' => $date_depart,
        'date_retour' => $date_retour ?: null,
        'nombre_passagers' => $nombre_passagers,
        'classe_desiree' => $classe_desiree,
        'notes_supplementaires' => $notes_supplementaires ?: null
    ]);

    $demande_id = $pdo->lastInsertId();

    $pdo->commit();

    // Envoyer email de confirmation au client
    $classe_label = [
        'ECONOMIQUE' => 'Économique',
        'AFFAIRE' => 'Affaires',
        'PREMIERE' => 'Première classe'
    ];

    $email_client_body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <h2 style='color: #2563eb;'>Demande de vol envoyée</h2>
        <p>Bonjour {$client['prenom']},</p>
        <p>Votre demande d'assistance a bien été envoyée à <strong>{$agence['nom_agence']}</strong>.</p>

        <div style='background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;'>
            <h3 style='margin-top: 0;'>Détails de votre demande :</h3>
            <ul style='list-style: none; padding: 0;'>
                <li><strong>Trajet :</strong> {$aeroport_depart} → {$aeroport_arrivee}</li>
                <li><strong>Date de départ :</strong> " . date('d/m/Y', strtotime($date_depart)) . "</li>
                " . ($date_retour ? "<li><strong>Date de retour :</strong> " . date('d/m/Y', strtotime($date_retour)) . "</li>" : "") . "
                <li><strong>Passagers :</strong> {$nombre_passagers}</li>
                <li><strong>Classe :</strong> {$classe_label[$classe_desiree]}</li>
            </ul>
        </div>

        <p>L'agence va traiter votre demande et vous contactera rapidement avec des propositions adaptées.</p>

        <p style='margin-top: 30px;'>
            Cordialement,<br>
            <strong>L'équipe FlyManager</strong>
        </p>
    </div>
    ";

    sendEmail(
        $client['email'],
        "Demande de vol envoyée - FlyManager",
        $email_client_body,
        "Votre demande d'assistance a bien été envoyée."
    );

    // Envoyer email de notification à l'agence
    $email_agence_body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <h2 style='color: #2563eb;'>Nouvelle demande de vol</h2>
        <p>Bonjour,</p>
        <p>Vous avez reçu une nouvelle demande de vol de la part d'un client.</p>

        <div style='background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;'>
            <h3 style='margin-top: 0;'>Informations client :</h3>
            <ul style='list-style: none; padding: 0;'>
                <li><strong>Nom :</strong> {$client['prenom']} {$client['nom']}</li>
                <li><strong>Email :</strong> {$client['email']}</li>
            </ul>

            <h3>Détails du voyage :</h3>
            <ul style='list-style: none; padding: 0;'>
                <li><strong>Trajet :</strong> {$aeroport_depart} → {$aeroport_arrivee}</li>
                <li><strong>Date de départ :</strong> " . date('d/m/Y', strtotime($date_depart)) . "</li>
                " . ($date_retour ? "<li><strong>Date de retour :</strong> " . date('d/m/Y', strtotime($date_retour)) . "</li>" : "") . "
                <li><strong>Passagers :</strong> {$nombre_passagers}</li>
                <li><strong>Classe :</strong> {$classe_label[$classe_desiree]}</li>
            </ul>

            " . ($notes_supplementaires ? "<p><strong>Notes :</strong><br>" . nl2br(htmlspecialchars($notes_supplementaires)) . "</p>" : "") . "
        </div>

        <p>
            <a href='" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/app/agency/detail-demande.php?id={$demande_id}'
               style='display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px;'>
                Voir la demande
            </a>
        </p>

        <p style='margin-top: 30px;'>
            Cordialement,<br>
            <strong>L'équipe FlyManager</strong>
        </p>
    </div>
    ";

    sendEmail(
        $agence['agence_email'],
        "Nouvelle demande de vol - FlyManager",
        $email_agence_body,
        "Vous avez reçu une nouvelle demande de vol."
    );

    $_SESSION['success_message'] = "Votre demande a été envoyée avec succès à {$agence['nom_agence']} ! Vous recevrez une réponse rapidement.";
    header("Location: /app/client/home.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = "Erreur lors de l'envoi de la demande : " . $e->getMessage();
    header("Location: /app/client/demander-assistance.php");
    exit();
}
