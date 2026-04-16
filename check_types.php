<?php
require_once 'includes/db.php';

$tables = ['responses' => 'grievance_id', 'escalation_logs' => 'grievance_id', 'notifications' => 'grievance_id'];
foreach ($tables as $table => $col) {
    echo "--- $table ($col) ---\n";
    try {
        $stmt = $pdo->prepare("DESCRIBE $table $col");
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row) {
            echo "Type: {$row['Type']}\n";
        } else {
            echo "Column not found!\n";
        }
    } catch (Exception $e) {
        echo "Error or table missing: " . $e->getMessage() . "\n";
    }
}
?>
