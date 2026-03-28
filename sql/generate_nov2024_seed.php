<?php
/**
 * Sinh file sql/seed_november_2024.sql (UTF-8).
 * Chạy: php sql/generate_nov2024_seed.php
 */
declare(strict_types=1);

$hashUser123 = '$2y$10$hWHa4A06iaI4GVZIhYQDFOM9Ndisu62HmXoSyqZGz.4cHyURQExB6';

$users = [
    'minh_anh', 'thanh_tung', 'hong_ngoc', 'duc_manh', 'thu_ha',
    'van_khanh', 'quoc_bao', 'kim_loan', 'hai_dang', 'phuong_linh',
    'trong_nghia', 'bich_tram', 'xuan_mai', 'hoang_long', 'diep_anh',
    'tuan_kiet', 'ngoc_bich', 'duc_thinh', 'lan_chi', 'minh_khoi',
];

$existingProducts = [
    ['Cà phê đen', 'Pha phin truyền thống Robusta–Arabica, hậu vị ngọt nhẹ — nóng hoặc đá.', 'Cà phê', 30000, 'cfden.jpg', 1],
    ['Cà phê sữa', 'Sữa đặc hòa quyện cà phê đậm, cân bằng vị đắng–ngọt.', 'Cà phê', 35000, 'cfsua.jpg', 1],
    ['Bạc xỉu', 'Espresso gốc Việt, lớp sữa tươi béo phủ trên.', 'Cà phê', 35000, 'bxiu.jpg', 1],
    ['Cà phê latte', 'Espresso + sữa tươi steam mịn, latte art nhẹ.', 'Cà phê', 45000, 'latte.jpg', 1],
    ['Americano nóng', 'Espresso pha loãng nước nóng, thơm caramel.', 'Cà phê', 40000, 'cfden.jpg', 0],
    ['Trà đào cam sả', 'Trà đen, đào ngâm, cam vàng và sả thơm.', 'Trà', 35000, 'tradao.jpg', 1],
    ['Trà sữa truyền thống', 'Trà đen Đài Loan, sữa béo — ghi chú ít đường khi đặt.', 'Trà', 35000, 'trasua_truyenthong.jpg', 0],
    ['Trà xanh đá xay', 'Matcha / trà xanh xay cùng đá và kem.', 'Đá xay & đặc biệt', 45000, 'traxanh.jpg', 0],
    ['Cà phê đá xay', 'Blend cà phê đá xay mịn, có thể thêm kem.', 'Đá xay & đặc biệt', 40000, 'cfdaxay.jpg', 0],
    ['Cookie đá xay', 'Bánh cookie nghiền, sữa và đá xay.', 'Đá xay & đặc biệt', 50000, 'cookie.jpg', 0],
    ['Nước cam ép', 'Cam vàng tươi ép tại chỗ.', 'Nước ép & tươi mát', 30000, 'nuoccamep.jpg', 0],
    ['Sinh tố xoài', 'Xoài chín, sữa chua/sữa tươi, đá.', 'Nước ép & tươi mát', 40000, 'sinhtoxoai.jpg', 0],
    ['Bánh Flan caramel', 'Flan mềm, caramel thơm — ăn kèm cà phê.', 'Ăn nhẹ', 20000, 'banhflan.jpg', 0],
    ['Khoai tây chiên', 'Giòn rụm, rắc muối / tỏi bơ tùy đợt.', 'Ăn nhẹ', 30000, 'khoaitaychien.jpg', 0],
];

$newProducts = [
    ['Espresso shot đôi', 'Hai shot espresso Ristretto, crema dày.', 'Cà phê', 28000, 'cfden.jpg', 0],
    ['Cappuccino', 'Espresso, sữa steam và lớp foam mịn.', 'Cà phê', 42000, 'latte.jpg', 1],
    ['Cold brew cam', 'Cold brew ủ lạnh, thêm cam và syrup nhẹ.', 'Cà phê', 48000, 'nuoccamep.jpg', 1],
    ['Trà ô long sữa', 'Ô long thanh chát, sữa tươi — vừa uống.', 'Trà', 38000, 'trasua_truyenthong.jpg', 0],
    ['Trà vải hoa nhài', 'Trà đen, vải tươi, hoa nhài thơm.', 'Trà', 36000, 'tradao.jpg', 1],
    ['Trà táo bạc hà', 'Trà xanh, táo xanh, bạc hà mát.', 'Trà', 34000, 'traxanh.jpg', 0],
    ['Freeze dâu', 'Đá xay dâu, kem phô mai mặn.', 'Freeze & đá xay', 52000, 'cookie.jpg', 1],
    ['Freeze cacao', 'Cacao đậm, sữa tươi, topping chip sô-cô-la.', 'Freeze & đá xay', 50000, 'cfdaxay.jpg', 0],
    ['Soda chanh dây', 'Soda, chanh dây, lá húng nhẹ.', 'Soda & đồ uống lạnh', 32000, 'nuoccamep.jpg', 0],
    ['Tonic cold brew', 'Cold brew pha tonic, cam khô.', 'Soda & đồ uống lạnh', 45000, 'cfden.jpg', 0],
    ['Sinh tố bơ', 'Bơ sáp, sữa đặc vừa, đá.', 'Sinh tố & yogurt', 45000, 'sinhtoxoai.jpg', 1],
    ['Yogurt việt quất', 'Sữa chua uống, mứt việt quất.', 'Sinh tố & yogurt', 39000, 'traxanh.jpg', 0],
    ['Combo sáng A', 'Cà phê sữa + bánh flan.', 'Combo & ưu đãi', 48000, 'cfsua.jpg', 1],
    ['Combo học sinh', 'Trà đào size M + khoai tây nhỏ.', 'Combo & ưu đãi', 55000, 'tradao.jpg', 0],
    ['Bánh croissant bơ', 'Croissant layers bơ lạt, nướng giòn.', 'Ăn nhẹ', 28000, 'banhflan.jpg', 0],
    ['Sandwich trứng', 'Trứng ốp la, sốt mayo, rau xà lách.', 'Ăn nhẹ', 35000, 'khoaitaychien.jpg', 0],
];

$allProducts = array_merge($existingProducts, $newProducts);
$numProducts = count($allProducts);

function sqlStr(string $s): string
{
    return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $s) . "'";
}

function randomDatetime2024Nov(): string
{
    $day = mt_rand(10, 30);
    $h = mt_rand(8, 21);
    $m = mt_rand(0, 59);
    $s = mt_rand(0, 59);

    return sprintf('2024-11-%02d %02d:%02d:%02d', $day, $h, $m, $s);
}

ob_start();

echo "-- Hi Coffee — seed dữ liệu demo đồ án (10–30/11/2024)\n";
echo "-- Mật khẩu tất cả tài khoản khách: user123 | admin: admin123\n";
echo "-- Import: phpMyAdmin → SQL → chọn DB coffeeshop → dán file này.\n\n";
echo "SET NAMES utf8mb4;\n";
echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";
echo "TRUNCATE TABLE `product_reviews`;\n";
echo "DELETE FROM `order_items`;\n";
echo "DELETE FROM `orders`;\n";
echo "DELETE FROM `comments`;\n";
echo "-- Xóa toàn bộ user rồi nạp lại admin + 20 khách (tránh trùng username)\n";
echo "DELETE FROM `users`;\n";
echo "DELETE FROM `products`;\n";
echo "-- DELETE không reset AUTO_INCREMENT — phải ALTER để id user/sp/đơn bắt đầu từ 1 (khớp FK trong file)\n";
echo "ALTER TABLE `users` AUTO_INCREMENT = 1;\n";
echo "ALTER TABLE `products` AUTO_INCREMENT = 1;\n";
echo "ALTER TABLE `orders` AUTO_INCREMENT = 1;\n";
echo "ALTER TABLE `order_items` AUTO_INCREMENT = 1;\n";
echo "SET FOREIGN_KEY_CHECKS = 1;\n\n";

echo "INSERT INTO `users` (`username`, `password`, `is_admin`) VALUES\n";
echo "('admin', '\$2y\$10\$vNiLJTiHSf6JP353E1zpjO6HZnjtgHNa3pgdXR32V.WXtKNhyMvKa', 1)";
foreach ($users as $u) {
    echo ",\n('{$u}', '{$hashUser123}', 0)";
}
echo ";\n\n";

echo "INSERT INTO `products` (`name`, `description`, `category`, `price`, `image`, `is_featured`) VALUES\n";
$rows = [];
foreach ($allProducts as $p) {
    $rows[] = sprintf(
        '(%s, %s, %s, %d, %s, %d)',
        sqlStr($p[0]),
        sqlStr($p[1]),
        sqlStr($p[2]),
        $p[3],
        sqlStr($p[4]),
        $p[5]
    );
}
echo implode(",\n", $rows) . ";\n\n";

mt_srand(20241110);
$statusWeights = ['Completed' => 52, 'Placed' => 18, 'Cancelled' => 8, 'Pending' => 6];
$statusList = [];
foreach ($statusWeights as $st => $w) {
    for ($i = 0; $i < $w; $i++) {
        $statusList[] = $st;
    }
}
shuffle($statusList);

$sizes = ['S', 'M', 'L', 'XL'];
$sweet = ['vua', 'du', 'nhieu'];
$ice = ['it', 'nhieu', 'rieng'];

$orders = [];
$orderId = 1;
$orderItemsSql = [];
$reviewCandidates = [];
$nextItemId = 1;

foreach ($statusList as $status) {
    $uid = 2 + mt_rand(0, count($users) - 1);
    $created = randomDatetime2024Nov();
    $nLines = mt_rand(1, 3);
    $lines = [];
    $total = 0;
    for ($k = 0; $k < $nLines; $k++) {
        $pid = 1 + mt_rand(0, $numProducts - 1);
        $qty = mt_rand(1, 3);
        $price = (int) $allProducts[$pid - 1][3];
        $total += $qty * $price;
        $note = mt_rand(0, 4) === 0 ? 'Ít đường' : null;
        $conf = 0;
        if ($status === 'Completed' && mt_rand(1, 100) <= 72) {
            $conf = 1;
        }
        $lines[] = [
            'product_id' => $pid,
            'quantity' => $qty,
            'price' => $price,
            'size' => $sizes[mt_rand(0, 3)],
            'sweetness' => $sweet[mt_rand(0, 2)],
            'ice_level' => $ice[mt_rand(0, 2)],
            'item_note' => $note,
            'confirmed_received' => $conf,
        ];
    }
    $cancel = null;
    if ($status === 'Cancelled') {
        $reasons = ['Đổi ý', 'Đặt nhầm món', 'Không còn ở địa chỉ nhận', 'Giá chưa phù hợp', 'Chọn nhầm size'];
        $cancel = $reasons[mt_rand(0, count($reasons) - 1)];
    }
    $orders[] = [
        'id' => $orderId,
        'user_id' => $uid,
        'status' => $status,
        'cancel_reason' => $cancel,
        'created_at' => $created,
        'total_price' => $total,
    ];
    foreach ($lines as $L) {
        $noteSql = $L['item_note'] === null ? 'NULL' : sqlStr($L['item_note']);
        $orderItemsSql[] = sprintf(
            '(%d, %d, %d, %d, %s, %s, %s, %s, %d)',
            $orderId,
            $L['product_id'],
            $L['quantity'],
            $L['price'],
            sqlStr($L['size']),
            sqlStr($L['sweetness']),
            sqlStr($L['ice_level']),
            $noteSql,
            $L['confirmed_received']
        );
        if ($status === 'Completed' && $L['confirmed_received'] === 1 && count($reviewCandidates) < 25) {
            $reviewCandidates[] = [
                'order_item_id' => $nextItemId,
                'product_id' => $L['product_id'],
                'user_id' => $uid,
                'created' => $created,
            ];
        }
        $nextItemId++;
    }
    $orderId++;
}

echo "INSERT INTO `orders` (`id`, `user_id`, `status`, `cancel_reason`, `created_at`, `total_price`) VALUES\n";
$chunks = [];
foreach ($orders as $o) {
    $cr = $o['cancel_reason'] === null ? 'NULL' : sqlStr($o['cancel_reason']);
    $chunks[] = sprintf(
        '(%d, %d, %s, %s, %s, %.2f)',
        $o['id'],
        $o['user_id'],
        sqlStr($o['status']),
        $cr,
        sqlStr($o['created_at']),
        $o['total_price']
    );
}
echo implode(",\n", $chunks) . ";\n\n";

echo "INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`, `size`, `sweetness`, `ice_level`, `item_note`, `confirmed_received`) VALUES\n";
echo implode(",\n", $orderItemsSql) . ";\n\n";

$maxOrderId = $orderId;
$maxItemId = $nextItemId;
echo "ALTER TABLE `orders` AUTO_INCREMENT = {$maxOrderId};\n";
echo "ALTER TABLE `order_items` AUTO_INCREMENT = {$maxItemId};\n\n";

$comments = [
    ['minh_anh', '0909111222', null, 'Giao diện đặt hàng dễ hiểu, mong thêm món cold brew chai.'],
    ['thu_ha', null, 'thuha@mail.demo', 'Trà vải thơm, ship đúng giờ buổi chiều.'],
    ['duc_manh', '0933444555', null, 'Combo sáng tiện cho sinh viên luôn.'],
    ['phuong_linh', null, 'linh.p@gmail.demo', 'Freeze dâu hơi ngọt nhưng vẫn thích.'],
    ['hoang_long', '0977889900', null, 'Cà phê đậm đúng gu, sẽ đặt lại.'],
    ['lan_chi', null, null, 'Website chạy mượt trên điện thoại.'],
    ['tuan_kiet', '0912233444', null, 'Tonic cold brew lạ miệng, recommend.'],
    ['ngoc_bich', null, 'bich.ngoc@demo.vn', 'Nhân viên xác nhận đơn nhanh (demo đồ án).'],
    ['Khách lẻ', null, null, 'Giá cả minh bạch, thích phần mô tả món.'],
    ['kim_loan', '0988776655', null, 'Sandwich trứng ăn kèm latte rất hợp.'],
];

echo "INSERT INTO `comments` (`username`, `phone`, `email`, `comment`, `created_at`) VALUES\n";
$cc = [];
foreach ($comments as $i => $c) {
    $day = 11 + ($i % 18);
    $h = 9 + ($i % 10);
    $m = ($i * 7) % 60;
    $created = sprintf('2024-11-%02d %02d:%02d:00', $day, $h, $m);
    $cc[] = sprintf(
        '(%s, %s, %s, %s, %s)',
        sqlStr($c[0]),
        $c[1] ? sqlStr($c[1]) : 'NULL',
        $c[2] ? sqlStr($c[2]) : 'NULL',
        sqlStr($c[3]),
        sqlStr($created)
    );
}
echo implode(",\n", $cc) . ";\n\n";

$reviewTexts = [
    ['Rất hài lòng, đúng mô tả món.', 5],
    ['Giao nhanh, nhiệt độ đồ uống ổn.', 5],
    ['Hơi ngọt so với gu nhưng chất lượng tốt.', 4],
    ['Sẽ đặt lại lần sau.', 5],
    ['Packaging gọn, không bị đổ.', 5],
    ['Đá xay mịn, topping đủ.', 4],
    ['Cà phê đậm vừa ý.', 5],
    ['Trà thơm, ít đá như ghi chú.', 5],
];

echo "INSERT INTO `product_reviews` (`product_id`, `user_id`, `order_item_id`, `display_name`, `phone`, `email`, `content`, `stars`, `created_at`) VALUES\n";
$rv = [];
foreach (array_slice($reviewCandidates, 0, 12) as $i => $rc) {
    $u = $users[$rc['user_id'] - 2] ?? 'khach';
    $pair = $reviewTexts[$i % count($reviewTexts)];
    $rvTime = sprintf('2024-11-%02d %02d:30:00', 12 + ($i % 17), 10 + ($i % 8));
    $rv[] = sprintf(
        '(%d, %d, %d, %s, NULL, NULL, %s, %d, %s)',
        $rc['product_id'],
        $rc['user_id'],
        $rc['order_item_id'],
        sqlStr($u),
        sqlStr($pair[0]),
        $pair[1],
        sqlStr($rvTime)
    );
}
echo implode(",\n", $rv) . ";\n\n";

echo "-- Xong: ~20 khách, 30 SP, ~84 đơn trong 10–30/11/2024\n";

file_put_contents(__DIR__ . '/seed_november_2024.sql', ob_get_clean());
fwrite(STDERR, "Wrote " . __DIR__ . "/seed_november_2024.sql\n");
