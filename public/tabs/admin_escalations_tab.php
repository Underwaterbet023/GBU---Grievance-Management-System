<?php
// admin_escalations_tab.php
if (!isset($pdo)) require_once '../../includes/db.php';

// 1. Fetch overdue tickets (Critical Escalations)
// Criteria: Pending/In Progress for > 72 hours
$escalationStmt = $pdo->prepare("
    SELECT g.*, u.name as student_name, u.email as student_email 
    FROM grievances g 
    JOIN users u ON g.user_id = u.id 
    WHERE g.status IN ('Pending', 'In Progress') 
    AND g.created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)
    ORDER BY g.created_at ASC 
");
$escalationStmt->execute();
$escalations = $escalationStmt->fetchAll();

// 2. Summary stats for the escalations tab
$totalOverdue = count($escalations);
$urgentOverdue = 0;
foreach($escalations as $e) {
    if($e['priority'] === 'Urgent') $urgentOverdue++;
}
?>

<div class="animate__animated animate__fadeIn">
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="glass-card p-3 p-md-4 border-0 shadow-sm bg-grad-red bg-opacity-10 border-start border-4 border-danger h-100">
                <div class="d-flex align-items-center gap-3 gap-md-4">
                    <div class="text-danger flex-shrink-0">
                        <i class="fas fa-radiation fs-3 fs-md-1"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-white mb-1 h6 h5-md">Critical Escalations Terminal</h5>
                        <p class="text-muted x-small mb-0 opacity-75">Displaying <span class="text-danger fw-bold"><?= $totalOverdue ?></span> tickets exceeding the 72-hour operational window.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-3 p-md-4 border-0 shadow-sm bg-white bg-opacity-5 h-100 d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="x-small text-muted fw-bold text-uppercase">Urgent Backlog</span>
                    <span class="badge bg-danger rounded-pill x-small"><?= $urgentOverdue ?></span>
                </div>
                <div class="progress bg-white bg-opacity-10" style="height: 4px;">
                    <div class="progress-bar bg-danger" style="width: <?= $totalOverdue > 0 ? ($urgentOverdue/$totalOverdue)*100 : 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-9">
            <div class="glass-card p-0 overflow-hidden border-0 shadow-lg">
                <div class="table-responsive">
                    <table class="table align-middle m-0 table-hover">
                        <thead class="x-small text-uppercase opacity-50 bg-white bg-opacity-5">
                            <tr>
                                <th class="ps-4">Ticket</th>
                                <th class="d-none d-md-table-cell">Assigned</th>
                                <th>Delay</th>
                                <th class="d-none d-sm-table-cell">Priority</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($escalations as $e): 
                                $days = floor((time() - strtotime($e['created_at'])) / (60 * 60 * 24));
                            ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-white text-truncate" style="max-width: 180px;">#<?= $e['id'] ?> - <?= htmlspecialchars($e['subject']) ?></div>
                                    <div class="x-small text-muted d-none d-sm-block">By <?= htmlspecialchars($e['student_name']) ?></div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="d-flex align-items-center">
                                        <?php if ($e['current_level'] === 'Department'): ?>
                                            <div class="avatar-xs bg-primary text-white me-2">DA</div>
                                            <span class="x-small fw-bold">Dept Admin</span>
                                        <?php elseif ($e['current_level'] === 'College'): ?>
                                            <div class="avatar-xs bg-violet text-white me-2">DN</div>
                                            <span class="x-small fw-bold">Dean</span>
                                        <?php else: ?>
                                            <div class="avatar-xs bg-info text-white me-2">RG</div>
                                            <span class="x-small fw-bold">Registrar</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-danger fw-bold x-small text-uppercase tracking-wider"><?= $days ?>d Overdue</span>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge bg-danger bg-opacity-10 text-danger x-small rounded-pill px-3 border border-danger border-opacity-10"><?= strtoupper($e['priority']) ?></span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 x-small fw-bold" data-bs-toggle="dropdown">
                                            ACTION
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border-white border-opacity-10">
                                            <li class="dropdown-header x-small text-uppercase opacity-50 fw-bold">Directive</li>
                                            <li>
                                                <form action="api/update_status.php" method="POST">
                                                    <input type="hidden" name="grievance_id" value="<?= $e['id'] ?>">
                                                    <input type="hidden" name="status" value="Resolved">
                                                    <button type="submit" class="dropdown-item x-small fw-bold text-success">
                                                        <i class="fas fa-check-circle me-2"></i>Resolve
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="api/update_status.php" method="POST">
                                                    <input type="hidden" name="grievance_id" value="<?= $e['id'] ?>">
                                                    <input type="hidden" name="status" value="Rejected">
                                                    <button type="submit" class="dropdown-item x-small fw-bold text-danger">
                                                        <i class="fas fa-times-circle me-2"></i>Reject
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li class="dropdown-header x-small text-uppercase opacity-50 fw-bold">Escalate</li>
                                            <li>
                                                <form action="api/escalate_grievance.php" method="POST">
                                                    <input type="hidden" name="grievance_id" value="<?= $e['id'] ?>">
                                                    <input type="hidden" name="level" value="College">
                                                    <button type="submit" class="dropdown-item x-small fw-bold text-violet">
                                                        <i class="fas fa-arrow-up me-2"></i>Shift to Dean
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="api/escalate_grievance.php" method="POST">
                                                    <input type="hidden" name="grievance_id" value="<?= $e['id'] ?>">
                                                    <input type="hidden" name="level" value="University">
                                                    <button type="submit" class="dropdown-item x-small fw-bold text-info">
                                                        <i class="fas fa-arrow-up me-2"></i>Shift to Registrar
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item x-small fw-bold" href="track_complaint.php?id=<?= $e['id'] ?>">
                                                    <i class="fas fa-eye me-2"></i>View History
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($escalations)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-success mb-3 opacity-25"><i class="fas fa-check-double fs-1"></i></div>
                                    <h6 class="text-muted x-small text-uppercase tracking-widest">No pending escalations.</h6>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="glass-card p-4">
                <h6 class="fw-bold mb-4">Escalation Policy</h6>
                <div class="policy-step mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <span class="step-num me-2">1</span>
                        <span class="x-small fw-bold text-white">Auto-Escalate (72h)</span>
                    </div>
                    <p class="x-small text-muted mb-0">Tickets unread by department heads for 3 days are moved to Academic Council.</p>
                </div>
                <div class="policy-step mb-4 border-top border-white border-opacity-5 pt-4">
                    <div class="d-flex align-items-center mb-2">
                        <span class="step-num me-2">2</span>
                        <span class="x-small fw-bold text-white">Dean Intervene (7 Days)</span>
                    </div>
                    <p class="x-small text-muted mb-0">Critical path delay triggers manual intervention requirement for the Dean.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-num { width: 22px; height: 22px; background: #6366f1; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800; }
.avatar-xs { width: 24px; height: 24px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800; }
</style>
