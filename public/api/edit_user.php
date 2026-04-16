<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

requireAnyAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $uid = (int)$_POST['user_id'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'Student';
    $dept = $_POST['department'] ?? null;
    $pass = $_POST['password'] ?? '';

    if (empty($name) || empty($email)) {
        header("Location: ../admin_dashboard.php?tab=users&error=missing_fields");
        exit;
    }

    try {
        if (!empty($pass)) {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, department=?, password_hash=? WHERE id=?");
            $stmt->execute([$name, $email, $role, $dept, $hash, $uid]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, department=? WHERE id=?");
            $stmt->execute([$name, $email, $role, $dept, $uid]);
        }
        header("Location: ../admin_dashboard.php?tab=users&success=user_updated");
    } catch (PDOException $e) {
        header("Location: ../admin_dashboard.php?tab=users&error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../admin_dashboard.php?tab=users");
}
exit;
?>