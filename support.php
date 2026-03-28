<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/schema_helper.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($username === '' || $comment === '') {
        $message = 'Vui lòng nhập đầy đủ tên hiển thị và nội dung phản hồi.';
    } elseif (comments_has_contact_columns($conn) && $phone === '' && $email === '') {
        $message = 'Vui lòng cung cấp ít nhất số điện thoại hoặc email để chúng tôi có thể phản hồi khi cần.';
    } else {
        $ok = false;
        if (comments_has_contact_columns($conn)) {
            $phone = $phone === '' ? null : mb_substr($phone, 0, 30);
            $email = $email === '' ? null : mb_substr($email, 0, 255);
            $stmt = $conn->prepare('INSERT INTO comments (username, phone, email, comment) VALUES (?, ?, ?, ?)');
            $ok = $stmt->execute([$username, $phone, $email, $comment]);
        } else {
            $stmt = $conn->prepare('INSERT INTO comments (username, comment) VALUES (?, ?)');
            $ok = $stmt->execute([$username, $comment]);
        }

        if ($ok) {
            $message = comments_has_contact_columns($conn)
                ? 'Cảm ơn bạn đã gửi phản hồi. Hi Coffee đã ghi nhận thông tin và sẽ liên hệ lại qua số điện thoại hoặc email bạn cung cấp khi cần.'
                : 'Cảm ơn bạn đã gửi phản hồi. Nội dung của bạn đã được ghi nhận thành công.';
        } else {
            $message = 'Hệ thống chưa thể ghi nhận phản hồi vào lúc này. Vui lòng thử lại sau.';
        }
    }
}

$comments = $conn->query('SELECT * FROM comments ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Hỗ trợ khách hàng | Hi Coffee';
$pageDescription = 'Liên hệ Hi Coffee để gửi góp ý, phản hồi hoặc yêu cầu hỗ trợ. Khách hàng có thể để lại thông tin liên hệ để được phản hồi nhanh chóng.';
require __DIR__ . '/includes/header.php';
?>

<main id="main-content" class="site-main page-support" tabindex="-1">
    <div id="main" class="content-panel">
        <h1 class="page-title">Góp ý và hỗ trợ khách hàng</h1>
        <p class="page-lead page-lead--wide">
            Hi Coffee luôn trân trọng mọi ý kiến đóng góp từ khách hàng. Nếu bạn cần hỗ trợ, muốn phản hồi về dịch vụ
            hoặc chia sẻ trải nghiệm của mình, vui lòng gửi thông tin qua biểu mẫu bên dưới. Chúng tôi sẽ tiếp nhận và xem xét trong thời gian sớm nhất.
        </p>

        <form method="post" action="support.php" class="support-form">
            <div class="form-group">
                <label for="username">Tên hiển thị</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    placeholder="Nhập tên của bạn"
                    value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    maxlength="30"
                    placeholder="Nhập số điện thoại để chúng tôi liên hệ"
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="255"
                    placeholder="Nhập email để nhận phản hồi"
                >
            </div>

            <div class="form-group">
                <label for="comment">Nội dung phản hồi</label>
                <textarea
                    id="comment"
                    name="comment"
                    rows="4"
                    required
                    placeholder="Vui lòng nhập góp ý, phản hồi hoặc yêu cầu hỗ trợ của bạn"
                ></textarea>
            </div>

            <button type="submit" class="btn btn--block">Gửi phản hồi</button>
        </form>

        <?php if ($message): ?>
            <p class="message text-center"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <h2 class="section-title">Phản hồi từ khách hàng</h2>
        <div class="comment-list">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $c): ?>
                    <article class="comment-item">
                        <div class="comment-item__meta">
                            <strong><?= htmlspecialchars($c['username']) ?></strong>

                            <?php if (!empty($c['phone'])): ?>
                                <span class="comment-item__contact"> · SĐT: <?= htmlspecialchars($c['phone']) ?></span>
                            <?php endif; ?>

                            <?php if (!empty($c['email'])): ?>
                                <span class="comment-item__contact"> · Email: <?= htmlspecialchars($c['email']) ?></span>
                            <?php endif; ?>

                            <span> · <?= htmlspecialchars($c['created_at']) ?></span>
                        </div>

                        <p class="comment-item__body"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:var(--color-ink-muted);">
                    Hiện chưa có phản hồi nào được hiển thị.
                </p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>