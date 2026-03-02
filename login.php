<?php
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

// Support both legacy and current schema variants for "utilisateur".
$columns = $pdo->query("SHOW COLUMNS FROM utilisateur")->fetchAll(PDO::FETCH_COLUMN, 0);
$idCol = in_array("id_utilisateur", $columns, true) ? "id_utilisateur" : "id";
$passwordCol = in_array("password", $columns, true) ? "password" : "mdp";
$roleCol = in_array("acreditation", $columns, true) ? "acreditation" : "role";

$sql = sprintf(
    "SELECT `%s` AS user_id, login, `%s` AS user_password, `%s` AS user_role FROM utilisateur WHERE login = ?",
    $idCol,
    $passwordCol,
    $roleCol
);

$stmt = $pdo->prepare($sql);
$stmt->execute([$login]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: /connexion.html?error=1");
    exit;
}

$storedHash = (string)($user["user_password"] ?? "");
$isValid = password_verify($password, $storedHash);

// Backward compatibility: if passwords were stored in plain text, allow once and rehash.
if (!$isValid && hash_equals($storedHash, $password)) {
    $isValid = true;
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $sqlUpdate = sprintf("UPDATE utilisateur SET `%s` = ? WHERE `%s` = ?", $passwordCol, $idCol);
    $upd = $pdo->prepare($sqlUpdate);
    $upd->execute([$newHash, $user["user_id"]]);
}

if (!$isValid) {
    header("Location: /connexion.html?error=1");
    exit;
}

$_SESSION["role"] = $user["user_role"] ?: "eleve";
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
