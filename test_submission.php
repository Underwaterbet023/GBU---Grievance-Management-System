<?php
session_start();
require 'includes/db.php';

// Mock a student user
$_SESSION['user_id'] = 2; // Assuming student exists
$_SESSION['role'] = 'Student';

// Mock data
$data = (object)[
    'subject' => 'Test Subject ' . time(),
    'description' => 'Test Description ' . time(),
    'priority' => 'Medium',
    'category_hint' => 'Academic',
    'department' => 'SOICT',
    'hostel' => 'Tansen'
];

// Replicate logic from public/api/submit_complaint.php
$year = date("Y");
$stmt_id = $pdo->prepare("SELECT id FROM grievances WHERE id LIKE ? ORDER BY id DESC LIMIT 1");
$stmt_id->execute(["GBU-$year-%"]);
$lastId = $stmt_id->fetchColumn();

if ($lastId) {
    $parts = explode('-', $lastId);
    $lastNum = (int)end($parts);
    $nextNum = $lastNum + 1;
} else {
    $nextNum = 1;
}
$grievance_id = "GBU-$year-" . str_pad($nextNum, 3, "0", STR_PAD_LEFT);

echo "Generating ID: $grievance_id\n";

try {
    $stmt = $pdo->prepare("INSERT INTO grievances (id, user_id, category, department, hostel, priority, subject, description, status, current_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Department')");
    $result = $stmt->execute([
        $grievance_id, 
        $_SESSION['user_id'], 
        $data->category_hint, 
        $data->department, 
        $data->hostel, 
        $data->priority, 
        $data->subject, 
        $data->description
    ]);
    
    if ($result) {
        echo "SUCCESS: Grievance inserted.\n";
    } else {
        print_r($stmt->errorInfo());
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
