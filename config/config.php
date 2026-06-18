<?php
define('APP_NAME', 'Earning Tigers');
define('BASE_PATH', dirname(__DIR__));
define('DB_PATH', BASE_PATH . '/data/earning_tigers.db');
define('UPLOAD_PATH', BASE_PATH . '/uploads/receipts');
define('UPLOAD_URL', 'uploads/receipts');
define('PROOFS_UPLOAD_PATH', BASE_PATH . '/uploads/proofs');
define('PROOFS_UPLOAD_URL', 'uploads/proofs');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

date_default_timezone_set('Asia/Karachi');

session_start();

require_once BASE_PATH . '/includes/db.php';
require_once BASE_PATH . '/includes/functions.php';
