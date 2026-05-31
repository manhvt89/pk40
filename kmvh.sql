-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 31, 2026 at 09:35 AM
-- Server version: 10.3.29-MariaDB-log
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql_kmvh_sys`
--

-- --------------------------------------------------------

--
-- Table structure for table `ospos_app_config`
--

CREATE TABLE `ospos_app_config` (
  `key` varchar(50) NOT NULL,
  `value` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_attendance`
--

CREATE TABLE `ospos_attendance` (
  `attendance_id` int(11) NOT NULL,
  `attendance_uuid` varchar(36) NOT NULL DEFAULT uuid(),
  `employee_id` int(11) NOT NULL,
  `employee_uuid` varchar(36) NOT NULL,
  `check_in_time` int(11) DEFAULT 0,
  `check_out_time` int(11) DEFAULT 0,
  `created_at` int(11) DEFAULT 0,
  `fullname` varchar(50) DEFAULT '',
  `shift_date` date NOT NULL,
  `hourly_wage` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{Lương theo giờ}'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_customers`
--

CREATE TABLE `ospos_customers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `taxable` int(1) NOT NULL DEFAULT 1,
  `discount_percent` decimal(15,2) NOT NULL DEFAULT 0.00,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `points` decimal(10,2) NOT NULL DEFAULT 0.00,
  `customer_uuid` varchar(250) NOT NULL DEFAULT uuid()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_daily_total`
--

CREATE TABLE `ospos_daily_total` (
  `daily_total_id` int(10) NOT NULL,
  `created_time` int(11) DEFAULT NULL,
  `begining_amount` decimal(15,2) NOT NULL,
  `ending_amount` decimal(15,2) NOT NULL,
  `increase_amount` decimal(15,2) NOT NULL,
  `decrease_amount` decimal(15,2) DEFAULT NULL,
  `daily_total_uuid` varchar(250) NOT NULL DEFAULT uuid()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_employees`
--

CREATE TABLE `ospos_employees` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `hash_version` int(1) NOT NULL DEFAULT 2,
  `type` tinyint(1) DEFAULT 1 COMMENT '1:staff;2:CTV',
  `log` varchar(10) NOT NULL DEFAULT '0',
  `code` varchar(20) NOT NULL DEFAULT '',
  `hourly_wage` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{Lương theo giờ}',
  `total_sale` decimal(10,2) NOT NULL DEFAULT 0.00,
  `comission_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `token` varchar(250) NOT NULL DEFAULT 'UUID()'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_fields`
--

CREATE TABLE `ospos_fields` (
  `id` int(10) NOT NULL,
  `field_key` varchar(250) DEFAULT NULL,
  `permission_id` int(10) NOT NULL DEFAULT 0,
  `permission` tinyint(1) NOT NULL DEFAULT 2,
  `field_name` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_giftcards`
--

CREATE TABLE `ospos_giftcards` (
  `record_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `giftcard_id` int(11) NOT NULL,
  `giftcard_number` int(10) NOT NULL,
  `value` decimal(15,2) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `person_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_grants`
--

CREATE TABLE `ospos_grants` (
  `permission_id` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_history_ctv`
--

CREATE TABLE `ospos_history_ctv` (
  `history_ctv_id` int(11) NOT NULL,
  `ctv_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `ctv_name` varchar(255) DEFAULT NULL,
  `ctv_code` varchar(255) DEFAULT NULL,
  `ctv_phone` varchar(255) DEFAULT NULL,
  `sale_code` varchar(50) DEFAULT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `created_time` int(11) DEFAULT current_timestamp(),
  `payment_time` int(11) DEFAULT current_timestamp(),
  `payment_amount` decimal(15,2) NOT NULL,
  `comission_amount` decimal(15,2) NOT NULL,
  `comission_rate` decimal(5,2) NOT NULL,
  `history_ctv_uuid` varchar(50) NOT NULL DEFAULT uuid(),
  `status` tinyint(1) DEFAULT 0 COMMENT '{0: moi tao; 1: Yeu cau thanh toan, 2: phê duyệt; 3: đã thanh toán }'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_history_points`
--

CREATE TABLE `ospos_history_points` (
  `id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL DEFAULT 0,
  `sale_id` int(10) NOT NULL DEFAULT 0,
  `sale_uuid` varchar(250) NOT NULL DEFAULT '0',
  `created_date` int(11) NOT NULL DEFAULT 0,
  `point` decimal(10,2) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_history_reminder`
--

CREATE TABLE `ospos_history_reminder` (
  `history_reminder_id` int(11) NOT NULL,
  `employeer_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `reminder_id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT '''',
  `status` tinyint(1) DEFAULT 0 COMMENT '{0: chua lien lac duoc; 1: sai so dien thoai; 2: da hen; 3: chua sap xep dc}',
  `created_time` int(11) DEFAULT current_timestamp(),
  `history_reminder_uuid` varchar(50) NOT NULL DEFAULT uuid()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_inc1`
--

CREATE TABLE `ospos_inc1` (
  `inc1_id` int(11) NOT NULL,
  `inc1_uuid` varchar(36) NOT NULL DEFAULT uuid(),
  `oinc_id` int(11) NOT NULL DEFAULT 0,
  `line_num` int(4) NOT NULL DEFAULT 0,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `item_name` varchar(50) NOT NULL DEFAULT '',
  `item_number` varchar(50) NOT NULL DEFAULT '',
  `item_category` varchar(50) NOT NULL DEFAULT '',
  `is_serialized` tinyint(1) NOT NULL DEFAULT 0,
  `whs_code` int(11) NOT NULL DEFAULT 0,
  `counted_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `in_whs_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `difference_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_inventory`
--

CREATE TABLE `ospos_inventory` (
  `trans_id` int(11) NOT NULL,
  `trans_items` int(11) NOT NULL DEFAULT 0,
  `trans_user` int(11) NOT NULL DEFAULT 0,
  `trans_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `trans_comment` text NOT NULL,
  `trans_location` int(11) NOT NULL,
  `trans_inventory` decimal(15,3) NOT NULL DEFAULT 0.000,
  `trans_uom_code` varchar(10) NOT NULL DEFAULT '' COMMENT '{Tên đơn vị lưu kho}',
  `trans_uom_name` varchar(10) NOT NULL DEFAULT '' COMMENT '{Tên đơn vị lưu kho}',
  `trans_item_number` varchar(50) NOT NULL DEFAULT '' COMMENT '{Mã SP}',
  `trans_item_name` varchar(50) NOT NULL DEFAULT '' COMMENT '{Tên SP}',
  `trans_doc_entry` varchar(15) NOT NULL DEFAULT '' COMMENT '{Mã tài liệu}',
  `trans_type` varchar(1) NOT NULL DEFAULT 'D' COMMENT '{O,F,C,D,R,S}',
  `trans_doc_num` varchar(15) NOT NULL DEFAULT '' COMMENT '{Mã tài liệu}',
  `trans_doc_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_items`
--

CREATE TABLE `ospos_items` (
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `item_number` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `reorder_level` decimal(15,3) NOT NULL DEFAULT 0.000,
  `receiving_quantity` decimal(15,3) NOT NULL DEFAULT 1.000,
  `item_id` int(10) NOT NULL,
  `pic_id` int(10) DEFAULT NULL,
  `allow_alt_description` tinyint(1) NOT NULL,
  `is_serialized` tinyint(1) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `custom1` varchar(25) NOT NULL,
  `custom2` varchar(25) NOT NULL,
  `custom3` varchar(25) NOT NULL,
  `custom4` varchar(25) NOT NULL,
  `custom5` varchar(25) NOT NULL,
  `custom6` varchar(25) NOT NULL,
  `custom7` varchar(25) NOT NULL,
  `custom8` varchar(25) NOT NULL,
  `custom9` varchar(25) NOT NULL,
  `custom10` varchar(25) NOT NULL,
  `standard_amount` decimal(15,3) NOT NULL DEFAULT 0.000,
  `item_number_new` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: mới tạo; 1 đã đồng bộ; 3 edited; ',
  `item_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `doc_entry` int(11) NOT NULL DEFAULT 0,
  `data_source` varchar(1) NOT NULL DEFAULT 'N' COMMENT '{N: No; Y: yes}',
  `man_btch_num` varchar(1) NOT NULL DEFAULT 'N' COMMENT '{N: No; Y: yes}',
  `leadtime` int(3) NOT NULL DEFAULT 0,
  `uom_group_id` int(11) NOT NULL DEFAULT 0,
  `serial` varchar(15) NOT NULL DEFAULT '' COMMENT '{Số serial SP nếu có}',
  `shipping_type` int(11) NOT NULL DEFAULT 0,
  `inventory_location` varchar(10) NOT NULL DEFAULT 'T' COMMENT '{vị trí lưu kho}',
  `inventory_weigth_per_unit` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Quy cách}',
  `inventory_uom_code` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Mã Đơn vị lưu kho}',
  `inventory_uom_name` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Tên Đơn vị lưu kho}',
  `set_default_warehouse_id` int(11) NOT NULL DEFAULT 0,
  `sale_packing_weigth` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `sale_packing_volume` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `sale_packing_width` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `sale_packing_height` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `sale_packing_length` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `sale_quality_per_packge` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Item trên mỗi goi/ Quy cách}',
  `sale_item_per_sale_unit` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Item trên mỗi goi/ Quy cách}',
  `sale_uom_code` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Mã Đơn vị khi bán hang}',
  `sale_uom_name` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Tên Đơn vị khi bán hang}',
  `purchase_packing_weigth` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `purchase_packing_volume` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `purchase_packing_width` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `purchase_packing_height` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `purchase_packing_length` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `purchase_quality_per_packge` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Item trên mỗi goi/ Quy cách}',
  `purchase_packing_uom_name` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Tên đơn vị đóng gói}',
  `purchase_item_per_purchase_unit` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '{}',
  `purchase_uom_code` varchar(10) NOT NULL DEFAULT 'CAI' COMMENT '{Mã Đơn vị khi mua hang}',
  `purchase_uom_name` varchar(10) NOT NULL DEFAULT 'Cái' COMMENT '{Tên Đơn vị khi mua hang}',
  `catalogue_no` varchar(25) NOT NULL DEFAULT '' COMMENT '{Mã nhà SX}',
  `item_group` varchar(25) NOT NULL DEFAULT '' COMMENT '{Nhóm các mặt hàng}',
  `type` varchar(1) NOT NULL DEFAULT 'D' COMMENT '{Loại sản phẩm gồm có: Theo quy định công ty T: Tròng Kính; G: Gọng kính; M: Kính mát; H: Hộp kính; A: Áp tròng V: Vật tư; }',
  `manufactory` varchar(15) NOT NULL DEFAULT 'Việt Nam' COMMENT '{Nhà SX}',
  `brand` varchar(15) NOT NULL DEFAULT 'Việt Nam' COMMENT '{Nhãn hiệu}',
  `country` varchar(15) NOT NULL DEFAULT 'Việt Nam' COMMENT '{Xuất xứ}',
  `kind` varchar(10) NOT NULL DEFAULT 'TK' COMMENT '{Phân loại SP: TK; KA; KB; }',
  `group_category` varchar(25) NOT NULL DEFAULT '' COMMENT '{Danh mục Phân nhóm}',
  `group` varchar(25) NOT NULL DEFAULT '' COMMENT '{Phân nhóm}',
  `short_name` varchar(50) NOT NULL DEFAULT '' COMMENT '{Tên gọi tắt}',
  `normal_name` varchar(50) NOT NULL DEFAULT '' COMMENT '{Tên thường gọi}',
  `code` varchar(255) DEFAULT NULL,
  `ref_item_id` int(10) NOT NULL DEFAULT 0,
  `synched_time` int(11) NOT NULL DEFAULT 0,
  `updated_time` int(11) NOT NULL DEFAULT 0,
  `created_time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_items_taxes`
--

CREATE TABLE `ospos_items_taxes` (
  `item_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_item_kits`
--

CREATE TABLE `ospos_item_kits` (
  `item_kit_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_item_kit_items`
--

CREATE TABLE `ospos_item_kit_items` (
  `item_kit_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_item_quantities`
--

CREATE TABLE `ospos_item_quantities` (
  `item_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL DEFAULT 0.000
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_messages`
--

CREATE TABLE `ospos_messages` (
  `message_id` int(11) NOT NULL,
  `to` varchar(25) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `type` tinyint(1) DEFAULT 0 COMMENT '0: gửi cảm ơn; 1: gửi nhắc khám;2 gửi sinh nhật;3 gửi giảm giá; 4 gửi sự kiện',
  `employee_id` int(11) DEFAULT NULL,
  `name` varchar(25) DEFAULT '',
  `created_date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_modules`
--

CREATE TABLE `ospos_modules` (
  `name_lang_key` varchar(255) NOT NULL,
  `desc_lang_key` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `module_key` varchar(255) NOT NULL,
  `id` int(11) NOT NULL,
  `code` varchar(250) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  `module_uuid` varchar(250) NOT NULL DEFAULT uuid()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_oincs`
--

CREATE TABLE `ospos_oincs` (
  `oinc_uuid` varchar(36) NOT NULL DEFAULT uuid(),
  `doc_entry` varchar(25) NOT NULL DEFAULT '',
  `doc_num` varchar(25) NOT NULL DEFAULT '',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `count_at` int(11) NOT NULL DEFAULT 0,
  `status` varchar(1) NOT NULL DEFAULT 'O' COMMENT '{O: Open; W: Working ;C: Closed; P: đã update kho;}',
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '{O: ; 1: Xóa;}',
  `oinc_id` int(11) NOT NULL,
  `oinc_type` varchar(1) NOT NULL DEFAULT 'G' COMMENT '{ L: Tròng kính; F: Gọng, M: Thuốc, C: Áp tròng; D: Khác}',
  `oinc_mode` varchar(1) NOT NULL DEFAULT 'A' COMMENT '{ A: Auto; M: Manual}',
  `series` varchar(15) NOT NULL DEFAULT '' COMMENT '{ Số tài liệu nếu có}',
  `creator_id` int(11) NOT NULL DEFAULT 0 COMMENT '{ Người tạo}',
  `creator_name` varchar(50) NOT NULL DEFAULT '' COMMENT '{ Tên người tạo: Vũ Thành Mạnh}',
  `countor_id` int(11) NOT NULL DEFAULT 0 COMMENT '{ người kiểm kê}',
  `zone` varchar(25) NOT NULL DEFAULT '' COMMENT '{ Vị trí ví dụ Tủ 1, Tu2, .. =category}',
  `countor_name` varchar(50) NOT NULL DEFAULT '' COMMENT '{ Tên người kiểm kê: Vũ Thành Mạnh}',
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_people`
--

CREATE TABLE `ospos_people` (
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` int(1) DEFAULT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `person_id` int(10) NOT NULL,
  `person_uuid` varchar(36) NOT NULL DEFAULT uuid() COMMENT '{uuid}',
  `age` varchar(12) DEFAULT NULL,
  `facebook` varchar(250) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_permissions`
--

CREATE TABLE `ospos_permissions` (
  `permission_key` varchar(255) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  `location_id` int(10) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `module_key` varchar(250) NOT NULL DEFAULT '''''',
  `permissions_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `module_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `name` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_purchases`
--

CREATE TABLE `ospos_purchases` (
  `purchase_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier_id` int(10) DEFAULT 0,
  `parent_id` int(10) DEFAULT 0,
  `curent` int(4) DEFAULT 1,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `edited_employee_id` int(10) NOT NULL DEFAULT 0,
  `approved_employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT 0 COMMENT '{1: dat coc;0: thanh toan đủ - hoàn thành}',
  `code` varchar(14) DEFAULT '0',
  `completed` tinyint(1) DEFAULT 0 COMMENT '0 draf; 1 Yêu cầu sửa lại; 2 đang chờ duyệt ;3: đã phê duyệt;4 nhập hàng;',
  `name` varchar(250) DEFAULT NULL,
  `total_quantity` varchar(250) DEFAULT '0',
  `total_amount` varchar(250) DEFAULT '0',
  `purchase_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `edited_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `v` tinyint(3) NOT NULL DEFAULT 0,
  `category` varchar(50) NOT NULL DEFAULT '',
  `kind` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_purchases_items`
--

CREATE TABLE `ospos_purchases_items` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT 0,
  `purchase_id` int(11) DEFAULT 0,
  `item_number` varchar(250) DEFAULT NULL,
  `item_name` varchar(250) DEFAULT NULL,
  `item_quantity` varchar(250) DEFAULT NULL,
  `item_price` varchar(250) DEFAULT NULL,
  `item_u_price` varchar(250) DEFAULT NULL,
  `item_category` varchar(250) DEFAULT NULL,
  `line` int(3) NOT NULL,
  `type` tinyint(1) DEFAULT 0 COMMENT '0 cũ; 2: sp mới; 3: sp mới đã tồn tại barcode Đã tồn tại',
  `created_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_receivings`
--

CREATE TABLE `ospos_receivings` (
  `receiving_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `receiving_id` int(10) NOT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `reference` varchar(32) DEFAULT NULL,
  `mode` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Nhập hàng; 1: Trả hàng',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT '{Tổng đơn}',
  `paid_amount` decimal(15,2) DEFAULT NULL,
  `remain_amount` decimal(15,2) DEFAULT NULL,
  `receiving_uuid` varchar(36) NOT NULL DEFAULT uuid(),
  `code` varchar(14) NOT NULL DEFAULT '0' COMMENT '{Mã phiếu nhập hàng}',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '{0: Đã thanh toán xong; 1: Chưa hoàn thành}'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_receivings_items`
--

CREATE TABLE `ospos_receivings_items` (
  `receiving_id` int(10) NOT NULL DEFAULT 0,
  `item_id` int(10) NOT NULL DEFAULT 0,
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL,
  `quantity_purchased` decimal(15,3) NOT NULL DEFAULT 0.000,
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` decimal(15,2) NOT NULL,
  `discount_percent` decimal(15,2) NOT NULL DEFAULT 0.00,
  `item_location` int(11) NOT NULL,
  `receiving_quantity` decimal(15,3) NOT NULL DEFAULT 1.000
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_receivings_payments`
--

CREATE TABLE `ospos_receivings_payments` (
  `receivings_id` int(11) NOT NULL DEFAULT 0,
  `receivings_payments_uuid` varchar(50) NOT NULL DEFAULT uuid(),
  `note` varchar(255) NOT NULL DEFAULT '',
  `payment_type` varchar(40) NOT NULL DEFAULT '',
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_id` int(10) NOT NULL,
  `payment_kind` varchar(40) NOT NULL DEFAULT '''''' COMMENT '{Thanh Toán='''';Đặt Trước}'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_reminders`
--

CREATE TABLE `ospos_reminders` (
  `id` int(11) NOT NULL,
  `test_id` int(11) DEFAULT NULL,
  `duration_dvt` varchar(10) NOT NULL DEFAULT '',
  `reminder_uuid` varchar(36) DEFAULT uuid(),
  `address` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `tested_date` int(11) DEFAULT NULL,
  `duration` int(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0 COMMENT '0 chưa remind; 1: đã remind; 2 remind lần 2; 3 remind lần 3',
  `remain` int(1) DEFAULT NULL COMMENT 'thời gian còn lại',
  `des` varchar(255) DEFAULT '',
  `action` varchar(10) DEFAULT NULL COMMENT '{sms:done;call:done;retest:done}',
  `expired_date` int(11) DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  `phone` varchar(25) DEFAULT '0',
  `customer_id` int(11) DEFAULT 0,
  `deleted` tinyint(11) DEFAULT 0,
  `is_sms` tinyint(1) DEFAULT 0 COMMENT '0 chưa gửi; 1 đã gửi thành công'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_reports_detail_sales`
--

CREATE TABLE `ospos_reports_detail_sales` (
  `id` int(11) NOT NULL,
  `code` varchar(25) DEFAULT NULL,
  `sale_time` timestamp NULL DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `saler` varchar(50) DEFAULT NULL,
  `buyer` varchar(50) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `profit` decimal(10,2) DEFAULT NULL,
  `paid_customer` varchar(250) DEFAULT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `kind` tinyint(1) DEFAULT 0 COMMENT '0: offline; 1: online',
  `items` text DEFAULT NULL,
  `sale_type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_roles`
--

CREATE TABLE `ospos_roles` (
  `id` int(10) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `display_name` varchar(250) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `role_uuid` varchar(250) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_role_permissions`
--

CREATE TABLE `ospos_role_permissions` (
  `id` int(10) NOT NULL,
  `role_id` int(10) NOT NULL DEFAULT 0,
  `permission_id` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales`
--

CREATE TABLE `ospos_sales` (
  `sale_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `invoice_number` varchar(32) DEFAULT NULL,
  `sale_id` int(10) NOT NULL,
  `test_id` int(11) DEFAULT 0 COMMENT '{0: mua hang ko qua don; > 0 mua hang qua đơn khám}',
  `kxv_id` int(11) DEFAULT 0 COMMENT '{0: mua hang ko kxv; > 0 mua hang co kxv}',
  `doctor_id` int(11) DEFAULT 0 COMMENT '{0: mua hang ko doctor; > 0 mua hang co doctor}',
  `paid_points` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm dùng để thanh toán',
  `status` tinyint(1) DEFAULT 0 COMMENT '{1: dat coc;0: thanh toan đủ - hoàn thành}',
  `code` varchar(14) DEFAULT '0',
  `kind` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: offline; 1: online',
  `shipping_address` varchar(250) DEFAULT '' COMMENT 'khác null khi kind=1',
  `shipping_city` varchar(100) DEFAULT '' COMMENT 'khac null kind = 1',
  `shipping_method` varchar(250) DEFAULT '' COMMENT 'VNPOST,VIETEL,....',
  `shipping_phone` varchar(11) DEFAULT '',
  `source` varchar(25) DEFAULT '',
  `completed` tinyint(1) DEFAULT 0 COMMENT '0 thông tin; 1 đặt hàng;2 chuyển đến nhà vận chuyển;3 nhận hàng;4 hoàn thành',
  `shipping_address_type` tinyint(1) DEFAULT 1,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `shipping_code` varchar(50) DEFAULT '',
  `ctv_id` int(11) DEFAULT 0,
  `current` int(4) NOT NULL DEFAULT 1 COMMENT '0 là cha, đã bị thay thế; 1: hiện tại đang dùng',
  `parent_id` int(10) NOT NULL DEFAULT 0,
  `confirm` tinyint(1) NOT NULL DEFAULT 0,
  `sale_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `sync` tinyint(1) DEFAULT 0 COMMENT '{0: moi tao; 1: Đã sync vào bảng history_ctv}',
  `completed_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_items`
--

CREATE TABLE `ospos_sales_items` (
  `sale_id` int(10) NOT NULL DEFAULT 0,
  `item_id` int(10) NOT NULL DEFAULT 0,
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL DEFAULT 0,
  `quantity_purchased` decimal(15,3) NOT NULL DEFAULT 0.000,
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` decimal(15,2) NOT NULL,
  `discount_percent` decimal(15,2) NOT NULL DEFAULT 0.00,
  `item_location` int(11) NOT NULL,
  `item_name` varchar(250) DEFAULT NULL,
  `item_description` varchar(12) DEFAULT NULL,
  `item_number` varchar(12) DEFAULT NULL,
  `item_supplier_id` varchar(12) DEFAULT NULL,
  `item_category` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_items_taxes`
--

CREATE TABLE `ospos_sales_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(3) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_payments`
--

CREATE TABLE `ospos_sales_payments` (
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_id` int(10) NOT NULL,
  `payment_kind` varchar(40) NOT NULL DEFAULT '''''' COMMENT '{Thanh Toán='''';Đặt Trước}',
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_suspended`
--

CREATE TABLE `ospos_sales_suspended` (
  `sale_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `invoice_number` varchar(32) DEFAULT NULL,
  `sale_id` int(10) NOT NULL,
  `lock` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_suspended_items`
--

CREATE TABLE `ospos_sales_suspended_items` (
  `sale_id` int(10) NOT NULL DEFAULT 0,
  `item_id` int(10) NOT NULL DEFAULT 0,
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL DEFAULT 0,
  `quantity_purchased` decimal(15,3) NOT NULL DEFAULT 0.000,
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` decimal(15,2) NOT NULL,
  `discount_percent` decimal(15,2) NOT NULL DEFAULT 0.00,
  `item_location` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_suspended_items_taxes`
--

CREATE TABLE `ospos_sales_suspended_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(3) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sales_suspended_payments`
--

CREATE TABLE `ospos_sales_suspended_payments` (
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sessions`
--

CREATE TABLE `ospos_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_short_survey`
--

CREATE TABLE `ospos_short_survey` (
  `id` int(10) NOT NULL,
  `customer_id` int(10) DEFAULT NULL,
  `sale_id` int(10) DEFAULT NULL,
  `sale_uuid` varchar(255) DEFAULT NULL,
  `nvbh_id` int(10) NOT NULL DEFAULT 0,
  `kxv_id` int(10) NOT NULL DEFAULT 0,
  `created_date` int(11) NOT NULL DEFAULT 0,
  `q1` int(1) NOT NULL DEFAULT 1,
  `q2` int(1) NOT NULL DEFAULT 1,
  `q3` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_sms_sale`
--

CREATE TABLE `ospos_sms_sale` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `is_sms` tinyint(1) DEFAULT 0 COMMENT '0: chưa gửi sms;1 đã gửi sms',
  `name` varchar(250) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `saled_date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_stock_locations`
--

CREATE TABLE `ospos_stock_locations` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `location_code` varchar(5) NOT NULL,
  `location_phone` varchar(12) NOT NULL,
  `location_address` varchar(255) NOT NULL,
  `location_owner_name` varchar(255) NOT NULL,
  `location_parent_id` int(11) NOT NULL DEFAULT 0,
  `location_uuid` varchar(250) NOT NULL DEFAULT uuid()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_suppliers`
--

CREATE TABLE `ospos_suppliers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `agency_name` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `company_phone` varchar(12) NOT NULL,
  `company_address` varchar(255) NOT NULL,
  `company_code` varchar(5) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `supplier_uuid` varchar(250) NOT NULL DEFAULT uuid()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_test`
--

CREATE TABLE `ospos_test` (
  `test_id` int(11) NOT NULL,
  `employeer_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `code` varchar(12) DEFAULT NULL,
  `right_e` varchar(255) DEFAULT NULL,
  `left_e` varchar(255) DEFAULT NULL,
  `toltal` varchar(255) DEFAULT '''''',
  `lens_type` varchar(255) DEFAULT NULL,
  `contact_lens_type` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT '''''',
  `test_time` int(11) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `duration` int(1) DEFAULT 6,
  `reminder` tinyint(1) DEFAULT 1 COMMENT 'nhắc tái khám 1; không nhắc 0',
  `expired_date` int(11) DEFAULT 0,
  `test_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `old_toltal` varchar(250) NOT NULL DEFAULT '',
  `left_e_old` varchar(250) NOT NULL DEFAULT '',
  `right_e_old` varchar(250) NOT NULL DEFAULT '',
  `l_va_o` varchar(250) NOT NULL DEFAULT '',
  `r_va_o` varchar(250) NOT NULL DEFAULT '',
  `prescription` text NOT NULL DEFAULT '',
  `duration_dvt` varchar(10) NOT NULL DEFAULT '',
  `l_va_lo` text NOT NULL DEFAULT '',
  `r_va_lo` text NOT NULL DEFAULT '',
  `updated_at` int(11) DEFAULT 0,
  `step` tinyint(1) DEFAULT 2 COMMENT '1: Tiếp; 2: đang khám; 3: khám xong;',
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_total`
--

CREATE TABLE `ospos_total` (
  `total_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_id` int(10) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `created_time` int(11) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '{ 0: Thu; 1: Chi}',
  `creator_personal_id` int(10) DEFAULT NULL,
  `personal_id` int(10) DEFAULT NULL,
  `sale_id` int(10) DEFAULT NULL,
  `kind` tinyint(1) NOT NULL DEFAULT 0 COMMENT '{0: Thanh toan; 1: Dat truoc; 2: return money}',
  `daily_total_id` int(10) NOT NULL,
  `note` varchar(250) NOT NULL DEFAULT '''''',
  `payment_method` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `ospos_user_roles`
--

CREATE TABLE `ospos_user_roles` (
  `id` int(10) NOT NULL,
  `role_id` int(10) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ospos_app_config`
--
ALTER TABLE `ospos_app_config`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `ospos_attendance`
--
ALTER TABLE `ospos_attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `ospos_customers`
--
ALTER TABLE `ospos_customers`
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `ospos_daily_total`
--
ALTER TABLE `ospos_daily_total`
  ADD PRIMARY KEY (`daily_total_id`),
  ADD KEY `sale_id` (`daily_total_id`) USING BTREE;

--
-- Indexes for table `ospos_employees`
--
ALTER TABLE `ospos_employees`
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `ospos_fields`
--
ALTER TABLE `ospos_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_giftcards`
--
ALTER TABLE `ospos_giftcards`
  ADD PRIMARY KEY (`giftcard_id`),
  ADD UNIQUE KEY `giftcard_number` (`giftcard_number`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `ospos_grants`
--
ALTER TABLE `ospos_grants`
  ADD PRIMARY KEY (`permission_id`,`role_id`);

--
-- Indexes for table `ospos_history_ctv`
--
ALTER TABLE `ospos_history_ctv`
  ADD PRIMARY KEY (`history_ctv_id`);

--
-- Indexes for table `ospos_history_points`
--
ALTER TABLE `ospos_history_points`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_history_reminder`
--
ALTER TABLE `ospos_history_reminder`
  ADD PRIMARY KEY (`history_reminder_id`);

--
-- Indexes for table `ospos_inc1`
--
ALTER TABLE `ospos_inc1`
  ADD PRIMARY KEY (`inc1_id`);

--
-- Indexes for table `ospos_inventory`
--
ALTER TABLE `ospos_inventory`
  ADD PRIMARY KEY (`trans_id`),
  ADD KEY `trans_items` (`trans_items`),
  ADD KEY `trans_user` (`trans_user`),
  ADD KEY `trans_location` (`trans_location`);

--
-- Indexes for table `ospos_items`
--
ALTER TABLE `ospos_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `item_number` (`item_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `unit_cost` (`unit_price`);
ALTER TABLE `ospos_items` ADD FULLTEXT KEY `name` (`name`);

--
-- Indexes for table `ospos_items_taxes`
--
ALTER TABLE `ospos_items_taxes`
  ADD PRIMARY KEY (`item_id`,`name`,`percent`);

--
-- Indexes for table `ospos_item_kits`
--
ALTER TABLE `ospos_item_kits`
  ADD PRIMARY KEY (`item_kit_id`);

--
-- Indexes for table `ospos_item_kit_items`
--
ALTER TABLE `ospos_item_kit_items`
  ADD PRIMARY KEY (`item_kit_id`,`item_id`,`quantity`),
  ADD KEY `ospos_item_kit_items_ibfk_2` (`item_id`);

--
-- Indexes for table `ospos_item_quantities`
--
ALTER TABLE `ospos_item_quantities`
  ADD PRIMARY KEY (`item_id`,`location_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `ospos_messages`
--
ALTER TABLE `ospos_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `ospos_modules`
--
ALTER TABLE `ospos_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `desc_lang_key` (`desc_lang_key`),
  ADD UNIQUE KEY `name_lang_key` (`name_lang_key`);

--
-- Indexes for table `ospos_oincs`
--
ALTER TABLE `ospos_oincs`
  ADD PRIMARY KEY (`oinc_id`);

--
-- Indexes for table `ospos_people`
--
ALTER TABLE `ospos_people`
  ADD PRIMARY KEY (`person_id`),
  ADD KEY `first_name` (`first_name`),
  ADD KEY `phone_number` (`phone_number`);
ALTER TABLE `ospos_people` ADD FULLTEXT KEY `last_name` (`last_name`);

--
-- Indexes for table `ospos_permissions`
--
ALTER TABLE `ospos_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `ospos_permissions_ibfk_2` (`location_id`);

--
-- Indexes for table `ospos_purchases`
--
ALTER TABLE `ospos_purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_purchases_items`
--
ALTER TABLE `ospos_purchases_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_receivings`
--
ALTER TABLE `ospos_receivings`
  ADD PRIMARY KEY (`receiving_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `reference` (`reference`);

--
-- Indexes for table `ospos_receivings_items`
--
ALTER TABLE `ospos_receivings_items`
  ADD PRIMARY KEY (`receiving_id`,`item_id`,`line`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `ospos_receivings_payments`
--
ALTER TABLE `ospos_receivings_payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `ospos_reminders`
--
ALTER TABLE `ospos_reminders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_reports_detail_sales`
--
ALTER TABLE `ospos_reports_detail_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_roles`
--
ALTER TABLE `ospos_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_role_permissions`
--
ALTER TABLE `ospos_role_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_sales`
--
ALTER TABLE `ospos_sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `sale_time` (`sale_time`);

--
-- Indexes for table `ospos_sales_items`
--
ALTER TABLE `ospos_sales_items`
  ADD PRIMARY KEY (`sale_id`,`item_id`,`line`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `item_location` (`item_location`);

--
-- Indexes for table `ospos_sales_items_taxes`
--
ALTER TABLE `ospos_sales_items_taxes`
  ADD PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `ospos_sales_payments`
--
ALTER TABLE `ospos_sales_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `ospos_sales_suspended`
--
ALTER TABLE `ospos_sales_suspended`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `ospos_sales_suspended_items`
--
ALTER TABLE `ospos_sales_suspended_items`
  ADD PRIMARY KEY (`sale_id`,`item_id`,`line`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `ospos_sales_suspended_items_ibfk_3` (`item_location`);

--
-- Indexes for table `ospos_sales_suspended_items_taxes`
--
ALTER TABLE `ospos_sales_suspended_items_taxes`
  ADD PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `ospos_sales_suspended_payments`
--
ALTER TABLE `ospos_sales_suspended_payments`
  ADD PRIMARY KEY (`sale_id`,`payment_type`);

--
-- Indexes for table `ospos_sessions`
--
ALTER TABLE `ospos_sessions`
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `ospos_short_survey`
--
ALTER TABLE `ospos_short_survey`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_sms_sale`
--
ALTER TABLE `ospos_sms_sale`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ospos_stock_locations`
--
ALTER TABLE `ospos_stock_locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `ospos_suppliers`
--
ALTER TABLE `ospos_suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `ospos_test`
--
ALTER TABLE `ospos_test`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `ospos_total`
--
ALTER TABLE `ospos_total`
  ADD PRIMARY KEY (`total_id`),
  ADD KEY `ospos_total_ibfk_1` (`sale_id`),
  ADD KEY `total_id` (`total_id`) USING BTREE;

--
-- Indexes for table `ospos_user_roles`
--
ALTER TABLE `ospos_user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ospos_attendance`
--
ALTER TABLE `ospos_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_daily_total`
--
ALTER TABLE `ospos_daily_total`
  MODIFY `daily_total_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_fields`
--
ALTER TABLE `ospos_fields`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_giftcards`
--
ALTER TABLE `ospos_giftcards`
  MODIFY `giftcard_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_history_ctv`
--
ALTER TABLE `ospos_history_ctv`
  MODIFY `history_ctv_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_history_points`
--
ALTER TABLE `ospos_history_points`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_history_reminder`
--
ALTER TABLE `ospos_history_reminder`
  MODIFY `history_reminder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_inc1`
--
ALTER TABLE `ospos_inc1`
  MODIFY `inc1_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_inventory`
--
ALTER TABLE `ospos_inventory`
  MODIFY `trans_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_items`
--
ALTER TABLE `ospos_items`
  MODIFY `item_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_item_kits`
--
ALTER TABLE `ospos_item_kits`
  MODIFY `item_kit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_messages`
--
ALTER TABLE `ospos_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_modules`
--
ALTER TABLE `ospos_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_oincs`
--
ALTER TABLE `ospos_oincs`
  MODIFY `oinc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_people`
--
ALTER TABLE `ospos_people`
  MODIFY `person_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_permissions`
--
ALTER TABLE `ospos_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_purchases`
--
ALTER TABLE `ospos_purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_purchases_items`
--
ALTER TABLE `ospos_purchases_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_receivings`
--
ALTER TABLE `ospos_receivings`
  MODIFY `receiving_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_receivings_payments`
--
ALTER TABLE `ospos_receivings_payments`
  MODIFY `payment_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_reminders`
--
ALTER TABLE `ospos_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_reports_detail_sales`
--
ALTER TABLE `ospos_reports_detail_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_roles`
--
ALTER TABLE `ospos_roles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_role_permissions`
--
ALTER TABLE `ospos_role_permissions`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_sales`
--
ALTER TABLE `ospos_sales`
  MODIFY `sale_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_sales_payments`
--
ALTER TABLE `ospos_sales_payments`
  MODIFY `payment_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_sales_suspended`
--
ALTER TABLE `ospos_sales_suspended`
  MODIFY `sale_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_short_survey`
--
ALTER TABLE `ospos_short_survey`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_sms_sale`
--
ALTER TABLE `ospos_sms_sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_stock_locations`
--
ALTER TABLE `ospos_stock_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_suppliers`
--
ALTER TABLE `ospos_suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_test`
--
ALTER TABLE `ospos_test`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_total`
--
ALTER TABLE `ospos_total`
  MODIFY `total_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ospos_user_roles`
--
ALTER TABLE `ospos_user_roles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
