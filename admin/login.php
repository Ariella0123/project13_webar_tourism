<?php
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('admin/index.php');
}

$error = '';
$databaseError = '';

try {
    if (is_post()) {
        verify_csrf();
        $statement = db()->prepare('SELECT * FROM users WHERE email=? AND role IN ("admin","editor")');
        $statement->execute([trim($_POST['email'] ?? '')]);
        $user = $statement->fetch();

        if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            log_action('login', 'Administrator logged in');
            redirect('admin/index.php');
        }

        $error = 'Invalid email or password.';
    }
} catch (PDOException $exception) {
    $databaseError = 'The database is not available. Start MySQL in XAMPP, import database/database.sql, and refresh this page.';
}

$pageTitle = 'Admin Login';
require __DIR__ . '/../includes/header.php';
?>
<section class="auth-page">
    <div class="auth-card card">
        <div class="text-center mb-4">
            <div class="auth-icon"><i class="fa-solid fa-lock"></i></div>
            <div class="eyebrow text-success mt-3">SECURE WORKSPACE</div>
            <h1 class="section-title mb-1">Admin login</h1>
            <p class="text-muted mb-0">Manage your AR Tourism Explorer platform.</p>
        </div>
        <?php if ($databaseError): ?>
            <div class="alert alert-warning"><i class="fa-solid fa-database me-2"></i><?= e($databaseError) ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <label class="form-label">Email address</label>
            <div class="input-group mb-3"><span class="input-group-text"><i class="fa-solid fa-envelope"></i></span><input class="form-control" type="email" name="email" autocomplete="username" required></div>
            <label class="form-label">Password</label>
            <div class="input-group mb-4"><span class="input-group-text"><i class="fa-solid fa-key"></i></span><input class="form-control" type="password" name="password" autocomplete="current-password" required></div>
            <button class="btn btn-success btn-lg w-100" <?= $databaseError ? 'disabled' : '' ?>>Sign in <i class="fa-solid fa-arrow-right ms-1"></i></button>
        </form>
        <div class="text-center mt-4"><small class="text-muted">Demo account: admin@example.com / password</small></div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
