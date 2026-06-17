<?php
require_once __DIR__ . '/config/config.php';

$db = getDB();

$db->exec("
CREATE TABLE IF NOT EXISTS settings (
    key_name TEXT PRIMARY KEY,
    value TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS payment_methods (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    account_title TEXT NOT NULL,
    account_number TEXT NOT NULL,
    instructions TEXT,
    is_active INTEGER DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT NOT NULL,
    city TEXT,
    gender TEXT,
    shift_type TEXT NOT NULL,
    status TEXT DEFAULT 'pending',
    payment_method_id INTEGER,
    receipt_file TEXT,
    transaction_id TEXT,
    amount TEXT,
    admin_notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
);
");

$defaults = [
    'site_name' => 'Earning Tigers',
    'tagline' => 'Pakistan\'s Trusted Online Earning Platform',
    'hero_title' => 'ONLINE JOB OPPORTUNITY',
    'hero_subtitle' => 'FEMALE STAFF NEEDED — Work From Home on Computer or Mobile',
    'hero_title_urdu' => 'خوشخبری! کمپیوٹر یا موبائل پر کام کرنے کے لیے لڑکیوں کی ضرورت ہے',
    'whatsapp' => '0309-7000767',
    'registration_fee' => '0',
    'company_registered' => 'Company Registered (FBR & SSL)',
    'hero_image' => 'https://aqservices.pk/wp-content/uploads/2024/12/Alqalam-1-794x1024.png',
    'banner_image' => 'https://aqservices.pk/wp-content/uploads/2023/06/1-1.png',

    'feature_1_title' => 'Work From Home',
    'feature_1_urdu' => 'گھر بیٹھے کام کرنے کا موقع',
    'feature_2_title' => 'Flexible Hours',
    'feature_2_urdu' => 'آسان اوقات — کوئی پابندی نہیں',
    'feature_3_title' => 'Good Income',
    'feature_3_urdu' => 'بہترین آمدنی + بونس',
    'feature_4_title' => 'Free Training',
    'feature_4_urdu' => 'مفت ٹریننگ اور مکمل رہنمائی',

    'shift_full_title' => 'Full-Time',
    'shift_full_time' => '11:00 AM — 5:00 PM',
    'shift_full_salary' => 'PKR 21,500 — 25,000 + Bonus & Incentive',
    'shift_part_title' => 'Part-Time',
    'shift_part_time' => '11:00 AM — 2:00 PM OR 2:00 PM — 5:00 PM',
    'shift_part_salary' => 'PKR 18,000 — 20,000 + Bonus & Incentive',
    'shift_home_title' => 'Home-Based',
    'shift_home_time' => '4 Hours Daily (No Time Restrictions)',
    'shift_home_salary' => 'PKR 18,000 — 20,000',

    'shift_alt_full_time' => '10:00 AM — 6:00 PM',
    'shift_alt_full_salary' => 'PKR 25,000 — 35,000',
    'shift_alt_part_time' => '10:00 AM — 2:00 PM OR 2:00 PM — 6:00 PM',
    'shift_alt_part_salary' => 'PKR 15,000 — 20,000',
    'salary_note' => 'Salary period weekly or monthly — depends on you',

    'eligibility_title' => 'Who Can Apply?',
    'eligibility_1' => 'Students',
    'eligibility_2' => 'Teachers',
    'eligibility_3' => 'House Wives',
    'eligibility_4' => 'Only Females',
    'eligibility_note' => 'No Fee • No Area Limit • No Age Limit • No Experience • No Specific Qualification',

    'req_1' => 'Smartphone or Laptop',
    'req_2' => 'Internet Access',
    'req_3' => 'Basic Communication Skills',

    'office_title' => 'Our Digital Working Spaces',
    'office_1_name' => 'Multan',
    'office_1_image' => 'https://aqservices.pk/wp-content/uploads/2024/12/78820249_741766139634111_7382101625571115008_n-1024x576.jpg',
    'office_2_name' => 'Islamabad',
    'office_2_image' => 'https://aqservices.pk/wp-content/uploads/2024/12/702afce384fbbdd0bd80a58090b7a1a4.jpg',
    'office_3_name' => 'Lahore',
    'office_3_image' => 'https://aqservices.pk/wp-content/uploads/2024/12/ingenious-design-studio-offices-karachi-5-1200x801-1-1024x684.jpg',
    'office_main_image' => 'https://aqservices.pk/wp-content/uploads/2023/06/pic-8.jpg',

    'about_text' => 'Earning Tigers is a registered platform dedicated to empowering women across Pakistan with flexible online work opportunities. Join thousands earning from home with full training and support.',
    'contact_email' => 'info@earningtigers.pk',
    'facebook' => '#',
    'instagram' => '#',
    'payment_instructions' => 'Send the registration amount to the selected account and upload your payment receipt screenshot below.',
    'registration_amount' => '500',
];

$stmt = $db->prepare('INSERT OR IGNORE INTO settings (key_name, value) VALUES (?, ?)');
foreach ($defaults as $key => $value) {
    $stmt->execute([$key, $value]);
}

$adminCheck = $db->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if ($adminCheck == 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $db->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)')->execute(['admin', $hash]);
}

$pmCheck = $db->query('SELECT COUNT(*) FROM payment_methods')->fetchColumn();
if ($pmCheck == 0) {
    $methods = [
        ['JazzCash', 'Earning Tigers', '03XX-XXXXXXX', 'Send payment via JazzCash app and upload screenshot.', 1, 1],
        ['EasyPaisa', 'Earning Tigers', '03XX-XXXXXXX', 'Send payment via EasyPaisa app and upload screenshot.', 1, 2],
    ];
    $stmt = $db->prepare('INSERT INTO payment_methods (name, account_title, account_number, instructions, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($methods as $m) {
        $stmt->execute($m);
    }
}

if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

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
