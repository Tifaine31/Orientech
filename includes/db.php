<?php
// Simple PDO connection helper
$dbHost = "127.0.0.1";
$dbName = "projet";
$dbUser = "root";
$dbPass = "root";
$dbCharset = "utf8mb4";

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Erreur connexion base de données.";
    exit;
}
