<?php require_once '../functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)$_POST['id'];
    $role = $_POST['role'] ?? 'customer';
    if (in_array($role, ['customer', 'admin']) && $id !== current_user()['id']) {
        $s = $pdo->prepare('UPDATE users SET role=? WHERE id=?');
        $s->execute([$role, $id]);
        flash('success', 'User role updated.');
    }
    redirect('users.php');
}
$q = trim($_GET['q'] ?? '');
$sql = 'SELECT u.*,COUNT(s.id) requests FROM users u LEFT JOIN service_requests s ON s.user_id=u.id WHERE 1';
$params = [];
if ($q) {
    $sql .= ' AND (u.name LIKE ? OR u.email LIKE ?)';
    $params = ["%$q%", "%$q%"];
}
$sql .= ' GROUP BY u.id ORDER BY u.created_at DESC';
$st = $pdo->prepare($sql);
$st->execute($params);
$users = $st->fetchAll();
$page_title = 'Manage users';
include '../partials/header.php'; ?><div class="page-head">
    <div>
        <p class="eyebrow">Administration</p>
        <h1>User accounts</h1>
        <p>View customers and manage access roles.</p>
    </div>
</div>
<div class="panel">
    <div class="search-bar">
        <form><input name="q" placeholder="Search name or email..." value="<?= e($q) ?>"></form>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Requests</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody><?php foreach ($users as $u): ?><tr>
                        <td><span class="user-chip"><span class="avatar"><?= e(strtoupper(substr($u['name'], 0, 1))) ?></span><span><b><?= e($u['name']) ?></b><br><small class="muted"><?= e($u['email']) ?></small></span></span></td>
                        <td><?= e($u['phone'] ?: '—') ?></td>
                        <td><span class="status <?= $u['role'] === 'admin' ? 'approved' : 'completed' ?>"><?= e(ucfirst($u['role'])) ?></span></td>
                        <td><?= e($u['requests']) ?></td>
                        <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td><?php if ($u['id'] !== current_user()['id']): ?><form method="post" class="actions"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= $u['id'] ?>"><select name="role" style="padding:7px;border:1px solid var(--line);border-radius:7px">
                                        <option value="customer" <?= $u['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select><button class="btn secondary small">Update</button></form><?php else: ?><span class="muted">Current user</span><?php endif; ?></td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </div>
</div><?php include '../partials/footer.php'; ?>