<?php
$mysqli = new mysqli('localhost', 'root', '', 'web_programming_2');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$result = $mysqli->query('SELECT id, reference_code, amount_sent, fee, amount_received, total_paid, from_currency_id, to_currency_id, payout_method, status FROM transactions ORDER BY created_at DESC LIMIT 5');

echo "=== Latest 5 Transactions ===\n";
while ($row = $result->fetch_assoc()) {
    echo json_encode($row, JSON_PRETTY_PRINT) . "\n\n";
}

$mysqli->close();
?>
