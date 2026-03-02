<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

$id = (int)($_GET["id"] ?? 0);
if ($id > 0) {
    // Delete linked balises first
    $delLinks = $pdo->prepare("DELETE FROM composer WHERE numero_du_parcours = ?");
    $delLinks->execute([$id]);

    $del = $pdo->prepare("DELETE FROM parcours WHERE numero_du_parcours = ?");
    $del->execute([$id]);
}

header("Location: gestion_parcours.php");
exit;
