-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 19, 2022 at 02:58 PM
-- Server version: 10.3.37-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


-- 03/07/2023
DROP TABLE IF EXISTS `ospos_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_purchases` (
  `purchase_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier_id` int(10) DEFAULT 0,
  `parent_id` int(10) DEFAULT 0,
  `curent` int(4) DEFAULT 1,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `edited_employee_id` int(10) NOT NULL DEFAULT 0,
  `approved_employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) DEFAULT 0 COMMENT '{1: dat coc;0: thanh toan đủ - hoàn thành}',
  `code` varchar(14) DEFAULT '0',
  `completed` tinyint(1) DEFAULT 0 COMMENT '0 draf; 1 Yêu cầu sửa lại; 2 đang chờ duyệt ;3: đã phê duyệt;4 nhập hàng;',
  `name` varchar(250) DEFAULT NULL,
  `total_quantity` varchar(250) DEFAULT '0',
  `total_amount` varchar(250) DEFAULT '0',
  `purchase_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `edited_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_purchases`
--

LOCK TABLES `ospos_purchases` WRITE;
/*!40000 ALTER TABLE `ospos_purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_purchases_items`
--

DROP TABLE IF EXISTS `ospos_purchases_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_purchases_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
-- 01/07/2023 (chưa update lên dr-cuong_dat)
ALTER TABLE `ospos_sales` ADD `created_at` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `ospos_sales` ADD `updated_at` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `ospos_total` ADD `payment_method` tinyint(1) NOT NULL DEFAULT 0; -- 0: 0 tien mat; 1: Ngan hang; 2: point, 3 ;


ALTER TABLE `ospos_sales_payments` ADD `payment_time` timestamp NOT NULL DEFAULT current_timestamp();
UPDATE `ospos_sales_payments` as `p` SET `payment_time` = (SELECT `sale_time` FROM `ospos_sales` as `s` where `s`.`sale_id` = `p`.`sale_id`);

UPDATE `ospos_sales_payments` as `p` SET `payment_time` = (SELECT FROM_UNIXTIME(`created_time`) FROM (select * from ospos_total as t1 where t1.type =0 AND t1.payment_id >0  group by t1.payment_id,t1.sale_id, t1.creator_personal_id) as t WHERE `t`.`payment_id` = `p`.`payment_id`);


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
DROP TABLE IF EXISTS `ospos_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_test` (
  `test_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `prescription` text NOT NULL,
  `r_va_o` varchar(50) NOT NULL,
  `l_va_o` varchar(50) NOT NULL,
  `right_e_old` varchar(250) NOT NULL,
  `left_e_old` varchar(250) NOT NULL,
  `old_toltal` varchar(50) NOT NULL,
  `test_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  PRIMARY KEY (`test_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3615 DEFAULT CHARSET=utf8;
--
-- Database: `namh_sys`
--

-- Create By ManhVT Support Update tol version to new version

-- ADD fields to ospos_customers
ALTER TABLE `ospos_customers` ADD `points` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `password`; 

ALTER TABLE `ospos_customers` ADD `customer_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `points`;
ALTER TABLE `ospos_items` ADD `category_code` VARCHAR(250) NOT NULL DEFAULT '' AFTER `code`;  
ALTER TABLE `ospos_items` ADD `ref_item_id` VARCHAR(50) NOT NULL DEFAULT '' AFTER `category_code`;  

ALTER TABLE `ospos_sales` ADD `current` int(4) NOT NULL DEFAULT 1 COMMENT '0 là cha, đã bị thay thế; 1: hiện tại đang dùng' AFTER `sale_uuid`; 
ALTER TABLE `ospos_sales` ADD `parent_id` int(10) NOT NULL DEFAULT 0 AFTER `sale_uuid`; 

ALTER TABLE `ospos_test` ADD `reason` text CHARACTER SET utf8 NOT NULL AFTER `test_uuid`; 
ALTER TABLE `ospos_test` ADD `step` tinyint(1) DEFAULT 2 COMMENT '1: Tiếp; 2: đang khám; 3: khám xong;' AFTER `test_uuid`; 
ALTER TABLE `ospos_test` ADD `updated_at` int(11) DEFAULT 0 AFTER `test_uuid`; 

-- ADD fields to ospos_daily_total
ALTER TABLE `ospos_daily_total` ADD `daily_total_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `decrease_amount`; 

ALTER TABLE `ospos_app_config` MODIFY `value` TEXT;

-- ADD table ospos_fields
DROP TABLE IF EXISTS `ospos_fields`;
CREATE TABLE `ospos_fields` (
  `id` int(10) NOT NULL,
  `field_key` varchar(250) CHARACTER SET utf8,
  `permission_id` int(10) NOT NULL DEFAULT 0,
  `permission` tinyint(1) NOT NULL DEFAULT 2,
  `field_name` varchar(250) CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `ospos_fields` ADD PRIMARY KEY( `id`); 
ALTER TABLE `ospos_fields` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT; 

-- ADD ospos_grants
ALTER TABLE `ospos_grants` CHANGE `person_id` `role_id` INT(11) NOT NULL;


--UPDATE ospos_grants SET role_id = 2 WHERE role_id = 7713;
--UPDATE ospos_grants SET role_id = 3 WHERE role_id = 8794;
--UPDATE ospos_grants SET role_id = 4 WHERE role_id = 8811;
--UPDATE ospos_grants SET role_id = 5 WHERE role_id = 8817;
--UPDATE ospos_grants SET role_id = 6 WHERE role_id = 8826;

-- ADD table ospos_history_points

DROP TABLE IF EXISTS `ospos_history_points`;

CREATE TABLE `ospos_history_points` (
  `id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL DEFAULT 0,
  `sale_id` int(10) NOT NULL DEFAULT 0,
  `sale_uuid` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `created_date` int(11) NOT NULL DEFAULT 0,
  `point` decimal(10,2) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `note` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `ospos_history_points` ADD PRIMARY KEY( `id`); 
ALTER TABLE `ospos_history_points` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT;

-- ADD field to Items
ALTER TABLE `ospos_items` ADD `code` VARCHAR(255) DEFAULT NULL AFTER `status`;
UPDATE ospos_items SET code = item_number;
ALTER TABLE `ospos_items` ADD `item_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `status`; 

-- ADD Modules
ALTER TABLE `ospos_modules` DROP PRIMARY KEY;
ALTER TABLE `ospos_modules` ADD `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `name_lang_key`, ADD PRIMARY KEY (`id`);
ALTER TABLE `ospos_modules` ADD `code` VARCHAR(250) DEFAULT NULL AFTER `id`, ADD `name` VARCHAR(250) DEFAULT NULL AFTER `code`;
ALTER TABLE `ospos_modules` ADD `module_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `name`; 
ALTER TABLE `ospos_modules` ADD `created_at` INT(11) NOT NULL DEFAULT '0' AFTER `name`, ADD `updated_at` INT(11) NOT NULL DEFAULT '0' AFTER `created_at`, ADD `deleted_at` INT(11) NOT NULL DEFAULT '0' AFTER `updated_at`;
ALTER TABLE `ospos_modules` CHANGE `module_id` `module_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

--ADD ospos_permissions
ALTER TABLE `ospos_permissions` DROP PRIMARY KEY;
ALTER TABLE `ospos_permissions` ADD `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `location_id`, ADD PRIMARY KEY (`id`);
ALTER TABLE `ospos_permissions` ADD `module_key` VARCHAR(250) NOT NULL DEFAULT '\'\'' AFTER `id`;
ALTER TABLE `ospos_permissions` CHANGE `permission_id` `permission_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `ospos_permissions` ADD `permissions_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `module_key`; 

ALTER TABLE `ospos_permissions` ADD `name` VARCHAR(250) DEFAULT NULL AFTER `module_key`;

UPDATE ospos_permissions SET module_key = module_id;
UPDATE ospos_grants SET permission_id = (select p.id from ospos_permissions p where p.permission_key =  permission_id);
-- Change ospos_sessions  to extend session
ALTER TABLE `ospos_sessions` CHANGE `data` `data` LONGBLOB NOT NULL; 

-- ADD table ospos_roles
DROP TABLE IF EXISTS `ospos_roles`;
CREATE TABLE `ospos_roles` (
  `id` int(10) NOT NULL,
  `name` varchar(250) CHARACTER SET utf8,
  `display_name` varchar(250) CHARACTER SET utf8,
  `code` varchar(20) CHARACTER SET utf8,
  `role_uuid` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,         
  `description` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `ospos_roles` ADD PRIMARY KEY( `id`); 
ALTER TABLE `ospos_roles` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT; 


-- ADD table ospos_role_permissions
DROP TABLE IF EXISTS `ospos_role_permissions`;
CREATE TABLE `ospos_role_permissions` (
   `id` int(10) NOT NULL,
   `role_id` int(10) NOT NULL DEFAULT 0,
   `permission_id` int(10) NOT NULL DEFAULT 0
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `ospos_role_permissions` ADD PRIMARY KEY( `id`); 
ALTER TABLE `ospos_role_permissions` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT; 


-- ADD fields to ospos_sales
ALTER TABLE `ospos_sales` ADD `confirm` TINYINT(1) NOT NULL DEFAULT 0 AFTER `ctv_id`; 
ALTER TABLE `ospos_sales` ADD `sale_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `confirm`; 

ALTER TABLE `ospos_sales` ADD `kxv_id` int(11) DEFAULT '0' COMMENT '{0: mua hang ko kxv; > 0 mua hang co kxv}' AFTER `test_id`;
ALTER TABLE `ospos_sales` ADD `doctor_id` int(11) DEFAULT '0' COMMENT '{0: mua hang ko doctor; > 0 mua hang co doctor}' AFTER `kxv_id`;
ALTER TABLE `ospos_sales` ADD `paid_points` DECIMAL(10,2) NOT NULL DEFAULT '0' COMMENT 'Điểm dùng để thanh toán' AFTER `doctor_id`;



--Add fields ospos_sales_items
ALTER TABLE `ospos_sales_items` ADD `item_name` VARCHAR(250) DEFAULT NULL AFTER `item_location`;
ALTER TABLE `ospos_sales_items` ADD `item_category` VARCHAR(250) DEFAULT NULL AFTER `item_name`;
ALTER TABLE `ospos_sales_items` ADD `item_supplier_id` VARCHAR(12) DEFAULT NULL AFTER `item_name`;
ALTER TABLE `ospos_sales_items` ADD `item_number` VARCHAR(12) DEFAULT NULL AFTER `item_name`;
ALTER TABLE `ospos_sales_items` ADD `item_description` VARCHAR(12) DEFAULT NULL AFTER `item_name`;

-- Add table ospos_short_survey
DROP TABLE IF EXISTS `ospos_short_survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_short_survey` (
  `id` int(10) NOT NULL,
  `customer_id` int(10),
  `sale_id` int(10),
  `sale_uuid` varchar(255) DEFAULT NULL,
  `nvbh_id` int(10) NOT NULL DEFAULT '0',
  `kxv_id` int(10) NOT NULL DEFAULT '0',
  `created_date` int(11) NOT NULL DEFAULT '0',
  `q1` int(1) NOT NULL DEFAULT '1',
  `q2` int(1) NOT NULL DEFAULT '1',
  `q3` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `ospos_short_survey` ADD PRIMARY KEY( `id`); 
ALTER TABLE `ospos_short_survey` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT; 
/*!40101 SET character_set_client = @saved_cs_client */;

-- add field ospos_stock_locations
ALTER TABLE `ospos_stock_locations` ADD `location_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `location_parent_id`; 

-- add field  ospos_suppliers
ALTER TABLE `ospos_suppliers` ADD `supplier_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `supplier_id`; 

-- ospos_test
ALTER TABLE `ospos_test` ADD `test_uuid` VARCHAR(250) NOT NULL DEFAULT UUID() AFTER `expired_date`; 
--
-- ADD ospos_user_roles
DROP TABLE IF EXISTS `ospos_user_roles`;
CREATE TABLE `ospos_user_roles` (
  `id` int(10) NOT NULL,
  `role_id` int(10) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
ALTER TABLE `ospos_user_roles` ADD PRIMARY KEY( `id`); 
ALTER TABLE `ospos_user_roles` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT;


INSERT INTO `ospos_user_roles` (`role_id`, `user_id`) VALUES
('1', '1');

INSERT INTO `ospos_roles` (`name`, `display_name`,`code`, `created_at`, `updated_at`, `deleted_at`,`status`,`description`) VALUES
('admin', 'admin', 'ADM', 1671459570, 1671459570, 1671459570,1,'Admin Role'),
('Bán hàng', 'Bán hàng', 'SALE', 1671459570, 1671459570, 1671459570,1,'Bán hàng'),
('Thu ngân', 'Thu ngân', 'thungan', 1671459570, 1671459570, 1671459570,1,'Thu ngân'),
('Thủ kho', 'Thủ kho', 'thukho', 1671459570, 1671459570, 1671459570,1,'Thủ kho'),
('Quản lý', 'Quản lý', 'MGR', 1671459570, 1671459570, 1671459570,1,'Quản lý của hàng'),
('Kỹ thuật', 'Kỹ thuật', 'KTV', 1671459570, 1671459570, 1671459570,1,'Kỹ thuật viên'),
('Khúc xạ', 'Khúc xạ', 'KXV', 1671459570, 1671459570, 1671459570,1,'Khúc xạ viên'),
('Nhà đầu tư', 'Nhà đầu tư', 'NDT', 1671459570, 1671459570, 1671459570,1,'Nhà đầu tư');

DROP TABLE IF EXISTS `ospos_fields`;
CREATE TABLE `ospos_fields` (
  `id` int(10) NOT NULL,
  `field_key` varchar(250) CHARACTER SET utf8,
  `permission_id` int(10) NOT NULL DEFAULT 0,
  `permission` tinyint(1) NOT NULL DEFAULT 2,
  `field_name` varchar(250) CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
ALTER TABLE `ospos_fields` ADD PRIMARY KEY( `id`); 
ALTER TABLE `ospos_fields` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT; 


--------------------------------- Update chuan Phan Quyen -----------------------
--
-- Table structure for table `ospos_modules`
--

DROP TABLE IF EXISTS `ospos_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_modules` (
  `name_lang_key` varchar(255) NOT NULL,
  `desc_lang_key` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `module_key` varchar(255) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(250) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  `module_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `desc_lang_key` (`desc_lang_key`),
  UNIQUE KEY `name_lang_key` (`name_lang_key`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_modules`
--

LOCK TABLES `ospos_modules` WRITE;
/*!40000 ALTER TABLE `ospos_modules` DISABLE KEYS */;
INSERT INTO `ospos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_key`, `id`, `code`, `name`, `created_at`, `updated_at`, `deleted_at`, `module_uuid`) VALUES
('module_account', 'module_account_desc', 120, 'account', 1, NULL, 'Kế toán', 0, 0, 0, 'aa3922b7-5819-11ed-b65f-040300000000'),
('module_config', 'module_config_desc', 130, 'config', 2, NULL, 'Thiết lập', 0, 0, 0, 'aa392523-5819-11ed-b65f-040300000000'),
('module_customers', 'module_customers_desc', 10, 'customers', 3, NULL, 'Khách hàng', 0, 0, 0, 'aa3926ef-5819-11ed-b65f-040300000000'),
('module_customer_info', 'module_customer_info', 121, 'customer_info', 4, NULL, 'Bảo hành', 0, 0, 0, 'aa3927cc-5819-11ed-b65f-040300000000'),
('module_employees', 'module_employees_desc', 80, 'employees', 5, NULL, 'Nhân viên', 0, 0, 0, 'aa3928f4-5819-11ed-b65f-040300000000'),
('module_giftcards', 'module_giftcards_desc', 90, 'giftcards', 6, NULL, 'Quà tặng', 0, 0, 0, 'aa392a99-5819-11ed-b65f-040300000000'),
('module_items', 'module_items_desc', 20, 'items', 7, NULL, 'Sản phẩm', 0, 0, 0, 'aa392b4c-5819-11ed-b65f-040300000000'),
('module_item_kits', 'module_item_kits_desc', 30, 'item_kits', 8, NULL, 'Nhóm sản phẩm', 0, 0, 0, 'aa392bf1-5819-11ed-b65f-040300000000'),
('module_messages', 'module_messages_desc', 100, 'messages', 9, NULL, 'Tin nhắn', 0, 0, 0, 'aa392ca6-5819-11ed-b65f-040300000000'),
('module_order', 'module_order_desc', 150, 'order', 10, NULL, NULL, 0, 0, 0, 'aa392d4a-5819-11ed-b65f-040300000000'),
('module_receivings', 'module_receivings_desc', 60, 'receivings', 11, NULL, 'Nhập hàng', 0, 0, 0, 'aa392df2-5819-11ed-b65f-040300000000'),
('module_reminders', 'module_reminders_desc', 140, 'reminders', 12, NULL, NULL, 0, 0, 0, 'aa392ea1-5819-11ed-b65f-040300000000'),
('module_reports', 'module_reports_desc', 50, 'reports', 13, NULL, 'Báo cáo', 0, 0, 0, 'aa392f58-5819-11ed-b65f-040300000000'),
('module_sales', 'module_sales_desc', 70, 'sales', 14, NULL, 'Bán hàng', 0, 0, 0, 'aa393000-5819-11ed-b65f-040300000000'),
('module_suppliers', 'module_suppliers_desc', 40, 'suppliers', 15, NULL, 'Nhà cung cấp', 0, 0, 0, 'aa3930d4-5819-11ed-b65f-040300000000'),
('module_test', 'module_test_desc', 110, 'test', 16, NULL, 'Đo mắt', 0, 0, 0, 'aa393173-5819-11ed-b65f-040300000000'),
('roles', 'roles', 10, 'roles', 17, 'roles', 'Phân quyền', 1667225850, 0, 0, 'c14fb1fe-5926-11ed-b3d8-040300000000'),
('barcodes', 'barcodes', 10, 'barcodes', 18, 'barcodes', 'Quản lý barcode', 1669912730, 0, 0, 'a29e2092-7196-11ed-8174-005056847d3e');
/*!40000 ALTER TABLE `ospos_modules` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `ospos_user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_user_roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_user_roles`
--

LOCK TABLES `ospos_user_roles` WRITE;
/*!40000 ALTER TABLE `ospos_user_roles` DISABLE KEYS */;
INSERT INTO `ospos_user_roles` (`id`, `role_id`, `user_id`) VALUES
(1, 1, 1);
/*!40000 ALTER TABLE `ospos_user_roles` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `ospos_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `display_name` varchar(250) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `role_uuid` varchar(250) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_roles`
--

LOCK TABLES `ospos_roles` WRITE;
/*!40000 ALTER TABLE `ospos_roles` DISABLE KEYS */;
INSERT INTO `ospos_roles` (`id`, `name`, `display_name`, `code`, `role_uuid`, `created_at`, `updated_at`, `deleted_at`, `status`, `description`) VALUES
(1, 'admin', 'admin', 'ADM', '7b498149-5877-11ed-a953-040300000000', 0, 0, 0, 0, '1'),
(2, 'Bán hàng', 'Bán hàng', 'SALE', '7b4984a5-5877-11ed-a953-040300000000', 0, 0, 0, 0, '1'),
(3, 'Thu ngân', 'Thu ngân', 'thungan', '7b4985c8-5877-11ed-a953-040300000000', 0, 0, 0, 0, '1'),
(4, 'Thủ kho', 'Thủ kho', 'thukho', '7b498693-5877-11ed-a953-040300000000', 0, 0, 0, 0, '1'),
(5, 'Quản lý', 'Quản lý', 'MGR', '7b49875d-5877-11ed-a953-040300000000', 0, 0, 0, 0, '1'),
(6, 'Nhà đầu tư', 'Nhà đầu tư', 'NDT', '7b498829-5877-11ed-a953-040300000000', 0, 0, 0, 0, '1'),
(7, 'Đo mắt', 'Đo mắt', 'DM', '0', 0, 0, 0, 0, '');
/*!40000 ALTER TABLE `ospos_roles` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `ospos_grants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_grants` (
  `permission_id` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_grants`
--

LOCK TABLES `ospos_grants` WRITE;
/*!40000 ALTER TABLE `ospos_grants` DISABLE KEYS */;
INSERT INTO `ospos_grants` (`permission_id`, `role_id`) VALUES
('100', 2),
('100', 3),
('100', 4),
('100', 5),
('101', 2),
('101', 3),
('101', 5),
('102', 1),
('106', 1),
('106', 2),
('106', 3),
('106', 5),
('108', 1),
('112', 1),
('112', 2),
('112', 3),
('112', 5),
('112', 7),
('113', 1),
('113', 7),
('114', 1),
('114', 7),
('115', 1),
('115', 5),
('115', 7),
('116', 1),
('116', 5),
('116', 7),
('117', 1),
('117', 2),
('117', 3),
('117', 5),
('118', 1),
('118', 2),
('118', 3),
('118', 5),
('119', 1),
('119', 4),
('12', 1),
('12', 2),
('12', 3),
('12', 5),
('12', 7),
('120', 1),
('120', 4),
('121', 1),
('17', 1),
('17', 2),
('17', 3),
('17', 4),
('17', 5),
('18', 1),
('18', 2),
('18', 3),
('18', 4),
('18', 5),
('19', 1),
('21', 1),
('21', 4),
('23', 1),
('23', 2),
('23', 3),
('23', 4),
('23', 5),
('24', 1),
('26', 1),
('26', 2),
('26', 3),
('26', 5),
('26', 8714),
('27', 1),
('27', 2),
('27', 3),
('27', 5),
('27', 8714),
('28', 1),
('28', 5),
('28', 8714),
('29', 1),
('29', 5),
('29', 8714),
('30', 1),
('30', 2),
('30', 4),
('30', 5),
('30', 8714),
('31', 1),
('31', 2),
('31', 4),
('31', 5),
('31', 8714),
('32', 1),
('32', 4),
('32', 5),
('32', 8714),
('33', 1),
('33', 5),
('33', 8714),
('34', 1),
('34', 4),
('34', 5),
('34', 8714),
('35', 1),
('35', 5),
('35', 6),
('35', 8714),
('36', 1),
('36', 5),
('36', 8714),
('37', 1),
('37', 5),
('37', 8714),
('4', 1),
('4', 2),
('4', 3),
('4', 4),
('4', 5),
('47', 1),
('47', 4),
('47', 5),
('47', 7),
('49', 1),
('49', 5),
('5', 1),
('5', 2),
('5', 3),
('5', 4),
('5', 5),
('51', 1),
('52', 1),
('52', 5),
('53', 1),
('53', 5),
('54', 1),
('54', 2),
('54', 3),
('54', 4),
('54', 5),
('55', 1),
('55', 5),
('56', 1),
('57', 1),
('58', 1),
('58', 4),
('60', 1),
('60', 5),
('61', 1),
('61', 4),
('62', 1),
('62', 5),
('65', 1),
('65', 5),
('67', 1),
('67', 5),
('68', 1),
('68', 4),
('68', 5),
('68', 6),
('69', 1),
('69', 4),
('69', 5),
('70', 1),
('70', 4),
('70', 5),
('71', 1),
('71', 4),
('71', 5),
('77', 1),
('77', 2),
('77', 3),
('77', 5),
('78', 1),
('78', 5),
('78', 7),
('80', 1),
('81', 1),
('82', 1),
('83', 1),
('84', 1),
('85', 1),
('86', 1),
('87', 1),
('88', 1),
('89', 1),
('90', 1),
('91', 1),
('92', 1),
('93', 1),
('94', 1),
('95', 1),
('97', 1),
('97', 4),
('97', 5),
('99', 1),
('99', 4),
('99', 5);
/*!40000 ALTER TABLE `ospos_grants` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `ospos_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_permissions` (
  `permission_key` varchar(255) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  `location_id` int(10) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_key` varchar(250) NOT NULL DEFAULT '''''',
  `permissions_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `module_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `ospos_permissions_ibfk_2` (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_permissions`
--

LOCK TABLES `ospos_permissions` WRITE;
/*!40000 ALTER TABLE `ospos_permissions` DISABLE KEYS */;
INSERT INTO `ospos_permissions` (`permission_key`, `module_id`, `location_id`, `id`, `module_key`, `permissions_uuid`, `module_uuid`, `name`) VALUES
('account_manage', 'account', NULL, 4, 'account', 'a5664fbb-5cd9-11ed-b8c5-040300000000', '0a63f505-5cd9-11ed-b8c5-040300000000', NULL),
('account_view', 'account', NULL, 5, 'account', 'a5665085-5cd9-11ed-b8c5-040300000000', '0a63f5c9-5cd9-11ed-b8c5-040300000000', NULL),
('customers_view', 'customers', NULL, 12, 'customers', 'a5665202-5cd9-11ed-b8c5-040300000000', '0a63f745-5cd9-11ed-b8c5-040300000000', 'Tạo mới khách hàng'),
('items_accounting', 'items', NULL, 17, 'items', 'a5665b0b-5cd9-11ed-b8c5-040300000000', '0a63f9fc-5cd9-11ed-b8c5-040300000000', 'Kế toán'),
('items_stock', 'items', 1, 18, 'items', 'a5665bc5-5cd9-11ed-b8c5-040300000000', '0a63fb3f-5cd9-11ed-b8c5-040300000000', 'Kho'),
('item_kits', 'item_kits', NULL, 19, 'item_kits', 'a5665c8f-5cd9-11ed-b8c5-040300000000', '0a63fc32-5cd9-11ed-b8c5-040300000000', NULL),
('order', 'order', NULL, 21, 'order', 'a5665ee5-5cd9-11ed-b8c5-040300000000', '0a63fda6-5cd9-11ed-b8c5-040300000000', NULL),
('receivings_stock', 'receivings', 1, 23, 'receivings', 'a5665fa0-5cd9-11ed-b8c5-040300000000', '0a63fe5c-5cd9-11ed-b8c5-040300000000', NULL),
('reminders', 'reminders', 1, 24, 'reminders', 'a566606e-5cd9-11ed-b8c5-040300000000', '0a63ff2e-5cd9-11ed-b8c5-040300000000', NULL),
('reports_categories', 'reports', NULL, 26, 'reports', 'a5666128-5cd9-11ed-b8c5-040300000000', '0a63ffe6-5cd9-11ed-b8c5-040300000000', NULL),
('reports_customers', 'reports', NULL, 27, 'reports', 'a56661d8-5cd9-11ed-b8c5-040300000000', '0a64008e-5cd9-11ed-b8c5-040300000000', NULL),
('reports_discounts', 'reports', NULL, 28, 'reports', 'a5666286-5cd9-11ed-b8c5-040300000000', '0a64013c-5cd9-11ed-b8c5-040300000000', NULL),
('reports_employees', 'reports', NULL, 29, 'reports', 'a5666334-5cd9-11ed-b8c5-040300000000', '0a6401ec-5cd9-11ed-b8c5-040300000000', NULL),
('reports_inventory', 'reports', NULL, 30, 'reports', 'a56663dc-5cd9-11ed-b8c5-040300000000', '0a640293-5cd9-11ed-b8c5-040300000000', NULL),
('reports_items', 'reports', NULL, 31, 'reports', 'a5666481-5cd9-11ed-b8c5-040300000000', '0a64033c-5cd9-11ed-b8c5-040300000000', NULL),
('reports_lens', 'reports', NULL, 32, 'reports', 'a5666523-5cd9-11ed-b8c5-040300000000', '0a6403ea-5cd9-11ed-b8c5-040300000000', NULL),
('reports_payments', 'reports', NULL, 33, 'reports', 'a56665ce-5cd9-11ed-b8c5-040300000000', '0a640495-5cd9-11ed-b8c5-040300000000', NULL),
('reports_receivings', 'reports', NULL, 34, 'reports', 'a566666e-5cd9-11ed-b8c5-040300000000', '0a64056a-5cd9-11ed-b8c5-040300000000', NULL),
('reports_sales', 'reports', NULL, 35, 'reports', 'a5666709-5cd9-11ed-b8c5-040300000000', '0a640619-5cd9-11ed-b8c5-040300000000', NULL),
('reports_suppliers', 'reports', NULL, 36, 'reports', 'a56667a8-5cd9-11ed-b8c5-040300000000', '0a6406b5-5cd9-11ed-b8c5-040300000000', NULL),
('reports_taxes', 'reports', NULL, 37, 'reports', 'a5666849-5cd9-11ed-b8c5-040300000000', '0a640756-5cd9-11ed-b8c5-040300000000', NULL),
('test_manage', 'test', NULL, 47, 'test', 'a5666d8a-5cd9-11ed-b8c5-040300000000', '0a640c85-5cd9-11ed-b8c5-040300000000', NULL),
('customers_index', 'customers', NULL, 49, 'customers', 'a5666ed7-5cd9-11ed-b8c5-040300000000', '0a640dcb-5cd9-11ed-b8c5-040300000000', 'Danh sách khách hàng'),
('customers_delete', 'customers', NULL, 51, 'customers', 'a5666f9e-5cd9-11ed-b8c5-040300000000', '0a640e88-5cd9-11ed-b8c5-040300000000', 'Xóa'),
('customers_excel_import', 'customers', NULL, 52, 'customers', 'a5667065-5cd9-11ed-b8c5-040300000000', '0a640f42-5cd9-11ed-b8c5-040300000000', 'Nhập excel'),
('customers_excel_import', 'customers', NULL, 53, 'customers', 'a5667124-5cd9-11ed-b8c5-040300000000', '0a640ff6-5cd9-11ed-b8c5-040300000000', 'Nhập excel'),
('items_index', 'items', NULL, 54, 'items', 'a56671e1-5cd9-11ed-b8c5-040300000000', '0a6410ab-5cd9-11ed-b8c5-040300000000', 'Danh sách sản phẩm'),
('items_excel_import', 'items', NULL, 55, 'items', 'a5667293-5cd9-11ed-b8c5-040300000000', '0a64115a-5cd9-11ed-b8c5-040300000000', 'Nhập excel'),
('items_delete', 'items', NULL, 56, 'items', 'a5667349-5cd9-11ed-b8c5-040300000000', '0a64120a-5cd9-11ed-b8c5-040300000000', 'Xóa sản phẩm'),
('items_bulk_update', 'items', NULL, 57, 'items', 'a56673fb-5cd9-11ed-b8c5-040300000000', '0a6412bb-5cd9-11ed-b8c5-040300000000', 'Cập nhật nhiều sản phẩm'),
('items_save_inventory', 'items', NULL, 58, 'items', 'a56674b2-5cd9-11ed-b8c5-040300000000', '0a641368-5cd9-11ed-b8c5-040300000000', 'Lưu vào kho'),
('items_bulk_edit', 'items', NULL, 60, 'items', 'a566756e-5cd9-11ed-b8c5-040300000000', '0a64141d-5cd9-11ed-b8c5-040300000000', 'Chỉnh sửa hàng loạt'),
('items_inventory', 'items', NULL, 61, 'items', 'a5667623-5cd9-11ed-b8c5-040300000000', '0a6414c7-5cd9-11ed-b8c5-040300000000', 'Kho'),
('items_view', 'items', NULL, 62, 'items', 'a56676ca-5cd9-11ed-b8c5-040300000000', '0a641566-5cd9-11ed-b8c5-040300000000', 'Tạo mới'),
('suppliers_view', 'suppliers', NULL, 65, 'suppliers', 'a5667770-5cd9-11ed-b8c5-040300000000', '0a641606-5cd9-11ed-b8c5-040300000000', 'Tạo mới'),
('suppliers_delete', 'suppliers', NULL, 67, 'suppliers', 'a5667834-5cd9-11ed-b8c5-040300000000', '0a6416b2-5cd9-11ed-b8c5-040300000000', 'Xóa'),
('reports_index', 'reports', NULL, 68, 'reports', 'a56678f0-5cd9-11ed-b8c5-040300000000', '0a64176a-5cd9-11ed-b8c5-040300000000', 'Danh sách báo cáo'),
('receivings_index', 'receivings', NULL, 69, 'receivings', 'a56679b0-5cd9-11ed-b8c5-040300000000', '0a64181c-5cd9-11ed-b8c5-040300000000', 'Danh sách '),
('receivings_lens', 'receivings', NULL, 70, 'receivings', 'a5667a61-5cd9-11ed-b8c5-040300000000', '0a6418ca-5cd9-11ed-b8c5-040300000000', 'Nhập tròng kính'),
('receivings_view', 'receivings', NULL, 71, 'receivings', 'a5667b14-5cd9-11ed-b8c5-040300000000', '0a641979-5cd9-11ed-b8c5-040300000000', 'Nhập hàng'),
('sales_index', 'sales', NULL, 77, 'sales', 'a5667f5c-5cd9-11ed-b8c5-040300000000', '0a641d91-5cd9-11ed-b8c5-040300000000', 'Bán hàng (tạo mới)'),
('test_index', 'test', NULL, 78, 'test', 'a566804a-5cd9-11ed-b8c5-040300000000', '0a641e2f-5cd9-11ed-b8c5-040300000000', 'Danh sách đơn kính'),
('config_index', 'config', NULL, 80, 'config', 'a56681c7-5cd9-11ed-b8c5-040300000000', '0a641f9c-5cd9-11ed-b8c5-040300000000', 'Danh sách'),
('roles_index', 'roles', NULL, 81, 'roles', 'a5668293-5cd9-11ed-b8c5-040300000000', '0a64204b-5cd9-11ed-b8c5-040300000000', 'Nhóm quyền'),
('roles_create', 'roles', NULL, 82, 'roles', 'a5668351-5cd9-11ed-b8c5-040300000000', '0a6420f6-5cd9-11ed-b8c5-040300000000', 'Tạo nhóm quyền'),
('roles_view', 'roles', NULL, 83, 'roles', 'a5668403-5cd9-11ed-b8c5-040300000000', '0a6421a5-5cd9-11ed-b8c5-040300000000', 'Xem nhóm quyền'),
('roles_edit', 'roles', NULL, 84, 'roles', 'a56684c1-5cd9-11ed-b8c5-040300000000', '0a642255-5cd9-11ed-b8c5-040300000000', 'Sửa nhóm quyền'),
('roles_per_index', 'roles', NULL, 85, 'roles', 'a5668583-5cd9-11ed-b8c5-040300000000', '0a642306-5cd9-11ed-b8c5-040300000000', 'Quyền'),
('roles_per_add', 'roles', NULL, 86, 'roles', 'a5668636-5cd9-11ed-b8c5-040300000000', '0a6423ae-5cd9-11ed-b8c5-040300000000', 'Thêm quyền'),
('roles_per_view', 'roles', NULL, 87, 'roles', 'a56686d4-5cd9-11ed-b8c5-040300000000', '0a642449-5cd9-11ed-b8c5-040300000000', 'Xem quyền'),
('roles_per_edit', 'roles', NULL, 88, 'roles', 'a566877f-5cd9-11ed-b8c5-040300000000', '0a6424ea-5cd9-11ed-b8c5-040300000000', 'Sửa quyền'),
('roles_mod_index', 'roles', NULL, 89, 'roles', 'a566882e-5cd9-11ed-b8c5-040300000000', '0a642589-5cd9-11ed-b8c5-040300000000', 'Danh sách mô đun'),
('roles_mod_add', 'roles', NULL, 90, 'roles', 'a56688d4-5cd9-11ed-b8c5-040300000000', '0a642624-5cd9-11ed-b8c5-040300000000', 'Thêm mô đun'),
('roles_mod_view', 'roles', NULL, 91, 'roles', 'a566896f-5cd9-11ed-b8c5-040300000000', '0a6426be-5cd9-11ed-b8c5-040300000000', 'Xem mô đun'),
('roles_mod_edit', 'roles', NULL, 92, 'roles', 'a5668a19-5cd9-11ed-b8c5-040300000000', '0a64275c-5cd9-11ed-b8c5-040300000000', 'Sửa mô đun'),
('employees_index', 'employees', NULL, 93, 'employees', 'a5668ac8-5cd9-11ed-b8c5-040300000000', '0a6427fe-5cd9-11ed-b8c5-040300000000', 'Danh sách nhân viên'),
('employees_view', 'employees', NULL, 94, 'employees', 'a5668b7c-5cd9-11ed-b8c5-040300000000', '0a6428ab-5cd9-11ed-b8c5-040300000000', 'Tạo mới'),
('employees_delete', 'employees', NULL, 95, 'employees', 'a5668c2d-5cd9-11ed-b8c5-040300000000', '0a64295a-5cd9-11ed-b8c5-040300000000', 'Xóa'),
('items_count_details', 'items', NULL, 97, 'items', 'a5668db5-5cd9-11ed-b8c5-040300000000', '0a642abe-5cd9-11ed-b8c5-040300000000', 'Xem chi tiết sản phẩm trong kho'),
('suppliers_index', 'suppliers', NULL, 99, 'suppliers', 'a5668e77-5cd9-11ed-b8c5-040300000000', '0a642b7c-5cd9-11ed-b8c5-040300000000', 'Danh sách nhà cung cấp'),
('items_unitprice_hide', 'items', NULL, 100, 'items', '6b284764-5cde-11ed-b8c5-040300000000', '6b28476f-5cde-11ed-b8c5-040300000000', 'Ẩn giá nhập'),
('customers_phonenumber_hide', 'customers', 1, 101, 'customers', 'fe2fc7fc-5ce9-11ed-b8c5-040300000000', 'fe2fc804-5ce9-11ed-b8c5-040300000000', ''),
('sales_price_edit', 'sales', 1, 102, 'sales', 'a974fae3-5d00-11ed-b8c5-040300000000', 'a974faeb-5d00-11ed-b8c5-040300000000', 'Cho phép thay đổi giá'),
('sales_manage', 'sales', NULL, 106, 'sales', '2f9353b8-5d0f-11ed-b8c5-040300000000', '2f9353c3-5d0f-11ed-b8c5-040300000000', 'Danh sách đơn hàng'),
('sales_delete', 'sales', 1, 108, 'sales', '9422bf13-5d0f-11ed-b8c5-040300000000', '9422bf1d-5d0f-11ed-b8c5-040300000000', ''),
('test_detail_test', 'test', 1, 112, 'test', '25460ad1-5d10-11ed-b8c5-040300000000', '25460ada-5d10-11ed-b8c5-040300000000', ''),
('test_delete', 'test', 1, 113, 'test', '314c3e14-5d10-11ed-b8c5-040300000000', '314c3e1c-5d10-11ed-b8c5-040300000000', ''),
('test_edit', 'test', 1, 114, 'test', '31dca20f-5d10-11ed-b8c5-040300000000', '31dca218-5d10-11ed-b8c5-040300000000', ''),
('test_view', 'test', 1, 115, 'test', '37a58d56-5d10-11ed-b8c5-040300000000', '37a58d63-5d10-11ed-b8c5-040300000000', ''),
('test_view_test', 'test', 1, 116, 'test', '3f3976cd-5d10-11ed-b8c5-040300000000', '3f3976d4-5d10-11ed-b8c5-040300000000', ''),
('account_index', 'account', 1, 117, 'account', '5776d5d3-5d10-11ed-b8c5-040300000000', '5776d5dd-5d10-11ed-b8c5-040300000000', ''),
('customer_info_index', 'customer_info', 1, 118, 'customer_info', '6a9e1d60-5d10-11ed-b8c5-040300000000', '6a9e1d69-5d10-11ed-b8c5-040300000000', ''),
('barcodes_index', 'barcodes', NULL, 119, 'barcodes', 'af1f3265-7196-11ed-8174-005056847d3e', 'af1f326a-7196-11ed-8174-005056847d3e', 'Quản lý Barcode'),
('items_generate_barcodes', 'items', NULL, 120, 'items', '8a2bee25-7337-11ed-8174-005056847d3e', '8a2bee2b-7337-11ed-8174-005056847d3e', 'Tao Barcode'),
('account_admin', 'account', NULL, 121, 'account', '8909405c-8dd8-11ed-8174-005056847d3e', '89094066-8dd8-11ed-8174-005056847d3e', 'Quản lý');
/*!40000 ALTER TABLE `ospos_permissions` ENABLE KEYS */;
UNLOCK TABLES;
/*
  supplier_id           | int(11)       | YES  | MUL | NULL    |                |
| item_number           | varchar(255)  | YES  | UNI | NULL    |                |
| description           | varchar(255)  | NO   |     | NULL    |                |
| cost_price            | decimal(15,2) | NO   |     | NULL    |                |
| unit_price            | decimal(15,2) | NO   | MUL | NULL    |                |
| reorder_level         | decimal(15,3) | NO   |     | 0.000   |                |
| receiving_quantity    | decimal(15,3) | NO   |     | 1.000   |                |
| item_id               | int(10)       | NO   | PRI | NULL    | auto_increment |
| pic_id                | int(10)       | YES  |     | NULL    |                |
| allow_alt_description | tinyint(1)    | NO   |     | NULL    |                |
| is_serialized   
*/


-- ADD fields to ospos_sales
-- sale_uuid, confirm: 0,1,2; 0: don't confirm; 1: OK; 2: not OK
--
--
-- Table structure for table `ospos_customers`
--



-- 10/06/2023
ALTER TABLE `ospos_test` ADD `reason` text CHARACTER SET utf8 NOT NULL AFTER `test_uuid`; 
ALTER TABLE `ospos_test` ADD `step` tinyint(1) DEFAULT 2 COMMENT '1: Tiếp; 2: đang khám; 3: khám xong;' AFTER `test_uuid`; 
ALTER TABLE `ospos_test` ADD `updated_at` int(11) DEFAULT 0 AFTER `test_uuid`; 










--- Edit table vì đã tồn tại ----------
-- CREATE TABLE `ospos_permissions` (
--   `id` int(10) NOT NULL,
--   `module_key` varchar(250) CHARACTER SET utf8,
--   `permission_id` int(10) NOT NULL DEFAULT 0,
--   `module_id` int(10) NOT NULL DEFAULT 0,
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ALTER TABLE `ospos_permissions` ADD PRIMARY KEY( `id`); 
-- ALTER TABLE `ospos_permissions` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT; 

-- CREATE TABLE `ospos_modules` (
--   `id` int(10) NOT NULL,
--   `module_id` varchar(250) CHARACTER SET utf8,
--   `name_lang_key` varchar(250) CHARACTER SET utf8,
--   `desc_lang_key` varchar(250) CHARACTER SET utf8,
--   `code` varchar(20) CHARACTER SET utf8,
--   `module_uuid` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '0',
--   `created_at` int(11) NOT NULL DEFAULT 0,
--   `updated_at` int(11) NOT NULL DEFAULT 0,
--   `deleted_at` int(11) NOT NULL DEFAULT 0,
--   `status` tinyint(1) NOT NULL DEFAULT 1,
--   `name` text CHARACTER SET utf8 NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ALTER TABLE `ospos_modules` ADD PRIMARY KEY( `id`); 
-- ALTER TABLE `ospos_modules` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT; 
--  ---------------

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
