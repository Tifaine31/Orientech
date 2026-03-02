<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/auth.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/db.php";

protectPage("eleve");

$pageTitle = "Espace élève";
$roleLabel = "Élève";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/header.php";

$login = $_SESSION["login"] ?? "";
$eleve = null;
$classe = null;
[$lastResult] = [null];
if ($login !== "") {
    $stmt = $pdo->prepare(
        "SELECT id_utilisateur, nom, prenom, login, photo_profil
         FROM utilisateur
         WHERE login = ? AND acreditation = 'eleve'"
    );
    $stmt->execute([$login]);
    $eleve = $stmt->fetch();

    if ($eleve) {
        $stmtC = $pdo->prepare(
            "SELECT c.nom
             FROM liaison_classe lc
             JOIN classe c ON c.id_classe = lc.id_classe
             WHERE lc.id_utilisateur = ? AND lc.date_fin IS NULL
             LIMIT 1"
        );
        $stmtC->execute([$eleve["id_utilisateur"]]);
        $classe = $stmtC->fetchColumn() ?: null;
    }
}

if (!empty($eleve["id_utilisateur"])) {
    $stmtR = $pdo->prepare(
        "SELECT sr.nb_valides, sr.nb_invalides, sr.note_finale, s.date_heure
         FROM seance_resultat sr
         JOIN seance s ON s.id_seance = sr.id_seance
         WHERE s.id_eleve = ?
         ORDER BY s.date_heure DESC, sr.id_resultat DESC
         LIMIT 1"
    );
    $stmtR->execute([$eleve["id_utilisateur"]]);
    $lastResult = $stmtR->fetch();
}
?>

<!-- ================= CONTENU PAGE ÉLÈVE ================= -->

<div class="container my-4">

    <div class="row g-4">

        <!-- Infos élève -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 rounded-4">
                <div class="card-body d-flex align-items-center gap-4">
                    <?php if (!empty($eleve["photo_profil"])): ?>
                        <img src="<?= htmlspecialchars($eleve["photo_profil"]) ?>"
                             alt="Photo de profil"
                             style="width:70px;height:70px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <div class="user-icon">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <p><strong>Nom :</strong> <?= htmlspecialchars($eleve["nom"] ?? "") ?></p>
                        <p><strong>Prénom :</strong> <?= htmlspecialchars($eleve["prenom"] ?? "") ?></p>
                        <p><strong>Classe :</strong> <?= htmlspecialchars($classe ?? "Non assignée") ?></p>
                        <a class="btn btn-sm btn-outline-success rounded-pill mt-2"
                           href="modifier_profil.php">
                            Modifier mon profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernière course -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100 rounded-4">
                <div class="card-body">
                    <h5 class="text-center fw-bold mb-3">Dernière course</h5>

                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Date</th>
                                <th>Résultats</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$lastResult): ?>
                                <tr>
                                    <td colspan="3">Aucun résultat.</td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td><?= htmlspecialchars(substr($lastResult["date_heure"], 0, 10)) ?></td>
                                    <td>
                                        <?php for ($i = 0; $i < (int)$lastResult["nb_valides"]; $i++): ?>
                                            <span class="ok">✔</span>
                                        <?php endfor; ?>
                                        <?php for ($i = 0; $i < (int)$lastResult["nb_invalides"]; $i++): ?>
                                            <span class="ko">✖</span>
                                        <?php endfor; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars((string)$lastResult["note_finale"]) ?>/20</strong></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

    <!-- Historique -->
    <div class="card shadow-sm border-0 rounded-4 mt-5">
        <div class="card-body">

            <h5 class="text-center fw-bold mb-4">Historique</h5>

            <table class="table table-bordered text-center align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>Date</th>
                        <th>Résultats</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>12/02/2025</td>
                        <td>
                            <span class="ok">✔</span>
                            <span class="ok">✔</span>
                            <span class="ok">✔</span>
                            <span class="ko">✖</span>
                            <span class="ok">✔</span>
                            <span class="ko">✖</span>
                            <span class="ok">✔</span>
                            <span class="ok">✔</span>
                        </td>
                        <td><strong>15/20</strong></td>
                    </tr>
                    <tr>
                        <td>10/01/2025</td>
                        <td>
                            <span class="ko">✖</span>
                            <span class="ok">✔</span>
                            <span class="ko">✖</span>
                            <span class="ko">✖</span>
                            <span class="ok">✔</span>
                            <span class="ok">✔</span>
                            <span class="ko">✖</span>
                            <span class="ok">✔</span>
                        </td>
                        <td><strong>13/20</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <button class="btn btn-orientech rounded-pill px-5"
                        onclick="window.location.href='historique_resultats_eleve.php'">
                    Suite
                </button>
            </div>

        </div>
    </div>

</div>

<!-- ================= FIN CONTENU ================= -->

<?php
// Footer commun
include "../includes/footer.php";
?>
