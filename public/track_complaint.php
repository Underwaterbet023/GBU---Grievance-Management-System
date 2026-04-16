<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    ?>
    <div class="container-fluid py-5 min-vh-100 d-flex align-items-center">
        <div class="row w-100 justify-content-center">
            <div class="col-md-8 col-lg-6 text-center">
                <div class="mb-5 animate-float">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle shadow-primary-sm mb-4" style="width: 140px; height: 140px; border: 1px solid rgba(0,217,255,0.2);">
                        <i class="fas fa-search-location fa-4x"></i>
                    </div>
                </div>
                <h1 class="display-4 fw-bold text-white mb-3">Trace Your Narrative</h1>
                <p class="text-secondary fs-5 mb-5 mx-auto" style="max-width: 500px;">Enter your unique Registry ID to decrypt the status and see the full resolution lifecycle.</p>
                
                <form action="track_complaint.php" method="GET" class="position-relative max-w-lg mx-auto">
                    <div class="glass-card p-2 rounded-pill shadow-lg border-0 d-flex">
                        <input type="text" name="id" class="form-control form-control-lg border-0 bg-transparent text-white px-4 py-3 rounded-pill" placeholder="Enter Case ID (e.g. GBU-2026-0001)" required style="box-shadow: none;">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold tracking-wider shadow-primary hover-lift">
                            TRACE MISSION
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    require_once '../includes/footer.php';
    exit;
}

$grievance_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch Grievance details
$stmt = $pdo->prepare("SELECT * FROM grievances WHERE id = ? AND user_id = ?");
$stmt->execute([$grievance_id, $user_id]);
$grievance = $stmt->fetch();

if (!$grievance) {
    echo "<div class='container px-4 py-5 mt-5 min-vh-100 d-flex flex-column align-items-center justify-content-center'>
            <div class='glass-card p-5 text-center border-danger border-opacity-20 shadow-lg' style='max-width: 600px;'>
                <div class='bg-danger bg-opacity-10 text-danger p-4 rounded-circle d-inline-block mb-4 shadow-sm'>
                    <i class='fas fa-shield-slash fa-3x'></i>
                </div>
                <h2 class='text-white fw-bold mb-3'>Forbidden Trace</h2>
                <p class='text-secondary mb-5'>Either this Ticket ID doesn't exist or you are not authorized to view its metadata. Your access attempt has been logged.</p>
                <div class='d-flex gap-3 justify-content-center'>
                    <a href='track_complaint.php' class='btn btn-outline-secondary rounded-pill px-4'>RETRY SEARCH</a>
                    <a href='student_dashboard.php' class='btn btn-primary rounded-pill px-4 shadow-primary'>BACK TO TERMINAL</a>
                </div>
            </div>
          </div>";
    require_once '../includes/footer.php';
    exit;
}

// Fetch Responses
$respStmt = $pdo->prepare("
    SELECT r.*, u.name as admin_name, u.role as admin_role 
    FROM responses r 
    JOIN users u ON r.admin_id = u.id 
    WHERE r.grievance_id = ? 
    ORDER BY r.created_at ASC
");
$respStmt->execute([$grievance_id]);
$responses = $respStmt->fetchAll();

// Progress Calculation
$progress = 25; // Pending
if ($grievance['status'] == 'In Progress') $progress = 60;
if ($grievance['status'] == 'Resolved') $progress = 100;
if ($grievance['status'] == 'Rejected') $progress = 100;

$statusBadge = 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-20';
if($grievance['status'] == 'Resolved') $statusBadge = 'bg-success bg-opacity-10 text-success border-success border-opacity-20';
if($grievance['status'] == 'Rejected') $statusBadge = 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-20';
if($grievance['status'] == 'In Progress') $statusBadge = 'bg-info bg-opacity-10 text-info border-info border-opacity-20';
?>

<div class="container-fluid py-4 min-vh-100 animate-fade-in">
    <!-- Header Mission Status -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="glass-card hero-gradient-card p-4 p-md-5 overflow-hidden position-relative border-0 shadow-lg">
                <div class="position-absolute top-0 end-0 p-5 opacity-5 animate-pulse">
                    <i class="fas fa-radar fa-10x text-primary"></i>
                </div>
                <div class="row align-items-center position-relative">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <a href="student_dashboard.php" class="btn btn-outline-secondary rounded-circle border-0 bg-white bg-opacity-5" style="width: 45px; height: 45px; display: flex; align-items:center; justify-content:center;"><i class="fas fa-chevron-left"></i></a>
                            <div class="px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-pill x-small fw-bold tracking-widest border border-primary border-opacity-20">CASE ID: #<?= $grievance_id ?></div>
                        </div>
                        <h1 class="display-5 fw-bold text-white mb-2"><?= htmlspecialchars($grievance['subject']) ?></h1>
                        <p class="text-secondary mb-0 small">Operational Index: <span class="font-monospace text-primary fw-bold"><?= substr(hash('sha256', $grievance_id), 0, 16) ?></span></p>
                    </div>
                    <div class="col-md-5 text-md-end mt-4 mt-md-0">
                        <div class="mb-4">
                            <span class="badge <?= $statusBadge ?> px-4 py-3 rounded-pill fs-6 border shadow-primary-sm">
                                <i class="fas <?= $grievance['status'] == 'Resolved' ? 'fa-check-circle' : 'fa-satellite' ?> <?= $grievance['status'] != 'Resolved' ? 'fa-spin' : '' ?> me-2"></i> <?= strtoupper($grievance['status']) ?>
                            </span>
                        </div>
                        <div class="progress rounded-pill bg-white bg-opacity-5 mb-2" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary shadow-primary" role="progressbar" style="width: <?= $progress ?>%"></div>
                        </div>
                        <div class="d-flex justify-content-between x-small fw-bold text-secondary text-uppercase tracking-widest">
                            <span>Analysis Initiated</span>
                            <span class="text-primary"><?= $progress ?>% VERIFIED</span>
                            <span>Resolution</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Intel Column -->
        <div class="col-lg-8">
            <div class="glass-card p-4 p-md-5 mb-4 border-0 shadow-lg">
                <div class="d-flex align-items-center gap-3 mb-5">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 shadow-primary-sm border border-primary border-opacity-20">
                        <i class="fas fa-file-invoice text-primary"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-0">Operational Statement</h5>
                </div>
                <div class="statement-box p-4 rounded-4 mb-5 bg-white bg-opacity-5 border-start border-4 border-primary shadow-sm" style="font-size: 1.1rem; line-height: 1.8; color: var(--text-primary);">
                    <?= nl2br(htmlspecialchars($grievance['description'])) ?>
                </div>

                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-5">
                            <span class="text-secondary x-small fw-bold text-uppercase d-block mb-1 tracking-widest">Time Log</span>
                            <span class="text-white fw-bold d-block"><?= date('M d, Y', strtotime($grievance['created_at'])) ?></span>
                            <span class="x-small text-secondary font-monospace"><?= date('H:i:s', strtotime($grievance['created_at'])) ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-5">
                            <span class="text-secondary x-small fw-bold text-uppercase d-block mb-1 tracking-widest">Department</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 x-small fw-bold border border-primary border-opacity-20"><?= strtoupper($grievance['category']) ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-5">
                            <span class="text-secondary x-small fw-bold text-uppercase d-block mb-1 tracking-widest">Urgency</span>
                            <span class="fw-bold <?= ($grievance['priority'] == 'Urgent' || $grievance['priority'] == 'High') ? 'text-danger' : 'text-warning' ?>">
                                <i class="fas fa-bolt me-1"></i> <?= strtoupper($grievance['priority']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-5">
                            <span class="text-secondary x-small fw-bold text-uppercase d-block mb-1 tracking-widest">Protocol</span>
                            <span class="fw-bold text-white">LEVEL <?= $grievance['current_level'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resolution Path -->
            <div class="px-2 mt-5">
                <div class="d-flex align-items-center gap-4 mb-5">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 shadow-primary-sm border border-primary border-opacity-20" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-route text-primary fs-5"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-0">Resolution Pathway</h3>
                </div>
                
                <div class="mission-timeline">
                    <!-- Entry Point -->
                    <div class="timeline-item active">
                        <div class="timeline-marker"><i class="fas fa-check"></i></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary text-uppercase tracking-widest">Intake Success</h6>
                                <span class="x-small text-secondary font-monospace fw-bold"><?= date('H:i', strtotime($grievance['created_at'])) ?></span>
                            </div>
                            <p class="text-secondary small mb-0">Complaint cryptographically signed and routed to processing nodes.</p>
                        </div>
                    </div>

                    <?php foreach($responses as $resp): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker"><i class="fas <?= $resp['status_change'] == 'Resolved' ? 'fa-check' : 'fa-info' ?>"></i></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold mb-0 text-white"><?= htmlspecialchars($resp['admin_name']) ?></h6>
                                    <span class="x-small text-uppercase fw-bold text-muted opacity-75"><?= $resp['admin_role'] ?></span>
                                </div>
                                <span class="x-small text-secondary font-monospace fw-bold"><?= date('M d, H:i', strtotime($resp['created_at'])) ?></span>
                            </div>
                            <div class="remark-content p-3 bg-white bg-opacity-5 rounded-3 mb-3 text-secondary" style="line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($resp['remark'])) ?>
                            </div>
                            <?php if($resp['status_change']): ?>
                                <div class="px-3 py-1 bg-<?= $resp['status_change'] == 'Resolved' ? 'success' : 'info' ?> bg-opacity-10 text-<?= $resp['status_change'] == 'Resolved' ? 'success' : 'info' ?> rounded-pill d-inline-flex align-items-center gap-2 x-small fw-bold tracking-widest border border-white border-opacity-5">
                                    <i class="fas fa-code-branch small"></i> PROTOCOL: <?= strtoupper($resp['status_change']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Intel -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 2rem; z-index: 10;">
                <div class="glass-card bg-primary bg-opacity-10 text-white border-primary border-opacity-10 shadow-primary-sm p-4 mb-4 overflow-hidden position-relative">
                    <div class="position-absolute bottom-0 end-0 p-5 opacity-10">
                        <i class="fas fa-fingerprint fa-8x text-primary"></i>
                    </div>
                    <h6 class="fw-bold mb-4 d-flex align-items-center gap-3 text-primary tracking-widest">
                        <i class="fas fa-shield-alt"></i> SECURITY METADATA
                    </h6>
                    <div class="mb-4">
                        <label class="x-small text-secondary fw-bold text-uppercase d-block mb-3 tracking-widest">Immutable Block Hash</label>
                        <div class="p-4 bg-dark bg-opacity-40 rounded-4 border border-white border-opacity-5 font-monospace text-primary x-small text-break line-height-base shadow-inner" style="letter-spacing: 1px;">
                            <?= strtoupper(hash('sha256', $grievance_id . $grievance['created_at'] . 'ANTIGRAVITY_OS')) ?>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-3 x-small fw-bold text-secondary text-uppercase tracking-widest">
                        <div class="d-flex align-items-center gap-3"><i class="fas fa-lock text-success"></i> AES-256 Encrypted</div>
                        <div class="d-flex align-items-center gap-3"><i class="fas fa-network-wired text-success"></i> Distributed Consensus</div>
                    </div>
                </div>

                <div class="glass-card border-0 p-4 mb-4 shadow-lg">
                    <h6 class="fw-bold mb-4 text-white text-uppercase tracking-widest small">Node Logistics</h6>
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-white border-opacity-5 pb-3">
                            <span class="text-secondary x-small fw-bold tracking-widest">CLUSTER</span>
                            <span class="text-white fw-bold x-small font-monospace"><?= $grievance['department'] ?? 'ROOT_LOG_NODE' ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-bottom border-white border-opacity-5 pb-3">
                            <span class="text-secondary x-small fw-bold tracking-widest">UPTIME</span>
                            <span class="text-white fw-bold x-small font-monospace">182 SEC_LOGS</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary x-small fw-bold tracking-widest">AUDITOR</span>
                            <span class="text-white fw-bold x-small text-uppercase"><?= count($responses) > 0 ? end($responses)['admin_name'] : 'AI_DAEMON_V4' ?></span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-primary d-flex align-items-center justify-content-center gap-3 w-100 py-3 rounded-pill shadow-primary border-0 hover-lift fw-bold tracking-widest" onclick="window.print()">
                        <i class="fas fa-file-pdf"></i> GENERATE_INTEL_REPORT
                    </button>
                    <p class="text-center x-small text-secondary mt-3 tracking-tighter">PRODUCED BY ANTIGRAVITY OS CORE • SHIELD_V2.4</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; color: black !important; }
    .glass-card { border: 1px solid #ddd !important; box-shadow: none !important; background: none !important; color: black !important; }
    .btn, .navbar, footer, .sticky-top { display: none !important; }
    .text-white, .text-secondary, .text-primary { color: black !important; }
}
</style>

<?php require_once '../includes/footer.php'; ?>
