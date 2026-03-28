<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/order_options.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}

$adminNavCurrent = 'orders';

$adminHasOpts = order_items_has_option_columns($conn);
$optCols = $adminHasOpts
    ? 'oi.size, oi.sweetness, oi.ice_level, oi.item_note,'
    : '';
$cancelSel = orders_has_cancel_reason($conn) ? 'o.cancel_reason,' : 'NULL AS cancel_reason,';

$sql_orders = "
    SELECT o.id AS order_id, u.username, p.name AS product_name, oi.quantity,
           {$optCols}
           (oi.quantity * oi.price) AS total_price, o.status, {$cancelSel} o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    ORDER BY o.created_at DESC
";
$orders = $conn->query($sql_orders)->fetchAll(PDO::FETCH_ASSOC);

$flash = null;
$flashType = null;
if (isset($_SESSION['message'])) {
    $flash = $_SESSION['message'];
    $flashType = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message'], $_SESSION['message_type']);
}

$pageTitle = 'Quản lý đơn hàng — Hi Coffee';
$pageDescription = 'Xem đơn, tùy chọn món, duyệt đơn chờ xử lý.';
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="main-content" class="site-main site-main--admin" tabindex="-1">
<div id="main" class="content-panel" style="max-width:1180px;">
    <h1 class="page-title">Quản lý đơn hàng</h1>
    <p class="page-lead">Đơn <strong>Placed</strong> = khách đã gửi, chờ quán duyệt. Duyệt xong chuyển <strong>Completed</strong> để khách xác nhận nhận hàng và đánh giá.</p>

    <?php if ($flash): ?>
        <div class="alert <?= $flashType === 'danger' ? 'alert--danger' : 'alert--success' ?>"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="data-table-wrap">
        <table class="data-table data-table--orders">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Khách</th>
                    <th>Sản phẩm</th>
                    <th>Tùy chọn</th>
                    <th>SL</th>
                    <th>Tổng</th>
                    <th>TT</th>
                    <th>Ngày</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= (int) $order['order_id'] ?></td>
                        <td><?= htmlspecialchars($order['username']) ?></td>
                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                        <td class="data-table__opts">
                            <?php if ($adminHasOpts): ?>
                                Size <?= htmlspecialchars((string) ($order['size'] ?? '')) ?>
                                · <?= htmlspecialchars(order_sweetness_choices()[(string) ($order['sweetness'] ?? 'vua')] ?? (string) ($order['sweetness'] ?? '')) ?>
                                · <?= htmlspecialchars(order_ice_choices()[(string) ($order['ice_level'] ?? 'it')] ?? (string) ($order['ice_level'] ?? '')) ?>
                                <?php if (!empty($order['item_note'])): ?>
                                    <br><em><?= htmlspecialchars($order['item_note']) ?></em>
                                <?php endif; ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?= (int) $order['quantity'] ?></td>
                        <td><?= number_format((float) $order['total_price']) ?> đ</td>
                        <td>
                            <?= htmlspecialchars($order['status']) ?>
                            <?php if ($order['status'] === 'Cancelled' && !empty($order['cancel_reason'])): ?>
                                <br><small class="data-table__reason"><?= htmlspecialchars(mb_substr($order['cancel_reason'], 0, 80)) ?><?= mb_strlen((string) $order['cancel_reason']) > 80 ? '…' : '' ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td class="data-table__actions">
                            <?php if ($order['status'] === 'Placed'): ?>
                                <form method="post" action="admin_order_action.php" class="inline-form" onsubmit="return confirm('Duyệt đơn này?');">
                                    <input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>">
                                    <input type="hidden" name="action" value="complete">
                                    <button type="submit" class="btn btn--sm">Duyệt</button>
                                </form>
                            <?php endif; ?>
                            <a href="<?= htmlspecialchars(app_url('admin/delete_order.php?order_id=' . (int) $order['order_id'])) ?>" class="btn btn--sm" style="background:#c62828;" onclick="return confirm('Xóa đơn?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($orders)): ?>
        <p class="text-center" style="color:var(--color-ink-muted);">Chưa có đơn hàng.</p>
    <?php endif; ?>
</div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
