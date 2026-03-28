<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}

$adminNavCurrent = 'comments';

$comments = $conn->query('SELECT * FROM comments ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Quản lý bình luận — Hi Coffee';
$pageDescription = 'Duyệt và xóa bình luận hỗ trợ Hi Coffee.';
require dirname(__DIR__) . '/includes/header.php';
?>
<main id="main-content" class="site-main site-main--admin" tabindex="-1">
<div id="main" class="content-panel" style="max-width:1100px;">
    <h1 class="page-title">Quản lý bình luận</h1>
    <p class="page-lead">Nội dung góp ý từ trang hỗ trợ khách hàng.</p>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người gửi</th>
                    <th>Liên hệ</th>
                    <th>Nội dung</th>
                    <th>Thời gian</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= (int) $comment['id'] ?></td>
                        <td><?= htmlspecialchars($comment['username']) ?></td>
                        <td style="max-width:10rem;white-space:normal;font-size:0.85rem;">
                            <?php if (!empty($comment['phone'])): ?>
                                <div>SĐT: <?= htmlspecialchars($comment['phone']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($comment['email'])): ?>
                                <div><?= htmlspecialchars($comment['email']) ?></div>
                            <?php endif; ?>
                            <?php if (empty($comment['phone']) && empty($comment['email'])): ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td style="max-width:14rem;white-space:normal;"><?= htmlspecialchars($comment['comment']) ?></td>
                        <td><?= htmlspecialchars($comment['created_at']) ?></td>
                        <td>
                            <a href="<?= htmlspecialchars(app_url('admin/delete_comment.php?id=' . (int) $comment['id'])) ?>" class="btn btn--sm" style="background:#c62828;" onclick="return confirm('Xóa?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($comments)): ?>
        <p class="text-center" style="color:var(--color-ink-muted);">Chưa có bình luận.</p>
    <?php endif; ?>
</div>
</main>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
