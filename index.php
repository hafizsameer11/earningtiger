<?php
require_once __DIR__ . '/config/config.php';

if (!file_exists(DB_PATH)) {
    header('Location: install.php');
    exit;
}

$s = getAllSettings();
$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="badge badge-yellow"><?= e($s['hero_subtitle']) ?></span>
            <h1><?= e($s['hero_title']) ?></h1>
            <p class="hero-urdu urdu"><?= e($s['hero_title_urdu']) ?></p>
            <p class="hero-desc"><?= e($s['about_text']) ?></p>
            <div class="hero-actions">
                <a href="signup.php" class="btn btn-primary btn-lg">Register Now — No Fee</a>
                <a href="https://wa.me/92<?= preg_replace('/\D/', '', $s['whatsapp']) ?>" class="btn btn-outline btn-lg" target="_blank">WhatsApp Us</a>
            </div>
            <div class="hero-badges">
                <span>✓ <?= e($s['company_registered']) ?></span>
                <span>✓ <?= e($s['eligibility_note']) ?></span>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?= e($s['hero_image']) ?>" alt="Online work opportunity">
        </div>
    </div>
</section>

<section class="features section">
    <div class="container">
        <div class="section-header">
            <h2>Work on <span class="highlight">Social Media</span></h2>
            <p class="urdu">سوشل میڈیا پر کام کریں اور گھر بیٹھے کمائیں</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🏠</div>
                <h3><?= e($s['feature_1_title']) ?></h3>
                <p class="urdu"><?= e($s['feature_1_urdu']) ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⏰</div>
                <h3><?= e($s['feature_2_title']) ?></h3>
                <p class="urdu"><?= e($s['feature_2_urdu']) ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3><?= e($s['feature_3_title']) ?></h3>
                <p class="urdu"><?= e($s['feature_3_urdu']) ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎓</div>
                <h3><?= e($s['feature_4_title']) ?></h3>
                <p class="urdu"><?= e($s['feature_4_urdu']) ?></p>
            </div>
        </div>
    </div>
</section>

<section id="shifts" class="shifts section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Working <span class="highlight">Shifts</span></h2>
            <p>Choose the shift that fits your schedule</p>
        </div>
        <div class="shifts-grid">
            <div class="shift-card shift-full">
                <div class="shift-badge">Popular</div>
                <h3><?= e($s['shift_full_title']) ?></h3>
                <p class="shift-time"><?= e($s['shift_full_time']) ?></p>
                <p class="shift-salary"><?= e($s['shift_full_salary']) ?></p>
                <hr>
                <p class="shift-alt"><small>Also: <?= e($s['shift_alt_full_time']) ?> — <?= e($s['shift_alt_full_salary']) ?></small></p>
            </div>
            <div class="shift-card">
                <h3><?= e($s['shift_part_title']) ?></h3>
                <p class="shift-time"><?= e($s['shift_part_time']) ?></p>
                <p class="shift-salary"><?= e($s['shift_part_salary']) ?></p>
                <hr>
                <p class="shift-alt"><small>Also: <?= e($s['shift_alt_part_time']) ?> — <?= e($s['shift_alt_part_salary']) ?></small></p>
            </div>
            <div class="shift-card shift-home">
                <div class="shift-badge">Flexible</div>
                <h3><?= e($s['shift_home_title']) ?></h3>
                <p class="shift-time"><?= e($s['shift_home_time']) ?></p>
                <p class="shift-salary"><?= e($s['shift_home_salary']) ?></p>
            </div>
        </div>
        <p class="salary-note text-center">📌 <?= e($s['salary_note']) ?></p>
    </div>
</section>

<section class="eligibility section">
    <div class="container">
        <div class="eligibility-box">
            <h2><?= e($s['eligibility_title']) ?> <span class="urdu">/ درخواست کون دے سکتا ہے؟</span></h2>
            <div class="eligibility-grid">
                <div class="elig-item"><span>👩‍🎓</span><strong><?= e($s['eligibility_1']) ?></strong><span class="urdu">طلبہ</span></div>
                <div class="elig-item"><span>👩‍🏫</span><strong><?= e($s['eligibility_2']) ?></strong><span class="urdu">اساتذہ</span></div>
                <div class="elig-item"><span>👩‍👧</span><strong><?= e($s['eligibility_3']) ?></strong><span class="urdu">گھریلو خواتین</span></div>
                <div class="elig-item highlight-item"><span>👩</span><strong><?= e($s['eligibility_4']) ?></strong><span class="urdu">صرف خواتین</span></div>
            </div>
            <p class="eligibility-note"><?= e($s['eligibility_note']) ?></p>
        </div>
    </div>
</section>

<section class="requirements section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Requirements <span class="urdu">/ ضروریات</span></h2>
        </div>
        <div class="req-grid">
            <div class="req-card"><span>📱💻</span><h4><?= e($s['req_1']) ?></h4><p class="urdu">موبائل یا لیپ ٹاپ</p></div>
            <div class="req-card"><span>📶</span><h4><?= e($s['req_2']) ?></h4><p class="urdu">انٹرنیٹ کنکشن</p></div>
            <div class="req-card"><span>💬</span><h4><?= e($s['req_3']) ?></h4><p class="urdu">بنیادی کمیونیکیشن اسکلز</p></div>
        </div>
    </div>
</section>

<section id="offices" class="offices section">
    <div class="container">
        <div class="section-header">
            <h2><?= e($s['office_title']) ?></h2>
            <p>Friendly environment across Pakistan</p>
        </div>
        <div class="office-hero">
            <img src="<?= e($s['office_main_image']) ?>" alt="Digital working space">
        </div>
        <div class="offices-grid">
            <div class="office-card">
                <img src="<?= e($s['office_1_image']) ?>" alt="<?= e($s['office_1_name']) ?>">
                <h3><?= e($s['office_1_name']) ?></h3>
            </div>
            <div class="office-card">
                <img src="<?= e($s['office_2_image']) ?>" alt="<?= e($s['office_2_name']) ?>">
                <h3><?= e($s['office_2_name']) ?></h3>
            </div>
            <div class="office-card">
                <img src="<?= e($s['office_3_image']) ?>" alt="<?= e($s['office_3_name']) ?>">
                <h3><?= e($s['office_3_name']) ?></h3>
            </div>
        </div>
    </div>
</section>

<section class="cta section section-teal">
    <div class="container text-center">
        <h2>Ready to Start Earning?</h2>
        <p>Register now — only females can apply. Students, teachers & house wives welcome!</p>
        <a href="signup.php" class="btn btn-yellow btn-lg">Apply Now — It's Free</a>
    </div>
</section>

<section id="contact" class="contact section">
    <div class="container">
        <div class="contact-box">
            <h2>Get in Touch</h2>
            <p>Message us on WhatsApp: <strong><a href="https://wa.me/92<?= preg_replace('/\D/', '', $s['whatsapp']) ?>"><?= e($s['whatsapp']) ?></a></strong></p>
            <p>Email: <strong><?= e($s['contact_email']) ?></strong></p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
