<?php

require_once __DIR__ . '/../includes/functions.php';

admin_required();

if (is_post()) {
    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');

    if (!$name || !preg_match('/^[a-z0-9-]+$/', $slug)) {
        flash('danger', 'Name and a valid slug are required.');
    } else {
        if ($id) {
            $statement = db()->prepare(
                'UPDATE destinations
                 SET name = ?, slug = ?, short_description = ?, description = ?
                 WHERE id = ?'
            );
            $statement->execute([
                $name,
                $slug,
                $shortDescription,
                $description,
                $id,
            ]);
            log_action('update', 'Updated destination ' . $name);
        } else {
            $statement = db()->prepare(
                'INSERT INTO destinations
                    (name, slug, short_description, description)
                 VALUES (?, ?, ?, ?)'
            );
            $statement->execute([
                $name,
                $slug,
                $shortDescription,
                $description,
            ]);
            log_action('create', 'Created destination ' . $name);
        }

        flash('success', 'Destination saved.');
    }

    redirect('admin/destinations.php');
}

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM destinations WHERE id = ?')
        ->execute([(int) $_GET['delete']]);

    flash('success', 'Destination deleted.');
    redirect('admin/destinations.php');
}

$items = db()->query(
    'SELECT * FROM destinations ORDER BY created_at DESC'
)->fetchAll();

$pageTitle = 'Manage Destinations';
require __DIR__ . '/_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="eyebrow">Tourism content</span>
            <h1 class="h3 mb-0">Destinations</h1>
        </div>
        <button
            class="btn btn-success"
            data-bs-toggle="collapse"
            data-bs-target="#destinationForm"
        >
            Add destination
        </button>
    </div>

    <form
        id="destinationForm"
        class="collapse card p-4 mb-4"
        method="post"
    >
        <?= csrf_field() ?>
        <input
            type="hidden"
            name="id"
            value="<?= e($_GET['edit'] ?? '') ?>"
        >

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" name="name" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Slug</label>
                <input class="form-control" name="slug" required>
            </div>
            <div class="col-12">
                <label class="form-label">Short description</label>
                <input class="form-control" name="short_description">
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="4"></textarea>
            </div>
            <div>
                <button class="btn btn-success">Save destination</button>
            </div>
        </div>
    </form>

    <div class="table-responsive card p-3">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td><?= e($item['slug']) ?></td>
                        <td>
                            <span class="badge text-bg-success">
                                <?= e($item['status']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a
                                class="btn btn-sm btn-outline-danger"
                                data-confirm="Delete this destination?"
                                href="?delete=<?= (int) $item['id'] ?>"
                            >
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
