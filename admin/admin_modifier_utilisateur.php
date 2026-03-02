?<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
protectPage("admin");

$pageTitle = "Espace administrateur";
$roleLabel = "Admin";

include "../includes/header.php";

$id = (int)($_GET["id"] ?? 0);
$stmt = $pdo->prepare("SELECT id_utilisateur, nom, prenom, login, acreditation FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: admin_utilisateurs.php");
    exit;
}
?>


<!-- Contenu -->
<div class="container my-4">

<div class="card shadow border-0 rounded-4 p-4 p-md-5">

    <!-- Titre + retour -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-orientech mb-0">Modifier un utilisateur</h4>

        <button class="btn btn-outline-success rounded-pill"
                onclick="retourUtilisateurs()">
            ← Retour
        </button>
    </div>

    <!-- Formulaire -->
    <form method="post" action="admin_modifier_utilisateur_action.php">
        <input type="hidden" name="id_utilisateur" value="<?= (int)$user["id_utilisateur"] ?>">

        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label fw-bold">Nom</label>
                <input type="text"
                       name="nom"
                       class="form-control rounded-pill"
                       value="<?= htmlspecialchars($user["nom"]) ?>"
                       required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Prénom</label>
                <input type="text"
                       name="prenom"
                       class="form-control rounded-pill"
                       value="<?= htmlspecialchars($user["prenom"]) ?>"
                       required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Login</label>
                <input type="text"
                       name="login"
                       class="form-control rounded-pill"
                       value="<?= htmlspecialchars($user["login"]) ?>"
                       required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Rôle</label>
                <select name="acreditation" class="form-select rounded-pill" required>
    <option value="admin" <?= $user["acreditation"] === "admin" ? "selected" : "" ?>>Admin</option>
    <option value="prof" <?= $user["acreditation"] === "prof" ? "selected" : "" ?>>Prof</option>
    <option value="eleve" <?= $user["acreditation"] === "eleve" ? "selected" : "" ?>>Élève</option>
</select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Statut du compte</label>
                <select class="form-select rounded-pill">
                    <option selected>Actif</option>
                    <option>Inactif</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Réinitialiser le mot de passe</label>
                <input type="password"
                       name="password"
                       class="form-control rounded-pill"
                       placeholder="Nouveau mot de passe (optionnel)">
            </div>

        </div>

        <!-- Boutons -->
        <div class="text-center mt-5">
            <button type="submit"
                    class="btn btn-orientech rounded-pill px-5 me-3">
                Enregistrer les modifications
            </button>

            <button type="button"
                    class="btn btn-outline-success rounded-pill px-5"
                    onclick="retourUtilisateurs()">
                Annuler
            </button>
        </div>

    </form>

</div>
</div>

<!-- Scripts -->
<script>
function retourUtilisateurs() {
    window.location.href = "admin_utilisateurs.php";
}

const params = new URLSearchParams(window.location.search);
if (params.get("error") === "1") {
    alert("Erreur: login déjà utilisé ou champs invalides.");
}
</script>

<?php
include "../includes/footer.php";
?>

