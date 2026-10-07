<?php

require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$id = (int) ($_GET['id'] ?? 0);
$statement = db()->prepare(
    "SELECT id, name, image_path, description, youtube_url, target_file, target_status
     FROM ar_posters
     WHERE id = ? AND status = 'active'"
);
$statement->execute([$id]);
$poster = $statement->fetch();

if (!$poster) {
    http_response_code(404);
    echo json_encode(['error' => 'Poster not found']);
    exit;
}

$poster['target_url'] = $poster['target_file']
    ? asset($poster['target_file'])
    : null;

$hotspots = db()->prepare(
    'SELECT h.*, a.name AS attraction_name, a.short_description,
        a.main_image, a.maps_url, a.youtube_url
     FROM ar_hotspots h
     LEFT JOIN attractions a ON a.id = h.attraction_id
     WHERE h.poster_id = ?
     ORDER BY h.z_index'
);
$hotspots->execute([$id]);

echo json_encode(
    [
        'poster' => $poster,
        'hotspots' => $hotspots->fetchAll(),
    ],
    JSON_UNESCAPED_SLASHES
);
