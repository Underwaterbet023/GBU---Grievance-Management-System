<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

if (!isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grievance_id'])) {
    $gid = trim($_POST['grievance_id']);
    $level = $_POST['level'] ?? 'College';
    
    $dbLevel = $level === 'Final' ? 'University' : $level; 
    $designation = $level === 'Final' ? 'Main Admin' : ($level === 'University' ? 'Registrar' : 'Dean');

    try {
        $pdo->beginTransaction();

        $getGrievance = $pdo->prepare("SELECT user_id, current_level FROM grievances WHERE id = ?");
        $getGrievance->execute([$gid]);
        $grievance = $getGrievance->fetch();

        // 1. Update current level
        $stmt = $pdo->prepare("UPDATE grievances SET current_level = ?, status = 'In Progress', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$dbLevel, $gid]);

        // 2. Record escalation log when moving up tiers
        if ($grievance && in_array($grievance['current_level'], ['Department', 'College'], true) && in_array($dbLevel, ['College', 'University'], true) && $grievance['current_level'] !== $dbLevel) {
            $logStmt = $pdo->prepare("INSERT INTO escalation_logs (grievance_id, escalated_from, escalated_to) VALUES (?, ?, ?)");
            $logStmt->execute([$gid, $grievance['current_level'], $dbLevel]);
        }

        // 3. Add to responses/audit trail
        $remark = "Grievance shifted to $designation for higher-level intervention.";
        $resp = $pdo->prepare("INSERT INTO responses (grievance_id, admin_id, remark, status_change, created_at) VALUES (?, ?, ?, 'In Progress', NOW())");
        $resp->execute([$gid, $_SESSION['user_id'], $remark]);

        // 4. Notify student
        if ($grievance) {
            $notif = $pdo->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
            $msg = "Your grievance (#$gid) has been shifted to $designation for review.";
            $notif->execute([$grievance['user_id'], $msg]);
        }

        $pdo->commit();
        redirectBackWithParam('../admin_dashboard.php', 'success', 'shifted');

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Database Error: " . htmlspecialchars($e->getMessage()));
    }
}
?>