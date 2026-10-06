<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_login();

$pdo = Database::getInstance();

// 统计数据
$destCount = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
$attrCount = $pdo->query("SELECT COUNT(*) FROM attractions")->fetchColumn();
$posterCount = $pdo->query("SELECT COUNT(*) FROM ar_posters")->fetchColumn();
$hotspotCount = $pdo->query("SELECT COUNT(*) FROM ar_hotspots")->fetchColumn();

// 最近日志
$logsStmt = $pdo->query("SELECT l.*, u.username FROM activity_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 5");
$recentLogs = $logsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <!-- 侧边栏 -->
    <div class="bg-dark text-white p-3 min-vh-100" style="width: 250px;">
        <h4><i class="fa-solid fa-vr-cardboard me-2"></i>AR Admin</h4>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"><a href="index.php" class="nav-link active"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
            <li><a href="posters.php" class="nav-link text-white"><i class="fa-solid fa-image me-2"></i>AR Posters</a></li>
            <li><a href="logout.php" class="nav-link text-white mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
        </ul>
    </div>

    <!-- 主体区域 -->
    <div class="p-4 flex-grow-1">
        <h2>Dashboard</h2>
        <div class="row g-3 my-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white p-3">
                    <h5>Destinations</h5>
                    <h3><?php echo $destCount; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white p-3">
                    <h5>Attractions</h5>
                    <h3><?php echo $attrCount; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark p-3">
                    <h5>AR Posters</h5>
                    <h3><?php echo $posterCount; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white p-3">
                    <h5>Active Hotspots</h5>
                    <h3><?php echo $hotspotCount; ?></h3>
                </div>
            </div>
        </div>

        <h4 class="mt-4">Recent Activity Logs</h4>
        <table class="table table-striped border">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentLogs as $log): ?>
                    <tr>
                        <td><?php echo sanitize($log['username'] ?? 'System'); ?></td>
                        <td><span class="badge bg-secondary"><?php echo sanitize($log['action']); ?></span></td>
                        <td><?php echo sanitize($log['description']); ?></td>
                        <td><?php echo sanitize($log['ip_address']); ?></td>
                        <td><?php echo $log['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>