<?php
/**
 * Fonctions pour récupérer les données de l'espace agence
 * Suit le schéma de db.sql avec tables en français
 */

/**
 * Récupère l'ID de l'agence depuis l'utilisateur
 */
function get_agency_id_from_user($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT id FROM agences
            WHERE utilisateur_id = :user_id
            AND date_suppression IS NULL
        ");
        $stmt->execute([':user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    } catch (PDOException $e) {
        error_log("Erreur get_agency_id_from_user: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les informations de l'agence
 */
function get_agency_info($pdo, $agence_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                a.*,
                u.email,
                u.statut_actuel as user_statut
            FROM agences a
            JOIN utilisateurs u ON a.utilisateur_id = u.id
            WHERE a.id = :agence_id
            AND a.date_suppression IS NULL
        ");
        $stmt->execute([':agence_id' => $agence_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_agency_info: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les statistiques de l'agence
 */
function get_agency_stats($pdo, $agence_id) {
    try {
        // Total réservations
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM reservations
            WHERE agence_id = :agence_id
            AND type_reservation = 'PAR_AGENCE'
            AND date_suppression IS NULL
        ");
        $stmt->execute([':agence_id' => $agence_id]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Confirmées
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM reservations
            WHERE agence_id = :agence_id
            AND type_reservation = 'PAR_AGENCE'
            AND statut = 'CONFIRMEE'
            AND date_suppression IS NULL
        ");
        $stmt->execute([':agence_id' => $agence_id]);
        $confirmees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Demandes en attente
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM demandes_vols
            WHERE agence_id = :agence_id
            AND statut IN ('NOUVELLE', 'VUE')
        ");
        $stmt->execute([':agence_id' => $agence_id]);
        $demandes_attente = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // En attente (réservations)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM reservations
            WHERE agence_id = :agence_id
            AND type_reservation = 'PAR_AGENCE'
            AND statut = 'EN_ATTENTE'
            AND date_suppression IS NULL
        ");
        $stmt->execute([':agence_id' => $agence_id]);
        $en_attente = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'total_reservations' => $total,
            'confirmees' => $confirmees,
            'en_attente' => $en_attente,
            'demandes_attente' => $demandes_attente
        ];
    } catch (PDOException $e) {
        error_log("Erreur get_agency_stats: " . $e->getMessage());
        return [
            'total_reservations' => 0,
            'confirmees' => 0,
            'en_attente' => 0,
            'demandes_attente' => 0
        ];
    }
}

/**
 * Récupère les dernières réservations de l'agence
 */
function get_agency_recent_reservations($pdo, $agence_id, $limit = 5) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                r.id,
                r.numero_reservation,
                r.statut,
                r.montant_total,
                r.devise,
                r.statut_paiement,
                r.date_creation,
                v.numero_vol,
                v.aeroport_depart,
                v.aeroport_arrivee,
                v.date_depart,
                v.date_arrivee,
                ca.nom_compagnie,
                ca.code_iata,
                p.prenom as passager_prenom,
                p.nom as passager_nom,
                s.numero_siege,
                s.type_classe
            FROM reservations r
            JOIN vols v ON r.vol_id = v.id
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            LEFT JOIN passagers p ON r.id = p.reservation_id
            JOIN sieges s ON r.siege_id = s.id
            WHERE r.agence_id = :agence_id
            AND r.type_reservation = 'PAR_AGENCE'
            AND r.date_suppression IS NULL
            ORDER BY r.date_creation DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':agence_id', $agence_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_agency_recent_reservations: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère toutes les réservations de l'agence avec filtres
 */
function get_agency_reservations($pdo, $agence_id, $filtre_statut = null) {
    try {
        $sql = "
            SELECT
                r.id,
                r.numero_reservation,
                r.statut,
                r.montant_total,
                r.devise,
                r.statut_paiement,
                r.mode_paiement,
                r.date_creation,
                v.numero_vol,
                v.aeroport_depart,
                v.aeroport_arrivee,
                v.date_depart,
                v.date_arrivee,
                v.statut as statut_vol,
                ca.nom_compagnie,
                ca.code_iata,
                p.prenom as passager_prenom,
                p.nom as passager_nom,
                p.email as passager_email,
                s.numero_siege,
                s.type_classe
            FROM reservations r
            JOIN vols v ON r.vol_id = v.id
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            LEFT JOIN passagers p ON r.id = p.reservation_id
            JOIN sieges s ON r.siege_id = s.id
            WHERE r.agence_id = :agence_id
            AND r.type_reservation = 'PAR_AGENCE'
            AND r.date_suppression IS NULL
        ";

        $params = [':agence_id' => $agence_id];

        if ($filtre_statut) {
            $sql .= " AND r.statut = :statut";
            $params[':statut'] = strtoupper($filtre_statut);
        }

        $sql .= " ORDER BY r.date_creation DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_agency_reservations: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les détails d'une réservation (avec vérification agence)
 */
function get_reservation_details_for_agency($pdo, $reservation_id, $agence_id) {
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
                b.numero_billet,
                b.url_pdf as billet_pdf
            FROM reservations r
            JOIN vols v ON r.vol_id = v.id
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            JOIN sieges s ON r.siege_id = s.id
            LEFT JOIN billets b ON r.id = b.reservation_id
            WHERE r.id = :reservation_id
            AND r.agence_id = :agence_id
            AND r.type_reservation = 'PAR_AGENCE'
            AND r.date_suppression IS NULL
        ");
        $stmt->execute([
            ':reservation_id' => $reservation_id,
            ':agence_id' => $agence_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_reservation_details_for_agency: " . $e->getMessage());
        return null;
    }
}

/**
 * Recherche de vols pour l'agence (même fonction que client mais avec context agency)
 */
function search_vols_for_agency($pdo, $depart, $arrivee, $date, $classe = null) {
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
            LEFT JOIN tarifs t ON v.id = t.vol_id AND t.date_suppression IS NULL
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
                    $parts = explode(':', $tarif_str);
                    if (count($parts) === 3) {
                        list($classe_type, $prix, $dispo) = $parts;
                        $vol['tarifs'][$classe_type] = [
                            'prix' => floatval($prix),
                            'disponibilite' => intval($dispo)
                        ];
                    }
                }
            }
        }

        return $vols;
    } catch (PDOException $e) {
        error_log("Erreur search_vols_for_agency: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère un vol pour réservation
 */
function get_vol_for_agency_booking($pdo, $vol_id, $classe) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                v.*,
                ca.nom_compagnie,
                ca.code_iata,
                av.modele as avion_modele,
                t.prix,
                t.disponibilite,
                t.id as tarif_id
            FROM vols v
            JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
            JOIN avions av ON v.avion_id = av.id
            JOIN tarifs t ON v.id = t.vol_id
            WHERE v.id = :vol_id
            AND t.type_classe = :classe
            AND v.statut = 'PROGRAMME'
            AND v.date_suppression IS NULL
            AND t.date_suppression IS NULL
        ");
        $stmt->execute([
            ':vol_id' => $vol_id,
            ':classe' => strtoupper($classe)
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_vol_for_agency_booking: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les sièges disponibles pour un vol
 */
function get_sieges_disponibles_for_agency($pdo, $vol_id, $classe) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM sieges
            WHERE vol_id = :vol_id
            AND type_classe = :classe
            AND statut = 'DISPONIBLE'
            AND date_suppression IS NULL
            ORDER BY numero_siege
        ");
        $stmt->execute([
            ':vol_id' => $vol_id,
            ':classe' => strtoupper($classe)
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_sieges_disponibles_for_agency: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les demandes de vols pour l'agence
 */
function get_agency_demandes($pdo, $agence_id, $filtre_statut = null) {
    try {
        $sql = "
            SELECT
                dv.id,
                dv.aeroport_depart,
                dv.aeroport_arrivee,
                dv.date_depart,
                dv.date_retour,
                dv.nombre_passagers,
                dv.classe_desiree,
                dv.notes_supplementaires,
                dv.statut,
                dv.date_creation,
                u.prenom as client_prenom,
                u.nom as client_nom,
                u.email as client_email,
                u.telephone as client_telephone
            FROM demandes_vols dv
            JOIN utilisateurs u ON dv.client_utilisateur_id = u.id
            WHERE dv.agence_id = :agence_id
        ";

        $params = [':agence_id' => $agence_id];

        if ($filtre_statut) {
            $sql .= " AND dv.statut = :statut";
            $params[':statut'] = strtoupper($filtre_statut);
        }

        $sql .= " ORDER BY
                    CASE dv.statut
                        WHEN 'NOUVELLE' THEN 1
                        WHEN 'VUE' THEN 2
                        WHEN 'TRAITEE' THEN 3
                        WHEN 'FERMEE' THEN 4
                    END,
                    dv.date_creation DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_agency_demandes: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les détails d'une demande de vol
 */
function get_demande_details($pdo, $demande_id, $agence_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                dv.*,
                u.prenom as client_prenom,
                u.nom as client_nom,
                u.email as client_email,
                u.telephone as client_telephone
            FROM demandes_vols dv
            JOIN utilisateurs u ON dv.client_utilisateur_id = u.id
            WHERE dv.id = :demande_id
            AND dv.agence_id = :agence_id
        ");
        $stmt->execute([
            ':demande_id' => $demande_id,
            ':agence_id' => $agence_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur get_demande_details: " . $e->getMessage());
        return null;
    }
}

/**
 * Compte les nouvelles demandes (pour badge notifications)
 */
function count_new_demandes($pdo, $agence_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM demandes_vols
            WHERE agence_id = :agence_id
            AND statut = 'NOUVELLE'
        ");
        $stmt->execute([':agence_id' => $agence_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log("Erreur count_new_demandes: " . $e->getMessage());
        return 0;
    }
}
