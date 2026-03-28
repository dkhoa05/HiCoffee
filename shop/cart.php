<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/order_options.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

if (!empty($_GET['product_id'])) {
    header('Location: ' . app_url('shop/order_products.php?product_id=' . (int) $_GET['product_id']));
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$optCols = order_items_has_option_columns($conn)
    ? ', oi.size, oi.sweetness, oi.ice_level, oi.item_note'
    : '';

$sql = "SELECT oi.id AS order_item_id, o.id AS order_id, p.name AS product_name,
        oi.quantity, oi.price AS unit_price, (oi.quantity * oi.price) AS total_price, p.image
        {$optCols}
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ? AND o.status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalAmount = 0;
foreach ($orders as $order) {
    $totalAmount += (float) $order['total_price'];
}

$flash = null;
$flashType = null;
if (isset($_SESSION['message'])) {
    $flash = $_SESSION['message'];
    $flashType = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message'], $_SESSION['message_type']);
}

$pageTitle = 'Giỏ hàng | Hi Coffee';
$pageDescription = 'Xem giỏ hàng tại Hi Coffee, cập nhật số lượng món, kiểm tra tùy chọn sản phẩm và xác nhận đơn hàng nhanh chóng.';
require dirname(__DIR__) . '/includes/header.php';
?>

<main id="main-content" class="site-main" tabindex="-1">
    <div id="main" class="content-panel">
        <h1 class="page-title">Giỏ hàng của bạn</h1>
        <p class="page-lead page-lead--wide">
            Kiểm tra lại các món đã chọn, cập nhật số lượng nếu cần và xác nhận đơn hàng để gửi đến quán.
            Thông tin sản phẩm, tùy chọn và tổng giá trị đơn hàng đều được hiển thị rõ ràng để bạn dễ theo dõi.
        </p>

        <?php if ($flash): ?>
            <div class="alert <?= $flashType === 'danger' ? 'alert--danger' : 'alert--success' ?>">
                <?= htmlspecialchars($flash) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <div class="cart-line">
                    <img
                        class="cart-line__img"
                        src="<?= htmlspecialchars(app_url('img/' . $order['image'])) ?>"
                        alt="<?= htmlspecialchars($order['product_name']) ?>"
                        width="88"
                        height="88"
                    >

                    <div class="cart-line__body">
                        <h2 class="cart-line__title"><?= htmlspecialchars($order['product_name']) ?></h2>
                        <p class="cart-line__options"><?= order_item_options_line($order) ?></p>
                        <p class="cart-line__meta">
                            Đơn giá: <?= number_format((int) $order['unit_price']) ?> đ
                        </p>

                        <form method="post" action="<?= htmlspecialchars(app_url('shop/update_cart_quantity.php')) ?>" class="cart-qty-form">
                            <input type="hidden" name="order_item_id" value="<?= (int) $order['order_item_id'] ?>">
                            <label class="cart-qty-form__label" for="quantity-<?= (int) $order['order_item_id'] ?>">Số lượng</label>
                            <div class="cart-qty-form__row">
                                <input
                                    type="number"
                                    id="quantity-<?= (int) $order['order_item_id'] ?>"
                                    name="quantity"
                                    class="cart-qty-form__input"
                                    value="<?= (int) $order['quantity'] ?>"
                                    min="1"
                                    max="99"
                                    required
                                >
                                <button type="submit" class="btn btn--sm">Cập nhật</button>
                            </div>
                        </form>

                        <p class="cart-line__sub">
                            Thành tiền: <strong><?= number_format((float) $order['total_price']) ?> đ</strong>
                        </p>
                    </div>

                    <form method="post" action="<?= htmlspecialchars(app_url('shop/delete_cart_item.php')) ?>" onsubmit="return confirm('Bạn có chắc muốn xóa món này khỏi giỏ hàng không?');">
                        <input type="hidden" name="order_item_id" value="<?= (int) $order['order_item_id'] ?>">
                        <button type="submit" class="btn cart-line__remove">Xóa</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div class="cart-summary">
                <p class="cart-summary__total">
                    Tổng cộng: <strong><?= number_format($totalAmount) ?> đ</strong>
                </p>

                <form
                    method="post"
                    action="<?= htmlspecialchars(app_url('shop/confirm_cart.php')) ?>"
                    class="cart-summary__checkout"
                    onsubmit="return confirm('Xác nhận gửi đơn hàng đến quán? Sau khi gửi, đơn sẽ được chuyển sang trạng thái chờ duyệt.');"
                >
                    <button type="submit" class="btn btn--lg cart-summary__btn">Xác nhận đặt hàng</button>
                </form>

                <p class="cart-summary__hint">
                    Lưu ý: Website demo hiện chưa hỗ trợ thanh toán trực tuyến. Khách hàng thanh toán khi nhận hàng hoặc tại quầy.
                </p>
            </div>
        <?php else: ?>
            <div class="cart-empty">
                <p class="text-center" style="color: var(--color-ink-muted);">
                    Giỏ hàng của bạn hiện chưa có sản phẩm nào.
                </p>
                <p class="text-center">
                    <a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>" class="btn">Xem thực đơn</a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>