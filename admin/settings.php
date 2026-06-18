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
    'General' => ['site_name', 'site_logo_icon', 'tagline', 'about_text', 'company_registered', 'contact_email', 'whatsapp', 'registration_amount', 'payment_instructions'],
    'Hero Section' => ['hero_title', 'hero_subtitle', 'hero_title_urdu', 'hero_image', 'banner_image'],
    'Features (English, Urdu & Icons)' => [
        'features_section_urdu',
        'feature_1_title', 'feature_1_urdu', 'feature_1_icon',
        'feature_2_title', 'feature_2_urdu', 'feature_2_icon',
        'feature_3_title', 'feature_3_urdu', 'feature_3_icon',
        'feature_4_title', 'feature_4_urdu', 'feature_4_icon',
    ],
    'Shifts & Salary' => ['shift_full_title', 'shift_full_time', 'shift_full_salary', 'shift_part_title', 'shift_part_time', 'shift_part_salary', 'shift_home_title', 'shift_home_time', 'shift_home_salary', 'shift_alt_full_time', 'shift_alt_full_salary', 'shift_alt_part_time', 'shift_alt_part_salary', 'salary_note'],
    'Eligibility (English, Urdu & Icons)' => [
        'eligibility_title', 'eligibility_title_urdu',
        'eligibility_1', 'eligibility_1_urdu', 'eligibility_1_icon',
        'eligibility_2', 'eligibility_2_urdu', 'eligibility_2_icon',
        'eligibility_3', 'eligibility_3_urdu', 'eligibility_3_icon',
        'eligibility_4', 'eligibility_4_urdu', 'eligibility_4_icon', 'eligibility_4_highlight',
        'eligibility_note',
    ],
    'Requirements (English, Urdu & Icons)' => [
        'requirements_title_urdu',
        'req_1', 'req_1_urdu', 'req_1_icon',
        'req_2', 'req_2_urdu', 'req_2_icon',
        'req_3', 'req_3_urdu', 'req_3_icon',
    ],
    'Offices' => ['office_title', 'office_main_image', 'office_1_name', 'office_1_image', 'office_2_name', 'office_2_image', 'office_3_name', 'office_3_image'],
    'Social Links' => ['facebook', 'instagram'],
    'Payment Proofs Slider' => ['proofs_section_title', 'proofs_section_subtitle'],
];

$labels = [
    'site_logo_icon' => 'Site Logo Icon (emoji)',
    'features_section_urdu' => 'Features Section Urdu Subtitle',
    'eligibility_title_urdu' => 'Eligibility Section Urdu Title',
    'requirements_title_urdu' => 'Requirements Section Urdu Title',
    'eligibility_4_highlight' => 'Highlight 4th Eligibility Card (1 = yes, 0 = no)',
    'proofs_section_title' => 'Payment Proofs Section Title',
    'proofs_section_subtitle' => 'Payment Proofs Section Subtitle',
    'registration_amount' => 'Registration Amount (PKR, 0 = free)',
];

$urduFields = ['hero_title_urdu', 'features_section_urdu', 'feature_1_urdu', 'feature_2_urdu', 'feature_3_urdu', 'feature_4_urdu', 'eligibility_title_urdu', 'eligibility_1_urdu', 'eligibility_2_urdu', 'eligibility_3_urdu', 'eligibility_4_urdu', 'eligibility_note', 'requirements_title_urdu', 'req_1_urdu', 'req_2_urdu', 'req_3_urdu', 'about_text', 'payment_instructions'];
$iconFields = ['site_logo_icon', 'feature_1_icon', 'feature_2_icon', 'feature_3_icon', 'feature_4_icon', 'eligibility_1_icon', 'eligibility_2_icon', 'eligibility_3_icon', 'eligibility_4_icon', 'req_1_icon', 'req_2_icon', 'req_3_icon'];
$fullFields = ['about_text', 'payment_instructions', 'eligibility_note'];

function settingsLabel(string $key, array $labels): string {
    if (isset($labels[$key])) {
        return $labels[$key];
    }
    if (str_ends_with($key, '_icon')) {
        return ucwords(str_replace('_', ' ', substr($key, 0, -5))) . ' Icon (emoji)';
    }
    if (str_ends_with($key, '_urdu')) {
        return ucwords(str_replace('_', ' ', substr($key, 0, -5))) . ' (Urdu)';
    }
    return ucwords(str_replace('_', ' ', $key));
}

$adminTitle = 'Site Settings';
require_once 'includes/header.php';
?>

<form method="post">
    <?php foreach ($groups as $groupName => $keys): ?>
    <div class="admin-card">
        <h2><?= e($groupName) ?></h2>
        <div class="settings-grid">
            <?php foreach ($keys as $key): ?>
                <div class="form-group <?= in_array($key, $fullFields, true) ? 'full' : '' ?>">
                    <label><?= e(settingsLabel($key, $labels)) ?></label>
                    <?php if (in_array($key, $fullFields, true) || in_array($key, $urduFields, true)): ?>
                        <textarea name="settings[<?= e($key) ?>]" rows="<?= in_array($key, $fullFields, true) ? 3 : 2 ?>" class="<?= in_array($key, $urduFields, true) ? 'urdu-input' : '' ?>"><?= e($s[$key] ?? '') ?></textarea>
                    <?php else: ?>
                        <input type="text" name="settings[<?= e($key) ?>]" value="<?= e($s[$key] ?? '') ?>" <?= in_array($key, $iconFields, true) ? 'class="icon-input"' : '' ?>>
                        <?php if (in_array($key, $iconFields, true) && !empty($s[$key])): ?>
                            <small class="icon-preview">Preview: <span><?= e($s[$key]) ?></span></small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-primary btn-lg">Save All Settings</button>
</form>

<?php require_once 'includes/footer.php'; ?>
