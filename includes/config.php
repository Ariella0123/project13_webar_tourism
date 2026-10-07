<?php

declare(strict_types=1);

define('APP_NAME', 'AR Tourism Explorer');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'synergy1_yuxuan_project13_webar_tourism');
define('DB_USER', 'synergy1_yenping');
define('DB_PASS', 'R.zb0ZwEuGZ}*fW2');
define('PORT', 3307);
define('APP_ROOT', dirname(__DIR__));

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$base = preg_replace('#/(admin|api)(/.*)?$#', '', $scriptName) ?: '';
define('BASE_URL', rtrim($base, '/'));
define('UPLOAD_DIR', APP_ROOT . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
define('TARGET_DIR', APP_ROOT . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'ar-targets');

date_default_timezone_set('Asia/Kuala_Lumpur');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => isset($_SERVER['HTTPS'])]);
    session_start();
}
