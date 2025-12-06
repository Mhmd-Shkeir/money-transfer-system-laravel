<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=web2_project', 'root', '123456789');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE reference_code = ? LIMIT 1");
    $stmt->execute(['Y0R11FVCYX']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "Transaction found:\n";
        foreach ($row as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "Transaction Y0R11FVCYX not found\n";
        // Let's see recent transactions
        $stmt = $pdo->query("SELECT id, reference_code, amount_sent, fee, payout_method, created_at FROM transactions ORDER BY created_at DESC LIMIT 5");
        echo "\nRecent transactions:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode($row) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
