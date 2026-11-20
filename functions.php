<?php
require_once __DIR__ . '/db.php';
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}
function flash($key, $message)
{
    $_SESSION['flash'][$key] = $message;
}
function get_flash($key)
{
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}
function is_logged_in()
{
    return isset($_SESSION['user']);
}
function current_user()
{
    return $_SESSION['user'] ?? null;
}
function require_login()
{
    if (!is_logged_in()) {
        flash('error', 'Please log in to continue.');
        redirect('login.php');
    }
}
function require_admin()
{
    require_login();
    if (current_user()['role'] !== 'admin') {
        flash('error', 'Administrator access is required.');
        redirect('dashboard.php');
    }
}
function csrf_token()
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24));
    return $_SESSION['csrf'];
}
function verify_csrf()
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) die('Invalid form token.');
}
function validate_date($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
function service_types()
{
    return ['Oil Change', 'Full Service', 'Brake Inspection', 'Tire & Wheel', 'AC Service', 'Engine Diagnostics', 'Other'];
}
function status_class($status)
{
    return strtolower(str_replace(' ', '-', $status));
}
