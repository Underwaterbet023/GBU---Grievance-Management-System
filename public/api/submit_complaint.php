<?php
header("Content-Type: application/json");
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if(!isset($data->subject) || !isset($data->description)) {
    echo json_encode(['success' => false, 'message' => 'Subject and description are required.']);
    exit();
}

// Robust ID Generation: Extract numeric suffix correctly regardless of alphabetical sort
$year = date("Y");
try {
    // Find the current maximum numeric ID for the current year
    $stmt_id = $pdo->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)) FROM grievances WHERE id LIKE ?");
    $stmt_id->execute(["GBU-$year-%"]);
    $maxNum = $stmt_id->fetchColumn();
    $nextNum = ($maxNum ? (int)$maxNum : 0) + 1;
    
    // Use 4-digit padding for longevity (GBU-2024-0001)
    $grievance_id = "GBU-$year-" . str_pad($nextNum, 4, "0", STR_PAD_LEFT);

    // Capture context: fallback to session if not in POST data
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $department = $data->department ?? ($_SESSION['department'] ?? 'General Administration');
    $hostel = $data->hostel ?? ($_SESSION['hostel'] ?? 'N/A');
    $priority = $data->priority ?? 'Medium';
    $category = (isset($data->category_hint) && $data->category_hint !== 'Unspecified') ? $data->category_hint : 'General';

    if (!$user_id) {
        error_log("Submission failed: Missing user_id in session. Session dump: " . print_r($_SESSION, true));
        echo json_encode(['success' => false, 'message' => 'Identity mismatch: Session expired or invalid.']);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO grievances (id, user_id, category, department, hostel, priority, subject, description, status, current_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Department')");
    $result = $stmt->execute([
        $grievance_id, 
        $user_id, 
        $category, 
        $department, 
        $hostel, 
        $priority, 
        $data->subject, 
        $data->description
    ]);
    
    if ($result) {
        // AI Webhook Notification (using @ to suppress network issues)
        $n8n_url = "https://n8n.theasindia.com/webhook/72fa69a2-671e-450b-8dce-0490b8480396"; 
        $payload = json_encode([
            "id" => $grievance_id,
            "subject" => $data->subject,
            "description" => $data->description,
            "priority" => $priority,
            "category" => $category,
            "department" => $department,
            "user_id" => $user_id
        ]);

        $ch = curl_init($n8n_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        @curl_exec($ch); 
        curl_close($ch);

        echo json_encode(['success' => true, 'message' => 'Grievance registered. ID: ' . $grievance_id, 'id' => $grievance_id]);
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Registry failure for User $user_id: " . print_r($errorInfo, true));
        echo json_encode(['success' => false, 'message' => 'Registry failure: ' . ($errorInfo[2] ?? 'Unknown error')]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Infrastructure error: ' . $e->getMessage()]);
}

?>
