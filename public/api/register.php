<?php
header("Content-Type: application/json");
require_once '../../includes/db.php';

$data = json_decode(file_get_contents("php://input"));

if(!isset($data->name) || !isset($data->roll_number) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(['success' => false, 'message' => 'Incomplete data provided.']);
    exit();
}

$name = trim($data->name);
$roll_number = trim($data->roll_number);
$email = trim($data->email);
$department = isset($data->department) ? trim($data->department) : null;
$hostel = isset($data->hostel) ? trim($data->hostel) : null;
$password = password_hash($data->password, PASSWORD_BCRYPT);

try {
    // Basic verification of unique email / roll number constraint
    $checkQry = $pdo->prepare("SELECT id FROM users WHERE email = ? OR roll_number = ?");
    $checkQry->execute([$email, $roll_number]);
    if ($checkQry->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email or Roll Number is already registered.']);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, roll_number, email, password_hash, role, department, hostel) VALUES (?, ?, ?, ?, 'Student', ?, ?)");
    if ($stmt->execute([$name, $roll_number, $email, $password, $department, $hostel])) {
        echo json_encode(['success' => true, 'message' => 'Account created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create account']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
