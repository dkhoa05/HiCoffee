<?php
require_once dirname(__DIR__) . '/includes/init.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . app_url('index.php'));
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if ($username === '' || $password === '' || $confirm_password === '') {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        $stmt_check = $conn->prepare('SELECT id FROM users WHERE username = ?');
        $stmt_check->execute([$username]);
        if ($stmt_check->fetch()) {
            $error = 'Tên đăng nhập đã tồn tại.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt_insert->execute([$username, $hash]);
            $_SESSION['message'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            header('Location: ' . app_url('auth/login.php'));
            exit;
        }
    }
}

$pageTitle = 'Đăng ký — Hi Coffee';
$pageDescription = 'Tạo tài khoản Hi Coffee để đặt đồ uống và nhận hỗ trợ nhanh hơn.';
$guestNav = true;
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="main-content" class="site-main site-main--narrow" tabindex="-1">
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-card__head">
                <h1>Tạo tài khoản</h1>
            </div>
            <div class="auth-card__body">
                <?php if ($error): ?>
                    <div class="alert alert--danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="<?= htmlspecialchars(app_url('auth/signup.php')) ?>" autocomplete="on">
                    <div class="form-group">
                        <label for="username">Tên đăng nhập</label>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn--block">Đăng ký</button>
                </form>
                <p class="auth-foot">Đã có tài khoản? <a href="<?= htmlspecialchars(app_url('auth/login.php')) ?>">Đăng nhập</a></p>
            </div>
        </div>
    </div>
</div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
