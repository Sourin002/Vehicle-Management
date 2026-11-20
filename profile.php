<?php require_once 'functions.php';
require_login();
$u = current_user();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
    if ($pass && strlen($pass) < 8) $errors[] = 'New password must be at least 8 characters.';
    if (!$errors) {
        if ($pass) {
            $s = $pdo->prepare('UPDATE users SET name=?,phone=?,password=? WHERE id=?');
            $s->execute([$name, $phone, password_hash($pass, PASSWORD_DEFAULT), $u['id']]);
        } else {
            $s = $pdo->prepare('UPDATE users SET name=?,phone=? WHERE id=?');
            $s->execute([$name, $phone, $u['id']]);
        }
        $u = array_merge($u, ['name' => $name, 'phone' => $phone]);
        $_SESSION['user'] = $u;
        flash('success', 'Profile updated successfully.');
        redirect('profile.php');
    }
}
$page_title = 'Your profile';
include 'partials/header.php'; ?><div class="page-head">
    <div>
        <p class="eyebrow">Account settings</p>
        <h1>Your profile</h1>
        <p>Keep your contact details up to date.</p>
    </div>
</div>
<div class="form-card" style="margin:0">
    <h2>Personal information</h2><?php foreach ($errors as $err): ?><div class="flash error"><?= e($err) ?></div><?php endforeach; ?><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="field"><label>Full name</label><input required name="name" value="<?= e($u['name']) ?>"></div>
        <div class="field"><label>Email address</label><input disabled value="<?= e($u['email']) ?>"><small>Email cannot be changed from this screen.</small></div>
        <div class="field"><label>Phone</label><input name="phone" value="<?= e($u['phone'] ?? '') ?>"></div>
        <hr style="border:0;border-top:1px solid var(--line);margin:26px 0">
        <h2>Change password</h2>
        <div class="field"><label>New password <span class="muted">(optional)</span></label><input type="password" name="password" placeholder="Leave blank to keep current password"><small>At least 8 characters</small></div><button class="btn primary">Save changes</button>
    </form>
</div><?php include 'partials/footer.php'; ?>