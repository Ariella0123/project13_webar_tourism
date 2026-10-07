<?php

require_once __DIR__ . '/../includes/functions.php';

admin_required();

// Keep existing installations compatible with the new poster video field.
$youtubeColumn = db()->query(
    "SHOW COLUMNS FROM ar_posters LIKE 'youtube_url'"
)->fetch();

if (!$youtubeColumn) {
    db()->exec(
        'ALTER TABLE ar_posters
         ADD COLUMN youtube_url VARCHAR(500) NULL AFTER description'
    );
}

if (is_post()) {
    verify_csrf();

    try {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $youtubeUrl = trim($_POST['youtube_url'] ?? '');

        if (!$name) {
            throw new RuntimeException('Please enter a poster name.');
        }

        if ($youtubeUrl && !youtube_id($youtubeUrl)) {
            throw new RuntimeException('Please enter a valid YouTube link.');
        }

        $image = upload_image($_FILES['poster'] ?? [], 'posters');

        if (!$image) {
            throw new RuntimeException('Please choose a poster image.');
        }

        $statement = db()->prepare(
            'INSERT INTO ar_posters
                (name, image_path, description, youtube_url)
             VALUES (?, ?, ?, ?)'
        );
        $statement->execute([
            $name,
            $image,
            $description,
            $youtubeUrl ?: null,
        ]);

        log_action('create', 'Uploaded AR poster ' . $name);
        flash('success', 'Poster uploaded. Compile its image target before publishing.');
    } catch (Throwable $exception) {
        flash('danger', $exception->getMessage());
    }

    redirect('admin/posters.php');
}

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM ar_posters WHERE id = ?')
        ->execute([(int) $_GET['delete']]);

    flash('success', 'Poster deleted.');
    redirect('admin/posters.php');
}

$items = db()->query(
    'SELECT * FROM ar_posters ORDER BY created_at DESC'
)->fetchAll();

$pageTitle = 'AR Posters';
require __DIR__ . '/_header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="eyebrow">AR management</span>
            <h1 class="h3 mb-0">AR posters</h1>
        </div>
        <button
            class="btn btn-success"
            data-bs-toggle="collapse"
            data-bs-target="#posterForm"
        >
            Upload poster
        </button>
    </div>

    <form
        id="posterForm"
        class="collapse card p-4 mb-4"
        method="post"
        enctype="multipart/form-data"
    >
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="posterName">Poster name</label>
                <input
                    id="posterName"
                    class="form-control"
                    name="name"
                    placeholder="Tourism poster name"
                    required
                >
            </div>
            <div class="col-md-6">
                <label class="form-label" for="posterImage">Poster image</label>
                <input
                    id="posterImage"
                    class="form-control"
                    type="file"
                    name="poster"
                    accept="image/jpeg,image/png,image/webp"
                    required
                >
            </div>
            <div class="col-12">
                <label class="form-label" for="youtubeUrl">
                    YouTube link
                </label>
                <input
                    id="youtubeUrl"
                    class="form-control"
                    type="url"
                    name="youtube_url"
                    placeholder="https://www.youtube.com/watch?v=..."
                >
                <div class="form-text">
                    Optional. Supports standard YouTube, youtu.be, Shorts and
                    embed links.
                </div>
            </div>
            <div class="col-12">
                <label class="form-label" for="posterDescription">
                    Description
                </label>
                <textarea
                    id="posterDescription"
                    class="form-control"
                    name="description"
                    rows="3"
                    placeholder="Describe the AR poster"
                ></textarea>
            </div>
            <div>
                <button class="btn btn-success">Upload poster</button>
            </div>
        </div>
    </form>

    <div class="row g-4">
        <?php foreach ($items as $poster): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <img
                        class="card-img-top"
                        src="<?= asset($poster['image_path']) ?>"
                        alt="<?= e($poster['name']) ?>"
                    >
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-2">
                            <h5><?= e($poster['name']) ?></h5>
                            <span
                                class="badge text-bg-<?= $poster['target_status'] === 'READY'
                                    ? 'success'
                                    : 'secondary' ?>"
                            >
                                <?= e($poster['target_status']) ?>
                            </span>
                        </div>

                        <?php if (!empty($poster['description'])): ?>
                            <p class="text-muted small">
                                <?= e($poster['description']) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($poster['youtube_url'])): ?>
                            <a
                                class="btn btn-sm btn-outline-danger"
                                href="<?= e($poster['youtube_url']) ?>"
                                target="_blank"
                                rel="noopener"
                            >
                                <i class="fa-brands fa-youtube me-1"></i>
                                Watch video
                            </a>
                        <?php endif; ?>

                        <a
                            data-confirm="Delete this poster?"
                            href="?delete=<?= (int) $poster['id'] ?>"
                            class="btn btn-sm btn-outline-danger float-end"
                        >
                            Delete
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
