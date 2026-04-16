<?php
require_once 'includes/db.php';

echo "Database connection successful!\n";

// Check tables
$tables = ['users', 'grievances', 'responses'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        echo "Table '$table' exists.\n";
    } catch (PDOException $e) {
        echo "Table '$table' MISSING!\n";
    }
}

// Check admin user
$stmt = $pdo->prepare("SELECT id, name, role FROM users WHERE email = ?");
$stmt->execute(['admin@gbu.ac.in']);
$admin = $stmt->fetch();

if ($admin) {
    echo "Admin user found: " . $admin['name'] . " (" . $admin['role'] . ")\n";
    if ($admin['role'] !== 'Main Admin') {
        echo "Correcting role to 'Main Admin'...\n";
        $pdo->prepare("UPDATE users SET role = 'Main Admin' WHERE id = ?")->execute([$admin['id']]);
        echo "Role updated.\n";
    }
} else {
    echo "Admin user NOT FOUND. Attempting to insert...\n";
    $pwd = password_hash('password123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, roll_number, email, password_hash, role) VALUES ('Main Admin', 'ADMIN001', 'admin@gbu.ac.in', ?, 'Main Admin')");
    if ($stmt->execute([$pwd])) {
        echo "Admin user successfully inserted with password 'password123'.\n";
    } else {
        echo "Failed to insert admin user.\n";
    }
}
?>
