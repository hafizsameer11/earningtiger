<?php
$s = getAllSettings();
$siteName = $s['site_name'] ?? APP_NAME;
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? $siteName) ?> — <?= e($siteName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Nastaliq+Urdu:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="logo">
            <span class="logo-icon">🐯</span>
            <span class="logo-text"><?= e($siteName) ?></span>
        </a>
        <button class="nav-toggle" aria-label="Menu" onclick="document.body.classList.toggle('nav-open')">
            <span></span><span></span><span></span>
        </button>
        <nav class="main-nav">
            <a href="index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Home</a>
            <a href="index.php#shifts">Shifts</a>
            <a href="index.php#offices">Offices</a>
            <a href="index.php#contact">Contact</a>
            <a href="signup.php" class="btn btn-primary btn-sm">Apply Now</a>
        </nav>
    </div>
</header>
<main>
