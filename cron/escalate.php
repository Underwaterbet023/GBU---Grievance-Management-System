<?php
// cron/escalate.php
// Run this script daily via cron job
require_once dirname(__DIR__) . '/includes/db.php';

echo "Running Escalation Script...\n";

try {
    $pdo->beginTransaction();

    // Find Pending grievances older than 7 days at Department Level
    $stmtDept = $pdo->query("SELECT id, user_id FROM grievances WHERE status IN ('Pending', 'In Progress') AND current_level = 'Department' AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $deptGrievances = $stmtDept->fetchAll();

    foreach ($deptGrievances as $g) {
        $updateStmt = $pdo->prepare("UPDATE grievances SET current_level = 'College', priority = 'High' WHERE id = ?");
        $updateStmt->execute([$g['id']]);
        
        $notif = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif->execute([$g['user_id'], "Automated Update: Your grievance ({$g['id']}) has been escalated to College level as no action was taken at the Department level for 7 days."]);
        echo "Escalated {$g['id']} to College level.\n";
    }

    // Find Pending grievances older than 14 days at College Level
    $stmtCollege = $pdo->query("SELECT id, user_id FROM grievances WHERE status IN ('Pending', 'In Progress') AND current_level = 'College' AND created_at < DATE_SUB(NOW(), INTERVAL 14 DAY)");
    $collegeGrievances = $stmtCollege->fetchAll();

    foreach ($collegeGrievances as $g) {
        $updateCollegeStmt = $pdo->prepare("UPDATE grievances SET current_level = 'University', priority = 'Urgent' WHERE id = ?");
        $updateCollegeStmt->execute([$g['id']]);
        
        $notif = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notif->execute([$g['user_id'], "Automated Update: Your grievance ({$g['id']}) has been escalated to University level (Final Level) due to further delay."]);
        echo "Escalated {$g['id']} to University level.\n";
    }

    $pdo->commit();
    echo "Escalation completed successfully.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error during escalation: " . $e->getMessage() . "\n";
}
?>
