<?php
require_once 'includes/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "--- Table Schema Check ---\n";
$tables = ['users', 'grievances', 'activity_logs', 'activity_log'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        echo "Table '$table' exists. Columns:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  - " . $row['Field'] . "\n";
        }
    } catch (Exception $e) {
        echo "Table '$table' error: " . $e->getMessage() . "\n";
    }
}
?>
