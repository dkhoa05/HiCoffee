<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Lấy dữ liệu từ form
    $userName = isset($_POST['userName']) ? trim($_POST['userName']) : null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : null;
    $date = date('Y-m-d H:i:s'); // Định dạng ngày giờ chuẩn

    // Kiểm tra đầu vào
    if (empty($userName) || empty($message)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
        exit;
    }

    // Xử lý các ký tự đặc biệt để tránh lỗi
    $userName = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Tạo nội dung ghi vào file
    $comment = $userName . "|||||" . $message . "|||||" . $date . "\r\n";

    // Đường dẫn đến file
    $filePath = "support.txt";

    // Ghi dữ liệu vào file
    try {
        // Kiểm tra tệp có tồn tại và có quyền ghi không
        if (!file_exists($filePath)) {
            $file = fopen($filePath, "w");
            if ($file === false) {
                throw new Exception("Không thể tạo tệp support.txt. Kiểm tra quyền thư mục.");
            }
            fclose($file);
        }

        if (!is_writable($filePath)) {
            throw new Exception("Tệp support.txt không thể ghi. Kiểm tra quyền truy cập.");
        }

        // Ghi dữ liệu
        $file = fopen($filePath, "a");
        if ($file === false) {
            throw new Exception("Không thể mở tệp support.txt để ghi dữ liệu.");
        }
        fwrite($file, $comment);
        fclose($file);

        // Thông báo thành công
        echo "<script>alert('Ý kiến của bạn đã được gửi thành công!'); window.location.href = 'support.php';</script>";
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: " . htmlspecialchars($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>
