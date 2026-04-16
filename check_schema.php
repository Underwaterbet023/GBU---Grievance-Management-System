<?php
require 'includes/db.php';
$tables = ['users', 'grievances', 'activity_log'];
foreach ($tables as $table) {
    echo "Table: $table\n";
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } catch (Exception $e) {
        echo "  MISSING OR ERROR: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
