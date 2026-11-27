<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

requireAnyAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'Student';
    $dept = !empty($_POST['department']) ? trim($_POST['department']) : null;
    $roll = !empty($_POST['roll_number']) ? trim($_POST['roll_number']) : null;
    $hostel = !empty($_POST['hostel']) ? trim($_POST['hostel']) : null;
    $pass = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($pass)) {
        header("Location: ../admin_dashboard.php?tab=users&error=missing_fields");
        exit;
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);

    try {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            header("Location: ../admin_dashboard.php?tab=users&error=email_exists");
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO users (roll_number, name, email, password_hash, role, department, hostel, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$roll, $name, $email, $hash, $role, $dept, $hostel]);
        header("Location: ../admin_dashboard.php?tab=users&success=user_added");
    } catch (PDOException $e) {
        header("Location: ../admin_dashboard.php?tab=users&error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../admin_dashboard.php?tab=users");
}
exit;
?>