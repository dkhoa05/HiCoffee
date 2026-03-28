-- =============================================================================
-- Hi Coffee — RESET & CÀI ĐẶT LẠI TOÀN BỘ CSDL (MariaDB / MySQL 5.7+)
-- =============================================================================
-- Cách dùng phpMyAdmin:
--   1. Tab "SQL" (không cần chọn database trước) — dán toàn bộ file → Thực hiện
--   2. Hoặc chọn sẵn database `coffeeshop` rồi chạy (bỏ qua 2 dòng CREATE/USE nếu lỗi quyền)
--
-- Sau khi chạy:
--   • admin  / admin123  (quản trị)
--   • demo   / user123   (khách)
--   • hieu   / user123   (khách mẫu thêm)
--
-- Ảnh sản phẩm: đặt file trong thư mục img/ của project (tên như cột image).
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coffeeshop`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coffeeshop`;

DROP TABLE IF EXISTS `product_reviews`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Bảng người dùng
-- -----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- bcrypt: admin123, user123 (PHP password_hash)
INSERT INTO `users` (`username`, `password`, `is_admin`) VALUES
('admin', '$2y$10$vNiLJTiHSf6JP353E1zpjO6HZnjtgHNa3pgdXR32V.WXtKNhyMvKa', 1),
('demo', '$2y$10$hWHa4A06iaI4GVZIhYQDFOM9Ndisu62HmXoSyqZGz.4cHyURQExB6', 0),
('hieu', '$2y$10$hWHa4A06iaI4GVZIhYQDFOM9Ndisu62HmXoSyqZGz.4cHyURQExB6', 0);

-- -----------------------------------------------------------------------------
-- Sản phẩm (có danh mục — khớp includes/product_constants.php)
-- -----------------------------------------------------------------------------
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(80) NOT NULL DEFAULT 'Khác',
  `price` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_products_category` (`category`),
  KEY `idx_products_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`name`, `description`, `category`, `price`, `image`, `is_featured`) VALUES
('Cà phê đen', 'Pha phin truyền thống Robusta–Arabica, hậu vị ngọt nhẹ — nóng hoặc đá.', 'Cà phê', 30000, 'cfden.jpg', 1),
('Cà phê sữa', 'Sữa đặc hòa quyện cà phê đậm, cân bằng vị đắng–ngọt.', 'Cà phê', 35000, 'cfsua.jpg', 1),
('Bạc xỉu', 'Espresso gốc Việt, lớp sữa tươi béo phủ trên.', 'Cà phê', 35000, 'bxiu.jpg', 1),
('Cà phê latte', 'Espresso + sữa tươi steam mịn, latte art nhẹ.', 'Cà phê', 45000, 'latte.jpg', 1),
('Americano nóng', 'Espresso pha loãng nước nóng, thơm caramel.', 'Cà phê', 40000, 'cfden.jpg', 0),
('Trà đào cam sả', 'Trà đen, đào ngâm, cam vàng và sả thơm.', 'Trà', 35000, 'tradao.jpg', 1),
('Trà sữa truyền thống', 'Trà đen Đài Loan, sữa béo — ghi chú ít đường khi đặt.', 'Trà', 35000, 'trasua_truyenthong.jpg', 0),
('Trà xanh đá xay', 'Matcha / trà xanh xay cùng đá và kem.', 'Đá xay & đặc biệt', 45000, 'traxanh.jpg', 0),
('Cà phê đá xay', 'Blend cà phê đá xay mịn, có thể thêm kem.', 'Đá xay & đặc biệt', 40000, 'cfdaxay.jpg', 0),
('Cookie đá xay', 'Bánh cookie nghiền, sữa và đá xay.', 'Đá xay & đặc biệt', 50000, 'cookie.jpg', 0),
('Nước cam ép', 'Cam vàng tươi ép tại chỗ.', 'Nước ép & tươi mát', 30000, 'nuoccamep.jpg', 0),
('Sinh tố xoài', 'Xoài chín, sữa chua/sữa tươi, đá.', 'Nước ép & tươi mát', 40000, 'sinhtoxoai.jpg', 0),
('Bánh Flan caramel', 'Flan mềm, caramel thơm — ăn kèm cà phê.', 'Ăn nhẹ', 20000, 'banhflan.jpg', 0),
('Khoai tây chiên', 'Giòn rụm, rắc muối / tỏi bơ tùy đợt.', 'Ăn nhẹ', 30000, 'khoaitaychien.jpg', 0);

-- -----------------------------------------------------------------------------
-- Đơn hàng
-- -----------------------------------------------------------------------------
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` enum('Pending','Placed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `cancel_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_created` (`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `size` enum('S','M','L','XL') NOT NULL DEFAULT 'M',
  `sweetness` enum('vua','du','nhieu') NOT NULL DEFAULT 'vua',
  `ice_level` enum('it','nhieu','rieng') NOT NULL DEFAULT 'it',
  `item_note` varchar(500) DEFAULT NULL,
  `confirmed_received` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_oi_order` (`order_id`),
  KEY `idx_oi_product` (`product_id`),
  CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đơn mẫu (user demo id=2): (1) hoàn thành + đã nhận để demo đánh giá, (2) chờ duyệt, (3) trong giỏ
INSERT INTO `orders` (`id`, `user_id`, `status`, `cancel_reason`, `created_at`, `total_price`) VALUES
(1, 2, 'Completed', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY), 65000.00),
(2, 2, 'Placed', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), 35000.00),
(3, 2, 'Pending', NULL, NOW(), 80000.00);

INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`, `size`, `sweetness`, `ice_level`, `item_note`, `confirmed_received`) VALUES
(1, 1, 1, 30000, 'M', 'vua', 'it', NULL, 1),
(1, 2, 1, 35000, 'L', 'du', 'nhieu', 'Ít đường', 1),
(2, 3, 1, 35000, 'M', 'vua', 'rieng', NULL, 0),
(3, 4, 1, 45000, 'M', 'vua', 'it', NULL, 0),
(3, 6, 1, 35000, 'S', 'nhieu', 'it', 'Thêm đá', 0);

-- -----------------------------------------------------------------------------
-- Bình luận / góp ý
-- -----------------------------------------------------------------------------
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_comments_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comments` (`username`, `phone`, `email`, `comment`, `created_at`) VALUES
('demo', '0909123456', NULL, 'Không gian web dễ nhìn, mong quán thêm món cold brew!', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('hieu', NULL, 'hieu@example.com', 'Trà đào vừa miệng, giao nhanh (demo đồ án).', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('Khách ẩn danh', NULL, NULL, 'Latte béo vừa, sẽ quay lại đặt thêm.', NOW());

-- -----------------------------------------------------------------------------
-- Đánh giá sản phẩm (sau khi đơn hoàn thành + khách xác nhận đã nhận)
-- -----------------------------------------------------------------------------
CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `stars` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pr_order_item` (`order_item_id`),
  KEY `idx_pr_product` (`product_id`),
  KEY `idx_pr_user` (`user_id`),
  CONSTRAINT `fk_pr_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pr_oi` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_reviews` (`product_id`, `user_id`, `order_item_id`, `display_name`, `phone`, `email`, `content`, `stars`) VALUES
(1, 2, 1, 'demo', '0909123456', NULL, 'Cà phê đen đậm đà, rất ổn!', 5);

-- =============================================================================
-- Hoàn tất. Kiểm tra: SELECT COUNT(*) FROM products; → 14
--
-- Dữ liệu demo đồ án (20 khách, 30 SP, đơn 10–30/11/2024): chạy thêm
--   sql/seed_november_2024.sql
-- Sinh lại file seed:  php sql/generate_nov2024_seed.php
-- =============================================================================
