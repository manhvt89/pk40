# Kế hoạch chuyển đổi dự án PK40 sang kiến trúc Laravel Multitenant

Dưới đây là kế hoạch chi tiết từng giai đoạn để chuyển đổi dự án PK40 từ CodeIgniter 3.1.2 (dựa trên OSPOS) sang kiến trúc **Laravel Multitenant** (Đa người thuê - SaaS).

Kiến trúc đề xuất: **Database-per-Tenant** (Mỗi phòng khám một Database riêng biệt) kết hợp với **1 Central Database** (chứa thông tin các phòng khám, gói cước).

## Giai đoạn 1: Phân tích và Thiết kế Kiến trúc (1 - 2 tuần)
Mục tiêu: Đánh giá mã nguồn, cơ sở dữ liệu hiện hành và chốt phương án kỹ thuật.
1. **Phân tích CSDL hiện tại (OSPOS CI3):**
   - Đánh giá các bảng dữ liệu, relationships, các ràng buộc (constraints).
   - Lược bỏ các phần không cần thiết của OSPOS không áp dụng cho phòng khám.
2. **Thiết kế CSDL Multitenant:**
   - **Central DB (Landlord):** Bảng `tenants` (thông tin phòng khám, domain), `domains`, `subscriptions` (gói cước), `central_users` (admin tổng quản lý hệ thống).
   - **Tenant DB:** Toàn bộ dữ liệu nghiệp vụ của 1 phòng khám (khách hàng, kho kính, phiếu khám, hóa đơn, nhân viên của phòng khám đó).
3. **Lựa chọn Thư viện & Công nghệ:**
   - Ngôn ngữ: PHP 8.x
   - Framework: Laravel 11.x
   - Multitenancy Package: `stancl/tenancy` (Rất mạnh cho mô hình Database-per-tenant).

## Giai đoạn 2: Khởi tạo Nền tảng & Cấu hình Tenancy (1 - 2 tuần)
Mục tiêu: Xây dựng base project có thể chạy được đa luồng tenant.
1. Khởi tạo dự án Laravel mới với Docker (Laravel Sail hoặc setup Docker Compose riêng).
2. Tích hợp và cấu hình package `stancl/tenancy`.
3. Cấu hình routing:
   - `routes/tenant.php`: Xử lý các nghiệp vụ bên trong 1 phòng khám (vd: `tenant1.pk40.com`).
   - `routes/web.php` hoặc `routes/api.php`: Xử lý trang chủ SaaS, đăng ký phòng khám mới, admin tổng.
4. Xây dựng Migrations cơ bản cho Central DB (tạo phòng khám mới, tự động tạo DB cho phòng khám đó).

## Giai đoạn 3: Tái cấu trúc Cấu trúc Dữ liệu & Data Migration (3 - 4 tuần)
Mục tiêu: Chuyển đổi và map (ánh xạ) dữ liệu cũ sang hệ thống mới.
1. Viết **Laravel Migrations** cho hệ thống Tenant (bảng `items`, `customers`, `sales`, `employees`, `permissions`,...).
2. Viết các **Eloquent Models** thay thế cho các Models cũ trong CI3, định nghĩa lại các Relationships (One-to-Many, Many-to-Many).
3. Viết **Data Migration Scripts** (PHP Console Commands):
   - Đọc dữ liệu từ DB MySQL cũ của CI3.
   - Làm sạch dữ liệu.
   - Bơm (import) dữ liệu vào các Tenant Database mới tương ứng với logic chia tách.

## Giai đoạn 4: Chuyển đổi Logic (Backend Rewrite) (4 - 8 tuần)
Mục tiêu: Viết lại các Controller và Service logic từ CI3 sang hệ sinh thái Laravel.
1. **Xác thực & Phân quyền (Auth & Roles):**
   - Thay thế hệ thống phân quyền tự build của OSPOS bằng **Spatie Permission** kết hợp với Laravel Sanctum/Passport (nếu dùng API) hoặc Laravel Breeze/Jetstream (nếu SSR).
2. **Chuyển đổi các Module cốt lõi:**
   - Module Kho hàng (Gọng, Mắt kính, Phụ kiện...).
   - Module Bán hàng (Point of Sale).
   - Module Y tế / Cận lâm sàng (Hồ sơ khám mắt, đơn kính).
   - Module Thu chi / Kế toán.
   - Module Báo cáo thống kê (Sử dụng Laravel Collections, Eloquent Aggregates thay vì raw SQL thuần).
3. **Refactor Code:** Chuyển đổi tư duy "Fat Controller" của CI3 sang MVC chuẩn hoặc mô hình Repository/Service Pattern trong Laravel giúp dễ bảo trì hơn.

## Giai đoạn 5: Tái tạo Frontend & Giao diện (3 - 6 tuần)
Mục tiêu: Áp dụng giao diện cũ vào cấu trúc mới hoặc nâng cấp UI.
- **Tùy chọn A (Nhanh nhất):** Đưa toàn bộ Views cũ (.php của CI3) sang Blade Templates của Laravel. Giữ nguyên CSS/JS cũ (Bower/Grunt).
- **Tùy chọn B (Khuyên dùng):** Xây dựng lại RESTful API từ Laravel. Sử dụng 1 framework tĩnh ở frontend như Vue.js, React, hoặc Livewire/Alpine.js để làm mới ứng dụng hoàn toàn thành SPA (Single Page Application).

## Giai đoạn 6: Kiểm thử (Testing) (2 tuần)
1. Viết Unit Tests / Feature Tests bằng **Pest** hoặc **PHPUnit** cho các luồng nghiệp vụ chính: Bán hàng, Tạo hồ sơ bệnh nhân, Nhập kho.
2. Kiểm tra tính cách ly dữ liệu giữa các Tenant (Đảm bảo user phòng khám A không thể query bằng bất cứ cách nào ra data của phòng khám B).
3. Tối ưu hóa truy vấn CSDL (N+1 query problem) do việc chuyển từ Query Builder sang ORM.

## Giai đoạn 7: Triển khai & Cấu hình Server (1 - 2 tuần)
1. Cấu hình Server / VPS:
   - Cài đặt Nginx/Apache với cấu hình Wildcard Subdomain (`*.pk40.com`) trỏ về ứng dụng Laravel.
   - Cài đặt SSL Let's Encrypt Wildcard.
2. Thiết lập CI/CD (GitHub Actions / GitLab CI) tự động deploy khi có code mới.
3. Thiết lập hệ thống sao lưu (Backup) tự động cho hàng chục/trăm Databases của các Tenants.

---

### Bảng tóm tắt tài nguyên ước tính:
* **Thời gian dự kiến:** 4 - 6 tháng (với team 2-3 người).
* **Độ khó rủi ro lớn nhất:** Đảm bảo toàn vẹn dữ liệu khi chuyển đổi (Migration phase), và kiến trúc logic routing theo Subdomain (ngăn chặn tình trạng rò rỉ session giữa các tenants).
