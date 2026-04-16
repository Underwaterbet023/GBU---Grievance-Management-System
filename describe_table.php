<?php
require 'includes/db.php';
$stmt = $pdo->query("DESCRIBE grievances");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($columns, JSON_PRETTY_PRINT);
