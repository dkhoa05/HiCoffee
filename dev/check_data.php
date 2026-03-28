<?php
// Kết nối đến cơ sở dữ liệu
require dirname(__DIR__) . '/db.php'; // Kiểm tra kết nối CSDL

// Kiểm tra dữ liệu có trong các bảng không
$sql_check = "SELECT * FROM orders LIMIT 1"; // Lấy một đơn hàng
$stmt_check = $conn->query($sql_check);
$order = $stmt_check->fetch();
var_dump($order); // In dữ liệu ra để kiểm tra

$sql_check_items = "SELECT * FROM order_items LIMIT 1"; // Lấy một bản ghi từ order_items
$stmt_check_items = $conn->query($sql_check_items);
$order_item = $stmt_check_items->fetch();
var_dump($order_item); // In dữ liệu ra để kiểm tra

$sql_check_products = "SELECT * FROM products LIMIT 1"; // Lấy một sản phẩm
$stmt_check_products = $conn->query($sql_check_products);
$product = $stmt_check_products->fetch();
var_dump($product); // In dữ liệu ra để kiểm tra
?>
