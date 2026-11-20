<?php require_once 'functions.php';
if (is_logged_in()) redirect('dashboard.php');
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (strlen($name) < 2) $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
    if (strlen($pass) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($pass !== $confirm) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email=?');
        $check->execute([$email]);
        if ($check->fetch()) $errors[] = 'An account with that email already exists.';
        else {
            $stmt = $pdo->prepare('INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)');
            $stmt->execute([$name, $email, $phone, password_hash($pass, PASSWORD_DEFAULT)]);
            flash('success', 'Account created. Welcome to AutoCare!');
            redirect('login.php');
        }
    }
}
$page_title = 'Create account';
include 'partials/header.php'; ?><div class="form-card">
    <h1>Create your account</h1>
    <p class="intro">Start managing your vehicle care in one place.</p><?php foreach ($errors as $err): ?><div class="flash error"><?= e($err) ?></div><?php endforeach; ?><form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="field"><label>Full name</label><input required name="name" value="<?= e($_POST['name'] ?? '') ?>"></div>
        <div class="field-row">
            <div class="field"><label>Email address</label><input required type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>"></div>
            <div class="field"><label>Phone <span class="muted">(optional)</span></label><input name="phone" value="<?= e($_POST['phone'] ?? '') ?>"></div>
        </div>
        <div class="field-row">
            <div class="field"><label>Password</label><input required type="password" name="password"><small>At least 8 characters</small></div>
            <div class="field"><label>Confirm password</label><input required type="password" name="confirm_password"></div>
        </div><button class="btn primary" type="submit">Create account →</button>
    </form>
    <div class="form-foot"><span>Already have an account?</span><a href="login.php">Log in</a></div>
</div><?php include 'partials/footer.php'; ?>