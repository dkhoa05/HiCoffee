# Hi Coffee - Đồ án môn: Lập trình Web

Website bán đồ uống & đồ ăn nhẹ (PHP thuần, MySQL), có cửa hàng cho khách, khu vực quản trị và bảng điều khiển thống kê đơn hàng.

**Repository:** [github.com/dkhoa05/HiCoffee](https://github.com/dkhoa05/HiCoffee)

## Yêu cầu hệ thống

- PHP 7.4+ (khuyến nghị 8.x)
- MySQL / MariaDB
- Máy chủ web (ví dụ **XAMPP** trên Windows)

## Cài đặt nhanh

1. **Clone** vào thư mục web, ví dụ:
   - `C:\xampp\htdocs\WebPHP`  
   - URL mặc định: `http://localhost/WebPHP/`

2. **Cơ sở dữ liệu** — trong phpMyAdmin (tab SQL), chạy lần lượt:
   - `sql/reset_fresh.sql` — tạo database `coffeeshop`, bảng và dữ liệu tối thiểu  
   - *(tuỳ chọn)* `sql/seed_november_2024.sql` — dữ liệu demo nhiều đơn (tháng 11/2024) cho dashboard  
   - Hoặc dùng `sql/coffeeshop.sql` nếu bạn có bản dump đầy đủ tương thích

3. **Kết nối DB** — chỉnh `db.php`:
   - `$host`, `$port` (XAMPP MySQL thường `127.0.0.1` và `3306` hoặc `3307`)
   - `$db`, `$user`, `$password`

4. Mở trình duyệt: `http://localhost/WebPHP/` (đổi `WebPHP` nếu tên thư mục khác).

## Tài khoản mẫu (sau `reset_fresh.sql`)

| Vai trò | Username | Mật khẩu |
|--------|----------|----------|
| Quản trị | `admin` | `admin123` |
| Khách | `demo` | `user123` |

## Cấu trúc thư mục (chính)

| Thư mục / file | Mô tả |
|----------------|--------|
| `index.php`, `about.php`, `support.php` | Trang công khai |
| `auth/` | Đăng nhập, đăng ký, đăng xuất |
| `shop/` | Giỏ hàng, đặt hàng, sản phẩm (luồng khách) |
| `admin/` | Quản lý sản phẩm, đơn hàng, bình luận, **dashboard** |
| `includes/` | `init.php`, `header.php`, `footer.php`, `auth.php`, `paths.php`, … |
| `css/`, `js/` | Giao diện và script |
| `img/` | Ảnh sản phẩm |
| `sql/` | Schema, seed, script sinh dữ liệu demo |
| `dev/` | Tiện ích phát triển / kiểm tra (không dùng production) |

Các file ngắn ở **gốc project** (ví dụ `cart.php`, `admin_dashboard.php`) thường **chuyển hướng** sang phiên bản trong `shop/` hoặc `admin/` để giữ URL ổn định.

## Đường dẫn ứng dụng

Hàm `app_url()` trong `includes/paths.php` tự suy ra tiền tố URL từ vị trí project trong `DocumentRoot` (ví dụ `/WebPHP`). Nếu cấu hình **virtual host** trỏ thẳng vào thư mục project, base URL có thể là `/`.

## Sinh lại dữ liệu demo (tháng 11/2024)

```bash
php sql/generate_nov2024_seed.php
```

File kết quả: `sql/seed_november_2024.sql` — import sau khi đã có schema.

## Ghi chú bảo mật

- Không đưa mật khẩu database thật lên public repo; có thể dùng `db.php` mẫu + `.gitignore` cho file cấu hình local.
- Đổi mật khẩu tài khoản admin trước khi triển khai thật.

## Giấy phép & tác giả

Dự án phục vụ học tập / đồ án. Liên hệ qua GitHub: [@dkhoa05](https://github.com/dkhoa05).
