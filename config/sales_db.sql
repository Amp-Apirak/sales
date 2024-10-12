-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2024 at 01:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
  `address` text DEFAULT NULL COMMENT 'ที่อยู่',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `email` varchar(255) DEFAULT NULL COMMENT 'อีเมล',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `company`, `address`, `phone`, `email`, `remark`, `created_by`, `created_at`) VALUES
('32104ee7-4b28-400b-bb7b-1ab55e1cf19d', 'นายสิรวิชฐ์ อำไพวงษ์ (ท่านนายก)', 'องค์การบริหารส่วนตำบลบ่อวิน', 'องค์การบริหารส่วนตำบลบ่อวิน เลขที่ 1 หมู่ที่ 6 ตำบลบ่อวิน อำเภอศรีราชา จังหวัดชลบุรี 20230 โทรศัพท์ 0-3834-5949 ,0-3834-5918 โทรสาร 0-3834-6116 สายด่วนร้องทุกข์ 24 ชม. 08-1949-7771 นายกเทศบาลตาบลบ่อวิน องค์การบริหารส่วนตำบลบ่อวิน', '038345949', 'admin@bowin.go.th', '', '2', '2024-10-12 06:26:14'),
('5e2a838a-110f-48bc-9518-f01a7066955b', 'นายอิทธิกร เล่นวารี  (นายกเทศมนตรีตำบลปากท่อ)', 'สำนักงานเทศบาลตำบลปากท่อ จ. ราชบุรี', '39 หมู่ที่ 7 ต.ปากท่อ อ.ปากท่อ จ. ราชบุรี 70140 โทรศัพท์ 032-281-266 โทรสาร 032-282-564', '0806508585', 'pakthocity@hotmail.com', 'http://www.pakthomunic.go.th/office.php', '2', '2024-10-12 06:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` char(36) NOT NULL COMMENT 'รหัสสินค้า (UUID)',
  `product_name` varchar(255) NOT NULL COMMENT 'ชื่อสินค้า',
  `product_description` text DEFAULT NULL COMMENT 'รายละเอียดสินค้า',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` int(11) DEFAULT NULL COMMENT 'ผู้อัพเดทข้อมูลล่าสุด',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันอัพเดทข้อมูลล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
('075afde8-650f-4d75-b73d-f41242854682', 'Software Devlopment', 'การพัฒนาระบบตามความต้องการของลูกค้า', '2', '2024-10-12 06:18:35', 3, '2024-10-12 09:47:45'),
('3224e7a4-44ee-40ad-a6ac-22305c2b01eb', 'Smart Healthcare', 'ชุดกระเป๋า (Health Kit Set) สำหรับตรวจสุขคัดกรอกสถานะสุขภาพเคลื่อนที่ เก็บค่าข้อมูลเข้าระบบ โดยการตรวจวัดค่าจากอุปกรณ์เชื่อมต่อเข้ากับระบบ', '2', '2024-10-12 05:58:23', NULL, '2024-10-12 06:52:54'),
('4c85d842-54f3-4f06-87e6-553f81488234', 'Smart Emergency', 'ระบบเฝ้าระวังเหตุฉุกเฉิน', '2', '2024-10-12 06:18:20', NULL, '2024-10-12 06:52:54'),
('7defdc10-75d8-4433-8b4f-0eeba38b674f', 'BioIDM Face Scan', 'ระบบยืนยันตัวตน ผ่านการเปรียบเทียบใบหน้า บัตรประจำตัวประชาชน และอื่นๆ', '2', '2024-10-12 06:18:48', NULL, '2024-10-12 06:52:54'),
('b9fcda13-e694-4e04-a8df-fdf27ee08979', 'IBOC', 'มหาวิทยาลัยขอนแก่น', '2', '2024-10-12 06:19:12', 3, '2024-10-12 06:54:16');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` char(36) NOT NULL COMMENT 'รหัสโปรเจกต์ (UUID)',
  `project_name` varchar(255) NOT NULL COMMENT 'ชื่อโปรเจกต์',
  `start_date` date DEFAULT NULL COMMENT 'วันที่เริ่มโปรเจกต์',
  `end_date` date DEFAULT NULL COMMENT 'วันที่สิ้นสุดโปรเจกต์',
  `status` varchar(50) DEFAULT NULL COMMENT 'สถานะของโปรเจกต์',
  `contract_no` varchar(50) DEFAULT NULL COMMENT 'หมายเลขสัญญา',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `sales_date` date DEFAULT NULL COMMENT 'วันที่เสนอขาย',
  `seller` char(36) DEFAULT NULL COMMENT 'รหัสผู้ขาย',
  `sale_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายไม่รวมภาษี',
  `sale_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายรวมภาษี',
  `cost_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนไม่รวมภาษี',
  `cost_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนรวมภาษี',
  `gross_profit` decimal(10,2) DEFAULT NULL COMMENT 'กำไรขั้นต้น',
  `potential` decimal(5,2) NOT NULL COMMENT 'กำไรขั้นต้นแบบเปอร์เซ็นต์',
  `es_sale_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ยอดขายที่คาดการณ์ (ไม่รวมภาษี)',
  `es_cost_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'ต้นทุนที่คาดการณ์ (ไม่รวมภาษี)',
  `es_gp_no_vat` decimal(10,2) DEFAULT NULL COMMENT 'กำไรที่คาดการณ์ (ไม่รวมภาษี)',
  `customer_id` char(36) DEFAULT NULL COMMENT 'รหัสลูกค้า (เชื่อมโยงกับตาราง customers)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตข้อมูลล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัปเดตข้อมูล',
  `product_id` char(36) DEFAULT NULL COMMENT 'รหัสสินค้า (เชื่อมโยงกับตาราง products)',
  `vat` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่ม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('7c67ce7e-ee05-487f-a763-4627899516bb', 'โครงการ บ่อวิน สมาร์ท ซิตี้ ดูแลสุขภาพแบบอัจฉริยะ (Smart Health Care) สำหรับผู้สูงอายุ ประจำปีงบประมาณ 2567', '2023-09-02', '2024-09-02', 'Win', '1/2567', '', '2023-09-15', '3', 670000.00, 716900.00, 200000.00, 214000.00, 470000.00, 70.15, 670000.00, 200000.00, 470000.00, '32104ee7-4b28-400b-bb7b-1ab55e1cf19d', '2024-10-12 06:29:28', '3', '2024-10-12 06:29:28', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00);

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
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างรายการ (เชื่อมโยงกับตาราง users)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` char(36) NOT NULL COMMENT 'รหัสทีม (UUID)',
  `team_name` varchar(255) NOT NULL COMMENT 'ชื่อทีม',
  `team_description` text DEFAULT NULL COMMENT 'รายละเอียดของทีม',
  `created_by` char(36) NOT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัปเดตข้อมูล',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตข้อมูลล่าสุด',
  `team_leader` char(36) DEFAULT NULL COMMENT 'รหัสหัวหน้าทีม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_id`, `team_name`, `team_description`, `created_by`, `created_at`, `updated_by`, `updated_at`, `team_leader`) VALUES
('1', 'Innovation', 'Product  Solution Teams', '2', '2024-09-26 03:35:50', '2', '2024-10-12 06:17:10', '5'),
('2', 'Sales A', 'Internal Sales Teams', '2', '2024-09-26 03:35:50', '2', '2024-10-12 06:17:11', '2'),
('3', 'Service', 'Internal Service', '2', '2024-09-26 03:35:50', '2', '2024-10-12 06:17:13', '2'),
('4', 'Point IT', 'Point IT Consulting Co. Ltd.', '2', '2024-09-26 03:35:50', '3', '2024-10-12 06:32:13', '2');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `email`, `role`, `team_id`, `position`, `phone`, `password`, `company`, `created_at`, `created_by`, `profile_image`) VALUES
('1', 'Parichart', 'Thonsuk', 'Sale', 'Parichart@gmail.com', 'Seller', '1', 'Sales', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co. Ltd', '2024-09-15 16:43:58', '2', ''),
('2', 'Systems', 'Admin', 'Admin', 'Systems_admin@gmail.com', 'Executive', '1', 'Systems Admin', '0839595800', '$2y$10$jcmTr.I9CthXOrWFC78XjuOjwPoZlbvF80M4RKow4RvnNbm1Ej8dO', 'Point IT Consulting Co. Ltd', '2024-09-15 16:43:58', '2', '670a138b6529d.jpg'),
('3', 'Apirak', 'Bangpuk', 'Supervisor', 'apirak.ba@gmail.com', 'Sale Supervisor', '1', 'Sale Supervisor', '0839595811', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co. Ltd', '2024-09-15 16:43:58', '2', NULL),
('4', 'Phisuit', 'PongSopa', 'Support', 'Phisuit@gmail.com', 'Engineer', '1', 'Tecnical Support', '0839595811', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co. Ltd', '2024-09-15 16:55:43', '2', NULL),
('5', 'Panit', 'Poapun', 'Panit', 'Panit@poinitit.co.th', 'Executive', '4', 'Executive Director', '0839595822', '$2y$10$eAar02e4iaTG6bhKs2XLfua7ck.2co.8dkla8VX0tVCC5cnQfc/E6', 'Point IT Consulting Co. Ltd', '2024-09-17 15:15:37', '2', NULL);

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
(3, '3', '0a9fec94-57d3-4f65-aeaa-052cba2c3b62', 'Seller', '2024-10-11 16:18:12');

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
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `projects_ibfk_2` (`created_by`),
  ADD KEY `projects_ibfk_3` (`seller`),
  ADD KEY `projects_customer_fk` (`customer_id`),
  ADD KEY `projects_product_fk` (`product_id`) USING BTREE;

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD UNIQUE KEY `fk_project_documents_project` (`project_id`),
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
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_creation_logs`
--
ALTER TABLE `user_creation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_image` FOREIGN KEY (`image_id`) REFERENCES `category_image` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`seller`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `projects_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`team_leader`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_team_fk` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
