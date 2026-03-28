<?php
require_once dirname(__DIR__) . '/includes/init.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    $conn->prepare('DELETE FROM comments WHERE id = ?')->execute([$id]);
}

header('Location: ' . app_url('admin/admin_comments.php'));
exit;
