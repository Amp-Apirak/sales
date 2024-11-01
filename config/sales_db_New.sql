-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2024 at 04:34 PM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_project_cost_summary` (IN `p_project_id` CHAR(36))   BEGIN
    DECLARE v_total_amount decimal(15,2);
    DECLARE v_total_cost decimal(15,2);
    
    -- คำนวณยอดรวม
    SELECT 
        COALESCE(SUM(quantity * price_per_unit), 0),
        COALESCE(SUM(quantity * cost_per_unit), 0)
    INTO v_total_amount, v_total_cost
    FROM project_costs 
    WHERE project_id = p_project_id;
    
    -- คำนวณภาษีและกำไร
    SET @vat_rate = 0.07;
    SET @vat_amount = v_total_amount * @vat_rate;
    SET @cost_vat_amount = v_total_cost * @vat_rate;
    SET @grand_total = v_total_amount + @vat_amount;
    SET @total_cost_with_vat = v_total_cost + @cost_vat_amount;
    SET @profit = @grand_total - @total_cost_with_vat;
    SET @profit_percentage = IF(@grand_total > 0, (@profit / @grand_total) * 100, 0);

    -- บันทึกหรืออัพเดทข้อมูลสรุป
    INSERT INTO project_cost_summary (
        project_id, 
        total_amount,
        total_cost,
        vat_amount,
        cost_vat_amount,
        profit_amount,
        profit_percentage
    ) VALUES (
        p_project_id,
        v_total_amount,
        v_total_cost,
        @vat_amount,
        @cost_vat_amount,
        @profit,
        @profit_percentage
    ) ON DUPLICATE KEY UPDATE
        total_amount = v_total_amount,
        total_cost = v_total_cost,
        vat_amount = @vat_amount,
        cost_vat_amount = @cost_vat_amount,
        profit_amount = @profit,
        profit_percentage = @profit_percentage;
END$$

DELIMITER ;

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
('2038ed09-b894-4b58-94c0-c5d4bf272f95', 'คุณตรีเทศ หะหวัง', '99 ถนนแจ้งวัฒนะ แขวงทุ่งสองห้อง เขตหลักสี่ กรุงเทพมหานคร 10210', '', '0894822387', 'treeted@nt.ntplc.co.th', '', '3', '2024-10-20 13:24:08', '', '', '', '', '2024-10-20 13:24:08'),
('5e2a838a-110f-48bc-9518-f01a7066955b', 'นายอิทธิกร เล่นวารี  (นายกเทศมนตรีตำบลปากท่อ)', 'สำนักงานเทศบาลตำบลปากท่อ จ. ราชบุรี', '39 หมู่ที่ 7 ต.ปากท่อ อ.ปากท่อ จ. ราชบุรี 70140 โทรศัพท์ 032-281-266 โทรสาร 032-282-564', '0806508585', 'pakthocity@hotmail.com', 'http://www.pakthomunic.go.th/office.php', '2', '2024-10-12 06:24:53', '3', '6715036cbd552.jpg', '', '', '2024-10-20 13:19:40'),
('dc56497c-7881-4d04-ae50-6cd10818744a', 'นงนุช โกวิทวณิช', 'BUSINESS SOLUTIONS PROVIDER CO.,LTD.', '7/129 18th Floor., Baromrajchonnee Rd.,Arunammarin, Bangkok-Noi, Bangkok. 10700', '0619522110', 'nongnuch@bspc.co.th', '', '3', '2024-10-20 13:23:34', '', '', '', '', '2024-10-20 13:23:34'),
('ed698e0f-d528-4f14-b0bf-77c17c223610', 'นายสิรวิชฐ์ อำไพวงษ์ (ท่านนายก)', 'องค์การบริหารส่วนตำบลบ่อวิน', 'องค์การบริหารส่วนตำบลบ่อวิน เลขที่ 1 หมู่ที่ 6 ตำบลบ่อวิน อำเภอศรีราชา จังหวัดชลบุรี 20230 โทรศัพท์ 0-3834-5949 ,0-3834-5918 โทรสาร 0-3834-6116 สายด่วนร้องทุกข์ 24 ชม. 08-1949-7771 นายกเทศบาลตาบลบ่อวิน องค์การบริหารส่วนตำบลบ่อวิน', '0819497771', 'admin@bowin.go.th', '', '3', '2024-10-20 13:22:35', '', '6715041b405ed.png', '0-3834-5949', '', '2024-10-20 13:22:35');

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
  `updated_by` int(11) DEFAULT NULL COMMENT 'ผู้อัพเดทข้อมูลล่าสุด',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันอัพเดทข้อมูลล่าสุด',
  `main_image` varchar(255) DEFAULT NULL COMMENT 'รูปหลักของสินค้า'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `created_by`, `created_at`, `updated_by`, `updated_at`, `main_image`) VALUES
('2d4febe3-20e4-4c4c-b9db-ed8eee716291', 'Software Devlopment', 'การพัฒนาระบบตามความต้องการของลูกค้า', '3', '2024-10-20 13:25:39', NULL, '2024-10-20 13:25:39', NULL),
('4c85d842-54f3-4f06-87e6-553f81488234', 'Smart Emergency', 'ระบบเฝ้าระวังเหตุฉุกเฉิน', '2', '2024-10-12 06:18:20', 3, '2024-10-20 13:35:30', '4c85d842-54f3-4f06-87e6-553f81488234.png'),
('9e0f64b9-9b1f-47dd-bd7f-d442a41986a2', 'MA', 'Maintenance Service Agreement หรือที่เราเรียกกันสั้นๆว่า MA คือ บริการดูแลและบำรุงรักษา อาทิเช่น อุปกรณ์เครือข่าย Network เครื่องคอมพิวเตอร์แม่ข่าย (Server) เครื่องลูกข่าย (Client) หรือ Workstation ตลอดจนระบบโปรแกรมใช้งานต่างๆ (Application Software) ให้อยู่ในสภาพพร้อมใช้งานตลอดเวลา', '3', '2024-10-20 13:37:10', NULL, '2024-10-20 13:37:10', NULL),
('b9fcda13-e694-4e04-a8df-fdf27ee08979', 'BioIDM Face Scan', 'ระบบยืนยันตัวตน ผ่านการเปรียบเทียบใบหน้า บัตรประจำตัวประชาชน และอื่นๆ', '2', '2024-10-12 06:19:12', 3, '2024-10-20 13:31:08', 'b9fcda13-e694-4e04-a8df-fdf27ee08979.png'),
('eedfc5c7-71e7-4ac2-8160-d2fcee41c1c0', 'Smart Healthcare', 'ชุดกระเป๋า (Health Kit Set) สำหรับตรวจสุขคัดกรอกสถานะสุขภาพเคลื่อนที่ เก็บค่าข้อมูลเข้าระบบ โดยการตรวจวัดค่าจากอุปกรณ์เชื่อมต่อเข้ากับระบบ', '3', '2024-10-20 13:34:49', NULL, '2024-10-20 13:34:49', 'eedfc5c7-71e7-4ac2-8160-d2fcee41c1c0.png');

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
('2ffd7f05-f010-4fb7-a1ba-16d0404754e8', 'โครงการพัฒนางานระบบจัดซื้อจัดจ้าง  กบข.', '2023-12-25', '2024-12-25', 'Win', 'PO2024012', '', '2023-10-20', '3', 3200000.00, 3424000.00, 2650000.00, 2835500.00, 550000.00, 17.19, 3200000.00, 2650000.00, 550000.00, 'dc56497c-7881-4d04-ae50-6cd10818744a', '2024-10-20 13:57:32', '3', '2024-10-20 13:57:32', NULL, '2d4febe3-20e4-4c4c-b9db-ed8eee716291', 7.00),
('4485a26a-c22e-47c4-90ca-a2db58251b46', 'งานจ้างบำรุงรักษาระบบ MOBILE FACE RECOGNITION', '2024-06-01', '2025-08-03', 'Win', 'A02/3160030757/2567', '', '2024-06-01', '3', 1073708.41, 1148868.00, 791114.96, 846493.01, 282593.45, 26.32, 1073708.41, 791114.96, 282593.45, '2038ed09-b894-4b58-94c0-c5d4bf272f95', '2024-10-20 13:49:58', '3', '2024-10-20 13:49:58', NULL, '9e0f64b9-9b1f-47dd-bd7f-d442a41986a2', 7.00),
('476239df-f7ca-4a7c-9dc2-29e358ba7182', 'โครงการจ้างระบบแพลตฟอร์มวิเคราะห์ข้อมูล ปัญญาประดิษฐ์ในการบริการดูแลการใช้ชีวิตและดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุและผู้ที่มีภาวะพึ่งพิง', '2024-09-27', '2025-09-09', 'Win', '๖/๒๕๖๗', '', '2024-09-06', '3', 266822.43, 285500.00, 198336.00, 212219.52, 68486.43, 25.67, 266822.43, 198336.00, 68486.43, '5e2a838a-110f-48bc-9518-f01a7066955b', '2024-10-20 13:42:20', '3', '2024-10-20 13:42:20', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('4b8552f7-d752-47e5-84e9-2215cac2ca1e', 'โครงการ บ่อวิน สมาร์ท ซิตี้ ดูแลสุขภาพแบบอัจฉริยะ (Smart Health Care) สำหรับผู้สูงอายุ ประจำปีงบประมาณ 2567', '2023-09-20', '2024-09-02', 'Win', '1/2567', '', '2023-09-15', '3', 623831.78, 667500.00, 423848.00, 453517.36, 199983.78, 32.06, 623831.78, 423848.00, 199983.78, 'ed698e0f-d528-4f14-b0bf-77c17c223610', '2024-10-20 13:46:39', '3', '2024-10-20 13:46:39', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00);

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
('476239df-f7ca-4a7c-9dc2-29e358ba7182', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-10-31 15:34:01');

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
('3f7f9521-dabe-4d36-82be-aa872b14b9d7', '476239df-f7ca-4a7c-9dc2-29e358ba7182', 'SO', 'pdf', '../../uploads/project_documents/Innovation/476239df-f7ca-4a7c-9dc2-29e358ba7182/67150923e8ed0.pdf', 124502, '2024-10-20 13:44:03', '3');

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
('1f0f9c78-b28e-45f7-b2c6-b005fc1f0b25', '476239df-f7ca-4a7c-9dc2-29e358ba7182', '670f40da5ac76.jpg', '../../uploads/project_images/Innovation/476239df-f7ca-4a7c-9dc2-29e358ba7182/67150943462d5.jpg', 'image/jpeg', 291125, '2024-10-20 13:44:35', '3', NULL),
('8f7dbd64-765f-44ff-bde7-731f81ec9bc2', '476239df-f7ca-4a7c-9dc2-29e358ba7182', '670f4189888ef.jpg', '../../uploads/project_images/Innovation/476239df-f7ca-4a7c-9dc2-29e358ba7182/6715094349fd7.jpg', 'image/jpeg', 460243, '2024-10-20 13:44:35', '3', NULL);

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
('052f39bf-8720-4cc3-9b59-5aa317e77998', '4485a26a-c22e-47c4-90ca-a2db58251b46', 6, 89439.91, 8.33, 0.00, NULL, '2024-12-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:54:07', '2024-10-20 13:54:07', '3', ''),
('053a4f36-b2c9-45b8-b6b0-b705b6f6037e', '4485a26a-c22e-47c4-90ca-a2db58251b46', 4, 89439.91, 8.33, 89439.91, NULL, '2024-10-01', 'Paid', NULL, NULL, NULL, '2024-10-20 13:53:17', '2024-10-20 13:53:17', '3', ''),
('0a52b90c-a9d0-4d37-bc35-01ae144b37b0', '4b8552f7-d752-47e5-84e9-2215cac2ca1e', 1, 311915.89, 50.00, 0.00, NULL, '0000-00-00', 'Pending', NULL, NULL, NULL, '2024-10-20 13:47:39', '2024-10-20 13:47:39', '3', ''),
('10419396-0c66-4bbc-b266-c24c1d3f81d3', '4485a26a-c22e-47c4-90ca-a2db58251b46', 3, 89439.91, 8.33, 89439.91, NULL, '2024-09-01', 'Paid', NULL, NULL, NULL, '2024-10-20 13:52:48', '2024-10-20 13:52:48', '3', ''),
('1354fedf-df49-455e-ab57-78ca2f229c84', '7c67ce7e-ee05-487f-a763-4627899516bb', 1, 335000.00, 50.00, 335000.00, '2024-10-19', '2024-10-19', 'Paid', NULL, NULL, NULL, '2024-10-19 07:07:42', '2024-10-19 07:08:05', '2', '2'),
('1614fb33-6338-48ff-935a-6d809042c589', '4485a26a-c22e-47c4-90ca-a2db58251b46', 5, 89439.91, 8.33, 0.00, NULL, '2024-11-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:53:49', '2024-10-20 13:53:49', '3', ''),
('413b213c-7c0f-4480-9685-61f0c0ca6e08', '4485a26a-c22e-47c4-90ca-a2db58251b46', 7, 89439.91, 8.33, 0.00, NULL, '2025-01-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:54:21', '2024-10-20 13:54:21', '3', ''),
('556665dc-62c6-4bc0-a06c-37545a5c8f39', '4b8552f7-d752-47e5-84e9-2215cac2ca1e', 2, 311915.89, 50.00, 0.00, NULL, '0000-00-00', 'Pending', NULL, NULL, NULL, '2024-10-20 14:25:31', '2024-10-20 14:25:31', '3', ''),
('5919a8a0-5b8c-477c-a423-0ebe9e947fa0', '2ffd7f05-f010-4fb7-a1ba-16d0404754e8', 1, 480000.00, 15.00, 480000.00, NULL, '2024-02-01', 'Paid', NULL, NULL, NULL, '2024-10-20 13:58:08', '2024-10-20 13:58:08', '3', ''),
('5c7aa110-b1fe-442d-bb2f-c2faa632fd53', '4485a26a-c22e-47c4-90ca-a2db58251b46', 1, 89475.70, 8.33, 89475.70, NULL, '2024-07-01', 'Paid', NULL, NULL, NULL, '2024-10-20 13:52:03', '2024-10-20 13:52:03', '3', ''),
('5de0badf-ce6b-4fd1-b9f4-31820086a7ef', 'a7c5d78d-c1a0-4f60-ba74-ce124795ca29', 1, 335000.00, 50.00, 0.00, NULL, '0000-00-00', 'Pending', NULL, NULL, NULL, '2024-10-19 13:25:15', '2024-10-19 13:25:15', '2', ''),
('5fede5fc-a506-44b6-af26-28470d7f476f', '4485a26a-c22e-47c4-90ca-a2db58251b46', 2, 89475.70, 8.33, 89475.70, NULL, '2024-08-01', 'Paid', NULL, NULL, NULL, '2024-10-20 13:52:27', '2024-10-20 13:52:27', '3', ''),
('6d235761-3f9f-4e65-8389-b63cbc580f01', '2ffd7f05-f010-4fb7-a1ba-16d0404754e8', 3, 1120000.00, 35.00, 0.00, NULL, '2024-09-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:58:50', '2024-10-20 13:58:50', '3', ''),
('8ba65372-ed52-4865-8526-450d76542ca0', '4485a26a-c22e-47c4-90ca-a2db58251b46', 8, 89439.91, 8.33, 0.00, NULL, '2025-02-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:54:40', '2024-10-20 13:54:40', '3', ''),
('9525eefb-d61f-4952-9872-0e59a90ebd39', '4485a26a-c22e-47c4-90ca-a2db58251b46', 9, 89439.91, 8.33, 0.00, NULL, '2025-03-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:54:53', '2024-10-20 13:54:53', '3', ''),
('9fc44d3b-0ab2-444e-9cdb-1fe4337a5ff8', '4485a26a-c22e-47c4-90ca-a2db58251b46', 11, 89439.91, 8.33, 0.00, NULL, '2025-04-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:55:22', '2024-10-20 13:55:22', '3', ''),
('cd4517a7-7de3-4a28-86ee-038bda9764ce', '4485a26a-c22e-47c4-90ca-a2db58251b46', 12, 89439.91, 8.33, 0.00, NULL, '2025-05-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:55:37', '2024-10-20 13:55:37', '3', ''),
('d44bd458-1221-4be6-bf34-a3738c86c216', '2ffd7f05-f010-4fb7-a1ba-16d0404754e8', 4, 960000.00, 30.00, 0.00, NULL, '2024-12-30', 'Pending', NULL, NULL, NULL, '2024-10-20 13:59:12', '2024-10-20 13:59:12', '3', ''),
('db923d01-5cc8-4ff8-b3e4-27a1cbbbf696', '4485a26a-c22e-47c4-90ca-a2db58251b46', 10, 89439.91, 8.33, 0.00, NULL, '2025-03-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:55:06', '2024-10-20 13:55:06', '3', ''),
('f66de228-eeb1-4908-9337-9eae9ed71454', '2ffd7f05-f010-4fb7-a1ba-16d0404754e8', 2, 640000.00, 20.00, 0.00, NULL, '2024-08-01', 'Pending', NULL, NULL, NULL, '2024-10-20 13:58:23', '2024-10-20 13:58:23', '3', ''),
('fe4e51a9-5e39-4d73-8c37-a692187d2136', '476239df-f7ca-4a7c-9dc2-29e358ba7182', 1, 266822.43, 100.00, 0.00, NULL, '2024-10-31', 'Pending', NULL, NULL, NULL, '2024-10-20 13:43:00', '2024-10-20 13:43:00', '3', '');

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
('1', 'Innovation', 'Product  Solution Teams', '2', '2024-09-26 03:35:50', '2', '2024-10-12 06:17:10', '5'),
('2', 'Sales A', 'Internal Sales Teams', '2', '2024-09-26 03:35:50', '2', '2024-10-12 06:17:11', '2'),
('3', 'Service', 'Internal Service', '2', '2024-09-26 03:35:50', '2', '2024-10-12 06:17:13', '2'),
('4', 'Point IT', 'Point IT Consulting Co. Ltd.', '2', '2024-09-26 03:35:50', '2', '2024-10-20 12:39:29', '5');

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
('1', 'Parichart', 'Thonsuk', 'Sale', 'Parichart@gmail.com', 'Seller', '1', 'Sales', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co. Ltd', '2024-09-15 16:43:58', '2', ''),
('2', 'Systems', 'Admin', 'Admin', 'Systems_admin@gmail.com', 'Executive', '1', 'Systems Admin', '0839595800', '$2y$10$jcmTr.I9CthXOrWFC78XjuOjwPoZlbvF80M4RKow4RvnNbm1Ej8dO', 'Point IT Consulting Co. Ltd', '2024-09-15 16:43:58', '2', '6713cc8ba31a5.jpg'),
('3', 'Apirak', 'Bangpuk', 'Supervisor', 'apirak.ba@gmail.com', 'Sale Supervisor', '1', 'Sale Supervisor', '0839595811', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co. Ltd', '2024-09-15 16:43:58', '2', NULL),
('4', 'Phisuit', 'PongSopa', 'Support', 'Phisuit@gmail.com', 'Engineer', '1', 'Tecnical Support', '0839595811', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co. Ltd', '2024-09-15 16:55:43', '2', NULL),
('5', 'Panit', 'Poapun', 'Panit', 'Panit@poinitit.co.th', 'Executive', '4', 'Executive Director', '0839595822', '$2y$10$eAar02e4iaTG6bhKs2XLfua7ck.2co.8dkla8VX0tVCC5cnQfc/E6', 'Point IT Consulting Co. Ltd', '2024-09-17 15:15:37', '2', NULL),
('bb027ef6-7b53-4775-92d6-8faa0e1f7abc', 'ผู้ขาย', 'สินค้าปกติ', 'sale2', 'apirakc@gmail.com', 'Seller', '3', 'IT support', '0839595833', '$2y$10$eAar02e4iaTG6bhKs2XLfua7ck.2co.8dkla8VX0tVCC5cnQfc/E6', 'Point IT Consulting Co. Ltd', '2024-10-20 11:55:07', '2', '6714ef9bf3362.jpg');

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
(4, '2', 'bb027ef6-7b53-4775-92d6-8faa0e1f7abc', 'Seller', '2024-10-20 11:55:08');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
