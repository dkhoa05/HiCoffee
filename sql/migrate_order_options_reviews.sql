-- =============================================================================
-- Nâng cấp CSDL có sẵn (không xóa dữ liệu) — chạy một lần trên database coffeeshop
-- =============================================================================
-- Thêm: Placed + lý do hủy, tùy chọn món (size/độ ngọt/đá/ghi chú), nhận hàng,
--       bình luận SĐT/email, bảng đánh giá sản phẩm.
-- Nếu lỗi enum, sao lưu DB trước; có thể import lại sql/reset_fresh.sql để cài mới hoàn toàn.
-- =============================================================================

USE `coffeeshop`;

-- Đơn: thêm Placed + cancel_reason
ALTER TABLE `orders`
  MODIFY COLUMN `status` ENUM('Pending','Placed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  ADD COLUMN `cancel_reason` TEXT NULL AFTER `status`;

-- order_items: tùy chọn + xác nhận đã nhận
ALTER TABLE `order_items`
  ADD COLUMN `size` ENUM('S','M','L','XL') NOT NULL DEFAULT 'M' AFTER `price`,
  ADD COLUMN `sweetness` ENUM('vua','du','nhieu') NOT NULL DEFAULT 'vua' AFTER `size`,
  ADD COLUMN `ice_level` ENUM('it','nhieu','rieng') NOT NULL DEFAULT 'it' AFTER `sweetness`,
  ADD COLUMN `item_note` VARCHAR(500) NULL AFTER `ice_level`,
  ADD COLUMN `confirmed_received` TINYINT(1) NOT NULL DEFAULT 0 AFTER `item_note`;

-- Góp ý shop: liên hệ
ALTER TABLE `comments`
  ADD COLUMN `phone` VARCHAR(30) NULL AFTER `username`,
  ADD COLUMN `email` VARCHAR(255) NULL AFTER `phone`;

-- Đánh giá theo dòng đơn (mỗi order_item tối đa 1 review)
CREATE TABLE IF NOT EXISTS `product_reviews` (
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
