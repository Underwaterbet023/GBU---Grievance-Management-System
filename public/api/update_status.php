<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

if (!isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if it's a POST from the dashboard forms
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grievance_id'])) {
    $gid = (int)$_POST['grievance_id'];
    $status = $_POST['status'] ?? 'In Progress';
    $remark = $_POST['remark'] ?? "Status updated to $status by Admin.";

    try {
        $pdo->beginTransaction();

        // 1. Update grievance status
        $stmt = $pdo->prepare("UPDATE grievances SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $gid]);

        // 2. Fetch student user_id for notification
        $getStudent = $pdo->prepare("SELECT user_id FROM grievances WHERE id = ?");
        $getStudent->execute([$gid]);
        $studentId = $getStudent->fetchColumn();

        // 3. Insert into responses
        $resp = $pdo->prepare("INSERT INTO responses (grievance_id, admin_id, response, created_at) VALUES (?, ?, ?, NOW())");
        $resp->execute([$gid, $_SESSION['user_id'], $remark]);

        // 4. Add notification for student
        $notif = $pdo->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
        $msg = "Your grievance (#$gid) status has been updated to '$status'.";
        $notif->execute([$studentId, $msg]);

        $pdo->commit();
        
        // Redirect back to referring page or dashboard
        $referer = $_SERVER['HTTP_REFERER'] ?? '../admin_dashboard.php';
        header("Location: $referer&success=status_updated");
        exit;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Database Error: " . $e->getMessage());
    }
}

// Fallback for JSON requests (existing logic)
$data = json_decode(file_get_contents("php://input"));
if($data && isset($data->grievance_id)) {
    // ... existing JSON logic can stay here if needed for AJAX calls ...
}
?>