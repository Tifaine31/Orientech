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

        <h4 class="fw-bold text-orientech mb-4">Administration</h4>

        <div class="row g-4 justify-content-center">

            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn"
                        onclick="location.href='admin_utilisateurs.php'">
                    Gérer utilisateurs
                </button>
            </div>

            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn"
                        onclick="location.href='admin_logs.php'">
                    Journaux système
                </button>
            </div>

        </div>

    </div>
</div>

<?php
include "../includes/footer.php";
?>
