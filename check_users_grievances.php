<?php
require 'includes/db.php';
$stmt = $pdo->query("SELECT user_id, COUNT(*) as count FROM grievances GROUP BY user_id");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "User ID: " . $row['user_id'] . " | Count: " . $row['count'] . "\n";
}
echo "\nTotal users in system:\n";
$stmt = $pdo->query("SELECT id, name, email, roll_number FROM users");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Email: " . $row['email'] . " | Roll: " . $row['roll_number'] . "\n";
}
