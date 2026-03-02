<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

protectPage("admin");

$id = (int)($_GET["id"] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$id]);
}

header("Location: admin_utilisateurs.php");
exit;
