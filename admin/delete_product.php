<?php
require_once dirname(__DIR__) . '/includes/init.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    $conn->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    $_SESSION['message'] = 'Đã xóa sản phẩm.';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Không tìm thấy sản phẩm.';
    $_SESSION['message_type'] = 'danger';
}

header('Location: ' . app_url('admin/admin_products.php'));
exit;
