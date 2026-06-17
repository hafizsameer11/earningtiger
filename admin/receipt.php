<?php
require_once dirname(__DIR__) . '/config/config.php';
requireAdmin();

$file = basename($_GET['f'] ?? '');
if ($file === '' || preg_match('/\.\./', $file)) {
    http_response_code(404);
    exit('Not found');
}

$path = UPLOAD_PATH . '/' . $file;
if (!file_exists($path)) {
    http_response_code(404);
    exit('Not found');
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $path);
finfo_close($finfo);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
readfile($path);
