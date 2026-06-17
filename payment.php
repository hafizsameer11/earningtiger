<?php
require_once __DIR__ . '/config/config.php';

if (!file_exists(DB_PATH)) {
    header('Location: install.php');
    exit;
}

$regId = $_SESSION['registration_id'] ?? null;
if (!$regId) {
    header('Location: signup.php');
    exit;
}

$registration = getRegistrationById((int) $regId);
if (!$registration) {
    unset($_SESSION['registration_id']);
    header('Location: signup.php');
    exit;
}

$s = getAllSettings();
$methods = getActivePaymentMethods();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $methodId = (int) ($_POST['payment_method_id'] ?? 0);
    $transactionId = trim($_POST['transaction_id'] ?? '');
    $amount = trim($_POST['amount'] ?? '');

    $methodStmt = getDB()->prepare('SELECT * FROM payment_methods WHERE id = ? AND is_active = 1');
    $methodStmt->execute([$methodId]);
    $method = $methodStmt->fetch();

    if (!$method) $errors[] = 'Please select a valid payment method.';
    if ($transactionId === '') $errors[] = 'Transaction ID is required.';
    if (empty($_FILES['receipt']['name'])) $errors[] = 'Please upload your payment receipt.';

    $filename = null;
    if (empty($errors) && !empty($_FILES['receipt']['name'])) {
        $upload = uploadReceipt($_FILES['receipt']);
        if (!$upload['success']) {
            $errors[] = $upload['error'];
        } else {
            $filename = $upload['filename'];
        }
    }

    if (empty($errors)) {
        $stmt = getDB()->prepare('UPDATE registrations SET payment_method_id = ?, receipt_file = ?, transaction_id = ?, amount = ?, status = ? WHERE id = ?');
        $stmt->execute([$methodId, $filename, $transactionId, $amount, 'submitted', $regId]);
        unset($_SESSION['registration_id']);
        $_SESSION['success_reg_id'] = $regId;
        header('Location: success.php');
        exit;
    }
}

$pageTitle = 'Payment';
require_once 'includes/header.php';
?>

<section class="form-page section">
    <div class="container container-sm">
        <div class="form-card">
            <div class="steps">
                <span class="step done">1. Register</span>
                <span class="step active">2. Payment</span>
                <span class="step">3. Done</span>
            </div>
            <h1>Select Payment Method</h1>
            <p class="form-subtitle"><?= e($s['payment_instructions']) ?></p>
            <?php if ((float) $s['registration_amount'] > 0): ?>
                <p class="amount-box">Amount: <strong>PKR <?= e($s['registration_amount']) ?></strong></p>
            <?php else: ?>
                <p class="amount-box">Upload your payment receipt as instructed below.</p>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $err): ?>
                        <p><?= e($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($methods)): ?>
                <div class="alert alert-warning">No payment methods available. Please contact admin.</div>
            <?php else: ?>
                <form method="post" enctype="multipart/form-data" class="payment-form">
                    <div class="payment-methods">
                        <?php foreach ($methods as $i => $method): ?>
                            <label class="payment-method-card">
                                <input type="radio" name="payment_method_id" value="<?= $method['id'] ?>" <?= $i === 0 ? 'checked' : '' ?> required>
                                <div class="pm-content">
                                    <h3><?= e($method['name']) ?></h3>
                                    <p><strong>Account Title:</strong> <?= e($method['account_title']) ?></p>
                                    <p><strong>Account Number:</strong> <span class="account-num"><?= e($method['account_number']) ?></span></p>
                                    <?php if ($method['instructions']): ?>
                                        <p class="pm-instructions"><?= e($method['instructions']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label for="transaction_id">Transaction ID / Reference *</label>
                        <input type="text" id="transaction_id" name="transaction_id" required value="<?= e($_POST['transaction_id'] ?? '') ?>" placeholder="Enter transaction ID from your payment app">
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount Paid (PKR)</label>
                        <input type="text" id="amount" name="amount" value="<?= e($_POST['amount'] ?? $s['registration_amount']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="receipt">Upload Receipt Screenshot *</label>
                        <input type="file" id="receipt" name="receipt" accept="image/*,.pdf" required>
                        <small>JPG, PNG, WEBP, GIF or PDF — max 5MB</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit Application</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
