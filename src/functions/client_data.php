<?php
/**
 * Fonctions pour récupérer les données de l'espace client
 */

/**
 * Récupère les statistiques du client
 */
function get_client_stats($pdo, $user_id) {
    try {
        // Total réservations
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM reservations
            WHERE client_id = :user_id AND date_suppression IS NULL
        ");
        $stmt->execute([':user_id' => $user_id]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Confirmées
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM reservations
            WHERE client_id = :user_id
            AND statut = 'CONFIRMEE'
            AND date_suppression IS NULL
        ");
        $stmt->execute([':user_id' => $user_id]);
        $confirmees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // En attente
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM reservations
            WHERE client_id = :user_id
            AND statut = 'EN_ATTENTE'
            AND date_suppression IS NULL
        ");
        $stmt->execute([':user_id' => $user_id]);
        $en_attente = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'total' => $total,
            'confirmees' => $confirmees,
            'en_attente' => $en_attente
        ];
    } catch (PDOException $e) {
        error_log("Erreur get_client_stats: " . $e->getMessage());
        return ['total' => 0, 'confirmees' => 0, 'en_attente' => 0];
    }
}

/**
 * Récupère les dernières réservations du client
 */
function get_client_recent_reservations($pdo, $user_id, $limit = 3) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                r.id,
                r.numero_reservation,
                r.statut,
                r.type_reservation,
                r.statut_paiement,
                r.montant_total,
                r.date_creation,
                v.numero_vol,
                v.aeroport_depart,
                v.aeroport_arrivee,
                v.date_depart,
                v.date_arrivee,
                ca.nom_compagnie,
                ca.code_iata
            FROM reservations r
            JOIN vols v ON r.vol_id = v.id
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            WHERE r.client_id = :user_id
            AND r.date_suppression IS NULL
            ORDER BY r.date_creation DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_client_recent_reservations: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère toutes les réservations du client avec filtres
 */
function get_client_reservations($pdo, $user_id, $filtre_statut = null, $filtre_type = null) {
    try {
        $sql = "
            SELECT
                r.id,
                r.numero_reservation,
                r.statut,
                r.type_reservation,
                r.statut_paiement,
                r.mode_paiement,
                r.montant_total,
                r.devise,
                r.date_creation,
                v.numero_vol,
                v.aeroport_depart,
                v.aeroport_arrivee,
                v.date_depart,
                v.date_arrivee,
                ca.nom_compagnie,
                ca.code_iata,
                s.numero_siege,
                s.type_classe,
                a.nom_agence
            FROM reservations r
            JOIN vols v ON r.vol_id = v.id
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            JOIN sieges s ON r.siege_id = s.id
            LEFT JOIN agences a ON r.agence_id = a.id
            WHERE r.client_id = :user_id
            AND r.date_suppression IS NULL
        ";

        $params = [':user_id' => $user_id];

        if ($filtre_statut) {
            $sql .= " AND r.statut = :statut";
            $params[':statut'] = strtoupper($filtre_statut);
        }

        if ($filtre_type) {
            $sql .= " AND r.type_reservation = :type";
            $params[':type'] = strtoupper($filtre_type);
        }

        $sql .= " ORDER BY r.date_creation DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_client_reservations: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les détails complets d'une réservation
 */
function get_reservation_details($pdo, $reservation_id, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                r.*,
                v.numero_vol,
                v.aeroport_depart,
                v.aeroport_arrivee,
                v.date_depart,
                v.date_arrivee,
                v.statut as statut_vol,
                ca.nom_compagnie,
                ca.code_iata,
                s.numero_siege,
                s.type_classe,
                a.nom_agence,
                a.telephone as agence_telephone,
                a.adresse as agence_adresse,
                b.numero_billet,
                b.url_pdf as billet_pdf
            FROM reservations r
            JOIN vols v ON r.vol_id = v.id
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            JOIN sieges s ON r.siege_id = s.id
            LEFT JOIN agences a ON r.agence_id = a.id
            LEFT JOIN billets b ON r.id = b.reservation_id
            WHERE r.id = :reservation_id
            AND r.client_id = :user_id
            AND r.date_suppression IS NULL
        ");
        $stmt->execute([
            ':reservation_id' => $reservation_id,
            ':user_id' => $user_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_reservation_details: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les passagers d'une réservation
 */
function get_reservation_passagers($pdo, $reservation_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM passagers
            WHERE reservation_id = :reservation_id
            AND date_suppression IS NULL
        ");
        $stmt->execute([':reservation_id' => $reservation_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_reservation_passagers: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère l'historique d'une réservation
 */
function get_reservation_historique($pdo, $reservation_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM historique_statuts_reservations
            WHERE reservation_id = :reservation_id
            ORDER BY date_creation DESC
        ");
        $stmt->execute([':reservation_id' => $reservation_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_reservation_historique: " . $e->getMessage());
        return [];
    }
}

/**
 * Recherche des vols
 */
function search_vols($pdo, $depart, $arrivee, $date, $classe = null) {
    try {
        $sql = "
            SELECT
                v.id as vol_id,
                v.numero_vol,
                v.aeroport_depart,
                v.aeroport_arrivee,
                v.date_depart,
                v.date_arrivee,
                v.statut,
                ca.nom_compagnie,
                ca.code_iata,
                av.modele as avion_modele,
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        t.type_classe, ':', t.prix, ':', t.disponibilite
                    ) SEPARATOR ';'
                ) as tarifs_info
            FROM vols v
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            JOIN avions av ON v.avion_id = av.id
            LEFT JOIN tarifs t ON v.id = t.vol_id
            WHERE v.aeroport_depart = :depart
            AND v.aeroport_arrivee = :arrivee
            AND DATE(v.date_depart) = :date
            AND v.statut = 'PROGRAMME'
            AND v.date_suppression IS NULL
        ";

        $params = [
            ':depart' => strtoupper($depart),
            ':arrivee' => strtoupper($arrivee),
            ':date' => $date
        ];

        if ($classe) {
            $sql .= " AND t.type_classe = :classe";
            $params[':classe'] = strtoupper($classe);
        }

        $sql .= " GROUP BY v.id ORDER BY v.date_depart ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $vols = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parser les tarifs
        foreach ($vols as &$vol) {
            $vol['tarifs'] = [];
            if ($vol['tarifs_info']) {
                $tarifs_array = explode(';', $vol['tarifs_info']);
                foreach ($tarifs_array as $tarif_str) {
                    list($classe_type, $prix, $dispo) = explode(':', $tarif_str);
                    $vol['tarifs'][$classe_type] = [
                        'prix' => floatval($prix),
                        'disponibilite' => intval($dispo)
                    ];
                }
            }
        }

        return $vols;
    } catch (PDOException $e) {
        error_log("Erreur search_vols: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les infos d'un vol pour réservation
 */
function get_vol_for_reservation($pdo, $vol_id, $classe) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                v.*,
                ca.nom_compagnie,
                ca.code_iata,
                av.modele as avion_modele,
                t.prix,
                t.disponibilite
            FROM vols v
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            JOIN avions av ON v.avion_id = av.id
            JOIN tarifs t ON v.id = t.vol_id
            WHERE v.id = :vol_id
            AND t.type_classe = :classe
            AND v.statut = 'PROGRAMME'
            AND v.date_suppression IS NULL
        ");
        $stmt->execute([
            ':vol_id' => $vol_id,
            ':classe' => strtoupper($classe)
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_vol_for_reservation: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les sièges disponibles
 */
function get_sieges_disponibles($pdo, $vol_id, $classe) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM sieges
            WHERE vol_id = :vol_id
            AND type_classe = :classe
            AND statut = 'DISPONIBLE'
            ORDER BY numero_siege
        ");
        $stmt->execute([
            ':vol_id' => $vol_id,
            ':classe' => strtoupper($classe)
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_sieges_disponibles: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les infos utilisateur
 */
function get_user_info($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM utilisateurs
            WHERE id = :user_id
            AND type_utilisateur = 'CLIENT'
        ");
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_user_info: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère la liste des agences actives pour les demandes de vol
 */
function get_active_agencies($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                a.id,
                a.nom_agence,
                a.adresse,
                a.telephone,
                a.pays
            FROM agences a
            JOIN utilisateurs u ON a.utilisateur_id = u.id
            WHERE a.statut_actuel = 'ACTIF'
            AND u.statut_actuel = 'ACTIF'
            AND a.date_suppression IS NULL
            ORDER BY a.nom_agence ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_active_agencies: " . $e->getMessage());
        return [];
    }
}
