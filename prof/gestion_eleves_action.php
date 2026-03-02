<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../includes/normalize.php";

protectPage("prof");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: gestion_eleves.php");
    exit;
}

$nom = normalize_name($_POST["nom"] ?? "");
$prenom = normalize_name($_POST["prenom"] ?? "");
$loginInput = trim($_POST["login"] ?? "");
$login = $loginInput !== "" ? normalize_login_input($loginInput) : normalize_login($nom, $prenom);
$password = $_POST["password"] ?? "";
$idClasse = trim($_POST["id_classe"] ?? "");

if ($nom === "" || $prenom === "" || $login === "" || $password === "") {
    header("Location: gestion_eleves.php?error=1");
    exit;
}

// unique login
$stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE login = ?");
$stmt->execute([$login]);
if ($stmt->fetch()) {
    header("Location: gestion_eleves.php?error=1");
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$insert = $pdo->prepare(
    "INSERT INTO utilisateur (nom, prenom, login, password, acreditation)
     VALUES (?, ?, ?, ?, 'eleve')"
);
$insert->execute([$nom, $prenom, $login, $hash]);

$newId = (int)$pdo->lastInsertId();
if ($idClasse !== '' && ctype_digit($idClasse)) {
    $link = $pdo->prepare(
        "INSERT INTO liaison_classe (id_utilisateur, id_classe, date_debut, date_fin)
         VALUES (?, ?, CURDATE(), NULL)"
    );
    $link->execute([$newId, (int)$idClasse]);
}

header("Location: gestion_eleves.php");
exit;
