<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../includes/normalize.php";

protectPage("admin");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_ajouter_utilisateur.php");
    exit;
}

$nom = normalize_name($_POST["nom"] ?? "");
$prenom = normalize_name($_POST["prenom"] ?? "");
$login = normalize_login_input($_POST["login"] ?? "");
$password = $_POST["password"] ?? "";
$acreditation = trim($_POST["acreditation"] ?? "");

if ($nom === "" || $prenom === "" || $login === "" || $password === "" || $acreditation === "") {
    header("Location: admin_ajouter_utilisateur.php?error=1");
    exit;
}

// Normalize role values if user submits label text
$roleMap = [
    "admin" => "admin",
    "prof" => "prof",
    "eleve" => "eleve",
    "Admin" => "admin",
    "Prof" => "prof",
    "�l�ve" => "eleve",
    "Élève" => "eleve",
];
$acreditation = $roleMap[$acreditation] ?? $acreditation;

// Ensure login is unique
$stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE login = ?");
$stmt->execute([$login]);
if ($stmt->fetch()) {
    header("Location: admin_ajouter_utilisateur.php?error=1");
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$insert = $pdo->prepare(
    "INSERT INTO utilisateur (nom, prenom, login, password, acreditation)
     VALUES (?, ?, ?, ?, ?)"
);
$insert->execute([$nom, $prenom, $login, $hash, $acreditation]);

header("Location: admin_utilisateurs.php?success=1");
exit;
