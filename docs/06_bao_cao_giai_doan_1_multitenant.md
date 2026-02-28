# Báo cáo Phân tích & Thiết kế Kiến trúc Multitenant (Giai đoạn 1)

## 1. Phân tích Cơ sở dữ liệu hiện tại (OSPOS / CI3)
Qua việc khảo sát file dump SQL (`sql_2024.sql` và các file liên quan) trong thư mục `database.sql`, hệ thống hiện tại đang sử dụng khoảng hơn 30 bảng với tiền tố `ospos_`. 

**Các nhóm dữ liệu chính đang có:**
- **Cấu hình & Phân quyền:** `ospos_app_config`, `ospos_modules`, `ospos_permissions`, `ospos_grants`.
- **Nhân sự & Khách hàng:** `ospos_people`, `ospos_employees`, `ospos_customers`, `ospos_suppliers`.
- **Kho & Sản phẩm:** `ospos_items`, `ospos_item_kits`, `ospos_item_quantities`, `ospos_inventory`, `ospos_stock_locations`.
- **Giao dịch (Bán / Nhập):** `ospos_sales`, `ospos_sales_items`, `ospos_sales_payments`, `ospos_receivings`, `ospos_receivings_items`.
- **Khác:** `ospos_giftcards`, `ospos_messages`, `ospos_sessions`, `ospos_sms_sale`.

**Đánh giá CSDL cũ:** 
- Mô hình này thiết kế cho một điểm bán (POS) duy nhất. Không có khái niệm Tenant (Phòng khám đa chi nhánh độc lập hoàn toàn) ở mức kiến trúc.
- Một số bảng mang tính chất của hệ thống PHP cũ (ví dụ: `ospos_sessions` lưu session DB, `ospos_modules` lưu hardcode các module) sẽ không cần thiết khi chuyển sang Laravel vì Laravel có cơ chế quản lý mạnh mẽ hơn từ core.
- Bảng `ospos_people` lưu chung thông tin cho cả nhân viên (`employees`), khách hàng (`customers`) và nhà cung cấp (`suppliers`). Cấu trúc này tuy linh hoạt nhưng khá phức tạp khi truy vấn. Khi sang Laravel, nên tách biệt rõ Model User, Customer, Supplier.

## 2. Thiết kế Cơ sở dữ liệu Multitenant (Kiến trúc Mới)
Phương án chốt để đáp ứng phần mềm cho nhiều phòng khám: **Database-per-Tenant** (Mỗi phòng khám một Database riêng biệt). 

### 2.1. Central Database (Landlord DB - Quản lý chung)
Dùng chung cho người quản trị tổng hệ thống SaaS.
- `users` (hoặc `admins`): Quản trị viên cấp cao của hệ thống PK40 SaaS.
- `tenants`: Lưu thông tin các phòng khám (id, name, data, created_at, updated_at).
- `domains`: Ánh xạ tên miền/tên miền phụ của các phòng khám (VD: tenant_id = 1, domain = 'pk1.pk40.com').
- `plans` & `subscriptions`: Quản lý các gói cước, tính phí, chu kỳ thuê của từng phòng khám.

### 2.2. Tenant Database (Dữ liệu nội bộ từng phòng khám - Thiết kế Hoàn chỉnh)
Khi đăng ký một phòng khám mới, hệ thống tự tạo một Database riêng (VD: `tenant_pk1`) để đảm bảo bảo mật và cách ly dữ liệu. Dưới đây là cấu trúc các phân hệ cốt lõi đã được tối ưu hóa cho **Chuỗi Phòng Khám / Cửa hàng kính mắt đa chi nhánh**:

**1. Cấu hình & Nhân sự (Administration & HR):**
- `locations` (Quản lý đa điểm bán/chi nhánh).
- `tenant_settings` (Cấu hình riêng của phòng khám: Logo, quy định in bill, thuế VAT, Timezone).
- `users` (Bác sĩ, Lễ tân, Thu ngân - gắn trường `location_id`), `roles`, `role_has_permissions`.
- `shift_sessions` (Quản lý Giao ca POS: Rất quan trọng cho chuỗi bán lẻ. Theo dõi nhân viên mở/đóng ca, đối chiếu số tiền mặt thực tế và số trên phần mềm chống thất thoát).

**2. Đối tác & Chăm sóc khách hàng (CRM):**
- `customers` (Lưu thông tin bệnh nhân/khách hàng, phân hạng thành viên `tier`, điểm thưởng `reward_points`).
- `suppliers` (Nhà cung cấp gọng kính, tròng kính, vật tư y tế).
- `appointments` (Đặt lịch khám: Khách hẹn trước theo khung giờ và bác sĩ cụ thể, trạng thái Pending/Confirmed/Done/Canceled).

**3. Quản lý Hàng hóa & Kho (Inventory & Logistics):**
- `items` (Danh mục sản phẩm vật lý & các loại dịch vụ như Khám đo, sửa chữa. Hỗ trợ hệ thống bảng giá đa dạng).
- `categories`, `brands` (Nhóm hàng, Thương hiệu).
- **ISO 13485 - Quản lý Lô/Date:** `item_batches` (Lưu Mã Lô `lot_number`, Ngày SX, Hạn dùng `exp_date`).
- **Tồn kho:** `inventories` (Tồn tổng hợp theo chi nhánh) & `inventory_batches` (Tồn chi tiết từng Lô tại chi nhánh).
- **Thao tác Kho:** 
  - `stock_transfers` & `stock_transfer_items` (Phiếu xuất điều chuyển nội bộ giữa các `locations`).
  - `inventory_counts` & `inventory_adjustments` (Biên bản kiểm kê và Phiếu điều chỉnh cân bằng kho định kỳ do hư hỏng, cận đát, hao hụt).

**4. Mua hàng & Cung ứng (Procurement):**
- `purchase_orders` & `purchase_order_items` (Phiếu Yêu cầu/Đặt hàng nhà cung cấp - PO, kèm chu trình duyệt).
- `receivings` & `receiving_items` (Phiếu nhập kho thực tế, link với ID của PO và bắt buộc nhập Lô/Date với hàng thiết bị).

**5. Bán hàng & Dịch vụ sau bán (Sales & Warranty):**
- `sales` & `sale_items` (Hóa đơn bán hàng/dịch vụ tại quầy, xuất kho trừ trực tiếp vào `inventory_batches` theo nguyên tắc FEFO - Hết hạn trước xuất trước).
- `warranties` (Quản lý thông tin bảo hành của từng chiếc kính bán ra/ Phiếu nhận sửa chữa kính cho khách).

**6. Tài chính & Kế toán (Finance):**
- `cash_flows` (Sổ quỹ Thu-Chi: Ghi nhận mọi giao dịch nộp tiền mặt, chuyển khoản, trả tiền điện nước mặt bằng... tại `location_id`).
- `customer_debts` (Công nợ Khách hàng: Dành cho khách mua nợ, trả góp).
- `supplier_debts` (Công nợ Nhà cung cấp: Theo dõi các khoản chưa thanh toán sau khi nhập hàng).

**7. Y tế chuyên môn (Chẩn đoán & Kê đơn - Chuẩn hóa y khoa):**
- `medical_records` (Hồ sơ khám: Khúc xạ khách quan/chủ quan, nhãn áp, tiền sử bệnh mắt).
- `prescriptions` (Đơn kính: Độ cận, viễn, loạn, trục, PD (khoảng cách đồng tử), ADD (độ cộng)).
- **Tuân thủ ISO 13485 (Audit Trail / Truy xuất nguồn gốc):** 
  - Lưu vết mọi thao tác (Tạo, Sửa, Xóa). Thêm các trường `created_by`, `updated_by` trên từng Object. 
  - **Quản lý Phiên bản (Versioning):** Hồ sơ khám và Đơn kính đã chốt không được phép ghi đè. Việc sửa đổi phải tạo Revision mới.
  - Phê duyệt bắt buộc: Lệnh cắt kính phải có chữ ký/duyệt điện tử của kỹ thuật viên (`approved_by`, `status`) để quy trách nhiệm y khoa rõ ràng.

## 3. Lựa chọn Thư viện & Công nghệ Backend
- **Framework:** Laravel 11.x (PHP 8.2+) → Tận dụng tính năng mới, hiệu năng cao và hệ sinh thái ORM mạnh mẽ.
- **Multitenancy engine:** `stancl/tenancy` v3 → Package phổ biến và mạnh nhất Laravel cho mô hình đa Database. Tự động chuyển đổi kết nối DB dựa trên URL hiện tại.
- **Xác thực & Quyền:** `spatie/laravel-permission` để quản lý Role/Permission cho nhân viên trong phòng khám.
- **Tương tác Frontend:** Xây dựng OpenAPI (RESTful) với Laravel Sanctum hoặc sử dụng kiến trúc Inertia.js / Livewire tuỳ thuộc vào đội ngũ FE.

## 4. Kết luận
Kiến trúc CSDL đã được làm rõ ranh giới giữa hệ thống quản trị chung (Central) và hệ thống của từng phòng khám (Tenant). Quá trình di chuyển (migration) dữ liệu từ `ospos_` sang cấu trúc mới có thể thực hiện được bằng các Script PHP chạy background.

**Bước tiếp theo (Giai đoạn 2):** Khởi tạo project Laravel, setup package `stancl/tenancy`, cấu trúc lại thư mục và viết file Migration tạo các bảng cho Central DB (`tenants`, `domains`).
