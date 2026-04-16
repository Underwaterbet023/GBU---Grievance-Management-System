<?php
ob_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

requireAnyAdmin();

// Admin stats filtering
$condition = "1=1";
$params = [];
if ($_SESSION['role'] === 'Department Admin') {
    $condition = "department = ? AND current_level = 'Department'";
    $params[] = $_SESSION['department'];
} elseif ($_SESSION['role'] === 'College Admin') {
    $condition = "department = ? AND current_level = 'College'";
    $params[] = $_SESSION['department'];
} elseif ($_SESSION['role'] === 'University Admin') {
    $condition = "current_level = 'University'";
}

// 1. Summary Stats
$statsQuery = $pdo->prepare("SELECT status, COUNT(*) as count FROM grievances WHERE $condition GROUP BY status");
$statsQuery->execute($params);
$stats = ['Pending' => 0, 'In Progress' => 0, 'Resolved' => 0, 'Rejected' => 0, 'Total' => 0];
while($row = $statsQuery->fetch()) {
    $stats[$row['status']] = $row['count'];
    $stats['Total'] += $row['count'];
}

// 2. Category Distribution (Doughnut Chart)
$catQuery = $pdo->prepare("SELECT category, COUNT(*) as count FROM grievances WHERE $condition GROUP BY category");
$catQuery->execute($params);
$categoryData = $catQuery->fetchAll(PDO::FETCH_ASSOC);

// 3. 7-Day Trend — Submitted + Resolved per day (multi-line)
$trend = [];
$trendResolved = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('D', strtotime($date));
    $trend[$label] = 0;
    $trendResolved[$label] = 0;
}

$trendQuery = $pdo->prepare("
    SELECT DATE(created_at) as day, COUNT(*) as count 
    FROM grievances 
    WHERE $condition AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
    GROUP BY DATE(created_at) 
    ORDER BY day ASC
");
$trendQuery->execute($params);
foreach($trendQuery->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $label = date('D', strtotime($r['day']));
    if(isset($trend[$label])) $trend[$label] = $r['count'];
}

$resolvedQuery = $pdo->prepare("
    SELECT DATE(updated_at) as day, COUNT(*) as count 
    FROM grievances 
    WHERE $condition AND status='Resolved' AND updated_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
    GROUP BY DATE(updated_at)
");
$resolvedQuery->execute($params);
foreach($resolvedQuery->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $label = date('D', strtotime($r['day']));
    if(isset($trendResolved[$label])) $trendResolved[$label] = $r['count'];
}

// 4. Efficiency Stats (Grievance Velocity & Resolution Efficiency)
// - Avg Response Time: Time from creation to 'In Progress' status update
// - Avg Resolution Time: Time from creation to 'Resolved' status update
// - Resolution Rate: Resolved / Total (%)
$effStats = [
    'avgResponse' => '0.0',
    'avgResolve'  => '0.0',
    'resRate'     => 0
];

// Calculate Avg Resolution Time (in days)
$resTimeQuery = $pdo->prepare("
    SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) / 24 as avg_days 
    FROM grievances 
    WHERE $condition AND status = 'Resolved' AND updated_at IS NOT NULL
");
$resTimeQuery->execute($params);
$resTime = $resTimeQuery->fetchColumn();
if($resTime) $effStats['avgResolve'] = round($resTime, 1);

// Calculate Avg Response Time (Approximated by time to first update if 'In Progress' or 'Resolved')
$respTimeQuery = $pdo->prepare("
    SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) / 24 as avg_days 
    FROM grievances 
    WHERE $condition AND status IN ('In Progress', 'Resolved') AND updated_at IS NOT NULL
");
$respTimeQuery->execute($params);
$respTime = $respTimeQuery->fetchColumn();
if($respTime) $effStats['avgResponse'] = round($respTime, 1);

// Resolution Rate
if($stats['Total'] > 0) {
    $effStats['resRate'] = round(($stats['Resolved'] / $stats['Total']) * 100);
}

// 5. Recent Activities
$recentStmt = $pdo->prepare("
    SELECT g.*, u.name as student_name 
    FROM grievances g 
    JOIN users u ON g.user_id = u.id 
    WHERE $condition 
    ORDER BY g.created_at DESC 
    LIMIT 6
");
$recentStmt->execute($params);
$recentGrievances = $recentStmt->fetchAll();
?>


<!-- Premium Dashboard Header -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="glass-card p-3 p-md-4 border-0 shadow-lg d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 p-2 p-md-3 rounded-4 border border-primary border-opacity-20 d-none d-sm-block">
                    <i class="fas fa-shield-alt text-primary"></i>
                </div>
                <div>
                    <h2 class="h5 h4-md fw-bold text-white mb-0" style="letter-spacing: -0.5px;">Admin Central</h2>
                </div>
            </div>
            
            <div class="d-flex gap-2 p-1 bg-white bg-opacity-5 rounded-pill-responsive border border-white border-opacity-5 overflow-x-auto-mobile justify-content-start">
                <a href="?tab=overview" class="btn btn-tab-pill <?= !isset($_GET['tab']) || $_GET['tab'] === 'overview' ? 'active' : '' ?>">
                    <i class="fas fa-home-alt me-1"></i><span class="x-small">Home</span>
                </a>
                <a href="?tab=users" class="btn btn-tab-pill <?= isset($_GET['tab']) && $_GET['tab'] === 'users' ? 'active' : '' ?>">
                    <i class="fas fa-users-cog me-1"></i><span class="x-small">Users</span>
                </a>
                <a href="?tab=reports" class="btn btn-tab-pill <?= isset($_GET['tab']) && $_GET['tab'] === 'reports' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line me-1"></i><span class="x-small">Stats</span>
                </a>
                <a href="?tab=escalations" class="btn btn-tab-pill <?= isset($_GET['tab']) && $_GET['tab'] === 'escalations' ? 'active' : '' ?>">
                    <i class="fas fa-exclamation-triangle me-1"></i><span class="x-small">Alerts</span>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Chart.js must be loaded before tabs that use it -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="dashboard-viewport mt-5">
    <?php
    $tab = $_GET['tab'] ?? 'overview';
    switch ($tab) {
        case 'users':
            include 'tabs/admin_users_tab.php';
            break;
        case 'reports':
            include 'tabs/admin_reports_tab.php';
            break;
        case 'escalations':
            include 'tabs/admin_escalations_tab.php';
            break;
        case 'overview':
        default:
            ?>
            <div class="row g-2 g-md-4 mb-4">
                <!-- Overview Quick Stats -->
                <div class="col-6 col-xl-3">
                    <div class="card stat-card-vibrant bg-grad-cyan border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body p-3 p-md-4">
                            <i class="fas fa-folder-open bg-icon d-none d-sm-block"></i>
                            <h6 class="card-subtitle text-uppercase mb-1 mb-md-2 tracking-widest text-white opacity-75 x-small">TOTAL</h6>
                            <h2 class="h3 fw-bold mb-0 text-white"><?= number_format($stats['Total']) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card stat-card-vibrant bg-grad-orange border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body p-3 p-md-4">
                            <i class="fas fa-spinner bg-icon d-none d-sm-block"></i>
                            <h6 class="card-subtitle text-uppercase mb-1 mb-md-2 tracking-widest text-white opacity-75 x-small">ACTIVE</h6>
                            <h2 class="h3 fw-bold mb-0 text-white"><?= number_format($stats['In Progress'] + $stats['Pending']) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card stat-card-vibrant bg-grad-green border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body p-3 p-md-4">
                            <i class="fas fa-check-circle bg-icon d-none d-sm-block"></i>
                            <h6 class="card-subtitle text-uppercase mb-1 mb-md-2 tracking-widest text-white opacity-75 x-small">DONE</h6>
                            <h2 class="h3 fw-bold mb-0 text-white"><?= number_format($stats['Resolved']) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="card stat-card-vibrant bg-grad-red border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body p-3 p-md-4">
                            <i class="fas fa-exclamation-circle bg-icon d-none d-sm-block"></i>
                            <h6 class="card-subtitle text-uppercase mb-1 mb-md-2 tracking-widest text-white opacity-75 x-small">FAIL</h6>
                            <h2 class="h3 fw-bold mb-0 text-white"><?= number_format($stats['Rejected']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="glass-card p-0 overflow-hidden">
                        <div class="p-4 border-bottom border-white border-opacity-5 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold m-0"><i class="fas fa-clock-rotate-left me-2 text-primary"></i>Recent Tickets</h5>
                            <a href="manage_complaints.php" class="btn btn-link text-primary p-0 x-small fw-bold text-decoration-none">Manage All <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle m-0 table-hover">
                                <thead class="x-small text-uppercase opacity-50">
                                    <tr>
                                        <th class="ps-4">Ticket Info</th>
                                        <th class="d-none d-md-table-cell">Student</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recentGrievances as $g): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="small fw-bold text-white text-truncate" style="max-width: 180px;">#<?= $g['id'] ?> - <?= htmlspecialchars($g['subject']) ?></div>
                                            <div class="x-small text-muted"><?= date('M d, Y', strtotime($g['created_at'])) ?></div>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <span class="x-small fw-bold text-white text-opacity-75"><?= htmlspecialchars($g['student_name']) ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'bg-warning shadow-warning';
                                            if($g['status'] == 'Resolved') $badgeClass = 'bg-success shadow-success';
                                            if($g['status'] == 'Rejected') $badgeClass = 'bg-danger shadow-danger';
                                            if($g['status'] == 'In Progress') $badgeClass = 'bg-primary shadow-primary';
                                            ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle <?= $badgeClass ?> animate-pulse" style="width: 8px; height: 8px;"></div>
                                                <span class="x-small fw-bold text-uppercase text-white-50 d-none d-sm-inline"><?= $g['status'] ?></span>
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="manage_complaints.php?search=<?= $g['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 x-small fw-bold">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="glass-card p-4">
                        <h6 class="fw-bold mb-4"><i class="fas fa-layer-group me-2 text-violet"></i>System Load</h6>
                        <div style="height: 220px; position: relative;">
                            <canvas id="categoryChartOverview"></canvas>
                        </div>
                        <div class="mt-4 pt-4 border-top border-white border-opacity-5">
                            <div class="d-flex justify-content-between x-small mb-2">
                                <span class="text-muted">Database Integrity</span>
                                <span class="text-success fw-bold">Optimum</span>
                            </div>
                            <div class="d-flex justify-content-between x-small">
                                <span class="text-muted">Response Health</span>
                                <span class="text-primary fw-bold">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('categoryChartOverview').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode(array_column($categoryData, 'category')) ?>,
                        datasets: [{
                            data: <?= json_encode(array_column($categoryData, 'count')) ?>,
                            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 0,
                            cutout: '80%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });
            });
            </script>
            <?php
            break;
    }
    ?>
</div>



<style>
.dashboard-icon-bg { width: 54px; height: 54px; background: rgba(99,102,241,0.15); border-radius: 16px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(99,102,241,0.1); }
.btn-tab-pill { 
    padding: 8px 20px; 
    font-weight: 700; 
    font-size: 0.8rem; 
    border-radius: 100px; 
    color: var(--text-muted); 
    border: none; 
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    text-decoration: none; 
    display: inline-flex; 
    align-items: center; 
    white-space: nowrap;
}
.btn-tab-pill:hover { 
    color: var(--text-primary); 
    background: rgba(255,255,255,0.1); 
}
[data-theme="light"] .btn-tab-pill:hover {
    background: rgba(0,0,0,0.05);
}
.btn-tab-pill.active { 
    background: var(--primary-indigo); 
    color: #fff !important; 
    box-shadow: 0 4px 15px rgba(99,102,241,0.4); 
}
.stat-icon-sm { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.dashboard-viewport { min-height: 600px; }
.bg-grad-red { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); }
</style>

<?php require_once '../includes/footer.php'; ?>
