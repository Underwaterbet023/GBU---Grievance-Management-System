<?php
// admin_reports_tab.php
if (!isset($pdo)) require_once '../../includes/db.php';
?>

<div class="animate__animated animate__fadeIn">
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold mb-1">Grievance Velocity</h5>
                        <p class="text-muted x-small mb-0">Trend of submitted vs resolved over time</p>
                    </div>
                    <div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">
                        LAST 7 DAYS
                    </div>
                </div>
                <div class="chart-container-responsive" style="height: 300px;">
                    <canvas id="detailedTrendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold mb-3">Resolution Efficiency</h5>
                <div id="resolutionEfficiency" class="text-center py-4">
                    <div class="radial-stat-wrap mb-4">
                        <div class="radial-stat" style="--percent: <?= $effStats['resRate'] ?>; --color: #10b981;">
                            <div class="radial-value"><?= $effStats['resRate'] ?>%</div>
                        </div>
                        <p class="mt-3 x-small text-muted fw-bold text-uppercase tracking-widest">Resolution Rate</p>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-5">
                                <h4 class="fw-bold text-white mb-0"><?= $effStats['avgResponse'] ?>d</h4>
                                <span class="x-small text-muted text-uppercase fw-bold">Avg Response</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-5">
                                <h4 class="fw-bold text-white mb-0"><?= $effStats['avgResolve'] ?>d</h4>
                                <span class="x-small text-muted text-uppercase fw-bold">Avg Resolve</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-4">Top Performing Departments</h5>
                <?php
                // Genuine department statistics
                $deptQuery = $pdo->query("
                    SELECT 
                        department, 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved
                    FROM grievances 
                    WHERE department IS NOT NULL AND department != ''
                    GROUP BY department
                    ORDER BY total DESC
                    LIMIT 5
                ");
                $depts = $deptQuery->fetchAll(PDO::FETCH_ASSOC);
                $colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                
                if (empty($depts)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted x-small">No department data available yet.</p>
                    </div>
                <?php else: 
                    foreach ($depts as $index => $d):
                        $pct = $d['total'] > 0 ? round(($d['resolved'] / $d['total']) * 100) : 0;
                        $color = $colors[$index % count($colors)];
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between x-small mb-1">
                        <span class="fw-bold text-white"><?= htmlspecialchars($d['department']) ?></span>
                        <span class="text-muted"><?= $pct ?>% Resolved (<?= $d['resolved'] ?>/<?= $d['total'] ?>)</span>
                    </div>
                    <div class="progress bg-white bg-opacity-5" style="height: 6px; border-radius: 100px;">
                        <div class="progress-bar" style="width: <?= $pct ?>%; background: <?= $color ?>; border-radius: 100px;"></div>
                    </div>
                </div>
                <?php endforeach; 
                endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-card p-4 d-flex flex-column">
                <h5 class="fw-bold mb-4">Export Data</h5>
                <p class="text-muted small mb-4">Generate comprehensive CSV or PDF reports for off-line audit and university submission.</p>
                <div class="mt-auto d-flex gap-3">
                    <button type="button" onclick="exportData('csv')" class="btn btn-primary-custom flex-grow-1"><i class="fas fa-file-csv me-2"></i>Export CSV</button>
                    <button type="button" onclick="exportData('pdf')" class="btn btn-outline-light border-white border-opacity-10 flex-grow-1"><i class="fas fa-file-pdf me-2"></i>Export PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportData(type) {
    if (type === 'pdf') {
        // Use window.print() but wrap it in a function that hides sidebar/header temporarily if needed
        // For a better PDF, usually we'd use a server-side lib like DomPDF or FPDF, 
        // but since we want current charts, window.print is the way to capture Canvas.
        window.print();
    } else if (type === 'csv') {
        // Simple CSV generation from current department table as example
        let csv = "Department,Total,Resolved,Percent\n";
        document.querySelectorAll('.glass-card table tr').forEach((row, i) => {
            if (i === 0) return; // skip header
            let cols = row.querySelectorAll('td');
            if (cols.length > 0) {
                let dept = cols[0].innerText.replace(/,/g, '');
                let total = cols[1].innerText.split(' ')[0]; // Handle badge text
                let resolved = cols[1].innerText.match(/\((\d+)\//) ? cols[1].innerText.match(/\((\d+)\//)[1] : '0';
                let pct = cols[2].innerText;
                csv += `${dept},${total},${resolved},${pct}\n`;
            }
        });
        
        let blob = new Blob([csv], { type: 'text/csv' });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.setAttribute('href', url);
        a.setAttribute('download', 'gms_report_' + new Date().toISOString().slice(0,10) + '.csv');
        a.click();
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('detailedTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_keys($trend)) ?>,
            datasets: [
                {
                    label: 'Submitted',
                    data: <?= json_encode(array_values($trend)) ?>,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 4
                },
                {
                    label: 'Resolved',
                    data: <?= json_encode(array_values($trendResolved)) ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#94a3b8',
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: { size: 10, weight: 'bold' }
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#94a3b8',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: true
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                    ticks: { color: '#64748b', font: { size: 10 } }
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 10 } }
                }
            }
        }
    });
});
</script>

<style>
.radial-stat { 
    width: 120px; height: 120px; border-radius: 50%; 
    background: conic-gradient(var(--color) calc(var(--percent) * 1%), rgba(255,255,255,0.05) 0);
    display: flex; align-items: center; justify-content: center; position: relative; margin: 0 auto;
}
.radial-stat::after { content: ''; width: 100px; height: 100px; background: #0f172a; border-radius: 50%; position: absolute; }
.radial-value { position: relative; z-index: 1; font-size: 1.5rem; font-weight: 800; color: #fff; }
</style>
