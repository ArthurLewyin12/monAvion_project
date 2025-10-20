<?php
/**
 * Fonctions pour gérer les templates d'emails
 */

/**
 * Rend un template d'email avec les variables fournies
 *
 * @param string $template_name Nom du template (sans extension)
 * @param array $variables Variables à passer au template
 * @param bool $is_text Si true, utilise la version texte du template
 * @return string Le contenu HTML/texte du template rendu
 */
function render_email_template($template_name, $variables = [], $is_text = false) {
    $extension = $is_text ? '_text.php' : '.php';
    $template_path = __DIR__ . '/../templates/emails/' . $template_name . $extension;

    if (!file_exists($template_path)) {
        error_log("Template email introuvable: $template_path");
        return '';
    }

    // Extraire les variables pour les rendre disponibles dans le template
    extract($variables);

    // Capturer le contenu du template
    ob_start();
    include $template_path;
    $content = ob_get_clean();

    return $content;
}

/**
 * Envoie un email en utilisant un template
 *
 * @param string $to Email du destinataire
 * @param string $subject Sujet de l'email
 * @param string $template_name Nom du template à utiliser
 * @param array $variables Variables à passer au template
 * @return bool True si l'email est envoyé, false sinon
 */
function send_templated_email($to, $subject, $template_name, $variables = []) {
    require_once __DIR__ . '/sendEmail.php';

    // Générer le contenu HTML
    $html_body = render_email_template($template_name, $variables, false);

    // Générer la version texte
    $text_body = render_email_template($template_name, $variables, true);

    // Si le template texte n'existe pas, créer une version simplifiée
    if (empty($text_body)) {
        $text_body = strip_tags($html_body);
    }

    return sendEmail($to, $subject, $html_body, $text_body);
}

/**
 * Envoie l'email de bienvenue à un nouveau client
 *
 * @param string $email Email du client
 * @param string $prenom Prénom du client
 * @param string $dashboard_url URL du dashboard client
 * @return bool
 */
function send_welcome_email($email, $prenom, $dashboard_url) {
    return send_templated_email(
        $email,
        "Bienvenue sur FlyManager !",
        "welcome_client",
        [
            'prenom' => $prenom,
            'dashboard_url' => $dashboard_url
        ]
    );
}

/**
 * Envoie un email de confirmation de réservation
 *
 * @param string $email Email du client
 * @param array $reservation_data Données de la réservation
 * @return bool
 */
function send_reservation_confirmation_email($email, $reservation_data) {
    return send_templated_email(
        $email,
        "Confirmation de votre réservation - " . $reservation_data['numero_reservation'],
        "reservation_confirmation",
        $reservation_data
    );
}

/**
 * Envoie un email de demande de partenariat reçue
 *
 * @param string $email Email du demandeur
 * @param string $type Type de demande (agence ou compagnie)
 * @param string $nom Nom de l'entité
 * @return bool
 */
function send_partnership_request_received_email($email, $type, $nom) {
    return send_templated_email(
        $email,
        "Demande de partenariat reçue",
        "partnership_request_received",
        [
            'type' => $type,
            'nom' => $nom
        ]
    );
}
