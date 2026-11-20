<?php require_once '../functions.php';
require_admin();
$counts = $pdo->query("SELECT COUNT(*) total,SUM(status='Pending') pending,SUM(status='Approved') approved,SUM(status='In Progress') progress,SUM(status='Completed') completed FROM service_requests")->fetch();
$users = $pdo->query('SELECT COUNT(*) c FROM users WHERE role="customer"')->fetch()['c'];
$recent = $pdo->query("
    SELECT s.*, u.name, v.make, v.model
    FROM service_requests s
    LEFT JOIN users u ON u.id = s.user_id
    LEFT JOIN vehicles v ON v.id = s.vehicle_id
    ORDER BY s.created_at DESC
    LIMIT 6
")->fetchAll();
include '../partials/header.php'; ?><div class="admin-banner">
    <p class="eyebrow" style="color:var(--lime)">Admin workspace</p>
    <h1>Good morning, <?= e(explode(' ', current_user()['name'])[0]) ?>.</h1>
    <p>Here is what is happening across the service hub today.</p>
</div>
<div class="grid-3" style="margin-top:20px">
    <div class="stat"><span class="stat-label">Total requests</span>
        <div class="stat-value"><?= e($counts['total'] ?? 0) ?></div><span class="stat-note">All time</span>
    </div>
    <div class="stat"><span class="stat-label">Needs review</span>
        <div class="stat-value"><?= e($counts['pending'] ?? 0) ?></div><span class="stat-note">Pending requests</span>
    </div>
    <div class="stat"><span class="stat-label">Customers</span>
        <div class="stat-value"><?= e($users) ?></div><span class="stat-note">Registered accounts</span>
    </div>
</div>
<div class="panel">
    <div class="page-head" style="margin:0 0 12px">
        <h2>Latest requests</h2><a class="text-link" href="requests.php">Manage all →</a>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php foreach ($recent as $r): ?><tr>
                        <td><?= e($r['name'] ?? 'Customer not found') ?></td>
                        <td>
                            <?= e(trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: 'Vehicle not found') ?>
                        </td>

                        <td><?= e($r['service_type']) ?></td>
                        <td><span class="status <?= status_class($r['status']) ?>"><?= e($r['status']) ?></span></td>
                        <td><a class="text-link" href="requests.php?focus=<?= $r['id'] ?>">Manage</a></td>
                    </tr><?php endforeach;
                        if (!$recent): ?><tr>
                        <td colspan="5" class="empty">No requests yet.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div><?php include '../partials/footer.php'; ?>