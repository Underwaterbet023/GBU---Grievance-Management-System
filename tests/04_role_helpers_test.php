<?php
// Test 4: Verify role helper functions in includes/auth.php
return function (PDO $pdo) {
    require_once dirname(__DIR__) . '/includes/auth.php';

    $backupSession = $_SESSION ?? [];

    try {
        // Test Student session
        $_SESSION = ['user_id' => 1, 'role' => 'Student'];
        if (!isLoggedIn() || isAdmin() || !hasRole('Student')) {
            throw new RuntimeException('Student role check failed.');
        }

        // Test Admin session
        $_SESSION = ['user_id' => 5, 'role' => 'College Admin'];
        if (!isAdmin() || getDisplayRole('College Admin') !== 'Dean') {
            throw new RuntimeException('Admin role / display mapping check failed.');
        }
    } finally {
        $_SESSION = $backupSession;
    }
};
