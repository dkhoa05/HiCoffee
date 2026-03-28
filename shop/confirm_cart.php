<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('shop/cart.php'));
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare(
    'SELECT COUNT(*) FROM orders o
     INNER JOIN order_items oi ON o.id = oi.order_id
     WHERE o.user_id = ? AND o.status = ?'
);
$stmt->execute([$user_id, 'Pending']);
$lineCount = (int) $stmt->fetchColumn();

if ($lineCount === 0) {
    $_SESSION['message'] = 'Giỏ hàng trống — không có món nào để xác nhận.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . app_url('shop/cart.php'));
    exit;
}

$upd = $conn->prepare(
    'UPDATE orders o
     SET o.status = ?
     WHERE o.user_id = ? AND o.status = ?
     AND EXISTS (SELECT 1 FROM order_items oi WHERE oi.order_id = o.id)'
);

try {
    $upd->execute(['Placed', $user_id, 'Pending']);
    $_SESSION['message'] = 'Đã gửi đơn — trạng thái chờ quán duyệt. Xem tại Đơn hàng; bạn có thể hủy kèm lý do nếu đổi ý. (Demo: thanh toán khi nhận món / tại quầy.)';
} catch (PDOException $e) {
    $upd2 = $conn->prepare(
        'UPDATE orders o
         SET o.status = ?
         WHERE o.user_id = ? AND o.status = ?
         AND EXISTS (SELECT 1 FROM order_items oi WHERE oi.order_id = o.id)'
    );
    $upd2->execute(['Completed', $user_id, 'Pending']);
    $_SESSION['message'] = 'Đã xác nhận đơn. (CSDL chưa có trạng thái &quot;Chờ duyệt&quot; — đơn chuyển thẳng &quot;Hoàn thành&quot;. Chạy sql/migrate_order_options_reviews.sql để dùng đủ luồng duyệt/hủy.)';
}
$_SESSION['message_type'] = 'success';
header('Location: ' . app_url('shop/order.php'));
exit;
