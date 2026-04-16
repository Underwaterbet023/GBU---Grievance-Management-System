<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

requireAnyAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $uid = (int)$_POST['user_id'];
    
    // Safety: Don't allow an admin to delete their own account
    if ($uid === (int)$_SESSION['user_id']) {
        header("Location: ../admin_dashboard.php?tab=users&error=self_action");
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        // Delete dependent records first (grievances, responses, etc.)
        // This assumes a schema without cascade delete. If cascade is on, we only need to delete user.
        $pdo->prepare("DELETE FROM responses WHERE admin_id = ?")->execute([$uid]);
        $pdo->prepare("DELETE FROM responses WHERE grievance_id IN (SELECT id FROM grievances WHERE user_id = ?)")->execute([$uid]);
        $pdo->prepare("DELETE FROM grievances WHERE user_id = ?")->execute([$uid]);
        $pdo->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$uid]);
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
        
        $pdo->commit();
        header("Location: ../admin_dashboard.php?tab=users&success=user_deleted");
    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: ../admin_dashboard.php?tab=users&error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../admin_dashboard.php?tab=users");
}
exit;
?>