<?php
require_once dirname(__DIR__) . '/includes/init.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . (!empty($_SESSION['is_admin']) ? app_url('admin/admin_dashboard.php') : app_url('index.php')));
    exit;
}

$flash = null;
if (isset($_SESSION['message'])) {
    $flash = $_SESSION['message'];
    unset($_SESSION['message']);
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $sql = 'SELECT * FROM users WHERE username = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = (int) $user['is_admin'] === 1;

        if (!empty($_SESSION['is_admin'])) {
            header('Location: ' . app_url('admin/admin_dashboard.php'));
        } else {
            header('Location: ' . app_url('index.php'));
        }
        exit;
    }
    $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
}

$pageTitle = 'Đăng nhập — Hi Coffee';
$pageDescription = 'Đăng nhập tài khoản Hi Coffee để đặt hàng, xem giỏ và lịch sử đơn hàng.';
$guestNav = true;
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="main-content" class="site-main site-main--narrow" tabindex="-1">
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-card__head">
                <h1>Đăng nhập</h1>
            </div>
            <div class="auth-card__body">
                <?php if ($flash): ?>
                    <div class="alert alert--success"><?= htmlspecialchars($flash) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert--danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="<?= htmlspecialchars(app_url('auth/login.php')) ?>" autocomplete="on">
                    <div class="form-group">
                        <label for="username">Tên đăng nhập</label>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn--block">Đăng nhập</button>
                </form>
                <p class="auth-foot">Chưa có tài khoản? <a href="<?= htmlspecialchars(app_url('auth/signup.php')) ?>">Đăng ký</a></p>
            </div>
        </div>
    </div>
</div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
