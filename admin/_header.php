<?php
require_once __DIR__ . '/../includes/functions.php';
admin_required();
$pageTitle = $pageTitle ?? 'Admin';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | Admin | <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<nav class="navbar navbar-dark admin-topbar"><div class="container-fluid px-3 px-lg-4">
<a class="navbar-brand fw-bold" href="<?= url('admin/index.php') ?>"><i class="fa-solid fa-vr-cardboard text-warning me-2"></i>AR Tourism <span class="text-warning">Admin</span></a>
<div class="d-flex align-items-center gap-3"><span class="text-white-50 small d-none d-md-inline"><i class="fa-solid fa-user me-1"></i><?= e($_SESSION['admin_name'] ?? 'Administrator') ?></span><a class="btn btn-sm btn-outline-light" href="<?= url('index.php') ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> View site</a></div>
</div></nav>
<div class="container-fluid"><div class="row">
<aside class="col-lg-2 p-0 admin-sidebar">
<div class="sidebar-heading">WORKSPACE</div>
<a href="<?= url('admin/index.php') ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
<div class="sidebar-heading">TOURISM CONTENT</div>
<a href="<?= url('admin/destinations.php') ?>"><i class="fa-solid fa-map-location-dot"></i> Destinations</a>
<a href="<?= url('admin/categories.php') ?>"><i class="fa-solid fa-tags"></i> Categories</a>
<a href="<?= url('admin/attractions.php') ?>"><i class="fa-solid fa-landmark"></i> Attractions</a>
<div class="sidebar-heading">AR MANAGEMENT</div>
<a href="<?= url('admin/posters.php') ?>"><i class="fa-solid fa-image"></i> AR Posters</a>
<a href="<?= url('admin/hotspots.php') ?>"><i class="fa-solid fa-crosshairs"></i> Hotspots</a>
<div class="sidebar-heading">SYSTEM</div>
<a href="<?= url('admin/users.php') ?>"><i class="fa-solid fa-users"></i> Users</a>
<a href="<?= url('admin/logs.php') ?>"><i class="fa-solid fa-clock-rotate-left"></i> Activity Logs</a>
<a href="<?= url('admin/settings.php') ?>"><i class="fa-solid fa-gear"></i> Settings</a>
<a class="sidebar-logout" href="<?= url('admin/logout.php') ?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</aside>
<main class="col-lg-10 admin-content p-3 p-lg-4">
<?php foreach (flashes() as [$type, $message]): ?><div class="alert alert-<?= e($type) ?> alert-dismissible fade show"><?= e($message) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endforeach; ?>
