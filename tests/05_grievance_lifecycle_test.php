<?php
// Test 5: Test grievance insert, status update, response remark, and cleanup
return function (PDO $pdo) {
    $testId = 'TEST-2026-9999';
    $studentId = $pdo->query("SELECT id FROM users WHERE role = 'Student' LIMIT 1")->fetchColumn();
    $adminId   = $pdo->query("SELECT id FROM users WHERE role = 'Main Admin' LIMIT 1")->fetchColumn();

    if (!$studentId || !$adminId) {
        throw new RuntimeException('Need at least one student and one admin in users table.');
    }

    try {
        // 1. Insert test grievance
        $ins = $pdo->prepare(
            "INSERT INTO grievances (id, user_id, category, department, priority, subject, description, status, current_level)
             VALUES (?, ?, 'Academic', 'Engineering', 'Medium', 'Test Complaint', 'Automated test entry.', 'Pending', 'Department')"
        );
        $ins->execute([$testId, $studentId]);

        // 2. Update status and add remark
        $pdo->prepare("UPDATE grievances SET status = 'In Progress' WHERE id = ?")->execute([$testId]);
        $pdo->prepare(
            "INSERT INTO responses (grievance_id, admin_id, remark, status_change) VALUES (?, ?, 'Looking into this.', 'In Progress')"
        )->execute([$testId, $adminId]);

        // 3. Verify saved values
        $status = $pdo->prepare('SELECT status FROM grievances WHERE id = ?');
        $status->execute([$testId]);
        if ($status->fetchColumn() !== 'In Progress') {
            throw new RuntimeException('Grievance status update did not persist.');
        }

        $respCount = $pdo->prepare('SELECT COUNT(*) FROM responses WHERE grievance_id = ?');
        $respCount->execute([$testId]);
        if ((int)$respCount->fetchColumn() !== 1) {
            throw new RuntimeException('Response remark was not saved.');
        }
    } finally {
        // Clean up so demo data stays untouched
        $pdo->prepare('DELETE FROM grievances WHERE id = ?')->execute([$testId]);
    }
};
