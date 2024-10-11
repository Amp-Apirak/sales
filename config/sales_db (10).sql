-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2024 at 08:20 PM
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
  `customer_id` char(36) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `company`, `address`, `phone`, `email`, `remark`, `created_by`, `created_at`) VALUES
('0', 'Apirapuk', 'PIT', 'เลขที่ 111/1 ธนพงษ์แมนชั่น ห้อง. 302 ซ. สันนิบาตเทศบาล แขวง จันทรเกษม', '0839595800', 'apira@gmail.com', 'เลขที่ 111/1 ธนพงษ์แมนชั่น ห้อง. 302 ซ. สันนิบาตเทศบาล แขวง จันทรเกษม', 2, '2024-09-25 08:16:44'),
('1', 'John Doe', 'TechCorp', '123 Main St, City A', '555-1234', 'john.doe@techcorp.com', 'ลูกค้าประจำ', 1, '2024-09-20 15:03:08'),
('10', 'Emma White', 'AutoMechanic', '707 Pinecone St, City J', '555-6789', 'emma.white@automechanic.com', 'ลูกค้าใหม่', 3, '2024-09-20 15:03:08'),
('11', 'Noah Harris', 'GreenEnergy', '808 Redwood St, City K', '555-2345', 'noah.harris@greenenergy.com', '', 2, '2024-09-20 15:03:08'),
('12', 'Ava Lewis', 'HealthPlus', '909 Sequoia St, City L', '555-5678', 'ava.lewis@healthplus.com', 'ลูกค้าประจำ', 1, '2024-09-20 15:03:08'),
('13', 'Benjamin Walker', 'BuildFuture', '1000 Willow St, City M', '555-7890', 'benjamin.walker@buildfuture.com', '', 2, '2024-09-20 15:03:08'),
('14', 'Mia Hall', 'StartUpLab', '1100 Cypress St, City N', '555-9012', 'mia.hall@startuplab.com', 'ลูกค้าใหม่', 3, '2024-09-20 15:03:08'),
('15', 'Lucas Clark', 'FinTech Solutions', '1200 Dogwood St, City O', '555-3456', 'lucas.clark@fintechsolutions.com', '', 1, '2024-09-20 15:03:08'),
('2', 'Jane Smith', 'Innovate Inc', '456 Elm St, City B', '555-5678', 'jane.smith@innovate.com', 'ลูกค้าใหม่', 2, '2024-09-20 15:03:08'),
('3', 'Michael Brown', 'Design Solutions', '789 Oak St, City C', '555-7890', 'michael.brown@design.com', '', 1, '2024-09-20 15:03:08'),
('4', 'Emily Davis', 'BuildIt', '101 Pine St, City D', '555-2345', 'emily.davis@buildit.com', 'ลูกค้าโครงการใหญ่', 3, '2024-09-20 15:03:08'),
('5', 'William Johnson', 'ConstructCo', '202 Maple St, City E', '555-6789', 'william.johnson@constructco.com', '', 2, '2024-09-20 15:03:08'),
('6', 'Olivia Wilson', 'WebCreatives', '303 Birch St, City F', '555-3456', 'olivia.wilson@webcreatives.com', 'ลูกค้าโครงการเล็ก', 1, '2024-09-20 15:03:08'),
('7', 'James Taylor', 'MarketingPro', '404 Cedar St, City G', '555-9012', 'james.taylor@marketingpro.com', '', 3, '2024-09-20 15:03:08'),
('8', 'Sophia Anderson', 'Smart Solutions', '505 Aspen St, City H', '555-7890', 'sophia.anderson@smartsolutions.com', 'ลูกค้าประจำ', 2, '2024-09-20 15:03:08'),
('8af25d3c-1e5d-4c59-9006-846911cb779a', 'ggggggggg', 'gggggggggggggg', 'gggggggggg', '0839595842', 'pirak.ba@gmail.com', 'ggggggggggggg', 3, '2024-09-28 15:37:33'),
('9', 'Liam Martinez', 'SecurityTech', '606 Spruce St, City I', '555-4567', 'liam.martinez@securitytech.com', '', 1, '2024-09-20 15:03:08'),
('99bd8425-10a1-4f31-92a7-95d7cfe5648a', 'ทดสอบ', 'ทดสอบ', 'ทดสอบ', '0839595654', 'sdfs@gmail.com', 'sfdsfdfd', 2, '2024-09-25 11:09:17'),
('d178cd84-7ad8-4225-b044-381bd602a1e2', 'หดหกดหกด', 'หกดกหดกหด', 'กหดกหดกหดกห', '0839596547', 'ba@gmail.com', 'เหกเกเกหดเกหเหกด', 2, '2024-09-25 11:15:48'),
('e168d70a-ac37-49bd-8eb0-3c28e8696238', 'กหดฟกหดด', 'ฟกหดฟกหด', 'ฟกหดฟกหดฟกห', '0839545678', 'sdfdsfs@gmail.com', 'sdfdsfsdfdsf', 2, '2024-09-25 11:12:28');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` char(36) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `created_by`, `created_at`) VALUES
('1', 'Product A', 'This is a description for Product A.', '1', '2024-09-24 15:46:16'),
('10', 'Product J', 'This is a description for Product J.', '5', '2024-09-24 15:46:16'),
('2', 'Product B', 'This is a description for Product B.', '1', '2024-09-24 15:46:16'),
('3', 'Product C', 'This is a description for Product C.', '2', '2024-09-24 15:46:16'),
('4', 'Product D', 'This is a description for Product D.', '2', '2024-09-24 15:46:16'),
('5', 'Product E', 'This is a description for Product E.', '3', '2024-09-24 15:46:16'),
('6', 'Product F', 'This is a description for Product F.', '3', '2024-09-24 15:46:16'),
('7', 'Product G', 'This is a description for Product G.', '4', '2024-09-24 15:46:16'),
('8', 'Product H', 'This is a description for Product H.', '4', '2024-09-24 15:46:16'),
('87438610-6611-4d56-b7dd-432f2a4da196', 'ทดสอบ', 'ทดสอบ', '3', '2024-09-30 15:17:13'),
('9', 'Product I', 'This is a description for Product I.', '5', '2024-09-24 15:46:16');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` char(36) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `contract_no` varchar(50) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `sales_date` date DEFAULT NULL,
  `seller` char(36) DEFAULT NULL,
  `sale_no_vat` decimal(10,2) DEFAULT NULL,
  `sale_vat` decimal(10,2) DEFAULT NULL,
  `cost_no_vat` decimal(10,2) DEFAULT NULL,
  `cost_vat` decimal(10,2) DEFAULT NULL,
  `gross_profit` decimal(10,2) DEFAULT NULL,
  `potential` decimal(5,2) NOT NULL COMMENT 'กำไรขั้นต้นแบบเปอร์เซ็นต์',
  `es_sale_no_vat` decimal(10,2) DEFAULT NULL,
  `es_cost_no_vat` decimal(10,2) DEFAULT NULL,
  `es_gp_no_vat` decimal(10,2) DEFAULT NULL,
  `customer_id` char(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `product_id` char(36) DEFAULT NULL,
  `vat` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'ภาษีมูลค่าเพิ่ม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('1', 'Project Alpha', '2023-01-10', '2023-02-10', 'Win', 'CN001', 'Remark for project Alpha', '2023-01-10', '1', 95000.00, 5000.00, 85000.00, 10000.00, 10000.00, 80.50, 92000.00, 83000.00, 9000.00, '1', '2024-09-22 05:54:27', '1', '2024-09-25 04:26:30', 2, '1', 0.00),
('10', 'Project Kappa', '2023-10-10', '2023-11-10', 'Lost', 'CN010', 'Remark for project Kappa', '2023-10-15', '5', 520000.00, 30000.00, 450000.00, 70000.00, 70000.00, 81.00, 530000.00, 500000.00, 30000.00, '5', '2024-09-22 05:54:27', '5', '2024-09-25 04:27:01', 2, '8', 0.00),
('11', 'Project Lambda', '2023-11-01', '2023-12-01', 'On Hold', 'CN011', 'Remark for project Lambda', '2023-11-05', '1', 580000.00, 20000.00, 510000.00, 70000.00, 70000.00, 80.50, 590000.00, 560000.00, 30000.00, '1', '2024-09-22 05:54:27', '1', '2024-09-25 04:27:04', 3, '9', 0.00),
('12', 'Project Mu', '2023-12-10', '2024-01-10', 'Bidding', 'CN012', 'Remark for project Mu', '2023-12-15', '2', 630000.00, 20000.00, 540000.00, 90000.00, 90000.00, 82.00, 640000.00, 610000.00, 30000.00, '2', '2024-09-22 05:54:27', '2', '2024-09-25 04:27:08', 4, '4', 0.00),
('13', 'Project Nu', '2024-01-01', '2024-02-01', 'Lost', 'CN013', 'Remark for project Nu', '2024-01-05', '3', 680000.00, 20000.00, 580000.00, 100000.00, 100000.00, 82.50, 690000.00, 660000.00, 30000.00, '3', '2024-09-22 05:54:27', '3', '2024-09-25 04:27:11', 1, '6', 0.00),
('14', 'Project Xi', '2024-02-15', '2024-03-15', 'Cancelled', 'CN014', 'Remark for project Xi', '2024-02-20', '4', 720000.00, 30000.00, 620000.00, 100000.00, 100000.00, 81.00, 730000.00, 700000.00, 30000.00, '4', '2024-09-22 05:54:27', '4', '2024-09-25 04:27:15', 2, '10', 0.00),
('15', 'Project Omicron', '2024-03-05', '2024-04-05', 'Negotiation', 'CN015', 'Remark for project Omicron', '2024-03-10', '5', 770000.00, 30000.00, 670000.00, 100000.00, 100000.00, 81.50, 780000.00, 750000.00, 30000.00, '5', '2024-09-22 05:54:27', '5', '2024-09-25 04:27:19', 3, '5', 0.00),
('2', 'Project Beta', '2023-02-15', '2023-03-20', 'Win', 'CN002', 'Remark for project Beta', '2023-02-20', '2', 145000.00, 5000.00, 125000.00, 20000.00, 20000.00, 85.30, 140000.00, 130000.00, 10000.00, '2', '2024-09-22 05:54:27', '2', '2024-09-25 04:26:33', 1, '2', 0.00),
('3', 'Project Gamma', '2023-03-05', '2023-04-10', 'On Hold', 'CN003', 'Remark for project Gamma', '2023-03-10', '3', 190000.00, 10000.00, 160000.00, 30000.00, 30000.00, 80.00, 185000.00, 175000.00, 10000.00, '3', '2024-09-22 05:54:27', '3', '2024-09-25 04:26:36', 2, '3', 0.00),
('3f9e4fdf-3bf8-475e-be2e-6fdab2e452eb', 'โครงการระบบแพลตฟอร์มวิเคราะห์ข้อมูลและปัญญาประดิษฐ์ในบริการการดูแลการใช้ชีวิตและดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุ', '2024-09-15', '2024-09-12', 'Win', 'C16F640358', 'โครงการระบบแพลตฟอร์มวิเคราะห์ข้อมูลและปัญญาประดิษฐ์ในบริการการดูแลการใช้ชีวิตและดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุ', '2024-09-24', '3', 30000000.00, 32100000.00, 560747.66, 600000.00, 29439252.34, 98.13, 30000000.00, 560747.66, 29439252.34, '11', '2024-09-25 07:41:48', '2', '2024-09-25 16:59:46', 3, '2', 7.00),
('4', 'Project Delta', '2023-04-01', '2023-05-01', 'Cancelled', 'CN004', 'Remark for project Delta', '2023-04-05', '4', 240000.00, 10000.00, 210000.00, 30000.00, 30000.00, 79.00, 235000.00, 225000.00, 10000.00, '4', '2024-09-22 05:54:27', '4', '2024-09-25 04:26:39', 3, '4', 0.00),
('5', 'Project Epsilon', '2023-05-15', '2023-06-15', 'Quotation', 'CN005', 'Remark for project Epsilon', '2023-05-20', '5', 285000.00, 15000.00, 240000.00, 45000.00, 45000.00, 82.00, 290000.00, 270000.00, 20000.00, '5', '2024-09-22 05:54:27', '5', '2024-09-25 04:26:42', 2, '6', 0.00),
('6', 'Project Zeta', '2023-06-10', '2023-07-10', 'Quotation', 'CN006', 'Remark for project Zeta', '2023-06-15', '1', 330000.00, 20000.00, 290000.00, 40000.00, 40000.00, 81.00, 340000.00, 320000.00, 20000.00, '1', '2024-09-22 05:54:27', '1', '2024-09-25 04:26:45', 3, '3', 0.00),
('7', 'Project Eta', '2023-07-01', '2023-08-01', 'On Hold', 'CN007', 'Remark for project Eta', '2023-07-05', '2', 380000.00, 20000.00, 320000.00, 60000.00, 60000.00, 80.50, 390000.00, 370000.00, 20000.00, '2', '2024-09-22 05:54:27', '2', '2024-09-25 04:26:48', 1, '7', 0.00),
('8', 'Project Theta', '2023-08-05', '2023-09-05', 'Cancelled', 'CN008', 'Remark for project Theta', '2023-08-10', '3', 430000.00, 20000.00, 370000.00, 60000.00, 60000.00, 82.00, 440000.00, 420000.00, 20000.00, '3', '2024-09-22 05:54:27', '3', '2024-09-25 04:26:54', 4, '1', 0.00),
('9', 'Project Iota', '2023-09-01', '2023-10-01', 'Bidding', 'CN009', 'Remark for project Iota', '2023-09-05', '4', 480000.00, 20000.00, 410000.00, 70000.00, 70000.00, 81.50, 490000.00, 460000.00, 30000.00, '4', '2024-09-22 05:54:27', '4', '2024-09-25 04:26:58', 1, '5', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` char(36) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `team_description` text DEFAULT NULL,
  `created_by` char(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `team_leader` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_id`, `team_name`, `team_description`, `created_by`, `created_at`, `updated_by`, `updated_at`, `team_leader`) VALUES
('0586dbce-172c-4410-8fe2-cf120b46ad6b', 'หน่วยซีล', 'ปติบัติการลับ', '', '2024-09-28 08:25:53', '3', '2024-09-30 14:47:44', '66f3b9c48eff57.82225589'),
('1', 'Innovation', 'ทีม Product ', '', '2024-09-26 03:35:50', NULL, '2024-09-26 17:00:17', '1'),
('109e03dc-7832-4cca-a061-16cf7615d3d7', 'sdfsdf', 'sdfdsfdsfds', '', '2024-09-28 15:40:31', NULL, NULL, '49279c96-4158-48c7-a1c2-a996e867ad34'),
('19a7bb0f-e6d2-4756-9229-eef1cd99a2d7', 'Non Service', 'Non Service', '', '2024-09-27 14:59:49', NULL, '2024-09-29 15:36:42', '2'),
('2', 'Sales A', 'ทีมฝ่ายขายของบริษัท', '', '2024-09-26 03:35:50', NULL, '2024-09-26 17:00:31', '3'),
('3', 'Service', 'ทีมให้บริการ', '', '2024-09-26 03:35:50', NULL, '2024-09-26 17:00:41', '2'),
('3047cbc0-50be-4398-aacd-bc2c399abaf5', 'sdfdsfds', 'fdsfdsfdsf', '', '2024-09-28 15:40:39', NULL, NULL, '4'),
('4', 'Point IT', 'บริษัทพอทไอที คอนซัลทิ่งจำกัด', '', '2024-09-26 03:35:50', NULL, '2024-09-26 17:01:02', '2'),
('57f01f39-e202-457a-ab86-28e3686e8404', 'fsdfsd', 'ฤฤฤฤฤฤฤฤฤฤฤฤฤฤฤฤฤฤฤ', '', '2024-09-28 15:40:20', '3', '2024-09-29 16:48:56', '49279c96-4158-48c7-a1c2-a996e867ad34'),
('744d485a-3e6d-49c4-9ef2-85dc205a325c', 'hhhhhh', 'hhhhhhhhh', '3', '2024-09-29 16:03:18', '3', '2024-09-29 16:48:40', '5'),
('9b8d39c3-f4b2-41f6-8dc4-204b597793a6', 'sdfdsfsdf', 'sdfdsfsdfdsfds', '', '2024-09-28 15:41:00', NULL, NULL, '3'),
('9eb239ca-4d37-44d3-93c6-470876954830', 'หน่วยซีลด', 'ปติบัติการลับด', '3', '2024-09-29 16:02:58', NULL, NULL, '12'),
('a835ad81-565c-4938-bfb8-1f84b1c495ae', 'ฟหกดฟหกดฟกหด', 'ฟหกดฟกหดฟหด', '3', '2024-09-28 17:08:22', NULL, NULL, '12'),
('d51c6aaf-86ab-4f0f-9644-f94cb593aeb7', 'sfsdfsdf', 'dsfdsfsdfsdfsdfdssdff', '', '2024-09-28 15:40:49', NULL, NULL, '5'),
('f45f81a4-7086-448b-b6a7-2e1862fb1eda', 'Pataya', 'ทีมฝ่ายขายของพัทยา', '', '2024-09-26 16:57:10', NULL, '2024-09-26 17:01:13', '2');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` char(36) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('Executive','Sale Supervisor','Seller','Engineer') NOT NULL,
  `team_id` char(36) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `email`, `role`, `team_id`, `position`, `phone`, `password`, `company`, `created_at`, `created_by`, `profile_image`) VALUES
('0a9fec94-57d3-4f65-aeaa-052cba2c3b62', 'มานะ', 'ใจดี', 'Test2', 'apirak55ba@xn--gmail-z6qaa3obb4ydc.com', 'Seller', '1', 'IT Service', '0839595478', '$2y$10$M3GyW3VvHB2tvQBTtyvIzel2gnbZx38UkXzqbqZ/wcivYhgDhGUDK', 'Point IT Consulting Co. Ltd', '2024-10-11 16:18:12', 3, '67095b35a51ad.jpg'),
('1', 'Supachai', 'Bangpuk', 'Sale', 'ApirakSS@gmail.com', 'Seller', '2', 'IT', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:43:58', NULL, NULL),
('12', 'Rungnapa', 'Positakub', 'ying', 'Ying@gmail.com', 'Sale Supervisor', '3', 'Product Sale', '0839595888', '$2y$10$c7lOPwTFlqF/qsiFR/K1DuNPzfXae.PPsJ5O4NH2bIazwc8mWYsNq', 'PIT', '2024-09-17 15:26:14', 2, NULL),
('1537eabd-0014-4f80-9116-9800cc8df26f', 'dfgdfgdf', 'dfgdfgdg', 'DDDDD', '', 'Seller', NULL, 'IT support', '0839595847', '$2y$10$uMB5yU2ZT6lFnujxBBdZJOIFoW0pqWDCnECmjP3gPy6yfZRxYWECG', 'Point IT Consulting Co. Ltd', '2024-10-10 16:15:03', 3, '6707fd87d695d.jpg'),
('2', 'Apirak2', 'Bangpuk', 'Admin', '', 'Executive', '1', 'IT', '0839595800', '$2y$10$jcmTr.I9CthXOrWFC78XjuOjwPoZlbvF80M4RKow4RvnNbm1Ej8dO', 'PIT', '2024-09-15 16:43:58', NULL, '6709533aa50e3.jpg'),
('2ae57cd7-cbe0-4608-a4e1-1b7c78d0163a', 'Apirak2', 'Bangpuk', 'Test', '', 'Seller', '1', 'IT', '0839595800', '$2y$10$vs1ljpTrkvgspkkTq3hm9.x9aibhb8cth4QXR36bLcU4Ant0IXKCK', 'PIT', '2024-09-25 07:57:03', 2, '6709533aa50e3.jpg'),
('3', 'Apirak3', 'Bangpuk', 'Supervisor', 'apirak.ba@gmail.com', 'Sale Supervisor', '1', 'IT support', NULL, '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:43:58', NULL, NULL),
('4', 'Apirakt5', 'Bangpuk', 'Support', 'apirakAA@gmail.com', 'Engineer', '3', 'IT Service', '0839595811', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:55:43', 2, NULL),
('49279c96-4158-48c7-a1c2-a996e867ad34', 'Apirak4', 'Bangpukx', 'czxc', 'a@gmail.com', 'Seller', '1', 'Product Sale', '0839595866', '$2y$10$rfHgTr0dFtZf/XIrULIa4.V2eh5L.LF20kKCmkB/4JHopzgZMzZUa', 'Point IT', '2024-09-25 09:29:51', 2, '6709656865d4b.jpg'),
('5', 'Panit', 'Poapun', 'Panit', 'Panit@gmail.com', 'Executive', '4', 'Executive Director', '0839595822', '$2y$10$eAar02e4iaTG6bhKs2XLfua7ck.2co.8dkla8VX0tVCC5cnQfc/E6', 'PIT', '2024-09-17 15:15:37', 2, NULL),
('66f3b9c48eff57.82225589', 'Kritpat', 'pumsorn', 'Kritpat', 'Kritpat@gmail.com', 'Engineer', '3', 'IT support', '0839595836', '$2y$10$ZOlZX8UoJJhVdmbPjLktA.nZuv7OnhzhdrKUkJNsqtGyhKIxf5x22', 'PIT', '2024-09-25 07:20:36', 2, NULL),
('66f3c074d3a618.04895052', 'Somchai', 'Bangpuk', 'Somchai', 'Somchai@gmail.com', 'Sale Supervisor', '2', 'Product Sale', '0839595889', '$2y$10$gia/NWu/Y/cwnHXRiv4dfuMihRA/tYsxebp5rzGAylI0SypRf1br6', 'Point IT', '2024-09-25 07:49:08', 2, NULL),
('6d9e3a20-df2a-462f-af6a-d9054f221e79', 'Apirak', 'Bangpuk', 'Operation', 'apiraaa@gmail.com', 'Engineer', NULL, 'IT support', '0839595600', '$2y$10$n1XCOBSH0ppnAk9K5mUtluZK/NpaDGs0ST3reS4DbELTk8dcTr/UK', 'Point IT Consulting Co. Ltd', '2024-10-10 16:11:38', 3, '6707fcba93c0e.jpg'),
('a0c895d7-3730-4b11-827d-8a7423d81761', 'ทดสอบ', 'ทดสอบ', 'root', 'apirak55ba@xn--gmail-z6qaa3obb4ydc.com', 'Seller', '1', 'IT Service', '0839595หกดกหดกหด', '$2y$10$Kd5hZtUnmAIFCmfogd1dm.Kpn8mLxWwczet/8FhSAFhtiicbUkvsS', 'Point IT Consulting Co. Ltd', '2024-10-11 14:34:01', 2, '670956f98b085.jpg'),
('d7d6b20c-079d-4ca8-8bd9-89ea2a1f531e', 'มานี', 'ศรีคงแก้ว', 'jit', 'apirak55ba@gmail.com', 'Seller', '1', 'IT Service', '0839595877', '$2y$10$xg2y9QqXma4uTlRYpgVtI.khjGNHTYbd8/no359BgCRBH9cKoUdC.', 'Point IT Consulting Co. Ltd', '2024-09-25 08:00:50', 2, '67096be028471.jpg');

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
