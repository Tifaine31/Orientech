<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/auth.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/db.php";

protectPage("eleve");

$login = $_SESSION["login"] ?? "";
if ($login === "") {
    header("Location: eleve.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id_utilisateur, password, photo_profil FROM utilisateur WHERE login = ? AND acreditation = 'eleve'");
$stmt->execute([$login]);
$eleve = $stmt->fetch();

if (!$eleve) {
    header("Location: eleve.php");
    exit;
}

$current = $_POST["current_password"] ?? "";
$new = $_POST["new_password"] ?? "";
$confirm = $_POST["confirm_password"] ?? "";

$changePassword = ($current !== "" || $new !== "" || $confirm !== "");
if ($changePassword) {
    if ($current === "" || $new === "" || $confirm === "" || $new !== $confirm) {
        header("Location: modifier_profil.php?error=1");
        exit;
    }

    if (!password_verify($current, $eleve["password"])) {
        header("Location: modifier_profil.php?error=1");
        exit;
    }
}

$photoPath = $eleve["photo_profil"];
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {
    $tmp = $_FILES["photo"]["tmp_name"];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $allowed = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp"];
    if (!isset($allowed[$mime])) {
        header("Location: modifier_profil.php?error=1");
        exit;
    }

    $ext = $allowed[$mime];
    $dir = $_SERVER["DOCUMENT_ROOT"] . "/uploads/profiles";
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filename = "eleve_" . $eleve["id_utilisateur"] . "_" . time() . "." . $ext;
    $dest = $dir . "/" . $filename;
    if (move_uploaded_file($tmp, $dest)) {
        $photoPath = "/uploads/profiles/" . $filename;
    }
}

$newHash = $eleve["password"];
if ($changePassword) {
    $newHash = password_hash($new, PASSWORD_DEFAULT);
}

$upd = $pdo->prepare("UPDATE utilisateur SET password = ?, photo_profil = ? WHERE id_utilisateur = ?");
$upd->execute([$newHash, $photoPath, $eleve["id_utilisateur"]]);

header("Location: modifier_profil.php?success=1");
exit;
