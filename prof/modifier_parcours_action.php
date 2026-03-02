<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../includes/normalize.php";

protectPage("prof");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: gestion_parcours.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);
$nom = normalize_text($_POST["nom"] ?? "");
$niveau = normalize_text($_POST["niveau"] ?? "");
$commentaire = normalize_text($_POST["commentaire"] ?? "");

if ($id <= 0 || $nom === "" || !preg_match("/^[A-Za-z0-9][A-Za-z0-9 _-]{0,99}$/", $nom)) {
    header("Location: modifier_parcours.php?id={$id}&error=1");
    exit;
}

$chk = $pdo->prepare("SELECT numero_du_parcours FROM parcours WHERE LOWER(nom) = LOWER(?) AND numero_du_parcours <> ?");
$chk->execute([$nom, $id]);
if ($chk->fetch()) {
    header("Location: modifier_parcours.php?id={$id}&error=1");
    exit;
}

$upd = $pdo->prepare(
    "UPDATE parcours SET nom = ?, niveau = ?, commentaire = ? WHERE numero_du_parcours = ?"
);
$upd->execute([$nom, $niveau !== "" ? $niveau : null, $commentaire !== "" ? $commentaire : null, $id]);

header("Location: modifier_parcours.php?id={$id}");
exit;
