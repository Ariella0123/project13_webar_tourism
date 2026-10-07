<?php
require_once __DIR__ . '/../includes/functions.php';
admin_required();
if (is_post()) {
    verify_csrf();
    try {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = in_array($_POST['role'] ?? '', ['admin','editor'], true) ? $_POST['role'] : 'editor';
        $password = (string)($_POST['password'] ?? '');
        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Name and a valid email are required.');
        }
        if (strlen($password) < 8) {
            throw new RuntimeException('Password must contain at least 8 characters.');
        }
        $stmt = db()->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        log_action('create', 'Created user ' . $email);
        flash('success', 'User created successfully.');
    } catch (Throwable $error) {
        flash('danger', $error->getCode() === '23000' ? 'Email already exists.' : $error->getMessage());
    }
    redirect('admin/users.php');
}
if (isset($_GET['delete']) && (int)$_GET['delete'] !== (int)$_SESSION['admin_id']) {
    db()->prepare('DELETE FROM users WHERE id=?')->execute([(int)$_GET['delete']]);
    flash('success', 'User deleted.');
    redirect('admin/users.php');
}
$users = db()->query('SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Users';
require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><div class="eyebrow text-success">SYSTEM</div><h1 class="admin-title mb-1">Users</h1><p class="text-muted mb-0">Control who can manage your tourism content.</p></div><button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#userForm"><i class="fa-solid fa-user-plus me-1"></i> Add user</button></div>
<form id="userForm" class="collapse card p-4 mb-4" method="post"><?= csrf_field() ?><div class="row g-3"><div class="col-md-4"><label class="form-label">Full name</label><input name="name" class="form-control" required></div><div class="col-md-4"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div><div class="col-md-4"><label class="form-label">Role</label><select name="role" class="form-select"><option value="editor">Editor</option><option value="admin">Administrator</option></select></div><div class="col-md-6"><label class="form-label">Temporary password</label><input name="password" type="password" class="form-control" minlength="8" required></div><div class="col-12"><button class="btn btn-success">Create user</button></div></div></form>
<div class="card p-3 table-responsive"><table class="table align-middle mb-0"><thead><tr><th>User</th><th>Role</th><th>Created</th><th></th></tr></thead><tbody><?php foreach ($users as $user): ?><tr><td><strong><?= e($user['name']) ?></strong><small class="d-block text-muted"><?= e($user['email']) ?></small></td><td><span class="badge text-bg-<?= $user['role']==='admin' ? 'success' : 'secondary' ?>"><?= e(ucfirst($user['role'])) ?></span></td><td><?= e($user['created_at']) ?></td><td><?php if ((int)$user['id'] !== (int)$_SESSION['admin_id']): ?><a href="?delete=<?= $user['id'] ?>" data-confirm="Delete this user?" class="btn btn-sm btn-outline-danger">Delete</a><?php else: ?><span class="text-muted small">Current account</span><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/_footer.php'; ?>
