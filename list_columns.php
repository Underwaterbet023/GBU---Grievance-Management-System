<?php
require_once 'includes/db.php';

$tables = ['grievances', 'responses', 'notifications', 'escalation_logs', 'users'];
foreach ($tables as $table) {
    echo "\n--- Columns for $table ---\n";
    try {
        $res = $pdo->query("DESCRIBE $table")->fetchAll();
        foreach ($res as $row) {
            echo "{$row['Field']} ({$row['Type']}) - Null: {$row['Null']}, Key: {$row['Key']}, Default: {$row['Default']}, Extra: {$row['Extra']}\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
