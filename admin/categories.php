<?php

require_once __DIR__ . '/../includes/functions.php';

admin_required();

if (is_post()) {
    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($id) {
        $statement = db()->prepare(
            'UPDATE categories SET name = ?, slug = ? WHERE id = ?'
        );
        $statement->execute([$name, $slug, $id]);
    } else {
        $statement = db()->prepare(
            'INSERT INTO categories (name, slug) VALUES (?, ?)'
        );
        $statement->execute([$name, $slug]);
    }

    flash('success', 'Category saved.');
    redirect('admin/categories.php');
}

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM categories WHERE id = ?')
        ->execute([(int) $_GET['delete']]);

    redirect('admin/categories.php');
}

$items = db()->query(
    'SELECT c.*, COUNT(d.id) AS destination_count
     FROM categories c
     LEFT JOIN destinations d ON d.category_id = c.id
     GROUP BY c.id
     ORDER BY c.name'
)->fetchAll();

$pageTitle = 'Categories';
require __DIR__ . '/_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="eyebrow">Tourism content</span>
            <h1 class="h3 mb-0">Categories</h1>
        </div>
        <button
            class="btn btn-success"
            data-bs-toggle="collapse"
            data-bs-target="#categoryForm"
        >
            Add category
        </button>
    </div>

    <form id="categoryForm" class="collapse card p-4 mb-4" method="post">
        <?= csrf_field() ?>
        <div class="row g-2">
            <div class="col-md-5">
                <input
                    name="name"
                    class="form-control"
                    placeholder="Category name"
                    required
                >
            </div>
            <div class="col-md-5">
                <input
                    name="slug"
                    class="form-control"
                    placeholder="slug"
                    required
                >
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100">Save</button>
            </div>
        </div>
    </form>

    <div class="card p-3 table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Destinations</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td><?= e($item['slug']) ?></td>
                        <td><?= (int) $item['destination_count'] ?></td>
                        <td class="text-end">
                            <a
                                data-confirm="Delete category?"
                                href="?delete=<?= (int) $item['id'] ?>"
                                class="btn btn-sm btn-outline-danger"
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
