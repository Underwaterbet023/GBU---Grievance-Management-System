<?php
// includes/db.php
$configFile = dirname(__DIR__) . '/config/config.php';
$config = file_exists($configFile)
    ? require $configFile
    : [
        'db' => [
            'host' => 'localhost',
            'name' => 'gbu_grievance',
            'user' => 'root',
            'pass' => '',
            'charset' => 'utf8mb4',
        ],
    ];

$db = $config['db'];
$dsn = "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";

try {
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Database connection failed. Please check config/config.php: " . htmlspecialchars($e->getMessage()));
}
