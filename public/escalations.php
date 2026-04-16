<?php
$pageTitle = 'Escalations';
require_once '../includes/db.php';
require_once '../includes/header.php';
requireAnyAdmin();

// Escalate action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['escalate_id'])) {
    $eid = (int)$_POST['escalate_id'];
    $levels = ['Department','College','University'];
    $g = $pdo->prepare("SELECT current_level FROM grievances WHERE id=?");
    $g->execute([$eid]);
    $row = $g->fetch();
    if ($row) {
        $currIdx = array_search($row['current_level'], $levels);
        $nextLevel = $levels[min($currIdx + 1, count($levels) - 1)];
        $pdo->prepare("UPDATE grievances SET current_level=?, status='In Review', updated_at=NOW() WHERE id=?")->execute([$nextLevel, $eid]);
        $pdo->prepare("INSERT INTO responses (grievance_id, admin_id, response, created_at) VALUES (?,?,?,NOW())")
            ->execute([$eid, $_SESSION['user_id'], 'Complaint escalated to '.$nextLevel.' level by Admin.']);
    }
    header('Location: escalations.php?msg=escalated');
    exit;
}

// Get overdue / escalated complaints (open for > 5 days, or priority High/Critical)
$escalated = $pdo->query("
    SELECT g.*, u.name as student_name, u.email as student_email,
           DATEDIFF(NOW(), g.created_at) as days_open
    FROM grievances g
    JOIN users u ON u.id = g.user_id
    WHERE g.status NOT IN ('Resolved','Closed')
    AND (
        DATEDIFF(NOW(), g.created_at) > 5
        OR g.priority IN ('High','Critical')
    )
    ORDER BY g.priority DESC, days_open DESC
")->fetchAll();

$critical = array_filter($escalated, fn($r) => $r['priority'] === 'Critical');
$highOld   = array_filter($escalated, fn($r) => $r['days_open'] > 10);
?>

<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-5 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-white mb-1" style="letter-spacing:-0.5px;">
                <i class="fas fa-exclamation-triangle text-warning me-3"></i>Escalations
            </h2>
            <p class="text-muted small mb-0">Complaints requiring urgent attention — overdue or high-priority cases.</p>
        </div>
    </div>

    <!-- Alert Banners -->
    <?php if (!empty($critical)): ?>
    <div class="alert border-0 mb-4 p-4 rounded-3 d-flex align-items-center gap-3"
         style="background:rgba(239,68,68,0.12);border-left:4px solid #ef4444!important;">
        <i class="fas fa-circle-exclamation text-danger fs-4"></i>
        <div>
            <div class="fw-bold text-danger">Critical Alert</div>
            <div class="text-muted small"><?= count($critical) ?> complaint(s) marked as <strong>Critical</strong> are still unresolved.</div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($highOld)): ?>
    <div class="alert border-0 mb-4 p-4 rounded-3 d-flex align-items-center gap-3"
         style="background:rgba(245,158,11,0.12);border-left:4px solid #f59e0b!important;">
        <i class="fas fa-clock text-warning fs-4"></i>
        <div>
            <div class="fw-bold text-warning">Overdue Notice</div>
            <div class="text-muted small"><?= count($highOld) ?> complaint(s) have been open for more than <strong>10 days</strong>.</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Summary Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="glass-card p-4 border-0 shadow-lg text-center">
                <div class="fs-1 fw-bold text-danger"><?= count($escalated) ?></div>
                <div class="text-muted x-small text-uppercase tracking-widest mt-1">Total Escalated</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 border-0 shadow-lg text-center">
                <div class="fs-1 fw-bold text-warning"><?= count($highOld) ?></div>
                <div class="text-muted x-small text-uppercase tracking-widest mt-1">Overdue &gt;10 Days</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 border-0 shadow-lg text-center">
                <div class="fs-1 fw-bold text-danger"><?= count($critical) ?></div>
                <div class="text-muted x-small text-uppercase tracking-widest mt-1">Critical Priority</div>
            </div>
        </div>
    </div>

    <!-- Escalation Cards -->
    <?php if (empty($escalated)): ?>
        <div class="glass-card p-5 border-0 text-center shadow-lg">
            <i class="fas fa-check-circle text-success fs-1 mb-3 d-block"></i>
            <h5 class="text-white fw-bold">All Clear!</h5>
            <p class="text-muted">No complaints require escalation at this time.</p>
        </div>
    <?php else: ?>
    <div class="d-flex flex-column gap-4">
        <?php foreach ($escalated as $c):
            $prioColors = ['Critical'=>'danger','High'=>'warning','Medium'=>'info','Low'=>'success'];
            $pColor = $prioColors[$c['priority']] ?? 'secondary';
            $daysOpen = $c['days_open'];
            $urgency = $daysOpen > 10 ? 'danger' : ($daysOpen > 5 ? 'warning' : 'info');
        ?>
        <div class="glass-card border-0 shadow-lg overflow-hidden">
            <!-- Color Accent Strip -->
            <div style="height:4px;background:<?= $c['priority']==='Critical' ? 'linear-gradient(90deg,#ef4444,#f87171)' : ($c['priority']==='High' ? 'linear-gradient(90deg,#f59e0b,#fbbf24)' : 'linear-gradient(90deg,#6366f1,#818cf8)') ?>;"></div>
            <div class="p-4 p-md-5">
                <div class="row align-items-center g-3">
                    <!-- Info -->
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                            <span class="badge bg-<?= $pColor ?>-subtle text-<?= $pColor ?> px-3 py-2 rounded-pill fw-bold">
                                <i class="fas fa-fire me-1"></i><?= htmlspecialchars($c['priority']) ?>
                            </span>
                            <span class="badge bg-<?= $urgency ?>-subtle text-<?= $urgency ?> px-3 py-2 rounded-pill">
                                <i class="fas fa-clock me-1"></i><?= $daysOpen ?> days open
                            </span>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                <?= htmlspecialchars($c['current_level']) ?> Level
                            </span>
                            <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                <?= htmlspecialchars($c['status']) ?>
                            </span>
                        </div>

                        <h5 class="fw-bold text-white mb-2"><?= htmlspecialchars($c['subject']) ?></h5>
                        <p class="text-muted small mb-3"><?= htmlspecialchars(substr($c['description'], 0, 200)) ?>...</p>

                        <div class="d-flex gap-4 flex-wrap">
                            <div>
                                <div class="text-muted x-small text-uppercase tracking-widest fw-bold">Student</div>
                                <div class="text-white small"><?= htmlspecialchars($c['student_name']) ?></div>
                            </div>
                            <div>
                                <div class="text-muted x-small text-uppercase tracking-widest fw-bold">Category</div>
                                <div class="text-white small"><?= htmlspecialchars($c['category']) ?></div>
                            </div>
                            <div>
                                <div class="text-muted x-small text-uppercase tracking-widest fw-bold">Department</div>
                                <div class="text-white small"><?= htmlspecialchars($c['department'] ?: '—') ?></div>
                            </div>
                            <div>
                                <div class="text-muted x-small text-uppercase tracking-widest fw-bold">Filed</div>
                                <div class="text-white small"><?= date('M d, Y', strtotime($c['created_at'])) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="col-md-5 text-md-end d-flex flex-column align-items-md-end gap-2">
                        <a href="manage_complaints.php?search=<?= $c['id'] ?>" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                            <i class="fas fa-eye me-2"></i>View Full Complaint
                        </a>
                        <?php $levels = ['Department','College','University'];
                              $currIdx = array_search($c['current_level'], $levels);
                              if ($currIdx < count($levels) - 1): ?>
                        <form method="POST">
                            <input type="hidden" name="escalate_id" value="<?= $c['id'] ?>">
                            <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold btn-escalate"
                                    data-id="<?= $c['id'] ?>" data-subject="<?= htmlspecialchars($c['subject']) ?>">
                                <i class="fas fa-arrow-up me-2"></i>Escalate to <?= $levels[$currIdx + 1] ?>
                            </button>
                        </form>
                        <?php else: ?>
                        <span class="text-muted small fst-italic">Already at University level</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
// Escalate with SweetAlert2 confirm
document.querySelectorAll('.btn-escalate').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = this.closest('form');
        const subject = this.dataset.subject;
        Swal.fire({
            title: 'Escalate Complaint?',
            html: `<p class="text-muted">Escalate <strong>${subject}</strong> to the next administrative level?</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-arrow-up me-2"></i>Yes, Escalate',
            cancelButtonText: 'Cancel',
            background: document.documentElement.getAttribute('data-theme') === 'light' ? '#fff' : '#1e293b',
            color: document.documentElement.getAttribute('data-theme') === 'light' ? '#0f172a' : '#f8fafc',
            confirmButtonColor: '#f59e0b',
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
