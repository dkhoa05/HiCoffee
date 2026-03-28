<?php
require dirname(__DIR__) . '/db.php';

try {
    // Kiểm tra kết nối
    $conn->query("SELECT 1");
    echo "<p style='color: green; text-align: center;'>Kết nối cơ sở dữ liệu thành công!</p>";
} catch (PDOException $e) {
    // Thông báo lỗi nếu kết nối thất bại
    echo "<p style='color: red; text-align: center;'>Lỗi kết nối cơ sở dữ liệu: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
