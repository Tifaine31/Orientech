<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: gestion_seances.php");
    exit;
}

$idSeance = (int)($_POST["id_seance"] ?? 0);
$idParcours = (int)($_POST["id_parcours"] ?? 0);
$nbValides = (int)($_POST["nb_valides"] ?? 0);
$nbInvalides = (int)($_POST["nb_invalides"] ?? 0);
$heureDebut = trim($_POST["heure_debut"] ?? "");
$heureFin = trim($_POST["heure_fin"] ?? "");
$duree = trim($_POST["duree"] ?? "");
$useAuto = isset($_POST["use_auto"]) ? 1 : 0;
$noteManuelle = $_POST["note_manuelle"] ?? null;

if ($idSeance <= 0 || $idParcours <= 0) {
    header("Location: seance_detail.php?id=" . $idSeance);
    exit;
}

// Get nb_balises for the parcours
$stmt = $pdo->prepare(
    "SELECT COUNT(c.numerodecarte) AS nb
     FROM composer c
     WHERE c.numero_du_parcours = ?"
);
$stmt->execute([$idParcours]);
$nbBalises = (int)($stmt->fetch()["nb"] ?? 0);

$noteAuto = 0.0;
if ($nbBalises > 0) {
    $noteAuto = round(($nbValides / $nbBalises) * 20, 1);
}

$noteFinale = $useAuto ? $noteAuto : (float)($noteManuelle ?? 0);

$ins = $pdo->prepare(
    "INSERT INTO seance_resultat
     (id_seance, id_parcours, nb_valides, nb_invalides, note_auto, note_finale, use_auto, heure_debut, heure_fin, duree)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$ins->execute([
    $idSeance,
    $idParcours,
    $nbValides,
    $nbInvalides,
    $noteAuto,
    $noteFinale,
    $useAuto,
    $heureDebut !== "" ? $heureDebut : null,
    $heureFin !== "" ? $heureFin : null,
    $duree !== "" ? $duree : null
]);

header("Location: seance_detail.php?id=" . $idSeance);
exit;
