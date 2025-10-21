<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';

// Récupérer les critères de recherche
$depart = $_GET['depart'] ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date = $_GET['date'] ?? '';
$classe = $_GET['classe'] ?? '';

$vols = [];
$search_performed = false;

// Si une recherche est lancée
if ($depart && $arrivee && $date) {
    $search_performed = true;

    // Requête pour chercher les vols
    $query = "SELECT v.*, ca.nom_compagnie,
              (SELECT prix FROM tarifs WHERE vol_id = v.id AND type_classe = :classe LIMIT 1) as prix,
              (SELECT disponibilite FROM tarifs WHERE vol_id = v.id AND type_classe = :classe2 LIMIT 1) as places_disponibles
              FROM vols v
              JOIN compagnies_aeriennes ca ON v.compagnie_id = ca.id
              WHERE v.aeroport_depart LIKE :depart
              AND v.aeroport_arrivee LIKE :arrivee
              AND DATE(v.date_depart) = :date
              AND v.statut = 'PREVU'
              AND v.date_suppression IS NULL
              ORDER BY v.date_depart ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':depart' => "%$depart%",
        ':arrivee' => "%$arrivee%",
        ':date' => $date,
        ':classe' => $classe ?: 'ECONOMIQUE',
        ':classe2' => $classe ?: 'ECONOMIQUE'
    ]);
    $vols = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'layouts/header.php';
?>

<link rel="stylesheet" href="assets/css/contact.css">

<section style="padding: 4rem 0; background: #f9fafb;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">

        <!-- En-tête -->
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="font-size: 2.5rem; font-weight: bold; color: #1e293b; margin-bottom: 1rem;">Rechercher un Vol</h1>
            <p style="font-size: 1.1rem; color: #64748b;">Trouvez le vol parfait pour votre prochain voyage</p>
        </div>

        <!-- Formulaire de recherche -->
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <form method="GET" action="">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">

                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #334155;">Aéroport de départ</label>
                        <input type="text" name="depart" value="<?= htmlspecialchars($depart) ?>"
                               placeholder="Ex: CDG, JFK..."
                               style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #334155;">Aéroport d'arrivée</label>
                        <input type="text" name="arrivee" value="<?= htmlspecialchars($arrivee) ?>"
                               placeholder="Ex: LHR, LAX..."
                               style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #334155;">Date de départ</label>
                        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>"
                               min="<?= date('Y-m-d') ?>"
                               style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #334155;">Classe</label>
                        <select name="classe" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;">
                            <option value="ECONOMIQUE" <?= $classe === 'ECONOMIQUE' ? 'selected' : '' ?>>Économique</option>
                            <option value="AFFAIRE" <?= $classe === 'AFFAIRE' ? 'selected' : '' ?>>Affaire</option>
                            <option value="PREMIERE" <?= $classe === 'PREMIERE' ? 'selected' : '' ?>>Première</option>
                        </select>
                    </div>
                </div>

                <button type="submit" style="width: 100%; padding: 1rem; background: #3b82f6; color: white; border: none; border-radius: 0.5rem; font-size: 1.1rem; font-weight: 600; cursor: pointer;">
                    Rechercher
                </button>
            </form>
        </div>

        <!-- Résultats -->
        <?php if ($search_performed): ?>
            <div>
                <h2 style="font-size: 1.5rem; font-weight: bold; color: #1e293b; margin-bottom: 1.5rem;">
                    <?= count($vols) ?> vol(s) trouvé(s)
                </h2>

                <?php if (empty($vols)): ?>
                    <div style="background: white; padding: 3rem; border-radius: 1rem; text-align: center;">
                        <p style="color: #64748b; font-size: 1.1rem;">Aucun vol trouvé pour ces critères. Essayez de modifier votre recherche.</p>
                    </div>
                <?php else: ?>
                    <div style="display: grid; gap: 1rem;">
                        <?php foreach ($vols as $vol): ?>
                            <div style="background: white; padding: 1.5rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">

                                <div style="flex: 1; min-width: 200px;">
                                    <div style="font-weight: bold; color: #3b82f6; margin-bottom: 0.5rem;">
                                        <?= htmlspecialchars($vol['numero_vol']) ?> - <?= htmlspecialchars($vol['nom_compagnie']) ?>
                                    </div>
                                    <div style="font-size: 1.2rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem;">
                                        <?= htmlspecialchars($vol['aeroport_depart']) ?> → <?= htmlspecialchars($vol['aeroport_arrivee']) ?>
                                    </div>
                                    <div style="color: #64748b; font-size: 0.9rem;">
                                        Départ: <?= date('d/m/Y H:i', strtotime($vol['date_depart'])) ?> |
                                        Arrivée: <?= date('d/m/Y H:i', strtotime($vol['date_arrivee'])) ?>
                                    </div>
                                </div>

                                <div style="text-align: right;">
                                    <?php if ($vol['prix']): ?>
                                        <div style="font-size: 1.5rem; font-weight: bold; color: #059669; margin-bottom: 0.5rem;">
                                            <?= number_format($vol['prix'], 2) ?> €
                                        </div>
                                        <div style="font-size: 0.9rem; color: #64748b; margin-bottom: 1rem;">
                                            <?= $vol['places_disponibles'] ?? 0 ?> places disponibles
                                        </div>
                                    <?php endif; ?>

                                    <a href="<?= url('app/auth/connexion.php') ?>"
                                       style="display: inline-block; padding: 0.75rem 1.5rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600;">
                                        Connexion pour réserver
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
include 'layouts/footer.php';
?>