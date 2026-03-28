<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

$orderUrl = app_url('shop/order.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $orderUrl);
    exit;
}

if (!order_items_has_confirmed_received($conn)) {
    $_SESSION['message'] = 'CSDL chưa có cột xác nhận nhận hàng. Chạy file sql/migrate_order_options_reviews.sql.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $orderUrl);
    exit;
}

$order_item_id = (int) ($_POST['order_item_id'] ?? 0);
$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare(
    'SELECT oi.id FROM order_items oi
     INNER JOIN orders o ON o.id = oi.order_id
     WHERE oi.id = ? AND o.user_id = ? AND o.status = ? AND oi.confirmed_received = 0'
);
$stmt->execute([$order_item_id, $user_id, 'Completed']);
if (!$stmt->fetch()) {
    $_SESSION['message'] = 'Không thể xác nhận nhận hàng cho dòng này.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $orderUrl);
    exit;
}

$conn->prepare('UPDATE order_items SET confirmed_received = 1 WHERE id = ?')->execute([$order_item_id]);

$_SESSION['message'] = 'Đã xác nhận bạn đã nhận món. Bạn có thể đánh giá sản phẩm.';
$_SESSION['message_type'] = 'success';
header('Location: ' . $orderUrl);
exit;
