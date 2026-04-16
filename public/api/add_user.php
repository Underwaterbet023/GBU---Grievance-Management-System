<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

requireAnyAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'Student';
    $dept = $_POST['department'] ?? null;
    $pass = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($pass)) {
        header("Location: ../admin_dashboard.php?tab=users&error=missing_fields");
        exit;
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, department, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $hash, $role, $dept]);
        header("Location: ../admin_dashboard.php?tab=users&success=user_added");
    } catch (PDOException $e) {
        header("Location: ../admin_dashboard.php?tab=users&error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../admin_dashboard.php?tab=users");
}
exit;
?>