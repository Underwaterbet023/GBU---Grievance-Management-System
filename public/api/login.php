<?php
header("Content-Type: application/json");
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

$data = json_decode(file_get_contents("php://input"));

if(!isset($data->identifier) || !isset($data->password)) {
    echo json_encode(['success' => false, 'message' => 'Please provide email/roll number and password.']);
    exit();
}

$identifier = trim($data->identifier);
$password = $data->password;

try {
    // Check if identifier is an email or roll number
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role, department, hostel, is_active FROM users WHERE email = :email OR roll_number = :roll LIMIT 1");
    $stmt->execute(['email' => $identifier, 'roll' => $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        if (isset($user['is_active']) && (int)$user['is_active'] === 0) {
            echo json_encode(['success' => false, 'message' => 'Your account has been deactivated. Contact the administrator.']);
            exit();
        }

        // Update last login timestamp
        $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?")->execute([$user['id']]);

        // Correct auth, set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['department'] = $user['department'];
        $_SESSION['hostel'] = $user['hostel'];
        
        $redirect = 'student_dashboard.php';
        if ($user['role'] !== 'Student') {
            $redirect = 'admin_dashboard.php';
        }

        echo json_encode(['success' => true, 'redirect' => $redirect]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>
