<?php
// includes/auth.php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireRole($role) {
    if (!hasRole($role)) {
        die("Unauthorized access.");
    }
}

// Check for any admin role
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }

    $adminRoles = ['Main Admin', 'Department Admin', 'College Admin', 'University Admin'];
    return isset($_SESSION['role']) && in_array($_SESSION['role'], $adminRoles, true);
}

// Safely append a query parameter when redirecting back to a referring page
function redirectBackWithParam($fallbackUrl, $key, $value) {
    $target = $_SERVER['HTTP_REFERER'] ?? $fallbackUrl;
    $separator = (strpos($target, '?') !== false) ? '&' : '?';
    header("Location: {$target}{$separator}" . urlencode($key) . '=' . urlencode($value));
    exit();
}

function requireAnyAdmin() {
    if (!isAdmin()) {
        header("Location: student_dashboard.php");
        exit();
    }
}

// Map internal roles to specific designations for display
function getDisplayRole($role) {
    $map = [
        'College Admin' => 'Dean',
        'University Admin' => 'Registrar',
        'Department Admin' => 'Dept Admin'
    ];
    return $map[$role] ?? $role;
}
?>
