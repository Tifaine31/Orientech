ï»¿<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/db.php";

// Only accept POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /connexion.html");
    exit;
}

$login = trim($_POST["login"] ?? "");
$password = $_POST["password"] ?? "";

if ($login === "" || $password === "") {
    header("Location: /connexion.html?error=1");
    exit;
}

$stmt = $pdo->prepare("SELECT id_utilisateur, login, password, acreditation FROM utilisateur WHERE login = ?");
$stmt->execute([$login]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: /connexion.html?error=1");
    exit;
}

$storedHash = $user["password"];
$isValid = password_verify($password, $storedHash);

// Backward compatibility: if passwords were stored in plain text, allow once and rehash.
if (!$isValid && hash_equals($storedHash, $password)) {
    $isValid = true;
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upd = $pdo->prepare("UPDATE utilisateur SET password = ? WHERE id_utilisateur = ?");
    $upd->execute([$newHash, $user["id_utilisateur"]]);
}

if (!$isValid) {
    header("Location: /connexion.html?error=1");
    exit;
}

$_SESSION["role"] = $user["acreditation"] ?: "eleve";
$_SESSION["login"] = $user["login"];

switch ($_SESSION["role"]) {
    case "admin":
        header("Location: /admin/admin.php");
        break;
    case "prof":
        header("Location: /prof/prof.php");
        break;
    case "eleve":
        header("Location: /eleve/eleve.php");
        break;
    default:
        header("Location: /connexion.html?error=1");
        break;
}
exit;
?>

