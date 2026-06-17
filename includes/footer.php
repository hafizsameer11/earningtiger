</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <h3>🐯 <?= e(getSetting('site_name', APP_NAME)) ?></h3>
            <p><?= e(getSetting('tagline')) ?></p>
            <p class="urdu"><?= e(getSetting('hero_title_urdu')) ?></p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="signup.php">Apply Now</a></li>
                <li><a href="index.php#shifts">Working Shifts</a></li>
            </ul>
        </div>
        <div>
            <h4>Contact</h4>
            <ul>
                <li>WhatsApp: <a href="https://wa.me/92<?= preg_replace('/\D/', '', getSetting('whatsapp')) ?>"><?= e(getSetting('whatsapp')) ?></a></li>
                <li>Email: <?= e(getSetting('contact_email')) ?></li>
            </ul>
        </div>
        <div>
            <h4>Follow Us</h4>
            <div class="social-links">
                <a href="<?= e(getSetting('facebook', '#')) ?>" target="_blank" rel="noopener">Facebook</a>
                <a href="<?= e(getSetting('instagram', '#')) ?>" target="_blank" rel="noopener">Instagram</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= e(getSetting('site_name', APP_NAME)) ?>. <?= e(getSetting('company_registered')) ?></p>
        </div>
    </div>
</footer>
<script src="assets/js/main.js"></script>
</body>
</html>
