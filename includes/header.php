<?php
$pageTitle = $pageTitle ?? 'Hi Coffee — Cà phê & đồ uống';
$pageDescription = $pageDescription ?? 'Hi Coffee: đặt cà phê, trà và đồ uống trực tuyến. Thực đơn đa dạng, đặt hàng nhanh, hỗ trợ khách hàng tận tâm.';
$guestNav = !empty($guestNav);

$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$siteScheme = $isHttps ? 'https' : 'http';
$siteHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$siteOrigin = $siteScheme . '://' . $siteHost;
$scriptPath = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$canonicalUrl = $siteOrigin . $scriptPath;
$ogImage = $siteOrigin . app_url('img/mainlogo.png');
$__self = basename($_SERVER['PHP_SELF'] ?? '');
$__adminScripts = [
    'admin.php', 'admin_dashboard.php', 'admin_products.php', 'admin_orders.php', 'admin_comments.php', 'edit_product.php',
];
$isAdminPage = in_array($__self, $__adminScripts, true);
$robotsContent = $isAdminPage ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

$headerLight = !$guestNav && empty($_SESSION['is_admin']);
$headerClass = 'site-header' . ($headerLight ? ' site-header--light' : ' site-header--dark');

$brandHref = $guestNav
    ? app_url('auth/login.php')
    : (!empty($_SESSION['is_admin']) ? app_url('admin/admin_dashboard.php') : app_url('index.php'));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="keywords" content="Hi Coffee, cà phê, trà, đặt hàng online, đồ uống, takeaway">
    <meta name="robots" content="<?= htmlspecialchars($robotsContent) ?>">
    <meta name="author" content="Hi Coffee">
    <meta name="theme-color" content="<?= $headerLight ? '#faf8f5' : '#1a120d' ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:site_name" content="Hi Coffee">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700&family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('css/style.css')) ?>">
</head>
<body>
<a href="#main-content" class="skip-link">Chuyển đến nội dung chính</a>
<div class="app-shell">
    <header class="<?= htmlspecialchars($headerClass) ?>" role="banner">
        <div class="site-header__inner">
            <a class="brand" href="<?= htmlspecialchars($brandHref) ?>">
                <span class="brand__mark" aria-hidden="true"></span>
                <img src="<?= htmlspecialchars(app_url('img/mainlogo.png')) ?>" alt="" class="brand__logo" width="44" height="44" role="presentation">
                <div class="brand__text">
                    <span class="brand__name">Hi Coffee</span>
                    <span class="brand__tagline"><?= !empty($_SESSION['is_admin']) ? 'Quản trị hệ thống' : ($guestNav ? 'Đăng nhập tài khoản' : 'Specialty &amp; signature drinks') ?></span>
                </div>
            </a>

            <?php if ($guestNav): ?>
                <button type="button" class="nav-toggle" id="nav-toggle" aria-controls="primary-nav" aria-expanded="false" aria-label="Mở menu">
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                </button>
                <nav id="primary-nav" class="primary-nav primary-nav--guest" aria-label="Tài khoản">
                    <ul class="nav-list">
                        <li><a href="<?= htmlspecialchars(app_url('auth/login.php')) ?>" class="nav-list__link<?= $__self === 'login.php' ? ' is-active' : '' ?>">Đăng nhập</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('auth/signup.php')) ?>" class="nav-list__link<?= $__self === 'signup.php' ? ' is-active' : '' ?>">Đăng ký</a></li>
                    </ul>
                </nav>
            <?php elseif (!empty($_SESSION['is_admin'])): ?>
                <?php if (!isset($adminNavCurrent)) { $adminNavCurrent = ''; } ?>
                <button type="button" class="nav-toggle" id="nav-toggle" aria-controls="primary-nav" aria-expanded="false" aria-label="Mở menu quản trị">
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                </button>
                <nav id="primary-nav" class="primary-nav primary-nav--admin" aria-label="Quản trị">
                    <ul class="nav-list">
                        <li><a href="<?= htmlspecialchars(app_url('admin/admin_dashboard.php')) ?>" class="nav-list__link<?= $adminNavCurrent === 'dashboard' ? ' is-active' : '' ?>">Dashboard</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('admin/admin_products.php')) ?>" class="nav-list__link<?= $adminNavCurrent === 'products' ? ' is-active' : '' ?>">Sản phẩm</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('admin/admin_orders.php')) ?>" class="nav-list__link<?= $adminNavCurrent === 'orders' ? ' is-active' : '' ?>">Đơn hàng</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('admin/admin_comments.php')) ?>" class="nav-list__link<?= $adminNavCurrent === 'comments' ? ' is-active' : '' ?>">Bình luận</a></li>
                        <li class="nav-list__item--cta"><a href="<?= htmlspecialchars(app_url('index.php')) ?>" class="nav-list__link nav-list__link--outline" target="_blank" rel="noopener noreferrer">Cửa hàng</a></li>
                    </ul>
                </nav>
                <div class="site-header__user">
                    <span class="user-pill">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                        <span class="user-badge">Admin</span>
                    </span>
                    <a href="<?= htmlspecialchars(app_url('auth/logout.php')) ?>" class="btn btn--header btn--sm">Đăng xuất</a>
                </div>
            <?php else: ?>
                <button type="button" class="nav-toggle" id="nav-toggle" aria-controls="primary-nav" aria-expanded="false" aria-label="Mở menu">
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                    <span class="nav-toggle__bar" aria-hidden="true"></span>
                </button>
                <nav id="primary-nav" class="primary-nav" aria-label="Điều hướng chính">
                    <ul class="nav-list">
                        <li><a href="<?= htmlspecialchars(app_url('index.php')) ?>" class="nav-list__link<?= $__self === 'index.php' ? ' is-active' : '' ?>">Trang chủ</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('shop/products.php')) ?>" class="nav-list__link<?= $__self === 'products.php' ? ' is-active' : '' ?>">Thực đơn</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('shop/cart.php')) ?>" class="nav-list__link<?= $__self === 'cart.php' ? ' is-active' : '' ?>">Giỏ hàng</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('shop/order.php')) ?>" class="nav-list__link<?= $__self === 'order.php' ? ' is-active' : '' ?>">Đơn hàng</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('support.php')) ?>" class="nav-list__link<?= $__self === 'support.php' ? ' is-active' : '' ?>">Hỗ trợ</a></li>
                        <li><a href="<?= htmlspecialchars(app_url('about.php')) ?>" class="nav-list__link<?= $__self === 'about.php' ? ' is-active' : '' ?>">Giới thiệu</a></li>
                    </ul>
                </nav>
                <div class="site-header__user">
                    <span class="user-pill">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                    </span>
                    <a href="<?= htmlspecialchars(app_url('auth/logout.php')) ?>" class="btn btn--header btn--sm">Đăng xuất</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <div class="nav-backdrop" id="nav-backdrop" aria-hidden="true"></div>
