<?php
require_once "../includes/auth.php";
protectPage("admin");

$pageTitle = "Espace administrateur";
$roleLabel = "Admin";

include "../includes/header.php";
?>

<!-- Contenu -->
<div class="container my-4">

<div class="card shadow border-0 rounded-4 p-4 p-md-5">

    <!-- Titre + retour -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-orientech mb-0">Journaux système</h4>

        <button class="btn btn-outline-success rounded-pill"
                onclick="retourAdmin()">
            ← Retour
        </button>
    </div>

    <!-- Filtres -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label fw-bold">Utilisateur</label>
            <select class="form-select rounded-pill">
                <option>Tous</option>
                <option>admin1</option>
                <option>prof1</option>
                <option>eleve1</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Type d’action</label>
            <select class="form-select rounded-pill">
                <option>Toutes</option>
                <option>Connexion</option>
                <option>Création utilisateur</option>
                <option>Modification</option>
                <option>Suppression</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Date</label>
            <input type="date" class="form-control rounded-pill">
        </div>
    </div>

    <!-- Tableau logs -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-secondary">
                <tr>
                    <th>Date & heure</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Détails</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>12/02/2025 10:22</td>
                    <td>admin1</td>
                    <td>Création utilisateur</td>
                    <td>Compte eleve12 créé</td>
                </tr>

                <tr>
                    <td>12/02/2025 10:15</td>
                    <td>admin1</td>
                    <td>Connexion</td>
                    <td>Connexion réussie</td>
                </tr>

                <tr>
                    <td>11/02/2025 16:40</td>
                    <td>prof1</td>
                    <td>Saisie de notes</td>
                    <td>Séance classe 95</td>
                </tr>

                <tr>
                    <td>11/02/2025 16:10</td>
                    <td>prof1</td>
                    <td>Clôture séance</td>
                    <td>Séance du 11/02/2025</td>
                </tr>

                <tr>
                    <td>10/02/2025 09:55</td>
                    <td>eleve1</td>
                    <td>Connexion</td>
                    <td>Connexion réussie</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
</div>

<!-- Scripts -->
<script>
function retourAdmin() {
    window.location.href = "admin.php";
}
</script>

<?php
include "../includes/footer.php";
?>
