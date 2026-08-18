<?php
function getSetting(string $key, string $default = ''): string {
    $stmt = getDB()->prepare('SELECT value FROM settings WHERE key_name = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['value'] : $default;
}

function setSetting(string $key, string $value): void {
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO settings (key_name, value) VALUES (?, ?)
        ON CONFLICT(key_name) DO UPDATE SET value = excluded.value');
    $stmt->execute([$key, $value]);
}

function getAllSettings(): array {
    $rows = getDB()->query('SELECT key_name, value FROM settings')->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['key_name']] = $row['value'];
    }
    return $settings;
}

function getActivePaymentMethods(): array {
    return getDB()->query('SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();
}

function getAllPaymentMethods(): array {
    return getDB()->query('SELECT * FROM payment_methods ORDER BY sort_order, id')->fetchAll();
}

function isAdminLoggedIn(): bool {
    return !empty($_SESSION['admin_id']);
}

function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function formatDate(string $date): string {
    return date('d M Y, h:i A', strtotime($date));
}

function uploadImage(array $file, string $dir, string $prefix, bool $allowPdf = false): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed. Please try again.'];
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File too large. Maximum 5MB allowed.'];
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if ($allowPdf) {
        $allowed[] = 'application/pdf';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed, true)) {
        return ['success' => false, 'error' => 'Invalid file type. Use JPG, PNG, WEBP or GIF.'];
    }

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
    $filename = uniqid($prefix, true) . '.' . strtolower($ext);
    $dest = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Could not save file.'];
    }

    return ['success' => true, 'filename' => $filename];
}

function uploadReceipt(array $file): array {
    $result = uploadImage($file, UPLOAD_PATH, 'receipt_', true);
    if (!$result['success'] && str_contains($result['error'] ?? '', 'Invalid file type')) {
        return ['success' => false, 'error' => 'Invalid file type. Use JPG, PNG, WEBP, GIF or PDF.'];
    }
    return $result;
}

function getActivePaymentProofs(): array {
    return getDB()->query('SELECT * FROM payment_proofs WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();
}

function getAllPaymentProofs(): array {
    return getDB()->query('SELECT * FROM payment_proofs ORDER BY sort_order, id')->fetchAll();
}

function getPaymentProofById(int $id): ?array {
    $stmt = getDB()->prepare('SELECT * FROM payment_proofs WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function proofImageUrl(string $filename): string {
    return PROOFS_UPLOAD_URL . '/' . rawurlencode($filename);
}

function getRegistrationById(int $id): ?array {
    $stmt = getDB()->prepare('SELECT * FROM registrations WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function setting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = getAllSettings();
    }
    return $cache[$key] ?? $default;
}

function buildDateFilterClause(string $filter, string $column = 'created_at', bool $dateOnly = false): array {
    if ($dateOnly) {
        switch ($filter) {
            case 'today':
                return ["$column = ?", [date('Y-m-d')]];
            case 'yesterday':
                return ["$column = ?", [date('Y-m-d', strtotime('-1 day'))]];
            case '7days':
                return ["$column >= ?", [date('Y-m-d', strtotime('-6 days'))]];
            case '30days':
                return ["$column >= ?", [date('Y-m-d', strtotime('-29 days'))]];
            default:
                return ['1=1', []];
        }
    }

    $todayStart = date('Y-m-d 00:00:00');
    $tomorrowStart = date('Y-m-d 00:00:00', strtotime('+1 day'));
    $yesterdayStart = date('Y-m-d 00:00:00', strtotime('-1 day'));

    switch ($filter) {
        case 'today':
            return ["$column >= ? AND $column < ?", [$todayStart, $tomorrowStart]];
        case 'yesterday':
            return ["$column >= ? AND $column < ?", [$yesterdayStart, $todayStart]];
        case '7days':
            return ["$column >= ?", [date('Y-m-d 00:00:00', strtotime('-6 days'))]];
        case '30days':
            return ["$column >= ?", [date('Y-m-d 00:00:00', strtotime('-29 days'))]];
        default:
            return ['1=1', []];
    }
}

function whatsappLink(?string $fallbackNumber = null): string {
    $group = trim(getSetting('whatsapp_group_link'));
    if ($group !== '') {
        if (preg_match('#^https?://#i', $group)) {
            return $group;
        }
        return 'https://' . ltrim($group, '/');
    }

    $number = $fallbackNumber ?? getSetting('whatsapp');
    $digits = preg_replace('/\D/', '', $number);
    if ($digits === '') {
        return '#';
    }
    if (str_starts_with($digits, '0')) {
        $cc = preg_replace('/\D/', '', getSetting('whatsapp_country_code', '92'));
        if ($cc !== '') {
            $digits = $cc . substr($digits, 1);
        }
    }
    return 'https://wa.me/' . $digits;
}

function whatsappLabel(): string {
    $label = trim(getSetting('whatsapp_button_text'));
    if ($label !== '') {
        return $label;
    }
    return trim(getSetting('whatsapp_group_link')) !== '' ? 'Join WhatsApp Group' : 'WhatsApp Us';
}

function getDateFilterOptions(): array {
    return [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7days' => 'Last 7 Days',
        '30days' => 'Last 30 Days',
        'all' => 'All',
    ];
}

function renderDateFilter(string $current, string $baseUrl, string $param = 'filter'): void {
    $filters = getDateFilterOptions();
    $path = parse_url($baseUrl, PHP_URL_PATH) ?: $baseUrl;
    $path = basename($path);
    echo '<div class="filter-toolbar">';
    echo '<span class="filter-toolbar-label">Filter by date:</span>';
    echo '<div class="filter-bar">';
    foreach ($filters as $key => $label) {
        $active = $current === $key ? ' active' : '';
        $href = $key === 'all' ? $path : $path . '?' . $param . '=' . urlencode($key);
        echo '<a href="' . e($href) . '" class="filter-tab' . $active . '">' . e($label) . '</a>';
    }
    echo '</div></div>';
}

function getClientIp(): string {
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', (string) $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

function lookupGeo(string $ip): array {
    $unknown = ['country' => 'Unknown', 'country_code' => '', 'city' => '', 'region' => ''];
    if ($ip === '0.0.0.0' || $ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
        return ['country' => 'Local', 'country_code' => 'LO', 'city' => 'Local Network', 'region' => ''];
    }
    $url = 'http://ip-api.com/json/' . urlencode($ip) . '?fields=status,country,countryCode,regionName,city';
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $json = @file_get_contents($url, false, $ctx);
    if (!$json) {
        return $unknown;
    }
    $data = json_decode($json, true);
    if (($data['status'] ?? '') !== 'success') {
        return $unknown;
    }
    return [
        'country' => $data['country'] ?? 'Unknown',
        'country_code' => $data['countryCode'] ?? '',
        'city' => $data['city'] ?? '',
        'region' => $data['regionName'] ?? '',
    ];
}

function trackVisit(): void {
    if (!file_exists(DB_PATH)) {
        return;
    }
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (str_contains($script, '/admin/') || str_contains($script, 'install.php') || str_contains($script, 'update.php')) {
        return;
    }

    $sessionKey = 'et_visit_' . date('Y-m-d');
    if (!empty($_SESSION[$sessionKey])) {
        return;
    }

    $ip = getClientIp();
    $geo = lookupGeo($ip);
    $page = $_SERVER['REQUEST_URI'] ?? '/';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $visitDate = date('Y-m-d');

    $stmt = getDB()->prepare('INSERT INTO site_visits (ip_address, country, country_code, city, region, page_url, referrer, user_agent, visit_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$ip, $geo['country'], $geo['country_code'], $geo['city'], $geo['region'], $page, $referrer, $ua, $visitDate]);
    $_SESSION[$sessionKey] = true;
}

function getVisitStats(string $filter = 'all'): array {
    [$where, $params] = buildDateFilterClause($filter, 'visit_date', true);
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM site_visits WHERE $where");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(DISTINCT ip_address) FROM site_visits WHERE $where");
    $stmt->execute($params);
    $unique = (int) $stmt->fetchColumn();

    return ['total' => $total, 'unique' => $unique];
}

function getVisitsByCountry(string $filter = 'all'): array {
    [$where, $params] = buildDateFilterClause($filter, 'visit_date', true);
    $stmt = getDB()->prepare("SELECT country, country_code, COUNT(*) as visits, COUNT(DISTINCT ip_address) as unique_ips FROM site_visits WHERE $where GROUP BY country, country_code ORDER BY visits DESC LIMIT 20");
    $stmt->execute($params);
    return $stmt->fetchAll();
}

