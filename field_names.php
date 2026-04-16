<?php
require 'includes/db.php';
$stmt = $pdo->query("DESCRIBE grievances");
$fields = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo implode("\n", $fields);
