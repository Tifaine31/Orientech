<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

$idClasse = (int)($_GET["id_classe"] ?? 0);
$idUtilisateur = (int)($_GET["id_utilisateur"] ?? 0);

if ($idClasse > 0 && $idUtilisateur > 0) {
    $close = $pdo->prepare(
        "UPDATE liaison_classe
         SET date_fin = CURDATE()
         WHERE id_utilisateur = ? AND id_classe = ? AND date_fin IS NULL"
    );
    $close->execute([$idUtilisateur, $idClasse]);
}

header("Location: modifier_classe.php?id_classe=" . $idClasse);
exit;
