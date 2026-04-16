<?php
require_once __DIR__ . '/../includes/db.php';

try {
    echo "Starting migration...\n";

    // 1. Update users table
    // Check if hostel exists
    $cols = $pdo->query("SHOW COLUMNS FROM users LIKE 'hostel'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN hostel VARCHAR(100) DEFAULT NULL AFTER department");
        echo "  Added 'hostel' column to users.\n";
    }

    $cols = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_login_at'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN last_login_at DATETIME DEFAULT NULL");
        echo "  Added 'last_login_at' column to users.\n";
    }

    $cols = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_action_at'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN last_action_at DATETIME DEFAULT NULL");
        echo "  Added 'last_action_at' column to users.\n";
    }

    // 2. Update grievances table
    $cols = $pdo->query("SHOW COLUMNS FROM grievances LIKE 'hostel'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE grievances ADD COLUMN hostel VARCHAR(100) DEFAULT NULL AFTER department");
        echo "  Added 'hostel' column to grievances.\n";
    }

    // 3. Create activity_logs table
    $pdo->exec("CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action VARCHAR(255) NOT NULL,
        context TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id),
        INDEX (created_at)
    )");
    echo "  Ensured 'activity_logs' table exists.\n";

    echo "Migration completed successfully!\n";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
