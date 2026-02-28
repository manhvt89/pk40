# Cấu trúc thư mục

Dự án PK40 tuân theo mô hình **MVC (Model - View - Controller)** tiêu chuẩn của CodeIgniter 3. 

## Cấu trúc tổng quát
```text
pk40/
├── application/       # Chứa toàn bộ logic backend (Controllers, Models, Views)
│   ├── config/        # Cấu hình CSDL, autoload, routes, v.v.
│   ├── controllers/   # Tiếp nhận Request, xử lý logic điều hướng
│   ├── core/          # Kế thừa mở rộng các core class của CI (MY_Controller, ...)
│   ├── helpers/       # Các hàm tiện ích hỗ trợ nhanh (functions)
│   ├── language/      # File ngôn ngữ (Hỗ trợ đa ngôn ngữ)
│   ├── libraries/     # Các thư viện PHP tùy chỉnh hoặc tích hợp ngoài
│   ├── models/        # Tương tác với CSDL (71 files model)
│   ├── third_party/   # Chứa thư viện bên thứ 3 (không dùng composer)
│   └── views/         # Giao diện người dùng (HTML/PHP mixed) (Hơn 200 files)
├── public/            # Thư mục gốc để web server trỏ tới (Document Root)
│   ├── uploads/       # Chứa file upload của người dùng/hệ thống
│   └── (css, js, images...) # Tài nguyên tĩnh tự build
├── database/          # Chứa script khởi tạo CSDL (.sql) và cấu hình liên quan
├── vendor/            # Các thư viện được quản lý bởi Composer
├── docker-compose.yml # File cấu hình Docker
├── Dockerfile*        # Files build Docker image cho các môi trường (dev, test)
├── composer.json      # File định nghĩa thư viện PHP
├── package.json       # File định nghĩa NodeJS (tooling: Grunt)
├── Gruntfile.js       # File cấu hình Grunt tự động hóa build frontend css/js
└── README.md          # Thông tin tra cứu cơ bản của dự án
```

## Luồng dữ liệu cơ bản (MVC trong CI3)
1. **Request** được gửi tới `index.php`.
2. Hệ thống kiểm tra trong `application/config/routes.php` để tìm **Controller** tương ứng trong `application/controllers/`.
3. **Controller** gọi dữ liệu từ **Model** (`application/models/`) hoặc sử dụng các **Library / Helper**.
4. **Model** thực thi câu lệnh SQL/Builder lấy dữ liệu từ MariaDB/MySQL.
5. Sau khi có kết quả, **Controller** truyền data vào **View** (`application/views/`) để hiển thị.
