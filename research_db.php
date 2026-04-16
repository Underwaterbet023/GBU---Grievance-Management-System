<?php
require_once 'includes/db.php';

function checkTable($pdo, $table) {
    echo "\n--- Table: $table ---\n";
    try {
        $res = $pdo->query("SHOW CREATE TABLE $table")->fetch();
        echo $res['Create Table'] . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

checkTable($pdo, 'grievances');
checkTable($pdo, 'responses');
checkTable($pdo, 'notifications');
checkTable($pdo, 'escalation_logs');
?>
