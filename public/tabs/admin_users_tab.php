<?php
// User Management Tab Logic
$roleFilter   = $_GET['user_role'] ?? '';
$searchFilter = $_GET['user_search'] ?? '';
$where = ["1=1"];
$params = [];

if ($roleFilter)   { $where[] = "role = ?"; $params[] = $roleFilter; }
if ($searchFilter) { 
    $where[] = "(name LIKE ? OR email LIKE ? OR roll_number LIKE ?)";
    $params[] = "%$searchFilter%"; $params[] = "%$searchFilter%"; $params[] = "%$searchFilter%"; 
}

$whereClause = "WHERE " . implode(" AND ", $where);

$usersStmt = $pdo->prepare("SELECT *, (SELECT COUNT(*) FROM grievances WHERE user_id = users.id) as complaint_count
    FROM users $whereClause ORDER BY created_at DESC");
$usersStmt->execute($params);
$tabUsers = $usersStmt->fetchAll();

$tabTotalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role='Student'")->fetchColumn();
$tabTotalAdmins   = $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'Student'")->fetchColumn();
$tabActiveUsers   = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();

// Recent Activity Logs for display (Top 10)
$activityLogs = [];
try {
    $activityLogs = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 10")->fetchAll();
} catch (Exception $e) {
    // Table missing, handled in view
}
?>

<div class="row g-4 mb-4">
    <!-- User Stats -->
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card-vibrant bg-grad-cyan border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-4">
                <i class="fas fa-user-graduate bg-icon"></i>
                <h6 class="card-subtitle text-uppercase mb-2 tracking-widest text-white opacity-75">Students</h6>
                <h2 class="display-5 fw-bold mb-0 text-white"><?= number_format($tabTotalStudents) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card-vibrant bg-grad-orange border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-4">
                <i class="fas fa-user-shield bg-icon"></i>
                <h6 class="card-subtitle text-uppercase mb-2 tracking-widest text-white opacity-75">Admins</h6>
                <h2 class="display-5 fw-bold mb-0 text-white"><?= number_format($tabTotalAdmins) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card stat-card-vibrant bg-grad-green border-0 rounded-4 shadow-sm h-100">
            <div class="card-body p-4">
                <i class="fas fa-user-check bg-icon"></i>
                <h6 class="card-subtitle text-uppercase mb-2 tracking-widest text-white opacity-75">Active Accounts</h6>
                <h3 class="display-5 fw-bold mb-0 text-white"><?= number_format($tabActiveUsers) ?></h3>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success border-0 mb-4 p-3 rounded-3 x-small fw-bold">
    <i class="fas fa-check-circle me-2"></i> Action Successful: User database synchronized.
</div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger border-0 mb-4 p-3 rounded-3 x-small fw-bold">
    <i class="fas fa-exclamation-circle me-2"></i> Error: <?= htmlspecialchars($_GET['error']) ?>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 border-bottom border-white border-opacity-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="fw-bold m-0"><i class="fas fa-users-cog me-2 text-primary"></i>Manage Users</h5>
                <button type="button" class="btn btn-primary-custom btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus me-2"></i>Add User
                </button>
            </div>
            
            <!-- Filter Bar -->
            <div class="px-3 px-md-4 py-3 bg-white bg-opacity-5 border-bottom border-white border-opacity-5">
                <form method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="tab" value="users">
                    <div class="col-12 col-md-5">
                        <label class="form-label x-small text-muted fw-bold mb-2">SEARCH OPERATIVE</label>
                        <input type="text" name="user_search" class="form-control form-control-sm custom-input no-temp-mail" placeholder="Name or email..." value="<?= htmlspecialchars($searchFilter) ?>" autocomplete="off">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label x-small text-muted fw-bold mb-2">ACCESS LEVEL</label>
                        <select name="user_role" class="form-select form-select-sm custom-input">
                            <option value="">All Roles</option>
                            <option value="Student" <?= $roleFilter === 'Student' ? 'selected' : '' ?>>Student</option>
                            <option value="Department Admin" <?= $roleFilter === 'Department Admin' ? 'selected' : '' ?>>Dept Admin</option>
                            <option value="College Admin" <?= $roleFilter === 'College Admin' ? 'selected' : '' ?>>Dean</option>
                            <option value="University Admin" <?= $roleFilter === 'University Admin' ? 'selected' : '' ?>>Registrar</option>
                            <option value="Main Admin" <?= $roleFilter === 'Main Admin' ? 'selected' : '' ?>>Main Admin</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-pill fw-bold py-2">APPLY</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle m-0 table-hover">
                    <thead class="x-small text-uppercase opacity-50">
                        <tr>
                            <th class="ps-4">Operative</th>
                            <th class="d-none d-sm-table-cell">Level</th>
                            <th class="d-none d-lg-table-cell">Status</th>
                            <th class="pe-4 text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tabUsers as $u): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($u['name']) ?>&background=random&color=fff&bold=true" class="user-avatar-list me-3 d-none d-sm-block">
                                    <div class="text-truncate" style="max-width: 150px;">
                                        <div class="small fw-bold user-name-text"><?= htmlspecialchars($u['name']) ?></div>
                                        <div class="x-small text-secondary fw-medium opacity-75 user-email-text"><?= htmlspecialchars($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="badge activity-badge"><?= strtoupper(getDisplayRole($u['role'])) ?></span>
                                <div class="x-small text-muted mt-1"><?= htmlspecialchars($u['department'] ?? 'General') ?></div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="badge activity-badge">System User</span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-2" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border-white border-opacity-10">
                                        <li>
                                            <button type="button" class="dropdown-item x-small fw-bold" 
                                                    onclick="openEditModal(<?= htmlspecialchars(json_encode($u)) ?>)">
                                                <i class="fas fa-edit me-2"></i>Edit Details
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="api/toggle_user.php" method="POST">
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="dropdown-item x-small fw-bold <?= $u['is_active'] ? 'text-warning' : 'text-success' ?>">
                                                    <i class="fas <?= $u['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?> me-2"></i>
                                                    <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="api/delete_user.php" method="POST" onsubmit="return confirm('Irreversible action. Continue?')">
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="dropdown-item x-small fw-bold text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i>Delete Account
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Activity Tracking Sidebar -->
    <div class="col-xl-4">
        <div class="glass-card h-100 p-0 overflow-hidden">
            <div class="p-4 border-bottom border-white border-opacity-5">
                <h5 class="fw-bold m-0"><i class="fas fa-stream me-2 text-warning"></i>Audit Logs</h5>
                <p class="text-muted x-small mt-1 mb-0">Recent administrative actions</p>
            </div>
            <div class="p-4">
                <div class="mission-timeline">
                    <?php if(empty($activityLogs)): ?>
                        <p class="text-muted x-small text-center">No recent activities logged.</p>
                    <?php else: ?>
                        <?php foreach($activityLogs as $log): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"><i class="fas fa-bolt"></i></div>
                            <div class="timeline-content p-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="x-small fw-bold text-primary">ID #<?= $log['user_id'] ?></span>
                                    <span class="x-small text-muted"><?= date('H:i', strtotime($log['created_at'])) ?></span>
                                </div>
                                <div class="small text-white opacity-75"><?= htmlspecialchars($log['action']) ?></div>
                                <div class="x-small text-muted mt-2 font-monospace"><?= htmlspecialchars($log['details']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-pill px-4 x-small fw-bold">Full Audit Trail</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg">
            <div class="modal-header p-4 border-0">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-user-plus me-2 text-primary"></i>Provision New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form action="api/add_user.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label text-secondary x-small fw-bold text-uppercase">Full Name</label>
                        <input type="text" name="name" class="form-control custom-input" required placeholder="Ex: John Doe" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary x-small fw-bold text-uppercase">Email Address</label>
                        <input type="text" name="email" class="form-control custom-input no-temp-mail" required placeholder="name@example.com" autocomplete="off" data-lpignore="true">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary x-small fw-bold text-uppercase">Role</label>
                            <select name="role" class="form-select custom-input" required>
                                <option value="Student" class="bg-dark">Student</option>
                                <option value="Department Admin" class="bg-dark">Dept Admin</option>
                                <option value="College Admin" class="bg-dark">Dean (College)</option>
                                <option value="University Admin" class="bg-dark">Registrar (Univ)</option>
                                <option value="Main Admin" class="bg-dark">Main Admin</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary x-small fw-bold text-uppercase">Department/Hostel</label>
                            <input type="text" name="department" class="form-control custom-input" placeholder="Optional" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-secondary x-small fw-bold text-uppercase">Password</label>
                        <input type="password" name="password" class="form-control custom-input" required placeholder="••••••••" autocomplete="new-password">
                        <div class="x-small text-muted mt-1">User can change this after first login.</div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 shadow-lg fw-bold">CREATE ACCOUNT</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg">
            <div class="modal-header p-4 border-0">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-user-edit me-2 text-primary"></i>Modify User Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form action="api/edit_user.php" method="POST">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="mb-3">
                        <label class="form-label text-secondary x-small fw-bold text-uppercase">Full Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control custom-input" required placeholder="Ex: John Doe" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary x-small fw-bold text-uppercase">Email Address</label>
                        <input type="text" name="email" id="edit_email" class="form-control custom-input no-temp-mail" required placeholder="name@example.com" autocomplete="off" data-lpignore="true">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary x-small fw-bold text-uppercase">Role</label>
                            <select name="role" id="edit_role" class="form-select custom-input" required>
                                <option value="Student" class="bg-dark">Student</option>
                                <option value="Department Admin" class="bg-dark">Dept Admin</option>
                                <option value="College Admin" class="bg-dark">Dean (College)</option>
                                <option value="University Admin" class="bg-dark">Registrar (Univ)</option>
                                <option value="Main Admin" class="bg-dark">Main Admin</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary x-small fw-bold text-uppercase">Department/Hostel</label>
                            <input type="text" name="department" id="edit_department" class="form-control custom-input" placeholder="Optional" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-secondary x-small fw-bold text-uppercase">New Password (Optional)</label>
                        <input type="password" name="password" class="form-control custom-input" placeholder="Leave blank to keep current" autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-3 shadow-lg fw-bold">UPDATE ACCOUNT</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon { width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; }
.no-temp-mail { background-image: none !important; }
.no-temp-mail + *, .no-temp-mail ~ [class*="tempmail"], .no-temp-mail ~ [id*="tempmail"] {
    display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important;
}
input.no-temp-mail::-webkit-contacts-auto-fill-button, input.no-temp-mail::-webkit-credentials-auto-fill-button {
    display: none !important; visibility: hidden !important;
}
</style>

<script>
function openEditModal(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_department').value = user.department || '';
    
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}
</script>
