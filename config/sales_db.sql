-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2024 at 12:05 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

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
('cHErWjdqY25XRjhoQmhxREpSVnJ3QT09', 'เครือข่าย', 'การเชื่อมต่อเครือข่าย', 'LAN', 'การเชื่อมต่อล้มเหลว', 'ผู้ใช้ไม่สามารถเข้าถึงอินเทอร์เน็ต', 'ตรวจสอบการตั้งค่า IP และรีสตาร์ทอุปกรณ์', NULL, '2024-10-04 18:35:54', '2024-10-06 11:49:40', '2');

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
  `category_id` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_image`
--

INSERT INTO `category_image` (`id`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`, `updated_at`, `category_id`) VALUES
('b16fcce8-0615-cf73-2ccc-270919ad597f', '67027cd979b2f.png', '../../uploads/category_images/67027cd979b2f.png', 'image/png', 241422, '2024-10-06 12:04:41', '2024-10-06 12:04:41', 'cHErWjdqY25XRjhoQmhxREpSVnJ3QT09');

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
  `customer_id` char(36) NOT NULL COMMENT 'รหัสลูกค้า (UUID)',
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
('9c4b87ca-3e3e-454d-ab77-b1ddd4a1f20b', 'คุณภัทราอร อมรโอภาคุณ', 'บริษัท พอยท์ ไอที คอนซัลทิ่ง จำกัด', 'พอยท์ ไอที คอนซัลทิ่ง จำกัด บริษัท พอยท์ ไอที คอนซัลทิ่ง จำกัด ซอย สุภาพงษ์ 1 แยก 6 แขวงหนองบอน เขต ประเวศ กรุงเทพมหานคร', '0619522111', 'phattraorn@pointit.co.th', '', 'c3f5b615-4b91-407a-80d7-ff6ef1995b10', '2024-10-09 06:44:09'),
('d1cef52a-afe7-42de-94b3-a18951ad9c9c', 'นายสิรวิชฐ์ อำไพวงษ์ (ท่านนายก)', 'องค์การบริหารส่วนตำบลบ่อวิน', 'องค์การบริหารส่วนตำบลบ่อวิน เลขที่ 1 หมู่ที่ 6 ตำบลบ่อวิน อำเภอศรีราชา จังหวัดชลบุรี 20230 โทรศัพท์ 0-3834-5949 ,0-3834-5918 โทรสาร 0-3834-6116 สายด่วนร้องทุกข์ 24 ชม. 08-1949-7771 นายกเทศบาลตาบลบ่อวิน องค์การบริหารส่วนตำบลบ่อวิน', '038345949', 'admin@bowin.go.th', '', '056adbd1-a6fc-46ac-b531-ef4aecb955d4', '2024-10-09 09:53:27');

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
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้อัพเดทข้อมูลล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `created_by`, `created_at`, `updated_by`) VALUES
('1', 'Health Care', 'ชุดกระเป๋า (Health Kit Set) สำหรับตรวจสุขคัดกรอกสถานะสุขภาพเคลื่อนที่ เก็บค่าข้อมูลเข้าระบบ โดยการตรวจวัดค่าจากอุปกรณ์เชื่อมต่อเข้ากับระบบ', '2', '2024-09-24 15:46:16', '2'),
('10', 'Emergency', 'ระบบเฝ้าระวังเหตุฉุกเฉิน', '2', '2024-09-24 15:46:16', '2'),
('10492a04-64ce-46c9-8ec1-89cd99c12fa5', 'Software Deerlopment', 'การพัฒนาระบบตามความต้องการของลูกค้า', 'c3f5b615-4b91-407a-80d7-ff6ef1995b10', '2024-10-09 06:41:03', NULL),
('2', 'BioIDM Face Scan', 'ระบบยืนยันตัวตน ผ่านการเปรียบเทียบใบหน้า บัตรประจำตัวประชาชน และอื่นๆ', '2', '2024-09-24 15:46:16', '2'),
('3', 'IBOC', 'มหาวิทยาลัยขอนแก่น', '2', '2024-09-24 15:46:16', '2');

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
  `updated_by` varchar(36) DEFAULT NULL COMMENT 'รหัสผู้ที่อัปเดตข้อมูล',
  `product_id` char(36) DEFAULT NULL COMMENT 'รหัสสินค้า (เชื่อมโยงกับตาราง products)',
  `vat` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่ม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('56340abb-864b-43e0-9772-f369491aa609', 'โครงการ บ่อวิน สมาร์ท ซิตี้ ดูแลสุขภาพแบบอัจฉริยะ (Smart Health Care) สำหรับผู้สูงอายุ ประจำปีงบประมาณ 2567', '2023-10-02', '2024-09-02', 'Win', '1/2567', '', '2023-09-15', '056adbd1-a6fc-46ac-b531-ef4aecb955d4', '670000.00', '716900.00', '200000.00', '214000.00', '470000.00', '70.15', '670000.00', '200000.00', '470000.00', NULL, '2024-10-09 09:51:12', '056adbd1-a6fc-46ac-b531-ef4aecb955d4', '2024-10-09 09:51:12', NULL, '10', '7.00'),
('b3bd007c-998f-457c-a1ba-cd479d89898d', 'โครงการการจ้างพัฒนาระบบการจัดซื้อจัดจ้าง กบข.', '2023-10-20', '2024-10-20', 'Win', '', 'ชำระงวดเงิน 4 งวด แบ่งเป็น 15% , 20%,35%,30%', '2023-10-15', '056adbd1-a6fc-46ac-b531-ef4aecb955d4', '3200000.00', '3424000.00', '2550000.00', '2728500.00', '650000.00', '20.31', '3200000.00', '2550000.00', '650000.00', '9c4b87ca-3e3e-454d-ab77-b1ddd4a1f20b', '2024-10-09 09:39:56', '056adbd1-a6fc-46ac-b531-ef4aecb955d4', '2024-10-09 09:39:56', NULL, '10492a04-64ce-46c9-8ec1-89cd99c12fa5', '7.00');

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
('1', 'Innovation', 'ทีม Product ', '', '2024-09-26 03:35:50', NULL, '2024-09-26 17:00:17', '1'),
('3', 'Service', 'ทีมให้บริการ', '', '2024-09-26 03:35:50', NULL, '2024-09-26 17:00:41', '2'),
('4', 'Point IT', 'บริษัทพอทไอที คอนซัลทิ่งจำกัด', '', '2024-09-26 03:35:50', NULL, '2024-09-26 17:01:02', '2');

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
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `email`, `role`, `team_id`, `position`, `phone`, `password`, `company`, `created_at`, `created_by`) VALUES
('056adbd1-a6fc-46ac-b531-ef4aecb955d4', 'ภัทราอร', 'อมรโอภาคุณ', 'Phattraorn', 'phattraorn@pointit.co.th', 'Sale Supervisor', '1', 'Product Sale', '0619522111', '$2y$10$BbGPBP99Xy0i5dT8Gx.YMui1BQlXTwVoXF/UK.354QNv93VEQFJhq', 'Point IT Consulting Co. Ltd', '2024-10-09 06:09:06', '2'),
('1', 'Systems', 'Admin', 'Systems', 'Systems@gmail.com', 'Executive', '4', 'Systems Admin', '0811111111', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co. Ltd', '2024-09-15 16:43:58', NULL),
('2', 'Apirak', 'Bangpuk', 'Admin', 'Apirak@gmail.com', 'Executive', '1', 'IT Service Management', '0839595800', '$2y$10$jcmTr.I9CthXOrWFC78XjuOjwPoZlbvF80M4RKow4RvnNbm1Ej8dO', 'Point IT Consulting Co. Ltd', '2024-09-15 16:43:58', NULL),
('80b63cce-54ad-49e9-975e-c8f0ca40f576', 'ธนาคม', 'อ่องสถาน', 'Tanacom', 'Tanacom@pointit.co.th', 'Engineer', '3', 'Tecnical Support', '0897771155', '$2y$10$36cNexS8GlB0/7Rl2a4qRe3SPFFYZW5blfdpPC5f0cPDwaVyX29hC', 'Point IT Consulting Co. Ltd', '2024-10-09 06:15:11', '2'),
('c3f5b615-4b91-407a-80d7-ff6ef1995b10', 'ผาณิต', 'เผ่าพันธ์', 'Panit', 'panit@pointit.co.th', 'Executive', '4', 'Executive Director', '0869958396', '$2y$10$jhrhk3ciz1w6RVpRYprAyuFX9ugLqmejoRHDGBg8udNHA23aVl5DG', 'Point IT Consulting Co. Ltd', '2024-10-09 06:10:25', '2'),
('cb4e50d1-c62f-488c-97c6-8e049ea3ac58', 'พิสุทธ์', 'วงศ์โสภา', 'Phisuit', 'Service@pointit.co.th', 'Seller', '3', 'Tecnical Support', '0915450988', '$2y$10$EwDRqvGY.ZSbMxyAIwMmxuWJfqtDyIkLeQ/sB3g2UhdlVxuotw..u', 'Point IT Consulting Co. Ltd', '2024-10-09 06:13:59', '2');

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
  ADD KEY `projects_product_fk` (`product_id`),
  ADD KEY `projects_customer_fk` (`customer_id`);

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
