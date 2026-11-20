<?php require_once 'functions.php';
if (is_logged_in()) redirect('dashboard.php');
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'phone' => $user['phone'], 'role' => $user['role']];
        flash('success', 'Welcome back, ' . explode(' ', $user['name'])[0] . '!');
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php');
    } else {
        $error = 'Email or password is incorrect.';
    }
}
$page_title = 'Log in';
include 'partials/header.php'; ?><div class="form-card" style="max-width:480px">
    <h1>Welcome back</h1>
    <p class="intro">Log in to continue to your service hub.</p><?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="field"><label>Email address</label><input required type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>"></div>
        <div class="field"><label>Password</label><input required type="password" name="password"></div><button class="btn primary" type="submit">Log in →</button>
    </form>
    <div class="form-foot"><span>New to AutoCare?</span><a href="register.php">Create account</a></div>
</div><?php include 'partials/footer.php'; ?>