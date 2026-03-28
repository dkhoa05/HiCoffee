<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/order_options.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

$user_id = (int) $_SESSION['user_id'];

$hasOpt = order_items_has_option_columns($conn);
$hasConf = order_items_has_confirmed_received($conn);
$hasCancel = orders_has_cancel_reason($conn);
$hasReviews = product_reviews_table_exists($conn);

$optSql = $hasOpt ? 'oi.size, oi.sweetness, oi.ice_level, oi.item_note,' : '';
$confSql = $hasConf ? 'oi.confirmed_received,' : '0 AS confirmed_received,';
$cancelSql = $hasCancel ? 'o.cancel_reason,' : 'NULL AS cancel_reason,';
$revSql = $hasReviews
    ? '(SELECT COUNT(*) FROM product_reviews pr WHERE pr.order_item_id = oi.id) AS review_count'
    : '0 AS review_count';

$sql = "
    SELECT o.id AS order_id, o.status, {$cancelSql}
           oi.id AS order_item_id, oi.quantity, {$confSql}
           {$optSql}
           p.id AS product_id, p.name AS product_name, oi.price AS unit_price,
           (oi.quantity * oi.price) AS line_total,
           o.created_at,
           {$revSql}
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ? AND o.status != 'Pending'
    ORDER BY o.created_at DESC, oi.id ASC
";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = null;
$flashType = null;
if (isset($_SESSION['message'])) {
    $flash = $_SESSION['message'];
    $flashType = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message'], $_SESSION['message_type']);
}

$pageTitle = 'Đơn hàng của tôi | Hi Coffee';
$pageDescription = 'Theo dõi trạng thái đơn hàng tại Hi Coffee, xem lịch sử đặt món, xác nhận đã nhận sản phẩm và gửi đánh giá sau khi hoàn tất.';
require dirname(__DIR__) . '/includes/header.php';
?>

<main id="main-content" class="site-main" tabindex="-1">
    <div id="main" class="content-panel content-panel--wide">
        <h1 class="page-title">Đơn hàng của tôi</h1>
        <p class="page-lead page-lead--wide">
            Tại đây, bạn có thể theo dõi toàn bộ đơn hàng đã gửi đến Hi Coffee, bao gồm trạng thái xử lý,
            thông tin món đã đặt, lý do hủy đơn nếu có, cũng như xác nhận đã nhận món và gửi đánh giá sau khi đơn hoàn tất.
        </p>

        <?php if ($flash): ?>
            <div class="alert <?= $flashType === 'danger' ? 'alert--danger' : 'alert--success' ?>">
                <?= htmlspecialchars($flash) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($rows)): ?>
            <div class="order-history">
                <?php foreach ($rows as $r): ?>
                    <article class="order-history__card">
                        <header class="order-history__head">
                            <span class="order-history__id">Đơn #<?= (int) $r['order_id'] ?></span>
                            <span class="order-history__badge order-history__badge--<?= htmlspecialchars(strtolower($r['status'])) ?>">
                                <?= htmlspecialchars(order_status_label($r['status'])) ?>
                            </span>
                            <time class="order-history__time" datetime="<?= htmlspecialchars($r['created_at']) ?>">
                                <?= htmlspecialchars($r['created_at']) ?>
                            </time>
                        </header>

                        <div class="order-history__body">
                            <strong class="order-history__product"><?= htmlspecialchars($r['product_name']) ?></strong>
                            <p class="order-history__options"><?= order_item_options_line($r) ?></p>
                            <p class="order-history__qty">
                                Số lượng: <?= (int) $r['quantity'] ?>
                                <span class="site-footer__sep">·</span>
                                Đơn giá: <?= number_format((int) $r['unit_price']) ?> đ
                                <span class="site-footer__sep">·</span>
                                <strong>Thành tiền: <?= number_format((float) $r['line_total']) ?> đ</strong>
                            </p>
                        </div>

                        <?php if ($r['status'] === 'Cancelled' && !empty($r['cancel_reason'])): ?>
                            <p class="order-history__cancel-reason">
                                <strong>Lý do hủy đơn:</strong>
                                <?= nl2br(htmlspecialchars($r['cancel_reason'])) ?>
                            </p>
                        <?php endif; ?>

                        <footer class="order-history__actions">
                            <?php if ($r['status'] === 'Placed'): ?>
                                <details class="order-history__cancel-details">
                                    <summary>Yêu cầu hủy đơn hàng</summary>
                                    <form method="post" action="cancel_order.php" class="order-cancel-form">
                                        <input type="hidden" name="order_id" value="<?= (int) $r['order_id'] ?>">
                                        <div class="form-group">
                                            <label for="cancel-<?= (int) $r['order_id'] ?>">Lý do hủy đơn</label>
                                            <textarea
                                                id="cancel-<?= (int) $r['order_id'] ?>"
                                                name="cancel_reason"
                                                rows="3"
                                                required
                                                minlength="5"
                                                maxlength="1000"
                                                placeholder="Ví dụ: Tôi muốn thay đổi món, đặt nhầm sản phẩm hoặc không còn nhu cầu nhận đơn."
                                            ></textarea>
                                        </div>
                                        <button type="submit" class="btn" style="background:#b91c1c;">Xác nhận hủy đơn</button>
                                    </form>
                                </details>

                            <?php elseif ($r['status'] === 'Completed'): ?>
                                <?php if (!$hasReviews): ?>
                                    <span class="order-history__done" style="font-size:0.82rem;">
                                        Chức năng đánh giá sản phẩm hiện chưa được kích hoạt trong hệ thống.
                                    </span>

                                <?php elseif (!$hasConf): ?>
                                    <?php if ((int) $r['review_count'] < 1): ?>
                                        <a href="<?= htmlspecialchars(app_url('shop/review_product.php?order_item_id=' . (int) $r['order_item_id'])) ?>" class="btn">
                                            Đánh giá sản phẩm
                                        </a>
                                    <?php else: ?>
                                        <span class="order-history__done">Bạn đã gửi đánh giá cho món này</span>
                                    <?php endif; ?>

                                <?php elseif (!(int) $r['confirmed_received']): ?>
                                    <form
                                        method="post"
                                        action="<?= htmlspecialchars(app_url('shop/confirm_receive_item.php')) ?>"
                                        class="inline-form"
                                        onsubmit="return confirm('Xác nhận rằng bạn đã nhận món này?');"
                                    >
                                        <input type="hidden" name="order_item_id" value="<?= (int) $r['order_item_id'] ?>">
                                        <button type="submit" class="btn btn--neutral">Xác nhận đã nhận món</button>
                                    </form>

                                <?php elseif ((int) $r['review_count'] < 1): ?>
                                    <a href="<?= htmlspecialchars(app_url('shop/review_product.php?order_item_id=' . (int) $r['order_item_id'])) ?>" class="btn">
                                        Đánh giá sản phẩm
                                    </a>
                                <?php else: ?>
                                    <span class="order-history__done">Bạn đã gửi đánh giá cho món này</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </footer>
                    </article>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="order-empty">
                <p class="text-center" style="color:var(--color-ink-muted);">
                    Bạn chưa có đơn hàng nào đã gửi đến quán.
                </p>
                <p class="text-center">
                    <a href="<?= htmlspecialchars(app_url('shop/cart.php')) ?>">Mở giỏ hàng</a> hoặc <a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>">xem thực đơn và đặt món ngay</a>.
                </p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>