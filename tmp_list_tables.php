<?php
// tmp_list_tables.php - read .env, connect to MySQL, list tables and columns
$envPath = __DIR__ . DIRECTORY_SEPARATOR . '.env';
if (!file_exists($envPath)) {
    echo "Error: .env file not found at $envPath\n";
    exit(1);
}
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$config = [];
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    list($k,$v) = explode('=', $line, 2);
    $k = trim($k);
    $v = trim($v);
    // remove surrounding quotes
    if ((substr($v,0,1) === '"' && substr($v,-1) === '"') || (substr($v,0,1) === "'" && substr($v,-1) === "'")) {
        $v = substr($v,1,-1);
    }
    $config[$k] = $v;
}
$dbHost = $config['DB_HOST'] ?? '127.0.0.1';
$dbPort = $config['DB_PORT'] ?? '3306';
$dbName = $config['DB_DATABASE'] ?? null;
$dbUser = $config['DB_USERNAME'] ?? null;
$dbPass = $config['DB_PASSWORD'] ?? null;
if (!$dbName || !$dbUser) {
    echo "Missing DB_DATABASE or DB_USERNAME in .env\n";
    exit(1);
}
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname=information_schema;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "DB connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
$sql = "SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA FROM COLUMNS WHERE TABLE_SCHEMA = :schema ORDER BY TABLE_NAME, ORDINAL_POSITION";
$stmt = $pdo->prepare($sql);
$stmt->execute([':schema' => $dbName]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) {
    echo "No columns found for database {$dbName}\n";
    exit(0);
}
$currentTable = null;
foreach ($rows as $r) {
    if ($currentTable !== $r['TABLE_NAME']) {
        $currentTable = $r['TABLE_NAME'];
        echo "\nTable: {$currentTable}\n";
        echo str_repeat('-', 60) . "\n";
        echo sprintf("%-30s %-20s %-8s %-10s %s\n", 'Column', 'Type', 'Nullable', 'Default', 'Extra');
        echo str_repeat('-', 60) . "\n";
    }
    $col = $r['COLUMN_NAME'];
    $type = $r['COLUMN_TYPE'];
    $nullable = $r['IS_NULLABLE'];
    $def = $r['COLUMN_DEFAULT'];
    $extra = $r['EXTRA'];
    echo sprintf("%-30s %-20s %-8s %-10s %s\n", $col, $type, $nullable, var_export($def, true), $extra);
}
echo "\nCompleted listing tables for database: {$dbName}\n";
