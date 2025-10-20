<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si un utilisateur est connecté.
 *
 * @return bool True si l'utilisateur est connecté, false sinon.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;
}

/**
 * Récupère l'utilisateur connecté.
 *
 * @return array|null Les données de l'utilisateur ou null.
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        "id" => $_SESSION["user_id"] ?? null,
        "prenom" => $_SESSION["user_prenom"] ?? null,
        "nom" => $_SESSION["user_nom"] ?? null,
        "email" => $_SESSION["user_email"] ?? null,
        "type_utilisateur" => $_SESSION["user_type"] ?? null,
    ];
}

/**
 * Vérifie si l'utilisateur connecté est un ADMIN.
 *
 * @return bool True si l'utilisateur est un ADMIN, false sinon.
 */
function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION["user_type"] ?? "") === "ADMIN";
}

/**
 * Vérifie si l'utilisateur connecté est une COMPAGNIE.
 *
 * @return bool True si l'utilisateur est une COMPAGNIE, false sinon.
 */
function isAirline(): bool
{
    return isLoggedIn() && ($_SESSION["user_type"] ?? "") === "COMPAGNIE";
}

/**
 * Vérifie si l'utilisateur connecté est une AGENCE.
 *
 * @return bool True si l'utilisateur est une AGENCE, false sinon.
 */
function isAgency(): bool
{
    return isLoggedIn() && ($_SESSION["user_type"] ?? "") === "AGENCE";
}

/**
 * Vérifie si l'utilisateur connecté est un CLIENT.
 *
 * @return bool True si l'utilisateur est un CLIENT, false sinon.
 */
function isClient(): bool
{
    return isLoggedIn() && ($_SESSION["user_type"] ?? "") === "CLIENT";
}

/**
 * Redirige l'utilisateur vers le dashboard approprié selon son type.
 */
function redirectToDashboard(): void
{
    if (!isLoggedIn()) {
        header("Location: /monAvion/public/connexion.php");
        exit();
    }

    $type = $_SESSION["user_type"] ?? "";

    switch ($type) {
        case "ADMIN":
            header("Location: /monAvion/app/admin/home.php");
            break;
        case "AGENCE":
            header("Location: /monAvion/app/agency/home.php");
            break;
        case "COMPAGNIE":
            header("Location: /monAvion/app/airline/home.php");
            break;
        case "CLIENT":
            header("Location: /monAvion/app/client/home.php");
            break;
        default:
            header("Location: /monAvion/public/index.php");
    }
    exit();
}

/**
 * Déconnexion de l'utilisateur.
 */
function logout(): void
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"],
        );
    }

    session_destroy();

    header("Location: /monAvion/public/index.php");
    exit();
}
