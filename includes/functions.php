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
