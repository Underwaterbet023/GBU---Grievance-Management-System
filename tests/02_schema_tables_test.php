<?php
// Test 2: Check all required database tables exist
return function (PDO $pdo) {
    $requiredTables = [
        'users',
        'grievances',
        'responses',
        'notifications',
        'escalation_logs',
        'activity_logs',
    ];

    $existing = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

    foreach ($requiredTables as $table) {
        if (!in_array($table, $existing, true)) {
            throw new RuntimeException("Missing table: $table");
        }
    }
};
