<?php
require_once '../includes/db.php';
require_once '../includes/header.php'; // Checks login

$userId = $_SESSION['user_id'];

// Get user profile info
$stmt = $pdo->prepare("SELECT name, email, roll_number, role, department, hostel, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

?>

<div class="container-fluid animate-fade-in">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1">Account Settings</h2>
            <p class="text-muted">Manage your profile details and preferences.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <!-- Profile Overview Card -->
            <div class="custom-card p-4 text-center">
                <div class="mb-3 d-flex justify-content-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                </div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                <p class="text-muted mb-3"><?= htmlspecialchars($user['role']) ?></p>
                <div class="badge bg-light text-dark border px-3 py-2">
                    <i class="far fa-calendar-alt me-2"></i> Joined <?= date('M Y', strtotime($user['created_at'])) ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Personal Information Form -->
            <div class="custom-card p-4 p-md-5">
                <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-user-circle text-primary me-2"></i> Personal Information</h5>
                
                <form>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Full Name</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['name']) ?>" readonly>
                        </div>
                        <div class="col-md-6 mt-4 mt-md-0">
                            <label class="form-label text-muted small fw-bold">Email Address</label>
                            <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                    </div>

                    <?php if ($user['role'] === 'Student'): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">Roll Number</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['roll_number']) ?>" readonly>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Department</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['department'] ?? 'Not Assigned') ?>" readonly>
                        </div>
                        <div class="col-md-6 mt-4 mt-md-0">
                            <label class="form-label text-muted small fw-bold">Hostel</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['hostel'] ?? 'Day Scholar') ?>" readonly>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 mt-4 d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <strong>Need to update your details?</strong><br>
                            <span class="small">Contact the University Administrator to request modifications to your profile.</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
