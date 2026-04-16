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

// Check for any type of admin - Restrict to specific authorized personnel
function isAdmin() {
    $authorizedEmails = [
        'Dean55gbu@gbu.ac.in',
        'Registar25gbu@gbu.ac.in',
        'admin5525gbu@gbu.ac.in'
    ];
    
    $adminRoles = ['Main Admin', 'Department Admin', 'College Admin', 'University Admin'];
    $hasRole = isset($_SESSION['role']) && in_array($_SESSION['role'], $adminRoles);
    $hasEmail = isset($_SESSION['email']) && in_array($_SESSION['email'], $authorizedEmails);
    
    return $hasRole && $hasEmail;
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
