<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/migrations.php';

$messages = runMigrations();

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Installed</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body class="install-page">
<div class="install-box">
<h1>✓ Installation Complete</h1>
<p><strong>Earning Tigers</strong> is ready to use.</p>
<ul>
<li><a href="index.php">View Website</a></li>
<li><a href="admin/login.php">Admin Panel</a> — username: <code>admin</code>, password: <code>admin123</code></li>
</ul>
<p class="text-muted">Change the admin password after first login. Delete or protect install.php in production.</p>
</div></body></html>';
