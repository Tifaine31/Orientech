<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: gestion_seances.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT s.id_seance, s.date_heure, u.id_utilisateur, u.nom, u.prenom, u.login
     FROM seance s
     JOIN utilisateur u ON u.id_utilisateur = s.id_eleve
     WHERE s.id_seance = ?"
);
$stmt->execute([$id]);
$seance = $stmt->fetch();

if (!$seance) {
    header("Location: gestion_seances.php");
    exit;
}

$parcours = $pdo->prepare(
    "SELECT p.numero_du_parcours, p.nom,
            COUNT(c.numerodecarte) AS nb_balises
     FROM seance_parcours sp
     JOIN parcours p ON p.numero_du_parcours = sp.id_parcours
     LEFT JOIN composer c ON c.numero_du_parcours = p.numero_du_parcours
     WHERE sp.id_seance = ?
     GROUP BY p.numero_du_parcours, p.nom
     ORDER BY p.nom"
);
$parcours->execute([$id]);
$parcours = $parcours->fetchAll();

if (count($parcours) === 0) {
    $parcours = $pdo->query(
        "SELECT p.numero_du_parcours, p.nom,
                COUNT(c.numerodecarte) AS nb_balises
         FROM parcours p
         LEFT JOIN composer c ON c.numero_du_parcours = p.numero_du_parcours
         GROUP BY p.numero_du_parcours, p.nom
         ORDER BY p.nom"
    )->fetchAll();
}

$resultats = $pdo->prepare(
    "SELECT r.id_resultat, r.id_parcours, p.nom AS parcours_nom, p.niveau,
            r.nb_valides, r.nb_invalides, r.note_auto, r.note_finale, r.use_auto,
            r.heure_debut, r.heure_fin, r.duree,
            (SELECT COUNT(*) FROM composer c WHERE c.numero_du_parcours = r.id_parcours) AS total_balises
     FROM seance_resultat r
     JOIN parcours p ON p.numero_du_parcours = r.id_parcours
     WHERE r.id_seance = ?
     ORDER BY r.id_resultat DESC"
);
$resultats->execute([$id]);
$resultats = $resultats->fetchAll();

$pageTitle = "Séance";
$roleLabel = "Professeur";

include "../includes/header.php";
?>

<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">
                Séance de <?= htmlspecialchars($seance["prenom"] . " " . $seance["nom"]) ?>
            </h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='gestion_seances.php'">
                ← Retour
            </button>
        </div>

        <!-- Parcours associé (lecture seule) -->
        <div class="mb-4">
            <label class="form-label fw-bold">Parcours de la séance</label>
            <input type="text" class="form-control"
                   value="<?= count($parcours) ? htmlspecialchars($parcours[0]["nom"]) : "Aucun parcours" ?>"
                   disabled>
        </div>

        <?php if (count($parcours) > 0): ?>
        <div class="mb-4">
            <form class="row g-3" method="post" action="seance_add_note_manuelle.php">
                <input type="hidden" name="id_seance" value="<?= (int)$seance["id_seance"] ?>">
                <input type="hidden" name="id_parcours" value="<?= (int)$parcours[0]["numero_du_parcours"] ?>">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Note manuelle</label>
                    <input type="text" name="note_manuelle" class="form-control"
                           inputmode="decimal"
                           placeholder="Ex: 13 ou 13,5"
                           title="Nombre uniquement (décimales autorisées)"
                           required>
                </div>
                <div class="col-12">
                    <button class="btn btn-orientech rounded-pill px-4" type="submit">
                        Enregistrer la note manuelle
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Résultats -->
        <h5 class="fw-bold mb-3">Résultats</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Parcours</th>
                        <th>Difficulté</th>
                        <th>Validées</th>
                        <th>Note manuelle</th>
                        <th>Heure début</th>
                        <th>Heure fin</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($resultats) === 0): ?>
                        <tr><td colspan="6">Aucun résultat.</td></tr>
                    <?php else: ?>
                        <?php foreach ($resultats as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r["parcours_nom"]) ?></td>
                                <td><?= htmlspecialchars($r["niveau"] ?? "") ?></td>
                                <td><?= (int)$r["nb_valides"] ?>/<?= (int)$r["total_balises"] ?></td>
                                <td><?= htmlspecialchars((string)$r["note_finale"]) ?></td>
                                <td><?= htmlspecialchars($r["heure_debut"] ?? "") ?></td>
                                <td><?= htmlspecialchars($r["heure_fin"] ?? "") ?></td>
                                <td><?= htmlspecialchars($r["duree"] ?? "") ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php
include "../includes/footer.php";
?>
