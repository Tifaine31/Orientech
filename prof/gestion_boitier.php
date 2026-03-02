<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

$pageTitle = "Gestion des boîtiers";
$roleLabel = "Professeur";

include "../includes/header.php";

$stmt = $pdo->query("SELECT numero, add_mac, add_reseau, etat, balisevalid FROM boitier ORDER BY numero DESC");
$boitiers = $stmt->fetchAll();
?>

<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Gestion des boîtiers</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='prof.php'">
                ← Retour
            </button>
        </div>

        <div class="mb-4">
            <form class="row g-3" method="post" action="gestion_boitier_action.php">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Adresse MAC</label>
                    <input type="text" name="add_mac" class="form-control" placeholder="AA:BB:CC:DD:EE:FF">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Adresse réseau</label>
                    <input type="text" name="add_reseau" class="form-control" placeholder="192.168.1.10">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">État</label>
                    <select name="etat" class="form-select">
                        <option value="disponible">Disponible</option>
                        <option value="occupe">Occupé</option>
                        <option value="hors_service">Hors service</option>
                    </select>
                </div>
                <div class="col-12">
                    <button class="btn btn-orientech rounded-pill px-4" type="submit">Ajouter le boîtier</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>Adresse MAC</th>
                        <th>Adresse réseau</th>
                        <th>État</th>
                        <th>Balise valide</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($boitiers) === 0): ?>
                    <tr><td colspan="6">Aucun boîtier.</td></tr>
                <?php else: ?>
                    <?php foreach ($boitiers as $b): ?>
                        <tr>
                            <td><?= (int)$b["numero"] ?></td>
                            <td><?= htmlspecialchars($b["add_mac"] ?? "") ?></td>
                            <td><?= htmlspecialchars($b["add_reseau"] ?? "") ?></td>
                            <td><?= htmlspecialchars($b["etat"] ?? "") ?></td>
                            <td><?= htmlspecialchars((string)($b["balisevalid"] ?? "")) ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-danger rounded-pill"
                                   href="gestion_boitier_delete.php?id=<?= (int)$b["numero"] ?>"
                                   onclick="return confirm('Supprimer ce boîtier ?')">
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

<?php
include "../includes/footer.php";
?>
