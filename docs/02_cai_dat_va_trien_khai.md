# Hướng dẫn Cài đặt & Triển khai

Dự án PK40 hỗ trợ triển khai nhanh chóng thông qua **Docker** và **Docker Compose**, giúp cô lập môi trường và giảm thiểu lỗi do phần mềm hệ thống gây ra.

## 1. Yêu cầu hệ thống
- Docker Engine
- Docker Compose
- Cổng trống: `8989` (cho web) và `3306` (cho cơ sở dữ liệu)

## 2. Các Service trong Docker
Trong file `docker-compose.yml`, các dịch vụ sau được thiết lập:
- **ospos (pk_v22_dev):** Container chứa mã nguồn ứng dụng (PHP, Web server). Hoạt động trên cổng `8989`. Map dữ liệu vào thư mục hiện tại (`/app`).
- **mysql (mysql_pk_v2):** Chạy `mariadb:10.5`. Mật khẩu root mặc định là `pointofsale`. Database được tự động khởi tạo từ file `./database/database.sql`.
- **memcached:** Dịch vụ cache hỗ trợ ứng dụng.

## 3. Các bước khởi chạy môi trường Dev
**Bước 1:** Clone / Tải mã nguồn về máy.

**Bước 2:** Di chuyển vào thư mục dự án:
```bash
cd /home/manhvt/Documents/projects/pk40
```

**Bước 3:** Chạy Docker Compose:
```bash
docker-compose up -d
```

**Bước 4:** Cài đặt dependencies (nếu cần, thực hiện trong container):
Cài đặt PHP packages:
```bash
docker exec -it pk_v22_dev composer install
```
Cài đặt Frontend packages:
```bash
npm install
npm run build (hoặc grunt)
```

**Bước 5:** Truy cập ứng dụng:
Mở trình duyệt và truy cập vào địa chỉ: `http://localhost:8989`
Thông tin kết nối CSDL trong Docker:
- Host: `mysql`
- User: `admin`
- Pass: `pointofsale`
- DB: `ospos`

## 4. Troubleshooting
- Quyền ghi file log/upload: Hãy chắc chắn thư mục `public/uploads` và `application/logs` có đủ quyền ghi cho user chạy PHP trong container (ví dụ `www-data`).
- Lỗi kết nối CSDL: Đảm bảo container MySQL đã khởi động xong trước khi truy cập ứng dụng. Mặc định `depends_on` đã được cấu hình.
