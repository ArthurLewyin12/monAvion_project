<?php
/**
 * Fonctions pour récupérer les données de l'espace compagnie aérienne
 */

/**
 * Récupère l'ID de la compagnie depuis l'ID utilisateur
 */
function get_compagnie_id_from_user($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT id
            FROM compagnies_aeriennes
            WHERE utilisateur_id = :user_id
            AND date_suppression IS NULL
        ");
        $stmt->execute(['user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['id'] : null;
    } catch (PDOException $e) {
        error_log("Erreur get_compagnie_id_from_user: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les informations complètes de la compagnie
 */
function get_compagnie_info($pdo, $compagnie_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                c.*,
                u.email,
                u.prenom,
                u.nom
            FROM compagnies_aeriennes c
            JOIN utilisateurs u ON c.utilisateur_id = u.id
            WHERE c.id = :compagnie_id
            AND c.date_suppression IS NULL
        ");
        $stmt->execute(['compagnie_id' => $compagnie_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_compagnie_info: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les statistiques du dashboard compagnie
 */
function get_compagnie_stats($pdo, $compagnie_id) {
    try {
        // Total vols
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM vols
            WHERE compagnie_id = :compagnie_id
            AND date_suppression IS NULL
        ");
        $stmt->execute(['compagnie_id' => $compagnie_id]);
        $total_vols = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Vols programmés (futurs)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM vols
            WHERE compagnie_id = :compagnie_id
            AND statut = 'PROGRAMME'
            AND date_depart > NOW()
            AND date_suppression IS NULL
        ");
        $stmt->execute(['compagnie_id' => $compagnie_id]);
        $vols_programmes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Vols retardés
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM vols
            WHERE compagnie_id = :compagnie_id
            AND statut = 'RETARDE'
            AND date_suppression IS NULL
        ");
        $stmt->execute(['compagnie_id' => $compagnie_id]);
        $vols_retardes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Vols annulés
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM vols
            WHERE compagnie_id = :compagnie_id
            AND statut = 'ANNULE'
            AND date_suppression IS NULL
        ");
        $stmt->execute(['compagnie_id' => $compagnie_id]);
        $vols_annules = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Nombre d'avions
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM avions
            WHERE compagnie_id = :compagnie_id
            AND date_suppression IS NULL
        ");
        $stmt->execute(['compagnie_id' => $compagnie_id]);
        $total_avions = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total réservations
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM reservations r
            JOIN vols v ON r.vol_id = v.id
            WHERE v.compagnie_id = :compagnie_id
            AND r.statut IN ('CONFIRMEE', 'EN_ATTENTE')
            AND r.date_suppression IS NULL
        ");
        $stmt->execute(['compagnie_id' => $compagnie_id]);
        $total_reservations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'total_vols' => $total_vols,
            'vols_programmes' => $vols_programmes,
            'vols_retardes' => $vols_retardes,
            'vols_annules' => $vols_annules,
            'total_avions' => $total_avions,
            'total_reservations' => $total_reservations
        ];
    } catch (PDOException $e) {
        error_log("Erreur get_compagnie_stats: " . $e->getMessage());
        return [
            'total_vols' => 0,
            'vols_programmes' => 0,
            'vols_retardes' => 0,
            'vols_annules' => 0,
            'total_avions' => 0,
            'total_reservations' => 0
        ];
    }
}

/**
 * Récupère les vols récents de la compagnie
 */
function get_compagnie_recent_vols($pdo, $compagnie_id, $limit = 5) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                v.*,
                a.modele as avion_modele
            FROM vols v
            JOIN avions a ON v.avion_id = a.id
            WHERE v.compagnie_id = :compagnie_id
            AND v.date_suppression IS NULL
            ORDER BY v.date_creation DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':compagnie_id', $compagnie_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_compagnie_recent_vols: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les prochains départs de la compagnie
 */
function get_compagnie_prochains_departs($pdo, $compagnie_id, $limit = 5) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                v.*,
                a.modele as avion_modele,
                COUNT(DISTINCT r.id) as nombre_reservations
            FROM vols v
            JOIN avions a ON v.avion_id = a.id
            LEFT JOIN reservations r ON v.id = r.vol_id
                AND r.statut IN ('CONFIRMEE', 'EN_ATTENTE')
                AND r.date_suppression IS NULL
            WHERE v.compagnie_id = :compagnie_id
            AND v.date_depart > NOW()
            AND v.statut = 'PROGRAMME'
            AND v.date_suppression IS NULL
            GROUP BY v.id
            ORDER BY v.date_depart ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':compagnie_id', $compagnie_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_compagnie_prochains_departs: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère la liste des avions de la compagnie
 */
function get_compagnie_avions($pdo, $compagnie_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                a.*,
                COUNT(DISTINCT v.id) as nombre_vols
            FROM avions a
            LEFT JOIN vols v ON a.id = v.avion_id AND v.date_suppression IS NULL
            WHERE a.compagnie_id = :compagnie_id
            AND a.date_suppression IS NULL
            GROUP BY a.id
            ORDER BY a.date_creation DESC
        ");
        $stmt->execute(['compagnie_id' => $compagnie_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_compagnie_avions: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les détails d'un avion
 */
function get_avion_details($pdo, $avion_id, $compagnie_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT a.*
            FROM avions a
            WHERE a.id = :avion_id
            AND a.compagnie_id = :compagnie_id
            AND a.date_suppression IS NULL
        ");
        $stmt->execute([
            'avion_id' => $avion_id,
            'compagnie_id' => $compagnie_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_avion_details: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère la liste des vols de la compagnie avec filtres
 */
function get_compagnie_vols($pdo, $compagnie_id, $filtre_statut = null, $filtre_date = null) {
    try {
        $sql = "
            SELECT
                v.*,
                a.modele as avion_modele,
                COUNT(DISTINCT r.id) as nombre_reservations,
                a.nombre_sieges_total
            FROM vols v
            JOIN avions a ON v.avion_id = a.id
            LEFT JOIN reservations r ON v.id = r.vol_id
                AND r.statut IN ('CONFIRMEE', 'EN_ATTENTE')
                AND r.date_suppression IS NULL
            WHERE v.compagnie_id = :compagnie_id
            AND v.date_suppression IS NULL
        ";

        $params = ['compagnie_id' => $compagnie_id];

        if ($filtre_statut && in_array($filtre_statut, ['PROGRAMME', 'RETARDE', 'ANNULE'])) {
            $sql .= " AND v.statut = :statut";
            $params['statut'] = $filtre_statut;
        }

        if ($filtre_date) {
            $sql .= " AND DATE(v.date_depart) = :date";
            $params['date'] = $filtre_date;
        }

        $sql .= " GROUP BY v.id ORDER BY v.date_depart DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_compagnie_vols: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les détails complets d'un vol
 */
function get_vol_details_for_compagnie($pdo, $vol_id, $compagnie_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                v.*,
                a.modele as avion_modele,
                a.nombre_sieges_total,
                a.sieges_par_classe
            FROM vols v
            JOIN avions a ON v.avion_id = a.id
            WHERE v.id = :vol_id
            AND v.compagnie_id = :compagnie_id
            AND v.date_suppression IS NULL
        ");
        $stmt->execute([
            'vol_id' => $vol_id,
            'compagnie_id' => $compagnie_id
        ]);

        $vol = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vol) {
            // Récupérer les tarifs
            $stmt = $pdo->prepare("
                SELECT * FROM tarifs
                WHERE vol_id = :vol_id
            ");
            $stmt->execute(['vol_id' => $vol_id]);
            $vol['tarifs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer les stats de réservations par classe
            $stmt = $pdo->prepare("
                SELECT
                    t.type_classe,
                    COUNT(r.id) as nombre_reservations
                FROM tarifs t
                LEFT JOIN reservations r ON t.id = r.tarif_id
                    AND r.statut IN ('CONFIRMEE', 'EN_ATTENTE')
                    AND r.date_suppression IS NULL
                WHERE t.vol_id = :vol_id
                GROUP BY t.type_classe
            ");
            $stmt->execute(['vol_id' => $vol_id]);
            $vol['reservations_par_classe'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $vol;
    } catch (PDOException $e) {
        error_log("Erreur get_vol_details_for_compagnie: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les réservations d'un vol
 */
function get_vol_reservations($pdo, $vol_id, $compagnie_id) {
    try {
        // Vérifier que le vol appartient à la compagnie
        $stmt = $pdo->prepare("
            SELECT id FROM vols
            WHERE id = :vol_id
            AND compagnie_id = :compagnie_id
        ");
        $stmt->execute([
            'vol_id' => $vol_id,
            'compagnie_id' => $compagnie_id
        ]);

        if (!$stmt->fetch()) {
            return [];
        }

        // Récupérer les réservations
        $stmt = $pdo->prepare("
            SELECT
                r.*,
                t.type_classe,
                t.prix,
                p.prenom as passager_prenom,
                p.nom as passager_nom,
                p.email as passager_email,
                s.numero_siege,
                CASE
                    WHEN r.agence_id IS NOT NULL THEN a.nom_agence
                    ELSE 'Réservation directe'
                END as source_reservation
            FROM reservations r
            JOIN tarifs t ON r.tarif_id = t.id
            LEFT JOIN passagers p ON r.id = p.reservation_id
            LEFT JOIN sieges s ON p.siege_id = s.id
            LEFT JOIN agences a ON r.agence_id = a.id
            WHERE r.vol_id = :vol_id
            AND r.date_suppression IS NULL
            ORDER BY r.date_reservation DESC
        ");
        $stmt->execute(['vol_id' => $vol_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_vol_reservations: " . $e->getMessage());
        return [];
    }
}

/**
 * Vérifie si un numéro de vol existe déjà
 */
function check_numero_vol_exists($pdo, $numero_vol, $compagnie_id, $exclude_vol_id = null) {
    try {
        $sql = "
            SELECT COUNT(*) as count
            FROM vols
            WHERE numero_vol = :numero_vol
            AND compagnie_id = :compagnie_id
            AND date_suppression IS NULL
        ";

        $params = [
            'numero_vol' => $numero_vol,
            'compagnie_id' => $compagnie_id
        ];

        if ($exclude_vol_id) {
            $sql .= " AND id != :exclude_vol_id";
            $params['exclude_vol_id'] = $exclude_vol_id;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    } catch (PDOException $e) {
        error_log("Erreur check_numero_vol_exists: " . $e->getMessage());
        return false;
    }
}
