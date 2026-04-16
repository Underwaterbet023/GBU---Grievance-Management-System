<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$grievance_id = $input['grievance_id'] ?? '';

if (empty($grievance_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid complaint ID.']);
    exit;
}

$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

try {
    // Check ownership / permission
    if ($role === 'Student') {
        // Students can only delete their OWN Pending complaints
        $stmt = $pdo->prepare("SELECT id, status FROM grievances WHERE id = ? AND user_id = ?");
        $stmt->execute([$grievance_id, $user_id]);
        $grievance = $stmt->fetch();

        if (!$grievance) {
            echo json_encode(['success' => false, 'message' => 'Complaint not found or access denied.']);
            exit;
        }

        if ($grievance['status'] !== 'Pending') {
            echo json_encode(['success' => false, 'message' => 'You can only delete Pending complaints. Once processing has started, contact admin.']);
            exit;
        }
    } elseif (in_array($role, ['Main Admin', 'University Admin', 'College Admin', 'Department Admin'])) {
        // Admins can delete any complaint
        $stmt = $pdo->prepare("SELECT id FROM grievances WHERE id = ?");
        $stmt->execute([$grievance_id]);
        $grievance = $stmt->fetch();

        if (!$grievance) {
            echo json_encode(['success' => false, 'message' => 'Complaint not found.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
        exit;
    }

    // Delete responses first (foreign key), then the grievance
    $pdo->beginTransaction();

    // Delete cascading references - Relying on database ON DELETE CASCADE for responses and escalation_logs
    // Removed invalid notifications delete call as it lacked grievance_id column
    $pdo->prepare("DELETE FROM grievances WHERE id = ?")->execute([$grievance_id]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Complaint deleted successfully.']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
