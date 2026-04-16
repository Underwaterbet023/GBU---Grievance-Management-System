<?php
$pageTitle = 'User Management';
require_once '../includes/db.php';
require_once '../includes/header.php';
requireAnyAdmin();

// Handle status toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $uid = (int)$_POST['user_id'];
    $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$uid]);
    header('Location: users.php?msg=updated');
    exit;
}

// Filters
$roleFilter   = $_GET['role'] ?? '';
$searchFilter = $_GET['search'] ?? '';
$where = ["1=1"];
$params = [];

if ($roleFilter)   { $where[] = "role = ?"; $params[] = $roleFilter; }
if ($searchFilter) { $where[] = "(name LIKE ? OR email LIKE ? OR enrollment_number LIKE ?)";
    $params[] = "%$searchFilter%"; $params[] = "%$searchFilter%"; $params[] = "%$searchFilter%"; }

$whereClause = "WHERE " . implode(" AND ", $where);

$users = $pdo->prepare("SELECT *, (SELECT COUNT(*) FROM grievances WHERE user_id = users.id) as complaint_count
    FROM users $whereClause ORDER BY created_at DESC");
$users->execute($params);
$users = $users->fetchAll();

$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role='Student'")->fetchColumn();
$totalAdmins   = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'Student'")->fetchColumn();
$activeUsers   = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
?>

<div class="container-fluid py-3 animate-fade-in">
    <!-- Header Section -->
    <div class="row mb-4 mb-md-5 align-items-center">
        <div class="col-12 col-md-7 mb-4 mb-md-0">
            <h6 class="text-primary fw-bold text-uppercase mb-2 x-small tracking-widest">User Directory</h6>
            <h2 class="display-6 fw-bold mb-0 text-white">Access Control</h2>
            <p class="text-muted mb-0 small">Manage operative identities and permissions.</p>
        </div>
    </div>

    <!-- User Stats -->
    <div class="row g-3 g-md-4 mb-5">
        <div class="col-6 col-md-4">
            <div class="glass-card p-3 p-md-4 border-0 shadow-lg text-center h-100">
                <div class="stat-icon mx-auto mb-2 mb-md-3 d-none d-sm-flex" style="background:rgba(99,102,241,0.15); width:40px; height:40px; border-radius:10px; align-items:center; justify-content:center;">
                    <i class="fas fa-user-graduate text-primary small"></i>
                </div>
                <div class="h3 h2-md fw-bold text-white mb-1"><?= $totalStudents ?></div>
                <div class="text-muted x-small text-uppercase tracking-widest fw-bold">Students</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="glass-card p-3 p-md-4 border-0 shadow-lg text-center h-100">
                <div class="stat-icon mx-auto mb-2 mb-md-3 d-none d-sm-flex" style="background:rgba(16,185,129,0.15); width:40px; height:40px; border-radius:10px; align-items:center; justify-content:center;">
                    <i class="fas fa-user-shield text-success small"></i>
                </div>
                <div class="h3 h2-md fw-bold text-white mb-1"><?= $totalAdmins ?></div>
                <div class="text-muted x-small text-uppercase tracking-widest fw-bold">Admins</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="glass-card p-3 p-md-4 border-0 shadow-lg text-center h-100">
                <div class="stat-icon mx-auto mb-2 mb-md-3 d-none d-sm-flex" style="background:rgba(245,158,11,0.15); width:40px; height:40px; border-radius:10px; align-items:center; justify-content:center;">
                    <i class="fas fa-circle-check text-warning small"></i>
                </div>
                <div class="h3 h2-md fw-bold text-white mb-1"><?= $activeUsers ?></div>
                <div class="text-muted x-small text-uppercase tracking-widest fw-bold">Active</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card p-3 mb-4 border-0 shadow-lg bg-white bg-opacity-5">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label text-muted x-small text-uppercase fw-bold mb-1">Operative Search</label>
                <input type="text" name="search" class="form-control form-control-sm custom-input py-2" placeholder="Name/ID..." value="<?= htmlspecialchars($searchFilter) ?>">
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label text-muted x-small text-uppercase fw-bold mb-1">Access Role</label>
                <select name="role" class="form-select form-select-sm custom-input py-2">
                    <option value="">All Roles</option>
                    <option value="Student" <?= $roleFilter === 'Student' ? 'selected' : '' ?>>Student</option>
                    <option value="Department Admin" <?= $roleFilter === 'Department Admin' ? 'selected' : '' ?>>Dept Admin</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill py-2">
                    <i class="fas fa-sync-alt small me-1"></i>SYNC
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="glass-card border-0 shadow-lg overflow-hidden p-0">
        <div class="p-4 border-bottom border-white border-opacity-5">
            <h5 class="fw-bold text-white mb-0">
                <i class="fas fa-list me-2 text-primary"></i>Operative Registry
                <span class="badge bg-primary ms-2 rounded-pill x-small"><?= count($users) ?></span>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="x-small text-uppercase opacity-50">
                    <tr>
                        <th class="ps-4">User</th>
                        <th class="d-none d-sm-table-cell">Role</th>
                        <th class="d-none d-lg-table-cell">Dept</th>
                        <th class="d-none d-md-table-cell">ID No.</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted x-small text-uppercase tracking-widest">No matching records.</td></tr>
                    <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="ps-3 ps-md-4">
                            <div class="d-flex align-items-center gap-2 gap-md-3">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($u['name']) ?>&background=6366f1&color=fff&bold=true&size=32"
                                     class="rounded-circle d-none d-md-block" width="32" alt="">
                                <div class="text-truncate" style="max-width: 120px;">
                                    <div class="fw-bold text-white x-small"><?= htmlspecialchars($u['name']) ?></div>
                                    <div class="text-muted x-small opacity-50"><?= htmlspecialchars($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <?php
                            $roleColors = [
                                'Student'          => 'bg-info-subtle text-info',
                                'Department Admin' => 'bg-warning-subtle text-warning',
                                'College Admin'    => 'bg-success-subtle text-success',
                                'University Admin' => 'bg-danger-subtle text-danger',
                            ];
                            $rc = $roleColors[$u['role']] ?? 'bg-primary-subtle text-primary';
                            ?>
                            <span class="badge <?= $rc ?> x-small fw-bold px-2 py-1 rounded-pill"><?= strtoupper(explode(' ', $u['role'])[0]) ?></span>
                        </td>
                        <td class="d-none d-lg-table-cell text-muted small">
                            <?= htmlspecialchars($u['department'] ?: '---') ?>
                        </td>
                        <td class="d-none d-md-table-cell font-monospace x-small text-muted">
                            <?= htmlspecialchars($u['enrollment_number'] ?? $u['roll_number'] ?? '---') ?>
                        </td>
                        <td class="pe-4 text-end">
                            <?php $active = $u['is_active'] ?? 1; ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" name="toggle_status" class="btn btn-sm rounded-pill px-2 <?= $active ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                    <i class="fas <?= $active ? 'fa-ban' : 'fa-check' ?>"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.stat-icon { width:56px;height:56px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center; }
[data-theme="light"] .table-dark-custom tbody td { color:#1e293b; }
</style>

<?php require_once '../includes/footer.php'; ?>
