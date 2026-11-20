<?php
require_once __DIR__ . '/../functions.php';

$page_title = $page_title ?? APP_NAME;

$base = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> · <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base ?>assets/style.css">
</head>

<body>

    <header class="topbar">

        <a class="brand" href="<?= $base ?><?= is_logged_in()
                                                ? (current_user()['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php')
                                                : 'index.php' ?>">
            <span class="brand-mark">A</span>
            <span>AutoCare <small>Service Hub</small></span>
        </a>

        <nav>
            <?php if (is_logged_in()): ?>

                <a href="<?= $base ?><?= current_user()['role'] === 'admin'
                                            ? 'admin/index.php'
                                            : 'dashboard.php' ?>">Dashboard</a>

                <?php if (current_user()['role'] === 'customer'): ?>

                    <a href="<?= $base ?>vehicles.php">My Vehicles</a>
                    <a href="<?= $base ?>services.php">Service Requests</a>

                <?php else: ?>

                    <a href="<?= $base ?>admin/requests.php">All Requests</a>
                    <a href="<?= $base ?>admin/users.php">Users</a>

                <?php endif; ?>

                <a href="<?= $base ?>profile.php">Profile</a>
                <a class="nav-logout" href="<?= $base ?>logout.php">Log out</a>

            <?php else: ?>

                <a href="<?= $base ?>login.php">Log in</a>
                <a class="nav-cta" href="<?= $base ?>register.php">Get started</a>

            <?php endif; ?>
        </nav>

    </header>

    <main class="container">

        <?php if ($success = get_flash('success')): ?>
            <div class="flash success">✓ <?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error = get_flash('error')): ?>
            <div class="flash error">! <?= e($error) ?></div>
        <?php endif; ?>