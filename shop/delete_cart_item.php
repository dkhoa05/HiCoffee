<?php
require_once dirname(__DIR__) . '/includes/init.php';

$cartUrl = app_url('shop/cart.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}

$order_item_id = isset($_POST['order_item_id']) ? (int) $_POST['order_item_id'] : 0;

if ($order_item_id > 0) {
    $stmt = $conn->prepare(
        'SELECT oi.order_id FROM order_items oi
         JOIN orders o ON o.id = oi.order_id
         WHERE oi.id = ? AND o.user_id = ?'
    );
    $stmt->execute([$order_item_id, $_SESSION['user_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $order_id = (int) $row['order_id'];
        $conn->prepare('DELETE FROM order_items WHERE id = ?')->execute([$order_item_id]);

        $cnt = $conn->prepare('SELECT COUNT(*) FROM order_items WHERE order_id = ?');
        $cnt->execute([$order_id]);
        if ((int) $cnt->fetchColumn() === 0) {
            $conn->prepare('DELETE FROM orders WHERE id = ?')->execute([$order_id]);
        }
    }
}

header('Location: ' . $cartUrl);
exit;
