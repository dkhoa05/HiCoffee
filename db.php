<?php
// Trùng khớp cổng MySQL trong XAMPP (Control Panel → MySQL → cột Ports). Mặc định thường là 3306; nếu 3306 bị chiếm, XAMPP thường đổi sang 3307.
$host = '127.0.0.1';
$port = 3307;
$db = 'coffeeshop';
$user = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage(), 0); // Ghi log lỗi
    echo "<div style='color: red; text-align: center;'>Lỗi: Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại sau.</div>";
    exit;
}
?>
