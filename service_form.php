<?php require_once 'functions.php';
require_login();
$uid = current_user()['id'];
$v = $pdo->prepare('SELECT * FROM vehicles WHERE user_id=? ORDER BY make,model');
$v->execute([$uid]);
$vehicles = $v->fetchAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $vehicle = (int)($_POST['vehicle_id'] ?? 0);
    $type = trim($_POST['service_type'] ?? '');
    $date = $_POST['preferred_date'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    $valid = $pdo->prepare('SELECT id FROM vehicles WHERE id=? AND user_id=?');
    $valid->execute([$vehicle, $uid]);
    if (!$valid->fetch()) $errors[] = 'Please choose one of your vehicles.';
    if (!in_array($type, service_types())) $errors[] = 'Please choose a valid service type.';
    if (!validate_date($date) || $date < date('Y-m-d')) $errors[] = 'Please choose a valid future date.';
    if (!$errors) {
        $s = $pdo->prepare('INSERT INTO service_requests(user_id,vehicle_id,service_type,preferred_date,notes) VALUES(?,?,?,?,?)');
        $s->execute([$uid, $vehicle, $type, $date, $notes]);
        flash('success', 'Your service request has been submitted.');
        redirect('services.php');
    }
}
$page_title = 'Request service';
include 'partials/header.php'; ?><div class="form-card">
    <h1>Request a service</h1>
    <p class="intro">Tell us what your vehicle needs and when you prefer to visit.</p><?php foreach ($errors as $err): ?><div class="flash error"><?= e($err) ?></div><?php endforeach;
                                                                                                                                                                if (!$vehicles): ?><div class="flash error">Add a vehicle before requesting a service. <a class="text-link" href="vehicles.php">Add vehicle →</a></div><?php else: ?><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="field"><label>Vehicle *</label><select name="vehicle_id" required>
                    <option value="">Select a vehicle</option><?php foreach ($vehicles as $vehicle): ?><option value="<?= $vehicle['id'] ?>"><?= e($vehicle['make'] . ' ' . $vehicle['model'] . ' · ' . $vehicle['registration_no']) ?></option><?php endforeach; ?>
                </select></div>
            <div class="field-row">
                <div class="field"><label>Service type *</label><select name="service_type" required>
                        <option value="">Choose a service</option><?php foreach (service_types() as $type): ?><option><?= e($type) ?></option><?php endforeach; ?>
                    </select></div>
                <div class="field"><label>Preferred date *</label><input type="date" name="preferred_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></div>
            </div>
            <div class="field"><label>Notes <span class="muted">(optional)</span></label><textarea name="notes" placeholder="Anything our technicians should know?"></textarea></div><button class="btn primary">Submit request →</button>
        </form><?php endif; ?>
</div><?php include 'partials/footer.php'; ?>