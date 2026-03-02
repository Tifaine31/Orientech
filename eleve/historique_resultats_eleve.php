<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
protectPage("eleve");

$pageTitle = "Espace élève";
$roleLabel = "Élève";

include "../includes/header.php";

$login = $_SESSION["login"] ?? "";
$eleve = null;
$results = [];
if ($login !== "") {
    $stmt = $pdo->prepare(
        "SELECT id_utilisateur
         FROM utilisateur
         WHERE login = ? AND acreditation = 'eleve'"
    );
    $stmt->execute([$login]);
    $eleve = $stmt->fetchColumn();

    if ($eleve) {
        $stmtR = $pdo->prepare(
            "SELECT sr.nb_valides, sr.nb_invalides, sr.note_finale, s.date_heure
             FROM seance_resultat sr
             JOIN seance s ON s.id_seance = sr.id_seance
             WHERE s.id_eleve = ?
             ORDER BY s.date_heure DESC, sr.id_resultat DESC"
        );
        $stmtR->execute([$eleve]);
        $results = $stmtR->fetchAll();
    }
}
?>


    <!-- Contenu -->
    <div class="container my-4">

        <div class="card shadow border-0 rounded-4 p-4">
            <!-- Titre + retour -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Gestion des classes</h4>
            <button class="btn btn-outline-success rounded-pill"
                    onclick="retourEleve()">
                ← Retour
            </button>
        </div>

            <h4 class="text-center fw-bold mb-4">Historique</h4>

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead>
                        <tr class="table-secondary">
                            <th style="width: 20%">Date</th>
                            <th style="width: 60%">Résultats</th>
                            <th style="width: 20%">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($results) === 0): ?>
                            <tr><td colspan="3">Aucun résultat.</td></tr>
                        <?php else: ?>
                            <?php foreach ($results as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars(substr($r["date_heure"], 0, 10)) ?></td>
                                    <td>
                                        <?php for ($i = 0; $i < (int)$r["nb_valides"]; $i++): ?>
                                            <span class="ok">✔</span>
                                        <?php endfor; ?>
                                        <?php for ($i = 0; $i < (int)$r["nb_invalides"]; $i++): ?>
                                            <span class="ko">✖</span>
                                        <?php endfor; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars((string)$r["note_finale"]) ?>/20</strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>


            </div>

        </div>
    </div>

<!-- Script -->
<script>
    function retourEleve() {
        window.location.href = "eleve.php";
    }
</script>

<?php
include "../includes/footer.php";
?>
