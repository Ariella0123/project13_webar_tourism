<?php
require_once __DIR__ . '/../includes/functions.php';
admin_required();
$destinations = db()->query('SELECT id,name FROM destinations ORDER BY name')->fetchAll();
if (is_post()) {
    verify_csrf();
    try {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $destination = (int)($_POST['destination_id'] ?? 0);
        if (!$name || !$destination || !preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new RuntimeException('Name, destination and a valid slug are required.');
        }
        $image = upload_image($_FILES['image'] ?? [], 'attractions');
        $s = db()->prepare('INSERT INTO attractions(destination_id,name,slug,short_description,description,youtube_url,maps_url,main_image,status) VALUES(?,?,?,?,?,?,?,?,?)');
        $s->execute([$destination, $name, $slug, trim($_POST['short_description'] ?? ''), trim($_POST['description'] ?? ''), trim($_POST['youtube_url'] ?? ''), trim($_POST['maps_url'] ?? ''), $image, 'active']);
        log_action('create', 'Created attraction ' . $name);
        flash('success', 'Attraction created.');
    } catch (Throwable $e) {
        flash('danger', $e->getMessage());
    }
    redirect('admin/attractions.php');
}
if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM attractions WHERE id=?')->execute([(int)$_GET['delete']]);
    flash('success', 'Attraction deleted.');
    redirect('admin/attractions.php');
}
$items = db()->query('SELECT a.*,d.name destination_name FROM attractions a JOIN destinations d ON d.id=a.destination_id ORDER BY a.created_at DESC')->fetchAll();
$pageTitle = 'Attractions';
require __DIR__ . '/_header.php';
?>
<div class="container py-5">
<div class="d-flex justify-content-between mb-4"><h1>Attractions</h1><button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#attractionForm">Add attraction</button></div>
<form id="attractionForm" class="collapse card p-4 mb-4" method="post" enctype="multipart/form-data"><?= csrf_field() ?><div class="row g-3"><div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" required></div><div class="col-md-6"><label class="form-label">Slug</label><input name="slug" class="form-control" placeholder="heritage-museum" required></div><div class="col-md-6"><label class="form-label">Destination</label><select name="destination_id" class="form-select" required><option value="">Choose destination</option><?php foreach ($destinations as $d):?><option value="<?=$d['id']?>"><?=e($d['name'])?></option><?php endforeach;?></select></div><div class="col-md-6"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp"></div><div class="col-12"><input name="short_description" class="form-control" placeholder="Short description"></div><div class="col-12"><textarea name="description" class="form-control" rows="3" placeholder="Full description"></textarea></div><div class="col-md-6"><input name="youtube_url" class="form-control" placeholder="YouTube URL"></div><div class="col-md-6"><input name="maps_url" class="form-control" placeholder="Google Maps URL"></div><div><button class="btn btn-success">Save attraction</button></div></div></form>
<div class="card p-3 table-responsive"><table class="table align-middle mb-0"><tr><th>Attraction</th><th>Destination</th><th>Status</th><th></th></tr><?php foreach ($items as $i):?><tr><td><strong><?=e($i['name'])?></strong><small class="d-block text-muted"><?=e($i['slug'])?></small></td><td><?=e($i['destination_name'])?></td><td><span class="badge text-bg-success"><?=e($i['status'])?></span></td><td><a data-confirm="Delete this attraction?" href="?delete=<?=$i['id']?>" class="btn btn-sm btn-outline-danger">Delete</a></td></tr><?php endforeach;?></table></div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
