<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

requireAnyAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $uid = (int)$_POST['user_id'];
    
    // Safety: Don't allow an admin to deactivate their own account
    if ($uid === (int)$_SESSION['user_id']) {
        header("Location: ../admin_dashboard.php?tab=users&error=self_action");
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$uid]);
        header("Location: ../admin_dashboard.php?tab=users&success=status_toggled");
    } catch (PDOException $e) {
        header("Location: ../admin_dashboard.php?tab=users&error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../admin_dashboard.php?tab=users");
}
exit;
?>