<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function requireLogin(): void {
    if (empty($_SESSION["role"])) {
        header("Location: /connexion.html");
        exit;
    }
}

function protectPage(string $requiredRole): void {
    requireLogin();

    $role = $_SESSION["role"];
    if ($role !== $requiredRole) {
        http_response_code(403);
        echo "Accès refusé.";
        exit;
    }
}

function logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    header("Location: /connexion.html");
    exit;
}
