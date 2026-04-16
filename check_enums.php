<?php
require 'includes/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM grievances LIKE 'status'");
$row = $stmt->fetch();
echo "Status Enum: " . $row['Type'] . "\n";

$stmt = $pdo->query("SHOW COLUMNS FROM grievances LIKE 'current_level'");
$row = $stmt->fetch();
echo "Current Level Enum: " . $row['Type'] . "\n";
