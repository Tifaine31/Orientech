﻿?<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
protectPage("admin");

$pageTitle = "Espace administrateur";
$roleLabel = "Admin";

include "../includes/header.php";

$users = $pdo->query("SELECT id_utilisateur, nom, prenom, login, acreditation FROM utilisateur ORDER BY id_utilisateur DESC")->fetchAll();
?>

    <!-- Contenu -->
    <div class="container my-4">

        <div class="card shadow border-0 rounded-4 p-4 p-md-5">

            <!-- Titre + retour -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-orientech mb-0">Gestion des utilisateurs</h4>

                <button class="btn btn-outline-success rounded-pill" onclick="retourAdmin()">
                     ? Retour
                </button>
            </div>

            <!-- Ajouter -->
            <div class="mb-4">
                <button class="btn btn-orientech rounded-pill px-4"
                    onclick="location.href='admin_ajouter_utilisateur.php'">
                    + Ajouter un utilisateur
                </button>
            </div>

            <!-- Tableau utilisateurs -->
            <div class="table-responsive">
                <div id="alert-success" class="alert alert-success d-none">
                    Utilisateur créé avec succés
                </div>
                <div id="alert-update" class="alert alert-success d-none">
                    Utilisateur modifié avec succés
                </div>
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Login</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u["nom"]) ?></td>
            <td><?= htmlspecialchars($u["prenom"]) ?></td>
            <td><?= htmlspecialchars($u["login"]) ?></td>
            <td><?= htmlspecialchars($u["acreditation"] ?: "eleve") ?></td>
            <td><span class="badge bg-success">Actif</span></td>
            <td>
                <a class="btn btn-sm btn-outline-success rounded-pill me-2"
                   href="admin_modifier_utilisateur.php?id=<?= (int)$u["id_utilisateur"] ?>">
                    Modifier
                </a>
                <a class="btn btn-sm btn-outline-danger rounded-pill"
                   href="admin_supprimer_utilisateur.php?id=<?= (int)$u["id_utilisateur"] ?>"
                   onclick="return confirm('Supprimer définitivement cet utilisateur ?');">
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
        function retourAdmin() {
            window.location.href = "admin.php";
        }

        const params = new URLSearchParams(window.location.search);

        if (params.get("success") === "1") {
            document.getElementById("alert-success").classList.remove("d-none");
        }
        if (params.get("updated") === "1") {
            document.getElementById("alert-update").classList.remove("d-none");
        }
    </script>

<?php
include "../includes/footer.php";
?>

