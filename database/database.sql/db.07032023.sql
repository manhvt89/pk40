-- MariaDB dump 10.19  Distrib 10.5.17-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ospos
-- ------------------------------------------------------
-- Server version	10.5.17-MariaDB-1:10.5.17+maria~ubu2004

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_app_config`
--

LOCK TABLES `ospos_app_config` WRITE;
/*!40000 ALTER TABLE `ospos_app_config` DISABLE KEYS */;
INSERT INTO `ospos_app_config` VALUES ('address','143 Tôn Đức Thắng - TP. Phan Thiết - Bình Thuận'),('barcode_content','number'),('barcode_first_row','name'),('barcode_font','Arial.ttf'),('barcode_font_size','10'),('barcode_generate_if_empty','1'),('barcode_height','20'),('barcode_num_in_row','1'),('barcode_page_cellspacing','20'),('barcode_page_width','100'),('barcode_quality','100'),('barcode_second_row','item_code'),('barcode_third_row','unit_price'),('barcode_type','Code128'),('barcode_width','220'),('client_id','675e673a-1518-4bc1-93e1-8eff41ebdade'),('company','CÔNG TY TNHH KÍNH MẮT NAM HẢI'),('company_logo','company_logo1.png'),('country_codes','vn'),('currency_decimals','0'),('currency_symbol','₫'),('custom10_name',''),('custom1_name',''),('custom2_name',''),('custom3_name',''),('custom4_name',''),('custom5_name',''),('custom6_name',''),('custom7_name',''),('custom8_name',''),('custom9_name',''),('dateformat','d/m/Y'),('default_sales_discount','0'),('default_tax_1_name','VAT'),('default_tax_1_rate','10'),('default_tax_2_name',''),('default_tax_2_rate',''),('default_tax_rate','8'),('email',''),('fax',''),('filter','KOREA\nNHẬP KHẨU\ntest 1\ngọng\n1T\nCARTIER\nGỌNG 1T\nGỌNG 2T\nGỌNG 3T\nGỌNG 4T\nGỌNG 5T\nGỌNG+5T'),('filter_contact_lens','NƯỚC NGÂM-NHỎ MẮT\nLENS 3 THÁNG TRẮNG\nLENS 3 THÁNG BROWN\nLENS 3 THÁNG GRAY\nLENS 3 THÁNG HONEY\nLENS 1DAY ALICA BROWN \nLENS 1DAY SUZY GRAY\nLENS 1DAY LATIN'),('filter_other',''),('filter_sun_glasses','M1T\nM2T\nM3T\nM4T\nM5T\nM0.5'),('iKindOfLens','1.56 CHEMI\n1.56 CHEMI U2\n1.56 CHEMI ASP PHOTO GRAY\n1.61 CHEMI Crystal U2\n1.67 CHEMI Crystal U2\n1.74 CHEMI UV400 ASP CRYSTAL U2 COATED\n1.56 KODAK\n1.60 UVBLUE KODAK\n1.60 KODAK FSV,UV400 Clean\'N\'CleAR\n1.67 KODAK FSV,UV400 Clean\'N\'CleAR\n1.67 KODAK FSV,UV400 Clean\'N\'CleAR\n1.56 POLAROID KHOI\n1.56 POLAROID XANH\nMẮT MÀU INDO\nMẮT LẺ KHÁC\n1.60 ESSILOR CRIZAL ALIZE UV\n1.60 NAHAmi SUPER HMC A+\n1.60 HOYA - NULUX SFT SV\n1.67 HOYA - NULUX SFT SV\n'),('invoice_default_comments',''),('invoice_email_message',''),('invoice_enable','0'),('language','english'),('language_code','en'),('lines_per_page','25'),('mailpath','/usr/sbin/sendmail'),('msg_msg',''),('msg_pwd',''),('msg_src',''),('msg_uid',''),('notify_horizontal_position','center'),('notify_vertical_position','top'),('number_locale','vi_VN'),('payment_options_order','cashdebitcredit'),('phone','9999 999 999'),('print_bottom_margin','0'),('print_footer','0'),('print_header','0'),('print_left_margin','0'),('print_right_margin','0'),('print_silently','0'),('print_top_margin','0'),('protocol','mail'),('quantity_decimals','0'),('receipt_printer','HP LaserJet Professional P1102'),('receipt_show_description','0'),('receipt_show_serialnumber','0'),('receipt_show_taxes','0'),('receipt_show_total_discount','1'),('receipt_template','receipt_default'),('receiving_calculate_average_price','1'),('recv_invoice_format','$CO'),('return_policy','Hệ thống truy cập vào tài nguyên'),('sales_invoice_format','$CO'),('smtp_crypto','ssl'),('smtp_port','465'),('smtp_timeout','5'),('statistics','1'),('takings_printer','HP LaserJet Professional P1102'),('tax_decimals','2'),('tax_included','1'),('theme','cerulean'),('thousands_separator','thousands_separator'),('timeformat','H:i:s'),('timezone','Asia/Bangkok'),('website','');
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
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_customers`
--

LOCK TABLES `ospos_customers` WRITE;
/*!40000 ALTER TABLE `ospos_customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_customers` ENABLE KEYS */;
UNLOCK TABLES;

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
  `daily_total_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  PRIMARY KEY (`daily_total_id`),
  KEY `sale_id` (`daily_total_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_daily_total`
--

LOCK TABLES `ospos_daily_total` WRITE;
/*!40000 ALTER TABLE `ospos_daily_total` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_daily_total` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_employees`
--

LOCK TABLES `ospos_employees` WRITE;
/*!40000 ALTER TABLE `ospos_employees` DISABLE KEYS */;
INSERT INTO `ospos_employees` VALUES ('admin','$2y$10$XIi4jJSzNVz0XaXaXxEwveaU2Fti9019AElA5ZY.mAftkuZEmmk/O',1,0,2,1,'0'),('adnvbh','$2y$10$Jb3W6Kk3tB9cLtJWyf26COWFnkhqGCJk3DEA905.V887Tu7q/StpC',6263,1,2,1,'0'),('Baocao','$2y$10$gzBBQ0F3v5xG0WN6dL/1x.DK0zPLE2jLpTvScx.vMjMozgjvTQi1C',8864,0,2,1,'0'),('bigboss','$2y$10$ZT8w3zDtwSb124ZUJ7mxWOw8n/I0LhcE2e85dXUfMvvRMbyDutuZW',8714,0,2,1,'0'),('DM001','$2y$10$w8PaMFUfiRAikeJcHQcfy.3ZI5QlRHTMsvXJOWGSC91diAE.yVPCy',9347,0,2,1,'0'),('DM002','$2y$10$8.AdZbMfcZKhjH6WsSmzwujmVpJV5P5TnfoqypiQmEn1AY5t2o15u',9348,0,2,1,'0'),('hoangluyen','$2y$10$xkHQJoBlYxAYKUq1TKrSAu3YUeeP0gmLO7taEkw8CryhXmtpfjvg2',4,1,2,1,'0'),('KHUC XA','$2y$10$UbZIqYswRrf5/Aq3/PXDhOsnxQewlAAgL4UwOSdXzwPGTXucMe5FW',8817,1,2,1,'0'),('khucxa','$2y$10$I5GubL3vEqQ1Tjl8nriRwuf1tOMezJ5hLO8Vc7Ho2LoVZw4VNNapS',9351,0,2,1,'0'),('khucxa1','$2y$10$6fxnrFlkLPljb70WmCSGK.K2ulkVULAn1p7Ygoa1XhMnUY5mhOw0S',9352,0,2,1,'0'),('khucxa2','$2y$10$Ie3ibHKn2XfrvbgCD6JMauH9ymrb2jQjU7m1AC5n5Dc0kvMwhkj7m',9353,0,2,1,'0'),('khucxa3','$2y$10$.aZX5hVq/80xTisx5KXNKeO2bb32gF0qsNZkIoWh/fm3aTHy0T0aS',9354,0,2,1,'0'),('lanchinh','$2y$10$6TdhNqB9ZR9a7aTehd6uS.GJjHGXQi5Bv9JI83bOaaAu3dzQhUaia',5467,1,2,1,'0'),('Lantrinh','$2y$10$p8xxvdQGHWUIe/e4Jr0/.O7s278c9XPdEQVzJuUSTpnBTCcMey69u',2980,1,2,1,'0'),('manhvt89','$2y$10$ZLGBLRSQF8PstAmtEVYnAu2D8VV2RlE2vZToIwHSDJtkySsdN8/Xa',7546,1,2,2,'0'),('nguyenlien','$2y$10$twsrUlNkF5c5xgUI2PeW9uELFo/6uD9yv7FxfSGFbBbwEk359qdvK',19,1,2,1,'0'),('nvtn01','$2y$10$giG.hQv5I3DMakUqrHPj6.l2hsWsSefua4BAS77pSkFkJ0EG6oO1S',9355,0,2,1,'0'),('nvtn02','$2y$10$W9.oBlIyUf72V5/X8pQsjuCwWXvjum2Y4PoqUIXb7DLh3D8rxOHGO',8826,0,2,1,'0'),('Phammai','$2y$10$kF59p9zzpjnsn/p1v1uTE.a0R08Rb50v/71.ZZEg4W9KaBmkDc74S',7713,1,2,1,'0'),('Quanlykho01','$2y$10$AW8ju.N4XwfDLRG5XKYG2.fyzRPuO2uVevwEIvmqPJu9pGEqxAd6C',9349,0,2,1,'0'),('Thuyhang','$2y$10$OrReRY3qWAstEnnzZ15bq.9N3PIbaO6PaIp.lEdZlgWCOK4HS796y',28,1,2,1,'0'),('tiendung','$2y$10$CUuxRgDD8Ox.dRtb.E2Afuq1uPohD7KFwdtPmS74cKL9c9p34NooO',5466,1,2,1,'0'),('TrucGiang','$2y$10$uyRgdazHcqoezHNge77b3upjrM.7uUeY.6b2WxqKwzS914HEskM8W',8794,1,2,1,'0'),('van.anh','$2y$10$gQTp6o4i6usaSzlGRRldU.jE.p.qvFDILWq4B7UwmAID5i3weB29C',8811,1,2,1,'0');
/*!40000 ALTER TABLE `ospos_employees` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_fields`
--

LOCK TABLES `ospos_fields` WRITE;
/*!40000 ALTER TABLE `ospos_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ospos_giftcards`
--

DROP TABLE IF EXISTS `ospos_giftcards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ospos_giftcards` (
  `record_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `giftcard_id` int(11) NOT NULL AUTO_INCREMENT,
  `giftcard_number` int(10) NOT NULL,
  `value` decimal(15,2) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `person_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`giftcard_id`),
  UNIQUE KEY `giftcard_number` (`giftcard_number`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_giftcards`
--

LOCK TABLES `ospos_giftcards` WRITE;
/*!40000 ALTER TABLE `ospos_giftcards` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_giftcards` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_grants`
--

LOCK TABLES `ospos_grants` WRITE;
/*!40000 ALTER TABLE `ospos_grants` DISABLE KEYS */;
INSERT INTO `ospos_grants` VALUES ('100',2),('100',3),('100',4),('100',5),('101',2),('101',3),('101',5),('102',1),('106',1),('106',2),('106',3),('106',5),('108',1),('112',1),('112',2),('112',3),('112',5),('112',7),('113',1),('113',7),('114',1),('114',2),('114',3),('114',7),('115',1),('115',2),('115',3),('115',5),('115',7),('116',1),('116',2),('116',3),('116',5),('116',7),('117',1),('117',2),('117',3),('117',4),('117',5),('118',1),('118',2),('118',3),('118',5),('119',1),('119',4),('119',5),('12',1),('12',2),('12',3),('12',5),('12',7),('120',1),('120',4),('120',5),('121',1),('121',4),('121',5),('127',1),('128',1),('129',1),('130',1),('131',1),('132',1),('132',4),('133',1),('133',4),('134',1),('134',4),('135',1),('135',4),('136',1),('137',1),('138',1),('17',1),('17',2),('17',3),('17',4),('17',5),('18',1),('18',2),('18',3),('18',4),('18',5),('19',1),('21',1),('21',4),('23',1),('23',2),('23',3),('23',4),('23',5),('24',1),('26',1),('26',2),('26',3),('26',4),('26',5),('26',8714),('27',1),('27',2),('27',3),('27',4),('27',5),('27',8714),('28',1),('28',4),('28',5),('28',8714),('29',1),('29',4),('29',5),('29',8714),('30',1),('30',2),('30',3),('30',4),('30',5),('30',8714),('31',1),('31',2),('31',3),('31',4),('31',5),('31',8714),('32',1),('32',4),('32',5),('32',8714),('33',1),('33',4),('33',5),('33',8714),('34',1),('34',2),('34',3),('34',4),('34',5),('34',8714),('35',1),('35',4),('35',5),('35',6),('35',8714),('36',1),('36',4),('36',5),('36',8714),('37',1),('37',4),('37',5),('37',8714),('4',1),('4',2),('4',3),('4',4),('4',5),('47',1),('47',4),('47',5),('47',7),('49',1),('49',5),('5',1),('5',2),('5',3),('5',4),('5',5),('51',1),('52',1),('52',5),('53',1),('53',5),('54',1),('54',2),('54',3),('54',4),('54',5),('55',1),('55',5),('56',1),('57',1),('58',1),('58',4),('60',1),('60',5),('61',1),('62',1),('62',5),('65',1),('65',5),('67',1),('67',5),('68',1),('68',2),('68',3),('68',4),('68',5),('68',6),('69',1),('69',2),('69',3),('69',4),('69',5),('70',1),('70',2),('70',3),('70',4),('70',5),('71',1),('71',2),('71',3),('71',4),('71',5),('77',1),('77',2),('77',3),('77',5),('78',1),('78',5),('78',7),('80',1),('81',1),('82',1),('83',1),('84',1),('85',1),('86',1),('87',1),('88',1),('89',1),('90',1),('91',1),('92',1),('93',1),('94',1),('95',1),('97',1),('97',4),('97',5),('99',1),('99',4),('99',5);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_history_points`
--

LOCK TABLES `ospos_history_points` WRITE;
/*!40000 ALTER TABLE `ospos_history_points` DISABLE KEYS */;
INSERT INTO `ospos_history_points` VALUES (1,10577,1639,'7a1711a6-ae6f-11ed-8174-005056847d3e',1676608811,98240.00,1,'+ 98240.00 boi 7a1711a6-ae6f-11ed-8174-005056847d3e'),(2,10867,1686,'52d01df8-b4cd-11ed-aaf9-005056847d3e',1677303070,236500.00,1,'+ 236500.00 boi 52d01df8-b4cd-11ed-aaf9-005056847d3e');
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
) ENGINE=InnoDB AUTO_INCREMENT=13437 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_inventory`
--

LOCK TABLES `ospos_inventory` WRITE;
/*!40000 ALTER TABLE `ospos_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_inventory` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_item_kit_items`
--

LOCK TABLES `ospos_item_kit_items` WRITE;
/*!40000 ALTER TABLE `ospos_item_kit_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `ospos_item_kit_items` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `item_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  `code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_number` (`item_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `unit_cost` (`unit_price`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6959 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
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
  FULLTEXT KEY `last_name` (`last_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10901 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_people`
--

LOCK TABLES `ospos_people` WRITE;
/*!40000 ALTER TABLE `ospos_people` DISABLE KEYS */;
INSERT INTO `ospos_people` VALUES ('admin','admin',1,'091301933','manhvt89@gmail.com','Address 1','HN','Hà Nội','HN','10001','Việt Nam','',1,'0',''),('Luyen','Hoang',1,'','','','HN','HN','HN','10001','Việt Nam','',4,'0',''),('Liên','Nguyễn',0,'01639221947','','','HN','Hà Nội','HN','10001','Việt Nam','',19,'0',''),('Hằng','Thúy',0,'0353889355','','','HN','Hà Nội','HN','10001','Việt Nam','',28,'0',''),('Trinh','Lan',0,'0335089232','','','HN','Hà Nội','HN','10001','Việt Nam','',2980,'0',''),('Dung','Tiên',0,'0859916662','','','HN','Hà Nội','HN','10001','Việt Nam','',5466,'0',''),('Chinh','Lan',0,'01667711834','','','HN','Hà Nội','HN','10001','Việt Nam','',5467,'0',''),('Thư','Minh',0,'','','','HN','Hà Nội','HN','10001','Việt Nam','',6263,'0',''),('Vũ Thành','Mạnh',1,'','','','HN','Hà Nội','HN','10001','Việt Nam','',7546,'0',''),('Thu Thảo','Ngô',0,'','','','HN','Hà Nội','HN','10001','Việt Nam','',7713,'0',''),('bv','boss',NULL,'','','','HN','Hà Nội','HN','10001','Việt Nam','',8714,'0',''),('Giang','Truc',0,'090909009','','50 trần đại nghĩa','HN','Hà Nội','HN','10001','Việt Nam','',8794,'0',''),('Anh','Vân',0,'','','','HN','Hà Nội','HN','10001','Việt Nam','',8811,'0',''),('Trọng','Vũ Huy',1,'0913019331','','','HN','Hà Nội','HN','10001','Việt Nam','',8817,'0',''),('02','Thu Ngân',1,'','','','HN','Hà Nội','HN','10001','Việt Nam','',8826,'0',''),('Cáo','Báo',1,'','','','HN','Hà Nội','HN','10001','Việt Nam','',8864,'0',''),('01','Đo Mắt',1,'','','','HN','Hà Nội','HN','10001','Việt Nam','',9347,'0',''),('02','Đo Mắt',1,'','','','HN','Hà Nội','HN','10001','Việt Nam','',9348,'0',''),('01','Quản Lý Kho',0,'','','','HN','Hà Nội','HN','10001','Việt Nam','',9349,'0',''),('Sy','Nguyen',1,'','','','HN','Hà Nội','HN','10001','Việt Nam','',9351,'0',''),('Sy','Nguyen',1,'','','','HN','Hà Nội','HN','10001','Việt Nam','',9352,'0',''),('Sy','Nguyen',1,'','','','HN','Hà Nội','HN','10001','Việt Nam','',9353,'0',''),('Sy','Nguyen',NULL,'','','','HN','Hà Nội','HN','10001','Việt Nam','',9354,'0',''),('01','Thu Ngân',0,'','','','HN','Hà Nội','HN','10001','Việt Nam','',9355,'0',''),('NAM HAI','KÍNH MẮT',NULL,'','','225 TRẦN ĐẠI NGHĨA - 50 TRẦN ĐẠI NGHĨA','HN','0','HN','10001','Việt Nam','',9357,'0','');
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
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_permissions`
--

LOCK TABLES `ospos_permissions` WRITE;
/*!40000 ALTER TABLE `ospos_permissions` DISABLE KEYS */;
INSERT INTO `ospos_permissions` VALUES ('account_manage','account',NULL,4,'account','a5664fbb-5cd9-11ed-b8c5-040300000000','0a63f505-5cd9-11ed-b8c5-040300000000',NULL),('account_view','account',NULL,5,'account','a5665085-5cd9-11ed-b8c5-040300000000','0a63f5c9-5cd9-11ed-b8c5-040300000000',NULL),('customers_view','customers',NULL,12,'customers','a5665202-5cd9-11ed-b8c5-040300000000','0a63f745-5cd9-11ed-b8c5-040300000000','Tạo mới khách hàng'),('items_accounting','items',NULL,17,'items','a5665b0b-5cd9-11ed-b8c5-040300000000','0a63f9fc-5cd9-11ed-b8c5-040300000000','Kế toán'),('items_stock','items',1,18,'items','a5665bc5-5cd9-11ed-b8c5-040300000000','0a63fb3f-5cd9-11ed-b8c5-040300000000','Kho'),('item_kits','item_kits',NULL,19,'item_kits','a5665c8f-5cd9-11ed-b8c5-040300000000','0a63fc32-5cd9-11ed-b8c5-040300000000',NULL),('order','order',NULL,21,'order','a5665ee5-5cd9-11ed-b8c5-040300000000','0a63fda6-5cd9-11ed-b8c5-040300000000',NULL),('receivings_stock','receivings',1,23,'receivings','a5665fa0-5cd9-11ed-b8c5-040300000000','0a63fe5c-5cd9-11ed-b8c5-040300000000',NULL),('reminders','reminders',1,24,'reminders','a566606e-5cd9-11ed-b8c5-040300000000','0a63ff2e-5cd9-11ed-b8c5-040300000000',NULL),('reports_categories','reports',NULL,26,'reports','a5666128-5cd9-11ed-b8c5-040300000000','0a63ffe6-5cd9-11ed-b8c5-040300000000',NULL),('reports_customers','reports',NULL,27,'reports','a56661d8-5cd9-11ed-b8c5-040300000000','0a64008e-5cd9-11ed-b8c5-040300000000',NULL),('reports_discounts','reports',NULL,28,'reports','a5666286-5cd9-11ed-b8c5-040300000000','0a64013c-5cd9-11ed-b8c5-040300000000',NULL),('reports_employees','reports',NULL,29,'reports','a5666334-5cd9-11ed-b8c5-040300000000','0a6401ec-5cd9-11ed-b8c5-040300000000',NULL),('reports_inventory','reports',NULL,30,'reports','a56663dc-5cd9-11ed-b8c5-040300000000','0a640293-5cd9-11ed-b8c5-040300000000',NULL),('reports_items','reports',NULL,31,'reports','a5666481-5cd9-11ed-b8c5-040300000000','0a64033c-5cd9-11ed-b8c5-040300000000',NULL),('reports_lens','reports',NULL,32,'reports','a5666523-5cd9-11ed-b8c5-040300000000','0a6403ea-5cd9-11ed-b8c5-040300000000',NULL),('reports_payments','reports',NULL,33,'reports','a56665ce-5cd9-11ed-b8c5-040300000000','0a640495-5cd9-11ed-b8c5-040300000000',NULL),('reports_receivings','reports',NULL,34,'reports','a566666e-5cd9-11ed-b8c5-040300000000','0a64056a-5cd9-11ed-b8c5-040300000000',NULL),('reports_sales','reports',NULL,35,'reports','a5666709-5cd9-11ed-b8c5-040300000000','0a640619-5cd9-11ed-b8c5-040300000000',NULL),('reports_suppliers','reports',NULL,36,'reports','a56667a8-5cd9-11ed-b8c5-040300000000','0a6406b5-5cd9-11ed-b8c5-040300000000',NULL),('reports_taxes','reports',NULL,37,'reports','a5666849-5cd9-11ed-b8c5-040300000000','0a640756-5cd9-11ed-b8c5-040300000000',NULL),('test_manage','test',NULL,47,'test','a5666d8a-5cd9-11ed-b8c5-040300000000','0a640c85-5cd9-11ed-b8c5-040300000000',NULL),('customers_index','customers',NULL,49,'customers','a5666ed7-5cd9-11ed-b8c5-040300000000','0a640dcb-5cd9-11ed-b8c5-040300000000','Danh sách khách hàng'),('customers_delete','customers',NULL,51,'customers','a5666f9e-5cd9-11ed-b8c5-040300000000','0a640e88-5cd9-11ed-b8c5-040300000000','Xóa'),('customers_excel_import','customers',NULL,52,'customers','a5667065-5cd9-11ed-b8c5-040300000000','0a640f42-5cd9-11ed-b8c5-040300000000','Nhập excel'),('customers_excel_import','customers',NULL,53,'customers','a5667124-5cd9-11ed-b8c5-040300000000','0a640ff6-5cd9-11ed-b8c5-040300000000','Nhập excel'),('items_index','items',NULL,54,'items','a56671e1-5cd9-11ed-b8c5-040300000000','0a6410ab-5cd9-11ed-b8c5-040300000000','Danh sách sản phẩm'),('items_excel_import','items',NULL,55,'items','a5667293-5cd9-11ed-b8c5-040300000000','0a64115a-5cd9-11ed-b8c5-040300000000','Nhập excel'),('items_delete','items',NULL,56,'items','a5667349-5cd9-11ed-b8c5-040300000000','0a64120a-5cd9-11ed-b8c5-040300000000','Xóa sản phẩm'),('items_bulk_update','items',NULL,57,'items','a56673fb-5cd9-11ed-b8c5-040300000000','0a6412bb-5cd9-11ed-b8c5-040300000000','Cập nhật nhiều sản phẩm'),('items_save_inventory','items',NULL,58,'items','a56674b2-5cd9-11ed-b8c5-040300000000','0a641368-5cd9-11ed-b8c5-040300000000','Lưu vào kho'),('items_bulk_edit','items',NULL,60,'items','a566756e-5cd9-11ed-b8c5-040300000000','0a64141d-5cd9-11ed-b8c5-040300000000','Chỉnh sửa hàng loạt'),('items_inventory','items',NULL,61,'items','a5667623-5cd9-11ed-b8c5-040300000000','0a6414c7-5cd9-11ed-b8c5-040300000000','Kho'),('items_view','items',NULL,62,'items','a56676ca-5cd9-11ed-b8c5-040300000000','0a641566-5cd9-11ed-b8c5-040300000000','Tạo mới'),('suppliers_view','suppliers',NULL,65,'suppliers','a5667770-5cd9-11ed-b8c5-040300000000','0a641606-5cd9-11ed-b8c5-040300000000','Tạo mới'),('suppliers_delete','suppliers',NULL,67,'suppliers','a5667834-5cd9-11ed-b8c5-040300000000','0a6416b2-5cd9-11ed-b8c5-040300000000','Xóa'),('reports_index','reports',NULL,68,'reports','a56678f0-5cd9-11ed-b8c5-040300000000','0a64176a-5cd9-11ed-b8c5-040300000000','Danh sách báo cáo'),('receivings_index','receivings',NULL,69,'receivings','a56679b0-5cd9-11ed-b8c5-040300000000','0a64181c-5cd9-11ed-b8c5-040300000000','Danh sách '),('receivings_lens','receivings',NULL,70,'receivings','a5667a61-5cd9-11ed-b8c5-040300000000','0a6418ca-5cd9-11ed-b8c5-040300000000','Nhập tròng kính'),('receivings_view','receivings',NULL,71,'receivings','a5667b14-5cd9-11ed-b8c5-040300000000','0a641979-5cd9-11ed-b8c5-040300000000','Nhập hàng'),('sales_index','sales',NULL,77,'sales','a5667f5c-5cd9-11ed-b8c5-040300000000','0a641d91-5cd9-11ed-b8c5-040300000000','Bán hàng (tạo mới)'),('test_index','test',NULL,78,'test','a566804a-5cd9-11ed-b8c5-040300000000','0a641e2f-5cd9-11ed-b8c5-040300000000','Danh sách đơn kính'),('config_index','config',NULL,80,'config','a56681c7-5cd9-11ed-b8c5-040300000000','0a641f9c-5cd9-11ed-b8c5-040300000000','Danh sách'),('roles_index','roles',NULL,81,'roles','a5668293-5cd9-11ed-b8c5-040300000000','0a64204b-5cd9-11ed-b8c5-040300000000','Nhóm quyền'),('roles_create','roles',NULL,82,'roles','a5668351-5cd9-11ed-b8c5-040300000000','0a6420f6-5cd9-11ed-b8c5-040300000000','Tạo nhóm quyền'),('roles_view','roles',NULL,83,'roles','a5668403-5cd9-11ed-b8c5-040300000000','0a6421a5-5cd9-11ed-b8c5-040300000000','Xem nhóm quyền'),('roles_edit','roles',NULL,84,'roles','a56684c1-5cd9-11ed-b8c5-040300000000','0a642255-5cd9-11ed-b8c5-040300000000','Sửa nhóm quyền'),('roles_per_index','roles',NULL,85,'roles','a5668583-5cd9-11ed-b8c5-040300000000','0a642306-5cd9-11ed-b8c5-040300000000','Quyền'),('roles_per_add','roles',NULL,86,'roles','a5668636-5cd9-11ed-b8c5-040300000000','0a6423ae-5cd9-11ed-b8c5-040300000000','Thêm quyền'),('roles_per_view','roles',NULL,87,'roles','a56686d4-5cd9-11ed-b8c5-040300000000','0a642449-5cd9-11ed-b8c5-040300000000','Xem quyền'),('roles_per_edit','roles',NULL,88,'roles','a566877f-5cd9-11ed-b8c5-040300000000','0a6424ea-5cd9-11ed-b8c5-040300000000','Sửa quyền'),('roles_mod_index','roles',NULL,89,'roles','a566882e-5cd9-11ed-b8c5-040300000000','0a642589-5cd9-11ed-b8c5-040300000000','Danh sách mô đun'),('roles_mod_add','roles',NULL,90,'roles','a56688d4-5cd9-11ed-b8c5-040300000000','0a642624-5cd9-11ed-b8c5-040300000000','Thêm mô đun'),('roles_mod_view','roles',NULL,91,'roles','a566896f-5cd9-11ed-b8c5-040300000000','0a6426be-5cd9-11ed-b8c5-040300000000','Xem mô đun'),('roles_mod_edit','roles',NULL,92,'roles','a5668a19-5cd9-11ed-b8c5-040300000000','0a64275c-5cd9-11ed-b8c5-040300000000','Sửa mô đun'),('employees_index','employees',NULL,93,'employees','a5668ac8-5cd9-11ed-b8c5-040300000000','0a6427fe-5cd9-11ed-b8c5-040300000000','Danh sách nhân viên'),('employees_view','employees',NULL,94,'employees','a5668b7c-5cd9-11ed-b8c5-040300000000','0a6428ab-5cd9-11ed-b8c5-040300000000','Tạo mới'),('employees_delete','employees',NULL,95,'employees','a5668c2d-5cd9-11ed-b8c5-040300000000','0a64295a-5cd9-11ed-b8c5-040300000000','Xóa'),('items_count_details','items',NULL,97,'items','a5668db5-5cd9-11ed-b8c5-040300000000','0a642abe-5cd9-11ed-b8c5-040300000000','Xem chi tiết sản phẩm trong kho'),('suppliers_index','suppliers',NULL,99,'suppliers','a5668e77-5cd9-11ed-b8c5-040300000000','0a642b7c-5cd9-11ed-b8c5-040300000000','Danh sách nhà cung cấp'),('items_unitprice_hide','items',NULL,100,'items','6b284764-5cde-11ed-b8c5-040300000000','6b28476f-5cde-11ed-b8c5-040300000000','Ẩn giá nhập'),('customers_phonenumber_hide','customers',1,101,'customers','fe2fc7fc-5ce9-11ed-b8c5-040300000000','fe2fc804-5ce9-11ed-b8c5-040300000000',''),('sales_price_edit','sales',1,102,'sales','a974fae3-5d00-11ed-b8c5-040300000000','a974faeb-5d00-11ed-b8c5-040300000000','Cho phép thay đổi giá'),('sales_manage','sales',NULL,106,'sales','2f9353b8-5d0f-11ed-b8c5-040300000000','2f9353c3-5d0f-11ed-b8c5-040300000000','Danh sách đơn hàng'),('sales_delete','sales',1,108,'sales','9422bf13-5d0f-11ed-b8c5-040300000000','9422bf1d-5d0f-11ed-b8c5-040300000000',''),('test_detail_test','test',1,112,'test','25460ad1-5d10-11ed-b8c5-040300000000','25460ada-5d10-11ed-b8c5-040300000000',''),('test_delete','test',1,113,'test','314c3e14-5d10-11ed-b8c5-040300000000','314c3e1c-5d10-11ed-b8c5-040300000000',''),('test_edit','test',1,114,'test','31dca20f-5d10-11ed-b8c5-040300000000','31dca218-5d10-11ed-b8c5-040300000000',''),('test_view','test',1,115,'test','37a58d56-5d10-11ed-b8c5-040300000000','37a58d63-5d10-11ed-b8c5-040300000000',''),('test_view_test','test',1,116,'test','3f3976cd-5d10-11ed-b8c5-040300000000','3f3976d4-5d10-11ed-b8c5-040300000000',''),('account_index','account',1,117,'account','5776d5d3-5d10-11ed-b8c5-040300000000','5776d5dd-5d10-11ed-b8c5-040300000000',''),('customer_info_index','customer_info',1,118,'customer_info','6a9e1d60-5d10-11ed-b8c5-040300000000','6a9e1d69-5d10-11ed-b8c5-040300000000',''),('barcodes_index','barcodes',NULL,119,'barcodes','af1f3265-7196-11ed-8174-005056847d3e','af1f326a-7196-11ed-8174-005056847d3e','Quản lý Barcode'),('items_generate_barcodes','items',NULL,120,'items','8a2bee25-7337-11ed-8174-005056847d3e','8a2bee2b-7337-11ed-8174-005056847d3e','Tao Barcode'),('account_admin','account',NULL,121,'account','8909405c-8dd8-11ed-8174-005056847d3e','89094066-8dd8-11ed-8174-005056847d3e','Quản lý'),('sales_view','sales',1,122,'sales','86b95060-8fd1-11ed-8174-005056847d3e','86b95068-8fd1-11ed-8174-005056847d3e',''),('roles___construct','roles',1,126,'roles','8207e17b-90af-11ed-8174-005056847d3e','8207e182-90af-11ed-8174-005056847d3e',''),('roles_ajax_load_actions_by_module','roles',1,127,'roles','82a3280b-90af-11ed-8174-005056847d3e','82a32812-90af-11ed-8174-005056847d3e',''),('roles_mod_save','roles',1,128,'roles','838a784b-90af-11ed-8174-005056847d3e','838a7854-90af-11ed-8174-005056847d3e',''),('roles_mod_update','roles',1,129,'roles','83f30f70-90af-11ed-8174-005056847d3e','83f30f76-90af-11ed-8174-005056847d3e',''),('roles_ajax_load_actions_by_module','roles',1,130,'roles','8a3c6969-90af-11ed-8174-005056847d3e','8a3c6970-90af-11ed-8174-005056847d3e',''),('roles_mod_save','roles',1,131,'roles','0d07aca6-90b1-11ed-8174-005056847d3e','0d07acd3-90b1-11ed-8174-005056847d3e',''),('purchases_index','purchases',1,132,'purchases','bf3cd23b-a952-11ed-a343-040300000000','bf3cd249-a952-11ed-a343-040300000000',''),('purchases_lens','purchases',NULL,133,'purchases','e2503968-a953-11ed-a343-040300000000','e2503975-a953-11ed-a343-040300000000','Nhập từ bảng'),('purchases_excel','purchases',NULL,134,'purchases','f990e87e-a953-11ed-a343-040300000000','f990e889-a953-11ed-a343-040300000000','Nhập từ Excel'),('purchases_manage','purchases',1,135,'purchases','1a1fcc7e-a954-11ed-a343-040300000000','1a1fcc88-a954-11ed-a343-040300000000',''),('purchases_editpurchase','purchases',NULL,136,'purchases','8eb6b2a5-a963-11ed-a343-040300000000','8eb6b2af-a963-11ed-a343-040300000000','quyền chỉnh sửa'),('purchases_approve','purchases',NULL,137,'purchases','9919fe10-a963-11ed-a343-040300000000','9919fe1c-a963-11ed-a343-040300000000','quyền phê duyệt'),('purchases_send','purchases',NULL,138,'purchases','a189588b-a963-11ed-a343-040300000000','a1895897-a963-11ed-a343-040300000000','quyền gửi đi');
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
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
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
  `confirm` tinyint(1) NOT NULL DEFAULT 0,
  `sale_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  PRIMARY KEY (`sale_id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`),
  KEY `sale_time` (`sale_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1722 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=1394 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_short_survey`
--

LOCK TABLES `ospos_short_survey` WRITE;
/*!40000 ALTER TABLE `ospos_short_survey` DISABLE KEYS */;
INSERT INTO `ospos_short_survey` VALUES (1,10577,1639,'7a1711a6-ae6f-11ed-8174-005056847d3e',0,0,1676608811,5,5,5),(2,10867,1686,'52d01df8-b4cd-11ed-aaf9-005056847d3e',0,0,1677303070,5,5,5);
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
) ENGINE=InnoDB AUTO_INCREMENT=2219 DEFAULT CHARSET=utf8;
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
  `location_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_stock_locations`
--

LOCK TABLES `ospos_stock_locations` WRITE;
/*!40000 ALTER TABLE `ospos_stock_locations` DISABLE KEYS */;
INSERT INTO `ospos_stock_locations` VALUES (1,'91TD',0,'','','','',0,'54061bb3-abc7-11ed-8174-005056847d3e'),(2,'TC',1,'','','','',0,'54061c61-abc7-11ed-8174-005056847d3e'),(3,'157CL',1,'','','','',0,'54061d13-abc7-11ed-8174-005056847d3e');
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
  `supplier_uuid` varchar(250) NOT NULL DEFAULT uuid(),
  PRIMARY KEY (`supplier_id`),
  UNIQUE KEY `account_number` (`account_number`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_suppliers`
--

LOCK TABLES `ospos_suppliers` WRITE;
/*!40000 ALTER TABLE `ospos_suppliers` DISABLE KEYS */;
INSERT INTO `ospos_suppliers` VALUES (9357,'KÍNH MẮT NAM HẢI - HÀ NỘI','KÍNH MẮT NAM HẢI - HÀ NỘI',NULL,0,'','','',1,'54073982-abc7-11ed-8174-005056847d3e');
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
) ENGINE=InnoDB AUTO_INCREMENT=1267 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=2085 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
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
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ospos_user_roles`
--

LOCK TABLES `ospos_user_roles` WRITE;
/*!40000 ALTER TABLE `ospos_user_roles` DISABLE KEYS */;
INSERT INTO `ospos_user_roles` VALUES (2,2,7713),(4,4,8811),(26,2,8794),(28,6,8714),(29,3,8877),(32,1,8856),(41,3,8878),(42,6,8878),(43,5,8817),(47,1,1),(60,4,233036),(71,7,104583),(72,7,104582),(82,2,104580),(83,2,104581),(84,4,233094),(87,4,233349),(94,7,9352),(95,7,9353),(96,7,9354),(103,2,9355),(107,3,8826),(111,7,9347),(112,7,9348),(124,7,9351),(128,4,9349);
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

-- Dump completed on 2023-03-07 16:39:11
