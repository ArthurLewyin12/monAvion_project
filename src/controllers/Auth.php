<?php
/**
 * Classe d'authentification et gestion des utilisateurs
 */

/**
 * Crée un nouvel utilisateur
 * @param PDO $pdo Instance PDO
 * @param string $prenom Prénom
 * @param string $nom Nom
 * @param string $email Email
 * @param string $telephone Téléphone (optionnel)
 * @param string $password Mot de passe
 * @param string $type_utilisateur Type d'utilisateur (CLIENT, AGENCE, COMPAGNIE, ADMIN)
 * @param bool $newsletter S'abonner à la newsletter
 * @return array ['success' => bool, 'user_id' => int|null, 'errors' => array]
 */
function create_user($pdo, $prenom, $nom, $email, $telephone, $password, $type_utilisateur = 'CLIENT', $newsletter = false) {
    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            return [
                'success' => false,
                'user_id' => null,
                'errors' => ['email' => 'Cette adresse email est déjà utilisée.']
            ];
        }

        // Hasher le mot de passe
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur
        $stmt = $pdo->prepare("
            INSERT INTO utilisateurs (
                prenom, nom, email, telephone, mot_de_passe,
                type_utilisateur, newsletter, statut
            )
            VALUES (
                :prenom, :nom, :email, :telephone, :mot_de_passe,
                :type_utilisateur, :newsletter, 'ACTIF'
            )
        ");

        $stmt->execute([
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone ?: null,
            ':mot_de_passe' => $password_hash,
            ':type_utilisateur' => $type_utilisateur,
            ':newsletter' => $newsletter ? 1 : 0
        ]);

        $user_id = $pdo->lastInsertId();

        return [
            'success' => true,
            'user_id' => $user_id,
            'errors' => []
        ];

    } catch (PDOException $e) {
        error_log("Erreur création utilisateur: " . $e->getMessage());
        return [
            'success' => false,
            'user_id' => null,
            'errors' => ['db' => 'Une erreur est survenue lors de la création du compte.']
        ];
    }
}

/**
 * Authentifie un utilisateur (alias pour authenticate_user)
 * @param PDO $pdo Instance PDO
 * @param string $email Email
 * @param string $password Mot de passe
 * @return array ['success' => bool, 'user' => array|null, 'error' => string|null]
 */
function login($pdo, $email, $password) {
    return authenticate_user($pdo, $email, $password);
}

/**
 * Authentifie un utilisateur
 * @param PDO $pdo Instance PDO
 * @param string $email Email
 * @param string $password Mot de passe
 * @return array ['success' => bool, 'user' => array|null, 'error' => string|null]
 */
function authenticate_user($pdo, $email, $password) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, prenom, nom, email, mot_de_passe, type_utilisateur, statut
            FROM utilisateurs
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [
                'success' => false,
                'user' => null,
                'error' => 'Email ou mot de passe incorrect.'
            ];
        }

        // Vérifier le statut
        if ($user['statut'] !== 'ACTIF') {
            return [
                'success' => false,
                'user' => null,
                'error' => 'Votre compte a été désactivé. Contactez l\'administrateur.'
            ];
        }

        // Vérifier le mot de passe
        if (!password_verify($password, $user['mot_de_passe'])) {
            return [
                'success' => false,
                'user' => null,
                'error' => 'Email ou mot de passe incorrect.'
            ];
        }

        // Supprimer le mot de passe du résultat
        unset($user['mot_de_passe']);

        return [
            'success' => true,
            'user' => $user,
            'error' => null
        ];

    } catch (PDOException $e) {
        error_log("Erreur authentification: " . $e->getMessage());
        return [
            'success' => false,
            'user' => null,
            'error' => 'Une erreur est survenue lors de la connexion.'
        ];
    }
}

/**
 * Met à jour le dernier login de l'utilisateur
 * @param PDO $pdo Instance PDO
 * @param int $user_id ID de l'utilisateur
 */
function update_last_login($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            UPDATE utilisateurs
            SET derniere_connexion = CURRENT_TIMESTAMP
            WHERE id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
    } catch (PDOException $e) {
        error_log("Erreur update last login: " . $e->getMessage());
    }
}

/**
 * Récupère un utilisateur par son ID
 * @param PDO $pdo Instance PDO
 * @param int $user_id ID de l'utilisateur
 * @return array|null Les données de l'utilisateur ou null
 */
function get_user_by_id($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, prenom, nom, email, telephone, type_utilisateur, statut, created_at
            FROM utilisateurs
            WHERE id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        error_log("Erreur get user by id: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère un utilisateur par son email
 * @param PDO $pdo Instance PDO
 * @param string $email Email de l'utilisateur
 * @return array|null Les données de l'utilisateur ou null
 */
function get_user_by_email($pdo, $email) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, prenom, nom, email, telephone, type_utilisateur, statut, created_at
            FROM utilisateurs
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        error_log("Erreur get user by email: " . $e->getMessage());
        return null;
    }
}
