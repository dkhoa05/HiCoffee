<?php
session_start();
require dirname(__DIR__) . '/db.php';

// Kiểm tra dữ liệu từ form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Lấy thông tin sản phẩm từ cơ sở dữ liệu
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        // Tạo đơn hàng trong bảng orders
        $sql_order = "INSERT INTO orders (username, product_id, quantity, status) VALUES (?, ?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->execute([$username, $product_id, $quantity, 'Pending']);

        echo "<p>Đơn hàng của bạn đã được ghi nhận. Cảm ơn bạn đã đặt hàng tại Hi Coffee!</p>";
    } else {
        echo "<p>Sản phẩm không hợp lệ. Vui lòng thử lại.</p>";
    }
} else {
    echo "<p>Đã xảy ra lỗi khi xử lý đơn hàng. Vui lòng thử lại sau.</p>";
}
?>
