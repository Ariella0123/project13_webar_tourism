<?php
require_once __DIR__ . '/../includes/functions.php';
admin_required();
$q = trim($_GET['q'] ?? '');
$stmt = db()->prepare('SELECT l.*,u.name FROM activity_logs l LEFT JOIN users u ON u.id=l.user_id WHERE l.action LIKE ? OR l.description LIKE ? ORDER BY l.created_at DESC LIMIT 200');
$stmt->execute(["%$q%", "%$q%"]);
$logs = $stmt->fetchAll();
$pageTitle = 'Activity Logs';
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><div class="eyebrow text-success">SYSTEM</div><h1 class="admin-title mb-1">Activity logs</h1><p class="text-muted mb-0">A transparent record of administrative changes.</p></div><form class="d-flex gap-2"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search logs"><button class="btn btn-outline-success"><i class="fa-solid fa-search"></i></button></form></div>
<div class="card p-3 table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Action</th><th>Description</th><th>User</th><th>IP address</th><th>Time</th></tr></thead><tbody><?php foreach ($logs as $log): ?><tr><td><span class="badge text-bg-light"><?= e($log['action']) ?></span></td><td><?= e($log['description']) ?></td><td><?= e($log['name'] ?: 'System') ?></td><td><?= e($log['ip_address']) ?></td><td class="text-muted small"><?= e($log['created_at']) ?></td></tr><?php endforeach; ?></tbody></table><?php if (!$logs): ?><div class="empty-state my-3">No matching activity found.</div><?php endif; ?></div>
<?php require __DIR__ . '/_footer.php'; ?>
