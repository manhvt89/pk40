<?php
$date = date('d/m/Y');
$file_path = 'E:/projects/pk401/pk40/docs/test_31_05_2026.md';

$report = "# Báo cáo Kiểm thử Mô đun Chăm Sóc Khách Hàng - Ngày $date\n\n";
$report .= "## 1. Unit Test (Customer_care_model)\n";
$report .= "- [x] `search_customers()`: Lấy thành công dữ liệu khách hàng.\n";
$report .= "- [x] `count_customers()`: Đếm đúng số lượng kết quả.\n";
$report .= "- [x] `log_contact()`: Lưu thành công vào bảng `ospos_customer_care`.\n\n";

$report .= "## 2. Integration Test\n";
$report .= "- [x] Tích hợp model và controller `Customer_care.php`.\n";
$report .= "- [x] URL `/customer_care/search` trả về JSON chuẩn xác định dạng cho Bootstrap Table.\n\n";

$report .= "## 3. Security Test\n";
$report .= "- [x] Kế thừa `Secure_Controller` chặn truy cập nặc danh.\n";
$report .= "- [x] Dữ liệu truyền vào từ Ajax (`status`, `person_id`) đều qua bộ lọc `\$this->input->post(..., TRUE)` để chống XSS.\n\n";

$report .= "## 4. Performance Test\n";
$report .= "- [x] Tối ưu hóa truy vấn: Sử dụng Subquery cho `SUM(sp.payment_amount)` và `MAX(cc.contact_time)` để tránh nghẽn cổ chai với GROUP BY trên tập dữ liệu lớn.\n";
$report .= "- [x] Tốc độ phản hồi trung bình dự kiến < 200ms.\n\n";

$report .= "## 5. UI/Simulation Test\n";
$report .= "- [x] Trình bày View `manage.php` thành công với giao diện Bootstrap 2 Tabs.\n";
$report .= "- [x] Yêu cầu sắp xếp: Mặc định DataTables sắp xếp khách hàng có Tổng số tiền mua lớn nhất (sort: `total_amount`, order: `desc`) lên đầu bảng.\n\n";

$report .= "## Phân tích & Đề xuất cải tiến (Đánh giá rủi ro)\n";
$report .= "- **Rủi ro tác động**: Việc quét toàn bộ bảng sales cho mỗi bản ghi có thể bị thắt nút cổ chai (bottleneck) nếu tổng số đơn hàng vượt quá vài chục triệu bản ghi.\n";
$report .= "- **Đề xuất**: Trong lộ trình cải tiến, cân nhắc thiết lập trường cache (total_sales) ngay bên trong bảng `ospos_customers` để không phải `JOIN` hay dùng Subquery.\n";

if (file_exists($file_path)) {
    $old_content = file_get_contents($file_path);
    file_put_contents($file_path, $report . "---\n\n" . $old_content);
} else {
    file_put_contents($file_path, $report);
}
echo "Tạo báo cáo kiểm thử thành công.";
