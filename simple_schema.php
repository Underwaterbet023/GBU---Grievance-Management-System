<?php
require 'includes/db.php';
$stmt = $pdo->query("DESCRIBE grievances");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
