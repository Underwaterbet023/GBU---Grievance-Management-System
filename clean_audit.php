<?php
require 'includes/db.php';
echo "--- ALL GRIEVANCES ---\n";
$stmt = $pdo->query("SELECT id, user_id, subject, status FROM grievances");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | User: {$row['user_id']} | Subject: {$row['subject']} | Status: {$row['status']}\n";
}
echo "\n--- ALL USERS ---\n";
$stmt = $pdo->query("SELECT id, name, role FROM users");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Name: {$row['name']} | Role: {$row['role']}\n";
}
