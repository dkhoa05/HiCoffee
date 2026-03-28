<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/order_options.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

$order_item_id = (int) ($_GET['order_item_id'] ?? $_POST['order_item_id'] ?? 0);
$user_id = (int) $_SESSION['user_id'];

function load_review_context(PDO $conn, int $user_id, int $order_item_id): ?array
{
    if (!product_reviews_table_exists($conn)) {
        return null;
    }

    $confSql = order_items_has_confirmed_received($conn) ? ' AND oi.confirmed_received = 1' : '';

    $stmt = $conn->prepare(
        "SELECT oi.id AS order_item_id, oi.product_id, p.name AS product_name
         FROM order_items oi
         INNER JOIN orders o ON o.id = oi.order_id
         INNER JOIN products p ON p.id = oi.product_id
         WHERE oi.id = ? AND o.user_id = ? AND o.status = ?{$confSql}"
    );
    $stmt->execute([$order_item_id, $user_id, 'Completed']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return null;
    }
    $chk = $conn->prepare('SELECT id FROM product_reviews WHERE order_item_id = ?');
    $chk->execute([$order_item_id]);
    if ($chk->fetch()) {
        return null;
    }
    return $row;
}

$ctx = $order_item_id > 0 ? load_review_context($conn, $user_id, $order_item_id) : null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ctx) {
    $display_name = trim($_POST['display_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $stars = (int) ($_POST['stars'] ?? 0);

    if ($display_name === '' || mb_strlen($display_name) > 100) {
        $error = 'Tên hiển thị không hợp lệ.';
    } elseif ($phone === '' && $email === '') {
        $error = 'Vui lòng nhập ít nhất số điện thoại hoặc email.';
    } elseif (mb_strlen($content) < 5) {
        $error = 'Nội dung đánh giá ít nhất 5 ký tự.';
    } elseif ($stars < 1 || $stars > 5) {
        $error = 'Chọn từ 1 đến 5 sao.';
    } else {
        $ctx2 = load_review_context($conn, $user_id, $order_item_id);
        if (!$ctx2) {
            $error = 'Không thể gửi đánh giá cho dòng này.';
        } else {
            $phone = $phone === '' ? null : mb_substr($phone, 0, 30);
            $email = $email === '' ? null : mb_substr($email, 0, 255);
            $ins = $conn->prepare(
                'INSERT INTO product_reviews (product_id, user_id, order_item_id, display_name, phone, email, content, stars)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $ins->execute([
                (int) $ctx2['product_id'],
                $user_id,
                $order_item_id,
                mb_substr($display_name, 0, 100),
                $phone,
                $email,
                mb_substr($content, 0, 2000),
                $stars,
            ]);
            $_SESSION['message'] = 'Cảm ơn bạn đã đánh giá sản phẩm!';
            $_SESSION['message_type'] = 'success';
            header('Location: ' . app_url('shop/products.php#danh-gia'));
            exit;
        }
    }
}

$pageTitle = 'Đánh giá sản phẩm — Hi Coffee';
$pageDescription = 'Đánh giá món đã nhận tại Hi Coffee — 1–5 sao, kèm liên hệ tùy chọn.';
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="main-content" class="site-main" tabindex="-1">
<div id="main" class="content-panel" style="max-width:520px;">
    <?php if (!$ctx): ?>
        <h1 class="page-title">Không thể đánh giá</h1>
        <p class="page-lead">
            <?php if (!product_reviews_table_exists($conn)): ?>
                Chưa có bảng <code>product_reviews</code> trong CSDL. Chạy <code>sql/migrate_order_options_reviews.sql</code> (hoặc import lại <code>sql/reset_fresh.sql</code>).
            <?php else: ?>
                Chỉ đánh giá được khi đơn đã hoàn thành<?= order_items_has_confirmed_received($conn) ? ', bạn đã xác nhận nhận món' : '' ?> và chưa gửi đánh giá cho dòng đó.
            <?php endif; ?>
        </p>
        <p class="text-center"><a href="<?= htmlspecialchars(app_url('shop/order.php')) ?>" class="btn">Về đơn hàng</a></p>
    <?php else: ?>
        <h1 class="page-title">Đánh giá: <?= htmlspecialchars($ctx['product_name']) ?></h1>
        <?php if ($error): ?>
            <div class="alert alert--danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= htmlspecialchars(app_url('shop/review_product.php')) ?>" class="support-form">
            <input type="hidden" name="order_item_id" value="<?= (int) $ctx['order_item_id'] ?>">
            <div class="form-group">
                <label for="display_name">Tên hiển thị</label>
                <input type="text" id="display_name" name="display_name" required maxlength="100"
                       value="<?= htmlspecialchars($_POST['display_name'] ?? ($_SESSION['username'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" maxlength="30" placeholder="Bắt buộc nếu bỏ trống email"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="255" placeholder="Bắt buộc nếu bỏ trống SĐT"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="stars">Số sao (1–5)</label>
                <select id="stars" name="stars" required>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>" <?= (int) ($_POST['stars'] ?? 5) === $i ? 'selected' : '' ?>><?= $i ?> sao</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Nội dung đánh giá</label>
                <textarea id="content" name="content" rows="4" required minlength="5" maxlength="2000" placeholder="Cảm nhận của bạn về món này"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn--block">Gửi đánh giá</button>
        </form>
    <?php endif; ?>
</div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
