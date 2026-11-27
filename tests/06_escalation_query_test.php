<?php
// Test 6: Verify escalation query runs cleanly with valid ENUM values
return function (PDO $pdo) {
    $stmt = $pdo->query(
        "SELECT id, priority, status, current_level
         FROM grievances
         WHERE status IN ('Pending', 'In Progress')
           AND (DATEDIFF(NOW(), created_at) > 5 OR priority IN ('High', 'Urgent'))"
    );
    $rows = $stmt->fetchAll();

    if (!is_array($rows)) {
        throw new RuntimeException('Escalation query did not return an array.');
    }
};
