<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance();
$posterStmt = $pdo->query("SELECT * FROM ar_posters WHERE status = 'active' ORDER BY id DESC LIMIT 1");
$poster = $posterStmt->fetch();

$hotspots = [];
if ($poster) {
    $hsStmt = $pdo->prepare("SELECT h.*, a.name as attr_name FROM ar_hotspots h LEFT JOIN attractions a ON h.attraction_id = a.id WHERE h.poster_id = ?");
    $hsStmt->execute([$poster['id']]);
    $hotspots = $hsStmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR 体验 - <?php echo APP_NAME; ?></title>
    <!-- CDN 依赖文件 -->
    <script src="https://aframe.io/releases/1.4.2/aframe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mind-ar@1.2.2/dist/mindar-image-aframe.prod.js"></script>
    <!-- 解耦样式表 -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div id="ar-overlay">
    <h6 class="m-0">请将摄像头对准宣传海报</h6>
    <small id="ar-status-text" class="ar-status-badge bg-warning text-dark">未检测到海报</small>
</div>

<a-scene mindar-image="imageTargetSrc: <?php echo BASE_URL . '/' . sanitize($poster['target_file'] ?? 'assets/ar-targets/targets.mind'); ?>;" 
         color-space="sRGB" 
         renderer="colorManagement: true, physicallyCorrectLights" 
         vr-mode-ui="enabled: false" 
         device-orientation-permission-ui="enabled: false">
         
    <a-camera position="0 0 0" look-controls="enabled: false"></a-camera>

    <a-entity mindar-image-target="targetIndex: 0">
        <?php foreach ($hotspots as $hs): ?>
            <?php 
                $posX = ($hs['norm_x'] + ($hs['norm_width'] / 2)) - 0.5;
                $posY = 0.5 - ($hs['norm_y'] + ($hs['norm_height'] / 2));
            ?>
            <a-plane position="<?php echo $posX; ?> <?php echo $posY; ?> 0.1" 
                     width="<?php echo $hs['norm_width']; ?>" 
                     height="<?php echo $hs['norm_height']; ?>" 
                     material="color: #0d6efd; opacity: 0.85">
                <a-text value="<?php echo sanitize($hs['attr_name'] ?? $hs['title']); ?>" 
                        align="center" 
                        color="#FFF" 
                        width="1.5" 
                        position="0 0 0.01"></a-text>
            </a-plane>
        <?php endforeach; ?>
    </a-entity>
</a-scene>

<!-- 加载解耦的外部 JavaScript 文件 -->
<script src="assets/js/ar-experience.js"></script>
</body>
</html>