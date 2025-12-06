<?php
// scripts/list_user_columns.php
require __DIR__ . '/../vendor/autoload.php';

$dotenvPath = __DIR__ . '/../.env';
if (!file_exists($dotenvPath)) {
    echo "No .env found at $dotenvPath\n";
    exit(1);
}
// Simple parser for .env (we won't install vlucas/phpdotenv to avoid deps)
$env = [];
$lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
}
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db   = $env['DB_DATABASE'] ?? null;
$user = $env['DB_USERNAME'] ?? null;
$pass = $env['DB_PASSWORD'] ?? null;

if (!$db || !$user) {
    echo "Database config not found in .env\n";
    exit(1);
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "DB connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo "No columns found or users table does not exist.\n";
        exit(1);
    }
    echo "Field\tType\tNull\tKey\tDefault\tExtra\n";
    foreach ($rows as $r) {
        echo $r['Field'] . "\t" . $r['Type'] . "\t" . $r['Null'] . "\t" . ($r['Key'] ?? '') . "\t" . ($r['Default'] ?? '') . "\t" . ($r['Extra'] ?? '') . "\n";
    }
} catch (Exception $e) {
    echo "Query failed: " . $e->getMessage() . "\n";
    exit(1);
}

