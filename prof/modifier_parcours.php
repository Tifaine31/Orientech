<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: gestion_parcours.php");
    exit;
}

$stmt = $pdo->prepare("SELECT numero_du_parcours, nom, niveau, commentaire FROM parcours WHERE numero_du_parcours = ?");
$stmt->execute([$id]);
$parcours = $stmt->fetch();

if (!$parcours) {
    header("Location: gestion_parcours.php");
    exit;
}

$balises = $pdo->query("SELECT numerodecarte, tagRFID FROM balise_RFID ORDER BY numerodecarte")->fetchAll();

$balisesParcours = $pdo->prepare(
    "SELECT b.numerodecarte, b.tagRFID, c.ordre
     FROM balise_RFID b
     JOIN composer c ON c.numerodecarte = b.numerodecarte
     WHERE c.numero_du_parcours = ?
     ORDER BY c.ordre, b.numerodecarte"
);
$balisesParcours->execute([$id]);
$balisesParcours = $balisesParcours->fetchAll();

$pageTitle = "Parcours - " . $parcours["nom"];
$roleLabel = "Professeur";

include "../includes/header.php";
?>

<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Parcours : <?= htmlspecialchars($parcours["nom"]) ?></h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='gestion_parcours.php'">
                ← Retour
            </button>
        </div>

        <!-- Modifier infos parcours -->
        <form class="row g-3 mb-4" method="post" action="modifier_parcours_action.php">
            <input type="hidden" name="id" value="<?= (int)$parcours["numero_du_parcours"] ?>">
            <div class="col-md-3">
                <label class="form-label fw-bold">Nom</label>
                <input type="text" name="nom" class="form-control rounded-pill" value="<?= htmlspecialchars($parcours["nom"]) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Niveau</label>
                <input type="text" name="niveau" class="form-control rounded-pill" value="<?= htmlspecialchars($parcours["niveau"] ?? "") ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Commentaire</label>
                <input type="text" name="commentaire" class="form-control rounded-pill" value="<?= htmlspecialchars($parcours["commentaire"] ?? "") ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-orientech rounded-pill px-4" type="submit">
                    Enregistrer
                </button>
            </div>
        </form>

        <!-- Ajouter balise -->
        <div class="mb-4">
            <form class="d-flex gap-2" method="post" action="modifier_parcours_add_balise.php">
                <input type="hidden" name="id" value="<?= (int)$parcours["numero_du_parcours"] ?>">
                <select name="numerodecarte" class="form-select rounded-pill" required>
                    <option value="">Sélectionner une balise</option>
                    <?php foreach ($balises as $b): ?>
                        <option value="<?= (int)$b["numerodecarte"] ?>">
                            <?= htmlspecialchars(($b["tagRFID"] ?? "Balise") . " (#" . $b["numerodecarte"] . ")") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-orientech rounded-pill px-4" type="submit">
                    + Ajouter la balise
                </button>
            </form>
        </div>

        <!-- Liste balises -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Ordre</th>
                        <th>Balise</th>
                        <th>Tag RFID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($balisesParcours) === 0): ?>
                        <tr>
                            <td colspan="4">Aucune balise associée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($balisesParcours as $b): ?>
                            <tr>
                                <td>
                                    <form class="d-flex justify-content-center gap-2" method="post" action="modifier_parcours_update_ordre.php">
                                        <input type="hidden" name="id" value="<?= (int)$parcours["numero_du_parcours"] ?>">
                                        <input type="hidden" name="numerodecarte" value="<?= (int)$b["numerodecarte"] ?>">
                                        <input type="number" name="ordre" class="form-control form-control-sm text-center" style="max-width: 90px"
                                               min="1" value="<?= (int)$b["ordre"] ?>" required>
                                        <button class="btn btn-sm btn-outline-success rounded-pill" type="submit">OK</button>
                                    </form>
                                </td>
                                <td><?= (int)$b["numerodecarte"] ?></td>
                                <td><?= htmlspecialchars($b["tagRFID"] ?? "") ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-danger rounded-pill"
                                       href="modifier_parcours_remove_balise.php?id=<?= (int)$parcours["numero_du_parcours"] ?>&numerodecarte=<?= (int)$b["numerodecarte"] ?>"
                                       onclick="return confirm('Retirer cette balise ?');">
                                        Retirer
                                    </a>
                                </td>
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
