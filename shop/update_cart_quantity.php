<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$cartUrl = app_url('shop/cart.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $cartUrl);
    exit;
}

$order_item_id = (int) ($_POST['order_item_id'] ?? 0);
$quantity = (int) ($_POST['quantity'] ?? 1);
$quantity = max(1, min(99, $quantity));
$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare(
    'SELECT oi.id, oi.order_id FROM order_items oi
     INNER JOIN orders o ON o.id = oi.order_id
     WHERE oi.id = ? AND o.user_id = ? AND o.status = ?'
);
$stmt->execute([$order_item_id, $user_id, 'Pending']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['message'] = 'Không thể cập nhật số lượng.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . $cartUrl);
    exit;
}

$order_id = (int) $row['order_id'];
$conn->prepare('UPDATE order_items SET quantity = ? WHERE id = ?')->execute([$quantity, $order_item_id]);

$sumStmt = $conn->prepare('SELECT COALESCE(SUM(quantity * price), 0) FROM order_items WHERE order_id = ?');
$sumStmt->execute([$order_id]);
$total = (float) $sumStmt->fetchColumn();
$conn->prepare('UPDATE orders SET total_price = ? WHERE id = ?')->execute([$total, $order_id]);

$_SESSION['message'] = 'Đã cập nhật số lượng.';
$_SESSION['message_type'] = 'success';
header('Location: ' . $cartUrl);
exit;
