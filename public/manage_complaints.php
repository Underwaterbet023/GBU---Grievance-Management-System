<?php
require_once '../includes/db.php';
require_once '../includes/header.php';
requireAnyAdmin();

// Filters via GET
$statusFilter = $_GET['status'] ?? '';
$catFilter = $_GET['category'] ?? '';
$deptFilter = $_GET['department'] ?? '';
$priorityFilter = $_GET['priority'] ?? '';
$searchFilter = $_GET['search'] ?? '';

$where = ["1=1"];
$params = [];

if ($statusFilter) { $where[] = "g.status = ?"; $params[] = $statusFilter; }
if ($catFilter) { $where[] = "g.category = ?"; $params[] = $catFilter; }
if ($deptFilter) { $where[] = "g.department = ?"; $params[] = $deptFilter; }
if ($priorityFilter) { $where[] = "g.priority = ?"; $params[] = $priorityFilter; }
if ($searchFilter) { 
    $where[] = "(g.subject LIKE ? OR u.name LIKE ? OR g.id LIKE ?)"; 
    $params[] = "%$searchFilter%";
    $params[] = "%$searchFilter%";
    $params[] = "%$searchFilter%";
}

if ($_SESSION['role'] === 'Department Admin') {
    $where[] = "g.department = ? AND g.current_level = 'Department'";
    $params[] = $_SESSION['department'];
} elseif ($_SESSION['role'] === 'College Admin') {
    $where[] = "g.department = ? AND g.current_level = 'College'";
    $params[] = $_SESSION['department'];
} elseif ($_SESSION['role'] === 'University Admin') {
    $where[] = "g.current_level = 'University'";
}

$whereClause = "WHERE " . implode(" AND ", $where);

$stmt = $pdo->prepare("
    SELECT g.*, u.name as student_name, u.email as student_email 
    FROM grievances g 
    JOIN users u ON g.user_id = u.id 
    $whereClause 
    ORDER BY CASE WHEN g.priority = 'Urgent' THEN 1 WHEN g.priority = 'High' THEN 2 ELSE 3 END, g.created_at DESC
");
$stmt->execute($params);
$grievances = $stmt->fetchAll();

$categories = ['Academic', 'Hostel', 'Facility', 'Harassment', 'Financial', 'Other'];
$departments = ['Computer Science', 'Mechanical', 'Electrical', 'Civil', 'Biotechnology', 'Management'];
?>

<div class="container-fluid animate__animated animate__fadeIn py-3">
    <!-- Header Section -->
    <div class="row mb-4 mb-md-5 align-items-center">
        <div class="col-12 col-md-7 mb-4 mb-md-0">
            <h6 class="text-primary fw-bold text-uppercase mb-2 x-small tracking-widest">Logistical Core</h6>
            <h2 class="display-6 fw-bold mb-0 text-white">Case Terminal</h2>
            <p class="text-muted mb-0 small">Overview of <span class="badge bg-primary-subtle text-primary rounded-pill"><?= count($grievances) ?> Active Cases</span></p>
        </div>
        <div class="col-12 col-md-5 text-md-end">
            <div class="input-group shadow-glow rounded-pill overflow-hidden border-0 bg-white bg-opacity-5" style="max-width: 400px; margin-left: auto;">
                <span class="input-group-text bg-transparent border-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="terminalSearch" class="form-control border-0 py-2 small text-white bg-transparent" placeholder="Search ID or Subject..." value="<?= htmlspecialchars($searchFilter) ?>">
                <button class="btn btn-primary px-4 fw-bold" onclick="handleSearch()">SYNC</button>
            </div>
        </div>
    </div>

    <!-- Terminal Filters -->
    <div class="mb-4 d-flex flex-wrap gap-2 align-items-center bg-white bg-opacity-5 p-3 rounded-4 border border-white border-opacity-5">
        <div class="small fw-bold text-muted text-uppercase me-2 x-small tracking-widest">Filters:</div>
        <div class="d-flex flex-wrap gap-2">
            <a href="manage_complaints.php" class="btn btn-xs <?= !$statusFilter ? 'btn-primary shadow' : 'btn-outline-secondary' ?> rounded-pill px-3 py-1 x-small fw-bold">ALL</a>
            <?php foreach(['Pending', 'In Progress', 'Resolved', 'Rejected'] as $s): ?>
                <a href="?status=<?= $s ?>&category=<?= $catFilter ?>" class="btn btn-xs <?= $statusFilter == $s ? 'btn-primary shadow' : 'btn-outline-secondary' ?> rounded-pill px-3 py-1 x-small fw-bold"><?= strtoupper($s) ?></a>
            <?php endforeach; ?>
        </div>
        
        <div class="vr mx-2 opacity-50 d-none d-sm-block"></div>
        
        <div class="dropdown">
            <button class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1 x-small fw-bold dropdown-toggle shadow-none" data-bs-toggle="dropdown">
                <?= strtoupper($catFilter ?: 'CATEGORY') ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-dark border-white border-opacity-10 shadow-lg p-2 rounded-3">
                <li><a class="dropdown-item rounded-2 x-small fw-bold" href="?status=<?= $statusFilter ?>">ALL CATEGORIES</a></li>
                <?php foreach($categories as $c): ?>
                    <li><a class="dropdown-item rounded-2 x-small fw-bold" href="?category=<?= $c ?>&status=<?= $statusFilter ?>"><?= strtoupper($c) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Case Logistics Table -->
    <div class="glass-card p-0 overflow-hidden border-0 shadow-lg">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="x-small text-uppercase opacity-50">
                    <tr>
                        <th class="ps-4">Ticket</th>
                        <th>Subject & Category</th>
                        <th class="d-none d-lg-table-cell">Student</th>
                        <th class="d-none d-sm-table-cell">Urgency</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($grievances as $g): 
                        $priorityColor = 'text-muted';
                        if($g['priority'] == 'Urgent') $priorityColor = 'text-danger';
                        if($g['priority'] == 'High') $priorityColor = 'text-warning';
                        if($g['priority'] == 'Medium') $priorityColor = 'text-primary';
                        
                        $statusBadge = 'bg-warning-subtle text-warning border border-warning-subtle';
                        if($g['status'] == 'Resolved') $statusBadge = 'bg-success-subtle text-success border border-success-subtle';
                        if($g['status'] == 'Rejected') $statusBadge = 'bg-danger-subtle text-danger border border-danger-subtle';
                        if($g['status'] == 'In Progress') $statusBadge = 'bg-info-subtle text-info border border-info-subtle';
                        
                        $time_ago = time() - strtotime($g['created_at']);
                        $days = floor($time_ago / 86400);
                        if($days < 0) $days = 0;
                    ?>
                    <tr>
                        <td class="ps-4">
                            <span class="small font-monospace fw-bold text-primary d-block">#<?= $g['id'] ?></span>
                            <span class="x-small text-muted"><?= $days == 0 ? 'Today' : ($days == 1 ? 'Yesterday' : "$days days ago") ?></span>
                        </td>
                        <td>
                            <div class="fw-bold text-white text-truncate mb-1" style="max-width: 250px;"><?= htmlspecialchars($g['subject']) ?></div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="x-small text-primary-subtle text-uppercase fw-bold"><?= $g['category'] ?></span>
                                <span class="bg-white bg-opacity-5 text-muted x-small px-2 rounded-pill d-none d-sm-inline"><?= strtoupper($g['current_level']) ?></span>
                            </div>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($g['student_name']) ?>&background=random&color=fff&size=32" class="rounded-circle" width="32">
                                <div>
                                    <div class="small fw-bold text-white"><?= htmlspecialchars($g['student_name']) ?></div>
                                    <div class="x-small text-secondary"><?= htmlspecialchars($g['student_email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <div class="d-flex align-items-center gap-1 <?= $priorityColor ?> fw-bold x-small text-uppercase">
                                <i class="fas fa-bolt" style="font-size: 0.7rem;"></i> <?= $g['priority'] ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?= $statusBadge ?> px-3 py-2 rounded-pill x-small fw-bold"><?= strtoupper($g['status']) ?></span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-dark btn-sm rounded-pill px-3 action-trigger x-small fw-bold" data-json='<?= json_encode($g) ?>'>
                                    <i class="fas fa-edit me-1"></i>EDIT
                                </button>
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-2 btn-delete-complaint" data-id="<?= $g['id'] ?>" data-subject="<?= htmlspecialchars($g['subject'], ENT_QUOTES) ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if(empty($grievances)): ?>
            <div class="text-center py-5">
                <div class="mb-3"><i class="fas fa-ghost fa-3x text-light"></i></div>
                <h5 class="text-muted">Archive approach clear. No matching cases.</h5>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Overhaul -->
<div class="modal fade animate__animated animate__fadeInRight" id="managementModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content overflow-hidden">
            <div class="row g-0">
                <!-- Sidebar Info -->
                <div class="col-lg-4 bg-dark text-white p-5">
                    <h6 class="text-primary text-uppercase fw-bold mb-4" style="letter-spacing: 2px;">Case Briefing</h6>
                    <h2 class="fw-bold mb-4" id="m_id_title">---</h2>
                    <div class="case-meta mb-5">
                        <div class="mb-4 d-flex flex-column border-bottom border-secondary pb-3">
                            <span class="text-muted small mb-1">Status</span>
                            <span id="m_status_text" class="fw-bold h5 mb-0">---</span>
                        </div>
                        <div class="mb-4 d-flex flex-column border-bottom border-secondary pb-3">
                            <span class="text-muted small mb-1">Submitted On</span>
                            <span id="m_date" class="fw-bold h5 mb-0">---</span>
                        </div>
                        <div class="mb-4 d-flex flex-column">
                            <span class="text-muted small mb-1">Subject Matter</span>
                            <span id="m_title" class="fw-bold h5 mb-0">---</span>
                        </div>
                    </div>
                    <div class="mt-auto pt-5">
                        <div class="alert alert-info bg-opacity-10 border-info text-info small">
                            <i class="fas fa-shield-alt me-2"></i> Integrity verified via GBU Blockchain.
                        </div>
                    </div>
                </div>
                
                <!-- Main Action Area -->
                <div class="col-lg-8 p-5" style="background: var(--bg-card);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold m-0 text-white">Operational Directives</h4>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="description-terminal p-4 rounded-4 mb-4 border border-white border-opacity-10" style="background: rgba(255,255,255,0.03);">
                        <h6 class="fw-bold small text-muted text-uppercase mb-2">Detailed Report</h6>
                        <p class="mb-0 text-white lh-base" id="m_desc" style="white-space: pre-wrap;">---</p>
                    </div>

                    <form id="managementForm">
                        <input type="hidden" name="grievance_id" id="form_id">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Update Phase</label>
                                <select class="form-select border-0 shadow-sm py-3 rounded-3 text-white" style="background: rgba(255,255,255,0.05);" name="status" id="form_status">
                                    <option value="Pending" class="bg-dark text-white">Pending</option>
                                    <option value="In Progress" class="bg-dark text-white">In Progress</option>
                                    <option value="Resolved" class="bg-dark text-white">Resolved</option>
                                    <option value="Rejected" class="bg-dark text-white">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Escalation Vector</label>
                                <select class="form-select border-0 shadow-sm py-3 rounded-3 text-white" style="background: rgba(255,255,255,0.05);" name="level" id="form_level">
                                    <option value="Department" class="bg-dark text-white">Department</option>
                                    <option value="College" class="bg-dark text-white">College</option>
                                    <option value="University" class="bg-dark text-white">University</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Directives / Remarks</label>
                            <textarea class="form-control border-0 shadow-sm p-3 rounded-3 text-white" style="background: rgba(255,255,255,0.05);" name="remark" id="form_remark" rows="4" placeholder="Enter resolution details or escalation rationale..." required></textarea>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-light btn-lg flex-grow-1 py-3 rounded-3" data-bs-dismiss="modal">Abort</button>
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 py-3 rounded-3 shadow-sm">
                                Execute Directive <i class="fas fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- No inline styles needed - using global style.css -->

<script>
function handleSearch() {
    const term = document.getElementById('terminalSearch').value;
    window.location.href = `?search=${encodeURIComponent(term)}`;
}

document.querySelectorAll('.action-trigger').forEach(btn => {
    btn.addEventListener('click', function() {
        const data = JSON.parse(this.dataset.json);
        document.getElementById('m_id_title').innerText = data.id;
        document.getElementById('m_title').innerText = data.subject;
        document.getElementById('m_desc').innerText = data.description;
        document.getElementById('m_status_text').innerText = data.status;
        document.getElementById('m_date').innerText = new Date(data.created_at).toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('form_id').value = data.id;
        
        // Pre-select modal values
        document.getElementById('form_status').value = data.status;
        document.getElementById('form_level').value = data.current_level;
        document.getElementById('form_remark').value = '';
        
        new bootstrap.Modal('#managementModal').show();
    });
});

document.getElementById('managementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
    submitBtn.disabled = true;

    const formData = {
        grievance_id: document.getElementById('form_id').value,
        status: document.getElementById('form_status').value,
        level: document.getElementById('form_level').value,
        remark: document.getElementById('form_remark').value
    };

    fetch('api/update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(res => {
        if(res.success) {
            Swal.fire({
                icon: 'success',
                title: 'Directive Executed',
                text: 'System state updated and logged to blockchain.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Operation Failed', res.message, 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(() => {
        Swal.fire('Critical Error', 'Blockchain node unreachable.', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
// Delete Complaint (Admin)
document.querySelectorAll('.btn-delete-complaint').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const subject = this.dataset.subject;
        const isDark = document.documentElement.getAttribute('data-theme') !== 'light';
        Swal.fire({
            title: 'Delete Complaint?',
            html: `<p class="text-muted small">Permanently delete <strong>#${id.padStart(4,'0')} — ${subject}</strong>?<br>This action cannot be undone.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef4444',
            background: isDark ? '#1e293b' : '#fff',
            color: isDark ? '#f8fafc' : '#0f172a',
        }).then(result => {
            if (result.isConfirmed) {
                fetch('api/delete_complaint.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ grievance_id: id })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({ 
                            icon:'success', 
                            title:'Deleted', 
                            text:'Complaint has been removed.', 
                            timer:1500, 
                            showConfirmButton:false,
                            didClose: () => { location.reload(); }
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Could not delete complaint.', 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Network error.', 'error'));
            }
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
