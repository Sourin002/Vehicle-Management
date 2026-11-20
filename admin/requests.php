<?php require_once '../functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)$_POST['id'];
    $status = $_POST['status'] ?? '';
    $notes = trim($_POST['admin_notes'] ?? '');
    $allowed = ['Pending', 'Approved', 'In Progress', 'Completed', 'Cancelled'];
    if (in_array($status, $allowed)) {
        $s = $pdo->prepare('UPDATE service_requests SET status=?,admin_notes=? WHERE id=?');
        $s->execute([$status, $notes, $id]);
        flash('success', 'Request status updated.');
    } else flash('error', 'Invalid status selected.');
    redirect('requests.php');
}
$q = trim($_GET['q'] ?? '');
$sort = in_array($_GET['sort'] ?? '', ['preferred_date', 'status', 'created_at']) ? $_GET['sort'] : 'created_at';
$dir = ($_GET['dir'] ?? '') === 'asc' ? 'ASC' : 'DESC';
$sql = "
    SELECT s.*, u.name, u.email, v.make, v.model, v.registration_no
    FROM service_requests s
    LEFT JOIN users u ON u.id = s.user_id
    LEFT JOIN vehicles v ON v.id = s.vehicle_id
    WHERE 1
";
$params = [];
if ($q) {
    $sql .= ' AND (u.name LIKE ? OR s.service_type LIKE ? OR s.status LIKE ? OR v.registration_no LIKE ?)';
    $params = ["%$q%", "%$q%", "%$q%", "%$q%"];
}
$sql .= " ORDER BY $sort $dir";
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();
$page_title = 'Manage requests';
include '../partials/header.php'; ?><div class="page-head">
    <div>
        <p class="eyebrow">Operations</p>
        <h1>All service requests</h1>
        <p>Review, schedule, and close out customer visits.</p>
    </div>
</div>
<div class="panel">
    <div class="search-bar">
        <form style="display:flex;gap:10px"><input name="q" placeholder="Search customer, plate, status..." value="<?= e($q) ?>"><button class="btn secondary">Search</button></form><a class="btn secondary" href="requests.php?sort=preferred_date&dir=asc">Sort by date ↑</a>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer / vehicle</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody><?php foreach ($rows as $r): ?><tr>
                        <td>
                            <b><?= e($r['name'] ?? 'Customer not found') ?></b><br>
                            <small class="muted">
                                <?= e(trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: 'Vehicle not found') ?>
                                · <?= e($r['registration_no'] ?? 'No registration number') ?>
                            </small>
                        </td>
                        <td><?= e($r['service_type']) ?></td>
                        <td><?= date('M d, Y', strtotime($r['preferred_date'])) ?></td>
                        <td><span class="status <?= status_class($r['status']) ?>"><?= e($r['status']) ?></span></td>
                        <td>
                            <form method="post" class="actions"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>"><select name="status" style="padding:7px;border:1px solid var(--line);border-radius:7px;font:inherit;font-size:12px"><?php foreach (['Pending', 'Approved', 'In Progress', 'Completed', 'Cancelled'] as $s): ?><option <?= $r['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select><input name="admin_notes" placeholder="Note" value="<?= e($r['admin_notes'] ?? '') ?>" style="width:110px;padding:7px;border:1px solid var(--line);border-radius:7px"><button class="btn primary small">Save</button><a class="btn secondary small" href="../service_view.php?id=<?= $r['id'] ?>">View</a></form>
                        </td>
                    </tr><?php endforeach;
                        if (!$rows): ?><tr>
                        <td colspan="5" class="empty">No matching requests.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div><?php include '../partials/footer.php'; ?>