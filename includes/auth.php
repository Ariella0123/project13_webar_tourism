<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

function is_admin_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        header("Location: " . BASE_URL . "/admin/login.php");
        exit;
    }
}
?>