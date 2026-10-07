<?php
require_once __DIR__ . '/../includes/functions.php';
admin_required();
$pageTitle = 'Dashboard';
$counts = [];
foreach (['destinations','attractions','categories','ar_posters','ar_hotspots','users'] as $table) {
    $counts[$table] = (int) db()->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
}
$logs = db()->query('SELECT l.*,u.name FROM activity_logs l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.created_at DESC LIMIT 8')->fetchAll();
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><div class="eyebrow text-success">ADMIN WORKSPACE</div><h1 class="admin-title mb-1">Good to see you, <?= e($_SESSION['admin_name']) ?>.</h1><p class="text-muted mb-0">Manage your tourism experience from one place.</p></div>
    <a href="<?= url('admin/posters.php') ?>" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i> Add AR poster</a>
</div>
<div class="row g-3 mb-4"><?php foreach (['destinations'=>['Destinations','fa-map-location-dot'],'attractions'=>['Attractions','fa-landmark'],'categories'=>['Categories','fa-tags'],'ar_posters'=>['AR Posters','fa-image'],'ar_hotspots'=>['Hotspots','fa-crosshairs'],'users'=>['Users','fa-users']] as $key => [$label,$icon]): ?><div class="col-6 col-md-4 col-xl-2"><div class="card admin-stat h-100 p-3"><div class="icon-tile mb-3"><i class="fa-solid <?= $icon ?>"></i></div><small class="text-muted"><?= e($label) ?></small><strong class="fs-2"><?= $counts[$key] ?></strong></div></div><?php endforeach; ?></div>
<div class="row g-4"><div class="col-xl-8"><div class="card p-4 h-100"><div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0">Recent activity</h5><a href="<?= url('admin/logs.php') ?>" class="small">View all</a></div><?php if (!$logs): ?><div class="empty-state py-4"><i class="fa-solid fa-clock-rotate-left mb-2"></i><p class="mb-0">No activity recorded yet.</p></div><?php else: foreach ($logs as $log): ?><div class="activity-row"><span class="activity-icon"><i class="fa-solid fa-bolt"></i></span><div><strong><?= e($log['action']) ?></strong><p class="mb-0 text-muted small"><?= e($log['description']) ?></p></div><time class="ms-auto text-muted small"><?= e($log['created_at']) ?></time></div><?php endforeach; endif; ?></div></div><div class="col-xl-4"><div class="card p-4 h-100"><h5>Quick actions</h5><div class="quick-actions"><a href="<?= url('admin/destinations.php') ?>"><i class="fa-solid fa-map-location-dot"></i><span>Add destination</span><i class="fa-solid fa-arrow-right ms-auto"></i></a><a href="<?= url('admin/attractions.php') ?>"><i class="fa-solid fa-landmark"></i><span>Add attraction</span><i class="fa-solid fa-arrow-right ms-auto"></i></a><a href="<?= url('admin/hotspots.php') ?>"><i class="fa-solid fa-crosshairs"></i><span>Configure hotspots</span><i class="fa-solid fa-arrow-right ms-auto"></i></a><a href="<?= url('admin/users.php') ?>"><i class="fa-solid fa-user-plus"></i><span>Manage users</span><i class="fa-solid fa-arrow-right ms-auto"></i></a></div></div></div></div>
<?php require __DIR__ . '/_footer.php'; ?>
