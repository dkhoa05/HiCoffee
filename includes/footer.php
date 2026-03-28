<footer class="site-footer site-footer--cafe" role="contentinfo">
        <div class="site-footer__top">
            <div class="site-footer__grid site-footer__grid--wide">
                
                <div class="site-footer__brand">
                    <strong class="site-footer__title">Hi Coffee</strong>
                    <p class="site-footer__desc">
                        Hi Coffee là điểm đến dành cho những ai yêu thích cà phê, trà trái cây và các loại đồ uống pha chế hiện đại.
                        Chúng tôi chú trọng chất lượng nguyên liệu, hương vị ổn định và trải nghiệm đặt hàng trực tuyến nhanh chóng, tiện lợi.
                    </p>
                    <p class="site-footer__hours">
                        <strong>Giờ mở cửa</strong><br>
                        Thứ 2 - Chủ nhật: 7:00 - 22:00
                    </p>
                </div>

                <nav class="site-footer__nav" aria-label="Menu nhanh">
                    <strong class="site-footer__heading">Khám phá</strong>
                    <ul class="site-footer__links">
                        <li><a href="<?= htmlspecialchars(app_url('index.php')) ?>">Trang chủ</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>">Thực đơn</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('about.php')) ?>">Về chúng tôi</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('support.php')) ?>">Liên hệ &amp; hỗ trợ</a></li>
                    </ul>
                </nav>

                <nav class="site-footer__nav" aria-label="Tài khoản khách hàng">
                    <strong class="site-footer__heading">Khách hàng</strong>
                    <ul class="site-footer__links">
                        <li><a href="<?= htmlspecialchars(app_url('shop/cart.php')) ?>">Giỏ hàng</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('shop/order.php')) ?>">Lịch sử đơn hàng</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('auth/login.php')) ?>">Đăng nhập</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('auth/signup.php')) ?>">Đăng ký tài khoản</a></li>
                    </ul>
                </nav>

                <div class="site-footer__contact">
                    <strong class="site-footer__heading">Liên hệ</strong>
                    <p class="site-footer__address">
                        <strong>Địa chỉ:</strong>
                        <span>69/68 Đặng Thùy Trâm, Bình Lợi Trung, TP Hồ Chí Minh</span>
                    </p>
                    <p>
                        <strong>Hotline:</strong>
                        <a href="tel:0909203873">0909 203 873</a>
                        <span class="site-footer__sep">·</span>
                        <a href="https://zalo.me/0909203873" rel="noopener noreferrer" target="_blank">Zalo</a>
                    </p>
                    <p class="site-footer__social">
                        <a href="https://www.instagram.com/" rel="noopener noreferrer" target="_blank">Instagram</a>
                        <span class="site-footer__sep">·</span>
                        <a href="https://www.facebook.com/" rel="noopener noreferrer" target="_blank">Facebook</a>
                    </p>
                </div>

            </div>
        </div>

        <div class="site-footer__bottom">
            <p class="site-footer__copy">
                &copy; <?= date('Y') ?> <strong>Hi Coffee</strong>. Website giới thiệu và đặt đồ uống trực tuyến với thực đơn đa dạng, thông tin rõ ràng và trải nghiệm tiện lợi.
            </p>
        </div>
    </footer>
</div>

<?php
$schemaOrg = [
    '@context' => 'https://schema.org',
    '@type' => 'CafeOrCoffeeShop',
    'name' => 'Hi Coffee',
    'description' => 'Hi Coffee chuyên cà phê, trà trái cây và đồ uống pha chế chất lượng, hỗ trợ đặt hàng trực tuyến nhanh chóng và tiện lợi.',
    'url' => $siteOrigin . app_url(''),
    'image' => $siteOrigin . app_url('img/mainlogo.png'),
    'telephone' => '+84-909-203-873',
    'sameAs' => [
        'https://www.instagram.com/',
        'https://www.facebook.com/',
        'https://zalo.me/0909203873'
    ],
    'openingHoursSpecification' => [
        [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
            ],
            'opens' => '07:00',
            'closes' => '22:00',
        ],
    ],
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => '69/68 Đặng Thùy Trâm, Bình Lợi Trung',
        'addressLocality' => 'Thành phố Hồ Chí Minh',
        'addressCountry' => 'VN',
    ],
];
?>
<script type="application/ld+json"><?= json_encode($schemaOrg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<script src="<?= htmlspecialchars(app_url('js/main.js')) ?>" defer></script>
</body>
</html>