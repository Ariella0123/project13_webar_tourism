<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/database.php';

$pdo = Database::getInstance();

// 查询热门目的地
$destStmt = $pdo->query("SELECT * FROM destinations WHERE status = 'active' LIMIT 6");
$destinations = $destStmt->fetchAll();
?>

<!-- Hero 区域 -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Explore Cultural Heritage in AR</h1>
        <p class="lead mb-4">Scan tourism posters or physical landmarks to unlock interactive 3D panels, rich details, and videos.</p>
        <a href="<?php echo BASE_URL; ?>/ar.php" class="btn btn-primary btn-lg shadow"><i class="fa-solid fa-camera me-2"></i>Start AR Experience</a>
    </div>
</div>

<!-- 推荐目的地列表 -->
<div class="container my-5">
    <h2 class="text-center mb-4"><i class="fa-solid fa-compass me-2"></i>Featured Destinations</h2>
    <div class="row g-4">
        <?php foreach ($destinations as $dest): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?php echo $dest['cover_image'] ? BASE_URL . '/' . sanitize($dest['cover_image']) : 'https://picsum.photos/400/250'; ?>" class="card-img-top" alt="<?php echo sanitize($dest['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo sanitize($dest['name']); ?></h5>
                        <p class="card-text text-muted"><?php echo sanitize($dest['short_description']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>