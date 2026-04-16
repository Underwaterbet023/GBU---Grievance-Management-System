<?php
require_once 'includes/db.php';

$users = [
    [
        'name' => 'Dean',
        'email' => 'Dean55gbu@gbu.ac.in',
        'password' => 'dean@prototypegbu',
        'role' => 'College Admin'
    ],
    [
        'name' => 'Registrar',
        'email' => 'Registar25gbu@gbu.ac.in',
        'password' => 'Registar25@prototypegbu',
        'role' => 'University Admin'
    ],
    [
        'name' => 'Main Admin',
        'email' => 'admin5525gbu@gbu.ac.in',
        'password' => 'admin25@prototypegbu',
        'role' => 'Main Admin'
    ]
];

foreach ($users as $u) {
    $hash = password_hash($u['password'], PASSWORD_BCRYPT);
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$u['email']]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update
        $stmt = $pdo->prepare("UPDATE users SET name = ?, password_hash = ?, role = ?, is_active = 1 WHERE email = ?");
        $stmt->execute([$u['name'], $hash, $u['role'], $u['email']]);
        echo "Updated user: " . $u['email'] . "\n";
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$u['name'], $u['email'], $hash, $u['role']]);
        echo "Created user: " . $u['email'] . "\n";
    }
}
?>