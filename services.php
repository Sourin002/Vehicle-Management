
<?php
require_once 'functions.php';
require_login();

$uid = current_user()['id'];
$q = trim($_GET['q'] ?? '');

$allowedSort = ['preferred_date', 'status', 'service_type'];
$sort = in_array($_GET['sort'] ?? '', $allowedSort, true)
    ? $_GET['sort']
    : 'created_at';

$dir = ($_GET['dir'] ?? '') === 'asc' ? 'ASC' : 'DESC';

$sql = "
    SELECT s.*, v.make, v.model, v.registration_no
    FROM service_requests s
    LEFT JOIN vehicles v ON v.id = s.vehicle_id
    WHERE s.user_id = ?
";

$params = [$uid];

if ($q !== '') {
    $sql .= "
        AND (
            s.service_type LIKE ?
            OR s.status LIKE ?
            OR v.registration_no LIKE ?
        )
    ";

    $params = array_merge($params, [
        "%$q%",
        "%$q%",
        "%$q%"
    ]);
}

$sql .= " ORDER BY s.$sort $dir";

$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();

$page_title = 'Service requests';
include 'partials/header.php';
?>

<div class="page-head">
    <div>
        <p class="eyebrow">Your service history</p>
        <h1>Service requests</h1>
        <p>Track every visit from request to completion.</p>
    </div>
    <a class="btn primary" href="service_form.php">+ New request</a>
</div>

<div class="panel">
    <div class="search-bar">
        <form style="display:flex;gap:10px">
            <input name="q" placeholder="Search requests..." value="<?= e($q) ?>">
            <button class="btn secondary">Search</button>
        </form>

        <a class="btn secondary" href="services.php?sort=preferred_date&dir=asc">
            Sort by date ↑
        </a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Service</th>
                    <th>Preferred date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$rows): ?>
                    <tr>
                        <td colspan="5" class="empty">
                            No service requests match your search.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td>
                            <b>
                                <?= e(trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: 'Vehicle not found') ?>
                            </b>
                            <br>
                            <small class="muted">
                                <?= e($r['registration_no'] ?? 'No registration number') ?>
                            </small>
                        </td>

                        <td><?= e($r['service_type']) ?></td>

                        <td>
                            <?= date('M d, Y', strtotime($r['preferred_date'])) ?>
                        </td>

                        <td>
                            <span class="status <?= status_class($r['status']) ?>">
                                <?= e($r['status']) ?>
                            </span>
                        </td>

                        <td>
                            <a class="text-link" href="service_view.php?id=<?= (int)$r['id'] ?>">
                                View detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
