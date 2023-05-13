-- MySQL dump 10.19  Distrib 10.3.38-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: namh_sys
-- ------------------------------------------------------
-- Server version	10.3.38-MariaDB-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ospos_app_config`
--

DROP TABLE IF EXISTS `ospos_app_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_app_config` (
  `key` varchar(50) NOT NULL,
  `value` mediumtext DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_app_config`
--

LOCK TABLES `ospos_app_config` WRITE;
/*!40000 ALTER TABLE `ospos_app_config` DISABLE KEYS */;
INSERT INTO `ospos_app_config` VALUES ('address','50 Trần Đại Nghĩa, HN'),('barcode_content','number'),('barcode_first_row','name'),('barcode_font','Arial.ttf'),('barcode_font_size','10'),('barcode_generate_if_empty','1'),('barcode_height','20'),('barcode_num_in_row','1'),('barcode_page_cellspacing','20'),('barcode_page_width','100'),('barcode_quality','100'),('barcode_second_row','item_code'),('barcode_third_row','unit_price'),('barcode_type','Code128'),('barcode_width','250'),('client_id','675e673a-1518-4bc1-93e1-8eff41ebdade'),('company','KÍNH THUỐC NAM HẢI'),('company_logo','company_logo1.png'),('country_codes','vn'),('currency_decimals','0'),('currency_symbol','₫'),('custom10_name',''),('custom1_name',''),('custom2_name',''),('custom3_name',''),('custom4_name',''),('custom5_name',''),('custom6_name',''),('custom7_name',''),('custom8_name',''),('custom9_name',''),('dateformat','d/m/Y'),('default_sales_discount','0'),('default_tax_1_name','VAT'),('default_tax_1_rate','10'),('default_tax_2_name',''),('default_tax_2_rate',''),('default_tax_rate','8'),('email',NULL),('fax',''),('filter','GONG 1T\nGONG 2T\nGONG 3T\nGONG 4T\nGONG 5T\nGONG +6T\nGONG 200K\nGONG 300K\nGONG 400K\nGONG 500K'),('filter_contact_lens','Ngâm-Nhỏ\nLens Seed 1M Trong\nLens Seed 1M Pure\nLens Seed 1D Trong\nLens Seed 1D Rich\nLens Seed 1D Base\nLens Seed 1D Pure\nCLALEN 1D Latin\nCLALEN 1D Alica Brown\nCLALEN 1D Soul Brown\nCLALEN 1D Suzy Gray\nLens Biomedics 1D\nLens Biomedics55 3M\nLens 3M O2O2'),('filter_other',''),('filter_sun_glasses','KINH 1T\nKINH 2T\nKINH 3T\nKINH 4T\nKINH 5T\nKINH +6T\nKINH 200K\nKINH 300K\nKINH 400K\nKINH 500K'),('iKindOfLens','1.56 CHEMI PHOTO GRAY\n1.60 CHEMI PHOTO GRAY\n1.56 CHEMI\n1.56 CHEMI Crystal U2\n1.61 CHEMI Crystal U2\n1.67 CHEMI Crystal U2\n1.74 CHEMI Crystal U2\n1.56 CHEMI Crystal U6\n1.60 CHEMI Crystal U6\n1.67 CHEMI Crystal U6\n1.56 KODAK CLEAN CLEAR\n1.60 KODAK CLEAN CLEAR\n1.67 KODAK CLEAN CLEAR\n1.60 KODAK UVBLUE\n1.60 ESSILOR CRIZAL ALIZE\n1.60 HOYA NULUX SFT\n1.67 HOYA NULUX SFT\n1.55 HOYA BLUE\n1.60 HOYA BLUE\n1.60 NAHAmi SUPER HMC A+\n1.56 POLAROID KHOI\n1.56 POLAROID XANH\n1.56 XANH 1 MÀU CR\n1.56 KHÓI 1 MÀU CR\n1.56 KHÓI 2 MÀU CR\n1.56 TRÀ 2 MÀU CR\n1.56 TRÀ 1 MÀU CR\n1.56 TRÁNG CỨNG\nMẮT MÀU INDO\nMẮT LẺ KHÁC'),('invoice_default_comments',''),('invoice_email_message',''),('invoice_enable','0'),('language','english'),('language_code','en'),('lines_per_page','25'),('mailpath','/usr/sbin/sendmail'),('msg_msg',''),('msg_pwd',''),('msg_src',''),('msg_uid',''),('notify_horizontal_position','center'),('notify_vertical_position','top'),('number_locale','vi_VN'),('payment_options_order','cashdebitcredit'),('phone','02436286005'),('print_bottom_margin','0'),('print_footer','0'),('print_header','0'),('print_left_margin','0'),('print_right_margin','0'),('print_silently','0'),('print_top_margin','0'),('protocol','mail'),('quantity_decimals','0'),('receipt_printer','HP LaserJet Professional P1102'),('receipt_show_description','0'),('receipt_show_serialnumber','0'),('receipt_show_taxes','0'),('receipt_show_total_discount','1'),('receipt_template','receipt_default'),('receiving_calculate_average_price','1'),('recv_invoice_format','$CO'),('return_policy','Hệ thống truy cập vào tài nguyên'),('sales_invoice_format','$CO'),('smtp_crypto','ssl'),('smtp_port','465'),('smtp_timeout','5'),('statistics','1'),('takings_printer','HP LaserJet Professional P1102'),('tax_decimals','2'),('tax_included','1'),('theme','united'),('thousands_separator','thousands_separator'),('timeformat','H:i:s'),('timezone','Asia/Bangkok'),('website','');
/*!40000 ALTER TABLE `ospos_app_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_customers`
--

DROP TABLE IF EXISTS `ospos_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `customer_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  UNIQUE KEY `account_number` (`account_number`),
  KEY `person_id` (`person_id`),
  FULLTEXT KEY `account_number_2` (`account_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_customers`
--



--
-- Table structure for table `ospos_daily_total`
--

DROP TABLE IF EXISTS `ospos_daily_total`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_daily_total` (
  `daily_total_id` int(10) NOT NULL AUTO_INCREMENT,
  `created_time` int(11) DEFAULT NULL,
  `begining_amount` decimal(15,2) NOT NULL,
  `ending_amount` decimal(15,2) NOT NULL,
  `increase_amount` decimal(15,2) NOT NULL,
  `decrease_amount` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`daily_total_id`),
  KEY `sale_id` (`daily_total_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_daily_total`
--


--
-- Table structure for table `ospos_employees`
--

DROP TABLE IF EXISTS `ospos_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_employees` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `hash_version` int(1) NOT NULL DEFAULT 2,
  `type` tinyint(1) DEFAULT 1 COMMENT '1:staff;2:CTV',
  `log` varchar(10) NOT NULL DEFAULT '0',
  UNIQUE KEY `username` (`username`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_employees`
--


--
-- Table structure for table `ospos_fields`
--

DROP TABLE IF EXISTS `ospos_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_fields` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `field_key` varchar(250) DEFAULT NULL,
  `permission_id` int(10) NOT NULL DEFAULT 0,
  `permission` tinyint(1) NOT NULL DEFAULT 2,
  `field_name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_fields`
--

LOCK TABLES `ospos_fields` WRITE;
/*!40000 ALTER TABLE `ospos_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_grants`
--

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
INSERT INTO `ospos_grants` VALUES ('100',2),('100',3),('100',4),('100',5),('101',2),('101',3),('101',5),('102',1),('106',1),('106',2),('106',3),('106',5),('108',1),('112',1),('112',2),('112',3),('112',5),('112',7),('113',1),('113',7),('114',1),('114',2),('114',3),('114',7),('115',1),('115',2),('115',3),('115',5),('115',7),('116',1),('116',2),('116',3),('116',5),('116',7),('117',1),('117',2),('117',3),('117',5),('118',1),('118',2),('118',3),('118',5),('119',1),('119',4),('12',1),('12',2),('12',3),('12',5),('12',7),('120',1),('120',4),('121',1),('121',2),('121',3),('127',1),('128',1),('129',1),('130',1),('131',1),('132',1),('132',4),('133',1),('133',4),('134',1),('134',4),('135',1),('135',4),('136',1),('136',4),('137',1),('137',4),('138',1),('138',4),('17',1),('17',2),('17',3),('17',4),('17',5),('18',1),('18',2),('18',3),('18',4),('18',5),('19',1),('21',1),('21',4),('23',1),('23',2),('23',3),('23',4),('23',5),('24',1),('26',1),('26',2),('26',3),('26',5),('26',8714),('27',1),('27',2),('27',3),('27',5),('27',8714),('28',1),('28',5),('28',8714),('29',1),('29',5),('29',8714),('30',1),('30',2),('30',3),('30',4),('30',5),('30',8714),('31',1),('31',2),('31',3),('31',4),('31',5),('31',8714),('32',1),('32',4),('32',5),('32',8714),('33',1),('33',5),('33',8714),('34',1),('34',2),('34',3),('34',4),('34',5),('34',8714),('35',1),('35',5),('35',6),('35',8714),('36',1),('36',5),('36',8714),('37',1),('37',5),('37',8714),('4',1),('4',2),('4',3),('4',4),('4',5),('47',1),('47',4),('47',5),('47',7),('49',1),('49',5),('5',1),('5',2),('5',3),('5',4),('5',5),('51',1),('52',1),('52',5),('53',1),('53',5),('54',1),('54',2),('54',3),('54',4),('54',5),('55',1),('55',5),('56',1),('57',1),('58',1),('58',4),('60',1),('60',5),('61',1),('61',4),('62',1),('62',5),('65',1),('65',5),('67',1),('67',5),('68',1),('68',2),('68',3),('68',4),('68',5),('68',6),('69',1),('69',2),('69',3),('69',4),('69',5),('70',1),('70',2),('70',3),('70',4),('70',5),('71',1),('71',2),('71',3),('71',4),('71',5),('77',1),('77',2),('77',3),('77',5),('78',1),('78',5),('78',7),('80',1),('81',1),('82',1),('83',1),('84',1),('85',1),('86',1),('87',1),('88',1),('89',1),('90',1),('91',1),('92',1),('93',1),('94',1),('95',1),('97',1),('97',4),('97',5),('99',1),('99',4),('99',5);
/*!40000 ALTER TABLE `ospos_grants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_history_points`
--

DROP TABLE IF EXISTS `ospos_history_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_history_points` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) NOT NULL DEFAULT 0,
  `sale_id` int(10) NOT NULL DEFAULT 0,
  `sale_uuid` varchar(250) NOT NULL DEFAULT '0',
  `created_date` int(11) NOT NULL DEFAULT 0,
  `point` decimal(10,2) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_history_points`
--

LOCK TABLES `ospos_history_points` WRITE;
/*!40000 ALTER TABLE `ospos_history_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_history_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_inventory`
--

DROP TABLE IF EXISTS `ospos_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_inventory` (
  `trans_id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_items` int(11) NOT NULL DEFAULT 0,
  `trans_user` int(11) NOT NULL DEFAULT 0,
  `trans_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `trans_comment` text NOT NULL,
  `trans_location` int(11) NOT NULL,
  `trans_inventory` decimal(15,3) NOT NULL DEFAULT 0.000,
  PRIMARY KEY (`trans_id`),
  KEY `trans_items` (`trans_items`),
  KEY `trans_user` (`trans_user`),
  KEY `trans_location` (`trans_location`)
) ENGINE=InnoDB AUTO_INCREMENT=67800 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_inventory`
--

--
-- Table structure for table `ospos_item_kit_items`
--

DROP TABLE IF EXISTS `ospos_item_kit_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_item_kit_items` (
  `item_kit_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL,
  PRIMARY KEY (`item_kit_id`,`item_id`,`quantity`),
  KEY `ospos_item_kit_items_ibfk_2` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_item_kit_items`
--

--
-- Table structure for table `ospos_item_kits`
--

DROP TABLE IF EXISTS `ospos_item_kits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_item_kits` (
  `item_kit_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`item_kit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_item_kits`
--

LOCK TABLES `ospos_item_kits` WRITE;
/*!40000 ALTER TABLE `ospos_item_kits` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_item_kits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_item_quantities`
--

DROP TABLE IF EXISTS `ospos_item_quantities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_item_quantities` (
  `item_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `quantity` decimal(15,3) NOT NULL DEFAULT 0.000,
  PRIMARY KEY (`item_id`,`location_id`),
  KEY `item_id` (`item_id`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_item_quantities`
--

LOCK TABLES `ospos_item_quantities` WRITE;
/*!40000 ALTER TABLE `ospos_item_quantities` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_item_quantities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_items`
--

DROP TABLE IF EXISTS `ospos_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
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
  `code` varchar(255) DEFAULT NULL,
  `ref_item_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_number` (`item_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `unit_cost` (`unit_price`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=52995 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_items`
--

LOCK TABLES `ospos_items` WRITE;
/*!40000 ALTER TABLE `ospos_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_items_taxes`
--

DROP TABLE IF EXISTS `ospos_items_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_items_taxes` (
  `item_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  PRIMARY KEY (`item_id`,`name`,`percent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_items_taxes`
--

LOCK TABLES `ospos_items_taxes` WRITE;
/*!40000 ALTER TABLE `ospos_items_taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_items_taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_messages`
--

DROP TABLE IF EXISTS `ospos_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(25) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `type` tinyint(1) DEFAULT 0 COMMENT '0: gửi cảm ơn; 1: gửi nhắc khám;2 gửi sinh nhật;3 gửi giảm giá; 4 gửi sự kiện',
  `employee_id` int(11) DEFAULT NULL,
  `name` varchar(25) DEFAULT '',
  `created_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_messages`
--

LOCK TABLES `ospos_messages` WRITE;
/*!40000 ALTER TABLE `ospos_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_modules`
--

/*DROP TABLE IF EXISTS `ospos_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
/*
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_modules`
--

LOCK TABLES `ospos_modules` WRITE;
/*!40000 ALTER TABLE `ospos_modules` DISABLE KEYS */;
INSERT INTO `ospos_modules` VALUES ('module_account','module_account_desc',120,'account',1,NULL,'Kế toán',0,0,0,'aa3922b7-5819-11ed-b65f-040300000000'),('module_config','module_config_desc',130,'config',2,NULL,'Thiết lập',0,0,0,'aa392523-5819-11ed-b65f-040300000000'),('module_customers','module_customers_desc',10,'customers',3,NULL,'Khách hàng',0,0,0,'aa3926ef-5819-11ed-b65f-040300000000'),('module_customer_info','module_customer_info',121,'customer_info',4,NULL,'Bảo hành',0,0,0,'aa3927cc-5819-11ed-b65f-040300000000'),('module_employees','module_employees_desc',80,'employees',5,NULL,'Nhân viên',0,0,0,'aa3928f4-5819-11ed-b65f-040300000000'),('module_giftcards','module_giftcards_desc',90,'giftcards',6,NULL,'Quà tặng',0,0,0,'aa392a99-5819-11ed-b65f-040300000000'),('module_items','module_items_desc',20,'items',7,NULL,'Sản phẩm',0,0,0,'aa392b4c-5819-11ed-b65f-040300000000'),('module_item_kits','module_item_kits_desc',30,'item_kits',8,NULL,'Nhóm sản phẩm',0,0,0,'aa392bf1-5819-11ed-b65f-040300000000'),('module_messages','module_messages_desc',100,'messages',9,NULL,'Tin nhắn',0,0,0,'aa392ca6-5819-11ed-b65f-040300000000'),('module_order','module_order_desc',150,'order',10,NULL,NULL,0,0,0,'aa392d4a-5819-11ed-b65f-040300000000'),('module_receivings','module_receivings_desc',60,'receivings',11,NULL,'Nhập hàng',0,0,0,'aa392df2-5819-11ed-b65f-040300000000'),('module_reminders','module_reminders_desc',140,'reminders',12,NULL,NULL,0,0,0,'aa392ea1-5819-11ed-b65f-040300000000'),('module_reports','module_reports_desc',50,'reports',13,NULL,'Báo cáo',0,0,0,'aa392f58-5819-11ed-b65f-040300000000'),('module_sales','module_sales_desc',70,'sales',14,NULL,'Bán hàng',0,0,0,'aa393000-5819-11ed-b65f-040300000000'),('module_suppliers','module_suppliers_desc',40,'suppliers',15,NULL,'Nhà cung cấp',0,0,0,'aa3930d4-5819-11ed-b65f-040300000000'),('module_test','module_test_desc',110,'test',16,NULL,'Đo mắt',0,0,0,'aa393173-5819-11ed-b65f-040300000000'),('roles','roles',10,'roles',17,'roles','Phân quyền',1667225850,0,0,'c14fb1fe-5926-11ed-b3d8-040300000000'),('barcodes','barcodes',10,'barcodes',18,'barcodes','Quản lý barcode',1669912730,0,0,'a29e2092-7196-11ed-8174-005056847d3e'),('account1','account1',10,'account1',19,'account1','Bán-nhập',1673279139,0,0,'aaa439ed-9034-11ed-8174-005056847d3e'),('purchases','purchases',10,'purchases',20,'purchases','Quản lý yêu cầu nhập kho',1676040796,0,0,'a66922bc-a952-11ed-a343-040300000000');
/*!40000 ALTER TABLE `ospos_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_people`
--

DROP TABLE IF EXISTS `ospos_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `person_id` int(10) NOT NULL AUTO_INCREMENT,
  `age` char(2) NOT NULL DEFAULT '0',
  `facebook` varchar(250) DEFAULT '',
  PRIMARY KEY (`person_id`),
  KEY `first_name` (`first_name`),
  KEY `phone_number` (`phone_number`),
  KEY `phone_number_2` (`phone_number`),
  KEY `phone_number_3` (`phone_number`),
  FULLTEXT KEY `last_name` (`last_name`),
  FULLTEXT KEY `first_name_2` (`first_name`,`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=234026 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_people`
--

LOCK TABLES `ospos_people` WRITE;
/*!40000 ALTER TABLE `ospos_people` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_people` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_permissions`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_permissions`
--

LOCK TABLES `ospos_permissions` WRITE;
/*!40000 ALTER TABLE `ospos_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_purchases`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=713 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_purchases_items`
--

LOCK TABLES `ospos_purchases_items` WRITE;
/*!40000 ALTER TABLE `ospos_purchases_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_purchases_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_receivings`
--

DROP TABLE IF EXISTS `ospos_receivings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_receivings` (
  `receiving_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `receiving_id` int(10) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(20) DEFAULT NULL,
  `reference` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`receiving_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `employee_id` (`employee_id`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB AUTO_INCREMENT=640 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_receivings`
--

LOCK TABLES `ospos_receivings` WRITE;
/*!40000 ALTER TABLE `ospos_receivings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_receivings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_receivings_items`
--

DROP TABLE IF EXISTS `ospos_receivings_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `receiving_quantity` decimal(15,3) NOT NULL DEFAULT 1.000,
  PRIMARY KEY (`receiving_id`,`item_id`,`line`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_receivings_items`
--

LOCK TABLES `ospos_receivings_items` WRITE;
/*!40000 ALTER TABLE `ospos_receivings_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_receivings_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_reminders`
--

DROP TABLE IF EXISTS `ospos_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_reminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) DEFAULT NULL,
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
  `is_sms` tinyint(1) DEFAULT 0 COMMENT '0 chưa gửi; 1 đã gửi thành công',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_reminders`
--

LOCK TABLES `ospos_reminders` WRITE;
/*!40000 ALTER TABLE `ospos_reminders` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_reminders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_reports_detail_sales`
--

DROP TABLE IF EXISTS `ospos_reports_detail_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_reports_detail_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `sale_type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_reports_detail_sales`
--

LOCK TABLES `ospos_reports_detail_sales` WRITE;
/*!40000 ALTER TABLE `ospos_reports_detail_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_reports_detail_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_role_permissions`
--

DROP TABLE IF EXISTS `ospos_role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_role_permissions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL DEFAULT 0,
  `permission_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_role_permissions`
--

LOCK TABLES `ospos_role_permissions` WRITE;
/*!40000 ALTER TABLE `ospos_role_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_roles`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_roles`
--

LOCK TABLES `ospos_roles` WRITE;
/*!40000 ALTER TABLE `ospos_roles` DISABLE KEYS */;
INSERT INTO `ospos_roles` VALUES (1,'admin','admin','ADM','7b498149-5877-11ed-a953-040300000000',0,0,0,0,'1'),(2,'Bán hàng','Bán hàng','SALE','7b4984a5-5877-11ed-a953-040300000000',0,0,0,0,'1'),(3,'Thu ngân','Thu ngân','thungan','7b4985c8-5877-11ed-a953-040300000000',0,0,0,0,'1'),(4,'Thủ kho','Thủ kho','thukho','7b498693-5877-11ed-a953-040300000000',0,0,0,0,'1'),(5,'Quản lý','Quản lý','MGR','7b49875d-5877-11ed-a953-040300000000',0,0,0,0,'1'),(6,'Nhà đầu tư','Nhà đầu tư','NDT','7b498829-5877-11ed-a953-040300000000',0,0,0,0,'1'),(7,'Đo mắt','Đo mắt','DM','0',0,0,0,0,'');
/*!40000 ALTER TABLE `ospos_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sales`
--

DROP TABLE IF EXISTS `ospos_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_sales` (
  `sale_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `invoice_number` varchar(32) DEFAULT NULL,
  `sale_id` int(10) NOT NULL AUTO_INCREMENT,
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
  `confirm` tinyint(1) DEFAULT 0 COMMENT '0: chưa confirm\r\n1: đã confirm',
  `sale_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  PRIMARY KEY (`sale_id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`),
  KEY `sale_time` (`sale_time`)
) ENGINE=InnoDB AUTO_INCREMENT=2159 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sales`
--

LOCK TABLES `ospos_sales` WRITE;
/*!40000 ALTER TABLE `ospos_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sales_items`
--

DROP TABLE IF EXISTS `ospos_sales_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `item_category` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`line`),
  KEY `sale_id` (`sale_id`),
  KEY `item_id` (`item_id`),
  KEY `item_location` (`item_location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sales_items`
--

LOCK TABLES `ospos_sales_items` WRITE;
/*!40000 ALTER TABLE `ospos_sales_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sales_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sales_items_taxes`
--

DROP TABLE IF EXISTS `ospos_sales_items_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_sales_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(3) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`),
  KEY `sale_id` (`sale_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sales_items_taxes`
--

LOCK TABLES `ospos_sales_items_taxes` WRITE;
/*!40000 ALTER TABLE `ospos_sales_items_taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sales_items_taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sales_payments`
--

DROP TABLE IF EXISTS `ospos_sales_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_sales_payments` (
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_id` int(10) NOT NULL AUTO_INCREMENT,
  `payment_kind` varchar(40) NOT NULL DEFAULT '''''' COMMENT '{Thanh Toán='''';Đặt Trước}',
  PRIMARY KEY (`payment_id`),
  KEY `sale_id` (`sale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1870 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sales_payments`
--

LOCK TABLES `ospos_sales_payments` WRITE;
/*!40000 ALTER TABLE `ospos_sales_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sales_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sales_suspended`
--

DROP TABLE IF EXISTS `ospos_sales_suspended`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_sales_suspended` (
  `sale_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `invoice_number` varchar(32) DEFAULT NULL,
  `sale_id` int(10) NOT NULL AUTO_INCREMENT,
  `lock` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sale_id`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sales_suspended`
--

LOCK TABLES `ospos_sales_suspended` WRITE;
/*!40000 ALTER TABLE `ospos_sales_suspended` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sales_suspended` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sales_suspended_items`
--

DROP TABLE IF EXISTS `ospos_sales_suspended_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `item_location` int(11) NOT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`line`),
  KEY `sale_id` (`sale_id`),
  KEY `item_id` (`item_id`),
  KEY `ospos_sales_suspended_items_ibfk_3` (`item_location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sales_suspended_items`
--

LOCK TABLES `ospos_sales_suspended_items` WRITE;
/*!40000 ALTER TABLE `ospos_sales_suspended_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sales_suspended_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sales_suspended_items_taxes`
--

DROP TABLE IF EXISTS `ospos_sales_suspended_items_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_sales_suspended_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(3) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `percent` decimal(15,3) NOT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sales_suspended_items_taxes`
--

LOCK TABLES `ospos_sales_suspended_items_taxes` WRITE;
/*!40000 ALTER TABLE `ospos_sales_suspended_items_taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sales_suspended_items_taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sales_suspended_payments`
--

DROP TABLE IF EXISTS `ospos_sales_suspended_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_sales_suspended_payments` (
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`sale_id`,`payment_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sales_suspended_payments`
--

LOCK TABLES `ospos_sales_suspended_payments` WRITE;
/*!40000 ALTER TABLE `ospos_sales_suspended_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sales_suspended_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sessions`
--

DROP TABLE IF EXISTS `ospos_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` longblob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sessions`
--

LOCK TABLES `ospos_sessions` WRITE;
/*!40000 ALTER TABLE `ospos_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_short_survey`
--

DROP TABLE IF EXISTS `ospos_short_survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_short_survey` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) DEFAULT NULL,
  `sale_id` int(10) DEFAULT NULL,
  `sale_uuid` varchar(255) DEFAULT NULL,
  `nvbh_id` int(10) NOT NULL DEFAULT 0,
  `kxv_id` int(10) NOT NULL DEFAULT 0,
  `created_date` int(11) NOT NULL DEFAULT 0,
  `q1` int(1) NOT NULL DEFAULT 1,
  `q2` int(1) NOT NULL DEFAULT 1,
  `q3` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_short_survey`
--

LOCK TABLES `ospos_short_survey` WRITE;
/*!40000 ALTER TABLE `ospos_short_survey` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_short_survey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_sms_sale`
--

DROP TABLE IF EXISTS `ospos_sms_sale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_sms_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) DEFAULT NULL,
  `is_sms` tinyint(1) DEFAULT 0 COMMENT '0: chưa gửi sms;1 đã gửi sms',
  `name` varchar(250) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `saled_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2159 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_sms_sale`
--

LOCK TABLES `ospos_sms_sale` WRITE;
/*!40000 ALTER TABLE `ospos_sms_sale` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_sms_sale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_stock_locations`
--

DROP TABLE IF EXISTS `ospos_stock_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_stock_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `location_code` varchar(5) NOT NULL,
  `location_phone` varchar(12) NOT NULL,
  `location_address` varchar(255) NOT NULL,
  `location_owner_name` varchar(255) NOT NULL,
  `location_parent_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_stock_locations`
--

LOCK TABLES `ospos_stock_locations` WRITE;
/*!40000 ALTER TABLE `ospos_stock_locations` DISABLE KEYS */;
INSERT INTO `ospos_stock_locations` VALUES (1,'Local',0,'','','','',0);
/*!40000 ALTER TABLE `ospos_stock_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_suppliers`
--

DROP TABLE IF EXISTS `ospos_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_suppliers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `agency_name` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `company_phone` varchar(12) NOT NULL,
  `company_address` varchar(255) NOT NULL,
  `company_code` varchar(5) NOT NULL,
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`supplier_id`),
  UNIQUE KEY `account_number` (`account_number`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_suppliers`
--

LOCK TABLES `ospos_suppliers` WRITE;
/*!40000 ALTER TABLE `ospos_suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_test`
--

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
  `test_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  PRIMARY KEY (`test_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101019 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_test`
--

LOCK TABLES `ospos_test` WRITE;
/*!40000 ALTER TABLE `ospos_test` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_total`
--

DROP TABLE IF EXISTS `ospos_total`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_total` (
  `total_id` int(10) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`total_id`),
  KEY `ospos_total_ibfk_1` (`sale_id`),
  KEY `total_id` (`total_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1171 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_total`
--

LOCK TABLES `ospos_total` WRITE;
/*!40000 ALTER TABLE `ospos_total` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_total` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_user_roles`
--

DROP TABLE IF EXISTS `ospos_user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_user_roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_user_roles`
--

LOCK TABLES `ospos_user_roles` WRITE;
/*!40000 ALTER TABLE `ospos_user_roles` DISABLE KEYS */;
INSERT INTO `ospos_user_roles` VALUES (2,2,7713),(4,4,8811),(6,6,8826),(26,2,8794),(28,6,8714),(29,3,8877),(32,1,8856),(41,3,8878),(42,6,8878),(43,5,8817),(47,1,1),(71,7,104583),(72,7,104582),(82,2,104580),(83,2,104581),(84,4,233094),(87,4,233349),(88,4,233036);
/*!40000 ALTER TABLE `ospos_user_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-08 21:18:21
