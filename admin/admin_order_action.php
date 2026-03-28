<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$ordersUrl = app_url('admin/admin_orders.php');

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $ordersUrl);
    exit;
}

$order_id = (int) ($_POST['order_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($order_id <= 0 || $action !== 'complete') {
    header('Location: ' . $ordersUrl);
    exit;
}

$stmt = $conn->prepare('SELECT id FROM orders WHERE id = ? AND status = ?');
$stmt->execute([$order_id, 'Placed']);
if (!$stmt->fetch()) {
    $_SESSION['message'] = 'Đơn không ở trạng thái chờ duyệt.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $ordersUrl);
    exit;
}

try {
    $conn->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['Completed', $order_id]);
    $_SESSION['message'] = 'Đã duyệt đơn #' . $order_id . ' (hoàn thành).';
    $_SESSION['message_type'] = 'success';
} catch (PDOException $e) {
    $_SESSION['message'] = 'Không cập nhật được đơn. Kiểm tra CSDL / trạng thái đơn.';
    $_SESSION['message_type'] = 'danger';
}
header('Location: ' . $ordersUrl);
exit;
