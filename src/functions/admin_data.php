<?php
/**
 * Fonctions pour récupérer les données de l'espace ADMIN
 */

/**
 * Récupère les statistiques globales de la plateforme
 */
function get_admin_stats($pdo) {
    try {
        $stats = [];

        // Total utilisateurs par type
        $stmt = $pdo->query("
            SELECT
                type_utilisateur,
                COUNT(*) as total
            FROM utilisateurs
            WHERE date_suppression IS NULL
            GROUP BY type_utilisateur
        ");
        $users_by_type = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $stats['total_clients'] = $users_by_type['CLIENT'] ?? 0;
        $stats['total_agences'] = $users_by_type['AGENCY'] ?? 0;
        $stats['total_compagnies'] = $users_by_type['COMPAGNIE'] ?? 0;
        $stats['total_admins'] = $users_by_type['ADMIN'] ?? 0;
        $stats['total_utilisateurs'] = array_sum($users_by_type);

        // Demandes en attente
        $stmt = $pdo->query("
            SELECT COUNT(*) FROM demandes_agences
            WHERE statut = 'EN_ATTENTE' AND date_suppression IS NULL
        ");
        $stats['demandes_agences_attente'] = $stmt->fetchColumn();

        $stmt = $pdo->query("
            SELECT COUNT(*) FROM demandes_compagnies
            WHERE statut = 'EN_ATTENTE' AND date_suppression IS NULL
        ");
        $stats['demandes_compagnies_attente'] = $stmt->fetchColumn();

        // Vols et réservations
        $stmt = $pdo->query("
            SELECT COUNT(*) FROM vols
            WHERE date_suppression IS NULL
        ");
        $stats['total_vols'] = $stmt->fetchColumn();

        $stmt = $pdo->query("
            SELECT COUNT(*) FROM reservations
            WHERE statut IN ('CONFIRMEE', 'EN_ATTENTE')
            AND date_suppression IS NULL
        ");
        $stats['total_reservations'] = $stmt->fetchColumn();

        // Messages contact non traités
        $stmt = $pdo->query("
            SELECT COUNT(*) FROM messages_contact
            WHERE statut = 'NOUVEAU'
            AND date_suppression IS NULL
        ");
        $stats['messages_non_traites'] = $stmt->fetchColumn();

        return $stats;

    } catch (PDOException $e) {
        error_log("Erreur get_admin_stats: " . $e->getMessage());
        return [
            'total_clients' => 0,
            'total_agences' => 0,
            'total_compagnies' => 0,
            'total_admins' => 0,
            'total_utilisateurs' => 0,
            'demandes_agences_attente' => 0,
            'demandes_compagnies_attente' => 0,
            'total_vols' => 0,
            'total_reservations' => 0,
            'messages_non_traites' => 0
        ];
    }
}

/**
 * Récupère la liste de tous les utilisateurs avec filtres
 */
function get_all_users($pdo, $type_filter = null, $search = null, $limit = 50, $offset = 0) {
    try {
        $sql = "
            SELECT
                u.*,
                CASE
                    WHEN u.type_utilisateur = 'AGENCY' THEN a.nom_agence
                    WHEN u.type_utilisateur = 'COMPAGNIE' THEN c.nom_compagnie
                    ELSE CONCAT(u.prenom, ' ', u.nom)
                END as display_name
            FROM utilisateurs u
            LEFT JOIN agences a ON u.id = a.utilisateur_id AND a.date_suppression IS NULL
            LEFT JOIN compagnies_aeriennes c ON u.id = c.utilisateur_id AND c.date_suppression IS NULL
            WHERE u.date_suppression IS NULL
        ";

        $params = [];

        if ($type_filter) {
            $sql .= " AND u.type_utilisateur = :type";
            $params['type'] = $type_filter;
        }

        if ($search) {
            $sql .= " AND (
                u.email LIKE :search
                OR u.prenom LIKE :search
                OR u.nom LIKE :search
                OR a.nom_agence LIKE :search
                OR c.nom_compagnie LIKE :search
            )";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY u.date_creation DESC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur get_all_users: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les demandes d'agences
 */
function get_demandes_agences($pdo, $statut_filter = null) {
    try {
        $sql = "
            SELECT
                da.*,
                u.email,
                u.prenom,
                u.nom
            FROM demandes_agences da
            JOIN utilisateurs u ON da.utilisateur_id = u.id
            WHERE da.date_suppression IS NULL
        ";

        $params = [];

        if ($statut_filter) {
            $sql .= " AND da.statut = :statut";
            $params['statut'] = $statut_filter;
        }

        $sql .= " ORDER BY
            CASE da.statut
                WHEN 'EN_ATTENTE' THEN 1
                WHEN 'APPROUVEE' THEN 2
                WHEN 'REJETEE' THEN 3
            END,
            da.date_demande DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur get_demandes_agences: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les demandes de compagnies
 */
function get_demandes_compagnies($pdo, $statut_filter = null) {
    try {
        $sql = "
            SELECT
                dc.*,
                u.email,
                u.prenom,
                u.nom
            FROM demandes_compagnies dc
            JOIN utilisateurs u ON dc.utilisateur_id = u.id
            WHERE dc.date_suppression IS NULL
        ";

        $params = [];

        if ($statut_filter) {
            $sql .= " AND dc.statut = :statut";
            $params['statut'] = $statut_filter;
        }

        $sql .= " ORDER BY
            CASE dc.statut
                WHEN 'EN_ATTENTE' THEN 1
                WHEN 'APPROUVEE' THEN 2
                WHEN 'REJETEE' THEN 3
            END,
            dc.date_demande DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur get_demandes_compagnies: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère tous les vols avec informations compagnie
 */
function get_all_vols($pdo, $statut_filter = null, $limit = 100, $offset = 0) {
    try {
        $sql = "
            SELECT
                v.*,
                c.nom_compagnie,
                a.modele as avion_modele,
                COUNT(DISTINCT r.id) as nombre_reservations
            FROM vols v
            JOIN compagnies_aeriennes c ON v.compagnie_id = c.id
            JOIN avions a ON v.avion_id = a.id
            LEFT JOIN reservations r ON v.id = r.vol_id
                AND r.statut IN ('CONFIRMEE', 'EN_ATTENTE')
                AND r.date_suppression IS NULL
            WHERE v.date_suppression IS NULL
        ";

        $params = [];

        if ($statut_filter) {
            $sql .= " AND v.statut = :statut";
            $params['statut'] = $statut_filter;
        }

        $sql .= " GROUP BY v.id ORDER BY v.date_depart DESC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur get_all_vols: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère toutes les réservations
 */
function get_all_reservations($pdo, $statut_filter = null, $limit = 100, $offset = 0) {
    try {
        $sql = "
            SELECT
                r.*,
                v.numero_vol,
                v.aeroport_depart,
                v.aeroport_arrivee,
                v.date_depart,
                c.nom_compagnie,
                t.type_classe,
                t.prix,
                p.prenom as passager_prenom,
                p.nom as passager_nom,
                p.email as passager_email
            FROM reservations r
            JOIN vols v ON r.vol_id = v.id
            JOIN compagnies_aeriennes c ON v.compagnie_id = c.id
            JOIN tarifs t ON r.tarif_id = t.id
            LEFT JOIN passagers p ON r.id = p.reservation_id
            WHERE r.date_suppression IS NULL
        ";

        $params = [];

        if ($statut_filter) {
            $sql .= " AND r.statut = :statut";
            $params['statut'] = $statut_filter;
        }

        $sql .= " ORDER BY r.date_reservation DESC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur get_all_reservations: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les messages de contact
 */
function get_messages_contact($pdo, $statut_filter = null) {
    try {
        $sql = "
            SELECT *
            FROM messages_contact
            WHERE date_suppression IS NULL
        ";

        $params = [];

        if ($statut_filter) {
            $sql .= " AND statut = :statut";
            $params['statut'] = $statut_filter;
        }

        $sql .= " ORDER BY
            CASE statut
                WHEN 'NOUVEAU' THEN 1
                WHEN 'EN_COURS' THEN 2
                WHEN 'RESOLU' THEN 3
            END,
            date_envoi DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur get_messages_contact: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les détails d'un utilisateur avec ses entités liées
 */
function get_user_details($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT u.*
            FROM utilisateurs u
            WHERE u.id = :user_id
            AND u.date_suppression IS NULL
        ");
        $stmt->execute(['user_id' => $user_id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        // Récupérer les infos spécifiques selon le type
        if ($user['type_utilisateur'] === 'AGENCY') {
            $stmt = $pdo->prepare("
                SELECT * FROM agences
                WHERE utilisateur_id = :user_id
                AND date_suppression IS NULL
            ");
            $stmt->execute(['user_id' => $user_id]);
            $user['agence'] = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($user['type_utilisateur'] === 'COMPAGNIE') {
            $stmt = $pdo->prepare("
                SELECT * FROM compagnies_aeriennes
                WHERE utilisateur_id = :user_id
                AND date_suppression IS NULL
            ");
            $stmt->execute(['user_id' => $user_id]);
            $user['compagnie'] = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $user;

    } catch (PDOException $e) {
        error_log("Erreur get_user_details: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les activités récentes (dernières actions sur la plateforme)
 */
function get_recent_activities($pdo, $limit = 20) {
    try {
        $activities = [];

        // Dernières réservations
        $stmt = $pdo->prepare("
            SELECT
                'reservation' as type,
                r.date_reservation as date_action,
                CONCAT(u.prenom, ' ', u.nom) as user_name,
                v.numero_vol as details
            FROM reservations r
            JOIN utilisateurs u ON r.client_utilisateur_id = u.id
            JOIN vols v ON r.vol_id = v.id
            WHERE r.date_suppression IS NULL
            ORDER BY r.date_reservation DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Derniers vols créés
        $stmt = $pdo->prepare("
            SELECT
                'vol' as type,
                v.date_creation as date_action,
                c.nom_compagnie as user_name,
                v.numero_vol as details
            FROM vols v
            JOIN compagnies_aeriennes c ON v.compagnie_id = c.id
            WHERE v.date_suppression IS NULL
            ORDER BY v.date_creation DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Trier par date
        usort($activities, function($a, $b) {
            return strtotime($b['date_action']) - strtotime($a['date_action']);
        });

        return array_slice($activities, 0, $limit);

    } catch (PDOException $e) {
        error_log("Erreur get_recent_activities: " . $e->getMessage());
        return [];
    }
}
