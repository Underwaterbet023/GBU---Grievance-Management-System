<?php
require_once 'includes/db.php';

echo "Restoring Admin Credentials...\n";

$email = 'admin@gbu.ac.in';
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Update password and role to ensure it's Main Admin
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, role = 'Main Admin' WHERE email = ?");
        $stmt->execute([$hash, $email]);
        echo "Admin password updated to 'password123'.\n";
    } else {
        // Insert new
        $stmt = $pdo->prepare("INSERT INTO users (name, roll_number, email, password_hash, role) VALUES ('Super Admin', 'ADMIN001', ?, ?, 'Main Admin')");
        $stmt->execute([$email, $hash]);
        echo "Admin user created with password 'password123'.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
