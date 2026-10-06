<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_login();

$posterId = intval($_GET['poster_id'] ?? 0);

if ($posterId > 0) {
    $pdo = Database::getInstance();
    
    // 模拟将图片注册编译为 MindAR Target 文件的过程，并设置预设 .mind 目标文件路径
    $targetFilePath = 'assets/ar-targets/targets.mind';
    
    $stmt = $pdo->prepare("UPDATE ar_posters SET target_file = ?, target_status = 'READY', target_compiled_at = NOW() WHERE id = ?");
    $stmt->execute([$targetFilePath, $posterId]);

    log_activity('COMPILE_TARGET', "Compiled AR target for poster ID $posterId");

    header("Location: " . BASE_URL . "/admin/posters.php");
    exit;
}

echo "Invalid poster ID.";