<?php
require_once '../includes/db.php';
require_once '../includes/header.php'; // Checks login

$userId = (int)$_SESSION['user_id'];
// Debug: error_log("Dashboard query for User: $userId");

// Get complaint statistics
$statsQuery = $pdo->prepare("SELECT status, COUNT(*) as count FROM grievances WHERE user_id = ? GROUP BY status");
$statsQuery->execute([$userId]);
$stats = ['Pending' => 0, 'In Progress' => 0, 'Resolved' => 0, 'Rejected' => 0, 'Total' => 0];
while($row = $statsQuery->fetch()) {
    $stats[$row['status']] = (int)$row['count'];
    $stats['Total'] += (int)$row['count'];
}

// Get recent complaints
$stmt = $pdo->prepare("SELECT id, subject, status, created_at, category, priority FROM grievances WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$grievances = $stmt->fetchAll();
?>

<div class="container-fluid py-2 py-md-4 min-vh-100 animate-fade-in">
    <!-- Main Command Header -->
    <div class="row mb-4 mb-md-5 align-items-center">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="d-flex align-items-center gap-3 gap-md-4">
                <div class="position-relative flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['name']) ?>&background=0D8ABC&color=fff&size=128" 
                         class="rounded-circle shadow-lg border border-3 border-primary border-opacity-25" style="width: 60px; height: 60px;">
                    <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-dark animate-pulse" style="width: 14px; height: 14px;"></div>
                </div>
                <div class="text-truncate">
                    <div class="d-flex align-items-center gap-2 mb-0 mb-md-1 flex-wrap">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 x-small fw-bold px-2 py-1 rounded-pill">STUDENT</span>
                        <span class="text-muted x-small fw-bold tracking-widest d-none d-sm-inline">NEXUS v2</span>
                    </div>
                    <h1 class="h3 fw-bold text-white mb-0 text-truncate">Welcome, <?= htmlspecialchars(explode(' ', $_SESSION['name'])[0]) ?></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <a href="submit_complaint.php" class="btn btn-primary btn-lg rounded-pill px-4 py-2 py-md-3 fw-bold tracking-wider shadow-primary hover-lift d-flex align-items-center justify-content-center gap-2 border-0 w-100">
                <span class="small">SUBMIT GRIEVANCE</span>
                <i class="fas fa-plus-circle small"></i>
            </a>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row g-2 g-md-4 mb-4 mb-md-5">
        <div class="col-6 col-xl-3">
            <div class="card stat-card-vibrant bg-grad-orange border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <i class="fas fa-satellite-dish bg-icon d-none d-sm-block"></i>
                    <h6 class="card-subtitle text-uppercase mb-1 mb-md-2 tracking-widest x-small opacity-75">Active</h6>
                    <h2 class="h2 fw-bold mb-0"><?= $stats['Pending'] + $stats['In Progress'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card-vibrant bg-grad-green border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <i class="fas fa-check-double bg-icon d-none d-sm-block"></i>
                    <h6 class="card-subtitle text-uppercase mb-1 mb-md-2 tracking-widest x-small opacity-75">Resolved</h6>
                    <h2 class="h2 fw-bold mb-0"><?= $stats['Resolved'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card-vibrant bg-grad-red border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <i class="fas fa-database bg-icon d-none d-sm-block"></i>
                    <h6 class="card-subtitle text-uppercase mb-1 mb-md-2 tracking-widest x-small opacity-75">Total</h6>
                    <h2 class="h2 fw-bold mb-0"><?= $stats['Total'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card-vibrant bg-grad-cyan border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <i class="fas fa-shield-alt bg-icon d-none d-sm-block"></i>
                    <h6 class="card-subtitle text-uppercase mb-1 mb-md-2 tracking-widest x-small opacity-75">Uptime</h6>
                    <h2 class="h2 fw-bold mb-0">98%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Feed & Action Center -->
    <div class="row g-4">
        <!-- Live Complaint Log -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                <h4 class="text-dark fw-bold m-0 d-flex align-items-center gap-3">
                    <i class="fas fa-list-ul text-primary"></i> My Recent Complaints
                </h4>
                <a href="my_complaints.php" class="btn btn-link text-primary text-decoration-none x-small fw-bold tracking-widest">
                    VIEW ALL <i class="fas fa-chevron-right ms-2 fs-x-small"></i>
                </a>
            </div>
            
            <div class="glass-card p-0 overflow-hidden border-0 shadow-lg">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-primary bg-opacity-10">
                                <th class="ps-4 py-3 py-md-4 text-dark x-small fw-bold tracking-widest border-bottom-0">ID</th>
                                <th class="py-3 py-md-4 text-dark x-small fw-bold tracking-widest border-bottom-0">SUBJECT</th>
                                <th class="py-3 py-md-4 text-dark x-small fw-bold tracking-widest border-bottom-0 text-center d-none d-sm-table-cell">PRIORITY</th>
                                <th class="py-3 py-md-4 text-dark x-small fw-bold tracking-widest border-bottom-0 text-center">STATUS</th>
                                <th class="pe-4 py-3 py-md-4 border-bottom-0"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($grievances)): ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state p-4 p-md-5">
                                            <i class="fas fa-folder-open mb-3"></i>
                                            <p class="x-small">No complaints submitted yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($grievances as $g): 
                                    $statusClass = 'bg-warning shadow-warning';
                                    if($g['status'] == 'Resolved') $statusClass = 'bg-success shadow-success';
                                    if($g['status'] == 'Rejected') $statusClass = 'bg-danger shadow-danger';
                                    if($g['status'] == 'In Progress') $statusClass = 'bg-primary shadow-primary';
                                    
                                    $priorityLabel = 'Normal';
                                    $priorityClass = 'bg-primary';
                                    if($g['priority'] == 'Urgent') { $priorityLabel = 'Urgent'; $priorityClass = 'bg-danger'; }
                                    if($g['priority'] == 'High') { $priorityLabel = 'High'; $priorityClass = 'bg-warning'; }
                                    if($g['priority'] == 'Low') { $priorityLabel = 'Low'; $priorityClass = 'bg-info'; }
                                ?>
                                <tr class="border-bottom border-white border-opacity-5">
                                    <td class="ps-4">
                                        <span class="font-monospace text-primary fw-bold x-small">#<?= htmlspecialchars($g['id']) ?></span>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-bold mb-1 x-small text-truncate" style="max-width: 120px;"><?= htmlspecialchars($g['subject']) ?></div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="x-small text-muted fw-bold text-uppercase d-none d-sm-inline"><?= htmlspecialchars($g['category']) ?></span>
                                            <span class="x-small text-muted"><?= date('M d', strtotime($g['created_at'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center d-none d-sm-table-cell">
                                        <span class="badge rounded-pill <?= $priorityClass ?> bg-opacity-10 text-white-50 border border-white border-opacity-10 px-2 py-1 x-small fw-bold">
                                            <?= $priorityLabel ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1 gap-md-2">
                                            <div class="rounded-circle <?= $statusClass ?> animate-pulse" style="width: 6px; height: 6px;"></div>
                                            <span class="x-small fw-bold text-uppercase text-white-50" style="font-size: 0.6rem;"><?= $g['status'] ?></span>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="track_complaint.php?id=<?= $g['id'] ?>" class="btn btn-link p-0 text-primary x-small fw-bold">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="glass-card p-4 border-0 shadow-lg mb-4">
                <h6 class="text-primary x-small fw-bold text-uppercase tracking-widest mb-4">Tactical Actions</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <a href="notifications.php" class="d-flex flex-column align-items-center justify-content-center p-3 rounded-4 bg-white bg-opacity-5 hover-lift text-decoration-none group">
                            <i class="fas fa-bell text-muted mb-2 group-hover-text-primary"></i>
                            <span class="x-small fw-bold text-dark opacity-75">Alerts</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="settings.php" class="d-flex flex-column align-items-center justify-content-center p-3 rounded-4 bg-white bg-opacity-5 hover-lift text-decoration-none group">
                            <i class="fas fa-cog text-muted mb-2 group-hover-text-primary"></i>
                            <span class="x-small fw-bold text-dark opacity-75">Settings</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Knowledge Base -->
            <div class="glass-card p-4 border-0 shadow-lg bg-primary bg-opacity-10 border border-primary border-opacity-10">
                <h6 class="text-primary x-small fw-bold text-uppercase tracking-widest mb-4 d-flex align-items-center gap-2">
                    <i class="fas fa-lightbulb"></i> Strategic Advice
                </h6>
                <div class="bg-light p-3 rounded-4 mb-3 border border-white border-opacity-5">
                    <p class="text-muted x-small mb-0 italic">"Ensure mission identifiers are accurate to prevent routing delays. Priority Omega should be reserved for catastrophic infrastructure failures."</p>
                </div>
                <button class="btn btn-outline-primary w-100 rounded-pill py-2 x-small fw-bold">ACCESS KNOWLEDGE BASE</button>
            </div>
        </div>
    </div>
</div>

<!-- Using global style.css -->

<?php require_once '../includes/footer.php'; ?>
