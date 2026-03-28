<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$adminProductsUrl = app_url('admin/admin_products.php');

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}

$adminNavCurrent = 'products';
$PRODUCT_CATEGORIES = require dirname(__DIR__) . '/includes/product_constants.php';
$imgDir = dirname(__DIR__) . '/img/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name'] ?? '');
    $price = (int) ($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'Khác');
    if (!in_array($category, $PRODUCT_CATEGORIES, true)) {
        $category = 'Khác';
    }
    $is_featured = !empty($_POST['is_featured']) ? 1 : 0;
    $image = $_FILES['image'] ?? null;

    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($image['name']);
        move_uploaded_file($image['tmp_name'], $imgDir . $image_name);
    } else {
        $image_name = 'default_image.jpg';
    }

    $sql = 'INSERT INTO products (name, price, description, category, image, is_featured) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $price, $description, $category, $image_name, $is_featured]);

    $_SESSION['message'] = 'Đã thêm sản phẩm.';
    $_SESSION['message_type'] = 'success';
    header('Location: ' . $adminProductsUrl);
    exit;
}

$products = $conn->query('SELECT * FROM products ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

$flash = null;
$flashType = null;
if (isset($_SESSION['message'])) {
    $flash = $_SESSION['message'];
    $flashType = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message'], $_SESSION['message_type']);
}

$pageTitle = 'Quản lý sản phẩm — Hi Coffee';
$pageDescription = 'Thêm và chỉnh sửa sản phẩm Hi Coffee.';
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="main-content" class="site-main site-main--admin" tabindex="-1">
<div id="main" class="content-panel" style="max-width:1100px;">
    <h1 class="page-title">Quản lý sản phẩm</h1>

    <?php if ($flash): ?>
        <div class="alert <?= ($flashType === 'danger') ? 'alert--danger' : 'alert--success' ?>"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <h2 class="section-title">Thêm sản phẩm</h2>
    <form method="post" action="<?= htmlspecialchars($adminProductsUrl) ?>" enctype="multipart/form-data" style="max-width:520px;margin-bottom:2rem;">
        <input type="hidden" name="add_product" value="1">
        <div class="form-group">
            <label for="name">Tên</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="price">Giá (đ)</label>
            <input type="number" id="price" name="price" min="0" required>
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="category">Danh mục</label>
            <select id="category" name="category" required>
                <?php foreach ($PRODUCT_CATEGORIES as $c): ?>
                    <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Ảnh</label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:0.5rem;font-weight:400;">
                <input type="checkbox" name="is_featured" value="1"> Đánh dấu nổi bật
            </label>
        </div>
        <button type="submit" class="btn">Thêm sản phẩm</button>
    </form>

    <h2 class="section-title">Danh sách sản phẩm</h2>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Nổi bật</th>
                    <th>Ảnh</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= (int) $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category'] ?? '—') ?></td>
                        <td><?= number_format((int) $product['price']) ?> đ</td>
                        <td><?= !empty($product['is_featured']) ? 'Có' : '' ?></td>
                        <td><img src="<?= htmlspecialchars(app_url('img/' . $product['image'])) ?>" alt="" width="56" height="56" style="object-fit:cover;border-radius:6px;"></td>
                        <td>
                            <a href="<?= htmlspecialchars(app_url('admin/edit_product.php?id=' . (int) $product['id'])) ?>" class="btn btn--sm">Sửa</a>
                            <a href="<?= htmlspecialchars(app_url('admin/delete_product.php?id=' . (int) $product['id'])) ?>" class="btn btn--sm" style="background:#c62828;" onclick="return confirm('Xóa sản phẩm?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
