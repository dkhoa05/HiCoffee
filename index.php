<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Hi Coffee | Cà phê nguyên chất, trà trái cây, đồ uống giao nhanh';
$pageDescription = 'Hi Coffee chuyên cà phê, trà trái cây và đồ uống pha chế chất lượng. Đặt món online nhanh chóng, giá rõ ràng, giao hàng tiện lợi tại TP. Hồ Chí Minh.';

try {
    $stmt = $conn->query('SELECT * FROM products WHERE is_featured = 1 ORDER BY id ASC LIMIT 4');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error fetching featured products: ' . $e->getMessage());
    $products = [];
}

require __DIR__ . '/includes/header.php';
?>

<main id="main-content" class="site-main page-home" tabindex="-1">

    <section class="home-hero" aria-labelledby="home-hero-title">
        <div class="home-hero__inner">
            <p class="home-hero__eyebrow">Thưởng thức đồ uống chất lượng mỗi ngày</p>
            <h1 id="home-hero-title" class="home-hero__title">Hi Coffee - Cà phê ngon, giao nhanh, trải nghiệm trọn vẹn</h1>
            <p class="home-hero__text">
                Từ cà phê đậm vị, latte thơm béo đến trà trái cây tươi mát, Hi Coffee mang đến thực đơn đa dạng,
                dễ chọn, dễ đặt và phù hợp cho mọi khoảnh khắc trong ngày.
            </p>
            <div class="home-hero__actions">
                <a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>" class="btn btn--lg">Xem thực đơn</a>
                <a href="<?= htmlspecialchars(app_url('about.php')) ?>" class="btn btn--secondary btn--lg">Khám phá Hi Coffee</a>
            </div>
        </div>
    </section>

    <div class="home-container">

        <section class="home-usp" aria-label="Điểm nổi bật của Hi Coffee">
            <ul class="home-usp__grid">
                <li class="home-usp__item">
                    <span class="home-usp__icon" aria-hidden="true">☕</span>
                    <h2 class="home-usp__heading">Cà phê chuẩn vị</h2>
                    <p>
                        Chọn lọc hạt cà phê chất lượng, pha chế chỉn chu để mang đến hương vị cân bằng, dễ uống và ổn định trong từng ly.
                    </p>
                </li>
                <li class="home-usp__item">
                    <span class="home-usp__icon" aria-hidden="true">🌿</span>
                    <h2 class="home-usp__heading">Nguyên liệu rõ ràng</h2>
                    <p>
                        Trà, sữa và trái cây được lựa chọn kỹ lưỡng, đảm bảo độ tươi ngon và minh bạch trong từng món đồ uống.
                    </p>
                </li>
                <li class="home-usp__item">
                    <span class="home-usp__icon" aria-hidden="true">📦</span>
                    <h2 class="home-usp__heading">Đặt món tiện lợi</h2>
                    <p>
                        Giao diện dễ sử dụng, thao tác nhanh chóng, giúp bạn đặt hàng online và theo dõi đơn hàng thuận tiện hơn.
                    </p>
                </li>
            </ul>
        </section>

        <section class="home-section home-featured" aria-labelledby="featured-heading">
            <div class="home-section__head">
                <h2 id="featured-heading" class="home-section__title">Sản phẩm nổi bật</h2>
                <p class="home-section__lead">
                    Những món được nhiều khách hàng yêu thích và lựa chọn thường xuyên tại Hi Coffee.
                </p>
                <a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>" class="home-section__link">Xem toàn bộ thực đơn →</a>
            </div>

            <div class="products products--home">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <article class="product-card">
                            <img
                                class="product_thumb"
                                src="<?= htmlspecialchars(app_url('img/' . $product['image'])) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                            >
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p><?= htmlspecialchars($product['description']) ?></p>
                            <p class="price"><?= number_format((int)$product['price']) ?> đ</p>
                            <a href="<?= htmlspecialchars(app_url('shop/order_products.php?product_id=' . (int) $product['id'])) ?>" class="btn">Đặt ngay</a>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="home-empty">
                        Sản phẩm nổi bật đang được cập nhật. Bạn có thể xem thêm trong
                        <a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>">thực đơn đầy đủ</a>.
                    </p>
                <?php endif; ?>
            </div>
        </section>

        <section class="home-process" aria-labelledby="steps-heading">
            <div class="home-process__head">
                <p class="home-process__eyebrow">Quy trình đặt hàng</p>
                <h2 id="steps-heading" class="home-process__title">Đặt đồ uống nhanh chóng chỉ với 4 bước</h2>
                <p class="home-process__lead">
                    Tối ưu thao tác đặt hàng để bạn dễ dàng chọn món, xác nhận đơn và theo dõi trạng thái mọi lúc.
                </p>
            </div>

            <ol class="home-process__grid">
                <li class="home-process__step">
                    <span class="home-process__num" aria-hidden="true">1</span>
                    <h3 class="home-process__step-title">Chọn món yêu thích</h3>
                    <p>
                        Duyệt thực đơn, xem mô tả, giá bán và lựa chọn món phù hợp theo sở thích của bạn.
                    </p>
                </li>
                <li class="home-process__step">
                    <span class="home-process__num" aria-hidden="true">2</span>
                    <h3 class="home-process__step-title">Thêm vào giỏ hàng</h3>
                    <p>
                        Chọn số lượng mong muốn và lưu sản phẩm vào giỏ hàng trước khi tiến hành đặt mua.
                    </p>
                </li>
                <li class="home-process__step">
                    <span class="home-process__num" aria-hidden="true">3</span>
                    <h3 class="home-process__step-title">Xác nhận và theo dõi đơn</h3>
                    <p>
                        Kiểm tra thông tin đơn hàng, xác nhận đặt món và theo dõi trạng thái xử lý trong tài khoản.
                    </p>
                </li>
                <li class="home-process__step">
                    <span class="home-process__num" aria-hidden="true">4</span>
                    <h3 class="home-process__step-title">Nhận đồ uống và trải nghiệm</h3>
                    <p>
                        Thưởng thức đồ uống yêu thích và gửi phản hồi để Hi Coffee ngày càng phục vụ tốt hơn.
                    </p>
                </li>
            </ol>
        </section>

        <section class="home-split" aria-labelledby="story-heading">
            <div class="home-split__visual" aria-hidden="true">
                <div class="home-split__visual-inner"></div>
            </div>

            <div class="home-split__content">
                <p class="home-split__eyebrow">Câu chuyện thương hiệu</p>
                <h2 id="story-heading" class="home-split__title">Hi Coffee - Không gian hiện đại, hương vị chỉn chu</h2>
                <p class="home-split__text">
                    Hi Coffee hướng đến trải nghiệm thưởng thức đồ uống hiện đại, tiện lợi và nhất quán.
                    Chúng tôi chú trọng từ chất lượng nguyên liệu, cách pha chế đến dịch vụ đặt hàng trực tuyến,
                    để mỗi ly đồ uống khi đến tay khách hàng đều giữ được sự hài hòa về hương vị và cảm xúc.
                </p>
                <div class="home-split__actions">
                    <a href="<?= htmlspecialchars(app_url('about.php')) ?>" class="btn">Xem thêm về chúng tôi</a>
                    <a href="<?= htmlspecialchars(app_url('support.php')) ?>" class="btn btn--ghost-dark">Liên hệ và góp ý</a>
                </div>
            </div>
        </section>

        <section class="home-cta home-cta--panel" aria-label="Mời khách xem thực đơn">
            <div class="home-cta__bg" aria-hidden="true"></div>
            <div class="home-cta__inner">
                <h2 class="home-cta__title">Sẵn sàng chọn ly đồ uống tiếp theo?</h2>
                <p class="home-cta__sub">
                    Khám phá thực đơn mới nhất của Hi Coffee và đặt món nhanh chóng ngay hôm nay.
                </p>
                <div class="home-cta__actions">
                    <a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>" class="btn btn--lg home-cta__btn-primary">Xem thực đơn</a>
                    <a href="<?= htmlspecialchars(app_url('shop/cart.php')) ?>" class="btn btn--lg home-cta__btn-secondary">Xem giỏ hàng</a>
                </div>
            </div>
        </section>

    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>