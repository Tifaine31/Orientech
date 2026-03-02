<script src="../js/auth.js"></script>
<script>
    protectPage("prof");
</script>


<?php
require_once "../includes/auth.php";
protectPage("prof");

$pageTitle = "Espace professeur";
$roleLabel = "Professeur";

include "../includes/header.php";
?>


<!-- Contenu -->
<div class="container my-4">

    <div class="card shadow border-0 rounded-4 p-4 p-md-5">

        <h4 class="fw-bold text-orientech mb-4">Tableau de bord</h4>

        <div class="row g-4 justify-content-center">

            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn"
                        onclick="location.href='gestion_classes.php'">
                    Gérer Classes
                </button>
            </div>

            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn"
                        onclick="location.href='gestion_eleves.php'">
                    Gérer Élèves
                </button>
            </div>

            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn"
                        onclick="location.href='gestion_parcours.php'">
                    Gérer Parcours
                </button>
            </div>

            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn"
                        onclick="location.href='gestion_seances.php'">
                    Gérer Séances
                </button>
            </div>

            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn"
                        onclick="location.href='historique_seances.php'">
                    Historique Séances
                </button>
            </div>

            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn"
                        onclick="location.href='gestion_boitier.php'">
                    Gérer Boîtiers
                </button>
            </div>

        </div>

    </div>
</div>

<?php
include "../includes/footer.php";
?>
