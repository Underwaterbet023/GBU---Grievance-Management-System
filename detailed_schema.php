<?php
require 'includes/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM grievances");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " | " . $row['Type'] . "\n";
}
