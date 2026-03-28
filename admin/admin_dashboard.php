<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . app_url('auth/login.php'));
    exit;
}

$validStatuses = ['Pending', 'Placed', 'Completed', 'Cancelled'];

$explicitDates = isset($_GET['date_from'], $_GET['date_to'])
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $_GET['date_from'])
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $_GET['date_to']);

$today = new DateTimeImmutable('today');
$todayStr = $today->format('Y-m-d');

$orderBounds = $conn->query('SELECT MIN(DATE(created_at)) AS dmin, MAX(DATE(created_at)) AS dmax FROM orders')->fetch(PDO::FETCH_ASSOC);
$ordersMinDate = !empty($orderBounds['dmin']) ? (string) $orderBounds['dmin'] : $todayStr;
$ordersMaxDate = !empty($orderBounds['dmax']) ? (string) $orderBounds['dmax'] : $todayStr;
if ($ordersMinDate > $ordersMaxDate) {
    [$ordersMinDate, $ordersMaxDate] = [$ordersMaxDate, $ordersMinDate];
}

if ($explicitDates) {
    $dateFrom = trim((string) $_GET['date_from']);
    $dateTo = trim((string) $_GET['date_to']);
} else {
    /* Tất cả / URL sạch: toàn bộ khoảng ngày có đơn trong CSDL */
    $dateFrom = $ordersMinDate;
    $dateTo = $ordersMaxDate;
}

$statusFilter = array_key_exists('status', $_GET) ? trim((string) $_GET['status']) : '';

$d1 = DateTimeImmutable::createFromFormat('Y-m-d', $dateFrom);
$d2 = DateTimeImmutable::createFromFormat('Y-m-d', $dateTo);
if (!$d1 || $d1->format('Y-m-d') !== $dateFrom) {
    $dateFrom = $todayStr;
    $dateTo = $todayStr;
    $d1 = DateTimeImmutable::createFromFormat('Y-m-d', $dateFrom);
    $d2 = DateTimeImmutable::createFromFormat('Y-m-d', $dateTo);
}
if (!$d2 || $d2->format('Y-m-d') !== $dateTo) {
    $d2 = $d1;
    $dateTo = $dateFrom;
}
if ($d1 > $d2) {
    [$dateFrom, $dateTo, $d1, $d2] = [$dateTo, $dateFrom, $d2, $d1];
}

if ($statusFilter !== '' && !in_array($statusFilter, $validStatuses, true)) {
    $statusFilter = '';
}

$filterActive = $explicitDates || $statusFilter !== '';

$interval = $d1->diff($d2);
if ($explicitDates && $interval->days > 120) {
    $dateTo = $d1->modify('+120 days')->format('Y-m-d');
    $d2 = DateTimeImmutable::createFromFormat('Y-m-d', $dateTo);
}

$paramsDate = [$dateFrom, $dateTo];

$statusClause = '';
$paramsStatus = [];
if ($statusFilter !== '') {
    $statusClause = ' AND o.status = ? ';
    $paramsStatus[] = $statusFilter;
}

$countUsers = (int) $conn->query('SELECT COUNT(*) FROM users WHERE COALESCE(is_admin, 0) = 0')->fetchColumn();
$countAdmins = (int) $conn->query('SELECT COUNT(*) FROM users WHERE is_admin = 1')->fetchColumn();
$countProducts = (int) $conn->query('SELECT COUNT(*) FROM products')->fetchColumn();

$sqlBuyers = 'SELECT COUNT(DISTINCT o.user_id) FROM orders o WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?' . $statusClause;
$stmt = $conn->prepare($sqlBuyers);
$stmt->execute(array_merge($paramsDate, $paramsStatus));
$countBuyersPeriod = (int) $stmt->fetchColumn();

$sqlOrd = 'SELECT COUNT(*) FROM orders o WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?' . $statusClause;
$stmt = $conn->prepare($sqlOrd);
$stmt->execute(array_merge($paramsDate, $paramsStatus));
$countOrdersPeriod = (int) $stmt->fetchColumn();

$sqlRev = 'SELECT COALESCE(SUM(o.total_price), 0) FROM orders o WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?' . $statusClause;
$stmt = $conn->prepare($sqlRev);
$stmt->execute(array_merge($paramsDate, $paramsStatus));
$revenuePeriod = (float) $stmt->fetchColumn();

$stmtC = $conn->prepare('SELECT COUNT(*) FROM comments WHERE DATE(created_at) >= ? AND DATE(created_at) <= ?');
$stmtC->execute($paramsDate);
$countCommentsPeriod = (int) $stmtC->fetchColumn();

$sqlStatus = 'SELECT o.status, COUNT(*) AS c FROM orders o WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?' . $statusClause . ' GROUP BY o.status';
$stmt = $conn->prepare($sqlStatus);
$stmt->execute(array_merge($paramsDate, $paramsStatus));
$statusRows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$dayMap = [];
$sqlDays = 'SELECT DATE(o.created_at) AS d, COUNT(*) AS c FROM orders o WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?' . $statusClause . ' GROUP BY DATE(o.created_at)';
$stmt = $conn->prepare($sqlDays);
$stmt->execute(array_merge($paramsDate, $paramsStatus));
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $dayMap[$row['d']] = (int) $row['c'];
}

$spanDays = $d1->diff($d2)->days;
$dateLabelFmt = ($spanDays > 50 || $d1->format('Y') !== $d2->format('Y')) ? 'd/m/Y' : 'd/m';

$labelsDaily = [];
$ordersDaily = [];
for ($cur = $d1; $cur <= $d2; $cur = $cur->modify('+1 day')) {
    $ds = $cur->format('Y-m-d');
    $labelsDaily[] = $cur->format($dateLabelFmt);
    $ordersDaily[] = $dayMap[$ds] ?? 0;
}
if (empty($labelsDaily)) {
    $labelsDaily = ['—'];
    $ordersDaily = [0];
}

$sqlTop = '
    SELECT p.name, SUM(oi.quantity) AS qty
    FROM order_items oi
    JOIN orders o ON o.id = oi.order_id
    JOIN products p ON p.id = oi.product_id
    WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?' . $statusClause . '
    GROUP BY oi.product_id, p.name
    ORDER BY qty DESC
    LIMIT 8
';
$stmt = $conn->prepare($sqlTop);
$stmt->execute(array_merge($paramsDate, $paramsStatus));
$topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$topLabels = array_column($topProducts, 'name');
$topQty = array_map('intval', array_column($topProducts, 'qty'));
if (empty($topLabels)) {
    $topLabels = ['—'];
    $topQty = [0];
}

$presetUrl = static function (string $from, string $to, string $status = ''): string {
    $q = ['date_from' => $from, 'date_to' => $to];
    if ($status !== '') {
        $q['status'] = $status;
    }

    return app_url('admin/admin_dashboard.php?' . http_build_query($q));
};

$presetActive = static function (string $df, string $dt, string $st, string $curF, string $curT, string $curSt): bool {
    return $df === $curF && $dt === $curT && $st === $curSt;
};

$yesterday = $today->modify('-1 day');
$from7 = $today->modify('-6 days');
$from30 = $today->modify('-29 days');
$monthStart = $today->modify('first day of this month');
$firstLastMonth = $today->modify('first day of last month');
$endLastMonth = $today->modify('last day of last month');

$dashboardPresets = [
    ['label' => 'Tất cả', 'reset' => true],
    ['label' => 'Hôm nay', 'from' => $today->format('Y-m-d'), 'to' => $today->format('Y-m-d'), 'status' => '', 'require_explicit' => true],
    ['label' => 'Hôm qua', 'from' => $yesterday->format('Y-m-d'), 'to' => $yesterday->format('Y-m-d'), 'status' => ''],
    ['label' => '7 ngày qua', 'from' => $from7->format('Y-m-d'), 'to' => $today->format('Y-m-d'), 'status' => ''],
    ['label' => '30 ngày qua', 'from' => $from30->format('Y-m-d'), 'to' => $today->format('Y-m-d'), 'status' => ''],
    ['label' => 'Tháng này', 'from' => $monthStart->format('Y-m-d'), 'to' => $today->format('Y-m-d'), 'status' => ''],
    ['label' => 'Tháng trước', 'from' => $firstLastMonth->format('Y-m-d'), 'to' => $endLastMonth->format('Y-m-d'), 'status' => ''],
];

$dashboardPresets[] = [
    'label' => 'Chỉ đơn hoàn thành',
    'from' => $dateFrom,
    'to' => $dateTo,
    'status' => 'Completed',
    'dynamic_dates' => true,
];

$adminNavCurrent = 'dashboard';
$pageTitle = 'Dashboard quản trị | Hi Coffee';
$pageDescription = 'Bảng điều khiển quản trị Hi Coffee: theo dõi đơn hàng, doanh thu, khách hàng và hiệu suất bán hàng theo thời gian.';
require dirname(__DIR__) . '/includes/header.php';

$chartStatusData = [];
foreach (['Pending', 'Placed', 'Completed', 'Cancelled'] as $st) {
    $chartStatusData[] = (int) ($statusRows[$st] ?? 0);
}
?>
<main id="main-content" class="site-main site-main--admin" tabindex="-1">
<div class="admin-dashboard content-panel" style="max-width:1180px;">
    <h1 class="page-title">Bảng điều khiển hệ thống</h1>
    <p class="page-lead">
        Theo dõi tổng quan hoạt động kinh doanh của Hi Coffee theo từng giai đoạn, bao gồm số lượng đơn hàng,
        doanh thu, phản hồi khách hàng và hiệu suất bán hàng của sản phẩm. Sử dụng bộ lọc bên dưới để xem dữ liệu chi tiết hơn.
    </p>

    <div class="admin-filters-wrap">
        <span class="admin-dashboard__toolbar-label">Khoảng thời gian nhanh</span>
        <nav class="admin-dashboard-presets" aria-label="Các mốc thời gian gợi ý">
            <?php foreach ($dashboardPresets as $p): ?>
                <?php
                if (!empty($p['reset'])) {
                    $href = app_url('admin/admin_dashboard.php');
                    $active = !$filterActive;
                } else {
                    $pf = $p['from'];
                    $pt = $p['to'];
                    $ps = $p['status'];
                    if (!empty($p['dynamic_dates'])) {
                        $pf = $dateFrom;
                        $pt = $dateTo;
                    }
                    $href = $presetUrl($pf, $pt, $ps);
                    $matches = $presetActive($pf, $pt, $ps, $dateFrom, $dateTo, $statusFilter);
                    $active = !empty($p['require_explicit'])
                        ? ($explicitDates && $matches)
                        : $matches;
                }
                ?>
                <a href="<?= htmlspecialchars($href) ?>" class="<?= $active ? 'is-active' : '' ?>">
                    <?= htmlspecialchars($p['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <p class="admin-dashboard-presets__note<?= $filterActive ? ' admin-dashboard-presets__note--active' : '' ?>">
            <?php if (!$filterActive): ?>
                Đang xem <strong>tất cả đơn hàng</strong> theo ngày tạo đơn: <?= htmlspecialchars($dateFrom) ?> → <?= htmlspecialchars($dateTo) ?>.
                Dùng lối tắt hoặc bộ lọc nâng cao để xem theo ngày cụ thể.
            <?php else: ?>
                <a href="<?= htmlspecialchars(app_url('admin/admin_dashboard.php')) ?>" class="btn btn--secondary admin-dashboard-presets__clear">Xóa bộ lọc hiện tại</a>
            <?php endif; ?>
        </p>

        <h2 class="section-title" style="margin-top:0.25rem;">Bộ lọc nâng cao</h2>
        <form class="admin-filters" method="get" action="<?= htmlspecialchars(app_url('admin/admin_dashboard.php')) ?>">
            <div class="admin-filters__field">
                <label for="f-from">Từ ngày</label>
                <input type="date" id="f-from" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>" required>
            </div>
            <div class="admin-filters__field">
                <label for="f-to">Đến ngày</label>
                <input type="date" id="f-to" name="date_to" value="<?= htmlspecialchars($dateTo) ?>" required>
            </div>
            <div class="admin-filters__field">
                <label for="f-status">Trạng thái đơn hàng</label>
                <select id="f-status" name="status">
                    <option value=""<?= $statusFilter === '' ? ' selected' : '' ?>>Tất cả</option>
                    <option value="Pending"<?= $statusFilter === 'Pending' ? ' selected' : '' ?>>Đang trong giỏ</option>
                    <option value="Placed"<?= $statusFilter === 'Placed' ? ' selected' : '' ?>>Đã đặt hàng</option>
                    <option value="Completed"<?= $statusFilter === 'Completed' ? ' selected' : '' ?>>Hoàn thành</option>
                    <option value="Cancelled"<?= $statusFilter === 'Cancelled' ? ' selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            <div class="admin-filters__actions">
                <button type="submit" class="btn">Cập nhật dữ liệu</button>
            </div>
        </form>
    </div>

    <section class="admin-kpi-grid" aria-label="Chỉ số tổng quan">
        <article class="admin-kpi">
            <span class="admin-kpi__label">Tổng số khách hàng</span>
            <strong class="admin-kpi__value"><?= number_format($countUsers) ?></strong>
            <span class="admin-kpi__hint">Tài khoản khách đã đăng ký trong hệ thống</span>
        </article>

        <article class="admin-kpi">
            <span class="admin-kpi__label">Khách phát sinh đơn trong kỳ</span>
            <strong class="admin-kpi__value"><?= number_format($countBuyersPeriod) ?></strong>
            <span class="admin-kpi__hint">Số khách hàng có ít nhất một đơn trong khoảng thời gian đã chọn</span>
        </article>

        <article class="admin-kpi">
            <span class="admin-kpi__label">Danh mục sản phẩm</span>
            <strong class="admin-kpi__value"><?= number_format($countProducts) ?></strong>
            <span class="admin-kpi__hint">Tổng số sản phẩm hiện có trong hệ thống</span>
        </article>

        <article class="admin-kpi">
            <span class="admin-kpi__label">Tổng đơn hàng trong kỳ</span>
            <strong class="admin-kpi__value"><?= number_format($countOrdersPeriod) ?></strong>
            <span class="admin-kpi__hint"><?= htmlspecialchars($dateFrom) ?> → <?= htmlspecialchars($dateTo) ?></span>
        </article>

        <article class="admin-kpi">
            <span class="admin-kpi__label">Số phản hồi khách hàng</span>
            <strong class="admin-kpi__value"><?= number_format($countCommentsPeriod) ?></strong>
            <span class="admin-kpi__hint">Ghi nhận từ trang hỗ trợ và góp ý</span>
        </article>

        <article class="admin-kpi admin-kpi--wide">
            <span class="admin-kpi__label">Doanh thu trong kỳ</span>
            <strong class="admin-kpi__value"><?= number_format($revenuePeriod) ?> đ</strong>
            <span class="admin-kpi__hint">
                Tổng doanh thu từ các đơn hàng trong khoảng thời gian đã chọn<?= $statusFilter !== '' ? ' · Trạng thái: ' . htmlspecialchars($statusFilter) : '' ?> · <?= number_format($countAdmins) ?> quản trị viên
            </span>
        </article>
    </section>

    <div class="admin-charts">
        <div class="admin-chart-card">
            <h2 class="section-title" style="margin-top:0;">Xu hướng đơn hàng theo ngày</h2>
            <canvas id="chartOrders14" height="120" aria-label="Biểu đồ xu hướng đơn hàng theo ngày"></canvas>
        </div>

        <div class="admin-chart-card">
            <h2 class="section-title" style="margin-top:0;">Phân bố trạng thái đơn hàng</h2>
            <canvas id="chartStatus" height="200" aria-label="Biểu đồ phân bố trạng thái đơn hàng"></canvas>
        </div>

        <div class="admin-chart-card admin-chart-card--wide">
            <h2 class="section-title" style="margin-top:0;">Sản phẩm bán chạy nhất</h2>
            <canvas id="chartTopProducts" height="140" aria-label="Biểu đồ sản phẩm bán chạy nhất"></canvas>
        </div>
    </div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function () {
    const accent = '#b45309';

    new Chart(document.getElementById('chartOrders14'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labelsDaily, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Số đơn hàng',
                data: <?= json_encode($ordersDaily) ?>,
                borderColor: accent,
                backgroundColor: 'rgba(180, 83, 9, 0.12)',
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Đang trong giỏ', 'Đã đặt hàng', 'Hoàn thành', 'Đã hủy'],
            datasets: [{
                data: <?= json_encode($chartStatusData) ?>,
                backgroundColor: ['#ca8a04', '#ea580c', '#15803d', '#78716c']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('chartTopProducts'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($topLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Số lượng đã bán',
                data: <?= json_encode($topQty) ?>,
                backgroundColor: accent
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
})();
</script>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>