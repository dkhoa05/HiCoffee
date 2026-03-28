<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/schema_helper.php';

$pageTitle = 'Thực đơn | Hi Coffee';
$pageDescription = 'Khám phá thực đơn Hi Coffee với cà phê, trà trái cây, đá xay và nước ép. Tìm kiếm món yêu thích, lọc danh mục và đặt hàng nhanh chóng.';

$PRODUCT_CATEGORIES = require dirname(__DIR__) . '/includes/product_constants.php';
$hasCategory = products_has_category_column($conn);

$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$sort = $_GET['sort'] ?? 'featured';
$onlyFeatured = isset($_GET['featured']) && $_GET['featured'] === '1';

$where = ['1=1'];
$params = [];

if ($q !== '') {
    $where[] = '(name LIKE ? OR description LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}

if ($hasCategory && $category !== '' && in_array($category, $PRODUCT_CATEGORIES, true)) {
    $where[] = 'category = ?';
    $params[] = $category;
}

if ($onlyFeatured) {
    $where[] = 'is_featured = 1';
}

$orderSql = 'is_featured DESC, name ASC';
switch ($sort) {
    case 'price_asc':
        $orderSql = 'price ASC, name ASC';
        break;
    case 'price_desc':
        $orderSql = 'price DESC, name ASC';
        break;
    case 'name':
        $orderSql = 'name ASC';
        break;
    case 'newest':
        $orderSql = 'id DESC';
        break;
    default:
        $sort = 'featured';
        break;
}

$sql = 'SELECT * FROM products WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $orderSql;
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$productList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$avgByProduct = [];
$publicReviews = [];
try {
    foreach ($conn->query('SELECT product_id, AVG(stars) AS avg_s, COUNT(*) AS n FROM product_reviews GROUP BY product_id')->fetchAll(PDO::FETCH_ASSOC) as $ar) {
        $avgByProduct[(int) $ar['product_id']] = [
            'avg' => round((float) $ar['avg_s'], 1),
            'n' => (int) $ar['n'],
        ];
    }
    $publicReviews = $conn->query(
        'SELECT pr.display_name, pr.content, pr.stars, pr.created_at, p.name AS product_name
         FROM product_reviews pr
         INNER JOIN products p ON p.id = pr.product_id
         ORDER BY pr.created_at DESC
         LIMIT 30'
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Chưa có bảng product_reviews hoặc hệ thống đánh giá chưa được cấu hình.
}

require dirname(__DIR__) . '/includes/header.php';
?>

<main id="main-content" class="site-main page-shop" tabindex="-1">
    <header class="shop-page-header">
        <h1 class="page-title">Thực đơn Hi Coffee</h1>
        <p class="page-lead">
            Khám phá các dòng đồ uống được yêu thích tại Hi Coffee, từ cà phê đậm vị, trà trái cây thanh mát
            đến đá xay và nước ép tươi. Tìm kiếm, lọc món và đặt hàng nhanh chóng chỉ trong vài bước.
        </p>
    </header>

    <form class="shop-page-form" method="get" action="<?= htmlspecialchars(app_url('shop/products.php')) ?>" id="shop-page-form">
        <div class="shop-toolbar-wrap">
            <div class="shop-toolbar">
                <?php if ($hasCategory): ?>
                    <div class="shop-toolbar__categories">
                        <span class="shop-toolbar__label" id="shop-cat-label">Danh mục sản phẩm</span>
                        <div class="shop-category-chips" role="group" aria-labelledby="shop-cat-label">
                            <label class="shop-chip">
                                <input type="radio" name="category" value="" <?= $category === '' ? 'checked' : '' ?> class="shop-chip__input">
                                <span class="shop-chip__text">Tất cả</span>
                            </label>
                            <?php foreach ($PRODUCT_CATEGORIES as $c): ?>
                                <label class="shop-chip">
                                    <input type="radio" name="category" value="<?= htmlspecialchars($c) ?>" <?= $category === $c ? 'checked' : '' ?> class="shop-chip__input">
                                    <span class="shop-chip__text"><?= htmlspecialchars($c) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="shop-toolbar__search">
                    <label for="shop-q" class="visually-hidden">Tìm kiếm sản phẩm</label>
                    <div class="shop-search-field">
                        <span class="shop-search-field__icon" aria-hidden="true">⌕</span>
                        <input
                            type="search"
                            id="shop-q"
                            name="q"
                            value="<?= htmlspecialchars($q) ?>"
                            placeholder="Tìm theo tên món hoặc mô tả..."
                            autocomplete="off"
                            class="shop-search-field__input"
                        >
                        <button type="submit" class="btn btn--sm shop-search-field__btn">Tìm kiếm</button>
                    </div>
                </div>
            </div>

            <?php if (!$hasCategory): ?>
                <p class="shop-toolbar__note">
                    Danh mục sản phẩm hiện chưa được kích hoạt trong hệ thống.
                </p>
            <?php endif; ?>
        </div>

        <div class="shop-layout">
            <aside class="shop-filters" aria-label="Bộ lọc sản phẩm">
                <div class="shop-filters__form">
                    <h2 class="shop-filters__title">Bộ lọc</h2>

                    <div class="form-group">
                        <label for="shop-sort">Sắp xếp theo</label>
                        <select id="shop-sort" name="sort">
                            <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Ưu tiên món nổi bật</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Tên từ A đến Z</option>
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Món mới thêm</option>
                        </select>
                    </div>

                    <div class="form-group shop-filters__check">
                        <label>
                            <input type="checkbox" name="featured" value="1" <?= $onlyFeatured ? 'checked' : '' ?>>
                            Chỉ hiển thị món nổi bật
                        </label>
                    </div>

                    <div class="shop-filters__actions">
                        <button type="submit" class="btn btn--block">Áp dụng bộ lọc</button>
                        <a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>" class="shop-filters__reset">Xóa bộ lọc</a>
                    </div>
                </div>
            </aside>

            <div class="shop-results content-panel">
                <p class="shop-results__meta">
                    <span class="shop-results__count"><?= count($productList) ?> món</span>
                    <?php if ($q !== '' || $category !== '' || $onlyFeatured): ?>
                        <span class="shop-results__hint">phù hợp với bộ lọc hiện tại</span>
                    <?php endif; ?>
                </p>

                <?php if (empty($productList)): ?>
                    <p class="shop-empty">
                        Không tìm thấy sản phẩm phù hợp. Bạn có thể thử đổi từ khóa tìm kiếm, thay đổi danh mục
                        hoặc tắt bộ lọc món nổi bật để xem thêm kết quả.
                    </p>
                <?php else: ?>
                    <div class="products products--shop">
                        <?php foreach ($productList as $product): ?>
                            <article class="product-card">
                                <img
                                    src="<?= htmlspecialchars(app_url('img/' . $product['image'])) ?>"
                                    alt="<?= htmlspecialchars($product['name']) ?>"
                                >

                                <?php if ($hasCategory && !empty($product['category'])): ?>
                                    <span class="product-card__badge"><?= htmlspecialchars($product['category']) ?></span>
                                <?php endif; ?>

                                <h3><?= htmlspecialchars($product['name']) ?></h3>

                                <?php
                                $pid = (int) $product['id'];
                                if (!empty($avgByProduct[$pid])):
                                    $ar = $avgByProduct[$pid];
                                ?>
                                    <p class="product-card__rating" title="<?= (int) $ar['n'] ?> đánh giá">
                                        <span class="product-card__stars" aria-hidden="true">
                                            <?= str_repeat('★', (int) round($ar['avg'])) ?><?= str_repeat('☆', 5 - (int) round($ar['avg'])) ?>
                                        </span>
                                        <span class="product-card__rating-num">
                                            <?= htmlspecialchars((string) $ar['avg']) ?>/5 · <?= (int) $ar['n'] ?> lượt đánh giá
                                        </span>
                                    </p>
                                <?php endif; ?>

                                <p><?= htmlspecialchars($product['description']) ?></p>
                                <p class="price"><?= number_format((int) $product['price']) ?> đ</p>
                                <a href="<?= htmlspecialchars(app_url('shop/cart.php?product_id=' . (int) $product['id'])) ?>" class="btn">Thêm vào giỏ và đặt món</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <section class="shop-reviews" id="danh-gia" aria-labelledby="shop-reviews-title">
        <div class="shop-reviews__inner content-panel">
            <h2 id="shop-reviews-title" class="section-title">Đánh giá từ khách hàng</h2>
            <p class="shop-reviews__lead">
                Những nhận xét dưới đây được gửi bởi khách hàng sau khi hoàn tất đơn hàng và xác nhận đã nhận sản phẩm.
                Đây là nguồn tham khảo hữu ích để bạn lựa chọn món phù hợp với sở thích của mình.
            </p>

            <?php if (!empty($publicReviews)): ?>
                <ul class="shop-reviews__list">
                    <?php foreach ($publicReviews as $rv): ?>
                        <li class="shop-review-card">
                            <div class="shop-review-card__head">
                                <strong><?= htmlspecialchars($rv['display_name']) ?></strong>
                                <span class="shop-review-card__stars" aria-label="<?= (int) $rv['stars'] ?> sao">
                                    <?= str_repeat('★', (int) $rv['stars']) ?><?= str_repeat('☆', 5 - (int) $rv['stars']) ?>
                                </span>
                                <span class="shop-review-card__product"><?= htmlspecialchars($rv['product_name']) ?></span>
                            </div>
                            <p class="shop-review-card__body"><?= nl2br(htmlspecialchars($rv['content'])) ?></p>
                            <time class="shop-review-card__time"><?= htmlspecialchars($rv['created_at']) ?></time>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="shop-reviews__empty">
                    Hiện chưa có đánh giá sản phẩm nào được hiển thị. Sau khi đặt món và hoàn tất đơn hàng,
                    bạn có thể gửi đánh giá từ trang Đơn hàng của tôi.
                </p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>