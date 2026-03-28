<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Về Hi Coffee — Câu chuyện & giá trị';
$pageDescription = 'Hi Coffee: hướng đi pha chế, nguyên liệu và trải nghiệm khách hàng — quán cà phê phong cách hiện đại.';
require __DIR__ . '/includes/header.php';
?>
<main id="main-content" class="site-main page-about" tabindex="-1">
    <header class="page-intro">
        <h1 class="page-title">Về Hi Coffee</h1>
        <p class="page-lead page-lead--wide">
            Chúng tôi tin một ly cà phê ngon không chỉ đến từ hạt rang, mà còn từ sự nhất quán:
            công thức rõ ràng, phục vụ chu đáo và không gian để khách thư giãn.
        </p>
    </header>

    <div class="about-grid content-panel">
        <section class="about-block">
            <h2 class="section-title">Hương vị &amp; pha chế</h2>
            <p>
                Dòng cà phê Việt (phin, sữa, bạc xỉu) được giữ độ đậm cân bằng; espresso và latte dùng blend ổn định,
                steam sữa mịn. Trà và đá xay được phối ngọt vừa — có thể ghi chú &quot;ít đường&quot; khi đặt hàng trực tuyến.
            </p>
        </section>
        <section class="about-block">
            <h2 class="section-title">Nguyên liệu</h2>
            <p>
                Trái cây ép và topping chọn nhà cung cấp có kiểm soát; trà dùng lá chất lượng tốt cho từng mức giá.
                Thực đơn minh bạch: mỗi món có mô tả ngắn để bạn dễ hình dung trước khi chọn.
            </p>
        </section>
        <section class="about-block about-block--full">
            <h2 class="section-title">Đặt hàng online</h2>
            <p>
                Website Hi Coffee hỗ trợ đăng ký tài khoản, xem thực đơn có lọc danh mục, thêm giỏ và theo dõi đơn.
                Đây là phiên bản phục vụ học tập (Web PHP) — luồng nghiệp vụ được mô phỏng gần với quán thật để bạn thực hành đầy đủ.
            </p>
            <p class="about-cta">
                <a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>" class="btn">Xem thực đơn</a>
                <a href="<?= htmlspecialchars(app_url('support.php')) ?>" class="btn btn--secondary">Gửi góp ý</a>
            </p>
        </section>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
