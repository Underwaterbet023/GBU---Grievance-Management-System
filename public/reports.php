<?php
$pageTitle = 'Reports & Analytics';
require_once '../includes/db.php';
require_once '../includes/header.php';
requireAnyAdmin();

// Summary stats
$totalComplaints  = $pdo->query("SELECT COUNT(*) FROM grievances")->fetchColumn();
$resolvedCount    = $pdo->query("SELECT COUNT(*) FROM grievances WHERE status='Resolved'")->fetchColumn();
$pendingCount     = $pdo->query("SELECT COUNT(*) FROM grievances WHERE status='Pending'")->fetchColumn();
$avgResolutionDays = $pdo->query("SELECT AVG(DATEDIFF(updated_at, created_at)) FROM grievances WHERE status='Resolved'")->fetchColumn();

// Monthly trend (last 12 months)
$monthly = $pdo->query("SELECT DATE_FORMAT(created_at,'%b %Y') as month,
    YEAR(created_at) as yr, MONTH(created_at) as mo, COUNT(*) as cnt
    FROM grievances WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at,'%b %Y')
    ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC")->fetchAll();

// Category breakdown
$categories = $pdo->query("SELECT category, COUNT(*) as cnt FROM grievances GROUP BY category ORDER BY cnt DESC")->fetchAll();

// Department breakdown
$departments = $pdo->query("SELECT department, COUNT(*) as cnt FROM grievances GROUP BY department ORDER BY cnt DESC LIMIT 8")->fetchAll();

// Priority breakdown
$priorities = $pdo->query("SELECT priority, COUNT(*) as cnt FROM grievances GROUP BY priority")->fetchAll();

// Status breakdown
$statuses = $pdo->query("SELECT status, COUNT(*) as cnt FROM grievances GROUP BY status")->fetchAll();
?>

<div class="container-fluid px-0 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 mb-md-5 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-white mb-1 h3 h2-md" style="letter-spacing:-0.5px;">
                <i class="fas fa-chart-bar text-primary me-2 me-md-3"></i>Analytics Core
            </h2>
            <p class="text-muted x-small mb-0 opacity-75">Comprehensive insights across all grievance records.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 fw-bold x-small">
            <i class="fas fa-download me-2"></i>EXPORT REPORT
        </button>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 g-md-4 mb-5">
        <?php
        $kpis = [
            ['Total Filed',      $totalComplaints, 'fa-folder-open', 'primary', 'Lifetime'],
            ['Resolved',         $resolvedCount,   'fa-circle-check','success', number_format($totalComplaints ? ($resolvedCount/$totalComplaints*100) : 0, 0) . '% Rate'],
            ['Pending',          $pendingCount,    'fa-clock',       'warning', 'Active'],
            ['Avg Closing',      round($avgResolutionDays ?? 0, 1).'d', 'fa-hourglass-half', 'info', 'Lead Time'],
        ];
        foreach ($kpis as [$label, $value, $icon, $color, $sub]):
        ?>
        <div class="col-6 col-xl-3">
            <div class="glass-card p-3 p-md-4 border-0 shadow-lg h-100">
                <div class="d-flex align-items-center justify-content-between mb-2 mb-md-3">
                    <div class="stat-icon d-none d-sm-flex" style="background:rgba(var(--<?= $color ?>-rgb,0.15) , 0.15);width:40px;height:40px;border-radius:10px;align-items:center;justify-content:center;">
                        <i class="fas <?= $icon ?> text-<?= $color ?> small"></i>
                    </div>
                    <span class="badge bg-<?= $color ?>-subtle text-<?= $color ?> px-2 py-1 rounded-pill x-small fw-bold"><?= $sub ?></span>
                </div>
                <div class="h3 h1-md fw-bold text-white mb-1"><?= $value ?></div>
                <div class="text-muted x-small text-uppercase tracking-widest fw-bold"><?= $label ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-4">
        <!-- Monthly Trend -->
        <div class="col-xl-8">
            <div class="glass-card p-4 border-0 shadow-lg h-100">
                <h6 class="fw-bold text-white mb-4 x-small text-uppercase tracking-widest">
                    <i class="fas fa-chart-line text-primary me-2"></i>Grievance Velocity
                </h6>
                <div class="chart-container-responsive" style="height: 250px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Status Donut -->
        <div class="col-xl-4">
            <div class="glass-card p-4 border-0 shadow-lg h-100">
                <h6 class="fw-bold text-white mb-4 x-small text-uppercase tracking-widest">
                    <i class="fas fa-circle-half-stroke text-success me-2"></i>Status Distribution
                </h6>
                <div class="chart-container-responsive" style="height: 250px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-4">
        <!-- Category Bar -->
        <div class="col-xl-6">
            <div class="glass-card p-4 border-0 shadow-lg h-100">
                <h6 class="fw-bold text-white mb-4 text-uppercase tracking-widest">
                    <i class="fas fa-tags text-warning me-2"></i>By Category
                </h6>
                <canvas id="categoryChart" height="180"></canvas>
            </div>
        </div>
        <!-- Priority Pie -->
        <div class="col-xl-3">
            <div class="glass-card p-4 border-0 shadow-lg h-100">
                <h6 class="fw-bold text-white mb-4 text-uppercase tracking-widest">
                    <i class="fas fa-fire text-danger me-2"></i>By Priority
                </h6>
                <canvas id="priorityChart" height="200"></canvas>
            </div>
        </div>
        <!-- Department Bar -->
        <div class="col-xl-3">
            <div class="glass-card p-4 border-0 shadow-lg h-100">
                <h6 class="fw-bold text-white mb-4 text-uppercase tracking-widest">
                    <i class="fas fa-building text-info me-2"></i>Top Departments
                </h6>
                <canvas id="deptChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Departments Table -->
    <div class="glass-card border-0 shadow-lg overflow-hidden">
        <div class="p-4 border-bottom" style="border-color:rgba(255,255,255,0.05)!important;">
            <h5 class="fw-bold text-white mb-0"><i class="fas fa-building me-2 text-info"></i>Department Breakdown</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-dark-custom mb-0">
                <thead><tr><th>Department</th><th>Complaints</th><th>Share</th><th>Progress</th></tr></thead>
                <tbody>
                <?php foreach ($departments as $d): $pct = $totalComplaints ? round($d['cnt']/$totalComplaints*100,1) : 0; ?>
                <tr>
                    <td class="fw-semibold text-white"><?= htmlspecialchars($d['department'] ?: 'Unspecified') ?></td>
                    <td><span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill"><?= $d['cnt'] ?></span></td>
                    <td class="text-muted"><?= $pct ?>%</td>
                    <td style="min-width:160px;">
                        <div class="progress" style="height:6px;background:rgba(255,255,255,0.05);border-radius:99px;">
                            <div class="progress-bar bg-primary" style="width:<?= $pct ?>%;border-radius:99px;"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
[data-theme="light"] canvas { filter: none; }
</style>

<script>
// PHP data embedded at render time
const monthlyLabels = <?= json_encode(array_column($monthly, 'month')) ?>;
const monthlyCnt    = <?= json_encode(array_column($monthly, 'cnt')) ?>;
const statusLabels  = <?= json_encode(array_column($statuses,   'status')) ?>;
const statusCnts    = <?= json_encode(array_column($statuses,   'cnt')) ?>;
const catLabels     = <?= json_encode(array_column($categories, 'category')) ?>;
const catCnts       = <?= json_encode(array_column($categories, 'cnt')) ?>;
const prioLabels    = <?= json_encode(array_column($priorities, 'priority')) ?>;
const prioCnts      = <?= json_encode(array_column($priorities, 'cnt')) ?>;
const deptLabels    = <?= json_encode(array_column($departments,'department')) ?>;
const deptCnts      = <?= json_encode(array_column($departments,'cnt')) ?>;

// Wait for Chart.js (loaded in footer) before initializing
window.addEventListener('load', function() {
    const isDark     = document.documentElement.getAttribute('data-theme') !== 'light';
    const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const tickColor  = isDark ? '#94a3b8' : '#64748b';
    const legendColor = isDark ? '#cbd5e1' : '#374151';

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { labels: { color: legendColor, font:{size:12} } } }
    };

    // Monthly Trend
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Complaints Filed',
                data: monthlyCnt,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.15)',
                fill: true,
                tension: 0.45,
                pointBackgroundColor: '#6366f1',
                pointRadius: 5,
                pointHoverRadius: 8,
            }]
        },
        options: { ...baseOptions, scales: {
            x: { ticks:{color:tickColor}, grid:{color:gridColor} },
            y: { ticks:{color:tickColor}, grid:{color:gridColor}, beginAtZero:true }
        }}
    });

    // Status Donut
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCnts,
                backgroundColor: ['#6366f1','#10b981','#f59e0b','#ef4444','#06b6d4'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: { ...baseOptions, cutout: '70%' }
    });

    // Category Bar
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: catLabels,
            datasets: [{
                label: 'Complaints',
                data: catCnts,
                backgroundColor: [
                    'rgba(99,102,241,0.8)','rgba(16,185,129,0.8)','rgba(245,158,11,0.8)',
                    'rgba(239,68,68,0.8)','rgba(6,182,212,0.8)','rgba(139,92,246,0.8)',
                    'rgba(236,72,153,0.8)','rgba(249,115,22,0.8)',
                ],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: { ...baseOptions, indexAxis: 'y', scales: {
            x: { ticks:{color:tickColor}, grid:{color:gridColor} },
            y: { ticks:{color:tickColor}, grid:{display:false} }
        }, plugins: { ...baseOptions.plugins, legend:{display:false} } }
    });

    // Priority Doughnut
    new Chart(document.getElementById('priorityChart'), {
        type: 'doughnut',
        data: {
            labels: prioLabels,
            datasets: [{
                data: prioCnts,
                backgroundColor: ['#ef4444','#f59e0b','#10b981','#6366f1'],
                borderWidth:0, hoverOffset:8,
            }]
        },
        options: { ...baseOptions, cutout:'65%' }
    });

    // Dept Bar
    new Chart(document.getElementById('deptChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label:'Complaints',
                data: deptCnts,
                backgroundColor:'rgba(6,182,212,0.75)',
                borderRadius:6, borderSkipped:false,
            }]
        },
        options: { ...baseOptions, indexAxis:'y', scales:{
            x:{ticks:{color:tickColor},grid:{color:gridColor}},
            y:{ticks:{color:tickColor, font:{size:10}},grid:{display:false}}
        }, plugins:{...baseOptions.plugins, legend:{display:false}} }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
