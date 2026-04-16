<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT id, subject, status, created_at FROM grievances ORDER BY created_at DESC LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $row) {
    echo "ID: {$row['id']} | Subject: {$row['subject']} | Status: {$row['status']} | Created: {$row['created_at']}\n";
}
