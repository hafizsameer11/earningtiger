<?php
require_once __DIR__ . '/config/config.php';

if (!file_exists(DB_PATH)) {
    header('Location: install.php');
    exit;
}

$regId = $_SESSION['success_reg_id'] ?? null;
$registration = $regId ? getRegistrationById((int) $regId) : null;

$pageTitle = 'Application Submitted';
require_once 'includes/header.php';
?>

<section class="form-page section">
    <div class="container container-sm">
        <div class="form-card success-card text-center">
            <div class="success-icon">✓</div>
            <h1>Application Submitted!</h1>
            <?php if ($registration): ?>
                <p>Thank you, <strong><?= e($registration['full_name']) ?></strong>! Your application has been received.</p>
                <p>Reference ID: <strong>#<?= str_pad($registration['id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
                <p>Our team will verify your payment and contact you on <strong><?= e($registration['phone']) ?></strong> within 24–48 hours.</p>
            <?php else: ?>
                <p>Your application has been received. We will contact you soon.</p>
            <?php endif; ?>
            <p>WhatsApp: <a href="https://wa.me/92<?= preg_replace('/\D/', '', getSetting('whatsapp')) ?>"><?= e(getSetting('whatsapp')) ?></a></p>
            <a href="index.php" class="btn btn-primary">Back to Home</a>
        </div>
    </div>
</section>

<?php
unset($_SESSION['success_reg_id']);
require_once 'includes/footer.php';
?>
