# Gợi ý Cải tiến Dự án (Roadmap)

Dự án hiện đang sử dụng các công nghệ vững chắc dựa trên nền tảng cũ (CodeIgniter 3, PHP ~7.4, Grunt). Để dự án dễ bảo trì, dễ mở rộng và đạt hiệu năng tốt hơn trong tương lai, dưới đây là một số đề xuất cải tiến:

## 1. Nâng cấp Framework & Ngôn ngữ
- **PHP 8.x:** Hiện PHP 7.4 đã End Of Life (EOL) và không còn nhận bản vá bảo mật. Việc nâng cấp lên PHP 8 (8.1 hoặc 8.2) sẽ gia tăng đáng kể hiệu năng và bảo mật. Lưu ý cần kiểm tra tính tương thích của CI3 với PHP 8.
- **Framework backend:** CodeIgniter 3 đã khá cũ. Xem xét lộ trình chuyển đổi dần sang:
  - **CodeIgniter 4:** Gần thiết kế nguyên thủy nhưng hiện đại hóa (dùng namespace, chuẩn PSR).
  - Hoặc xây dựng module mới bằng **Laravel** để tận dụng hệ sinh thái phong phú và ORM Eloquent mạnh mẽ.

## 2. Số hóa & Cải tiến Frontend
- **Loại bỏ Bower & Grunt:** Bower đã bị *deprecated* từ rất lâu. Grunt vẫn hoạt động nhưng tốc độ biên dịch chậm hơn các công cụ mới.
  - Chuyển toàn bộ quản lý package frontend sang `npm` hoặc `yarn`.
  - Nâng cấp công cụ build sang **Vite** hoặc **Webpack** để tối ưu hóa JS/CSS module và hỗ trợ Hot Module Replacement (HMR).
- **Phân tách Frontend / Backend (RESTful / GraphQL API):**
  - Hiện tại hệ thống đang render View thẳng từ PHP (`views/`). Dấu hiệu trong `composer.json` cho thấy đã có thư mục `app\models\api\`. 
  - Nên dần dần chuyển đổi các module sang dạng API, trả về JSON.
  - Frontend có thể xây dựng lại bằng các thư viện/framework UI hiện đại: **React, Vue.js, hoặc Svelte** mang lại trải nghiệm tương tác kiểu mượt mà (SPA).

## 3. Quản lý Cơ sở Dữ liệu
- **Database Migrations:** Việc quản lý `database.sql` thô sẽ khó khăn trong team nhiều người. Nên sử dụng cơ chế Migration của framework hoặc dùng tool như Phinx để kiểm soát sự chuyển đổi schema DB.

## 4. Tự động hoá và DevOps (CI/CD)
- Áp dụng các luồng CI/CD (GitHub Actions / GitLab CI) mỗi khi Push / Merge code:
  - Tự động chạy PHP CodeSniffer / PHPStan để kiểm soát chất lượng code.
  - Xây dựng **Unit Tests** (với PHPUnit) cho các hàm tính toán giá cả, tồn kho, doanh thu (vì đây là các module quan trọng).
  - Tự động build Docker Image đẩy lên Registry.
