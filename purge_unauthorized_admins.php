<?php
require_once 'includes/db.php';

// Authorized emails
$authorizedEmails = [
    'Dean55gbu@gbu.ac.in',
    'Registar25gbu@gbu.ac.in',
    'admin5525gbu@gbu.ac.in'
];

// 1. Remove/Downgrade the specific old admin account
$oldAdminEmail = 'admin@gbu.ac.in';
$stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
$stmt->execute([$oldAdminEmail]);
echo "Attempted to delete old admin: $oldAdminEmail\n";

// 2. Ensure all other accounts are NOT admins unless they are in the authorized list
$placeholders = str_repeat('?,', count($authorizedEmails) - 1) . '?';
$stmt = $pdo->prepare("UPDATE users SET role = 'Student' WHERE role != 'Student' AND email NOT IN ($placeholders)");
$stmt->execute($authorizedEmails);
echo "Downgraded unauthorized admin accounts to 'Student'\n";

// 3. Re-verify the 3 authorized accounts (Seed them if missing)
$admins = [
    ['name' => 'Dean', 'email' => 'Dean55gbu@gbu.ac.in', 'pass' => 'dean@prototypegbu', 'role' => 'College Admin'],
    ['name' => 'Registrar', 'email' => 'Registar25gbu@gbu.ac.in', 'pass' => 'Registar25@prototypegbu', 'role' => 'University Admin'],
    ['name' => 'Main Admin', 'email' => 'admin5525gbu@gbu.ac.in', 'pass' => 'admin25@prototypegbu', 'role' => 'Main Admin']
];

foreach ($admins as $a) {
    $hash = password_hash($a['pass'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$a['email']]);
    if ($stmt->fetch()) {
        $upd = $pdo->prepare("UPDATE users SET role = ?, password_hash = ?, is_active = 1 WHERE email = ?");
        $upd->execute([$a['role'], $hash, $a['email']]);
        echo "Verified Admin: " . $a['email'] . "\n";
    } else {
        $ins = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        $ins->execute([$a['name'], $a['email'], $hash, $a['role']]);
        echo "Created Authorized Admin: " . $a['email'] . "\n";
    }
}
?>