<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/auth.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/db.php";

protectPage("eleve");

$login = $_SESSION["login"] ?? "";
$eleve = null;
if ($login !== "") {
    $stmt = $pdo->prepare(
        "SELECT id_utilisateur, nom, prenom, login, photo_profil, password
         FROM utilisateur
         WHERE login = ? AND acreditation = 'eleve'"
    );
    $stmt->execute([$login]);
    $eleve = $stmt->fetch();
}

if (!$eleve) {
    header("Location: eleve.php");
    exit;
}

$pageTitle = "Mon profil";
$roleLabel = "Élève";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/header.php";
?>

<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Modifier mon profil</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='eleve.php'">
                ← Retour
            </button>
        </div>

        <?php if (isset($_GET["success"])): ?>
            <div class="alert alert-success">Profil mis à jour.</div>
        <?php elseif (isset($_GET["error"])): ?>
            <div class="alert alert-danger">Erreur : vérifie le mot de passe actuel et le format du fichier.</div>
        <?php endif; ?>

        <form method="post" action="modifier_profil_action.php" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Photo de profil</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <?php if (!empty($eleve["photo_profil"])): ?>
                        <img src="<?= htmlspecialchars($eleve["photo_profil"]) ?>"
                             alt="Photo actuelle"
                             style="width:90px;height:90px;border-radius:50%;object-fit:cover;margin-top:10px;">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-orientech rounded-pill px-5" type="submit">
                    Enregistrer
                </button>
            </div>
        </form>

    </div>
</div>

<?php
include "../includes/footer.php";
?>
