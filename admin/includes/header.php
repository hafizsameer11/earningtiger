<?php
if (!defined('APP_NAME')) {
    require_once dirname(__DIR__, 2) . '/config/config.php';
}
requireAdmin();

$currentAdminPage = basename($_SERVER['PHP_SELF'], '.php');
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminTitle ?? 'Admin') ?> — <?= e(APP_NAME) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=3.2">
</head>
<body class="admin-body">
    <header class="admin-header">
        <a href="index.php" class="admin-brand">🐯 <?= e(APP_NAME) ?> Admin</a>
        <div>
            <span style="margin-right:16px;opacity:0.8"><?= e($_SESSION['admin_username'] ?? '') ?></span>
            <a href="../index.php" target="_blank">View Site</a> |
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <nav class="admin-nav">
        <a href="index.php" class="<?= $currentAdminPage === 'index' ? 'active' : '' ?>">Dashboard</a>
        <a href="users.php" class="<?= $currentAdminPage === 'users' ? 'active' : '' ?>">Registrations</a>
        <a href="visitors.php" class="<?= $currentAdminPage === 'visitors' ? 'active' : '' ?>">Visitors</a>
        <a href="payments.php" class="<?= $currentAdminPage === 'payments' ? 'active' : '' ?>">Payment Methods</a>
        <a href="proofs.php" class="<?= $currentAdminPage === 'proofs' ? 'active' : '' ?>">Payment Proofs</a>
        <a href="settings.php" class="<?= $currentAdminPage === 'settings' ? 'active' : '' ?>">Site Settings</a>
        <a href="password.php" class="<?= $currentAdminPage === 'password' ? 'active' : '' ?>">Change Password</a>
    </nav>
    <div class="admin-content">
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
