# Yêu cầu Xây dựng Phần mềm Quản lý Phòng Khám (PK40)

Hãy đóng vai là một chuyên gia lập trình phát triển các hệ thống quản lý chuyên nghiệp dựa trên nền tảng PHP. Tôi cần bạn xây dựng một phần mềm quản lý phòng khám (đặc biệt phù hợp cho phòng khám mắt, nha khoa, da liễu) nhằm mục đích chuyển đổi số, cải tiến công tác chuyên môn và giảm thiểu lãng phí.

## 1. Yêu cầu Công nghệ
- **Backend:** Ngôn ngữ PHP (khuyến nghị >= 7.4), sử dụng framework CodeIgniter phiên bản 3.1.2.
- **Frontend:** HTML, CSS, JavaScript cơ bản kết hợp cùng thư viện/framework UI hiện đại thiết kế thân thiện, dễ sử dụng cho các nhân viên y tế (Sử dụng NPM/Grunt để build nếu cần thiết).
- **Cơ sở dữ liệu:** MySQL / MariaDB thiết kế chuẩn hóa cho việc quản lý hàng hóa, khách hàng và giao dịch.
- **Hạ tầng triển khai:** Đóng gói bằng Docker (có file `Dockerfile`, `docker-compose.yml`) bao gồm các services: mã nguồn app, MySQL database, và Memcached.

## 2. Các Chức năng Cốt lõi
Phần mềm cần bao gồm các module và chức năng sau, dựa trên nền tảng cấu trúc quản lý điểm bán hàng mạnh mẽ:

### 2.1 Quản lý Tổng hợp, Kho hàng & Dịch vụ
- Quản lý danh mục sản phẩm (VD: gọng kính, mắt kính, hóa chất, vật tư khám bệnh).
- Có khả năng quản lý sản phẩm theo các tiêu chí chi tiết (màu sắc, thông số cận/viễn/loạn, nguyên vật liệu...).
- Quản lý nhập hàng: Tạo phiếu nhập, quản lý nhà cung cấp, hỗ trợ in hóa đơn/phiếu nhập.
- Kiểm kho: Các tính năng tạo báo cáo kiểm kho, báo cáo tồn kho chi tiết đối với mắt kính, gọng kính và các vật tư theo thời gian thực.

### 2.2 Nghiệp vụ Chuyên môn Y Tế (Khám mắt)
- Quản lý hồ sơ bệnh án: Tạo, chỉnh sửa và lưu trữ kết quả khám cho từng khách hàng (thông số thị lực, toa thuốc, tiến trình điều trị).
- Phân tách phân hệ khám bệnh với phân hệ bán hàng để dễ quản lý luồng công việc.

### 2.3 POS Bán hàng (Point of Sale)
- Giao diện bán lẻ thân thiện và tốc độ đáp ứng nhanh.
- Xử lý giao dịch bán hàng linh hoạt: Kết hợp giữa sản phẩm (gọng, mắt kính) và dịch vụ y tế (phí khám bệnh).
- Tích hợp quét mã vạch (Barcode/QR code) trong quá trình bán hàng.

### 2.4 Quản lý Nhân sự & Khách hàng
- **Nhân viên:** Phân quyền chi tiết (admin, nhân viên bán hàng, bác sĩ khám bệnh), theo dõi logs hoạt động và bảo mật thông tin.
- **Khách hàng:** Quản lý thông tin hồ sơ thành viên, lịch sử khám, lịch sử mua hàng cá nhân.
- Hỗ trợ tính năng ghi nhận đánh giá của khách hàng (Feedback/Rating).

### 2.5 Kế toán & Báo cáo (Analytics)
- Kế toán thu chi (Ghi nhận các khoản thu nhập, chi phí hoạt động trực tiếp trên hệ thống).
- Xuất các báo cáo phân tích mạnh mẽ:
  - Báo cáo doanh thu theo: Ngày, tháng, quý, năm.
  - Báo cáo chi tiết theo nhân viên (hiệu suất bán hàng).
  - Báo cáo doanh thu theo từng mặt hàng/nhóm sản phẩm.
- Khả năng xuất dữ liệu báo cáo ra các định dạng chuẩn (Excel, PDF, CSV).

## 3. Kiến trúc Phần mềm cần tuân thủ (CodeIgniter MVC)
- Mã nguồn tuân thủ các chuẩn MVC truyền thống của CodeIgniter (Model, View, Controller).
- Khuyến nghị sử dụng `composer` để quản lý các package hỗ trợ (như tạo mã Barcode, làm việc với Google API, tạo PDF).

---
*Lưu ý: Bạn hãy phát sinh các file thiết kế cơ sở dữ liệu (`database.sql`), file cấu trúc thư mục, các lớp Entity quan trọng và mô tả chi tiết logic triển khai module Bán hàng kết hợp Khám bệnh.*
