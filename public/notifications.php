<?php
require_once '../includes/db.php';
require_once '../includes/header.php'; // Checks login

$userId = $_SESSION['user_id'];

// Get all notifications for this user
$stmt = $pdo->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll();

// Mark all as read after fetching
if (!empty($notifications)) {
    $markRead = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    $markRead->execute([$userId]);
}
?>

<div class="container-fluid animate__animated animate__fadeIn">
    <div class="row mb-5">
        <div class="col-12">
            <h6 class="text-primary fw-bold text-uppercase mb-2" style="letter-spacing: 2px;">Inbox</h6>
            <h2 class="display-6 fw-bold mb-0">System Notifications</h2>
            <p class="text-muted mb-0">Important updates regarding your grievance status and university circulars.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="custom-card p-0 overflow-hidden border-0 shadow-lg" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
                <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0"><i class="fas fa-bell me-2 text-primary"></i> Notifications (<?= count($notifications) ?>)</h5>
                    <?php if(!empty($notifications)): ?>
                        <button class="btn btn-link text-muted text-decoration-none small fw-bold" onclick="location.reload()"><i class="fas fa-sync-alt me-1"></i> Refresh</button>
                    <?php endif; ?>
                </div>

                <div class="notifications-list">
                    <?php if(empty($notifications)): ?>
                        <div class="text-center py-5">
                            <div class="mb-4 d-inline-block p-4 rounded-circle bg-light">
                                <i class="fas fa-bell-slash fa-3x text-muted opacity-50"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Zen Mode</h5>
                            <p class="text-muted mb-0">You're all caught up! No new updates at the moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($notifications as $n): ?>
                            <div class="notification-item p-4 border-bottom position-relative <?= !$n['is_read'] ? 'unread' : '' ?>">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="icon-blob">
                                            <i class="fas <?= !$n['is_read'] ? 'fa-bolt animate__animated animate__pulse animate__infinite' : 'fa-check' ?>"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="small fw-bold text-primary text-uppercase" style="letter-spacing: 1px;">Update</span>
                                            <span class="small text-muted"><i class="far fa-clock me-1"></i> <?= date('d M, h:i A', strtotime($n['created_at'])) ?></span>
                                        </div>
                                        <p class="mb-0 text-dark fw-medium" style="line-height: 1.6;"><?= htmlspecialchars($n['message']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    transition: all 0.3s ease;
    cursor: default;
    background: #fff;
}
.notification-item:hover {
    background: #f8f9ff;
}
.notification-item.unread {
    background: #f0f4ff;
}
.notification-item.unread::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--primary-purple);
}
.icon-blob {
    width: 45px;
    height: 45px;
    background: var(--primary-purple);
    color: #fff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    box-shadow: 0 4px 10px rgba(58, 35, 147, 0.2);
}
.notification-item:not(.unread) .icon-blob {
    background: #e9ecef;
    color: #adb5bd;
    box-shadow: none;
}
</style>

<?php require_once '../includes/footer.php'; ?>
