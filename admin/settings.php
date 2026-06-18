<?php
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = $_POST['settings'] ?? [];
    foreach ($settings as $key => $value) {
        setSetting($key, trim($value));
    }
    flash('success', 'Site settings saved successfully.');
    header('Location: settings.php');
    exit;
}

$s = getAllSettings();

$groups = [
    'General' => ['site_name', 'tagline', 'about_text', 'company_registered', 'contact_email', 'whatsapp', 'registration_amount', 'payment_instructions'],
    'Hero Section' => ['hero_title', 'hero_subtitle', 'hero_title_urdu', 'hero_image', 'banner_image'],
    'Features' => ['feature_1_title', 'feature_1_urdu', 'feature_2_title', 'feature_2_urdu', 'feature_3_title', 'feature_3_urdu', 'feature_4_title', 'feature_4_urdu'],
    'Shifts & Salary' => ['shift_full_title', 'shift_full_time', 'shift_full_salary', 'shift_part_title', 'shift_part_time', 'shift_part_salary', 'shift_home_title', 'shift_home_time', 'shift_home_salary', 'shift_alt_full_time', 'shift_alt_full_salary', 'shift_alt_part_time', 'shift_alt_part_salary', 'salary_note'],
    'Eligibility' => ['eligibility_title', 'eligibility_1', 'eligibility_2', 'eligibility_3', 'eligibility_4', 'eligibility_note'],
    'Requirements' => ['req_1', 'req_2', 'req_3'],
    'Offices' => ['office_title', 'office_main_image', 'office_1_name', 'office_1_image', 'office_2_name', 'office_2_image', 'office_3_name', 'office_3_image'],
    'Social Links' => ['facebook', 'instagram'],
    'Payment Proofs Slider' => ['proofs_section_title', 'proofs_section_subtitle'],
];

$labels = [
    'site_name' => 'Site Name',
    'tagline' => 'Tagline',
    'about_text' => 'About Text',
    'company_registered' => 'Registration Badge Text',
    'contact_email' => 'Contact Email',
    'whatsapp' => 'WhatsApp Number',
    'registration_amount' => 'Registration Amount (PKR, 0 = free)',
    'payment_instructions' => 'Payment Instructions',
    'hero_title' => 'Hero Title',
    'hero_subtitle' => 'Hero Subtitle',
    'hero_title_urdu' => 'Hero Urdu Text',
    'hero_image' => 'Hero Image URL',
    'banner_image' => 'Banner Image URL',
    'salary_note' => 'Salary Note',
    'office_title' => 'Offices Section Title',
    'office_main_image' => 'Main Office Image URL',
    'facebook' => 'Facebook URL',
    'instagram' => 'Instagram URL',
    'proofs_section_title' => 'Payment Proofs Section Title',
    'proofs_section_subtitle' => 'Payment Proofs Section Subtitle',
];

$adminTitle = 'Site Settings';
require_once 'includes/header.php';
?>

<form method="post">
    <?php foreach ($groups as $groupName => $keys): ?>
    <div class="admin-card">
        <h2><?= e($groupName) ?></h2>
        <div class="settings-grid">
            <?php foreach ($keys as $key): ?>
                <div class="form-group <?= in_array($key, ['about_text', 'payment_instructions', 'hero_title_urdu', 'eligibility_note']) ? 'full' : '' ?>">
                    <label><?= e($labels[$key] ?? ucwords(str_replace('_', ' ', $key))) ?></label>
                    <?php if (in_array($key, ['about_text', 'payment_instructions', 'hero_title_urdu', 'eligibility_note'])): ?>
                        <textarea name="settings[<?= e($key) ?>]" rows="3"><?= e($s[$key] ?? '') ?></textarea>
                    <?php else: ?>
                        <input type="text" name="settings[<?= e($key) ?>]" value="<?= e($s[$key] ?? '') ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-primary btn-lg">Save All Settings</button>
</form>

<?php require_once 'includes/footer.php'; ?>
