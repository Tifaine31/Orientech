<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("prof");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: gestion_parcours.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);
$numerodecarte = (int)($_POST["numerodecarte"] ?? 0);

if ($id > 0 && $numerodecarte > 0) {
    $chk = $pdo->prepare("SELECT 1 FROM composer WHERE numero_du_parcours = ? AND numerodecarte = ?");
    $chk->execute([$id, $numerodecarte]);
    if (!$chk->fetch()) {
        $next = $pdo->prepare("SELECT COALESCE(MAX(ordre), 0) + 1 AS next_ordre FROM composer WHERE numero_du_parcours = ?");
        $next->execute([$id]);
        $nextOrdre = (int)($next->fetch()["next_ordre"] ?? 1);

        $ins = $pdo->prepare("INSERT INTO composer (numero_du_parcours, numerodecarte, ordre) VALUES (?, ?, ?)");
        $ins->execute([$id, $numerodecarte, $nextOrdre]);
    }
}

// Update nb_balises count
$upd = $pdo->prepare(
    "UPDATE parcours
     SET nb_balises = (SELECT COUNT(*) FROM composer WHERE numero_du_parcours = ?)
     WHERE numero_du_parcours = ?"
);
$upd->execute([$id, $id]);

header("Location: modifier_parcours.php?id=" . $id);
exit;
