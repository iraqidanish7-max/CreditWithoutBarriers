<?php
// logout.php — destroy session and redirect to homepage
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// clear session variables
$_SESSION = [];

// destroy the session cookie (if set)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// finally destroy server-side session
session_destroy();

// redirect to homepage (index.php)
header('Location: index.php');
exit;