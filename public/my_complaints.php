<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

$userId = (int)$_SESSION['user_id'];

// Get counts for stats
$stmt_stats = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved
    FROM grievances WHERE user_id = ?");
$stmt_stats->execute([$userId]);
$stats = $stmt_stats->fetch();

// Get grievances
$stmt = $pdo->prepare("SELECT id, subject, status, created_at, category, priority, description FROM grievances WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$grievances = $stmt->fetchAll();
?>

<div class="container-fluid py-4 min-vh-100 animate-fade-in">
    <!-- Archive Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="glass-card hero-gradient-card p-4 p-md-5 overflow-hidden position-relative shadow-lg">
                <div class="position-absolute top-0 end-0 p-5 opacity-10">
                    <i class="fas fa-archive fa-10x text-primary animate-pulse"></i>
                </div>
                <div class="position-relative">
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <a href="student_dashboard.php" class="btn btn-outline-secondary rounded-circle border-0 bg-white bg-opacity-5" style="width: 45px; height: 45px; display: flex; align-items:center; justify-content:center; color: var(--text-primary);"><i class="fas fa-chevron-left"></i></a>
                        <div class="px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-pill x-small fw-bold tracking-widest border border-primary border-opacity-20 uppercase">Personnel Records</div>
                    </div>
                    <h1 class="display-5 fw-bold text-white mb-2">Operation History</h1>
                    <p class="text-secondary mb-0 max-w-lg">Complete audit trail of all grievances filed within the Antigravity ecosystem. Mission status and resolution lifecycle are preserved in immutable logs.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 g-md-4 mb-5">
        <div class="col-6 col-md-4">
            <div class="glass-card p-3 p-md-4 border-0 shadow-sm hover-lift bg-white bg-opacity-5">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-secondary x-small fw-bold text-uppercase tracking-widest mb-1 d-block">All Time</span>
                        <h2 class="text-primary fw-bold mb-0"><?= $stats['total'] ?? 0 ?></h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-2 p-md-3 rounded-4 border border-primary border-opacity-20 d-none d-sm-block">
                        <i class="fas fa-database text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="glass-card p-3 p-md-4 border-0 shadow-sm hover-lift bg-white bg-opacity-5">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-secondary x-small fw-bold text-uppercase tracking-widest mb-1 d-block">Active</span>
                        <h2 class="text-warning fw-bold mb-0"><?= $stats['pending'] ?? 0 ?></h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-2 p-md-3 rounded-4 border border-warning border-opacity-20 d-none d-sm-block">
                        <i class="fas fa-satellite-dish text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 d-none d-md-block">
            <div class="glass-card p-4 border-0 shadow-sm hover-lift bg-white bg-opacity-5">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-secondary x-small fw-bold text-uppercase tracking-widest mb-1 d-block">Resolved</span>
                        <h2 class="text-success fw-bold mb-0"><?= $stats['resolved'] ?? 0 ?></h2>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 border border-success border-opacity-20">
                        <i class="fas fa-check-double text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main List -->
        <div class="col-lg-8">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                <h4 class="text-white fw-bold m-0 d-flex align-items-center gap-3">
                    <i class="fas fa-stream text-primary opacity-50"></i> Active Logs
                </h4>
                <div class="search-box position-relative w-100 w-sm-auto">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary x-small"></i>
                    <input type="text" id="tableSearch" class="form-control bg-white bg-opacity-5 border-0 rounded-pill px-5 x-small text-white w-100" placeholder="Search mission IDs..." style="min-width: 200px;">
                </div>
            </div>

            <?php if (empty($grievances)): ?>
                <div class="glass-card p-5 text-center border-0 shadow-lg">
                    <div class="bg-white bg-opacity-5 p-4 p-md-5 rounded-circle d-inline-block mb-4">
                        <i class="fas fa-folder-open fa-3x fa-md-4x text-secondary opacity-25"></i>
                    </div>
                    <h3 class="text-white fw-bold mb-3">No Operational Records</h3>
                    <p class="text-secondary mb-5 small">You haven't initiated any grievance protocols yet.</p>
                    <a href="submit_complaint.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-primary border-0">INITIATE MISSION</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3" id="grievanceList">
                    <?php foreach ($grievances as $g): 
                        $badgeClass = 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-20';
                        if($g['status'] == 'Resolved') $badgeClass = 'bg-success bg-opacity-10 text-success border-success border-opacity-20';
                        if($g['status'] == 'Rejected') $badgeClass = 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-20';
                        if($g['status'] == 'In Progress') $badgeClass = 'bg-info bg-opacity-10 text-info border-info border-opacity-20';

                        $priorityClass = 'text-secondary';
                        if($g['priority'] == 'High') $priorityClass = 'text-warning';
                        if($g['priority'] == 'Urgent') $priorityClass = 'text-danger';
                    ?>
                        <div class="grievance-item glass-card p-3 p-md-4 border-0 hover-lift shadow-sm position-relative overflow-hidden group">
                            <div class="position-absolute top-0 start-0 h-100 bg-primary opacity-0 group-hover-opacity-100 transition-all" style="width: 4px;"></div>
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center gap-2 gap-md-3 mb-2 mb-md-3 flex-wrap">
                                        <span class="small font-monospace fw-bold text-primary">#<?= $g['id'] ?></span>
                                        <span class="badge <?= $badgeClass ?> x-small fw-bold tracking-widest uppercase py-1 px-3 border"><?= $g['status'] ?></span>
                                        <span class="x-small text-secondary fw-bold text-uppercase tracking-widest d-none d-sm-inline"><?= htmlspecialchars($g['category']) ?></span>
                                        <span class="x-small <?= $priorityClass ?> fw-bold text-uppercase tracking-widest"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> <?= $g['priority'] ?></span>
                                    </div>
                                    <h5 class="text-white fw-bold mb-2 group-hover-text-primary transition-all text-truncate"><?= htmlspecialchars($g['subject']) ?></h5>
                                    <p class="text-secondary x-small mb-0 text-truncate opacity-75"><?= htmlspecialchars($g['description']) ?></p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="mb-3 d-none d-md-block">
                                        <span class="text-secondary x-small d-block mb-1 text-uppercase tracking-widest fw-bold">Timestamp</span>
                                        <span class="text-white small fw-bold"><?= date('M d, Y', strtotime($g['created_at'])) ?></span>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        <a href="track_complaint.php?id=<?= $g['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 px-md-4 fw-bold hover-lift transition-all x-small">
                                            VIEW INTEL <i class="fas fa-chevron-right ms-1 small"></i>
                                        </a>
                                        <?php if ($g['status'] === 'Pending'): ?>
                                        <button class="btn btn-outline-danger btn-sm rounded-pill px-2 fw-bold btn-delete-my-complaint" data-id="<?= $g['id'] ?>" data-subject="<?= htmlspecialchars($g['subject'], ENT_QUOTES) ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 2rem;">
                <div class="glass-card p-4 border-0 shadow-lg mb-4 bg-primary bg-opacity-10 border-primary border-opacity-10">
                    <h6 class="fw-bold text-primary text-uppercase tracking-widest mb-4 d-flex align-items-center gap-3">
                        <i class="fas fa-info-circle"></i> Protocol Brief
                    </h6>
                    <div class="d-flex flex-column gap-4">
                        <div class="protocol-item">
                            <p class="text-white small fw-bold mb-1">REAL-TIME SYNC</p>
                            <p class="text-secondary x-small mb-0">Your grievance status is synchronized across all processing nodes every 60 seconds.</p>
                        </div>
                        <div class="protocol-item">
                            <p class="text-white small fw-bold mb-1">ESCALATION LOGIC</p>
                            <p class="text-secondary x-small mb-0">Unresolved tickets are automatically moved to higher administration levels based on defined SLAs.</p>
                        </div>
                        <div class="protocol-item">
                            <p class="text-white small fw-bold mb-1">DATA INTEGRITY</p>
                            <p class="text-secondary x-small mb-0">All interactions between you and the administration are hashed and timestamped for audit accuracy.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-4 border-0 shadow-lg text-center bg-white bg-opacity-5">
                    <p class="text-secondary small mb-4">Need to initiate a new resolution protocol?</p>
                    <a href="submit_complaint.php" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-primary border-0 hover-lift tracking-widest">
                        NEW GRIEVANCE
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.group:hover .group-hover-text-primary { color: var(--accent-cyan) !important; }
.group-hover-opacity-100 { opacity: 1 !important; }
.w-1 { width: 0.25rem; }
.transition-all { transition: all 0.3s ease; }
.max-w-lg { max-width: 32rem; }
.search-box input:focus {
    background: rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 0 15px rgba(0, 243, 255, 0.1);
}
</style>

<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let items = document.querySelectorAll('.grievance-item');
    items.forEach(item => {
        let text = item.innerText.toUpperCase();
        item.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Delete Complaint (Student — Pending only)
document.querySelectorAll('.btn-delete-my-complaint').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const subject = this.dataset.subject;
        const isDark = document.documentElement.getAttribute('data-theme') !== 'light';
        Swal.fire({
            title: 'Withdraw Complaint?',
            html: `<p class="text-muted small">Permanently withdraw <strong>#${id} — ${subject}</strong>?<br>This cannot be undone.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Yes, Delete',
            cancelButtonText: 'Keep It',
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
                            title:'Withdrawn', 
                            text:'Your complaint has been removed.', 
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
