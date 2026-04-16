<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

if (!isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grievance_id'])) {
    $gid = (int)$_POST['grievance_id'];
    $level = $_POST['level'] ?? 'College';
    
    // Map 'Final' to a high priority flag or special level if needed, 
    // but for now we follow the existing current_level logic.
    $dbLevel = $level === 'Final' ? 'University' : $level; 
    $designation = $level === 'Final' ? 'Main Admin' : ($level === 'University' ? 'Registrar' : 'Dean');

    try {
        $pdo->beginTransaction();

        // 1. Update current level
        $stmt = $pdo->prepare("UPDATE grievances SET current_level = ?, status = 'In Review', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$dbLevel, $gid]);

        // 2. Add to responses/audit trail
        $remark = "Grievance shifted to $designation for higher-level intervention.";
        $resp = $pdo->prepare("INSERT INTO responses (grievance_id, admin_id, response, created_at) VALUES (?, ?, ?, NOW())");
        $resp->execute([$gid, $_SESSION['user_id'], $remark]);

        // 3. Notify student
        $getStudent = $pdo->prepare("SELECT user_id FROM grievances WHERE id = ?");
        $getStudent->execute([$gid]);
        $studentId = $getStudent->fetchColumn();

        $notif = $pdo->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
        $msg = "Your grievance (#$gid) has been shifted to $designation for review.";
        $notif->execute([$studentId, $msg]);

        $pdo->commit();
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '../admin_dashboard.php';
        header("Location: $referer&success=shifted");
        exit;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Database Error: " . $e->getMessage());
    }
}
?>