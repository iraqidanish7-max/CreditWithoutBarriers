<?php
// connection.php - unified DB connection loader
$config = [];
if (file_exists(__DIR__ . '/config.php')) {
    $config = include __DIR__ . '/config.php';
} elseif (file_exists(__DIR__ . '/config.sample.php')) {
    $config = include __DIR__ . '/config.sample.php';
} else {
    $config = [
        'db_host' => 'localhost',
        'db_name' => 'database1',
        'db_user' => 'root',
        'db_pass' => '',
        'db_port' => 3306,
        'app_debug' => true,
    ];
}

$host = $config['db_host'] ?? 'localhost';
$user = $config['db_user'] ?? 'root';
$pass = $config['db_pass'] ?? '';
$db   = $config['db_name'] ?? '';
$port = $config['db_port'] ?? 3306;

$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    error_log("DB connect error ({$mysqli->connect_errno}): {$mysqli->connect_error}");
    if (!empty($config['app_debug'])) {
        die("Database connection failed: " . htmlspecialchars($mysqli->connect_error));
    } else {
        die("Database connection failed.");
    }
}
$mysqli->set_charset('utf8mb4');
// backward-compatibility variable
$conn = $mysqli;