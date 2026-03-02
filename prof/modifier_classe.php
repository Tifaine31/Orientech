<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

$idClasse = (int)($_GET["id_classe"] ?? 0);
if ($idClasse <= 0) {
    header("Location: gestion_classes.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id_classe, nom FROM classe WHERE id_classe = ?");
$stmt->execute([$idClasse]);
$classe = $stmt->fetch();

if (!$classe) {
    header("Location: gestion_classes.php");
    exit;
}

$elevesClasse = $pdo->prepare(
    "SELECT u.id_utilisateur, u.nom, u.prenom, u.login
     FROM utilisateur u
     JOIN liaison_classe l ON l.id_utilisateur = u.id_utilisateur
     WHERE l.id_classe = ? AND l.date_fin IS NULL
     ORDER BY u.nom, u.prenom"
);
$elevesClasse->execute([$idClasse]);
$elevesClasse = $elevesClasse->fetchAll();

$elevesDisponibles = $pdo->prepare(
    "SELECT u.id_utilisateur, u.nom, u.prenom, u.login
     FROM utilisateur u
     LEFT JOIN liaison_classe l
       ON l.id_utilisateur = u.id_utilisateur AND l.date_fin IS NULL
     WHERE u.acreditation = 'eleve' AND l.id_utilisateur IS NULL
     ORDER BY u.nom, u.prenom"
);
$elevesDisponibles->execute();
$elevesDisponibles = $elevesDisponibles->fetchAll();

$pageTitle = "Classe - " . $classe["nom"];
$roleLabel = "Professeur";

include "../includes/header.php";
?>

<!-- Contenu -->
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <!-- Titre + retour -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Classe : <?= htmlspecialchars($classe["nom"]) ?></h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='gestion_classes.php'">
                ← Retour
            </button>
        </div>

        <!-- Renommer la classe -->
        <form class="row g-3 mb-4" method="post" action="modifier_classe_rename.php">
            <input type="hidden" name="id_classe" value="<?= (int)$classe["id_classe"] ?>">
            <div class="col-md-6">
                <label class="form-label fw-bold">Nom de la classe</label>
                <input type="text" name="nom" class="form-control rounded-pill" value="<?= htmlspecialchars($classe["nom"]) ?>" required>
            </div>
            <div class="col-12">
                <button class="btn btn-orientech rounded-pill px-4" type="submit">Renommer</button>
            </div>
        </form>

        <!-- Ajouter un élève -->
        <div class="mb-4">
            <form class="d-flex gap-2" method="post" action="modifier_classe_add_eleve.php">
                <input type="hidden" name="id_classe" value="<?= (int)$classe["id_classe"] ?>">
                <select name="id_utilisateur" class="form-select rounded-pill" required>
                    <option value="">Sélectionner un élève</option>
                    <?php foreach ($elevesDisponibles as $e): ?>
                        <option value="<?= (int)$e["id_utilisateur"] ?>">
                            <?= htmlspecialchars($e["prenom"] . " " . $e["nom"] . " (" . $e["login"] . ")") ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-orientech rounded-pill px-4" type="submit">
                    + Ajouter l’élève
                </button>
            </form>
        </div>

        <!-- Import CSV -->
        <div class="mb-4">
            <form class="d-flex gap-2" method="post" action="modifier_classe_import_csv.php" enctype="multipart/form-data">
                <input type="hidden" name="id_classe" value="<?= (int)$classe["id_classe"] ?>">
                <input type="file" name="csv_file" class="form-control rounded-pill" accept=".csv" required>
                <button class="btn btn-orientech rounded-pill px-4" type="submit">
                    Importer CSV
                </button>
            </form>
            <small class="text-muted d-block mt-2">
                Format attendu (CSV) : la 1ère colonne contient "NOM Prénom". Le login est généré "nom.prenom" et le mot de passe par défaut est "1234".
            </small>
        </div>

        <!-- Liste élèves de la classe -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($elevesClasse) === 0): ?>
                        <tr>
                            <td colspan="4">Aucun élève dans cette classe.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($elevesClasse as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e["nom"]) ?></td>
                                <td><?= htmlspecialchars($e["prenom"]) ?></td>
                                <td><?= htmlspecialchars($e["login"]) ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-danger rounded-pill"
                                       href="modifier_classe_remove_eleve.php?id_classe=<?= (int)$classe["id_classe"] ?>&id_utilisateur=<?= (int)$e["id_utilisateur"] ?>"
                                       onclick="return confirm('Retirer cet élève de la classe ?');">
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
