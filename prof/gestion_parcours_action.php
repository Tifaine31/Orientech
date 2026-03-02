<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../includes/normalize.php";

protectPage("prof");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: gestion_parcours.php");
    exit;
}

$nom = normalize_text($_POST["nom"] ?? "");
$niveau = normalize_text($_POST["niveau"] ?? "");
$commentaire = normalize_text($_POST["commentaire"] ?? "");

if ($nom === "" || !preg_match("/^[A-Za-z0-9][A-Za-z0-9 _-]{0,99}$/", $nom)) {
    header("Location: gestion_parcours.php?error=1");
    exit;
}

// Duplicate name (case-insensitive)
$chk = $pdo->prepare("SELECT numero_du_parcours FROM parcours WHERE LOWER(nom) = LOWER(?)");
$chk->execute([$nom]);
if ($chk->fetch()) {
    header("Location: gestion_parcours.php?error=1");
    exit;
}

$insert = $pdo->prepare(
    "INSERT INTO parcours (nom, nb_balises, niveau, commentaire)
     VALUES (?, 0, ?, ?)"
);
$insert->execute([$nom, $niveau !== "" ? $niveau : null, $commentaire !== "" ? $commentaire : null]);

header("Location: gestion_parcours.php");
exit;
