<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_login();
$pdo = Database::getInstance();
$posterId = $_GET['poster_id'] ?? 1;

$posterStmt = $pdo->prepare("SELECT * FROM ar_posters WHERE id = ?");
$posterStmt->execute([$posterId]);
$poster = $posterStmt->fetch();

if (!$poster) { die("AR Poster not found."); }

$attractions = $pdo->query("SELECT id, name FROM attractions WHERE status = 'active'")->fetchAll();
$hotspotsStmt = $pdo->prepare("SELECT * FROM ar_hotspots WHERE poster_id = ?");
$hotspotsStmt->execute([$posterId]);
$existingHotspots = $hotspotsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>可视化 AR 热点编辑器 - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>海报热点编辑器：<?php echo sanitize($poster['title']); ?></h2>
        <a href="posters.php" class="btn btn-secondary">返回海报列表</a>
    </div>

    <div class="row">
        <div class="col-md-8 text-center">
            <div id="poster-container">
                <img id="poster-img" src="<?php echo BASE_URL . '/' . sanitize($poster['poster_image']); ?>" alt="Poster Target">
                <?php foreach ($existingHotspots as $hs): ?>
                    <div class="hotspot-box" style="
                        left: <?php echo $hs['norm_x'] * 100; ?>%;
                        top: <?php echo $hs['norm_y'] * 100; ?>%;
                        width: <?php echo $hs['norm_width'] * 100; ?>%;
                        height: <?php echo $hs['norm_height'] * 100; ?>%;
                    ">
                        <?php echo sanitize($hs['title']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-muted mt-2">点击海报任意位置以精准定位并添加 AR 热点 overlay。</p>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>添加热点</h5>
                    <form id="hotspot-form" action="../api/save_hotspot.php" method="POST">
                        <input type="hidden" name="poster_id" value="<?php echo $posterId; ?>">
                        <input type="hidden" id="norm_x" name="norm_x">
                        <input type="hidden" id="norm_y" name="norm_y">
                        <input type="hidden" id="norm_width" name="norm_width" value="0.2">
                        <input type="hidden" id="norm_height" name="norm_height" value="0.15">

                        <div class="mb-3">
                            <label class="form-label">热点名称</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">关联景点</label>
                            <select name="attraction_id" class="form-select">
                                <option value="">-- 选择景点 --</option>
                                <?php foreach ($attractions as $attr): ?>
                                    <option value="<?php echo $attr['id']; ?>"><?php echo sanitize($attr['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary w-100" id="save-btn" disabled>请先点击左侧图片确定坐标</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 加载解耦的外部 JavaScript 文件 -->
<script src="../assets/js/editor.js"></script>
</body>
</html>