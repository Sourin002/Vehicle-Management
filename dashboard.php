<?php require_once 'functions.php';
require_login();
if (current_user()['role'] === 'admin') redirect('admin/index.php');
$uid = current_user()['id'];
$counts = $pdo->prepare("SELECT status,COUNT(*) c FROM service_requests WHERE user_id=? GROUP BY status");
$counts->execute([$uid]);
$stats = ['Pending' => 0, 'Approved' => 0, 'Completed' => 0];
foreach ($counts as $r) {
    if (isset($stats[$r['status']])) $stats[$r['status']] = $r['c'];
}
$stmt = $pdo->prepare('
    SELECT s.*, v.make, v.model, v.registration_no
    FROM service_requests s
    LEFT JOIN vehicles v ON v.id = s.vehicle_id
    WHERE s.user_id = ?
    ORDER BY s.created_at DESC
    LIMIT 5
');
$stmt->execute([$uid]);
$requests = $stmt->fetchAll();
$page_title = 'Your dashboard';
include 'partials/header.php'; ?><div class="page-head">
    <div>
        <p class="eyebrow">Good to see you, <?= e(explode(' ', current_user()['name'])[0]) ?></p>
        <h1>Your service overview</h1>
        <p>Keep an eye on every visit and every mile.</p>
    </div><a class="btn primary" href="service_form.php">+ Request service</a>
</div>
<div class="grid-3">
    <div class="stat"><span class="stat-label">Pending</span>
        <div class="stat-value"><?= $stats['Pending'] ?></div><span class="stat-note">Awaiting review</span>
    </div>
    <div class="stat"><span class="stat-label">Approved</span>
        <div class="stat-value"><?= $stats['Approved'] ?></div><span class="stat-note">Ready for service</span>
    </div>
    <div class="stat"><span class="stat-label">Completed</span>
        <div class="stat-value"><?= $stats['Completed'] ?></div><span class="stat-note">Visits completed</span>
    </div>
</div>
<div class="panel">
    <div class="page-head" style="margin:0 0 12px">
        <h2>Recent service requests</h2><a class="text-link" href="services.php">View all</a>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php if (!$requests): ?><tr>
                        <td colspan="5" class="empty">No service requests yet. Start with your first booking.</td>
                    </tr><?php endif;
                        foreach ($requests as $r): ?><tr>
                        <td>
                            <b><?= e(trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: 'Vehicle not found') ?></b>
                            <br>
                            <small class="muted"><?= e($r['registration_no'] ?? 'No registration number') ?></small>
                        </td>
                        <td><?= e($r['service_type']) ?></td>
                        <td><?= date('M d, Y', strtotime($r['preferred_date'])) ?></td>
                        <td><span class="status <?= status_class($r['status']) ?>"><?= e($r['status']) ?></span></td>
                        <td><a class="text-link" href="service_view.php?id=<?= $r['id'] ?>">Details</a></td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </div>
</div><?php include 'partials/footer.php'; ?>