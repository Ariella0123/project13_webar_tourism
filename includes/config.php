<?php
// 开启 Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 基础配置
define('APP_NAME', 'AR Tourism Explorer');
define('DB_HOST', 'localhost');
define('DB_NAME', 'ar_tourism');
define('DB_USER', 'root');
define('DB_PASS', '1234');
define('PORT', '3307');

// 自动计算当前 Base URL
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($protocol . "://" . $host . $script_name, '/');
// 处理子目录层级
$base_url = preg_replace('/\/admin.*$/', '', $base_url);
$base_url = preg_replace('/\/api.*$/', '', $base_url);
define('BASE_URL', rtrim($base_url, '/'));

// 文件上传路径
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('TARGET_PATH', __DIR__ . '/../assets/ar-targets/');
?>