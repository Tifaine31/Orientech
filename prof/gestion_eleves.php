<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
protectPage("prof");

$pageTitle = "Espace professeur";
$roleLabel = "Professeur";

include "../includes/header.php";

$classes = $pdo->query("SELECT id_classe, nom FROM classe ORDER BY nom")->fetchAll();

$filterClasse = (int)($_GET["id_classe"] ?? 0);
$filterNom = trim($_GET["nom"] ?? "");
$filterPrenom = trim($_GET["prenom"] ?? "");

$where = ["u.acreditation = 'eleve'"];
$params = [];
if ($filterClasse > 0) {
    $where[] = "c.id_classe = ?";
    $params[] = $filterClasse;
}
if ($filterNom !== "") {
    $where[] = "u.nom LIKE ?";
    $params[] = "%" . $filterNom . "%";
}
if ($filterPrenom !== "") {
    $where[] = "u.prenom LIKE ?";
    $params[] = "%" . $filterPrenom . "%";
}

$sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.login,
               c.nom AS classe
        FROM utilisateur u
        LEFT JOIN liaison_classe l
          ON l.id_utilisateur = u.id_utilisateur
         AND l.date_fin IS NULL
        LEFT JOIN classe c
          ON c.id_classe = l.id_classe
        WHERE " . implode(" AND ", $where) . "
        ORDER BY u.nom, u.prenom";

$stmtEleves = $pdo->prepare($sql);
$stmtEleves->execute($params);
$eleves = $stmtEleves->fetchAll();
?>


<!-- Contenu -->
<div class="container my-4">

    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <!-- Titre + retour -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Gestion des élèves</h4>

            <!-- Retour (outline, vert #4CAF50 via CSS) -->
            <button class="btn btn-outline-success rounded-pill"
                    onclick="retourDashboard()">
                ← Retour
            </button>
        </div>

        <!-- Filtres -->
        <div class="mb-4">
            <form class="row g-3" method="get">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Classe</label>
                    <select name="id_classe" class="form-select rounded-pill">
                        <option value="">Toutes</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= (int)$c["id_classe"] ?>" <?= $filterClasse === (int)$c["id_classe"] ? "selected" : "" ?>>
                                <?= htmlspecialchars($c["nom"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" class="form-control rounded-pill" value="<?= htmlspecialchars($filterNom) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Prénom</label>
                    <input type="text" name="prenom" class="form-control rounded-pill" value="<?= htmlspecialchars($filterPrenom) ?>">
                </div>
                <div class="col-12">
                    <button class="btn btn-outline-success rounded-pill px-4" type="submit">Rechercher</button>
                </div>
            </form>
        </div>

        <!-- Création élève (popup) -->
        <div class="mb-4">
            <button class="btn btn-orientech rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalEleve">
                + Ajouter un élève
            </button>
        </div>

        <div class="mb-4">
            <form method="post" action="gestion_eleves_supprimer_tout.php" onsubmit="return confirm('Supprimer tous les élèves ? Cette action est irréversible.');">
                <button class="btn btn-outline-danger rounded-pill px-4" type="submit">
                    Supprimer tous les élèves
                </button>
            </form>
        </div>

        <!-- Tableau élèves -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Classe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eleves as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e["nom"]) ?></td>
                            <td><?= htmlspecialchars($e["prenom"]) ?></td>
                            <td>
                                <form class="d-flex gap-2" method="post" action="gestion_eleves_affecter.php">
                                    <input type="hidden" name="id_utilisateur" value="<?= (int)$e["id_utilisateur"] ?>">
                                    <select name="id_classe" class="form-select form-select-sm">
                                        <option value="">Sans classe</option>
                                        <?php foreach ($classes as $c): ?>
                                            <option value="<?= (int)$c["id_classe"] ?>"
                                                <?= $e["classe"] == $c["nom"] ? "selected" : "" ?>>
                                                <?= htmlspecialchars($c["nom"]) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-outline-success rounded-pill" type="submit">
                                        Affecter
                                    </button>
                                </form>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-danger rounded-pill"
                                   href="gestion_eleves_supprimer.php?id=<?= (int)$e["id_utilisateur"] ?>"
                                   onclick="return confirm('Supprimer cet élève ?');">
                                    Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
    alert("Erreur: login déjà utilisé ou champs invalides.");
}
</script>

<!-- Modal création élève -->
<div class="modal fade" id="modalEleve" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Ajouter un élève</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="gestion_eleves_action.php">
          <div class="col-md-3">
            <label class="form-label fw-bold">Nom</label>
            <input type="text" name="nom" class="form-control rounded-pill" placeholder="Ex: Dupont" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Prénom</label>
            <input type="text" name="prenom" class="form-control rounded-pill" placeholder="Ex: Marie" required>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Login</label>
            <input type="text" name="login" class="form-control rounded-pill" placeholder="Ex: m.dupont" required>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Mot de passe</label>
            <input type="password" name="password" class="form-control rounded-pill" placeholder="••••••••" required>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Classe</label>
            <select name="id_classe" class="form-select rounded-pill">
              <option value="">Sans classe</option>
              <?php foreach ($classes as $c): ?>
                <option value="<?= (int)$c["id_classe"] ?>"><?= htmlspecialchars($c["nom"]) ?></option>
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

<?php
include "../includes/footer.php";
?>
