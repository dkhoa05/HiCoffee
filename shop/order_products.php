<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/order_options.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

$message = '';
$messageIsError = false;

if (isset($_GET['product_id'])) {
    $product_id = (int) $_GET['product_id'];
    $stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $_SESSION['order'] = [
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => (int) $product['price'],
            'image' => $product['image'],
            'quantity' => 1,
        ];
    } else {
        $message = 'Sản phẩm không tồn tại.';
        $messageIsError = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['order'])) {
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
    $product_id = (int) $_SESSION['order']['product_id'];
    $price = (int) $_SESSION['order']['price'];
    $total_price = $price * $quantity;
    $user_id = (int) $_SESSION['user_id'];

    $size = order_normalize_size((string) ($_POST['size'] ?? 'M'));
    $sweetness = order_normalize_sweetness((string) ($_POST['sweetness'] ?? 'vua'));
    $ice_level = order_normalize_ice((string) ($_POST['ice_level'] ?? 'it'));
    $item_note = trim((string) ($_POST['item_note'] ?? ''));
    $item_note = $item_note === '' ? null : mb_substr($item_note, 0, 500);

    $sql_order = 'INSERT INTO orders (user_id, total_price) VALUES (?, ?)';
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->execute([$user_id, $total_price]);
    $order_id = (int) $conn->lastInsertId();

    if (order_items_has_option_columns($conn)) {
        $sql_order_item = 'INSERT INTO order_items (order_id, product_id, quantity, price, size, sweetness, ice_level, item_note)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt_order_item = $conn->prepare($sql_order_item);
        $stmt_order_item->execute([$order_id, $product_id, $quantity, $price, $size, $sweetness, $ice_level, $item_note]);
    } else {
        $stmt_order_item = $conn->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        $stmt_order_item->execute([$order_id, $product_id, $quantity, $price]);
    }

    unset($_SESSION['order']);
    header('Location: ' . app_url('shop/cart.php'));
    exit;
}

$sizes = order_size_choices();
$sweetOpts = order_sweetness_choices();
$iceOpts = order_ice_choices();

$schemaOpts = order_items_has_option_columns($conn);

$pageTitle = 'Đặt hàng — Hi Coffee';
$pageDescription = 'Chọn size, độ ngọt, đá, ghi chú và số lượng — thêm vào giỏ hàng.';
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="main-content" class="site-main" tabindex="-1">
<div id="main" class="content-panel order-product-panel">
    <?php if (!empty($_SESSION['order'])): ?>
        <?php
        $o = $_SESSION['order'];
        $unit = (int) $o['price'];
        ?>
        <h1 class="page-title">Đặt: <?= htmlspecialchars($o['name']) ?></h1>
        <p class="page-lead text-center">
            <img src="<?= htmlspecialchars(app_url('img/' . $o['image'])) ?>" alt="" class="order-product-panel__img">
        </p>
        <?php if ($message): ?>
            <p class="<?= $messageIsError ? 'error' : 'message' ?> text-center"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(app_url('shop/order_products.php')) ?>" class="support-form order-product-form">
            <?php if (!$schemaOpts): ?>
                <p class="alert alert--danger" style="margin-bottom:1rem;font-size:0.9rem;">
                    CSDL chưa có cột tùy chọn món (size, độ ngọt, đá…). Chạy file <code>sql/migrate_order_options_reviews.sql</code> hoặc <code>sql/reset_fresh.sql</code> để bật đầy đủ.
                </p>
            <?php else: ?>
            <div class="form-group">
                <label for="size">Size</label>
                <select id="size" name="size" required>
                    <?php foreach ($sizes as $val => $lab): ?>
                        <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($lab) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="sweetness">Độ ngọt</label>
                <select id="sweetness" name="sweetness" required>
                    <?php foreach ($sweetOpts as $val => $lab): ?>
                        <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($lab) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ice_level">Đá</label>
                <select id="ice_level" name="ice_level" required>
                    <?php foreach ($iceOpts as $val => $lab): ?>
                        <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($lab) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="item_note">Ghi chú thêm (tùy chọn)</label>
                <textarea id="item_note" name="item_note" rows="2" maxlength="500" placeholder="Ví dụ: ít đường, lấy ống hút…"></textarea>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label>Số lượng</label>
                <div class="qty-row">
                    <button type="button" class="btn btn--sm" id="decrease-quantity" aria-label="Giảm">−</button>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="99" required>
                    <button type="button" class="btn btn--sm" id="increase-quantity" aria-label="Tăng">+</button>
                </div>
            </div>
            <p class="text-center" style="font-weight:700;">
                Tạm tính: <span id="total-price" data-price="<?= $unit ?>"><?= number_format($unit) ?> đ</span>
            </p>
            <button type="submit" class="btn btn--block">Xác nhận thêm vào giỏ</button>
        </form>
    <?php else: ?>
        <h1 class="page-title">Đặt hàng</h1>
        <p class="error text-center"><?= htmlspecialchars($message ?: 'Chọn sản phẩm từ thực đơn.') ?></p>
        <p class="text-center"><a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>" class="btn">Về thực đơn</a></p>
    <?php endif; ?>
</div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
