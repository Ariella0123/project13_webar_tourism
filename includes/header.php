<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --dark-bg: #121212; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section { background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://picsum.photos/1920/1080?nature') no-repeat center center/cover; color: #fff; padding: 100px 0; }
        .card-img-top { height: 200px; object-fit: cover; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>"><i class="fa-solid fa-vr-cardboard me-2"></i><?php echo APP_NAME; ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/destinations.php">Destinations</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/attractions.php">Attractions</a></li>
        <li class="nav-item"><a class="btn btn-primary ms-lg-2" href="<?php echo BASE_URL; ?>/ar.php"><i class="fa-solid fa-camera me-1"></i> AR Experience</a></li>
      </ul>
    </div>
  </div>
</nav>