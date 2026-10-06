<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_login();
$pdo = Database::getInstance();

// 处理海报上传
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['poster_image'])) {
    $title = sanitize($_POST['title'] ?? '');
    $file = $_FILES['poster_image'];

    if ($file['error'] === UPLOAD_ERR_OK && !empty($title)) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed)) {
            $filename = 'poster_' . time() . '.' . $ext;
            $destPath = UPLOAD_PATH . $filename;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $relativePath = 'assets/uploads/' . $filename;
                $stmt = $pdo->prepare("INSERT INTO ar_posters (title, poster_image, target_status) VALUES (?, ?, 'NOT_COMPILED')");
                $stmt->execute([$title, $relativePath]);
                log_activity('UPLOAD_POSTER', "Uploaded poster: $title");
                header("Location: posters.php");
                exit;
            }
        }
    }
}

$posters = $pdo->query("SELECT * FROM ar_posters ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>AR 海报管理 - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <div class="bg-dark text-white p-3 sidebar">
        <h4><i class="fa-solid fa-vr-cardboard me-2"></i>AR Admin</h4>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="index.php" class="nav-link text-white"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
            <li><a href="posters.php" class="nav-link active"><i class="fa-solid fa-image me-2"></i>AR Posters</a></li>
            <li><a href="logout.php" class="nav-link text-white mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
        </ul>
    </div>

    <div class="p-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>AR 海报与 Target 管理</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="fa-solid fa-upload me-2"></i>上传新海报</button>
        </div>

        <table class="table table-bordered bg-white shadow-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>海报预览</th>
                    <th>标题</th>
                    <th>Target 状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posters as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><img src="<?php echo BASE_URL . '/' . sanitize($p['poster_image']); ?>" height="60" class="rounded"></td>
                    <td><?php echo sanitize($p['title']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $p['target_status'] === 'READY' ? 'success' : 'warning'; ?>">
                            <?php echo $p['target_status']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="editor.php?poster_id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen-to-square me-1"></i>热点编辑</a>
                        <a href="../api/compile_target.php?poster_id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-gear me-1"></i>编译 Target</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 上传弹窗 -->
<div class="modal fade" id="uploadModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">上传海报</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">海报标题</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">海报图片文件 (JPG/PNG)</label>
            <input type="file" name="poster_image" class="form-control" accept="image/*" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">开始上传</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>