-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2024 at 11:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sales_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` char(36) NOT NULL COMMENT 'รหัสหมวดหมู่ (UUID)',
  `service_category` varchar(255) NOT NULL COMMENT 'หมวดหมู่บริการ',
  `category` varchar(255) NOT NULL COMMENT 'หมวดหมู่',
  `sub_category` varchar(255) DEFAULT NULL COMMENT 'หมวดหมู่ย่อย',
  `problems` text DEFAULT NULL COMMENT 'ปัญหา',
  `cases` text DEFAULT NULL COMMENT 'กรณีศึกษา',
  `resolve` text DEFAULT NULL COMMENT 'การแก้ไข',
  `image_id` char(36) DEFAULT NULL COMMENT 'รหัสรูปภาพ (เชื่อมโยงกับตาราง Category_image)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด',
  `created_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `service_category`, `category`, `sub_category`, `problems`, `cases`, `resolve`, `image_id`, `created_at`, `updated_at`, `created_by`) VALUES
('86c853e966365809ea11581594569399', 'Notebook', 'Monitor', 'Edit', 'จอแตก', 'ตกแตก', 'เครม', NULL, '2024-10-06 17:01:49', '2024-10-06 17:07:06', '2'),
('cHErWjdqY25XRjhoQmhxREpSVnJ3QT09', 'เครือข่าย', 'การเชื่อมต่อเครือข่าย', 'LAN', 'การเชื่อมต่อล้มเหลว ', 'ผู้ใช้ไม่สามารถเข้าถึงอินเทอร์เน็ต', 'ตรวจสอบการตั้งค่า IP และรีสตาร์ทอุปกรณ์', NULL, '2024-10-04 18:35:54', '2024-10-06 16:54:39', '2');

--
-- Triggers `category`
--
DELIMITER $$
CREATE TRIGGER `before_insert_category` BEFORE INSERT ON `category` FOR EACH ROW BEGIN
    -- ถ้าไม่ได้กำหนดค่า id ให้สร้าง UUID ใหม่
    IF NEW.id IS NULL THEN
        SET NEW.id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `category_image`
--

CREATE TABLE `category_image` (
  `id` char(36) NOT NULL COMMENT 'รหัสรูปภาพ (UUID)',
  `file_name` varchar(255) NOT NULL COMMENT 'ชื่อไฟล์',
  `file_path` varchar(255) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ประเภทไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด',
  `category_id` char(36) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `upload_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `category_image`
--
DELIMITER $$
CREATE TRIGGER `before_insert_category_image` BEFORE INSERT ON `category_image` FOR EACH ROW BEGIN
    -- ถ้าไม่ได้กำหนดค่า id ให้สร้าง UUID ใหม่
    IF NEW.id IS NULL THEN
        SET NEW.id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` char(36) NOT NULL COMMENT '\r\nรหัสลูกค้า (UUID',
  `customer_name` varchar(255) NOT NULL COMMENT 'ชื่อลูกค้า',
  `company` varchar(255) DEFAULT NULL COMMENT 'ชื่อบริษัท',
  `address` mediumtext DEFAULT NULL COMMENT 'ที่อยู่',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `email` varchar(255) DEFAULT NULL COMMENT 'อีเมล',
  `remark` mediumtext DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` char(36) NOT NULL,
  `customers_image` varchar(255) DEFAULT NULL COMMENT 'รูปบริษัทลูกค้า Logo',
  `office_phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์หน่วยงาน',
  `extension` varchar(10) DEFAULT NULL COMMENT 'เบอร์ต่อ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัพเดทล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `company`, `address`, `phone`, `email`, `remark`, `created_by`, `created_at`, `updated_by`, `customers_image`, `office_phone`, `extension`, `updated_at`) VALUES
('1fb0fb81-4482-438a-ab66-5472c52bf9e4', 'องค์การบริหารส่วนจังหวัดชลบุรี', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:38:21', '', '', '', '', '2024-11-04 03:38:21'),
('2621ade4-bbfa-474f-a74d-fcb04d70f2eb', 'คุณตรีเทศ หะหวัง', 'บริษัท โทรคมนาคมแห่งชาติ จำกัด', '99 ถนนแจ้งวัฒนะ แขวงทุ่งสองห้อง เขตหลักสี่ กรุงเทพมหานคร 10210', '089-482-2387', 'treeted@nt.ntplc.co.th', '', '3', '2024-10-15 21:52:58', '3', '671244b41c0cb.jpg', '', '', '2024-10-18 04:21:24'),
('32104ee7-4b28-400b-bb7b-1ab55e1cf19d', 'นายสิรวิชฐ์ อำไพวงษ์ (ท่านนายก)', 'องค์การบริหารส่วนตำบลบ่อวิน', 'องค์การบริหารส่วนตำบลบ่อวิน เลขที่ 1 หมู่ที่ 6 ตำบลบ่อวิน อำเภอศรีราชา จังหวัดชลบุรี 20230 โทรศัพท์ 0-3834-5949 ,0-3834-5918 โทรสาร 0-3834-6116 สายด่วนร้องทุกข์ 24 ชม. 08-1949-7771 นายกเทศบาลตาบลบ่อวิน องค์การบริหารส่วนตำบลบ่อวิน', '038345949', 'admin@bowin.go.th', '', '3', '2024-10-11 23:26:14', '', NULL, NULL, NULL, '2024-10-17 19:18:35'),
('34ea3368-fa1c-445a-aeb8-821c87086d3a', 'นงนุช โกวิทวณิช', 'BUSINESS SOLUTIONS PROVIDER CO.,LTD.', '7/129 18th Floor., Baromrajchonnee Rd.,Arunammarin, Bangkok-Noi, Bangkok. 10700', '0619522110', 'nongnuch@bspc.co.th', '', '3', '2024-10-17 04:45:13', '2', NULL, NULL, NULL, '2024-10-17 19:18:35'),
('5e2a838a-110f-48bc-9518-f01a7066955b', 'นายอิทธิกร เล่นวารี  (นายกเทศมนตรีตำบลปากท่อ)', 'สำนักงานเทศบาลตำบลปากท่อ จ. ราชบุรี', '39 หมู่ที่ 7 ต.ปากท่อ อ.ปากท่อ จ. ราชบุรี 70140 โทรศัพท์ 032-281-266 โทรสาร 032-282-564', '0806508585', 'pakthocity@hotmail.com', 'http://www.pakthomunic.go.th/office.php', '2', '2024-10-12 06:24:53', '3', '6715036cbd552.jpg', '', '', '2024-10-20 13:19:40'),
('69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 'Onpailin Poomsiriroj', 'Master Maker Co.,Ltd.', '274/3 Soi Rungruang, Suthisarnvinichai Road, Samsennok, Huaykwang, Bangkok 10310', '0863696540', 'onpailin@mastermakerth.com', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:51:20', '', '', '', '', '2024-10-31 21:51:20'),
('cea804cd-55ab-4a3f-b9ff-a942547402a7', 'Siripong Siriprasert', 'Supreme Distribution Public Company Limited', '2/1 Soi Praditmanutham 5, Praditmanutham Road, Tha Raeng, Bang Khen, Bangkok 10230', '0651962456', 'siripong.s@supreme.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 20:59:42', '', '', '', '', '2024-10-31 20:59:42'),
('dd7d359f-6c63-4c11-80c5-d4dfa7407c92', 'Naruemon Rayayoy', 'Master Maker Co.,Ltd.', '274/3 Soi Rungruang, Suthisarnvinichai Road, Samsennok, Huaykwang, Bangkok 10310', '0629829978', 'naruemon@mastermakerth.com', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 20:55:45', '9223372036854775807', '', '', '', '2024-10-31 21:31:25'),
('f004cbe4-f666-4de7-8e85-7f940b6d8393', 'Kanitnicha Charoenpattanaphak', 'Business Solutions Provider Co.,Ltd.', '', '0957965498', 'Kanitnicha@bspc.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:30:43', '', '', '', '', '2024-10-31 22:30:43'),
('f313a7ba-64ae-4d61-af99-f493a98039b2', 'Adiphol Sermphol', 'Supreme Distribution Public Company Limited', '2/1 Soi Praditmanutham 5, Praditmanutham Road, Tha Raeng, Bang Khen, Bangkok 10230', '0814847928', 'adiphol.s@supreme.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:53:06', '', '', '', '', '2024-10-31 21:53:06'),
('fda15ece-1a00-4583-b354-cb5f3c01bb23', 'ศาลาว่าการเมืองพัทยา', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 02:48:07', '0', '', '', '', '2024-11-04 03:38:49');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` char(36) NOT NULL,
  `product_name` varchar(255) NOT NULL COMMENT 'ชื่อสินค้า',
  `product_description` mediumtext DEFAULT NULL COMMENT 'รายละเอียดสินค้า',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้อัพเดทข้อมูลล่าสุด',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันอัพเดทข้อมูลล่าสุด',
  `main_image` varchar(255) DEFAULT NULL COMMENT 'รูปหลักของสินค้า'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `created_by`, `created_at`, `updated_by`, `updated_at`, `main_image`) VALUES
('075afde8-650f-4d75-b73d-f41242854682', 'Software Devlopment', 'การพัฒนาระบบตามความต้องการของลูกค้า', '2', '2024-10-11 23:18:35', '3', '2024-10-12 02:47:45', ''),
('162fd42b-855e-40ac-8696-0d0535fbe2b1', 'Implementation', '', '2', '2024-11-01 00:09:37', NULL, '2024-11-01 00:09:37', NULL),
('3224e7a4-44ee-40ad-a6ac-22305c2b01eb', 'Smart Healthcare', 'ชุดกระเป๋า (Health Kit Set) สำหรับตรวจสุขคัดกรอกสถานะสุขภาพเคลื่อนที่ เก็บค่าข้อมูลเข้าระบบ โดยการตรวจวัดค่าจากอุปกรณ์เชื่อมต่อเข้ากับระบบ', '2', '2024-10-11 22:58:23', NULL, '2024-10-11 23:52:54', ''),
('3431f4cb-f892-4e08-a9af-240a743ebc25', 'Smart Safety', 'งานเกี่ยวกับกล้องโทรทัศน์วงจรปิด\r\nและงานสายใยแก้วนำแสง\r\nรวมถึงซ่อมแซม CCTV', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:26:48', NULL, '2024-11-04 03:26:48', NULL),
('4c85d842-54f3-4f06-87e6-553f81488234', 'Smart Emergency', 'ระบบเฝ้าระวังเหตุฉุกเฉิน', '2', '2024-10-12 06:18:20', '3', '2024-10-20 13:35:30', '4c85d842-54f3-4f06-87e6-553f81488234.png'),
('581f6ca7-8e1e-447a-9dae-680755c4fd29', 'Installation', 'งานจ้างเหมาติดตั้งโครงการฯ', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:15:31', NULL, '2024-11-04 03:15:31', NULL),
('7defdc10-75d8-4433-8b4f-0eeba38b674f', 'BioIDM Face Scan', 'ระบบยืนยันตัวตน ผ่านการเปรียบเทียบใบหน้า บัตรประจำตัวประชาชน และอื่นๆ', '2', '2024-10-11 23:18:48', NULL, '2024-10-11 23:52:54', ''),
('ae10bae3-0b1c-419f-8b21-8c57c607d3de', 'MA', '', '3', '2024-10-15 21:56:31', NULL, '2024-10-15 21:56:31', ''),
('b9fcda13-e694-4e04-a8df-fdf27ee08979', 'IBOC', 'มหาวิทยาลัยขอนแก่น', '2', '2024-10-11 23:19:12', '3', '2024-10-11 23:54:16', ''),
('df374787-e96c-4d3c-8089-3867edd96cf4', 'Project Management', '', '3', '2024-10-31 21:59:27', NULL, '2024-10-31 21:59:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_documents`
--

CREATE TABLE `product_documents` (
  `id` char(36) NOT NULL COMMENT 'รหัสเอกสาร (UUID)',
  `product_id` char(36) DEFAULT NULL COMMENT 'รหัสสินค้า (เชื่อมโยงกับตาราง products)',
  `document_type` enum('presentation','specification','manual','other') DEFAULT NULL COMMENT 'ประเภทเอกสาร',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'ที่อยู่ของไฟล์',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (bytes)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้าง',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัพเดทล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัพเดทล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บเอกสารและไฟล์ที่เกี่ยวข้องกับสินค้า';

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` char(36) NOT NULL COMMENT 'รหัสรูปภาพ (UUID)',
  `product_id` char(36) NOT NULL COMMENT 'รหัสสินค้า (เชื่อมโยงกับตาราง products)',
  `image_path` varchar(255) DEFAULT NULL COMMENT 'ที่อยู่ของรูปภาพ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้าง',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัพเดทล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัพเดทล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บรูปภาพเพิ่มเติมของสินค้า';

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` char(36) NOT NULL COMMENT 'รหัสโปรเจคต์ (UUID)',
  `project_name` varchar(255) NOT NULL COMMENT 'ชื่อโปรเจคต์',
  `start_date` date DEFAULT NULL COMMENT 'วันที่เริ่มโปรเจคต์',
  `end_date` date DEFAULT NULL COMMENT 'วันสิ้นสุดโปรเจคต์',
  `status` varchar(50) DEFAULT NULL COMMENT 'สถานะของโปรเจคต์',
  `contract_no` varchar(50) DEFAULT NULL COMMENT 'หมายเลขสัญญา',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `sales_date` date DEFAULT NULL COMMENT 'วันที่สิ้นสุดขาย',
  `seller` char(36) DEFAULT NULL COMMENT 'รหัสผู้ขาย',
  `sale_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายไม่รวมภาษี',
  `sale_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายรวมภาษี',
  `cost_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนไม่รวมภาษี',
  `cost_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนรวมภาษี',
  `gross_profit` decimal(10,2) DEFAULT NULL COMMENT 'กำไรขั้นต้น',
  `potential` decimal(5,2) DEFAULT NULL COMMENT 'กำไรขั้นต้นแบบเปอร์เซ็นต์',
  `es_sale_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายที่คาดการณ์ (ไม่รวมภาษี)',
  `es_cost_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนที่คาดการณ์ (ไม่รวมภาษี)',
  `es_gp_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'กำไรที่คาดการณ์ (ไม่รวมภาษี)',
  `customer_id` char(36) DEFAULT NULL COMMENT 'รหัสลูกค้า (เชื่อมโยงกับตาราง customers)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขข้อมูลล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่แก้ไขข้อมูล',
  `product_id` char(36) DEFAULT NULL COMMENT 'รหัสสินค้า (เชื่อมโยงกับตาราง products)',
  `vat` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่ม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางโปรเจคต์';

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('04b15a59-91a2-4bab-9dd1-6366c49a06d2', 'จ้างซ่อมแซมท่อร้อยสายใต้ดินและสายใยแก้สนำแสงของระบบกล้องโทรทัศน์วงจรปิด จำนวน 2 รายการ', '2024-10-01', '2025-01-28', 'Win', '400/2567', '', '2024-09-30', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 4074766.36, 4360000.00, 1085500.00, 1161485.00, 2989266.36, 73.36, 4074766.36, 1085500.00, 2989266.36, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:10:34', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:43:30', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('05c427ba-81af-4873-9e10-df57427e8305', 'ระบบฌาปนกิจสงเคราะห์ สตช.', '2024-09-30', '2024-12-25', 'Win', '', 'Sangfor HCI, Netowrk, Microsft License', '2024-08-19', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 2850000.00, 3049500.00, 1982863.87, 2121664.34, 867136.13, 30.43, 2850000.00, 1982863.87, 867136.13, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:22:34', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-11-01 00:12:50', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('05d29d2b-39ab-4c46-b34b-801ede800172', 'โครงการพัฒนางานระบบจัดซื้อจัดจ้าง  กบข.', '2023-12-25', '2024-12-25', 'Win', 'PO2024012', '', '2023-10-20', '3', 3200000.00, 3424000.00, 2650000.00, 2835500.00, 550000.00, 17.19, 3200000.00, 2650000.00, 550000.00, '34ea3368-fa1c-445a-aeb8-821c87086d3a', '2024-10-17 04:53:37', '3', '2024-10-17 04:53:37', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('0b23febb-a6b0-4897-99b0-f181f3dfe903', 'MA DLD Server', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 160000.00, 171200.00, 88804.49, 95020.80, 71195.51, 44.50, 160000.00, 88804.49, 71195.51, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:44:06', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:44:06', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('104d9772-4091-4b50-bad7-b89e445cdada', 'NID DLD', '2024-08-08', '2024-10-08', 'Win', '', 'Dell Server, Microsoft License', '2024-08-05', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 541000.00, 578870.00, 357230.65, 382236.80, 183769.35, 33.97, 541000.00, 357230.65, 183769.35, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:25:01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-11-01 00:12:37', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('22b13d95-688f-4c84-8012-f08793f2103d', 'MA DLD NSW5', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 230000.00, 246100.00, 4160.00, 4451.20, 225840.00, 98.19, 230000.00, 4160.00, 225840.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:41:44', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:41:44', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('2c2f0090-5f59-46be-a426-e426fde826df', 'MA DLD Regislive', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 500000.00, 535000.00, 89450.00, 95711.50, 410550.00, 82.11, 500000.00, 89450.00, 410550.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:42:55', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:42:55', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('3bde2447-a24c-4b11-933c-5a5160e902f3', 'จ้างปรับปรุงและเพิ่มประสิทธิภาพระบบสายนำสัญญาณใยแก้วนำแสงแบบฝั่งใต้ดิน ระยะที่6', '2024-05-31', '2024-11-26', 'Win', '256/2567', '', '2024-05-30', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 32383177.57, 34650000.00, 16562000.00, 17721340.00, 15821177.57, 48.86, 32383177.57, 16562000.00, 15821177.57, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:03:57', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:43:19', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('464369d4-5720-4b09-9834-8e46884ab187', 'งานติดตั้งสายสัญญาณคอมพิวเตอร์ และย้ายห้องควบคุมระบบคอมพิวเตอร์ (GEN)', '2024-10-25', '2025-04-26', 'Win', '00/2568', '', '2024-10-25', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 1860000.00, 1990200.00, 1335834.00, 1429342.38, 524166.00, 28.18, 1860000.00, 1335834.00, 524166.00, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-04 03:49:25', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 04:12:19', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('4677b262-6fc0-4bc7-8708-a0806b091577', 'จัดซื้อจอภาพและระบบการแสดงผลภาพมัลติมีเดียแบบเชื่อมต่อกัน (Video Wall Display)', '2024-05-09', '2024-10-06', 'Win', '234/2567', '', '2024-04-09', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 3128037.38, 3347000.00, 0.00, 0.00, 0.00, 0.00, 3128037.38, 0.00, 3128037.38, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:00:40', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:43:12', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('476239df-f7ca-4a7c-9dc2-29e358ba7182', 'โครงการจ้างระบบแพลตฟอร์มวิเคราะห์ข้อมูล ปัญญาประดิษฐ์ในการบริการดูแลการใช้ชีวิตและดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุและผู้ที่มีภาวะพึ่งพิง', '2024-09-27', '2025-09-09', 'Win', '๖/๒๕๖๗', '', '2024-09-06', '2', 266822.43, 285500.00, 198336.00, 212219.52, 68486.43, 25.67, 266822.43, 198336.00, 68486.43, '5e2a838a-110f-48bc-9518-f01a7066955b', '2024-10-20 13:42:20', '3', '2024-11-01 02:36:07', '2', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('4821cea3-07a4-4495-a139-8e8d74e26254', 'OBEC Firewall Gateway', '2024-12-02', '2025-02-02', 'Bidding', '', '', '2024-10-21', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 500000.00, 535000.00, 1.00, 1.07, 499999.00, 100.00, 250000.00, 0.50, 249999.50, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 22:26:58', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-11-01 00:11:54', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('49b9dd79-d94d-45c9-8645-cf4caaab398a', 'จ้างเหมาติดตั้งสิทธิ์การใช้งานโปรแกรมป้องกันไวรัสคอมพิวเตอร์', '2024-10-05', '2024-10-19', 'Win', '34/2568', '', '2024-10-04', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 732710.28, 784000.00, 618412.00, 661700.84, 114298.28, 15.60, 732710.28, 618412.00, 114298.28, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 02:47:33', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:43:04', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 'งานจ้างบำรุงรักษาระบบ MOBILE FACE RECOGNITION', '2024-06-01', '2025-08-03', 'Win', 'A02/3160030757/2567', '', '2024-06-01', '3', 1073708.41, 1148868.00, 791114.96, 846493.01, 282593.45, 26.32, 1073708.41, 791114.96, 282593.45, NULL, '2024-10-15 21:59:34', '3', '2024-10-15 21:59:34', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('57cff5f7-e083-40ed-be05-323e55b0f12c', 'MA OBEC SUN', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 2099065.42, 2246000.00, 0.93, 1.00, 2099064.49, 100.00, 2099065.42, 0.93, 2099064.49, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:17:32', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:17:32', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('7269890f-a7b1-47e0-907b-c0fb5eacc576', 'MA OBEC FM', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 4609000.00, 4931630.00, 1.00, 1.07, 4608999.00, 100.00, 4609000.00, 1.00, 4608999.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:10:17', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:10:17', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('72f91cb8-944d-44f5-babc-f4288568c964', 'Web Hosting สตช.', '2024-12-01', '2025-11-30', 'Bidding', '', 'Nutanix HCI, Network, Backup, UPS, Firewall', '2024-10-07', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 12131800.00, 12981026.00, 10393849.25, 11121418.70, 1737950.75, 14.33, 6065900.00, 5196924.63, 868975.38, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:20:48', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-11-01 00:11:27', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('759f33fb-b998-4d5f-bd80-343867ef52a0', 'จ้างเหมาปรับปรุงระบบไฟฟ้าของกล้องโทรทัศน์วงจรปิด', '2024-08-23', '2024-12-20', 'Win', '336/2567', '', '2024-08-22', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 461682.24, 494000.00, 108062.00, 115626.34, 353620.24, 76.59, 461682.24, 108062.00, 353620.24, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:07:51', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:42:52', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('7c67ce7e-ee05-487f-a763-4627899516bb', 'โครงการ บ่อวิน สมาร์ท ซิตี้ ดูแลสุขภาพแบบอัจฉริยะ (Smart Health Care) สำหรับผู้สูงอายุ ประจำปีงบประมาณ 2567', '2023-09-02', '2024-09-02', 'Win', '1/2567', '', '2023-09-15', '3', 623831.78, 667500.00, 423848.00, 453517.36, 199983.78, 32.06, 623831.78, 423848.00, 199983.78, '32104ee7-4b28-400b-bb7b-1ab55e1cf19d', '2024-10-11 23:29:28', '3', '2024-10-15 21:48:44', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('8b9ac1ee-fee4-4bb4-be6d-aabe610f27aa', 'MA DLD NSW3', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 500000.00, 535000.00, 204160.00, 218451.20, 295840.00, 59.17, 500000.00, 204160.00, 295840.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:39:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:39:52', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('96f5ad4d-3a2d-4f3d-a909-9c74eaf3df55', 'MA K8S Honda Leasing', '2025-01-01', '2025-12-31', 'Quotation', '', '', '2024-10-28', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 95000.00, 101650.00, 1.00, 1.07, 94999.00, 100.00, 9500.00, 0.10, 9499.90, 'f004cbe4-f666-4de7-8e85-7f940b6d8393', '2024-10-31 22:32:22', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:32:22', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('a2476611-d200-4882-93b6-a48caab4900e', 'OBEC Infrastructure 77M', '2024-08-01', '2024-10-01', 'Win', '', '', '2024-07-17', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 1490000.00, 1594300.00, 0.93, 1.00, 1489999.07, 100.00, 1490000.00, 0.93, 1489999.07, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 21:15:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:06:22', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'df374787-e96c-4d3c-8089-3867edd96cf4', 7.00),
('a8634a06-fac7-43c5-bae1-f1b24d7509aa', 'e-Movement DLD', '2024-12-23', '2025-03-31', 'Quotation', '', 'Nutanix HCI, Network, Microsoft License', '2024-10-15', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 6488400.00, 6942588.00, 4988707.65, 5337917.19, 1499692.35, 23.11, 648840.00, 498870.77, 149969.23, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:28:46', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-11-01 00:10:56', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('b2fbde7d-175a-4822-a719-495b57d4b9c0', 'MA DLD e-Movement', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 450000.00, 481500.00, 205760.00, 220163.20, 244240.00, 54.28, 450000.00, 205760.00, 244240.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:37:51', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:37:51', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('b659bc53-f12a-4a5c-9ab9-905939c9fb2e', 'Network สกบ.', '2024-10-07', '2024-12-31', 'Win', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 740000.00, 791800.00, 0.93, 1.00, 739999.07, 100.00, 740000.00, 0.93, 739999.07, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 21:24:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-11-01 00:12:19', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('bad1c47d-0180-44ef-89eb-b7e853877c6b', 'e-Payment DLD', '2024-10-01', '2025-01-28', 'Win', '', 'Nutanix HCI, Network, Microsoft License', '2024-09-02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 9840000.00, 10528800.00, 8360994.85, 8946264.49, 1479005.15, 15.03, 9840000.00, 8360994.85, 1479005.15, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:46:55', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:46:55', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('bdb816d7-49d1-4fae-881f-f6ac087c1bdc', 'MA e-Library กนอ.', '2024-10-01', '2025-09-30', 'Win', '', 'เช่าใช้ Cloud 1 ปี', '2024-09-02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 84000.00, 89880.00, 20954.88, 22421.72, 63045.12, 75.05, 84000.00, 20954.88, 63045.12, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:06:00', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:06:00', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('c1eeffcd-ceca-46dc-9b09-dae4d6d00091', 'ติดตั้งสายสัญญาณคอมพิวเตอร์ และย้ายห้องควบคุมระบบคอมพิวเตอร์', '2024-07-01', '2024-12-28', 'Win', '105/2567', '', '2024-07-01', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 4635514.02, 4960000.00, 2921450.00, 3125951.50, 1714064.02, 36.98, 4635514.02, 2921450.00, 1714064.02, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-04 03:42:21', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:42:21', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('c9607961-6240-4066-965f-5a171dcee526', 'MA BAAC PromptPay ปีที่ 5', '2024-10-01', '2025-09-30', 'Win', '', 'ปีสุดท้าย', '2024-09-10', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 116000.00, 124120.00, 1.00, 1.07, 115999.00, 100.00, 116000.00, 1.00, 115999.00, 'f313a7ba-64ae-4d61-af99-f493a98039b2', '2024-10-31 21:57:08', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:57:08', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('deca5fd5-88c4-4a82-8d93-538f735cffe4', 'งานปรับปรุงและเพิ่มประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิดในพื้นที่ชุมชนเมืองพัทยา', '2024-10-28', '2025-04-26', 'Win', '42/2568', '', '2024-10-28', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 12803738.32, 13700000.00, 0.00, 0.00, 0.00, 0.00, 12803738.32, 0.00, 12803738.32, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 04:07:32', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 04:07:32', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('e5ff0b66-f1db-4f84-befe-73d06829d4ec', 'MA OBEC Mail', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 228000.00, 243960.00, 0.93, 1.00, 227999.07, 100.00, 228000.00, 0.93, 227999.07, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:11:16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:11:16', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('e7c861a2-b027-4992-a280-c8dc6a180784', 'MA BAAC ICAS ปีที่ 4', '2024-03-01', '2025-02-28', 'Win', '', '', '2024-11-01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 50000.00, 53500.00, 1.00, 1.07, 49999.00, 100.00, 50000.00, 1.00, 49999.00, 'f313a7ba-64ae-4d61-af99-f493a98039b2', '2024-10-31 22:00:44', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:00:44', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'โครงการจ้างระบบแพลตฟอร์มวิเคราะห์ข้อมูล ปัญญาประดิษฐ์ในการบริการดูแลการใช้ชีวิตและดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุและผู้ที่มีภาวะพึ่งพิง', '2024-09-27', '2025-09-29', 'Win', '๖/๒๕๖๗', '', '2024-09-06', '3', 266822.43, 285500.00, 198336.00, 212219.52, 68486.43, 25.67, 266822.43, 198336.00, 68486.43, '5e2a838a-110f-48bc-9518-f01a7066955b', '2024-10-15 21:17:03', '3', '2024-10-15 21:19:35', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('eab3d4f8-1ab7-4654-b1c3-d1ddce015b5b', 'MA DLD Datalake', '2024-11-01', '2025-09-30', 'Quotation', '', '', '2024-10-01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 816400.00, 873548.00, 278320.00, 297802.40, 538080.00, 65.91, 81640.00, 27832.00, 53808.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:04:08', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:04:08', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('f50eb76c-0230-4f71-b47f-c2e60d652ce1', 'MA OBEC DataCenter', '2024-10-01', '2025-09-30', 'Win', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 3080000.00, 3295600.00, 1.00, 1.07, 3079999.00, 100.00, 3080000.00, 1.00, 3079999.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:09:02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:09:02', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00);

-- --------------------------------------------------------

--
-- Table structure for table `project_costs`
--

CREATE TABLE `project_costs` (
  `cost_id` char(36) NOT NULL COMMENT 'รหัสต้นทุน (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `type` varchar(100) NOT NULL COMMENT 'ประเภท (Hardware, Software, etc.)',
  `part_no` varchar(100) NOT NULL COMMENT 'รหัสสินค้า/บริการ',
  `description` text NOT NULL COMMENT 'รายละเอียด',
  `quantity` int(11) NOT NULL COMMENT 'จำนวน',
  `price_per_unit` decimal(15,2) NOT NULL COMMENT 'ราคาต่อหน่วย',
  `total_amount` decimal(15,2) GENERATED ALWAYS AS (`quantity` * `price_per_unit`) STORED COMMENT 'ยอดรวม (จำนวน × ราคาต่อหน่วย)',
  `cost_per_unit` decimal(15,2) NOT NULL COMMENT 'ต้นทุนต่อหน่วย',
  `total_cost` decimal(15,2) GENERATED ALWAYS AS (`quantity` * `cost_per_unit`) STORED COMMENT 'ต้นทุนรวม (จำนวน × ต้นทุนต่อหน่วย)',
  `supplier` varchar(255) NOT NULL COMMENT 'ชื่อซัพพลายเออร์',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างรายการ',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างรายการ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บรายการต้นทุนโครงการ';

--
-- Dumping data for table `project_costs`
--

INSERT INTO `project_costs` (`cost_id`, `project_id`, `type`, `part_no`, `description`, `quantity`, `price_per_unit`, `cost_per_unit`, `supplier`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('093c686b-60c8-4ed9-9dd9-f7f98f8a3390', '476239df-f7ca-4a7c-9dc2-29e358ba7182', 'A', 'Herdware', 'Sim Internet แบบรายเดือน ระยะเวลา 12 เดือน', 31, 1500.00, 1188.00, 'AIS', '2024-11-01 03:02:20', '2', '2024-11-01 03:02:44', '2'),
('13f9d3e5-aa9c-4fe7-b57f-f7e2eaa3f1d3', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'A', 'Hardware', 'ชุดเฝ้าระวัง', 30, 2850.00, 1347.00, 'Stock Point ', '2024-11-01 11:14:30', '3', '2024-11-01 11:14:30', NULL),
('7ec94428-a477-4a67-901a-099f9a739826', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'A', 'Hardware', 'Sim Internet แบบรายเดือน ระยะเวลา 12 เดือน', 31, 1366.20, 1188.00, 'AIS', '2024-11-01 11:16:14', '3', '2024-11-01 11:20:20', '3'),
('e8a817f3-3a58-4106-8bf3-69a184c557c9', '476239df-f7ca-4a7c-9dc2-29e358ba7182', 'A', 'Herdware', 'ชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุในบ้านและภายนอกบ้าน', 30, 5999.00, 1347.00, 'Stock Point ', '2024-11-01 02:07:29', '2', '2024-11-01 02:59:53', '2');

--
-- Triggers `project_costs`
--
DELIMITER $$
CREATE TRIGGER `before_insert_project_costs` BEFORE INSERT ON `project_costs` FOR EACH ROW BEGIN
    IF NEW.cost_id IS NULL THEN
        SET NEW.cost_id = UUID();
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_project_costs` BEFORE UPDATE ON `project_costs` FOR EACH ROW BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_cost_summary`
--

CREATE TABLE `project_cost_summary` (
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ',
  `total_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'ยอดรวมทั้งหมด (ไม่รวมภาษี)',
  `vat_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่ม (VAT)',
  `grand_total` decimal(15,2) DEFAULT 0.00 COMMENT 'ยอดรวมสุทธิ (รวมภาษี)',
  `total_cost` decimal(15,2) DEFAULT 0.00 COMMENT 'ต้นทุนรวมทั้งหมด (ไม่รวมภาษี)',
  `cost_vat_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่มของต้นทุน',
  `total_cost_with_vat` decimal(15,2) DEFAULT 0.00 COMMENT 'ต้นทุนรวมทั้งหมด (รวมภาษี)',
  `profit_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'กำไรขั้นต้น',
  `profit_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'เปอร์เซ็นต์กำไร',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางสรุปต้นทุนโครงการ';

--
-- Dumping data for table `project_cost_summary`
--

INSERT INTO `project_cost_summary` (`project_id`, `total_amount`, `vat_amount`, `grand_total`, `total_cost`, `cost_vat_amount`, `total_cost_with_vat`, `profit_amount`, `profit_percentage`, `updated_at`) VALUES
('476239df-f7ca-4a7c-9dc2-29e358ba7182', 226470.00, 15852.90, 242322.90, 77238.00, 5406.66, 82644.66, 159678.24, 65.89, '2024-11-01 03:02:44'),
('ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 127852.20, 8949.65, 136801.85, 77238.00, 5406.66, 82644.66, 54157.19, 39.59, '2024-11-01 11:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `document_id` char(36) NOT NULL COMMENT 'รหัสเอกสาร (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `document_name` varchar(255) NOT NULL COMMENT 'ชื่อเอกสาร',
  `document_type` varchar(50) NOT NULL COMMENT 'ประเภทเอกสาร',
  `file_path` varchar(255) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (หน่วยเป็นไบต์)',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  `uploaded_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้อัปโหลด (เชื่อมโยงกับตาราง users)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_documents`
--

INSERT INTO `project_documents` (`document_id`, `project_id`, `document_name`, `document_type`, `file_path`, `file_size`, `upload_date`, `uploaded_by`) VALUES
('0412c148-9070-4004-bd70-e9930742eaad', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'SO', 'pdf', '../../uploads/project_documents/Innovation/ea072d02-f6d1-42b2-bdf9-9451bb5eff3f/670f3fffd9c53.pdf', 124502, '2024-10-15 21:24:31', '3');

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `image_id` char(36) NOT NULL COMMENT 'รหัสรูปภาพ (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `image_name` varchar(255) NOT NULL COMMENT 'ชื่อรูปภาพ',
  `file_path` varchar(255) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ประเภทไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์ (หน่วยเป็นไบต์)',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  `uploaded_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้อัปโหลด (เชื่อมโยงกับตาราง users)',
  `description` text DEFAULT NULL COMMENT 'คำอธิบายรูปภาพ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_images`
--

INSERT INTO `project_images` (`image_id`, `project_id`, `image_name`, `file_path`, `file_type`, `file_size`, `upload_date`, `uploaded_by`, `description`) VALUES
('16531036-05da-400a-b579-cf93b4e56e31', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'LINE_ALBUM_งานติดตั้งอุปกรณ์ aitracker เทศบาลปากท่อ จ.ร.jpg', '../../uploads/project_images/Innovation/ea072d02-f6d1-42b2-bdf9-9451bb5eff3f/670f40da5ac76.jpg', 'image/jpeg', 291125, '2024-10-15 21:28:10', '3', NULL),
('e803c46a-0d19-44d4-9c9d-a3226daf048b', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'S__6987801.jpg', '../../uploads/project_images/Innovation/ea072d02-f6d1-42b2-bdf9-9451bb5eff3f/670f4189888ef.jpg', 'image/jpeg', 460243, '2024-10-15 21:31:05', '3', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_payments`
--

CREATE TABLE `project_payments` (
  `payment_id` char(36) NOT NULL COMMENT 'รหัสการชำระเงิน (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `payment_number` int(11) NOT NULL COMMENT 'งวดที่',
  `amount` decimal(10,2) NOT NULL COMMENT 'จำนวนเงินที่ต้องชำระ',
  `payment_percentage` decimal(5,2) NOT NULL COMMENT 'เปอร์เซ็นต์การชำระต่องวด',
  `amount_paid` decimal(10,2) DEFAULT 0.00 COMMENT 'จำนวนเงินที่ชำระแล้ว',
  `remaining_amount` decimal(10,2) GENERATED ALWAYS AS (`amount` - `amount_paid`) STORED COMMENT 'จำนวนเงินคงเหลือ',
  `payment_progress` decimal(5,2) GENERATED ALWAYS AS (`amount_paid` / `amount` * 100) STORED COMMENT 'ความคืบหน้าการชำระ (%)',
  `payment_date` date DEFAULT NULL COMMENT 'วันที่ชำระ',
  `due_date` date DEFAULT NULL COMMENT 'วันครบกำหนดชำระ',
  `status` enum('Pending','Partial','Paid','Overdue') DEFAULT 'Pending' COMMENT 'สถานะการชำระ',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'วิธีการชำระเงิน',
  `transaction_id` varchar(100) DEFAULT NULL COMMENT 'รหัสอ้างอิงการทำธุรกรรม',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างรายการ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างรายการ (เชื่อมโยงกับตาราง users)',
  `updated_by` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_payments`
--

INSERT INTO `project_payments` (`payment_id`, `project_id`, `payment_number`, `amount`, `payment_percentage`, `amount_paid`, `payment_date`, `due_date`, `status`, `payment_method`, `transaction_id`, `remark`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
('15f67018-2f9f-44c8-89eb-6d84bb0834c1', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 3, 88347.95, 7.69, 88347.95, NULL, '2024-09-01', 'Paid', NULL, NULL, NULL, '2024-10-15 22:05:06', '2024-10-15 22:05:06', '3', ''),
('17777793-1c36-48f7-b9fc-db6ceaefe004', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 9, 88347.95, 7.69, 0.00, NULL, '2025-03-03', 'Pending', NULL, NULL, NULL, '2024-10-15 22:07:13', '2024-10-15 22:07:13', '3', ''),
('1807a92b-30a2-4e30-a077-f92d2d1b32da', '05d29d2b-39ab-4c46-b34b-801ede800172', 4, 960000.00, 30.00, 0.00, NULL, '2024-12-30', 'Pending', NULL, NULL, NULL, '2024-10-17 04:56:25', '2024-10-18 11:38:02', '3', '2'),
('1bcabad2-13a1-494e-bcce-290f413bfa91', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 1, 88347.95, 7.69, 88347.95, NULL, '2024-07-01', 'Paid', NULL, NULL, NULL, '2024-10-15 22:03:09', '2024-10-15 22:03:09', '3', ''),
('496db098-563c-44ab-a206-407cb5c51bee', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 13, 88347.95, 7.69, 0.00, NULL, '2025-07-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:08:37', '2024-10-15 22:08:37', '3', ''),
('4bed82ad-6a45-4b9d-94ff-045eea98acf4', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 1, 266822.43, 100.00, 0.00, NULL, '2024-10-31', 'Pending', NULL, NULL, NULL, '2024-10-19 02:38:58', '2024-10-19 02:38:58', '2', ''),
('4ce991cb-589b-4475-a7fe-ccbea0a47235', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 6, 88347.95, 7.69, 0.00, NULL, '2024-12-02', 'Pending', NULL, NULL, NULL, '2024-10-15 22:06:29', '2024-10-15 22:06:29', '3', ''),
('6061a5c2-5637-46e1-9794-ba9343bdf178', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 12, 88347.95, 7.69, 0.00, NULL, '2025-06-02', 'Pending', NULL, NULL, NULL, '2024-10-15 22:08:04', '2024-10-15 22:08:04', '3', ''),
('65c1021b-79d0-4998-971e-c1a186dbf4ab', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 8, 88347.95, 7.69, 0.00, NULL, '2025-02-03', 'Pending', NULL, NULL, NULL, '2024-10-15 22:06:58', '2024-10-15 22:06:58', '3', ''),
('82d3348c-f211-4781-827a-0191cd799cbb', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 7, 88347.95, 7.69, 0.00, NULL, '2025-01-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:06:44', '2024-10-15 22:06:44', '3', ''),
('85dd68d7-de46-4e81-99f8-4414590503ac', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 4, 88347.95, 7.69, 88347.95, NULL, '2024-10-01', 'Paid', NULL, NULL, NULL, '2024-10-15 22:05:34', '2024-10-15 22:05:34', '3', ''),
('8937df61-8c93-48c6-8984-28b6493ffdaf', '05d29d2b-39ab-4c46-b34b-801ede800172', 2, 640000.00, 20.00, 0.00, NULL, '2024-08-01', 'Pending', NULL, NULL, NULL, '2024-10-17 04:55:42', '2024-10-18 04:19:17', '3', '3'),
('8efee306-0748-44f7-9575-fe2ecb19d709', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 5, 88347.95, 7.69, 0.00, NULL, '2024-11-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:06:08', '2024-10-15 22:06:08', '3', ''),
('a229e370-811d-408d-ad8c-bf638ff054e8', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 11, 88347.95, 7.69, 0.00, NULL, '2025-05-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:07:44', '2024-10-15 22:07:44', '3', ''),
('b3ae6692-1ae7-4c27-bc03-0c5b401f85f5', '05d29d2b-39ab-4c46-b34b-801ede800172', 3, 1120000.00, 35.00, 0.00, NULL, '2024-09-02', 'Pending', NULL, NULL, NULL, '2024-10-17 04:56:02', '2024-10-18 04:19:28', '3', '3'),
('b76b3d7a-3559-4b8c-93a8-b3dbae92ca48', '05d29d2b-39ab-4c46-b34b-801ede800172', 1, 480000.00, 15.00, 480000.00, NULL, '2024-02-01', 'Paid', NULL, NULL, NULL, '2024-10-18 02:49:48', '2024-10-18 04:18:52', '3', '3'),
('b7b50966-c6d0-46ce-8e43-81052cb8e3ce', '7c67ce7e-ee05-487f-a763-4627899516bb', 1, 71690.00, 10.00, 0.00, NULL, '0000-00-00', 'Pending', NULL, NULL, NULL, '2024-10-14 04:59:13', '2024-10-14 09:54:16', '2', '3'),
('c096b705-b4fe-43c0-9040-a6a2f298af77', '7c67ce7e-ee05-487f-a763-4627899516bb', 2, 55488.06, 7.74, 0.00, NULL, '0000-00-00', 'Pending', NULL, NULL, NULL, '2024-10-14 09:43:05', '2024-10-14 09:55:15', '3', '3'),
('c1a72926-f8af-48b0-a9ed-2431c2a7c9cb', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 10, 88347.95, 7.69, 0.00, NULL, '2025-04-01', 'Pending', NULL, NULL, NULL, '2024-10-15 22:07:27', '2024-10-15 22:07:27', '3', ''),
('c725bf71-e0c8-43f1-9763-1b15ff94166c', '4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 2, 88347.95, 7.69, 88347.95, NULL, '2024-08-01', 'Paid', NULL, NULL, NULL, '2024-10-15 22:04:10', '2024-10-15 22:04:10', '3', '');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` char(36) NOT NULL COMMENT 'รหัสทีม (UUID)',
  `team_name` varchar(255) NOT NULL COMMENT 'ชื่อทีม',
  `team_description` mediumtext DEFAULT NULL COMMENT 'รายละเอียดของทีม',
  `created_by` char(36) NOT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัปเดตข้อมูล',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตข้อมูลล่าสุด',
  `team_leader` char(36) DEFAULT NULL COMMENT 'รหัสหัวหน้าทีม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_id`, `team_name`, `team_description`, `created_by`, `created_at`, `updated_by`, `updated_at`, `team_leader`) VALUES
('1', 'Innovation_PIT', 'Product  Solution Teams', '2', '2024-09-26 03:35:50', '5', '2024-11-04 02:27:46', '5'),
('2', 'Zoom', 'Zoom Project', '2', '2024-09-26 03:35:50', '5', '2024-11-04 01:36:06', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1'),
('3', 'Service_PIT', 'Service Bank & Corporate_Chittichai', '2', '2024-09-26 03:35:50', '5', '2024-11-04 04:12:13', '5'),
('37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'Enterprise', 'Presales Service & Enterprise Solution', '5', '2024-11-01 01:05:11', '2', '2024-11-04 04:12:20', '5'),
('4', 'Point IT', 'Point IT Consulting Co. Ltd.', '2', '2024-09-26 03:35:50', '2', '2024-10-20 12:39:29', '5'),
('b9db21db-cfd7-4887-9ca7-5088a12f1bda', 'Sales Solution', 'Sales Solution', '5', '2024-11-04 02:58:23', NULL, NULL, '5'),
('db32697a-0f69-41f7-9413-58ffe920ad7d', 'Bank Corporate Sales', 'Bank Corporate Sales', '5', '2024-11-04 02:29:50', '5', '2024-11-04 02:55:59', '6614b721-a8b4-46d2-9c80-0caab04772dc'),
('f4b11a86-0fca-45e5-8511-6a946c7f21d4', 'Point IT Smart City_Oran', 'Smart City Team_Government', '5', '2024-11-04 01:39:32', '5', '2024-11-04 01:40:06', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` char(36) NOT NULL COMMENT 'รหัสผู้ใช้ (UUID)',
  `first_name` varchar(255) NOT NULL COMMENT 'ชื่อผู้ใช้',
  `last_name` varchar(255) NOT NULL COMMENT 'นามสกุลผู้ใช้',
  `username` varchar(255) NOT NULL COMMENT 'ชื่อผู้ใช้สำหรับล็อกอิน',
  `email` varchar(255) NOT NULL COMMENT 'อีเมล',
  `role` enum('Executive','Sale Supervisor','Seller','Engineer') NOT NULL COMMENT 'บทบาท (เช่น Executive, Sale Supervisor)',
  `team_id` char(36) DEFAULT NULL COMMENT 'รหัสทีม (เชื่อมโยงกับตาราง teams)',
  `position` varchar(255) NOT NULL COMMENT 'ตำแหน่งงาน',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `password` varchar(255) NOT NULL COMMENT 'รหัสผ่าน',
  `company` varchar(255) DEFAULT NULL COMMENT 'ชื่อบริษัท',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'รูปภาพ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `email`, `role`, `team_id`, `position`, `phone`, `password`, `company`, `created_at`, `created_by`, `profile_image`) VALUES
('1', 'Sale', 'Test Platform', 'Sale', 'Saletest@gmail.com', 'Seller', '4', 'Sale Test Platform', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co. Ltd', '2024-09-15 09:43:58', '2', ''),
('14d9e34c-b691-4ce8-a5ef-929ace71248a', 'Boongred', 'Theephukhieo', 'boongerd', 'boongerd@pointit.co.th', 'Seller', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'System Engineer Manager', '0818741889', '$2y$10$GOwL0Y1yiZl1gkkC/b9vn.R5pysD9YLzTiAaJWhRJA/0MVcTCacpm', 'Point IT Consulting Co.,Ltd.', '2024-10-31 23:55:23', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', NULL),
('1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'Arunnee', 'Thiamthawisin', 'Arunnee', 'arunnee@pointit.co.th', 'Seller', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 'Account Executive Manager', '', '$2y$10$mzextohitcaMnwfRGgyUg.C1LyMEvRbxq2sy4dnN3WKlOVJZIMi4S', 'Point IT Consulting Co.,Ltd.', '2024-11-04 03:05:45', '5', NULL),
('2', 'Systems', 'Admin', 'Admin', 'Systems_admin@gmail.com', 'Executive', '1', 'Systems Admin', '0839595800', '$2y$10$jcmTr.I9CthXOrWFC78XjuOjwPoZlbvF80M4RKow4RvnNbm1Ej8dO', 'Point IT Consulting Co. Ltd', '2024-09-15 09:43:58', '2', ''),
('2f6d353b-53f1-4492-8878-bc93c18c5de9', 'Prakorb', 'Jongjarussang', 'Prakorb', 'prakorb@pointit.co.th', 'Executive', '4', 'MD', '', '$2y$10$Nl9zzwKG.i1pS2jiZhQ41OcybPwbB5qGl80aY12.pcV4v6/bVzxn6', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:31:48', '5', NULL),
('3', 'Miss Phattraorn', 'Amornophakun', 'Phattraorn', 'phattraorn.a@pointit.co.th', 'Sale Supervisor', '1', 'Sale Supervisor', '0619522111', '$2y$10$LZXVwCISxNHxb8lvs93mDe9RCLU76842RRbezYEqSmJbvuBCgvExe', 'Point IT Consulting Co. Ltd', '2024-09-15 09:43:58', '2', '670e42ef5b4a3.jpg'),
('3140fdaf-5103-4423-bf87-11b7c1153416', 'Direk', 'Wongsngam', 'Direk', 'Direk@pointit.co.th', 'Seller', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 'Bank & Corporate SalesDirector', '', '$2y$10$M/bAx1lFykgf1LklAvbQKONKI4OQfpu7NofVfwA.r1GDy9xx94uGO', 'Point IT Consulting Co.,Ltd', '2024-11-04 02:39:01', '5', NULL),
('34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'Pisarn', 'Siribandit', 'Pisarn', 'pisarn@pointit.co.th', 'Sale Supervisor', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'Digital Transformation Consulting Director', '', '$2y$10$aEOtRUxIfKi52ib5Jj.Vpue/FP7eIWKeNRdM68DEr1GCH5OUa1uOy', 'Point IT Consulting Co.,Ltd.', '2024-10-31 18:08:53', '5', '67242a25ce524.png'),
('3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'Natapornsuang', 'Chanasarn', 'Natapornsuang', 'natapornsuang@pointit.co.th', 'Seller', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 'Account Executive Manager', '', '$2y$10$cMPa/VsJaIs.WSQxCHvVT.Ct6hbifKjAkQScjAEQv6dbGQwR8zCOC', 'Point IT Consulting Co.,Ltd.', '2024-11-04 03:00:40', '5', NULL),
('4', 'Support', 'Platform', 'Support', 'Support@gmail.com', 'Executive', '4', 'Application Support', '0839595811', '$2y$10$RAWOJU03Vy72u4zMVF/M/O9Af1HSbGOHAjlDKZHgrzbSZodZUcuky', 'Point IT Consulting Co. Ltd', '2024-09-15 09:55:43', '2', '6724613260590.png'),
('44ab4e8b-e3e6-431d-ad49-40d4601779b4', 'Nutjaree', 'Chaothonglang', 'Nutjaree', 'nutjaree@pointit.co.th', 'Seller', '3', 'Assistant Service Manager', '', '$2y$10$OeTqb/woFTv/pt7uaBRx4ujA7jJYTuyGzSmx2y4jtijxn9oJcRuky', 'Point It Consulting Co.,Ltd.', '2024-11-04 02:04:37', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', NULL),
('5', 'Panit', 'Paophan', 'Panit', 'panit@poinitit.co.th', 'Executive', '4', 'Executive Director', '0814834619', '$2y$10$eAar02e4iaTG6bhKs2XLfua7ck.2co.8dkla8VX0tVCC5cnQfc/E6', 'Point IT Consulting Co. Ltd', '2024-09-17 08:15:37', '2', NULL),
('6614b721-a8b4-46d2-9c80-0caab04772dc', 'Woradol', 'Daoduang', 'Woradol', 'Woradol@pointit.co.th', 'Executive', '4', 'Executive Director', '', '$2y$10$l454f/PTDFOabJbIz0BAkedEGdUGc000TRpac7ffYJrRzlIIwcUc2', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:34:51', '5', NULL),
('8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'Pawitcha', 'Katekhong', 'Pawitcha', 'Pawitcha@pointit.co.th', 'Seller', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 'Bank &amp; Corporate Account Executive', '', '$2y$10$7KiTeTdi7qz/fTJwIUAk7O0RsNirsc6.5DLrrZWSDIrnjHu3Oc5wG', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:43:57', '5', NULL),
('97c68703-a8b7-4ceb-9344-65fe4404c4ab', 'Chittichai', 'Duangnang', 'Chittichai', 'chittichai@pointit.co.th', 'Sale Supervisor', '3', 'Service Manager', '', '$2y$10$fcPYrOzeITItjTlHZULv4O7vqbdmSHnY6UG2twK59QBL5miVHIZw6', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:00:10', '5', NULL),
('b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'Nanthika', 'Chongcharassang', 'nanthika', 'nanthika@pointit.co.th', 'Seller', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'Project Manager', '0631979263', '$2y$10$qqx4FgiPm0Dvf3eVHMQK4.qgGWNZtjdNIBk/DspuAoEk5oubf7EoC', 'Point IT Consulting Co.,Ltd.', '2024-10-31 23:57:35', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', NULL),
('ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'Gun', 'Oran', 'Oran.gun', 'oran.gun@gmail.com', 'Sale Supervisor', '2', 'MD', '', '$2y$10$pLijvFmVbbbLlQQJO0pLvuBrYKUMRkQYT2rx02TpPXEbxxyxdH5Ca', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:34:43', '5', '672824b3cb14d.png'),
('bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'Yanisa', 'Khemthong', 'Yanisa_Pit', 'Yanisa@pointit.co.th', 'Seller', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 'Senior Account Executive, Smart City Solution', '', '$2y$10$8MVON.Qy8jULwCYimJyhiOsN1AaQwbspq6iVdyMs6QbOM1nBV3kTy', 'Point It Conulting Co.,Ltd.', '2024-11-04 01:49:55', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', NULL),
('c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 'Yanisa', 'Zoom', 'Yanisa_Zoom', 'yanisa8742@gmail.com', 'Seller', '2', 'Senior Account Executive, Smart City Solution', '', '$2y$10$8RPFI9bXPqbx4JD1kYWv8.oqqov7QDiG./zL1kdqggpFz3/gpK9nu', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:54:45', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', NULL),
('e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 'Daranee', 'Punyathiti', 'Daranee', 'daranee@pointit.co.th', 'Sale Supervisor', '2', 'MD', '', '$2y$10$qxVyPQvvdbr4N42hmamITO0Rz6Uwg7JOa5wlRNv2StsFvZ4gnpBS2', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:30:21', '5', '672823ad217eb.png'),
('ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'Oran.gun', 'Point IT', 'Oran.pit', 'Oran@pointit.co.th', 'Sale Supervisor', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 'Smart City Consulting Director', '', '$2y$10$UtrO5s8iizTT0FyegEO4KuW3GPGKBvebPEZ/oS9SwMlkVxybE1kDW', 'Point IT Consulting Co.,Ltd.', '2024-11-04 01:45:31', '5', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_creation_logs`
--

CREATE TABLE `user_creation_logs` (
  `id` int(11) NOT NULL,
  `creator_id` varchar(255) NOT NULL,
  `new_user_id` varchar(255) NOT NULL,
  `new_user_role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_creation_logs`
--

INSERT INTO `user_creation_logs` (`id`, `creator_id`, `new_user_id`, `new_user_role`, `created_at`) VALUES
(1, '3', '1537eabd-0014-4f80-9116-9800cc8df26f', 'Seller', '2024-10-10 16:15:03'),
(2, '2', 'a0c895d7-3730-4b11-827d-8a7423d81761', 'Seller', '2024-10-11 14:34:01'),
(3, '3', '0a9fec94-57d3-4f65-aeaa-052cba2c3b62', 'Seller', '2024-10-11 16:18:12'),
(4, '2', 'bb027ef6-7b53-4775-92d6-8faa0e1f7abc', 'Seller', '2024-10-20 11:55:08'),
(5, '5', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 'Sale Supervisor', '2024-11-04 01:30:21'),
(6, '5', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'Sale Supervisor', '2024-11-04 01:34:43'),
(7, '5', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'Sale Supervisor', '2024-11-04 01:45:31'),
(8, 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'Seller', '2024-11-04 01:49:55'),
(9, 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 'Seller', '2024-11-04 01:54:46'),
(10, '5', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', 'Sale Supervisor', '2024-11-04 02:00:11'),
(11, '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '44ab4e8b-e3e6-431d-ad49-40d4601779b4', 'Seller', '2024-11-04 02:04:37'),
(12, '5', '2f6d353b-53f1-4492-8878-bc93c18c5de9', 'Executive', '2024-11-04 02:31:48'),
(13, '5', '6614b721-a8b4-46d2-9c80-0caab04772dc', 'Executive', '2024-11-04 02:34:51'),
(14, '5', '3140fdaf-5103-4423-bf87-11b7c1153416', 'Seller', '2024-11-04 02:39:01'),
(15, '5', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'Seller', '2024-11-04 02:43:57'),
(16, '5', '3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'Seller', '2024-11-04 03:00:40'),
(17, '5', '1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'Seller', '2024-11-04 03:05:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_image` (`image_id`);

--
-- Indexes for table `category_image`
--
ALTER TABLE `category_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `products_ibfk_1` (`created_by`);

--
-- Indexes for table `product_documents`
--
ALTER TABLE `product_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `projects_ibfk_2` (`created_by`),
  ADD KEY `projects_ibfk_3` (`seller`),
  ADD KEY `projects_customer_fk` (`customer_id`),
  ADD KEY `projects_product_fk` (`product_id`);

--
-- Indexes for table `project_costs`
--
ALTER TABLE `project_costs`
  ADD PRIMARY KEY (`cost_id`),
  ADD KEY `idx_project_costs_project` (`project_id`) COMMENT 'ดัชนีสำหรับการค้นหาตามรหัสโครงการ',
  ADD KEY `idx_costs_created_at` (`created_at`) COMMENT 'ดัชนีสำหรับการค้นหาตามวันที่สร้าง',
  ADD KEY `idx_costs_supplier` (`supplier`) COMMENT 'ดัชนีสำหรับการค้นหาตามซัพพลายเออร์';

--
-- Indexes for table `project_cost_summary`
--
ALTER TABLE `project_cost_summary`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `fk_project_documents_project` (`project_id`),
  ADD KEY `fk_project_documents_user` (`uploaded_by`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_project_images_project` (`project_id`),
  ADD KEY `fk_project_images_user` (`uploaded_by`);

--
-- Indexes for table `project_payments`
--
ALTER TABLE `project_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_project_payments_project` (`project_id`),
  ADD KEY `fk_project_payments_user` (`created_by`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `teams_ibfk_1` (`team_leader`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `users_team_fk` (`team_id`);

--
-- Indexes for table `user_creation_logs`
--
ALTER TABLE `user_creation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_creator` (`creator_id`),
  ADD KEY `fk_new_user` (`new_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_creation_logs`
--
ALTER TABLE `user_creation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_image` FOREIGN KEY (`image_id`) REFERENCES `category_image` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_documents`
--
ALTER TABLE `product_documents`
  ADD CONSTRAINT `product_documents_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`seller`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `projects_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `project_costs`
--
ALTER TABLE `project_costs`
  ADD CONSTRAINT `project_costs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_cost_summary`
--
ALTER TABLE `project_cost_summary`
  ADD CONSTRAINT `project_cost_summary_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD CONSTRAINT `fk_project_documents_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `fk_project_documents_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `fk_project_images_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `fk_project_images_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `fk_team_leader` FOREIGN KEY (`team_leader`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
