<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';


$orders = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $username = isset($_POST['username']) ? trim($_POST['username']) : null;

    // Kiểm tra tên đăng nhập
    if (!empty($username)) {
        $sql = "
            SELECT orders.id AS order_id, users.username, orders.status, orders.created_at
            FROM orders
            INNER JOIN users ON orders.user_id = users.id
            WHERE users.username = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<script>alert('Vui lòng nhập tên đăng nhập!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đặt hàng - Hi Coffee</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('css/style.css')) ?>">
</head>
<body>
    <div id="body">
        <!-- Header -->
        <div id="header">
            <div id="logo-and-title">
                <img id="mainlogo" src="<?= htmlspecialchars(app_url('img/mainlogo.png')) ?>" alt="Hi Coffee Logo">
                <div id="header-text">
                    <h1 id="title">Hi Coffee</h1>
                    <p id="tagline">Welcome to Hi coffee, Hi coffee hi you</p>
                </div>
            </div>
            <ul id="navlinks">
                <li><a href="<?= htmlspecialchars(app_url('index.php')) ?>">Trang chủ</a></li>
                <li><a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>">Các sản phẩm</a></li>
                <li><a href="<?= htmlspecialchars(app_url('shop/cart.php')) ?>">Chi tiết đơn hàng</a></li>
                <li><a href="<?= htmlspecialchars(app_url('shop/order.php')) ?>">Danh sách đặt hàng</a></li>
                <li><a href="<?= htmlspecialchars(app_url('support.php')) ?>">Tư vấn-Hỗ trợ</a></li>
                <li><a href="<?= htmlspecialchars(app_url('about.php')) ?>">Về chúng tôi</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div id="main">
            <h2>Kiểm tra đơn hàng</h2>
            <form method="POST" action="<?= htmlspecialchars(app_url('dev/order_list.php')) ?>" class="order-form">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required placeholder="Nhập tên đăng nhập"><br><br>
                <button type="submit" class="btn">Kiểm tra</button>
            </form>

            <br>
            <?php if (!empty($orders)): ?>
                <h3>Danh sách đơn hàng</h3>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Tên đăng nhập</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>
                                <td><?= htmlspecialchars($order['status']) ?></td>
                                <td><?= htmlspecialchars($order['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <p>Không tìm thấy đơn hàng nào cho tên đăng nhập này.</p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div id="footer" class="about contacts">
            <p>
                Contact: <br>
                Ngô Liêm Thượng<br>
                Phone&Zalo: 0909203873<br>
                Instagram: <a href="https://www.instagram.com/n1t_19" title="Instagram">Instagram</a><br>
                Facebook: <a href="https://www.facebook.com/profile.php?id=100040843357437" title="Facebook">Facebook</a>
            </p>
        </div>
    </div>
</body>
</html>
