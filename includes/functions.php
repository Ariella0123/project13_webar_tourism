<?php

declare(strict_types=1);
require_once __DIR__ . '/database.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}
function redirect(string $path): never
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)));
    exit;
}
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = [$type, $message];
}
function flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}
function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function verify_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}
function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}
function upload_image(array $file, string $folder = 'images'): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Invalid or oversized image.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('Only valid JPG, PNG or WebP images are allowed.');
    }
    $dir = UPLOAD_DIR . DIRECTORY_SEPARATOR . $folder;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . DIRECTORY_SEPARATOR . $name)) {
        throw new RuntimeException('Unable to save upload.');
    }
    return 'uploads/' . $folder . '/' . $name;
}
function youtube_id(?string $value): ?string
{
    if (!$value) {
        return null;
    }
    $parts = parse_url(trim($value));
    if (!$parts || empty($parts['host'])) {
        return null;
    }
    parse_str($parts['query'] ?? '', $query);
    if (!empty($query['v'])) {
        return preg_match('/^[\w-]{11}$/', $query['v']) ? $query['v'] : null;
    }
    $path = trim($parts['path'] ?? '', '/');
    $candidate = preg_replace('#^(embed/|shorts/|v/)#', '', $path);
    return preg_match('/^[\w-]{11}$/', $candidate) ? $candidate : null;
}
function admin_required(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect('admin/login.php');
    }
}
function log_action(string $action, string $description): void
{
    if (empty($_SESSION['admin_id'])) {
        return;
    }
    $stmt = db()->prepare('INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_SESSION['admin_id'], $action, $description, $_SERVER['REMOTE_ADDR'] ?? '']);
}
