<?php
require_once __DIR__ . '/config/config.php';

if (!file_exists(DB_PATH)) {
    header('Location: install.php');
    exit;
}

$errors = [];
$s = getAllSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $shift = trim($_POST['shift_type'] ?? '');

    if ($name === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($phone === '') $errors[] = 'Phone number is required.';
    if (!in_array($gender, ['male', 'female'], true)) $errors[] = 'Please select your gender.';
    if (!in_array($shift, ['full-time', 'part-time', 'home-based'], true)) $errors[] = 'Please select a shift.';

    if (empty($errors)) {
        $stmt = getDB()->prepare('INSERT INTO registrations (full_name, email, phone, city, gender, shift_type) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, $phone, $city, $gender, $shift]);
        $regId = (int) getDB()->lastInsertId();
        $_SESSION['registration_id'] = $regId;
        header('Location: payment.php');
        exit;
    }
}

$pageTitle = 'Apply Now';
require_once 'includes/header.php';
?>

<section class="form-page section">
    <div class="container container-sm">
        <div class="form-card">
            <h1>Register for <?= e($s['site_name']) ?></h1>
            <p class="form-subtitle">Fill in your details to apply.</p>

            <?php if ($errors): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $err): ?>
                        <p><?= e($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="signup-form">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone / WhatsApp *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="03XX-XXXXXXX" value="<?= e($_POST['phone'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?= e($_POST['city'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Gender *</label>
                    <div class="gender-options">
                        <label class="gender-option">
                            <input type="radio" name="gender" value="female" required <?= ($_POST['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                            <span class="gender-box">
                                <span class="gender-icon">👩</span>
                                <span class="gender-label">Female</span>
                            </span>
                        </label>
                        <label class="gender-option">
                            <input type="radio" name="gender" value="male" required <?= ($_POST['gender'] ?? '') === 'male' ? 'checked' : '' ?>>
                            <span class="gender-box">
                                <span class="gender-icon">👨</span>
                                <span class="gender-label">Male</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="shift_type">Preferred Shift *</label>
                    <select id="shift_type" name="shift_type" required>
                        <option value="">Select shift...</option>
                        <option value="full-time" <?= ($_POST['shift_type'] ?? '') === 'full-time' ? 'selected' : '' ?>>
                            Full-Time — <?= e($s['shift_full_time']) ?> (<?= e($s['shift_full_salary']) ?>)
                        </option>
                        <option value="part-time" <?= ($_POST['shift_type'] ?? '') === 'part-time' ? 'selected' : '' ?>>
                            Part-Time — <?= e($s['shift_part_time']) ?> (<?= e($s['shift_part_salary']) ?>)
                        </option>
                        <option value="home-based" <?= ($_POST['shift_type'] ?? '') === 'home-based' ? 'selected' : '' ?>>
                            Home-Based — <?= e($s['shift_home_time']) ?> (<?= e($s['shift_home_salary']) ?>)
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Continue to Payment</button>
            </form>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
