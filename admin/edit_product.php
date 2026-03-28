<?php
require_once dirname(__DIR__) . '/includes/init.php';

$adminProductsUrl = app_url('admin/admin_products.php');
$loginUrl = app_url('auth/login.php');
$imgDir = dirname(__DIR__) . '/img/';

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . $loginUrl);
    exit;
}

$adminNavCurrent = 'products';
$PRODUCT_CATEGORIES = require dirname(__DIR__) . '/includes/product_constants.php';

$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($product_id < 1) {
    header('Location: ' . $adminProductsUrl);
    exit;
}

$stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: ' . $adminProductsUrl);
    exit;
}

$error_message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (int) ($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? 'Khác');
    if (!in_array($category, $PRODUCT_CATEGORIES, true)) {
        $category = 'Khác';
    }
    $is_featured = !empty($_POST['is_featured']) ? 1 : 0;
    $image = $_FILES['image'] ?? null;
    $image_name = $product['image'];

    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($image['type'], $allowed, true)) {
            $image_name = basename($image['name']);
            move_uploaded_file($image['tmp_name'], $imgDir . $image_name);
        } else {
            $error_message = 'Chỉ chấp nhận JPG, PNG, GIF, WebP.';
        }
    }

    if ($error_message === null && $name !== '' && $price >= 0 && $description !== '') {
        $sql = 'UPDATE products SET name = ?, price = ?, description = ?, category = ?, image = ?, is_featured = ? WHERE id = ?';
        $conn->prepare($sql)->execute([$name, $price, $description, $category, $image_name, $is_featured, $product_id]);
        $_SESSION['message'] = 'Đã cập nhật sản phẩm.';
        $_SESSION['message_type'] = 'success';
        header('Location: ' . $adminProductsUrl);
        exit;
    }
    if ($error_message === null) {
        $error_message = 'Vui lòng điền đủ thông tin hợp lệ.';
    }
}

$pageTitle = 'Sửa sản phẩm — Hi Coffee';
$pageDescription = 'Chỉnh sửa thông tin sản phẩm trong hệ thống quản trị Hi Coffee.';
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="main-content" class="site-main site-main--admin" tabindex="-1">
<div id="main" class="content-panel" style="max-width:560px;">
    <h1 class="page-title">Sửa sản phẩm</h1>
    <?php if ($error_message): ?>
        <div class="alert alert--danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= htmlspecialchars(app_url('admin/edit_product.php?id=' . $product_id)) ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Tên</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Giá (đ)</label>
            <input type="number" id="price" name="price" min="0" value="<?= (int) $product['price'] ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="category">Danh mục</label>
            <select id="category" name="category" required>
                <?php
                $curCat = $product['category'] ?? 'Khác';
                foreach ($PRODUCT_CATEGORIES as $c):
                ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $curCat === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Ảnh mới (để trống nếu giữ ảnh cũ)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:0.5rem;font-weight:400;">
                <input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>> Nổi bật
            </label>
        </div>
        <?php if (!empty($product['image'])): ?>
            <p class="text-center"><img src="<?= htmlspecialchars(app_url('img/' . $product['image'])) ?>" alt="" style="max-width:120px;border-radius:8px;"></p>
        <?php endif; ?>
        <button type="submit" class="btn btn--block">Lưu</button>
    </form>
    <p class="text-center mt-1"><a href="<?= htmlspecialchars($adminProductsUrl) ?>">← Về quản lý sản phẩm</a></p>
</div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
