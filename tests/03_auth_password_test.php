<?php
// Test 3: Verify seed student & admin accounts exist and password_verify works
return function (PDO $pdo) {
    $accounts = ['aarav.sharma@gbu.ac.in', 'admin@gbu.ac.in'];

    $stmt = $pdo->prepare('SELECT email, password_hash, is_active FROM users WHERE email = ? LIMIT 1');

    foreach ($accounts as $email) {
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new RuntimeException("Seed account not found: $email");
        }
        if ((int)$user['is_active'] !== 1) {
            throw new RuntimeException("Seed account is inactive: $email");
        }
        if (!password_verify('password123', $user['password_hash'])) {
            throw new RuntimeException("Password verification failed for: $email");
        }
    }
};
