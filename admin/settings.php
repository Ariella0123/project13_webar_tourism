<?php
require_once __DIR__ . '/../includes/functions.php';
admin_required();
db()->exec("CREATE TABLE IF NOT EXISTS settings (setting_key VARCHAR(100) PRIMARY KEY, setting_value TEXT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");
if (is_post()) {
    verify_csrf();
    $settings = ['site_name'=>trim($_POST['site_name']??APP_NAME),'site_tagline'=>trim($_POST['site_tagline']??''),'contact_email'=>trim($_POST['contact_email']??''),'maintenance_mode'=>isset($_POST['maintenance_mode']) ? '1' : '0'];
    $stmt = db()->prepare('INSERT INTO settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
    foreach ($settings as $key=>$value) {
        $stmt->execute([$key,$value]);
    }
    log_action('update', 'Updated system settings');
    flash('success', 'Settings saved.');
    redirect('admin/settings.php');
}
$rows = db()->query('SELECT setting_key,setting_value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
$pageTitle = 'Settings';
require __DIR__ . '/_header.php';
?>
<div class="mb-4"><div class="eyebrow text-success">SYSTEM</div><h1 class="admin-title mb-1">Settings</h1><p class="text-muted mb-0">Keep your public experience and contact details up to date.</p></div>
<form class="card p-4" method="post"><?= csrf_field() ?><div class="row g-4"><div class="col-md-6"><label class="form-label">Site name</label><input class="form-control" name="site_name" value="<?= e($rows['site_name'] ?? APP_NAME) ?>"></div><div class="col-md-6"><label class="form-label">Tagline</label><input class="form-control" name="site_tagline" value="<?= e($rows['site_tagline'] ?? 'Discover culture through your camera.') ?>"></div><div class="col-md-6"><label class="form-label">Contact email</label><input class="form-control" type="email" name="contact_email" value="<?= e($rows['contact_email'] ?? '') ?>"></div><div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance" <?= ($rows['maintenance_mode']??'0')==='1' ? 'checked' : '' ?>><label class="form-check-label" for="maintenance">Enable maintenance mode</label><small class="d-block text-muted">Use this while updating content. Admin pages remain accessible.</small></div></div><div><button class="btn btn-success"><i class="fa-solid fa-save me-1"></i> Save settings</button></div></div></form>
<?php require __DIR__ . '/_footer.php'; ?>
