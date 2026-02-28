# Tổng quan dự án PK40

## 1. Giới thiệu chung
PK40 (PK Learn) là một phần mềm quản lý phòng khám (đặc biệt phù hợp cho phòng khám mắt, nha khoa, da liễu) nhằm mục đích chuyển đổi số, cải tiến công tác chuyên môn và giảm thiểu lãng phí.
Dự án được xây dựng dựa trên nền tảng **CodeIgniter (CI 3.1.2)** và phát triển dựa trên mã nguồn mở **OSPOS (Open Source Point of Sale)**.

## 2. Công nghệ chính
- **Ngôn ngữ:** PHP (phiên bản khuyến nghị >= 7.4)
- **Framework:** CodeIgniter 3.1.2
- **Cơ sở dữ liệu:** MySQL / MariaDB
- **Quản lý gói:** Composer (PHP), NPM / Bower (Frontend)
- **Công cụ build:** Grunt
- **Hạ tầng / Môi trường:** Docker & Docker Compose

## 3. Các chức năng chính
Hệ thống cung cấp một loạt các tính năng quản lý toàn diện bao gồm:
- **Quản lý kho hàng:** Quản lý gọng kính, mắt kính và các sản phẩm khác.
- **Báo cáo và Kiểm kho:** Xuất báo cáo tồn kho chuyên sâu cho mắt kính, gọng kính.
- **Quản lý nhập hàng:** Tạo phiếu nhập hàng linh hoạt.
- **Nghiệp vụ chuyên môn:** Quản lý hồ sơ khám mắt.
- **Bán hàng (POS):** Xử lý giao dịch bán hàng, thanh toán.
- **Kế toán:** Quản lý thu chi.
- **Báo cáo thống kê:**
  - Báo cáo doanh thu theo: ngày, tháng, quý, năm.
  - Báo cáo doanh thu theo nhân viên bán hàng.
  - Báo cáo doanh thu theo từng sản phẩm cụ thể.
- **Quản lý khách hàng:** Hồ sơ thành viên, theo dõi đánh giá của khách hàng.
