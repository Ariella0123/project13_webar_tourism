<?php require_once __DIR__ . '/functions.php'; $pageTitle = $pageTitle ?? APP_NAME; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark site-nav sticky-top"><div class="container">
 <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= url('index.php') ?>"><span class="brand-mark"><i class="fa-solid fa-vr-cardboard"></i></span><span>AR Tourism<small>EXPLORER</small></span></a>
 <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
 <div id="nav" class="collapse navbar-collapse"><ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
  <li class="nav-item"><a class="nav-link" href="<?= url('index.php') ?>">Home</a></li>
  <li class="nav-item"><a class="nav-link" href="<?= url('destinations.php') ?>">Destinations</a></li>
  <li class="nav-item"><a class="nav-link" href="<?= url('attractions.php') ?>">Attractions</a></li>
  <li class="nav-item"><a class="nav-link" href="<?= url('about.php') ?>">About</a></li>
  <li class="nav-item"><a class="nav-link nav-cta px-3 ms-lg-2" href="<?= url('ar.php') ?>"><i class="fa-solid fa-camera"></i> Start AR</a></li>
 </ul></div>
</div></nav>
<main>
<?php foreach (flashes() as [$type, $message]): ?><div class="container mt-3"><div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div></div><?php endforeach; ?>
