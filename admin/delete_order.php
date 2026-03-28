<?php
require_once dirname(__DIR__) . '/includes/init.php';

$loginUrl = app_url('auth/login.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $loginUrl);
    exit;
}

$is_admin = !empty($_SESSION['is_admin']);
$user_id = (int) $_SESSION['user_id'];
$adminOrders = app_url('admin/admin_orders.php');
$shopCart = app_url('shop/cart.php');

if (!isset($_GET['order_id'])) {
    $_SESSION['message'] = 'Không tìm thấy đơn hàng.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . ($is_admin ? $adminOrders : $shopCart));
    exit;
}

$order_id = (int) $_GET['order_id'];

try {
    if ($is_admin) {
        $conn->beginTransaction();
        $conn->prepare('DELETE FROM order_items WHERE order_id = ?')->execute([$order_id]);
        $conn->prepare('DELETE FROM orders WHERE id = ?')->execute([$order_id]);
        $conn->commit();
        $_SESSION['message'] = 'Đã xóa đơn hàng.';
        $_SESSION['message_type'] = 'success';
    } else {
        $stmt = $conn->prepare('SELECT id FROM orders WHERE id = ? AND user_id = ?');
        $stmt->execute([$order_id, $user_id]);
        if ($stmt->fetch()) {
            $conn->beginTransaction();
            $conn->prepare('DELETE FROM order_items WHERE order_id = ?')->execute([$order_id]);
            $conn->prepare('DELETE FROM orders WHERE id = ?')->execute([$order_id]);
            $conn->commit();
            $_SESSION['message'] = 'Đã xóa đơn hàng.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Bạn không có quyền xóa đơn này.';
            $_SESSION['message_type'] = 'danger';
        }
    }
} catch (PDOException $e) {
    $conn->rollBack();
    $_SESSION['message'] = 'Lỗi khi xóa đơn hàng.';
    $_SESSION['message_type'] = 'danger';
}

header('Location: ' . ($is_admin ? $adminOrders : $shopCart));
exit;
