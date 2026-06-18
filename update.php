<?php
/**
 * Run this script on the server after uploading new files to apply database updates.
 * Usage: php update.php   OR visit https://yoursite.com/update.php in browser
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/migrations.php';

$messages = runMigrations();
$isCli = PHP_SAPI === 'cli';

if ($isCli) {
    echo "Earning Tigers — Update Script\n";
    echo str_repeat('-', 32) . "\n";
    foreach ($messages as $msg) {
        echo "• $msg\n";
    }
    echo "\nMigration version: " . MIGRATION_VERSION . "\n";
    echo "Update complete.\n";
    exit(0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Update — <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="install-page">
    <div class="install-box">
        <h1>✓ Update Complete</h1>
        <p><strong><?= e(APP_NAME) ?></strong> has been updated successfully.</p>
        <ul style="text-align:left;margin:20px 0">
            <?php foreach ($messages as $msg): ?>
                <li><?= e($msg) ?></li>
            <?php endforeach; ?>
        </ul>
        <p>Migration version: <strong><?= MIGRATION_VERSION ?></strong></p>
        <p><a href="index.php">View Website</a> · <a href="admin/login.php">Admin Panel</a></p>
        <p class="text-muted">Delete or protect update.php after running on production.</p>
    </div>
</body>
</html>
