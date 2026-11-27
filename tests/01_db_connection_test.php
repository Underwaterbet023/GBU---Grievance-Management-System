<?php
// Test 1: Check database connection works
return function (PDO $pdo) {
    $val = $pdo->query('SELECT 1')->fetchColumn();
    if ((int)$val !== 1) {
        throw new RuntimeException('Database ping failed.');
    }
};
