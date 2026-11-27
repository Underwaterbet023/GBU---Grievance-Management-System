<?php
// Simple test runner for GBU Grievance Management System
// Run from project root: C:\xampp\php\php.exe tests\run_tests.php

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$testFiles = [
    __DIR__ . '/01_db_connection_test.php',
    __DIR__ . '/02_schema_tables_test.php',
    __DIR__ . '/03_auth_password_test.php',
    __DIR__ . '/04_role_helpers_test.php',
    __DIR__ . '/05_grievance_lifecycle_test.php',
    __DIR__ . '/06_escalation_query_test.php',
];

$passed = 0;
$failed = 0;

echo "Running GBU GMS Test Suite...\n";
echo str_repeat('-', 50) . "\n";

foreach ($testFiles as $file) {
    $name = basename($file);
    try {
        $testFn = require $file;
        $testFn($pdo);
        echo "[PASS] $name\n";
        $passed++;
    } catch (Throwable $e) {
        echo "[FAIL] $name -> " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo str_repeat('-', 50) . "\n";
echo "Result: $passed passed, $failed failed.\n";

exit($failed > 0 ? 1 : 0);
