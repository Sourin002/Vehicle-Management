<?php require_once 'functions.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$sql = 'SELECT s.*,v.make,v.model,v.year,v.registration_no,v.color,u.name,u.email FROM service_requests s JOIN vehicles v ON v.id=s.vehicle_id JOIN users u ON u.id=s.user_id WHERE s.id=?';
$params = [$id];
if (current_user()['role'] !== 'admin') {
    $sql .= ' AND s.user_id=?';
    $params[] = current_user()['id'];
}
$st = $pdo->prepare($sql);
$st->execute($params);
$r = $st->fetch();
if (!$r) {
    flash('error', 'Service request not found.');
    redirect(current_user()['role'] === 'admin' ? 'admin/requests.php' : 'services.php');
}
$page_title = 'Service request details';
include 'partials/header.php'; ?><div class="page-head">
    <div>
        <p class="eyebrow">Request #<?= str_pad($r['id'], 4, '0', STR_PAD_LEFT) ?></p>
        <h1><?= e($r['service_type']) ?></h1>
        <p>Submitted <?= date('M d, Y', strtotime($r['created_at'])) ?></p>
    </div><span class="status <?= status_class($r['status']) ?>"><?= e($r['status']) ?></span>
</div>
<div class="detail-grid">
    <div class="panel">
        <h2>Request details</h2>
        <div class="detail-item"><label>Vehicle</label><b><?= e($r['make'] . ' ' . $r['model']) ?></b>
            <div class="muted"><?= e($r['year'] . ' · ' . $r['registration_no'] . ' · ' . ($r['color'] ?: 'Color not specified')) ?></div>
        </div>
        <div class="detail-item"><label>Preferred date</label><?= date('l, F j, Y', strtotime($r['preferred_date'])) ?></div>
        <div class="detail-item"><label>Customer notes</label><?= nl2br(e($r['notes'] ?: 'No notes added.')) ?></div><?php if ($r['admin_notes']): ?><div class="detail-item"><label>Technician note</label><?= nl2br(e($r['admin_notes'])) ?></div><?php endif; ?>
    </div>
    <div class="panel">
        <h2>Status timeline</h2>
        <div class="detail-item"><label>Current status</label><span class="status <?= status_class($r['status']) ?>"><?= e($r['status']) ?></span></div>
        <p class="muted" style="font-size:13px;line-height:1.6">Our team will update this request as it moves through review, scheduling, and completion.</p><a class="btn secondary" href="<?= current_user()['role'] === 'admin' ? 'admin/requests.php' : 'services.php' ?>">← Back to requests</a>
    </div>
</div><?php include 'partials/footer.php'; ?>