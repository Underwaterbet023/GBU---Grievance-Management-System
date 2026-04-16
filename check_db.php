<?php
require_once 'includes/db.php';
echo "Grievances table structure:\n";
$stmt = $pdo->query("DESCRIBE grievances");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "\nResponses table structure:\n";
$stmt = $pdo->query("DESCRIBE responses");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
