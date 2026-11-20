<?php require_once 'functions.php';
require_login();
$uid = current_user()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $s = $pdo->prepare('DELETE FROM vehicles WHERE id=? AND user_id=?');
        $s->execute([(int)$_POST['id'], $uid]);
        flash('success', 'Vehicle removed from your garage.');
    } else {
        $make = trim($_POST['make'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $year = (int)($_POST['year'] ?? 0);
        $reg = strtoupper(trim($_POST['registration_no'] ?? ''));
        $color = trim($_POST['color'] ?? '');
        if (!$make || !$model || $year < 1950 || $year > (date('Y') + 1) || !$reg) {
            flash('error', 'Please complete all required vehicle fields.');
        } else {
            try {
                $s = $pdo->prepare('INSERT INTO vehicles(user_id,make,model,year,registration_no,color) VALUES(?,?,?,?,?,?)');
                $s->execute([$uid, $make, $model, $year, $reg, $color]);
                flash('success', 'Vehicle added to your garage.');
            } catch (PDOException $e) {
                flash('error', 'Registration number must be unique.');
            }
        }
    }
    redirect('vehicles.php');
}
$q = trim($_GET['q'] ?? '');
$sql = 'SELECT * FROM vehicles WHERE user_id=?';
$params = [$uid];
if ($q) {
    $sql .= ' AND (make LIKE ? OR model LIKE ? OR registration_no LIKE ?)';
    $params = array_merge($params, ["%$q%", "%$q%", "%$q%"]);
}
$sql .= ' ORDER BY created_at DESC';
$s = $pdo->prepare($sql);
$s->execute($params);
$vehicles = $s->fetchAll();
$page_title = 'My vehicles';
include 'partials/header.php'; ?><div class="page-head">
    <div>
        <p class="eyebrow">Your garage</p>
        <h1>My vehicles</h1>
        <p>Keep the details your service team needs close at hand.</p>
    </div>
</div>
<div class="panel">
    <h2>Add a vehicle</h2>
    <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="field-row">
            <div class="field"><label>Make *</label><input name="make" required placeholder="e.g. Toyota"></div>
            <div class="field"><label>Model *</label><input name="model" required placeholder="e.g. Corolla"></div>
        </div>
        <div class="field-row">
            <div class="field"><label>Year *</label><input type="number" name="year" required min="1950" max="<?= date('Y') + 1 ?>"></div>
            <div class="field"><label>Registration number *</label><input name="registration_no" required placeholder="e.g. ABC-1234"></div>
        </div>
        <div class="field"><label>Color <span class="muted">(optional)</span></label><input name="color" placeholder="e.g. Pearl White"></div><button class="btn primary">Add vehicle</button>
    </form>
</div>
<div class="panel">
    <div class="search-bar">
        <form><input name="q" placeholder="Search make, model, registration..." value="<?= e($q) ?>"></form>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Year</th>
                    <th>Registration</th>
                    <th>Color</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody><?php foreach ($vehicles as $v): ?><tr>
                        <td><b><?= e($v['make'] . ' ' . $v['model']) ?></b></td>
                        <td><?= e($v['year']) ?></td>
                        <td><?= e($v['registration_no']) ?></td>
                        <td><?= e($v['color'] ?: '—') ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Remove this vehicle?')"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $v['id'] ?>"><button class="btn danger small">Delete</button></form>
                        </td>
                    </tr><?php endforeach;
                        if (!$vehicles): ?><tr>
                        <td colspan="5" class="empty">No vehicles found.</td>
                    </tr><?php endif; ?></tbody>
        </table>
    </div>
</div><?php include 'partials/footer.php'; ?>