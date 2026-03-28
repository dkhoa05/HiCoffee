<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

$orderUrl = app_url('shop/order.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $orderUrl);
    exit;
}

$order_id = (int) ($_POST['order_id'] ?? 0);
$reason = trim($_POST['cancel_reason'] ?? '');
$user_id = (int) $_SESSION['user_id'];

if ($order_id <= 0 || mb_strlen($reason) < 5) {
    $_SESSION['message'] = 'Vui lòng nhập lý do hủy (ít nhất 5 ký tự).';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $orderUrl);
    exit;
}

$reason = mb_substr($reason, 0, 1000);

$stmt = $conn->prepare('SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = ?');
$stmt->execute([$order_id, $user_id, 'Placed']);
if (!$stmt->fetch()) {
    $_SESSION['message'] = 'Chỉ có thể hủy đơn đang chờ duyệt.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $orderUrl);
    exit;
}

if (orders_has_cancel_reason($conn)) {
    $conn->prepare('UPDATE orders SET status = ?, cancel_reason = ? WHERE id = ?')->execute(['Cancelled', $reason, $order_id]);
} else {
    $conn->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['Cancelled', $order_id]);
}

$_SESSION['message'] = 'Đã hủy đơn #' . $order_id . '.';
$_SESSION['message_type'] = 'success';
header('Location: ' . $orderUrl);
exit;
