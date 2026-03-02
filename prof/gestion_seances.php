<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
protectPage("prof");

$pageTitle = "Espace professeur";
$roleLabel = "Professeur";

include "../includes/header.php";

$eleves = $pdo->query(
    "SELECT id_utilisateur, nom, prenom, login
     FROM utilisateur
     WHERE acreditation = 'eleve'
     ORDER BY nom, prenom"
)->fetchAll();

$parcours = $pdo->query(
    "SELECT numero_du_parcours, nom
     FROM parcours
     ORDER BY nom"
)->fetchAll();

$boitiers = $pdo->query(
    "SELECT numero, add_mac
     FROM boitier
     ORDER BY numero"
)->fetchAll();

$dateFilter = trim($_GET["date"] ?? "");
$filterNom = trim($_GET["nom"] ?? "");
$filterPrenom = trim($_GET["prenom"] ?? "");

$where = [];
$params = [];
if ($dateFilter !== "") {
    $where[] = "DATE(s.date_heure) = ?";
    $params[] = $dateFilter;
}
if ($filterNom !== "") {
    $where[] = "u.nom LIKE ?";
    $params[] = "%" . $filterNom . "%";
}
if ($filterPrenom !== "") {
    $where[] = "u.prenom LIKE ?";
    $params[] = "%" . $filterPrenom . "%";
}

$sql = "SELECT s.id_seance, s.date_heure, u.nom, u.prenom, u.login
        FROM seance s
        JOIN utilisateur u ON u.id_utilisateur = s.id_eleve";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY s.date_heure DESC";

$stmtSeances = $pdo->prepare($sql);
$stmtSeances->execute($params);
$seances = $stmtSeances->fetchAll();
?>

<!-- Contenu -->
<div class="container my-4">

<div class="card shadow border-0 rounded-4 p-4 p-md-5">

    <!-- Titre + retour -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-orientech mb-0">Gestion des séances</h4>
        <button class="btn btn-outline-success rounded-pill"
                onclick="retourDashboard()">
            ← Retour
        </button>
    </div>

        <!-- Filtres -->
        <div class="mb-4">
            <form class="row g-3" method="get">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Date</label>
                    <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($dateFilter) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($filterNom) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Prénom</label>
                    <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($filterPrenom) ?>">
                </div>
                <div class="col-12">
                    <button class="btn btn-outline-success rounded-pill px-4" type="submit">Rechercher</button>
                </div>
            </form>
        </div>

    <!-- Création séance (popup) -->
    <div class="mb-4">
        <button class="btn btn-orientech rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalSeance">
            + Créer une séance
        </button>
    </div>

    <!-- Liste séances -->
    <h5 class="fw-bold mb-3">Séances</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-secondary">
                <tr>
                    <th>Date & heure</th>
                    <th>Élève</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($seances) === 0): ?>
                    <tr><td colspan="3">Aucune séance.</td></tr>
                <?php else: ?>
                    <?php foreach ($seances as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s["date_heure"]) ?></td>
                            <td><?= htmlspecialchars($s["prenom"] . " " . $s["nom"] . " (" . $s["login"] . ")") ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-success rounded-pill me-2"
                                   href="seance_detail.php?id=<?= (int)$s["id_seance"] ?>">
                                    Gérer
                                </a>
                                <a class="btn btn-sm btn-outline-danger rounded-pill"
                                   href="gestion_seances_delete.php?id=<?= (int)$s["id_seance"] ?>"
                                   onclick="return confirm('Supprimer cette séance ?');">
                                    Supprimer
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

<!-- Modal création séance -->
<div class="modal fade" id="modalSeance" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Créer une séance (par élève)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="gestion_seances_action.php">
          <div class="col-md-6">
            <label class="form-label fw-bold">Élève</label>
            <select name="id_eleve" class="form-select" required>
              <option value="">Sélectionner un élève</option>
              <?php foreach ($eleves as $e): ?>
                <option value="<?= (int)$e["id_utilisateur"] ?>">
                  <?= htmlspecialchars($e["prenom"] . " " . $e["nom"] . " (" . $e["login"] . ")") ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Date de la séance</label>
            <input type="date" name="date" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Parcours (à la création)</label>
            <select name="id_parcours" class="form-select">
              <option value="">Aucun</option>
              <?php foreach ($parcours as $p): ?>
                <option value="<?= (int)$p["numero_du_parcours"] ?>">
                  <?= htmlspecialchars($p["nom"]) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Boîtier (optionnel)</label>
            <select name="numero_boitier" class="form-select">
              <option value="">Aucun</option>
              <?php foreach ($boitiers as $b): ?>
                <option value="<?= (int)$b["numero"] ?>">
                  Boîtier #<?= (int)$b["numero"] ?><?= $b["add_mac"] ? " - " . htmlspecialchars($b["add_mac"]) : "" ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
            <button class="btn btn-orientech rounded-pill px-4" type="submit">Créer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script>
function retourDashboard() {
    window.location.href = "prof.php";
}

const params = new URLSearchParams(window.location.search);
if (params.get("error") === "1") {
    alert("Erreur: champs invalides.");
}
</script>

<?php
include "../includes/footer.php";
?>
