<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: gestion_seances.php");
    exit;
}

$idResultat = (int)($_POST["id_resultat"] ?? 0);
$idSeance = (int)($_POST["id_seance"] ?? 0);
$useAuto = isset($_POST["use_auto"]) ? 1 : 0;
$noteManuelle = $_POST["note_manuelle"] ?? null;

if ($idResultat <= 0 || $idSeance <= 0) {
    header("Location: seance_detail.php?id=" . $idSeance);
    exit;
}

// Load note_auto
$stmt = $pdo->prepare("SELECT note_auto FROM seance_resultat WHERE id_resultat = ? AND id_seance = ?");
$stmt->execute([$idResultat, $idSeance]);
$row = $stmt->fetch();
if (!$row) {
    header("Location: seance_detail.php?id=" . $idSeance);
    exit;
}

$noteFinale = $useAuto ? (float)$row["note_auto"] : (float)($noteManuelle ?? 0);

$upd = $pdo->prepare("UPDATE seance_resultat SET use_auto = ?, note_finale = ? WHERE id_resultat = ?");
$upd->execute([$useAuto, $noteFinale, $idResultat]);

header("Location: seance_detail.php?id=" . $idSeance);
exit;
