-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 02:34 PM
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
  `position` varchar(255) DEFAULT NULL COMMENT 'ตำแหน่ง',
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

INSERT INTO `customers` (`customer_id`, `customer_name`, `company`, `position`, `address`, `phone`, `email`, `remark`, `created_by`, `created_at`, `updated_by`, `customers_image`, `office_phone`, `extension`, `updated_at`) VALUES
('02e18007-e4e7-4fb7-a2c2-c924ece0a966', 'คุณทอม', 'บริษัท อัลทิเมทเทคแอนด์อินโนเวชั่น จำกัด (สำนักงานใหญ่)', 'Management Director', 'เลขที่ 51 อาคารเมเจอร์ทาวเวอร์ พระราม 9 - รามคำแหง ห้องเลขที่ 2006.2, 2007 ชั้นที่ 20 \r\nถนนพระราม 9 แขวงหัวหมาก เขตบางกะปิ กรุงเทพมหานคร 10240', '0220277836', '', '', '3', '2025-04-09 06:03:36', '', NULL, '', NULL, '2025-04-09 06:03:36'),
('054160f3-f50e-40a2-a45e-569777875172', 'Somboon  Sangcharoen', 'GFCA  Co., Ltd.', 'Manager - Infrastructure', '1526-1540 Soi Pattanakan 48, Pattanakan Road, Suanluang, Bangkok 10250, Thailand', '0845519109', 'somboon@gfca.com', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-13 05:01:13', '', '', '020730977', '202', '2025-01-13 05:01:13'),
('065f2ab4-63ac-4758-8eb1-380df80c8f83', 'คุณก้อง  เทพสิทธิ์', 'สหกรณ์ออมทรัพย์', '', '', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-16 02:07:49', '', '', '', '', '2024-12-16 02:07:49'),
('0968cd06-9d79-4933-8de8-399cb9ac5868', 'คุณโอฬาร', 'Zoom Information System Company Limited', 'Manager Director', '223/16 หมู่บ้าน เซนสิริทาวน์ หมู่ที่ 1ซอย พรประภานิมิตร 17 ถนนแยกมิตรกมล ต.หนองปรือ อ.บางละมุง จ.ชลบุรี', '', 'Oran.gun@gmail.com', 'ลูกค้าภายใน', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 08:47:01', '', NULL, '', NULL, '2025-01-16 08:47:01'),
('0a462754-178e-4f0c-a510-d9dd40db6490', 'คุณพลอย', 'Master Maker Co., Ltd.', NULL, '274/3 ซ.รุ่งเรือง ถ.สุทธิสารวินิจฉัย แขวงสามเสนนอก เขตห้วยขวาง กรุงเทพฯ 10310', '', '', '', '3', '2024-12-02 13:55:53', '', '', '02-276-4388', '', '2024-12-02 13:55:53'),
('0f80acd4-d034-4175-b501-f879a9e203de', 'ดิเรก วงศ์งาม', 'ธนาคารไทยพาณิชย์ จำกัด(มหาชน)', '', '', '', '', '', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-30 06:57:48', '', NULL, '', NULL, '2025-01-30 06:57:48'),
('1fb0fb81-4482-438a-ab66-5472c52bf9e4', 'องค์การบริหารส่วนจังหวัดชลบุรี', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', NULL, '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:38:21', '', '', '', '', '2024-11-04 03:38:21'),
('213830aa-08d9-4673-9081-3fcba6ce1625', 'คุณชาคริยา  นาคมณี', 'บริษัท เอเชี่ยน เอ็กซ์ฟิดิชั่น จำกัด', NULL, '9/1 อาคารมูลนิธิสนธิอิสลาม ชั้น 4 ห้อง 402 ถนนอรุณอมรินทร์  แขวงอรุณอมรินทร์  เขตบางกอกน้อย กรุงเทพมหานคร', '0903177256', '', '', '3', '2024-12-02 13:33:10', '', '', '', '', '2024-12-02 13:33:10'),
('2621ade4-bbfa-474f-a74d-fcb04d70f2eb', 'คุณตรีเทศ หะหวัง', 'บริษัท โทรคมนาคมแห่งชาติ จำกัด', NULL, '99 ถนนแจ้งวัฒนะ แขวงทุ่งสองห้อง เขตหลักสี่ กรุงเทพมหานคร 10210', '089-482-2387', 'treeted@nt.ntplc.co.th', '', '3', '2024-10-15 21:52:58', '3', '671244b41c0cb.jpg', '', '', '2024-10-18 04:21:24'),
('2b5c101f-db79-4143-89f9-2b42fbea06bd', 'Danai Sinsakjaroongdech', 'SmartBiz Solutions Co.,Ltd.', 'Sales Manager', '', '0954159936', 'danai@smartbiz.co.th', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-28 06:10:06', '', '', '0954159936', '', '2025-01-28 06:10:06'),
('2d4610f7-471d-42c1-a193-d79ac4eb24e8', 'ปลัดเรวีญา ขจิตเนติธรรม', 'เทศบาลตำบลทับมา', NULL, 'เลขที่ 20/3 หมู่ ที่ 4 อำเภอ เมือง, ตำบลทับมา อำเภอเมืองระยอง ระยอง 21000', '0928957111', '', '', '3', '2024-12-02 14:57:17', '', '', '', '', '2024-12-02 14:57:17'),
('31ed3e1f-0435-4ede-a66a-ae985d0a751e', 'คุณเตย', 'บริษัท โซยี (ไทยแลนด์) จำกัด', NULL, 'เลขที่ 222/8 หมู่ 4 ตำบลบางแก้ว อำเภอบางพลี จังหวัดสมุทรปราการ 10540', '0971188010', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-06 10:07:19', '', '', '021153838', '', '2024-12-06 10:07:19'),
('32104ee7-4b28-400b-bb7b-1ab55e1cf19d', 'นายสิรวิชฐ์ อำไพวงษ์ (ท่านนายก)', 'องค์การบริหารส่วนตำบลบ่อวิน', NULL, 'องค์การบริหารส่วนตำบลบ่อวิน เลขที่ 1 หมู่ที่ 6 ตำบลบ่อวิน อำเภอศรีราชา จังหวัดชลบุรี 20230 โทรศัพท์ 0-3834-5949 ,0-3834-5918 โทรสาร 0-3834-6116 สายด่วนร้องทุกข์ 24 ชม. 08-1949-7771 นายกเทศบาลตาบลบ่อวิน องค์การบริหารส่วนตำบลบ่อวิน', '038345949', 'admin@bowin.go.th', '', '3', '2024-10-11 23:26:14', '', NULL, NULL, NULL, '2024-10-17 19:18:35'),
('34ea3368-fa1c-445a-aeb8-821c87086d3a', 'นงนุช โกวิทวณิช', 'BUSINESS SOLUTIONS PROVIDER CO.,LTD.', NULL, '7/129 18th Floor., Baromrajchonnee Rd.,Arunammarin, Bangkok-Noi, Bangkok. 10700', '0619522110', 'nongnuch@bspc.co.th', '', '3', '2024-10-17 04:45:13', '2', NULL, NULL, NULL, '2024-10-17 19:18:35'),
('350429f1-d84a-4cec-8c28-d1a2ce9c4763', 'Mr. CHAWAPAT PRASERTTONGSUK', 'WT Partnership (Thailand) Limited', 'Senior MEP Quantity Surveyor', 'U1802, L18, S Metro Building, 725 Sukhumvit Rd, Klongton Nua, Wattana, Bangkok 10110', '0855750465', 'chawapatp@wtpthailand.com', '', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-17 03:25:34', '', NULL, '', NULL, '2025-03-17 03:25:34'),
('360a7a11-6bcd-4301-8156-b4d11ebd6794', 'ผอ.เค', 'สำนักงานศาลรัฐธรรมนูญ', '', '', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-16 01:51:59', '', '', '', '', '2024-12-16 01:51:59'),
('3ef73d28-72ff-4c90-b04a-693a33baf895', 'นางสาวปรียาภรณ์ บริสุทธิพันธ์', 'ธนาคารออมสิน', 'รองผู้อำนวยการฝ่ายการพัสดุ ส่วยบริหารพัสดุ', '470 ถนนพหลโยธิน แขวงสามเสนใน เขตพญาไท กรุงเทพฯ 10400', '', '', '', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 04:45:50', '', '677b5ffeaedd3.png', '022998000', '030127', '2025-01-06 04:45:50'),
('45af1f14-b041-43b2-b4ff-d93692564a61', 'Pilanthana Wisawamitr', 'TOYO TIRE (THAILAND) CO., LTD.', 'OE Sales Division', '2/8 Sukhaphiban 2 Rd. Khwang Prawet, Khet Prawet, Bangkok 10250', '0922495893', 'pilanthana@toyotires.co.th', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 04:35:11', '', '', '02-329 2012', '203', '2025-01-06 04:35:11'),
('48cf0983-375c-46de-ab41-72350901a376', 'คุณอลิสา ธนสารเสถียร', 'บริษัท ไอไอเอส ออโตเมชั่น จำกัด', NULL, '36 ซอยสุขาภิบาล 5 ซอย 5  แยก 13  แขวงท่าแร้ง เขตบางเขน  กรุงเทพฯ 10220', '0905424694', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-06 10:05:07', '', '', '', '', '2024-12-06 10:05:07'),
('4f049ce6-9488-4664-865f-5d9729659ee2', 'คุณมิกซ์', 'MPLUS INTERNATIONAL CO.,LTD.', '', '1 Empire Tower, 47th Floor., Unit 4703 (river 25), South Sathorn Road, Yannawa, Sathorn, Bangkok,10120, Thailand', '', '', '', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 09:10:26', '', NULL, '', NULL, '2025-01-16 09:10:26'),
('594abb40-0296-4aa0-a1fd-82f479359ed5', 'คุณเก๋', 'บริษัท เฟิรส์วัน ซิสเต็มส์ จำกัด', NULL, 'เลขที่ 719 อาคารเคพีเอ็นทาวเวอร์ ชั้น 11 ถนนพระราม 9 แขวงบางกะปิ เขตห้วยขวาง กรุงเทพมหานคร 10310', '0629169244', 'Sumitta@firstone.co.th', '', '3', '2024-12-02 13:47:25', '', '', '', '', '2024-12-02 13:47:25'),
('5aa126c7-c78d-4234-b0f3-45153034626e', 'คุณบอล', 'เทศบาลเมืองมาบตาพุด', NULL, 'เลขที่ 9 ถนนเมืองใหม่มาบตาพุดสาย 7 ตำบลห้วยโป่ง อำเภอเมืองระยอง จังหวัดระยอง 21150', '0892790210', '', '', '3', '2024-12-02 13:51:04', '', '', '', '', '2024-12-02 13:51:04'),
('5e2a838a-110f-48bc-9518-f01a7066955b', 'นายอิทธิกร เล่นวารี  (นายกเทศมนตรีตำบลปากท่อ)', 'สำนักงานเทศบาลตำบลปากท่อ จ. ราชบุรี', NULL, '39 หมู่ที่ 7 ต.ปากท่อ อ.ปากท่อ จ. ราชบุรี 70140 โทรศัพท์ 032-281-266 โทรสาร 032-282-564', '0806508585', 'pakthocity@hotmail.com', 'http://www.pakthomunic.go.th/office.php', '2', '2024-10-12 06:24:53', '3', '6715036cbd552.jpg', '', '', '2024-10-20 13:19:40'),
('642afc1e-c8d5-42f3-a685-aa899e78be1e', 'Apinya Luanthaisong', 'Master Maker Co.,Ltd.', 'Operation and Business Control Manager', '274/3 Soi Rungruang, Suthisarnvinichai Road, Samsennok, Huaykwang, Bangkok 10310', '0863232642', 'apinya@mastermakerth.com', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-03-13 09:41:23', '', NULL, '022764388', NULL, '2025-03-13 09:41:23'),
('65b9a9b5-5272-4b9d-a02f-1b4c85460069', 'คุณณาศิส', 'บริษัท มายด์ซอฟท์ คอร์ปอเรชั่น จำกัด', NULL, 'เลขที่ 363/38 ซอยพหลโยธิน 26 ถนนพหลโยธิน แขวงจอมพล เขตจตุจักร กรุงเทพ 10900', '0991095665', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-03 04:23:35', '', '', '', '', '2024-12-03 04:23:35'),
('677f5f38-3f7f-4ca8-b9d6-e4b60f7f241a', 'นายสรวิชญ์ เพชรนคร', 'เทศบาลนครระยอง', 'รองปลัดเทศบาลนครระยอง', '888 ถ. ตากสินมหาราช ตำบล ท่าประดู่ อำเภอเมืองระยอง ระยอง 21000', '0996535194', '', '', '3', '2025-04-09 07:05:27', '3', NULL, '', '', '2025-04-09 07:11:28'),
('690cfd6a-0270-4b22-8d1f-de1f91dda830', 'K.Noppadol', 'AT Technology Anywhere Co., Ltd.', NULL, '216/22, City Link Rama 9-Srinakarin, Kanchanaphisek, Thap Chang, Saphan Sung, Bangkok, Thailand', '0863749945', 'noppadol@atanywhere.co.th', '', '3', '2024-12-02 13:43:19', '', '', '', '', '2024-12-02 13:43:19'),
('69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 'Onpailin Poomsiriroj', 'Master Maker Co.,Ltd.', NULL, '274/3 Soi Rungruang, Suthisarnvinichai Road, Samsennok, Huaykwang, Bangkok 10310', '0863696540', 'onpailin@mastermakerth.com', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:51:20', '', '', '', '', '2024-10-31 21:51:20'),
('6b3ba15b-ee6d-41ab-a543-d345e9f62259', 'Auto X', 'Auto X', NULL, '', '', '', '', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:45:29', '', '', '', '', '2024-11-11 08:45:29'),
('6d128135-3e95-4226-9956-21bb63f25cc0', 'คุณบอล', 'บริษัท มายด์ โซลูชั่น แอนด์ เซอร์วิส จำกัด', NULL, 'เลขที่ 363/38 ซอยพหลโยธิน 26 ถนนพหลโยธิน แขวงจอมพล เขตจตุจักร กรุงเทพ 10900', '', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-06 10:08:54', '', '', '025124318', '201', '2024-12-06 10:08:54'),
('6e23608d-46bb-4e74-8326-21365397565b', 'คุณต้น', 'MPLUS INTERNATIONAL CO.,LTD.', 'เลขาคุณธง', '1 Empire Tower, 47th Floor., Unit 4703 (river 25), South Sathorn Road, Yannawa, Sathorn, Bangkok,10120', '0875087327', '', '', '3', '2025-04-09 06:38:37', '', NULL, '', NULL, '2025-04-09 06:38:37'),
('778e27be-efad-41b8-a243-d40cf58bba85', 'คุณแม็ค', 'สำนักจราจรและขนส่ง กรุงเทพมหานคร', NULL, '', '', '', '', 'a5741799-938b-4d0a-a3dc-4ca1aa164708', '2024-12-10 03:47:32', '', '6757b9d40a653.png', '', '', '2024-12-10 03:47:32'),
('7f242c52-9e30-4791-97b2-053fb960423b', 'คุณประกอบ จ้องจรัสแสง', 'คุณประกอบ จ้องจรัสแสง', NULL, '215 ซอยพัฒนาการ 50 แขวงสวนหลวง เขตสวนหลวง กรุงเทพฯ 10250', '0816236990', 'prakorb@pointit.co.th', '', '5', '2024-12-02 07:06:59', '5', '', '', '', '2024-12-06 10:02:14'),
('81b62776-9408-4a36-af8e-45799f86883d', 'โรงเรียนสาธิตรามคำแหง(ฝ่ายมัธยม)', 'โรงเรียนสาธิตรามคำแหง(ฝ่ายมัธยม)', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-23 13:10:13', '', '', '', '', '2024-11-23 13:10:13'),
('88bc1a3c-f646-4e7a-863d-3424b0fbe1c1', 'Toyo Tires Thailand Co.,Ltd.', NULL, NULL, NULL, NULL, NULL, NULL, '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 06:23:08', '', NULL, NULL, NULL, '2025-01-06 06:23:08'),
('88d465c6-3e16-4c58-a6da-10bce309af89', 'สตช.', 'สตช.', '', '', '', '', '', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-11 03:56:20', '', '', '', '', '2025-03-11 03:56:20'),
('895e71fc-991e-4b42-9803-4bcafdb03023', 'Suchart Buddhaunchalee', 'IOTtechgroup', 'Director', '', '0952822656', 'suchartb@iottechgroup.com', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-31 06:21:25', '', '', '', '', '2025-01-31 06:21:25'),
('8b315bda-7e61-4d0d-a995-3653ddda3140', 'สำนักงานตำรวจ สน.ปทุมวัน', 'สำนักงานตำรวจ สน.ปทุมวัน', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-03 01:13:36', '', '', '', '', '2024-12-03 01:13:36'),
('8e364a05-4022-454d-b4fb-515393936175', 'คุณจักรกฤษ', 'สถาบันเทคโนโลยีป้องกันประเทศ', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-23 13:08:02', '', '', '', '', '2024-11-23 13:08:02'),
('92463365-36a7-4898-a759-c4ef2a90cedd', 'สำนักงานตำรวจแห่งชาติ', 'สำนักงานตำรวจแห่งชาติ', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-03 01:07:13', '', '', '', '', '2024-12-03 01:07:13'),
('9392ce88-098b-49a8-8df4-c4882971735e', 'คุณสุกัลยา ภิรมย์รัตน์', 'ธนาคารกรุงไทย จำกัด (มหาชน)', 'ผู้อำนวยการฝ่าย ผู้บริหารทีมย่อย ทีม Channel Management', 'เลขที่ 10 อาคารสุขุมวิท ชั้น 20 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110', '0917718387', 'sukanlaya.piromrath@krungthai.com', '', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 07:18:12', '', '677b83b484539.png', '022088340', '', '2025-01-06 07:18:12'),
('9a8307fa-375b-47c3-b09d-2f7ca12f0c02', 'คุณจุติฝัน คิดฉลาด', 'บริษัท เอ อาร์ ที ไบโอเทค จำกัด', NULL, '162/7 ซอยประเสริฐมนูกิจ 29 ถนนประเสริฐมนูกิจ แขวงจรเข้บัว เขตลาดพร้าว กรุงเทพมหานคร 10230', '0834965777', '', '', '3', '2024-12-02 14:49:20', '', '', '', '', '2024-12-02 14:49:20'),
('9f005cab-6ce1-4813-bafe-95be81d93b1d', 'Ying Bacom', 'บริษัท เบคอม อินเตอร์เน็ทเวอร์ค จำกัด', 'Project sale', '48/1ซอยพระรามเก้า 57, 3 ซอย วิเศษสุข เขตสวนหลวง กรุงเทพมหานคร 10250', '0994151562', 'Anyapat@bacominternetwork.com', '', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-17 03:10:24', '', NULL, '', NULL, '2025-03-17 03:10:24'),
('a485226f-e787-44e7-a140-4bf50433c525', 'พี่จา', 'BUSINESS SOLUTIONS PROVIDER CO.,LTD.', NULL, '7/129 ชั้น 18 อาคาร สำนักงาน TowerA เซ็นทรัล ปิ่น เกล้า แขวงอรุณอมรินทร์ เขตบางกอกน้อย กรุงเทพมหานคร 10700', '0863104221', 'jaruwan@bspc.co.th', '', '3', '2024-12-02 13:29:16', '', '', '', '', '2024-12-02 13:29:16'),
('a686607a-56b8-4d1b-ab90-dc2c99ebd878', 'คุณศุภชัย', 'ธนาคารกรุงไทย จำกัด (มหาชน)', '', '', '', '', '', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-16 01:54:54', '', '', '', '', '2024-12-16 01:54:54'),
('a7398772-5d5f-4f09-9eb6-6edf32fb9893', 'คุณเจษฎา', 'สำนักรักษาความปลอดภัย สำนักงานเลขาธิการสภาผู้แทนราษฎร', 'หัวหน้าตำรวจรัฐสภา', '๑๑๑๑ ถนนสามเสน แขวงถนนนครไชยศรี เขตดุสิต กรุงเทพมหานคร ๑๐๓๐๐', '', 'jetsada321@gmail.com', '', '3', '2025-04-09 05:25:25', '', NULL, '', NULL, '2025-04-09 05:25:25'),
('ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', 'เทศบาลเมืองป่าตอง', 'เทศบาลเมืองป่าตอง', NULL, '', '', '', '', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-11-27 08:10:30', '', '', '', '', '2024-11-27 08:10:30'),
('ae83116d-3c1a-41f7-a066-3e99373b2b44', 'คุณอำพล', 'บริษัท เอที เทคโนโลยี เอนนี่แวร์ จำกัด', '', '216/22 หมู่บ้านซิตี้ลิงก์ พระราม 9-ศรีนครินทร์ ถ.กาญจนาภิเษก แขวงทับช้าง เขตสะพานสูง กรุงเทพมหานคร 10250', '0657529666', '', '', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 07:30:50', '', NULL, '', NULL, '2025-01-16 07:30:50'),
('affeb10c-dc64-41d9-952e-d6f01c2d05d1', 'Warayutt Suttivas', 'MIND SOLUTION AND SERVICES CO., LTD.', 'Technical Section Director', '', '0863694306', 'warayutt@mindss.co.th', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 09:17:07', '', '', '025124318', '', '2025-01-06 09:17:07'),
('b26996d4-08c7-4365-96fe-ea74a40aced8', 'องค์การบริหารส่วนตำบลพลูตาหลวง', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', NULL, '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-06 04:37:27', '', '', '', '', '2024-11-06 04:37:27'),
('b2907f8d-53f0-4f71-bc36-11e24a52c10d', 'ธนาคารกรุงไทย', 'ธนาคารกรุงไทย', NULL, '', '', '', '', '8c1c0a55-2610-4081-8d12-b2a6971ffbe8', '2024-12-09 07:58:18', '', '6756a31a6d8d7.png', '', '', '2024-12-09 07:58:18'),
('c2968a16-8dea-4f07-ab94-c7d2197562fa', 'สำนักงานตำรวจแห่งชาติ', 'กองบังคับการตำรวจสันติบาล', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-03 01:15:27', '', '', '', '', '2024-12-03 01:15:27'),
('c918919d-7d14-4f42-97a8-3357016c382a', 'Jaruwan Chanawong', 'Business Solutions Provider Co.,Ltd.', 'Account Manager', '', '0891133003', 'jaruwan@bspc.co.th', '', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-15 07:22:43', '', '', '028849185', '101', '2025-01-15 07:22:43'),
('c9286dcf-c779-4fd0-8101-ca004bfc51ad', 'คุณเสกสรร (ไก่)', 'Synergic Technology', 'Account Assistant Manager', '', '0818875936', '', 'Smart Meeting Room & Access Control กบข.', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-02-17 03:21:23', '', NULL, '', NULL, '2025-02-17 03:21:23'),
('cb8e3303-3fd7-438c-9c64-07e6c80e012f', 'ธนาคารออมสิน', NULL, NULL, NULL, NULL, NULL, NULL, '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 06:27:17', '', NULL, NULL, NULL, '2025-01-06 06:27:17'),
('cbf32bae-0896-4e5b-ab8e-f4fdca7916f8', 'โรงพยาบาลกรุงเทพ', 'โรงพยาบาลกรุงเทพ', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-23 13:08:45', '', '', '', '', '2024-11-23 13:08:45'),
('cc80c251-336b-4039-9850-5a042948e8f3', 'นางสาวเรวีญา ขจิตเนติธรรม (ปลัดเทศบาล)', 'เทศบาลตำบลทับมา', NULL, 'เลข ที่ 20/3 หมู่ ที่ 4 อำเภอ เมือง, ตำบลทับมา อำเภอเมืองระยอง ระยอง 21000', '0928957111', '', '', '3', '2024-12-02 14:55:51', '', '', '', '', '2024-12-02 14:55:51'),
('cc91e26c-61c7-494a-9dc9-109298dfa5ac', 'Danai Sinsakjaroongdech', 'SmartBiz Solutions Co.,Ltd.', 'Sales Manager', '', '0954159936', 'danai@smartbiz.co.th', 'Customer Evergreen', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 06:12:26', '', '', '', '', '2025-01-06 06:12:26'),
('cdd15d78-73d7-41d6-9fad-dfd0da61a1a9', 'คุณธัญลักษณ์', 'Yamaha Motor Parts Manufacturing (Thailand) Co., Ltd.', NULL, '700/18 Moo 6, Soi 8 Amata Nakorn, Bangna-Trad Highway Km.57 Tambol Nongmaidang, Amphur Muang, Chonburi 20000 Thailand', '0902491469', 'thanyalak@yamaha-motor-parts.co.th', '', '3', '2024-12-02 13:39:12', '', '', '', '', '2024-12-02 13:39:12'),
('cea804cd-55ab-4a3f-b9ff-a942547402a7', 'Siripong Siriprasert', 'Supreme Distribution Public Company Limited', NULL, '2/1 Soi Praditmanutham 5, Praditmanutham Road, Tha Raeng, Bang Khen, Bangkok 10230', '0651962456', 'siripong.s@supreme.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 20:59:42', '', '', '', '', '2024-10-31 20:59:42'),
('cf5b1437-ce07-4f44-a672-ecd9cee08e41', 'คุณอเล็กซ์', 'บริษัท ธนบุรีพานิช จํากัด', 'CRM Management', 'เลขที่ 84/1 อาคารวิริยะพันธุ์ ชั้นที่ 4 ถนนจรัญสนิทวงศ์ แขวงบางพลัด เขตบางพลัด กรุงเทพมหานคร', '0909424154', '', '', '3', '2025-04-09 06:16:03', '', NULL, '', NULL, '2025-04-09 06:16:03'),
('d4efc031-32d4-487f-87ff-69afe9f948e4', 'องค์การบริหารส่วนตำบลบ่อวิน', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', NULL, '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-06 04:58:23', '', '', '', '', '2024-11-06 04:58:23'),
('da8ca97f-0a95-49cd-99a4-a6f698cbe98c', 'ศาลรัฐธรรมนูญ', 'ศาลรัฐธรรมนูญ', NULL, '', '', '', '', '8c1c0a55-2610-4081-8d12-b2a6971ffbe8', '2024-12-09 07:59:46', '', '6756a372b7c5f.png', '', '', '2024-12-09 07:59:46'),
('dd7d359f-6c63-4c11-80c5-d4dfa7407c92', 'Naruemon Rayayoy', 'Master Maker Co.,Ltd.', NULL, '274/3 Soi Rungruang, Suthisarnvinichai Road, Samsennok, Huaykwang, Bangkok 10310', '0629829978', 'naruemon@mastermakerth.com', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 20:55:45', '9223372036854775807', '', '', '', '2024-10-31 21:31:25'),
('df6e7ebd-77f2-49e4-bcdf-04c71608005f', 'นางสาวจันทนา อุดม', 'นางสาวจันทนา อุดม', NULL, '8/31 ม.โชคสำอางค์  ถ.บางแวก แขวงบางไผ่ เขตบางแค กรุงเทพมหานคร 10160', '0969372260', '', '', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-06 10:03:28', '', '', '', '', '2024-12-06 10:03:28'),
('e4ad10b0-1850-4f98-82e9-56f8afe1c0ff', 'โรงพยาบาลพริ้นซ์', 'โรงพยาบาลพริ้นซ์', NULL, '', '', '', '', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-23 13:09:33', '', '', '', '', '2024-11-23 13:09:33'),
('f004cbe4-f666-4de7-8e85-7f940b6d8393', 'Kanitnicha Charoenpattanaphak', 'Business Solutions Provider Co.,Ltd.', NULL, '', '0957965498', 'Kanitnicha@bspc.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 22:30:43', '', '', '', '', '2024-10-31 22:30:43'),
('f313a7ba-64ae-4d61-af99-f493a98039b2', 'Adiphol Sermphol', 'Supreme Distribution Public Company Limited', NULL, '2/1 Soi Praditmanutham 5, Praditmanutham Road, Tha Raeng, Bang Khen, Bangkok 10230', '0814847928', 'adiphol.s@supreme.co.th', '', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-10-31 21:53:06', '', '', '', '', '2024-10-31 21:53:06'),
('f5489b6a-fd5b-4896-b655-761768e44b8f', 'SCB', 'SCB', NULL, '', '', '', '', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:37:29', '', '', '', '', '2024-11-11 08:37:29'),
('fb683856-9635-4316-ad3a-2eb57d6eb10f', 'คุณโอฬาร สินธุพันธ์', 'ZOOM INFORMATION SYSTEM', 'Management Director', '223/16 หมู่บ้าน เซนสิริทาวน์ หมู่ที่ 1ซอย พรประภานิมิตร 17 ถนนแยกมิตรกมล ตำบลหนองปรือ อำเภอบางละมุง จ.ชลบุรี 20150', '0851511551', 'Oran.gun@gmail.com', '', '3', '2025-04-09 05:57:26', '', NULL, '', NULL, '2025-04-09 05:57:26'),
('fc372e65-cca3-4c7c-b580-c689ef2d0798', 'เทศบาลตำบลบางจะเกร็ง', 'เทศบาลตำบลบางจะเกร็ง', NULL, '', '', '', '', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2024-12-03 02:41:20', '', '', '', '', '2024-12-03 02:41:20'),
('fda15ece-1a00-4583-b354-cb5f3c01bb23', 'ศาลาว่าการเมืองพัทยา', 'บจ.ซูม อินฟอร์เมชั่น ซิสเต็ม', NULL, '', '', '', '', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 02:48:07', '0', '', '', '', '2024-11-04 03:38:49');

-- --------------------------------------------------------

--
-- Table structure for table `document_links`
--

CREATE TABLE `document_links` (
  `id` char(36) NOT NULL,
  `project_id` char(36) NOT NULL,
  `category` varchar(50) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` char(36) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` char(36) NOT NULL COMMENT 'รหัสพนักงาน (UUID)',
  `first_name_th` varchar(255) NOT NULL COMMENT 'ชื่อพนักงาน (ภาษาไทย)',
  `last_name_th` varchar(255) NOT NULL COMMENT 'นามสกุลพนักงาน (ภาษาไทย)',
  `first_name_en` varchar(255) NOT NULL COMMENT 'ชื่อพนักงาน (ภาษาอังกฤษ)',
  `last_name_en` varchar(255) NOT NULL COMMENT 'นามสกุลพนักงาน (ภาษาอังกฤษ)',
  `gender` varchar(10) DEFAULT NULL,
  `birth_date` date DEFAULT NULL COMMENT 'วันเกิดของพนักงาน',
  `personal_email` varchar(255) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL COMMENT 'แผนกที่สังกัด',
  `team_id` char(36) DEFAULT NULL COMMENT 'รหัสทีม',
  `supervisor_id` char(36) DEFAULT NULL COMMENT 'รหัสหัวหน้า',
  `address` text DEFAULT NULL COMMENT 'ที่อยู่',
  `hire_date` date DEFAULT NULL COMMENT 'วันที่เริ่มงาน',
  `profile_image` varchar(255) DEFAULT NULL COMMENT 'URL รูปโปรไฟล์',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้อัปเดตข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตข้อมูลล่าสุด',
  `nickname_th` varchar(50) DEFAULT NULL COMMENT 'ชื่อเล่นภาษาไทย',
  `nickname_en` varchar(50) DEFAULT NULL COMMENT 'ชื่อเล่นภาษาอังกฤษ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name_th`, `last_name_th`, `first_name_en`, `last_name_en`, `gender`, `birth_date`, `personal_email`, `company_email`, `phone`, `position`, `department`, `team_id`, `supervisor_id`, `address`, `hire_date`, `profile_image`, `created_by`, `updated_by`, `created_at`, `updated_at`, `nickname_th`, `nickname_en`) VALUES
('0016f66d-64fd-431e-ad26-45b643f987cc', 'น.ส. ภัทราอร', 'อมรโอภาคุณ', 'Phattraorn', 'Amornophakun', 'female', NULL, 'Phattraorn@gmail.com', 'Phattraorn.a@pointit.co.th', '061-952-2111', 'Sales', 'Sales', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 01:45:14', 'ซีน', 'Zeen'),
('001c3d0b-b49d-43e7-9d6e-5060c74f9089', 'นายอนุรักษ์', 'บุตศรี', 'Mr.Anurak', 'Butsri', NULL, NULL, NULL, NULL, '063-7426650', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอล', NULL),
('0110c4fb-fcf0-4636-ac8b-78e0c7c48850', 'น.ส.รสชนก', 'โยธี', 'Miss Rotchanok', 'Yothee', NULL, NULL, NULL, NULL, '085-935-9244', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตุ๋งติ๋ง', NULL),
('01f91b84-57a6-476b-ac85-bf31ea59ffe6', 'นายอภิชิต', 'ชารีกัน', 'Mr.Apichit', 'Chareekan', NULL, NULL, NULL, NULL, '084-788-6612', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ต๋อง', NULL),
('020c88b6-bb49-43fd-a55b-b04b8a868169', 'นาย กฤษณะ', 'พรหมไหม', 'Kirtsana', 'Phrommai', NULL, NULL, NULL, NULL, '090-909-3073', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('05f8e733-dbd6-4ccf-bd8c-9cb4bf191a33', 'นาย กานต์', 'กาญจนมหกุล', 'Kan', 'Kanjanamahakul', NULL, NULL, NULL, NULL, '086-6335050', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กาน', NULL),
('060ca930-3e08-42a9-b4ca-cd5e47af0d8c', 'นาง ผาณิต', 'เผ่าพันธ์', 'Panit', 'Paophan', 'female', NULL, 'panit@pointit.co.th', 'panitpaophan@gmail.com', '0814834619', 'Executive Director', 'IT Service', '4', '2f6d353b-53f1-4492-8878-bc93c18c5de9', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-05 03:48:10', 'พี่หญิง', 'Ying'),
('0850049e-2fbf-4d51-961f-5f7fd5363416', 'น.ส. นภัสดา', 'ที่รักษ์', 'Naputsada', 'Teerak', NULL, NULL, NULL, NULL, '083-016-8818', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เป้', NULL),
('087aa549-1e7d-43d5-9262-b552a25b0bfe', 'นายอาทิตยพล', 'พิมลเอกอักษร', 'Mr.Artidtayaphol', 'Phimonekaksorn', NULL, NULL, NULL, NULL, '087-686-9443', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บิ๊ก', NULL),
('08c33992-6bc7-44c6-b2a8-9de00694705f', 'น.ส.ดวงดาว', 'แก้วเรือง', 'Duangdow', 'Kaewrueng', NULL, NULL, NULL, NULL, '087-687-1184', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ดาว', NULL),
('0965b563-9350-4941-822e-ac152969dc07', 'น.ส.ธนวรรณ', 'เที่ยงสมบุญ', 'Mr.Tanawan', 'Tengsomboon', NULL, NULL, NULL, NULL, '095-405-9339', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โย', NULL),
('0b2ea25c-3171-49d4-88c8-d9bfad9a60f1', 'นายชานนทร์', 'แก้วโชติหิรัญ', 'Mr.Chanon', 'Kaewchothiran', NULL, NULL, NULL, NULL, '087-338-0441', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นนท์', NULL),
('0b51a3a4-a2bd-44db-b9cd-dc6cb2dcf860', 'นาย นนทชัย', 'พลับอินทร์', 'Nontachai', 'Plub-in', NULL, NULL, NULL, NULL, '082-256-6580', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นนท์', NULL),
('0ca2ec28-3a41-4408-8321-751e77dfed4a', 'น.ส.ปุญชรัสมิ์', 'มงคลเตียวสกุล', 'Miss Puncharat', 'Mongkoltewsakul', NULL, NULL, NULL, NULL, '090-927-6646', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แนน', NULL),
('0ca753b6-1750-48fd-b9ee-a2e91efecf20', 'นาย สุรพันธ์', 'พะวันรัมย์', 'Mr.Surapan', 'Phawanram', 'male', NULL, 'surapan@gmail.com', 'surapan@pointit.co.th', '0879927502', 'DevOps Engineer', 'IT Service', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-05 03:51:38', 'ขวัญ', 'Khaw'),
('0e335b9a-ddbf-4586-9ae5-e438e0c43928', 'นายชุมพล', 'คงกระจ่าง', 'Mr.Chumpol', 'Khonkrajung', NULL, NULL, NULL, NULL, '086-336-0392', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จอน', NULL),
('1063724d-5db5-4843-8f0d-5d291dfc1400', 'นาย ปิยวัช', 'แผ้วฉ่ำ', 'Piyawach', 'Paewcham', NULL, NULL, NULL, NULL, '087-617-8926', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แมน', NULL),
('109bc197-84d9-4102-ab5b-c38b18c1c78f', 'นายจิรประภา', 'พรโสม', 'Mr.Jiraprapha', 'Pornsom', NULL, NULL, NULL, NULL, '088-369-8398', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จีจี้', NULL),
('11998e43-25fc-43b2-915f-b7e93be3de60', 'นาย กริชเพชร', 'พุ่มซ้อน', 'Mr.Kritpet', 'Pumsorn', NULL, NULL, NULL, NULL, '094-495-5667', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เด่น', NULL),
('119bf90b-0874-4d23-92f6-db0c5f5ee416', 'นาย สมพร', 'หล่อจีรานนท์', 'Somporn', 'Lorgeranon', NULL, NULL, NULL, NULL, '081-8050815', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พี่เข้', NULL),
('12b19ce0-f08c-456a-ac54-17fb9c260ae6', 'นาย ฐานัสท์', 'มหารัชมงคล', 'Thanat', 'Maharatchamongkhon', NULL, NULL, NULL, NULL, '094-916-9399', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เจียว', NULL),
('13b2c0e8-4f29-45b9-b82a-632cdd490e9d', 'นาย ธนาคม', 'อ่องสถาน', 'Tanacom', 'Ongsathan', NULL, NULL, NULL, NULL, '089-777-1155', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จ๋า', NULL),
('13d98cae-03c1-4485-8cd5-dc39ae5b9563', 'น.ส.ปัฐมา', 'ประการแก้ว', 'Miss Pattama', 'Prakarnkaew', NULL, NULL, NULL, NULL, '091-714-9013', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL),
('154aed4c-a8b4-4a01-b2c4-53cd25133b13', 'น.ส. ภรภัทร', 'ยานธุกิจ', 'Porapath', 'Yanthukij', NULL, NULL, NULL, NULL, '088-896-2702', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แก็ป', NULL),
('157ca547-5b9d-41a6-94ff-faf29ade5dbb', 'นายกานต์ณรงค์', 'เขียวสลับ', 'Mr.Kannarong', 'Khiawsalab', NULL, NULL, NULL, NULL, '086-360-7117', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอล', NULL),
('1701eb8c-db2b-4dfb-9313-11d339cf6c64', 'นายชลชัย', 'ประดิษฐพงศ์', 'Mr.Cholachai', 'Praditthapong', NULL, NULL, NULL, NULL, '082-429-4240', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บิ๊ก', NULL),
('17e2b41b-3ae1-478a-a2c7-5134129a7aee', 'นายเฉลิมเกียรติ์', 'ศรีจำรัส', 'Mr. Chaloemkiat', 'Srijamrat', NULL, NULL, NULL, NULL, '091-775-2965', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ต้น', NULL),
('1b5da05a-ded6-449a-8886-b12c30a3d90d', 'นายเทพดนัย', 'ธรรมจักร์', 'Mr.Tepdanai', 'Thummajak', NULL, NULL, NULL, NULL, '090-173-8499', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แดน', NULL),
('1bea872c-04b1-4d56-a467-5da10dd29e59', 'น.ส.สุภาวดี', 'วุฒิศุภศิริ', 'Miss Suphawadee', 'Wutthisuphasiri', NULL, NULL, NULL, NULL, '083-267-8462', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จูน', NULL),
('1d06a9f3-2669-4283-b712-aaf8472d89c9', 'นาย สมบัติ', 'โสละมัด', 'Sombat', 'Solamad', NULL, NULL, NULL, NULL, '099-735-1366', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL),
('1e14553a-4d96-49ce-a3fe-543470e1eb02', 'นายทวิ', 'บัวปลื้ม', 'Mr. Thawi', 'Buaplaum', NULL, NULL, NULL, NULL, '088-678-4959', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ไนท์', NULL),
('1e5a9630-fb72-4a6a-82df-56c061553e07', 'นายปริพัฒน์', 'อัศวศรีกุลธร', 'Mr.Paripat', 'Ausavasrikulton', NULL, NULL, NULL, NULL, '082-016-9194', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอย', NULL),
('20a7307f-310d-4543-bccf-f27faf453c27', 'นายพิภูษณ', 'มุ่งดี', 'Mr.Pipusana', 'Mungdee', NULL, NULL, NULL, NULL, '090-791-3425', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๊ต', NULL),
('2266d0e8-6c83-4274-babf-58bff5a160b6', 'น.ส. ลิขิต', 'ชาวทองหลาง', 'Likhit', 'Chaothonglang', NULL, NULL, NULL, NULL, '091-545-0987', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ้อย', NULL),
('230b54e6-169b-4c41-8656-79a535ec32bb', 'นาย วรรธน์พล', 'ศิริบุญลักษณ์กุล', 'Watthaphon', 'Siriboonlukkun', NULL, NULL, NULL, NULL, '085-399-0758', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ', NULL),
('26d339f3-7b5e-4abc-999a-05c51fd19ea7', 'นาย วัฒนา', 'ประภาเลิศ', 'Mr.Watthana', 'Prapalert', NULL, NULL, NULL, NULL, '063-421-1002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อั๋น', NULL),
('28203eed-3b4d-4129-91d6-09d564555937', 'นายสุบิน', 'ด่านกลาง', 'Mr.Subin', 'Danklang', NULL, NULL, NULL, NULL, '099-369-2699', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โต้ง', NULL),
('28f44c7d-d75c-4310-b079-f0e7f4445958', 'นาย สมพงษ์', 'ไตรสุทธา', 'Sompong', 'Trisutta', NULL, NULL, NULL, NULL, '063-181-9959', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โล้น', NULL),
('2e3bde74-654e-4d35-ac3a-5bcf251fd8df', 'น.ส. จารุนันท์', 'ปันใจ', 'Jarunan', 'Panjai', NULL, NULL, NULL, NULL, '086-555-4990', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แอน', NULL),
('2e964ef8-82c7-460f-ba23-56bf59e7828a', 'นายนวพล', 'สุนทรถาวร', 'Mr.Nawapol', 'Soontornthaworn', NULL, NULL, NULL, NULL, '092-2847054', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แบงค์', NULL),
('2f2b5845-c1a1-470e-857e-efd61a9916e5', 'นาย วิศรุต', 'สายโยธา', 'Wisrut', 'Saiyotha', NULL, NULL, NULL, NULL, '094-487-2740', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เติ้ล', NULL),
('33e031af-950c-4d75-8b60-f7eea1eccb0e', 'น.ส.ปรียนุช', 'ทัศบุตร', 'Miss Preeyanut', 'Thusabut', NULL, NULL, NULL, NULL, '089-6151916', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปุ้ย', NULL),
('34660212-5152-407a-aa40-689d6160a832', 'นาย อนุชิต', 'บัวพันธ์', 'Mr.Anuchit', 'Buapan', NULL, NULL, NULL, NULL, '065-029-1450', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ทัช', NULL),
('34d8bd02-3a9a-41fa-a9c3-0df557bf24ac', 'นายเหมวัฒน์', 'ประกอบสุข', 'Mr.Hemewat', 'Prakobsuk', NULL, NULL, NULL, NULL, '081-193-5190', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โป้ง', NULL),
('355b65e8-f4a4-4c16-859c-00ef3ae79a38', 'น.ส.ญาณิศา', 'เข็มทอง', 'Miss Yanisa', 'Khamtong', NULL, NULL, NULL, NULL, '064-181-4939', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๋', NULL),
('36b9089b-2ccf-4840-ad12-d43d985a943d', 'นายบรรจง', 'ชาติเป', 'Mr.Banjong', 'Chatpae', NULL, NULL, NULL, NULL, '088-784-5297', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จูน', NULL),
('37c39988-5f2e-4fc2-ba34-59020d3da350', 'นายณัฐวุฒิ', 'ม่วงชา', 'Mr. Nuttawut', 'Muangcha', NULL, NULL, NULL, NULL, '098-882-6239', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๊ต', NULL),
('3b47be7b-5ef5-490b-8666-98cc73b54b82', 'นาย จิตรติชัย', 'ด้วงนาง', 'Chittichai', 'Duangnang', NULL, NULL, NULL, NULL, '082-509-0245', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จิต', NULL),
('3e92e792-1995-403a-bd34-c2ff9fff266f', 'นายกฤษฎา', 'บุญเฉลียว', 'Mr.Krissada', 'Boonchaleaw', NULL, NULL, NULL, NULL, '096-253-4210', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปุ๊ก', NULL),
('3f350eff-549d-42ad-9d26-9c6294e9c424', 'นายสิทธิพงษ์', 'โพธิน', 'Mr.Sittiphong', 'Phothin', NULL, NULL, NULL, NULL, '061-576-2601', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แบงค์', NULL),
('402028ba-6732-4405-bedf-a5f86bc22044', 'นายพีรวัส', 'ชัยคำวัง', 'Mr.Peerawats', 'Chaikhamwang', NULL, NULL, NULL, NULL, '063-779-0099', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ไนซ์', NULL),
('40a7dd65-8ab2-422b-ae49-3263345fdf13', 'นาย วิรุตม์', 'จุดทะมาศ', 'Virut', 'Chudtamas', NULL, NULL, NULL, NULL, '086-780-4514', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บิว', NULL),
('41813955-8db9-4e53-a2ec-0cdbe76589a5', 'นายวิษณุ', 'พานา', 'Mr.Visanu', 'Pana', NULL, NULL, NULL, NULL, '080-571-4636', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นายวิษณุ', NULL),
('41993a3f-17c1-41da-ac32-66a1553eeb05', 'นายพงศธร', 'ล้วนดำรงค์กุล', 'Mr.Pongsatorn', 'Luandumrongkul', NULL, NULL, NULL, NULL, '099-286-4114', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เฟรน', NULL),
('41c6c35a-9029-4ce3-b842-d43da1319497', 'นาย ปิติ', 'นิธิธนาพรกุล', 'Piti', 'Nititanapornkul', 'male', NULL, 'Piti@pointit.co.th', 'Piti@pointit.co.th', '089-692-6913', 'Programer', 'Innovation', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 01:53:28', 'เบียร์', 'Beer'),
('43b64dc9-6c33-434f-84d4-66e7d816db8d', 'นาย วีรยุทธ', 'เจริญชื่น', 'Weerayut', 'Charoenchuen', NULL, NULL, NULL, NULL, '083-157-1142', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ยุทธ', NULL),
('44f12282-8cbe-4f29-9fd1-14a8cd017d48', 'นาย จิรานุวัฒน์', 'กุลนาฑล', 'Jiranuwut', 'Kunnathon', NULL, NULL, NULL, NULL, '083-572-0882', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ๊อฟ', NULL),
('486cf6ce-bc81-4ab5-a31e-eccf6a3033f9', 'นายชลพรรณ', 'ภักดี', 'Mr.Choniapun', 'Pukdee', NULL, NULL, NULL, NULL, '095-097-0707', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หมี', NULL),
('48b71d45-5875-435f-9923-b49e95e5b73b', 'นายนพพล', 'หลักแหล่ง', 'Mr.Noppon', 'Laklang', NULL, NULL, NULL, NULL, '083-953-9620', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พีท', NULL),
('4951a17d-21c2-44ac-8ee6-eba10e574119', 'นายดนนท์ภรรค', 'ดาวดวง', 'Dhanonpuk', 'Daodoung', NULL, NULL, NULL, NULL, '081-842-1369', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตี้', NULL),
('4a172280-f198-4e21-a81d-ad2829a7b093', 'นาย ชวนนท์', 'ตันชัยฤทธิกุล', 'Chawanon', 'Tanchairittikul', NULL, NULL, NULL, NULL, '099-113-2815', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอสส์', NULL),
('4d789493-f4fe-472b-9894-875690c94c74', 'นาย เสถียร', 'การคำ', 'Satean', 'Kankam', NULL, NULL, NULL, NULL, '083-124-3326', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โหน่ง', NULL),
('4d79beb9-4363-430f-b5ff-0851a5c1e143', 'นาย ทวีศักดิ์', 'เขียวสระคู', 'Taweesak', 'Khiaewsraku', NULL, NULL, NULL, NULL, '088-141-1669', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบิร์ด', NULL),
('4df5de09-0a6f-4afb-a0b4-2da33400f987', 'ว่าที่ ร.ต. วรดล', 'ดาวดวง', 'Woradol', 'Daodoung', NULL, NULL, NULL, NULL, '081-612-9616', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โด่ง', NULL),
('4e83dfbb-a125-47ce-a3ab-04bc412ffe81', 'น.ส. วรรณา', 'กิจการ', 'Wanna', 'Kitchakarn', NULL, NULL, NULL, NULL, '080-521-1843', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'คิม', NULL),
('4ecf8118-bc6d-4c0a-ab88-ba95bee8454f', 'นายกันตพัฒน์', 'จงเจริญ', 'Mr.kantaphat', 'Jongjaroen', NULL, NULL, NULL, NULL, '099-328-0644', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จิง', NULL),
('50e27beb-d2b3-45f6-a722-6564f17319d3', 'นายศักรินทร์', 'รัตน์ตัน', 'Mr.Sakkarin', 'Rattan', NULL, NULL, NULL, NULL, '096-954-6526', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ก๊อต', NULL),
('5150c3a9-5e53-4dde-9dad-c0cf64df929d', 'นายธาตรี', 'นกแจ่ม', 'Mr. Thatree', 'Nogjaem', NULL, NULL, NULL, NULL, '092-585-1828', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อูฐ', NULL),
('51a03a5c-2261-4e42-a897-a4fdda982ef1', 'นายคุณากร', 'จรัญจอหอ', 'Mr.Kunakon', 'Jaranjoho', NULL, NULL, NULL, NULL, '090-358-3846', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ใหม่', NULL),
('54f00387-cb21-43bd-8298-3f6bef951adb', 'นาย อดุลย์', 'ยะมะ', 'Adul', 'Ya-ma', NULL, NULL, NULL, NULL, '088-789-4840', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ซาบีน', NULL),
('55104721-250e-435f-9273-01fc7ea92a3c', 'นายเมธวิน', 'ทิพยมงคล', 'Mr.Metawin', 'Tippayamongkol', NULL, NULL, NULL, NULL, '093-580-5880', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'รอม', NULL),
('57398376-5a73-4a63-93ba-04271a2456f0', 'นาย นัฐนันท์', 'ฟักแก้ว', 'Natthanan', 'Fakkaew', NULL, NULL, NULL, NULL, '093-328-8985', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ชานัท', NULL),
('57619331-6351-410f-b1c3-ae67a1b9e022', 'นายพันธรัตน์', 'แก้วเกลี้ยง', 'Mr.Pantarat', 'Kaewkliang', NULL, NULL, NULL, NULL, '086-949-7060', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พัน', NULL),
('5798ec7a-ba3a-4b60-b304-d1de75025364', 'นายชนม์นุชา', 'ภู่สุวรรณ', 'Mr.Chonnucha', 'Poosuwan', NULL, NULL, NULL, NULL, '089-154-2431', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โจ', NULL),
('5a4bc6c3-929c-4d91-a8b1-0549122dcdf0', 'นายณัฐพล', 'สิงห์ทอง', 'Mr.Nutthapol', 'Singthong', NULL, NULL, NULL, NULL, '098-857-4309', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หนุ่ม', NULL),
('5b789738-c72c-4ac4-83b9-50bc1fe69aaa', 'นาย ราเชนทร์', 'มิ่งรักษา', 'Rachen', 'Mingraksa', NULL, NULL, NULL, NULL, '082-790-2062', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เชนทร์', NULL),
('5ca3b81a-f2d1-4d3a-8bd1-578700e11a1f', 'นายจิตรภานุ', 'พรหมภัทร', 'Mr.Jitpanu', 'Promphat', NULL, NULL, NULL, NULL, '080-8934471', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพชร', NULL),
('5d1a3297-f1eb-42bf-a1f1-be83a517bc0f', 'นาย ศุภกิติ์', 'แก้วกล่ำศรี', 'Suprakit', 'Kaewklamsri', NULL, NULL, NULL, NULL, '094-249-4701', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โจ้', NULL),
('5dba38d1-969e-4d18-86bf-4df26f50c76d', 'น.ส.ธณัฐชนน', 'โภคสมบัติ', 'Miss Thanutchanon', 'Phoksombat', NULL, NULL, NULL, NULL, '062-095-6999', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มด', NULL),
('5f0325d8-d64a-4c1c-99ea-51c190224d97', 'นายประเสริฐ', 'รัฐวิเศษ', 'Mr.Prasert', 'Rattawisad', NULL, NULL, NULL, NULL, '091-714-2267', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ฆัง', NULL),
('609f6e59-2d10-4f43-9558-c663e24167c2', 'นาย ดิเรก', 'วงศ์งาม', 'Direk', 'Wongngam', NULL, NULL, NULL, NULL, '086-379-8041', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ต้อย', NULL),
('62162065-4d9a-4be3-bf14-0cf485979edf', 'นาย พงศ์สรร', 'เปรมสิริพงษ์กุล', 'Pongsan', 'Premsiriphongkun', NULL, NULL, NULL, NULL, '094-870-9996', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เสก', NULL),
('62246af9-dd9f-48ca-805f-b34457c6c6fd', 'นายจักรพันธ์', 'ลือสัตย์', 'Mr.Jarkapan', 'Lousataya', NULL, NULL, NULL, NULL, '080-078-3396', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('62a33901-5aa6-4d6f-9aa3-d54bbe325d42', 'นายณัฏฐกิตติ์', 'ทัศนพันธุ์', 'Mr. Natthakit', 'Thussanaphan', NULL, NULL, NULL, NULL, '089-791-9394', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ณัฐ', NULL),
('638df302-3894-4a58-b91c-fa5032eb2ae1', 'นาย ยุทธนา', 'จตุรจิตราพร', 'Yuthana', 'Jaturajitraporn', NULL, NULL, NULL, NULL, '081-626-6307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตำรวจ', NULL),
('6a33d6bb-1192-4677-bff0-41a348043bb0', 'น.ส.สุภาพร', 'เที่ยงสมบุญ', 'Suparporn', 'Thengsomboon', NULL, NULL, NULL, NULL, '099-492-2696', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ยุ้ย', NULL),
('6ab384dd-3a07-436d-8ba1-64522e74117a', 'นายธนวิน', 'คณิตนันทกุล', 'Mr. Tanawin', 'Khanitnanthakul', NULL, NULL, NULL, NULL, '083-652-2659', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เทล', NULL),
('6ae73cf7-8803-4b8c-9276-3636f6fb660c', 'น.ส. แพรวอรุณ', 'นุ่นรักษา', 'Preaw-aroon', 'Noonruksa', NULL, NULL, NULL, NULL, '089-405-2477', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เกด', NULL),
('6c556fc8-87fd-46b0-867d-922b6af12643', 'น.ส.กัลยกร', 'เพ็ชรคง', 'Miss Kalayakorn', 'Phetkong', NULL, NULL, NULL, NULL, '095-075-2396', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จูน', NULL),
('7069f6b9-5619-4ae4-a55e-ff1c86b99099', 'นายจิราวุฒิ', 'ฉิมไทย', 'Mr.Jirawut', 'Chimthai', NULL, NULL, NULL, NULL, '086-449-0642', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'วุฒิ', NULL),
('7075bdb9-80ee-4c7f-8066-266c15969499', 'นายนัฐพล', 'กองพรมลิ', 'Mr.Nattapon', 'Kongpromli', NULL, NULL, NULL, NULL, '064-769-2194', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาร์ม', NULL),
('710dfd16-365b-4714-a08c-ffe754ee79ac', 'นายอรรถกร', 'ปุญญะฐิติ', 'Mr. Atthakorn', 'Punyathiti', NULL, NULL, NULL, NULL, '085-993-6540', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอ็ม', NULL),
('72191de0-63bb-42fe-a80b-c12c3a5ef291', 'นายพงษ์สรรค์', 'ขาครานนท์', 'Mr.Pongsan', 'Chakranon', 'male', NULL, 'pongsan.chakranon@gmail.com', NULL, '065-578-0475', 'Ai Software Developer', 'Innovation', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-06 03:09:48', '2025-01-06 03:09:48', 'ซีน', 'Zeen'),
('73effd44-931a-4a41-a702-b155bf010c14', 'นาย เอกพจน์', 'จันสาขะ', 'Ekapot', 'Junsaka', NULL, NULL, NULL, NULL, '062-661-5225', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('75668d5f-6b72-4736-8b08-2d675ea6f180', 'นาย เอกวิทย์', 'มูซา', 'Aekkawit', 'Musa', NULL, NULL, NULL, NULL, '083-448-6490', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('76e2bae3-d06a-45f6-9987-f504202511df', 'น.ส. วรลักษณ์', 'คุณสุวรรณชัย', 'Woraluck', 'Khunsuwanchai', NULL, NULL, NULL, NULL, '081-253-5130', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หญิงเล็ก', NULL),
('7748e29c-07da-49a2-bd19-427ecc7a5851', 'นาย สุรเดช', 'สุนะพรม', 'Mr.Suradate', 'Sunaprom', NULL, NULL, NULL, NULL, '088-003-9550', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบล', NULL),
('7a7225e1-ab97-47ae-a559-c302b810d6d4', 'นายยุทธนา', 'ทำคำมูล', 'Mr.Yutthana', 'Thamkhammun', NULL, NULL, NULL, NULL, '094-251-5567', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาม', NULL),
('7a770b99-bb51-4695-b25b-a086e8f8ea1f', 'นาย วัชระพงศ์', 'ปรือปรัง', 'Watcharapong', 'Prueprang', NULL, NULL, NULL, NULL, '087-458-2215', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เก่ง', NULL),
('7c481b78-104a-4501-a676-636fc98f4b44', 'นาย สุริยา', 'บุญเอื้อ', 'Suriya', 'Boonnou', NULL, NULL, NULL, NULL, '099-058-3071', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ยา', NULL),
('7dbe221a-126d-46c3-816a-33ea9634d570', 'นายพีระพงษ์', 'บรรยงค์', 'Mr.Peerapong', 'Bunyong', NULL, NULL, NULL, NULL, '094-860-5066', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL),
('7f125064-ce76-4a3f-bcc4-c0842157c3e0', 'นายอดิเทพ', 'จันทรากานตานันท์', 'Mr.Adithap', 'Chantrakantanun', NULL, NULL, NULL, NULL, '081-491-0873', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หยอง', NULL),
('7f9cd3dc-39ba-4a82-9494-5ee4ced1462d', 'นายอภิรักษ์', 'บางพุก', 'Mr.Apirak', 'Bangpuk', NULL, NULL, NULL, NULL, '083-959-5800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แอมป์', NULL),
('7fb7605a-84bb-4dd4-a2c9-b22d8db5f904', 'น.ส.อนิษา', 'อนันตะการ', 'Miss Anisa', 'Anantakan', NULL, NULL, NULL, NULL, '089-026-8149', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๋', NULL),
('81e4903d-911b-4a7f-93c9-5660d8caa587', 'นายสิทธิศักดิ์', 'พิบูลย์', 'Mr.Sittisak', 'Phiboon', NULL, NULL, NULL, NULL, '095-429-7104', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอย', NULL),
('8264761d-2d42-4963-bfff-57f68af50506', 'นาย กวี', 'กรุณา', 'Kavee', 'Karuna', NULL, NULL, NULL, NULL, '082-790-2069', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ออด', NULL),
('833e79a9-d5e2-45a0-8580-8c09e80335b5', 'น.ส.ปราณี', 'ศรีวรานนท์', 'Miss Pranee', 'Sriwaranon', NULL, NULL, NULL, NULL, '093-920-9551', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ฝ้าย', NULL),
('83a5fbb4-c4e3-43aa-a926-91d3a70e759a', 'นาย พัชรพงศ์', 'พูลสุข', 'Patcharapong', 'Poolsuk', NULL, NULL, NULL, NULL, '087-436-7617', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'สเน็ก', NULL),
('891069a6-3957-4517-aa41-1c025dc26ab2', 'นาย ภาณุวัฒน์', 'สุขชีพ', 'Panuwat', 'Sukcheep', NULL, NULL, NULL, NULL, '089-116-0969', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แพน', NULL),
('8a18bf02-f612-4977-9fea-01ed55cbbec4', 'นายฟักครูดีน', 'ยะยอ', 'Mr.Fakrudin', 'Yayo', NULL, NULL, NULL, NULL, '087-393-4050', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ดีน', NULL),
('8a3fd425-e40f-46e2-ada9-8f9e129cac2b', 'นาย ประกอบ', 'จ้องจรัสแสง', 'Prakorb', 'Jongjarussang', NULL, NULL, NULL, NULL, '081-623-6990', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พี่กอบ', NULL),
('8aefa618-9799-4d34-b7fd-ae66b05cb8e1', 'นายสิทธิพร', 'จึงศรีพิษณุ', 'Mr. Sittiporn', 'Jungsrepisanu', NULL, NULL, NULL, NULL, '096-849-9989', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ๊อฟ', NULL),
('8be4f4d5-e1fc-417e-9acf-7872bfedae34', 'นาย กิตติกร', 'ม่วงจีน', 'Kittikorn', 'Muangieen', NULL, NULL, NULL, NULL, '083-230-7797', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กร', NULL),
('8c8101cd-5919-4223-978f-3192d816fb7a', 'นาย นิมิตร', 'หีบสัมฤทธิ์', 'Nimit', 'Hebsumrit', NULL, NULL, NULL, NULL, '082-790-2054', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มิตร', NULL),
('8cfd6989-01a5-4102-aaf0-b458aef568cd', 'น.ส. ชรินทร์ทิพย์', 'วรพุทธตนัน', 'Charintip', 'Vorraputtanun', NULL, NULL, NULL, NULL, '089-955-7764', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บี๋', NULL),
('8f12e843-f684-4dc8-ab46-06013ac11e0b', 'นายอนุวัฒน์', 'พรมลี', 'Mr.Anuwat', 'Promlee', NULL, NULL, NULL, NULL, '065-916-4814', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เจมส์', NULL),
('8f1598ff-4211-4894-92a3-f91a1b97d93a', 'นายปัญณทัต', 'ปองดี', 'Mr.Pannathat', 'Pongdee', NULL, NULL, NULL, NULL, '095-667-3169', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอล', NULL),
('8ff7c801-ba11-409a-80ab-82019073e018', 'นาย อิทธิพร', 'สุวรรณสิงห์', 'Itiporn', 'Suwannasing', NULL, NULL, NULL, NULL, '085-829-0902', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อิท', NULL),
('9050d4c4-ba7e-4247-9690-dcea357553b4', 'นายอภิชาติ', 'เชิดเพชรรัตน์', 'Mr.Aphichat', 'Choedpetcharat', NULL, NULL, NULL, NULL, '088-711-0203', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โต้', NULL),
('93ba3215-f270-485a-a509-ea934d4fcade', 'นายปิยพร', 'ดีเลิศ', 'Mr.Piyaporn', 'Dee-lert', NULL, NULL, NULL, NULL, '093-616-5265', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แก๊ส', NULL),
('94e5f4b7-b97f-4f5e-8241-c1ac44caec21', 'นาย โอฬาร', 'สินธุพันธ์', 'Oran', 'Sintuphan', 'male', NULL, 'Oran.gun@gmail.com', NULL, '085-151-1551', 'Management Diractor ', 'Sales', '2', '2f6d353b-53f1-4492-8878-bc93c18c5de9', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-16 08:49:41', 'ปืน', 'Gun'),
('968da662-e2c9-4079-957f-7da6e12c71b1', 'นาย สมพงษ์', 'ปานเผือก', 'sompong', 'Pranpuek', NULL, NULL, NULL, NULL, '082-790-2060', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เหมา', NULL),
('97a47848-0290-48ed-9616-4b81209b406c', 'นาย ภูษิต', 'มูหะหมัด', 'Phusit', 'Muhamad', NULL, NULL, NULL, NULL, '095-583-3294', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ลีด', NULL),
('97ff614d-2913-4317-bd44-a828b033714a', 'นาย ฉัตรชัย', 'บุญรัตนเสถียร', 'Chaichai', 'Bunrattanasatien', NULL, NULL, NULL, NULL, '085-560-9000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตุ้ม', NULL),
('980a8c86-ecad-4b35-a6a6-dea83e4bc141', 'นาย ทวีวัฒน์', 'สวัสดี', 'Taweewat', '-', NULL, NULL, NULL, NULL, '097-245-2449', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เปี๊ยก', NULL),
('9979da4c-c3e7-4026-9654-0c03a7aa8297', 'คุณจิราธิป', 'วิทยานุศักดิ์', 'Jiratip', 'vittayanusak', 'male', NULL, 'j.vittayanusak@gmail.com', NULL, '090-221-5120', 'Software Tester', 'IT Service', '1', '8c782887-8fd3-4f99-ac27-63054a8a1942', '', NULL, NULL, '2', NULL, '2025-01-06 01:49:08', '2025-01-06 01:49:08', 'เอิร์ธ', 'Earth'),
('9b4e89a6-bf08-442a-81df-92470e43b240', 'นายสุกิจ', 'พรพิทยากุล', 'Mr. Sukit', 'Pornpittayakul', NULL, NULL, NULL, NULL, '097-194-8265', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กิจ', NULL),
('9b998747-978c-48c9-ac5e-f46b1be3b7ae', 'นาย เอกลักษณ์', 'จันทร์เรือง', 'Aekaluk', 'Junruang', NULL, NULL, NULL, NULL, '063-746-4702', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โม่', NULL),
('9c07a6c4-2328-4902-8acb-0f1126797064', 'นายเอกลักษณ์', 'สงวนสินธิ์', 'Mr.Aekalak', 'Sa-nguansin', NULL, NULL, NULL, NULL, '082-072-2972', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เจมส์', NULL),
('9c6f04da-8096-428d-9045-af39787957c5', 'นาง อรุณณีย์', 'เทียมทวีสิน', 'Arunnee', 'Thiamthawisin', NULL, NULL, NULL, NULL, '081-341-1724', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หน่อย', NULL),
('9cafb053-e8f6-4168-bd2a-5b951bbcf54b', 'นายณัฐวัตร', 'มั่งนิมิตร', 'Mr.Natthawat', 'Mungnimit', NULL, NULL, NULL, NULL, '062-556-0393', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เต้ย', NULL),
('9cc0e5db-dea6-44ef-941b-b2525ae6ba2e', 'นายชาญวิทย์', 'บุญมา', 'Mr. Chanwit', 'Boonma', NULL, NULL, NULL, NULL, '084-633-7959', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ๊ต', NULL),
('9fbc57a2-c79c-43d3-b2f2-f6b8e5931d75', 'น.ส. ณฐพรสรวง', 'ชนะแสน', 'Natapornsuang', 'Chanasan', NULL, NULL, NULL, NULL, '086-789-5323', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แหม่ม', NULL),
('a0d1c9bc-9a40-4b04-8917-3edd6d534297', 'นาง สุภาภรณ์', 'สมสงวน', 'Supaporn', 'Somsanguan', NULL, NULL, NULL, NULL, '082-790-2068', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพชร', NULL),
('a188554c-23d6-4815-b9ce-7a14fba41788', 'น.ส. ฐิติชญา', 'มาลาล้ำ', 'Thitichaya', 'Malalam', NULL, NULL, NULL, NULL, '091-739-7430', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพ็ญ', NULL),
('a2447f3e-f44e-4ca5-a359-a4bbcaa2306b', 'นาย รุ่งโรจน์', 'มานพ', 'Rungrote', 'Manob', NULL, NULL, NULL, NULL, '088-874-4141', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โรจน์', NULL),
('a32363c3-470d-4788-8592-e6a1c46b9318', 'น.ส.จิราภรณ์', 'บุตรอุดม', 'Miss Jiraporn', 'Bootudom', NULL, NULL, NULL, NULL, '085-043-8518', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จิ', NULL),
('a4b633e4-b737-45bf-b1bb-d4b9cc649b6e', 'นายณัฐกุล', 'จรรยาเจริญกุล', 'Mr.Nattakun', 'Janyacharoenkun', NULL, NULL, NULL, NULL, '082-601-5502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพียว', NULL),
('a5624855-feb0-4cd7-b500-be0753fcf995', 'นายสันทัศ', 'วาทีกานท์', 'Mr.Suntut', 'Wateekarn', NULL, NULL, NULL, NULL, '086-749-8928', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอย', NULL),
('a5ed74c6-64a7-43cb-a67f-b2d3b635c224', 'น.ส. พรรณราย', 'บุญมาก', 'Pannarai', 'Boonmark', NULL, NULL, NULL, NULL, '081-303-0472', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จูน', NULL),
('a8ed33a8-4522-4b3b-badb-d2bddf28e5ae', 'นายวสวัตติ์', 'หาญชิงชัย', 'Mr.Wasawat', 'Hanchingchai', NULL, NULL, NULL, NULL, '081-001-6449', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาร์ม', NULL),
('a9a5f869-ba2d-4f91-b925-e5c83aaf4589', 'นาย เอกรินทร์', 'หาญประชุม', 'Ekarin', 'Hanprachum', NULL, NULL, NULL, NULL, '084-561-5321', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ๊อฟ', NULL),
('a9ebfeda-7be3-4dee-b503-f2cb4cd2ddbe', 'นาย สำเนียง', 'จำปาแดง', 'Samniang', 'Champadang', NULL, NULL, NULL, NULL, '086-445-1652', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หลุ่ย', NULL),
('a9f9f40b-78d8-4ec5-9f3d-6ffdf96bdfbe', 'นายพรรณภัทร', 'ภูธนะกูล', 'Punnaphat', 'Phuthanakoon', NULL, NULL, NULL, NULL, '097-946-6599', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กิ่ง', NULL),
('add349cc-bad2-41e8-a6e4-edf51049ebfe', 'น.ส.ภรรตธีรา', 'ดาวดวง', 'Pateera', 'Daodoung', NULL, NULL, NULL, NULL, '097-195-9756', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จิน', NULL),
('af10727c-1a47-4379-afa9-c14ca65b6578', 'นาย พรศักดิ์', 'อุทยานิก', 'Pornsak', 'Uttaganik', NULL, NULL, NULL, NULL, '083-428-3524', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบิร์ด', NULL),
('af873f82-ce92-402b-a5be-9a6e582b276a', 'น.ส.ทัตพิชา', 'อับดุลสลาม', 'Miss Tachpicha', 'Abdulsalam', NULL, NULL, NULL, NULL, '089-672-0380', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กีส', NULL),
('b2ecd517-43e1-411e-bff0-6143541d038a', 'ว่าที่ ร.ต. สุวรินทร์', 'เศรษฐธราวงศ์', 'Suwarin', 'Settarawong', NULL, NULL, NULL, NULL, '082-169-4929', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หมี', NULL),
('b4e0645a-df8e-4c64-bd33-18366e10f5c0', 'นาย ธรรมนูญ', 'พงษ์ธนู', 'Thummanoon', 'Phongthanoo', NULL, NULL, NULL, NULL, '090-295-8539', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เลย์', NULL),
('b7e7b967-809a-4f51-be3b-314f5e54f3e6', 'น.ส. สุภาภรณ์', 'งามเปลี่ยม', 'Supaporn', 'Ngampliam', NULL, NULL, NULL, NULL, '088-691-4565', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ฝน', NULL),
('ba4cabdc-17a0-42da-92f6-572effd61dc7', 'ภูมิศักดิ์', 'จันทร์ล้วน', 'Poomsak', 'Janluan', 'male', '2537-06-27', 'poomsak1994@gmail.com', NULL, '086-229-5093', 'Full Stack Developer', 'Innovation', '1', '8c782887-8fd3-4f99-ac27-63054a8a1942', '85 หมู่ 9 ต.นาพู่ อ.เพ็ญ จ.อุดรธานี', '2567-01-04', '677b4d923d6e0.jpg', 'ff2acbbb-4ec0-4214-8a30-eb1fc6e02700', NULL, '2025-01-06 03:27:14', '2025-01-06 03:32:25', 'ภูมิ', 'Poom'),
('bacffdf9-60ea-476d-ba96-71af04f68026', 'นาย สุชานนท์', 'ศรีประพันธ์', 'Suchanon', 'Sriparphan', NULL, NULL, NULL, NULL, '063-7134519', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นัท', NULL),
('bb35fbff-87e4-49b1-877f-931d26eb15b5', 'นาย สราวุธ', 'พูสุวรรณ', 'Sarawut', 'Poosuwan', NULL, NULL, NULL, NULL, '080-658-0866', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปอนด์', NULL),
('bb63771e-f802-4b3d-b38b-706c0f8fe0eb', 'นาย นนทวัฒน์', 'พัฒนกุล', 'Nontawat', 'Pattanakul', NULL, NULL, NULL, NULL, '061-732-8420', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นน', NULL),
('bbd25605-f34d-4810-a57b-cd0c8ff99e4d', 'นายศุภชัย', 'เหมืองทรายมูล', 'Mr.Supachai', 'Muangsaimoon', NULL, NULL, NULL, NULL, '080-492-9178', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นายศุภชัย', NULL),
('bcd4c579-562a-402c-a4de-3ad307b5fd95', 'นาย ธีรชาติ', 'ติยพงศ์พัฒนา', 'Theerachart', 'Tiyapongpattana', 'male', NULL, 'theerachart17@gmail.com', 'theerachart@pointit.co.th', '0819838998', 'Service Management', 'IT Service', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 03:50:21', 'ตั้ม', 'Tum'),
('be822eae-0f92-419b-8653-725f80c8235f', 'นายธวัชชัย', 'โตนวน', 'Mr.Thawatchai', 'Tonuan', NULL, NULL, NULL, NULL, '095-061-1681', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แม็ก', NULL),
('bf678ded-d8ef-45c3-905c-bdafafdfa081', 'นายกาโรล', 'เฟรมัน', 'Mr.Karel', 'Veerman', NULL, NULL, NULL, NULL, '087-271-3776', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จอนท์', NULL),
('c05883b4-12cc-4fd2-b938-c9fe034dc3f1', 'นายณัฐดนัย', 'แจ่มแจ้ง', 'Mr.Natdanai', 'Jamjang', NULL, NULL, NULL, NULL, '081-190-7860', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นัด', NULL),
('c3fa26dd-4c54-4386-933a-f65846e84020', 'น.ส. มาลี', 'แซ่หว้า', 'Marlee', 'Sawar', NULL, NULL, NULL, NULL, '090-996-3690', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มาลี', NULL),
('c71c1c48-3204-4806-b893-0f42ea45a69d', 'นาย จักรกริช', 'พ้นภัย', 'Jakkrit', 'Pontpai', NULL, NULL, NULL, NULL, '081-484-8835', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เพิร์ล', NULL),
('c7b8143b-a6a6-4ea8-b0b0-3737e48929ea', 'ว่าที่ ร.ต.หญิง ชุติพันธุ์', 'วอนเบ้า', 'Acting Sub Lt. Chutiphan', 'Wonbao', NULL, NULL, NULL, NULL, '098-264-0562', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ชุ', NULL),
('c8434fe7-b4b5-41a8-8166-ca8be6a7b03d', 'นาย บุลากร', 'พัวพันธุ์', 'Bulakorn', 'Puapun', NULL, NULL, NULL, NULL, '081-360-2828', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พีท', NULL),
('c8846a35-9d2d-4a9c-b367-244b333afb1c', 'นาย ลิขิต', 'รุ่งเรือง', 'Likit', 'Rungruang', NULL, NULL, NULL, NULL, '084-250-0422', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ชาง', NULL),
('c8efd0c0-5134-4070-9833-936ed3049557', 'นาย ธรรมรัตน์', 'โพธิวัฒน์', 'Thammarat', 'Phothiwat', NULL, NULL, NULL, NULL, '091-545-0995', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ่ง', NULL),
('c95d9acd-3e69-48e9-afae-27a23f0a9b1e', 'น.ส.ฟาตีมะห์', 'สาเต็ง', 'Miss Fatimah', 'Sateng', NULL, NULL, NULL, NULL, '061-448-5706', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มะห์', NULL),
('c9b28e79-0165-46a2-b9e0-9c8579529c14', 'นาย นัฐพล', 'ศรีประกอบ', 'Nattaphon', 'Sriprakob', NULL, NULL, NULL, NULL, '091-734-0063', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ก๊อต', NULL),
('cb77ead9-ad40-4657-9610-08aabba8efa9', 'ว่าที่ ร.อ. อิศเรศ', 'เกตุเดชา', 'Isaret', 'Ketdecha', NULL, NULL, NULL, NULL, '064-097-3480', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ดุ๊ก', NULL),
('cdf26871-edae-4849-8c19-e75d3d7b9051', 'นาย อวิรุทธ์', 'สมสงวน', 'Awirut', 'Somsanguan', NULL, NULL, NULL, NULL, '081-834-1331', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แว่น', NULL),
('cf306963-754e-4469-9c91-9d534bb72d69', 'นายศศิกร', 'สัญญาอริยาภรณ์', 'Mr.Sasikorn', 'Sanyaariyaporn', NULL, NULL, NULL, NULL, '083-415-9966', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ทีม', NULL),
('cfa9b8fa-d92b-4e81-8e5f-74edf5acb532', 'นายยุทธนา', 'เส็มไข', 'Mr.Yuttana', 'Semkhai', NULL, NULL, NULL, NULL, '090-704-5641', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ริท', NULL),
('cfc63ce0-713c-4c99-940c-0204dfdec09c', 'นายธนัชชา', 'คงสมใจ', 'Mr.Thanatcha', 'Kongsomjai', NULL, NULL, NULL, NULL, '080-098-7677', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาร์ม', NULL),
('cfe4264f-24b2-4e6c-8641-b7eec4985dfc', 'ว่าที่ ร.ต. พิสุทธ์', 'วงศ์โสภา', 'Phisut', 'Wongsopha', NULL, NULL, NULL, NULL, '091-545-0988', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เชษฐ์', NULL),
('d21600f0-7c43-4822-9bb1-8bf5300b4eda', 'นาย พิศาล', 'ศิริบัณฑิตย์', 'Pisarn', 'Siribandit', NULL, NULL, NULL, NULL, '081-911-4692', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พิศาล', NULL),
('d223e154-f7de-4c1c-a17c-19a85edf2d2f', 'นายกฤษณะ', 'โตสอาด', 'Mr.Kridsana', 'Tosa-ad', NULL, NULL, NULL, NULL, '091-061-6305', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อาร์ม', NULL),
('d36bc038-b249-468e-a3ed-e01d5a781b9b', 'นายปวรุตม์', 'บุตรจันทร์', 'Mr.Pawarut', 'Bootchan', NULL, NULL, NULL, NULL, '095-846-9398', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เปา', NULL),
('d3cce445-e684-4af4-b9c0-dd386658fccc', 'น.ส.เบญจมาศ', 'มีนาค', 'Miss Benjamas', 'Meenak', NULL, NULL, NULL, NULL, '083-778-7059', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ขวัญ', NULL),
('d45fa00f-eb00-4023-96d2-f4766a95c6da', 'นาย พศิน', 'จันพร้อม', 'Pasin', 'Chanphrom', NULL, NULL, NULL, NULL, '062-348-5353', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ดิษ', NULL),
('d516df1f-e81d-406a-94e0-09ff1774a9ed', 'นายสัมพันธ์', 'ประสิทธิเขตกิจ', 'Mr.Samphan', 'Parsittiketekit', NULL, NULL, NULL, NULL, '091-704-1626', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อัท', NULL),
('d6a77940-c799-4452-adf4-0dded8b7d470', 'นาย ธีรยุทธ์', 'แคว้งใจ', 'Teerayut', 'Kaengjai', NULL, NULL, NULL, NULL, '088-088-4969', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แจ๊ค', NULL),
('d72f9275-43c3-4989-aba2-181b2f75628d', 'น.ส. ปวิชชา', 'เกตุคง', 'Pawitcha', 'Katekong', NULL, NULL, NULL, NULL, '084-464-9246', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นก', NULL),
('d7554419-c280-4f1b-8a65-747013138875', 'นาง พรพรรณ', 'สรรพทรัพย์สิริ', 'Pornpan', 'Suppasubsiri', NULL, NULL, NULL, NULL, '097-331-9824', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ป้าโส่ย', NULL),
('d843c477-0ebf-4fc8-b4a9-6ec76fa6c8d8', 'นายพงศกร', 'หมั่นประกอบ', 'Mr.Pongsakorn', 'Munprakop', NULL, NULL, NULL, NULL, '080-832-0818', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พงศ', NULL),
('da02ba90-a15f-495b-b533-71824126f2e2', 'นายณัชพล', 'ยศราวาส', 'Mr.Nutchapol', 'Yotsarawas', NULL, NULL, NULL, NULL, '082-480-4318', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'หนึ่ง', NULL),
('dabf09bf-4374-4a76-be88-fcd76475c378', 'นายจิรศักดิ์', 'อินยฤทธิ์', 'Mr. Jirasak', 'Inyarit', NULL, NULL, NULL, NULL, '087-484-8009', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โอ', NULL),
('db38edd8-2787-4892-bfda-828b57da36bb', 'นายมูฮัมมัดอามีรูเด็ง', 'นาแซ', 'Mr. Muhammad-armirudeng', '-', NULL, NULL, NULL, NULL, '082-234-8254', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อามี', NULL),
('dc422082-567a-4726-b177-fa76b54e763d', 'นาย ธนวัต', 'สันติอนุรักษ์', 'Thanawat', 'Santianurak', NULL, NULL, NULL, NULL, '095-805-7527', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นิค', NULL),
('dce4c1c7-fcdb-4122-bde4-1ef03406ce67', 'นาง ขนิษฐา', 'อ่องสถาน', 'Kanitta', 'Ongsathan', NULL, NULL, NULL, NULL, '088-022-3292', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เชอรี่', NULL),
('df600b53-afcc-4da2-bba5-38e5229e37ab', 'นาย พิชัย', 'บริบาลบุรีภัณฑ์', 'Phichai', 'Boribanbureephan', NULL, NULL, NULL, NULL, '090-131-8323', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ตู่', NULL),
('e1993400-7c75-45d2-b452-81885882534b', 'นาย อภิศักดิ์', 'จันทร์หนองสรวง', 'Apisak', 'Jannongsrong', NULL, NULL, NULL, NULL, '063-473-0990', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ศักดิ์', NULL),
('e46f0f37-5f31-48c0-ae6e-66eef30af78f', 'นาย ธาวิน', 'พร้อมเจริญ', 'Tawin', 'Promcharoen', NULL, NULL, NULL, NULL, '088-874-4142', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นิว', NULL),
('e71752cf-f292-47c3-839f-b51c311cff0e', 'นาย เอกชัย', 'เขียวสด', 'Akkachai', 'Khiawsod', NULL, NULL, NULL, NULL, '088-022-3282', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อ๊อด', NULL),
('e746b7c4-cee1-4816-9f4f-f4ee02edd61d', 'นายศุภชัย', 'ซื่อตรง', 'Mr.Supachai', 'Suetong', NULL, NULL, NULL, NULL, '099-185-4333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เตย', NULL),
('e76d95e1-1a90-4a7c-b65a-5013db101c2b', 'น.ส.อัญชลี', 'โอฬารจารุชิต', 'Anchalee', 'Olancharuchit', NULL, NULL, NULL, NULL, '084-704-8919', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นุ้ย', NULL),
('e802e276-1fb5-4805-9d89-d7b2e393f395', 'น.ส.สุวลี', 'หลักหาญ', 'Miss Suwalee', 'Lakhan', NULL, NULL, NULL, NULL, '083-100-4414', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'จี', NULL),
('e8985b74-0c37-412b-8e5e-338e4db9a471', 'น.ส.ณัฐทิญา', 'ดาวประดิษฐ์', 'Miss Nattity', 'Daopradit', NULL, NULL, NULL, NULL, '094-590-2913', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แนน', NULL),
('eb38e9bb-9ab2-4a7a-b886-f569402709d8', 'น.ส. นันทิกา', 'จ้องจรัสแสง', 'Nanthika', 'Chongcharassang', 'female', NULL, 'nanthika@gmail.com', 'nanthika@pointit.com', '063-197-9263', 'Project Manager', 'Enterprise', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 01:50:58', 'โม', 'Mo');
INSERT INTO `employees` (`id`, `first_name_th`, `last_name_th`, `first_name_en`, `last_name_en`, `gender`, `birth_date`, `personal_email`, `company_email`, `phone`, `position`, `department`, `team_id`, `supervisor_id`, `address`, `hire_date`, `profile_image`, `created_by`, `updated_by`, `created_at`, `updated_at`, `nickname_th`, `nickname_en`) VALUES
('eba684ff-0ab3-4082-878b-92a2d8773fa5', 'น.ส. ธนวรรณ', 'สีหราช', 'Tanawan', 'Siharaj', NULL, NULL, NULL, NULL, '098-167-9929', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'น้ำหวาน', NULL),
('ec65aef7-9df2-4da2-be8b-a027b445b589', 'น.ส. มัทธนา', 'คล้อยวิถี', 'Matthana', 'Khloiwithi', NULL, NULL, NULL, NULL, '089-8902794', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ป้านิด', NULL),
('ec79f246-4382-4a6b-bd32-3ac55c48bfd6', 'น.ส.ชฎาวรรณ', 'ดำคำ', 'Miss Chadawan', 'Dumkhum', NULL, NULL, NULL, NULL, '083-418-2215', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นิด', NULL),
('ecd30c57-b1f0-453d-bdc9-d426a5e8f0e7', 'น.ส.วินยาดา', 'บึงกระเสริม', 'Miss Winyada', 'Buengkraseam', NULL, NULL, NULL, NULL, '094-616-2944', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มด', NULL),
('ed1d30d4-c223-4b8d-bdf5-8c0d5212f8e5', 'นายฐสิษฐ์', 'ชัยวังกุลพัฒน์', 'Mr.Thasit', 'Chaiwangkunlaput', NULL, NULL, NULL, NULL, '062-376-5454', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ไมค์', NULL),
('ee14a91d-61ef-4390-93c9-28b65fa6e390', 'นายนิกร', 'อ่อนชื่นจิตร', 'Mr.Nikorn', 'Oncheunjit', NULL, NULL, NULL, NULL, '081-677-0902', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'มัง', NULL),
('ef31401f-208d-4574-83c7-b8000ff3665d', 'นายวัฒนชัย', 'น้อยหล่อง', 'Mr.Wattanachai', 'Noilong', NULL, NULL, NULL, NULL, '064-108-3718', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'แชมป์', NULL),
('f049ab3c-88a9-4dc5-813a-7f8f4d451228', 'นายกิตติพงษ์', 'บุญสูง', 'Mr.kittipong', 'Boonsung', NULL, NULL, NULL, NULL, '082-018-6679', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เก่ง', NULL),
('f1aaff60-e88e-4905-a166-00808a181664', 'นาย เดชา', 'สุรัจกุลวัฒนา', 'Mr.Decha', 'Suratkullwattana', 'male', NULL, 'suratkullwattana.decha@gmail.com', 'decha@pointit.co.th', '0931581695', 'นักพัฒนาระบบ (C ,C# , JAVA)', 'IT Service', '1', '5', '', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-05 03:46:37', 'ไข่', 'Khai'),
('f328af8d-389f-41ec-a5cb-00d74b85f7e3', 'นาย ธนกาลบดี', 'กังสรัตน์วาณิช', 'Thanakalbodee', 'Kungsaratwanich', NULL, NULL, NULL, NULL, '091-545-0983', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอก', NULL),
('f38f805b-a1b3-4965-ae4f-a2374f7e2110', 'น.ส. ปรัชญา', 'ปู่แตงอ่อน', 'Pratchaya', 'Poo-tangon', NULL, NULL, NULL, NULL, '093-107-2152', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปัท', NULL),
('f4b78c9b-b0aa-42f2-8fcb-53c8e125c67e', 'นายภัฏพล', 'กาญจนสิริวิโรจน์', 'Mr.Pattapol', 'Kanjanasirivirote', NULL, NULL, NULL, NULL, '082-241-4676', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ปาร์ค', NULL),
('f4e0505f-63d5-4491-8182-9bcda079e6fc', 'นายธนกฤต', 'สิงห์ประเสริฐ', 'Mr.Thanagrid', 'Singprasoed', NULL, NULL, NULL, NULL, '084-780-2502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'โจ้', NULL),
('f54e9031-a608-4f8c-8a0e-450f98c1ede6', 'นายโศภณ', 'ปุญณกิตติ', 'Mr.Somon', 'Punnakitti', NULL, NULL, NULL, NULL, '094-810-5337', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'พล', NULL),
('f604f0ce-34ae-4808-8e5a-699e6e012092', 'นาย บุญเกิด', 'ทีภูเขียว', 'Boongerd', 'Theehukhieo', 'male', NULL, 'boongerd@pointit.co.th', 'boongerd@pointit.co.th', '081-874-1889', 'System Engineer Manager', 'Enterprise', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '19 Soi Suphaphong 1 split 6,\r\nKweng Nongbon', NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-06 04:41:08', 'บอส', ''),
('f655d139-ca13-4449-9c03-d7604659ca98', 'นาย มนตรี', 'โพธิ์ศรี', 'Montri', 'Poesri', NULL, NULL, NULL, NULL, '087-602-0009', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ต้อม', NULL),
('f7c4d951-23d7-4e01-a116-3bf4cb61a356', 'นายพลังประชา', 'มีแสง', 'Mr.Phalangpracha', 'Meeseang', NULL, NULL, NULL, NULL, '090-336-9929', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอย', NULL),
('f836c2c9-95dd-4c47-8b3f-b53d8045e69d', 'นาย คฑาวุฒิ', 'วงศ์งาม', 'Khatawut', 'Wongngam', NULL, NULL, NULL, NULL, '098-535-9654', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'บอส', NULL),
('f84f12af-300a-4bb5-a321-fd622516412c', 'นายชัยวัฒน์', 'บุญเพ็ชร', 'Mr.Chaiwat', 'Bunpech', NULL, NULL, NULL, NULL, '087-781-3822', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'กอล์ฟ', NULL),
('f8befa7c-b5b7-496c-a6f2-70be59ce5e73', 'น.ส.อัจฉรา', 'สมนางรอง', 'Miss Atchara', 'Somnangrong', NULL, NULL, NULL, NULL, '092-473-2951', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'อัจฉรา', NULL),
('f91a680f-54df-49d3-8c71-a2330e96db65', 'นาย อิสรา', 'เดชคำภู', 'Isara', 'Detchacumpoo', NULL, NULL, NULL, NULL, '091-545-0992', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'ทูล', NULL),
('fa8be4b1-3350-4786-bec0-d02a9879fabe', 'นาง นุจรี', 'ชาวทองหลาง', 'Nutjaree', 'Chaowtonglang', NULL, NULL, NULL, NULL, '088-874-4140', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'นก', NULL),
('fce2bf50-704c-48b4-8b04-219a2c247b34', 'น.ส.ดารณี', 'ปุญญะฐิติ', 'Miss Daranee', 'Punyathiti', NULL, NULL, NULL, NULL, '096-843-7008', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เอ๋', NULL),
('fdbb0a58-25d3-4413-b986-d12538f963f6', 'นาย กฤษณพงศ์', 'เย็นเยือก', 'Kitsanapong', 'Yenyuak', NULL, NULL, NULL, NULL, '081-653-1517', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL),
('febfd06b-7d52-45f2-a13f-48d68d2fbe5f', 'นายอนุชา', 'หลุยจันทึก', 'Mr.Anucha', 'Luichanthuek', NULL, NULL, NULL, NULL, '065-881-4493', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, '2025-01-04 12:42:12', '2025-01-04 12:42:12', 'เบียร์', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` char(36) NOT NULL COMMENT 'รหัสการเบิกค่าใช้จ่าย (UUID)',
  `expense_number` varchar(30) NOT NULL COMMENT 'เลขที่การเบิกอัตโนมัติ (EXP-YYMMDDH-NNNN)',
  `expense_title` varchar(255) NOT NULL COMMENT 'หัวข้อการเบิก',
  `project_id` char(36) DEFAULT NULL COMMENT 'รหัสโครงการที่เกี่ยวข้อง',
  `expense_date` date NOT NULL COMMENT 'วันที่เกิดค่าใช้จ่าย',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'จำนวนเงินรวมทั้งหมด',
  `status` enum('Pending','Approved','Rejected','Paid') NOT NULL DEFAULT 'Pending' COMMENT 'สถานะการเบิก',
  `submitter_id` char(36) NOT NULL COMMENT 'รหัสผู้เบิก',
  `approver_id` char(36) DEFAULT NULL COMMENT 'รหัสผู้อนุมัติ',
  `paid_date` date DEFAULT NULL COMMENT 'วันที่จ่ายเงิน',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'วิธีการจ่ายเงิน',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาที่สร้างรายการ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันเวลาที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `expense_number`, `expense_title`, `project_id`, `expense_date`, `total_amount`, `status`, `submitter_id`, `approver_id`, `paid_date`, `payment_method`, `remark`, `created_at`, `updated_at`) VALUES
('3a250e4c-eff3-4474-a30a-95b3c52c2ed3', 'EXP-250427151611-0001', 'ทดสอบ', '52d95985-84b0-4d61-8748-b1a76856536f', '0000-00-00', 0.00, 'Pending', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', NULL, NULL, NULL, '', '2025-04-27 08:16:11', '2025-04-27 08:16:11');

--
-- Triggers `expenses`
--
DELIMITER $$
CREATE TRIGGER `before_insert_expenses` BEFORE INSERT ON `expenses` FOR EACH ROW BEGIN
    -- ประกาศตัวแปรในส่วนต้นของบล็อก
    DECLARE new_number VARCHAR(30);
    DECLARE counter INT;
    
    -- สร้าง UUID ถ้ายังไม่มี
    IF NEW.expense_id IS NULL THEN
        SET NEW.expense_id = UUID();
    END IF;
    
    -- สร้างเลขที่การเบิกอัตโนมัติ ถ้ายังไม่มี
    IF NEW.expense_number IS NULL OR NEW.expense_number = '' THEN
        -- นับจำนวนรายการที่สร้างในวันนี้
        SELECT COUNT(*) + 1 INTO counter 
        FROM expenses 
        WHERE DATE(created_at) = CURDATE();
        
        -- สร้างเลขที่การเบิกใหม่
        SET new_number = CONCAT(
            'EXP-',
            DATE_FORMAT(NOW(), '%y%m%d%H%i%s'),
            '-',
            LPAD(counter, 4, '0')
        );
        
        SET NEW.expense_number = new_number;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `expense_approval_limits`
--

CREATE TABLE `expense_approval_limits` (
  `limit_id` char(36) NOT NULL COMMENT 'รหัสเพดานการอนุมัติ (UUID)',
  `role` varchar(50) NOT NULL COMMENT 'บทบาทผู้ใช้',
  `max_amount` decimal(15,2) NOT NULL COMMENT 'จำนวนเงินสูงสุดที่อนุมัติได้',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาที่สร้าง',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันเวลาที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_approval_limits`
--

INSERT INTO `expense_approval_limits` (`limit_id`, `role`, `max_amount`, `created_at`, `updated_at`) VALUES
('c4b50a5c-186e-11f0-a634-3417ebbed40a', 'Executive', 1000000.00, '2025-04-13 13:54:07', '2025-04-13 13:54:07'),
('c4b5839c-186e-11f0-a634-3417ebbed40a', 'Sale Supervisor', 100.00, '2025-04-13 13:54:07', '2025-04-27 07:55:32'),
('c4b58485-186e-11f0-a634-3417ebbed40a', 'Seller', 100.00, '2025-04-13 13:54:07', '2025-04-27 07:55:38'),
('c4b584be-186e-11f0-a634-3417ebbed40a', 'Engineer', 100.00, '2025-04-13 13:54:07', '2025-04-27 07:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `expense_approval_logs`
--

CREATE TABLE `expense_approval_logs` (
  `log_id` char(36) NOT NULL COMMENT 'รหัสล็อก (UUID)',
  `expense_id` char(36) NOT NULL COMMENT 'รหัสการเบิกค่าใช้จ่าย',
  `reviewer_id` char(36) NOT NULL COMMENT 'รหัสผู้ทบทวน',
  `action` enum('Submit','Approve','Reject','Request Revision','Pay') NOT NULL COMMENT 'การกระทำ',
  `status` varchar(50) NOT NULL COMMENT 'สถานะหลังจากการกระทำ',
  `comment` text DEFAULT NULL COMMENT 'ความคิดเห็น',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาที่สร้างบันทึก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_approval_logs`
--

INSERT INTO `expense_approval_logs` (`log_id`, `expense_id`, `reviewer_id`, `action`, `status`, `comment`, `created_at`) VALUES
('dfccde07-2029-4d83-beb2-3acb5e87ee42', '3a250e4c-eff3-4474-a30a-95b3c52c2ed3', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'Submit', 'Pending', 'สร้างคำขอเบิกค่าใช้จ่าย', '2025-04-27 08:16:11');

--
-- Triggers `expense_approval_logs`
--
DELIMITER $$
CREATE TRIGGER `before_insert_expense_approval_logs` BEFORE INSERT ON `expense_approval_logs` FOR EACH ROW BEGIN
    -- สร้าง UUID ถ้ายังไม่มี
    IF NEW.log_id IS NULL THEN
        SET NEW.log_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `expense_documents`
--

CREATE TABLE `expense_documents` (
  `document_id` char(36) NOT NULL COMMENT 'รหัสเอกสาร (UUID)',
  `expense_id` char(36) NOT NULL COMMENT 'รหัสการเบิกค่าใช้จ่าย',
  `item_id` char(36) DEFAULT NULL COMMENT 'รหัสรายการค่าใช้จ่าย',
  `document_name` varchar(255) NOT NULL COMMENT 'ชื่อเอกสาร',
  `document_type` varchar(50) DEFAULT NULL COMMENT 'ประเภทเอกสาร',
  `file_path` varchar(255) NOT NULL COMMENT 'ที่อยู่ไฟล์',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'ประเภทไฟล์',
  `file_size` int(11) DEFAULT NULL COMMENT 'ขนาดไฟล์',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปโหลด',
  `uploaded_by` char(36) NOT NULL COMMENT 'ผู้อัปโหลด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `expense_documents`
--
DELIMITER $$
CREATE TRIGGER `before_insert_expense_documents` BEFORE INSERT ON `expense_documents` FOR EACH ROW BEGIN
    -- สร้าง UUID ถ้ายังไม่มี
    IF NEW.document_id IS NULL THEN
        SET NEW.document_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `expense_items`
--

CREATE TABLE `expense_items` (
  `item_id` char(36) NOT NULL COMMENT 'รหัสรายการค่าใช้จ่าย (UUID)',
  `expense_id` char(36) NOT NULL COMMENT 'รหัสการเบิกค่าใช้จ่าย',
  `expense_type` varchar(100) NOT NULL COMMENT 'ประเภทค่าใช้จ่าย',
  `description` text DEFAULT NULL COMMENT 'รายละเอียด',
  `amount` decimal(15,2) NOT NULL COMMENT 'จำนวนเงิน',
  `expense_date` date DEFAULT NULL COMMENT 'วันที่ของค่าใช้จ่ายนี้',
  `location` varchar(255) DEFAULT NULL COMMENT 'สถานที่ที่เกิดค่าใช้จ่าย',
  `is_taxable` tinyint(1) DEFAULT 0 COMMENT 'มีภาษีหรือไม่',
  `tax_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'จำนวนภาษี',
  `has_receipt` tinyint(1) DEFAULT 0 COMMENT 'มีใบเสร็จหรือไม่',
  `receipt_number` varchar(100) DEFAULT NULL COMMENT 'เลขที่ใบเสร็จ (ถ้ามี)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาที่สร้างรายการ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันเวลาที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `expense_items`
--
DELIMITER $$
CREATE TRIGGER `after_delete_expense_items` AFTER DELETE ON `expense_items` FOR EACH ROW BEGIN
    UPDATE expenses
    SET total_amount = (
        SELECT COALESCE(SUM(amount), 0)
        FROM expense_items
        WHERE expense_id = OLD.expense_id
    ),
    updated_at = CURRENT_TIMESTAMP
    WHERE expense_id = OLD.expense_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_expense_items` AFTER INSERT ON `expense_items` FOR EACH ROW BEGIN
    UPDATE expenses
    SET total_amount = (
        SELECT SUM(amount)
        FROM expense_items
        WHERE expense_id = NEW.expense_id
    ),
    updated_at = CURRENT_TIMESTAMP
    WHERE expense_id = NEW.expense_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_expense_items` AFTER UPDATE ON `expense_items` FOR EACH ROW BEGIN
    UPDATE expenses
    SET total_amount = (
        SELECT SUM(amount)
        FROM expense_items
        WHERE expense_id = NEW.expense_id
    ),
    updated_at = CURRENT_TIMESTAMP
    WHERE expense_id = NEW.expense_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_expense_items` BEFORE INSERT ON `expense_items` FOR EACH ROW BEGIN
    -- สร้าง UUID ถ้ายังไม่มี
    IF NEW.item_id IS NULL THEN
        SET NEW.item_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `expense_types`
--

CREATE TABLE `expense_types` (
  `type_id` char(36) NOT NULL COMMENT 'รหัสประเภทค่าใช้จ่าย (UUID)',
  `type_name` varchar(100) NOT NULL COMMENT 'ชื่อประเภทค่าใช้จ่าย',
  `description` text DEFAULT NULL COMMENT 'รายละเอียด',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'สถานะใช้งาน',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันเวลาที่สร้าง',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันเวลาที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_types`
--

INSERT INTO `expense_types` (`type_id`, `type_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('3767ea1f-186c-11f0-a634-3417ebbed40a', 'ค่าเดินทาง', 'ค่าเดินทางโดยเครื่องบิน, รถไฟ, รถโดยสาร หรือพาหนะอื่นๆ', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('37686477-186c-11f0-a634-3417ebbed40a', 'ค่าที่พัก', 'ค่าโรงแรม หรือที่พักอาศัย', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('3768655f-186c-11f0-a634-3417ebbed40a', 'ค่าเบี้ยเลี้ยง', 'เบี้ยเลี้ยงรายวันสำหรับการเดินทาง', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('376865ac-186c-11f0-a634-3417ebbed40a', 'ค่ารับรอง', 'ค่าอาหาร ค่าเครื่องดื่ม หรือการรับรองลูกค้า', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('376865eb-186c-11f0-a634-3417ebbed40a', 'ค่าน้ำมัน', 'ค่าน้ำมันเชื้อเพลิงสำหรับการเดินทาง', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('37686627-186c-11f0-a634-3417ebbed40a', 'ค่าทางด่วน', 'ค่าผ่านทางหรือทางด่วนพิเศษ', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('3768665c-186c-11f0-a634-3417ebbed40a', 'ค่าวัสดุอุปกรณ์', 'ค่าอุปกรณ์หรือวัสดุสำหรับงาน', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('376866a4-186c-11f0-a634-3417ebbed40a', 'ค่าจัดส่ง', 'ค่าส่งเอกสารหรือพัสดุ', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('376866e1-186c-11f0-a634-3417ebbed40a', 'ค่าที่จอดรถ', 'ค่าที่จอดรถระหว่างปฏิบัติงาน', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51'),
('3768671a-186c-11f0-a634-3417ebbed40a', 'อื่นๆ', 'ค่าใช้จ่ายอื่นๆ ที่ไม่ได้ระบุประเภท', 1, '2025-04-13 13:35:51', '2025-04-13 13:35:51');

--
-- Triggers `expense_types`
--
DELIMITER $$
CREATE TRIGGER `before_insert_expense_types` BEFORE INSERT ON `expense_types` FOR EACH ROW BEGIN
    -- สร้าง UUID ถ้ายังไม่มี
    IF NEW.type_id IS NULL THEN
        SET NEW.type_id = UUID();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` char(36) NOT NULL,
  `product_name` varchar(255) NOT NULL COMMENT 'ชื่อสินค้า',
  `product_description` mediumtext DEFAULT NULL COMMENT 'รายละเอียดสินค้า',
  `unit` varchar(50) DEFAULT NULL COMMENT 'หน่วยนับ',
  `cost_price` decimal(15,2) DEFAULT NULL COMMENT 'ราคาต้นทุน',
  `selling_price` decimal(15,2) DEFAULT NULL COMMENT 'ราคาขาย',
  `supplier_id` char(36) DEFAULT NULL COMMENT 'รหัสผู้จำหน่าย',
  `team_id` char(36) DEFAULT NULL COMMENT 'รหัสทีม (เชื่อมโยงกับตาราง teams)',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้อัพเดทข้อมูลล่าสุด',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันอัพเดทข้อมูลล่าสุด',
  `main_image` varchar(255) DEFAULT NULL COMMENT 'รูปหลักของสินค้า'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `unit`, `cost_price`, `selling_price`, `supplier_id`, `team_id`, `created_by`, `created_at`, `updated_by`, `updated_at`, `main_image`) VALUES
('047b8da6-f2b4-44fa-b959-498d4996c07b', 'กดเกดเกด', 'กดเดกเกด', 'ชิ้น', 0.00, 0.00, NULL, '37547921-5387-4be1-bde0-e9ba5c4e0fdf', '2', '2025-04-12 05:45:30', '2', '2025-04-12 05:47:15', '047b8da6-f2b4-44fa-b959-498d4996c07b.png'),
('075afde8-650f-4d75-b73d-f41242854682', 'Software Devlopment', 'การพัฒนาระบบตามความต้องการของลูกค้า', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 23:18:35', '2', '2024-12-04 09:28:29', '075afde8-650f-4d75-b73d-f41242854682.jpeg'),
('0eb7a552-9888-4541-a43d-a6fa5b143dbc', 'Point IT IOC Platform', 'Platform - Smart City', NULL, NULL, NULL, NULL, NULL, '5', '2024-12-02 07:10:43', NULL, '2024-12-02 07:10:43', NULL),
('162fd42b-855e-40ac-8696-0d0535fbe2b1', 'Implementation', '', NULL, NULL, NULL, NULL, NULL, '2', '2024-11-01 00:09:37', '2', '2024-12-04 09:19:59', '162fd42b-855e-40ac-8696-0d0535fbe2b1.jpg'),
('19747bf2-8f2d-47db-a2e8-4fca20843812', 'Toner', 'Toner for Printer Samsung 203E', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:38:16', NULL, '2024-11-11 08:38:16', NULL),
('1d285bc6-cc8c-47f7-900e-bf84c92f12ad', 'ค่าเช่าเครื่อง Printer', 'ค่าเช่าเครื่องเงินไชโย', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:39:17', NULL, '2024-11-11 08:39:17', NULL),
('1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 'Auto Update Passbook Printer', 'Hitachi BH-180AZ', NULL, NULL, NULL, NULL, NULL, '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 04:17:21', NULL, '2025-01-06 04:17:21', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc.png'),
('3224e7a4-44ee-40ad-a6ac-22305c2b01eb', 'Smart Healthcare', 'ชุดกระเป๋า (Health Kit Set) สำหรับตรวจสุขคัดกรอกสถานะสุขภาพเคลื่อนที่ เก็บค่าข้อมูลเข้าระบบ โดยการตรวจวัดค่าจากอุปกรณ์เชื่อมต่อเข้ากับระบบ', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 22:58:23', '2', '2024-12-04 09:27:28', '3224e7a4-44ee-40ad-a6ac-22305c2b01eb.jpg'),
('3431f4cb-f892-4e08-a9af-240a743ebc25', 'Smart Safety', 'งานเกี่ยวกับกล้องโทรทัศน์วงจรปิด\r\nและงานสายใยแก้วนำแสง\r\nรวมถึงซ่อมแซม CCTV', NULL, NULL, NULL, NULL, NULL, 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:26:48', '2', '2024-12-04 09:23:35', '3431f4cb-f892-4e08-a9af-240a743ebc25.jpg'),
('3bf8bc62-f878-4fd9-9bee-2a6917190458', 'Magnetic Stripe LKE477U-N', 'Magnetic Stripe', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:48:14', NULL, '2024-11-11 08:48:14', NULL),
('46a5caa6-f5f6-413c-a9f2-cd4167247e69', 'ทดสอบ 5', '', 'ชิ้น', 0.00, 0.00, NULL, '1', '2', '2025-04-12 05:38:58', '2', '2025-04-12 05:44:54', '46a5caa6-f5f6-413c-a9f2-cd4167247e69.png'),
('4c85d842-54f3-4f06-87e6-553f81488234', 'Smart Emergency', 'ระบบเฝ้าระวังเหตุฉุกเฉิน', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-12 06:18:20', '3', '2024-10-20 13:35:30', '4c85d842-54f3-4f06-87e6-553f81488234.png'),
('581f6ca7-8e1e-447a-9dae-680755c4fd29', 'Installation', 'งานจ้างเหมาติดตั้งโครงการฯ', NULL, NULL, NULL, NULL, NULL, 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-11-04 03:15:31', '2', '2024-12-04 09:21:55', '581f6ca7-8e1e-447a-9dae-680755c4fd29.jpg'),
('6e2ba9df-293d-4d88-b85e-4399e237d8c0', 'K-Lynx Platform', 'Smart Management', 'ระบบ', 300000.00, 500000.00, '23722daa-6eec-4a29-aa60-89cdea4dcd8c', NULL, '3', '2025-04-09 05:50:58', NULL, '2025-04-09 05:50:58', NULL),
('7defdc10-75d8-4433-8b4f-0eeba38b674f', 'BioIDM Face Scan', 'ระบบยืนยันตัวตน ผ่านการเปรียบเทียบใบหน้า บัตรประจำตัวประชาชน และอื่นๆ', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 23:18:48', NULL, '2024-10-11 23:52:54', ''),
('aa203517-e140-4abc-9fa8-0e9926365967', 'Signotec Gamma', 'Signature Pad', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:40:01', NULL, '2024-11-11 08:40:01', NULL),
('abf31336-8385-4be6-9a6c-587719a5e0df', 'NEW6260', '3in1 Pinpad', NULL, NULL, NULL, NULL, NULL, '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-11-11 08:40:33', NULL, '2024-11-11 08:40:33', NULL),
('ae10bae3-0b1c-419f-8b21-8c57c607d3de', 'MA', '', NULL, NULL, NULL, NULL, NULL, '3', '2024-10-15 21:56:31', NULL, '2024-10-15 21:56:31', ''),
('b9fcda13-e694-4e04-a8df-fdf27ee08979', 'IBOC', 'มหาวิทยาลัยขอนแก่น', NULL, NULL, NULL, NULL, NULL, '2', '2024-10-11 23:19:12', '3', '2024-10-11 23:54:16', ''),
('c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 'Smart Showroom', 'ระบบ AI เพื่อเพิ่มประสิทธิภาพของ Showroom', 'ระบบ', 25000.00, 50000.00, '23722daa-6eec-4a29-aa60-89cdea4dcd8c', NULL, '3', '2025-04-09 06:09:43', NULL, '2025-04-09 06:09:43', NULL),
('de486d4d-c877-40a8-a113-d92b2dfcbda5', 'Hardware', '', NULL, NULL, NULL, NULL, NULL, 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-11-25 01:15:10', '2', '2024-12-04 09:19:33', 'de486d4d-c877-40a8-a113-d92b2dfcbda5.jpg'),
('df374787-e96c-4d3c-8089-3867edd96cf4', 'Project Management', '', NULL, NULL, NULL, NULL, NULL, '3', '2024-10-31 21:59:27', '2', '2024-12-04 09:25:06', 'df374787-e96c-4d3c-8089-3867edd96cf4.jpg'),
('e021eb8c-6bd5-49a7-a652-8f0bdc860a17', 'Microsoft 365', 'Microsoft 365 Business Standard\r\nMicrosoft 365 Business Premium', NULL, NULL, NULL, NULL, NULL, '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 04:39:19', NULL, '2025-01-06 04:39:19', NULL);

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

--
-- Dumping data for table `product_documents`
--

INSERT INTO `product_documents` (`id`, `product_id`, `document_type`, `file_path`, `file_name`, `file_size`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('b3ea1d86-1f62-4450-be10-39bf61901a55', '46a5caa6-f5f6-413c-a9f2-cd4167247e69', 'specification', '../../../uploads/product_documents/6e3e6d4d-0c4a-4217-92b3-bfb25f01c0cc.xlsx', 'dsfsdf.xlsx', 5638, '2025-04-12 05:44:54', '2', '2025-04-12 05:44:54', NULL);

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
('0082723c-a633-407f-bd10-a76d6c64b2cf', 'พัฒนาระบบ QR และติดตั้ง บนเครื่อง AUP ธ.กรุงไทย', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '0000-00-00', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 14018691.59, 15000000.00, 9345794.39, 10000000.00, 4672897.20, 33.33, 1401869.16, 934579.44, 467289.72, NULL, '2024-11-11 08:38:02', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-11 13:14:59', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('009b7557-c96b-4f2c-aeba-3649b4278cb2', 'เช่าใช้อุปกรณ์และระบบแพลตฟอร์มเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุภายในบ้านและภายนอกบ้าน   Emergency Monitoring', '2024-10-07', '0000-00-00', 'ชนะ (Win)', 'CNTR-00066/68', '', '2024-10-10', '3', 494747.66, 529380.00, 280373.83, 300000.00, 214373.83, 43.33, 494747.66, 280373.83, 214373.83, '32104ee7-4b28-400b-bb7b-1ab55e1cf19d', '2024-11-07 04:39:00', '3', '2024-12-11 13:14:59', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('0369b298-43fe-4664-8eaa-f71e691586fe', 'ระบบการประชุมและถ่ายทอดสด', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '0000-00-00', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 308411.21, 330000.00, 229100.00, 245137.00, 79311.21, 25.72, 30841.12, 22910.00, 7931.12, NULL, '2024-11-28 08:45:41', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-11 13:14:59', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('04b15a59-91a2-4bab-9dd1-6366c49a06d2', 'จ้างซ่อมแซมท่อร้อยสายใต้ดินและสายใยแก้สนำแสงของระบบกล้องโทรทัศน์วงจรปิด จำนวน 2 รายการ', '2024-10-01', '2025-01-28', 'ชนะ (Win)', '400/2567', '', '2024-09-30', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 4074766.36, 4360000.00, 1085500.00, 1161485.00, 2989266.36, 73.36, 4074766.36, 1085500.00, 2989266.36, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:10:34', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('05c427ba-81af-4873-9e10-df57427e8305', 'ระบบฌาปนกิจสงเคราะห์ สตช.', '2024-09-30', '2024-12-25', 'ชนะ (Win)', '', 'Sangfor HCI, Netowrk, Microsft License', '2024-08-19', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 2850000.00, 3049500.00, 1982863.87, 2121664.34, 867136.13, 30.43, 2850000.00, 1982863.87, 867136.13, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:22:34', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('05d29d2b-39ab-4c46-b34b-801ede800172', 'โครงการพัฒนางานระบบจัดซื้อจัดจ้าง  กบข.', '2023-12-25', '2024-12-25', 'ชนะ (Win)', 'PO2024012', '', '2023-10-20', '3', 3200000.00, 3424000.00, 2650000.00, 2835500.00, 550000.00, 17.19, 3200000.00, 2650000.00, 550000.00, '34ea3368-fa1c-445a-aeb8-821c87086d3a', '2024-10-17 04:53:37', '3', '2024-12-11 13:14:59', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('08122fe3-3a63-47f7-abf3-fbd14d3d947e', 'Signature Pad', '2024-08-31', '2024-10-31', 'ชนะ (Win)', '', '', '2024-08-31', '3140fdaf-5103-4423-bf87-11b7c1153416', 24672897.20, 26400000.00, 20066121.50, 21470750.00, 4606775.70, 18.67, 24672897.20, 20066121.50, 4606775.70, NULL, '2024-11-11 08:55:52', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-12-11 13:14:59', NULL, 'aa203517-e140-4abc-9fa8-0e9926365967', 7.00),
('0acb3ca0-9c79-4953-a686-1f4ab035b35c', 'Health Kit Set', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-11-18', '3', 186284.11, 199324.00, 142242.99, 152200.00, 44041.12, 23.64, 18628.41, 14224.30, 4404.11, '594abb40-0296-4aa0-a1fd-82f479359ed5', '2024-12-02 13:49:43', '3', '2024-12-11 13:14:59', '3', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('0b23febb-a6b0-4897-99b0-f181f3dfe903', 'MA DLD Server', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 160000.00, 171200.00, 88804.49, 95020.80, 71195.51, 44.50, 160000.00, 88804.49, 71195.51, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:44:06', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('0b8f2e6e-5e6f-42c9-8ab5-aafa5ad065eb', 'ระบบบริหารจัดการส่วนกลาง K-Lynx 3 จุด', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'Project-co: พี่ซีน\r\nUX/UI: พี่แอมป์\r\nDev Internal: พี่ขวัญ\r\n\r\n**จุดละ 200,000 บาท', '0000-00-00', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 600000.00, 642000.00, 0.93, 1.00, 599999.07, 100.00, 0.00, 0.00, 0.00, '0968cd06-9d79-4933-8de8-399cb9ac5868', '2025-01-17 11:33:20', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-17 11:44:14', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '0eb7a552-9888-4541-a43d-a6fa5b143dbc', 7.00),
('0df824e9-139b-4fe0-af22-e2c390df0cc6', 'WA SCB Pinpad 3in1 จำนวน 3,000u (5ปี)', '2024-12-01', '2029-11-30', 'ชนะ (Win)', '', 'PO.674111068117 Date 28/08/2024', '2024-11-11', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', 3000000.00, 3210000.00, 0.00, 0.00, 0.00, 0.00, 3000000.00, 0.00, 3000000.00, '0f80acd4-d034-4175-b501-f879a9e203de', '2025-01-30 07:53:14', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-30 07:53:14', NULL, 'abf31336-8385-4be6-9a6c-587719a5e0df', 7.00),
('104d9772-4091-4b50-bad7-b89e445cdada', 'NID DLD', '2024-08-08', '2024-10-08', 'ชนะ (Win)', '', 'Dell Server, Microsoft License', '2024-08-05', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 541000.00, 578870.00, 357230.65, 382236.80, 183769.35, 33.97, 541000.00, 357230.65, 183769.35, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:25:01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('1103540e-b227-4f59-8729-1b85ecd0d05d', 'MA Faculty of Veterinary Science Chulalongkorn University', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '- Preventive Maintenance (PM: 8x5 NBD) 12 Times/year\r\n- Corrective Maintenance (CM: 8x5 NBD) 4 Times/year\r\n- Remote/Email/Call unlimited\r\n- Include Travel expenses and Accommodation (BKK Area)\r\n- Start date 1-Oct- 2024 to 30-Sep-2025', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 90000.00, 96300.00, 0.93, 1.00, 89999.07, 100.00, 90000.00, 0.93, 89999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-06 09:48:27', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 09:48:27', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('11bf7c88-a563-4a01-8419-a32500a9194d', 'โครงการจ้างเหมาบริการจัดหาและพัฒนาติดตั้งระบบเตือนภัยและแจ้งเหตุร้ายด้วยระบบข้อความผ่านโครงข่ายโทรศัพท์เคลื่อนที่ (Cell Broadcast) ของศูนย์ปฏิบัติการสํานักงานตํารวจแห่งชาติ (ศปก.ตร.) สํานักงานตํารวจแห่งชาติ (Police Emergency Warning System ; PEWS)', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'End user : RTP\r\nProject prime : MM', '0000-00-00', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 89376612.82, 95632975.72, 76500140.31, 81855150.13, 12876472.51, 14.41, 0.00, 0.00, 0.00, '88d465c6-3e16-4c58-a6da-10bce309af89', '2025-03-17 03:51:49', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-17 03:51:49', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('1338b661-5cb2-438f-ad71-e24316d9b2ae', 'Access Control and Time &amp;amp; Attendance Terminal', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-09-07', '3', 200000.00, 214000.00, 121495.33, 130000.00, 78504.67, 39.25, 20000.00, 12149.53, 7850.47, '213830aa-08d9-4673-9081-3fcba6ce1625', '2024-12-02 13:35:41', '3', '2024-12-11 13:14:59', '3', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('15288aa6-9497-471c-812c-3251021c8f72', 'MA Faculty of Dentistry Chulalongkorn University', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '- Preventive Maintenance (PM: 8x5 NBD) 3 Times/year (4 months per time)\r\n- Corrective Maintenance (CM: 8x5 NBD) 8 Times/year\r\n- Remote/Email/Call unlimited\r\n- Exclude Hardware Spare part and Software License\r\n- Include Travel expenses and Accommodation (BKK Area)\r\n- Start date 1-Oct- 2024 to 30-Sep-2025', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 150000.00, 160500.00, 0.93, 1.00, 149999.07, 100.00, 150000.00, 0.93, 149999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-06 09:51:28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 09:51:28', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('161e830e-355e-4364-acce-405857cf30b9', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Booth)', '2025-03-13', '2025-03-19', 'ชนะ (Win)', '', '', '2025-03-09', '3', 49500.00, 52965.00, 30460.00, 32592.20, 19040.00, 38.46, 49500.00, 30460.00, 19040.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-04-09 06:20:27', '3', '2025-04-09 06:20:27', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('16e09e12-f206-4f3b-a5f7-21cff663edfa', 'Client Certificate Authentication', '0000-00-00', '0000-00-00', 'แพ้ (Loss)', '', 'Project-co: พี่ซีน\r\nDev Internal: พี่ขวัญ\r\nOnsite 2 Day: เอิร์ธ พี่แอมป์', '2025-01-16', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 150000.00, 160500.00, 0.93, 1.00, 149999.07, 100.00, 0.00, 0.00, 0.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-01-16 09:24:51', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-02-17 03:41:37', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '581f6ca7-8e1e-447a-9dae-680755c4fd29', 7.00),
('1c9c103d-de4b-4542-8ba0-c017cd06e23b', 'สถานีตรวจวัดก๊าซแบบออนไลน์ รุ่น MUI-Station 1.3', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-11-27', '3', 934000.00, 999380.00, 707289.72, 756800.00, 226710.28, 24.27, 93400.00, 70728.97, 22671.03, '5aa126c7-c78d-4234-b0f3-45153034626e', '2024-12-02 13:52:52', '3', '2024-12-11 13:14:59', '3', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('1cf8da79-24cc-4f87-ad15-bae140ab4e55', 'GFCA Project Wireless Controller and Access Point MA 1Y6M_8x5NBD_พัฒนาการ48', '2025-03-17', '2026-08-31', 'ชนะ (Win)', '', '', '2025-02-28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 75000.00, 80250.00, 59880.00, 64071.60, 15120.00, 20.16, 75000.00, 59880.00, 15120.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-02-28 08:46:37', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-19 09:30:08', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('1f46b8e2-23bb-433f-99e7-0485db56d84b', 'โครงการพัฒนาศักยภาพด้านความปลอดภัยบริเวณพื้นที่สาธารณ ถนน ราษอุทิศ 200 ปี', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 10934579.44, 11700000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', '2024-11-27 08:13:44', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('22b13d95-688f-4c84-8012-f08793f2103d', 'MA DLD NSW5', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 230000.00, 246100.00, 4160.00, 4451.20, 225840.00, 98.19, 230000.00, 4160.00, 225840.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:41:44', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('26b7618c-cba9-47bd-a7f5-026e193dd543', 'โครงการเพิ่มประสิทธิภาพระบบให้บริการสัญญาณภาพแบบ OnLine  เมืองพัทยา', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', 'เสนองานในนามคุณโอฬาร อบจ.ชลบุรี', '2025-02-03', '3', 467289.72, 500000.00, 300000.00, 321000.00, 167289.72, 35.80, 46728.97, 30000.00, 16728.97, 'fb683856-9635-4316-ad3a-2eb57d6eb10f', '2025-04-09 05:57:36', '3', '2025-04-09 05:57:36', NULL, '6e2ba9df-293d-4d88-b85e-4399e237d8c0', 7.00),
('26ffb5a6-2d63-464a-a9db-3e976c2d3893', 'GFCA MA Service Onsite Support 2025 5x8NBD_1Y_END 31Dec2025', '2025-01-01', '2025-12-30', 'ชนะ (Win)', '', '', '2025-03-19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 136500.00, 146055.00, 0.93, 1.00, 136499.07, 100.00, 136500.00, 0.93, 136499.07, '054160f3-f50e-40a2-a45e-569777875172', '2025-03-19 09:37:47', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-19 09:37:47', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('284033fa-5e82-48be-a26e-60f91dd0b65f', 'WA SCB Signature Pad 3,000u (5ปี)', '2024-12-01', '2029-11-30', 'ชนะ (Win)', '', 'PO.674111068116 Date 28/08/2024', '2024-11-11', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', 3000000.00, 3210000.00, 0.00, 0.00, 0.00, 0.00, 3000000.00, 0.00, 3000000.00, '0f80acd4-d034-4175-b501-f879a9e203de', '2025-01-30 07:02:30', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-30 07:54:21', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', 'aa203517-e140-4abc-9fa8-0e9926365967', 7.00),
('2c2f0090-5f59-46be-a426-e426fde826df', 'MA DLD Regislive', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 500000.00, 535000.00, 89450.00, 95711.50, 410550.00, 82.11, 500000.00, 89450.00, 410550.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:42:55', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('2cfe725d-8bfa-4d78-a196-a30881a8eb22', 'เช่าช่ ใช้ชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้ที่มีภาวะพึ่งพิงในบ้านและภายนอกบ้านพร้อมระบบแพลตฟอร์และงานบริการ ระยะเวลา 11 เดือน', '2025-02-03', '2025-12-31', 'ชนะ (Win)', '70/2568', '', '2025-01-01', '3', 266822.43, 285500.00, 201192.50, 215275.98, 65629.93, 24.60, 266822.43, 201192.50, 65629.93, '5aa126c7-c78d-4234-b0f3-45153034626e', '2025-04-09 05:45:25', '3', '2025-04-09 05:45:25', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('3b418fca-65f3-471b-b61d-ba338a9aa36e', 'ระบบบริหารจัดการอุปกรณ์เทคโนโลยีสารสนเทศ', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-09-11', '3', 2609906.54, 2792600.00, 1401869.16, 1500000.00, 1208037.38, 46.29, 2609906.54, 1401869.16, 1208037.38, 'a485226f-e787-44e7-a140-4bf50433c525', '2024-12-02 13:31:24', '3', '2024-12-11 13:14:59', '3', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('3bde2447-a24c-4b11-933c-5a5160e902f3', 'จ้างปรับปรุงและเพิ่มประสิทธิภาพระบบสายนำสัญญาณใยแก้วนำแสงแบบฝั่งใต้ดิน ระยะที่6', '2024-05-31', '2024-11-26', 'ชนะ (Win)', '256/2567', '', '2024-05-30', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 32383177.57, 34650000.00, 16562000.00, 17721340.00, 15821177.57, 48.86, 32383177.57, 16562000.00, 15821177.57, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:03:57', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('43ca8d52-9f8f-4b7f-b676-998928a0145d', 'จ้างติดตั้งระบบกล้องโทรทัศน์วงจรปิด', '2024-10-18', '2024-12-19', 'ชนะ (Win)', '', '', '2024-10-02', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 4665000.00, 4991550.00, 309810.27, 331496.99, 4355189.73, 93.36, 4665000.00, 309810.27, 4355189.73, '8b315bda-7e61-4d0d-a995-3653ddda3140', '2024-12-03 01:12:40', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-11 13:14:59', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('464369d4-5720-4b09-9834-8e46884ab187', 'งานติดตั้งสายสัญญาณคอมพิวเตอร์ และย้ายห้องควบคุมระบบคอมพิวเตอร์ (GEN)', '2024-10-25', '2025-04-26', 'ชนะ (Win)', '00/2568', '', '2024-10-25', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 1860000.00, 1990200.00, 1335834.00, 1429342.38, 524166.00, 28.18, 1860000.00, 1335834.00, 524166.00, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-04 03:49:25', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('4677b262-6fc0-4bc7-8708-a0806b091577', 'จัดซื้อจอภาพและระบบการแสดงผลภาพมัลติมีเดียแบบเชื่อมต่อกัน (Video Wall Display)', '2024-05-09', '2024-10-06', 'ชนะ (Win)', '234/2567', '', '2024-04-09', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 3128037.38, 3347000.00, 0.00, 0.00, 0.00, 0.00, 3128037.38, 0.00, 3128037.38, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:00:40', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('4821cea3-07a4-4495-a139-8e8d74e26254', 'OBEC Firewall Gateway', '2024-12-02', '2025-02-02', 'ชนะ (Win)', '', '', '2024-10-21', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 500000.00, 535000.00, 1.00, 1.07, 499999.00, 100.00, 500000.00, 1.00, 499999.00, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 22:26:58', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-01-16 08:49:14', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('49100e5b-82a9-4a11-849b-17e45117adba', 'Microsoft 365 Renewal Yearly', '2025-01-02', '2026-01-01', 'ชนะ (Win)', '', '', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 38700.00, 41409.00, 30130.00, 32239.10, 9169.90, 0.00, 0.00, 0.00, 0.00, '88bc1a3c-f646-4e7a-863d-3424b0fbe1c1', '2025-01-06 06:23:09', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 06:47:19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'e021eb8c-6bd5-49a7-a652-8f0bdc860a17', 7.00),
('49b9dd79-d94d-45c9-8645-cf4caaab398a', 'จ้างเหมาติดตั้งสิทธิ์การใช้งานโปรแกรมป้องกันไวรัสคอมพิวเตอร์', '2024-10-05', '2024-10-19', 'ชนะ (Win)', '34/2568', '', '2024-10-04', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 732710.28, 784000.00, 618412.00, 661700.84, 114298.28, 15.60, 732710.28, 618412.00, 114298.28, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 02:47:33', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('4ab1ec63-fe78-4c3f-b039-1870bd5ad987', 'งานติดตั้งสายสัญญาณคอมพิวเตอร์ และย้ายห้องควบคุมระบบคอมพิวเตอร์ (ปรับปรุงห้อง control)', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2024-10-25', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 3568000.00, 3817760.00, 3754861.60, 4017701.91, -186861.60, -5.24, 3568000.00, 3754861.60, -186861.60, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-06 05:07:24', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('4ae49ab9-51d6-43f4-a6f3-ad20f3ed16c4', 'งานจ้างบำรุงรักษาระบบ MOBILE FACE RECOGNITION', '2024-06-01', '2025-08-03', 'ชนะ (Win)', 'A02/3160030757/2567', '', '2024-06-01', '3', 1073708.41, 1148868.00, 791114.96, 846493.01, 282593.45, 26.32, 1073708.41, 791114.96, 282593.45, NULL, '2024-10-15 21:59:34', '3', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('4b7ab0ca-b747-482a-a113-03a5891f9aab', 'BSP Hayashi Telempu Project Replacement Veeam Server', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-03-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 42000.00, 44940.00, 1.00, 1.07, 41999.00, 100.00, 42000.00, 1.00, 41999.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-03-06 04:39:00', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-06 04:39:00', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('4b86e1b4-e921-4ca4-809b-28fc4ef63b07', 'ค่าชุดอุปกรณ์และระบบแพลดฟอร์มวิเคราะห์ข้อมูลและปัญญาประติษฐ์ในบริการการดูแลการใช้ชีวิตและ ดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุ', '2025-06-02', '2025-09-30', 'ชนะ (Win)', '', '', '2025-01-01', '3', 319000.00, 341330.00, 244294.00, 261394.58, 74706.00, 23.42, 319000.00, 244294.00, 74706.00, 'cc80c251-336b-4039-9850-5a042948e8f3', '2025-04-09 05:32:18', '3', '2025-04-09 05:37:09', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('4c4bc3e0-f462-4c9a-b626-8999a69acb72', 'SC Polymer Solar - PO-2024081900001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024081900001', '', '2024-08-19', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 11040.00, 11812.80, 10080.00, 10785.60, 960.00, 8.70, 11040.00, 10080.00, 960.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:44:40', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('4ce93953-2518-44e8-9380-e55008c39155', 'GFCA MA IBM Server and Storage_AAI_Site_2Years_END30Apr2027', '2025-05-01', '2027-04-30', 'ชนะ (Win)', '', '', '2025-03-19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 628700.00, 672709.00, 330400.00, 353528.00, 298300.00, 47.45, 628700.00, 330400.00, 298300.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-03-19 09:49:26', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-19 09:49:26', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('525b742d-5749-40d2-a148-0290161fd3c3', 'เช่าใช้กล้อง CCTV พร้อมระบบ People Counting (Auto Showcase ที่ Central Eastville)', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-04-09', '3', 93457.94, 100000.00, 46728.97, 50000.00, 0.00, 0.00, 9345.79, 4672.90, 4672.90, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-04-09 12:25:18', '3', '2025-04-09 12:25:18', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('52d95985-84b0-4d61-8748-b1a76856536f', 'BSP Hayashi Telempu MA Service Onsite support 8x5 NBD (1Year) 2025', '2025-02-01', '2026-01-31', 'ชนะ (Win)', '', '', '2025-01-20', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 90000.00, 96300.00, 0.93, 1.00, 89999.07, 100.00, 90000.00, 0.93, 89999.07, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-01-20 04:17:13', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-20 04:17:13', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('5486e1ad-8bc0-4884-a100-c626c0a2d731', 'BSP NIDEC Network Device preventive maintenance and Asset Management', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-02-28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 142000.00, 151940.00, 4800.00, 5136.00, 137200.00, 96.62, 14200.00, 480.00, 13720.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-02-28 08:42:34', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-02-28 08:42:34', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('56235a39-8439-479b-9c55-9affabfa7b2a', 'Web Application for ISO Management', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', 'Project-co: พี่ซีน\r\nUX/UI: พี่แอมป์ (Leader) / Outsource\r\nDev Internal: พี่ขวัญ\r\n\r\n**รอ Test Demo เพื่อสรุป SOW สำหรับออกใบเสนอราคา', '2025-01-16', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-01-16 09:44:23', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-02-17 03:42:42', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('5713dfdc-147e-4618-83e4-6c4ed544d80c', 'SC Polymer Solar - PO-2024082000001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024082000001', '', '2024-08-20', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 334475.00, 357888.25, 307529.48, 329056.54, 26945.52, 8.06, 334475.00, 307529.48, 26945.52, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:51:01', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('57cff5f7-e083-40ed-be05-323e55b0f12c', 'MA OBEC SUN', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 2099065.42, 2246000.00, 0.93, 1.00, 2099064.49, 100.00, 2099065.42, 0.93, 2099064.49, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:17:32', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('5a85ff90-219a-4cc4-8d6a-30fd3153a864', 'บ้านบางแสน - SO2024-AC-08-08', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'SO2024-AC-08-08', '', '2024-08-07', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 33644.86, 36000.00, 23550.00, 25198.50, 10094.86, 30.00, 33644.86, 23550.00, 10094.86, '7f242c52-9e30-4791-97b2-053fb960423b', '2024-12-06 09:57:57', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('5ee574f8-06dc-4c6f-8d61-7fb7c093d010', 'Service Domain evergreen.co.th and SSL Certificate Configuration for Evergreen.co.th', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', 'Service Domain evergreen.co.th and SSL Certificate Configuration for Evergreen.co.th\r\nSales ขาย Yaniza', '2025-01-28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 0.00, 0.00, 12000.00, 12840.00, 0.00, 0.00, 0.00, 12000.00, -12000.00, '2b5c101f-db79-4143-89f9-2b42fbea06bd', '2025-01-28 06:12:18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-28 06:12:18', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('5f6e4e14-2709-42ac-bc70-c32e8c7656ff', 'โครงการพัฒนาศักยภาพด้านความปลอดภัยบริเวณพื้นที่เสี่ยงภัย ในเส้นทางสายเลียบหาดป่าตอง ระยะที่ 3', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 13084112.15, 14000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', '2024-11-27 08:11:34', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('63d6b111-d394-4ac7-a60c-1041a59872c0', 'SC Polymer Solar - PO-2024080800001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024080800001', '', '2024-08-08', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 211500.00, 226305.00, 193500.00, 207045.00, 18000.00, 8.51, 211500.00, 193500.00, 18000.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 07:38:34', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('64e25a53-27be-4e55-8dc8-8a6cdb3b8115', 'จัดซื้อระบบรักษาความปลอดภัยสำนักรักษาความปลอดภัย สำนักงานเลขาธิการสภาผู้แทนราษฎร ด้วยระบบวิเคราะห์ภาพด้วย AI (ระบบเฝ้าระวังอัจฉริยะด้วยปัญญาประดิษฐ์) จำนวน ๑ ระบบ', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-01-01', '3', 35468224.30, 37951000.00, 31775700.93, 34000000.00, 3692523.37, 10.41, 3546822.43, 3177570.09, 369252.34, 'a7398772-5d5f-4f09-9eb6-6edf32fb9893', '2025-04-09 05:25:35', '3', '2025-04-09 05:25:35', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('69b3d4b2-f00b-4d75-9379-f17aaf4c2e34', 'โครงการสวนสัตว์อัจฉริยะเพื่อเศรษฐกิจ การศึกษา และการอนุรักษ์ ขับเคลื่อนระบบนิเวศสู่ความยั่งยืนด้วยเทคโนโลยีดิจิทัล (Thailand ZooNova : A New Era of Smart Zoo - Economic Education and Conservation and Sustainability Ecosystem', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'โครงการนี้นำเสนอให้กับ องค์การสวนสัตว์แห่งประเทศไทย ในพระบรมราชูปถัมภ์ 327 ถ. สุโขทัย แขวงดุสิต เขตดุสิต กรุงเทพมหานคร 10300', '2025-01-01', '3', 11214953.27, 12000000.00, 10000000.00, 10700000.00, 1214953.27, 10.83, 0.00, 0.00, 0.00, '6e23608d-46bb-4e74-8326-21365397565b', '2025-04-09 06:39:31', '3', '2025-04-09 12:22:07', '3', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('6a50ca74-9f27-483a-a312-126660ddcfc7', '3in1 Pinpad', '2024-08-01', '2024-10-31', 'ชนะ (Win)', '', '', '2024-08-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 14579439.25, 15600000.00, 11014813.08, 11785850.00, 3564626.17, 24.45, 14579439.25, 11014813.08, 3564626.17, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2024-11-11 08:57:47', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-12-11 13:14:59', NULL, 'abf31336-8385-4be6-9a6c-587719a5e0df', 7.00),
('6ad9b333-0acc-410b-b5b0-7c6c9497d9be', 'โครงการจัดทำระบบเครือข่ายเสมือน (Server Consolidation and Virtualization) VPN+IP Phone Ph2', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 11635514.02, 12450000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-27 08:16:07', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('6f49e7bd-2886-4448-8c2c-a34b78d05b7e', 'โครงการเพิ่มประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิดในจุดเสี่ยงภัยของเมืองพัทยา', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 9532710.28, 10200000.00, 6822429.91, 7300000.00, 2710280.37, 28.43, 0.00, 0.00, 0.00, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:22:07', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('6f882833-ac9e-4367-8468-ebb05dd81a8e', 'Mindss Thai-Otsuka VMware Server Deployment', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-01-21', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 20000.00, 21400.00, 0.93, 1.00, 19999.07, 100.00, 2000.00, 0.09, 1999.91, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-21 09:30:36', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-21 09:30:36', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('7269890f-a7b1-47e0-907b-c0fb5eacc576', 'MA OBEC FM', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 4609000.00, 4931630.00, 1.00, 1.07, 4608999.00, 100.00, 4609000.00, 1.00, 4608999.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:10:17', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('72f91cb8-944d-44f5-babc-f4288568c964', 'Web Hosting สตช.', '2024-12-01', '2025-11-30', 'ชนะ (Win)', '', 'Nutanix HCI, Network, Backup, UPS, Firewall', '2024-10-07', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 12131800.00, 12981026.00, 10393849.25, 11121418.70, 1737950.75, 14.33, 12131800.00, 10393849.25, 1737950.75, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:20:48', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-16 06:46:49', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('74152581-8a94-443f-ad39-140e5f9dc509', 'Samsung LaserJet Toner SL-4020ND  (SU894A) - 15 box', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'IV671030001', '', '2024-10-30', '2', 36448.60, 39000.00, 27670.50, 29607.44, 8778.10, 24.08, 36448.60, 27670.50, 8778.10, 'df6e7ebd-77f2-49e4-bcdf-04c71608005f', '2024-12-09 01:55:48', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:15:13', '2', '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('759f33fb-b998-4d5f-bd80-343867ef52a0', 'จ้างเหมาปรับปรุงระบบไฟฟ้าของกล้องโทรทัศน์วงจรปิด', '2024-08-23', '2024-12-20', 'ชนะ (Win)', '336/2567', '', '2024-08-22', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 461682.24, 494000.00, 108062.00, 115626.34, 353620.24, 76.59, 461682.24, 108062.00, 353620.24, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 03:07:51', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('7692b403-0447-4011-8154-ddcf896f5dd4', 'Apple Tablet - PO6711-00003', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO6711-00003', '', '2024-11-01', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 58100.00, 62167.00, 55520.00, 59406.40, 2580.00, 4.44, 58100.00, 55520.00, 2580.00, '6d128135-3e95-4226-9956-21bb63f25cc0', '2024-12-06 10:27:15', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('78f216ec-1aad-43a2-898f-e2301ce04a05', 'โครงการจัดตั้งศูนย์รับแจ้งเหตุฉุกเฉินแห่งชาติ สำนักงานตำรวจแห่งชาติ', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'End user : RTP\r\nProject prime : SP', '0000-00-00', '2', 934579.44, 1000000.00, 46728.97, 50000.00, 887850.47, 95.00, 0.00, 0.00, 0.00, '88d465c6-3e16-4c58-a6da-10bce309af89', '2025-03-17 04:04:12', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-17 11:34:04', '2', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('7b578d51-8794-46f4-8a3a-f1da2429e855', 'MA กล้องอาคารบ้านเจ้าพระยา', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'ราคา 1 ปี ไม่รวมอะไหล่', '0000-00-00', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 317757.01, 340000.00, 261682.24, 280000.00, 56074.77, 17.65, 31775.70, 26168.22, 5607.48, '360a7a11-6bcd-4301-8156-b4d11ebd6794', '2024-11-28 08:52:18', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-16 01:55:56', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('7c338957-c9a9-4134-b79b-3d131b19dec9', 'WA SCB Magnetic Stripe 2,200u(5ปี)', '2024-12-01', '2029-11-30', 'ชนะ (Win)', '', 'PO. 674111078697 Date 03/10/2024', '2024-11-11', '2', 989000.00, 1058230.00, 0.00, 0.00, 989000.00, 100.00, 989000.00, 0.00, 989000.00, '0f80acd4-d034-4175-b501-f879a9e203de', '2025-01-30 07:57:09', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-03-13 14:51:55', '2', '3bf8bc62-f878-4fd9-9bee-2a6917190458', 7.00),
('7c67ce7e-ee05-487f-a763-4627899516bb', 'โครงการ บ่อวิน สมาร์ท ซิตี้ ดูแลสุขภาพแบบอัจฉริยะ (Smart Health Care) สำหรับผู้สูงอายุ ประจำปีงบประมาณ 2567', '2023-09-02', '2024-09-02', 'ชนะ (Win)', '1/2567', '', '2023-09-15', '3', 623831.78, 667500.00, 423848.00, 453517.36, 199983.78, 32.06, 623831.78, 423848.00, 199983.78, '32104ee7-4b28-400b-bb7b-1ab55e1cf19d', '2024-10-11 23:29:28', '3', '2024-12-11 13:14:59', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('7cb191ba-d203-4c00-8841-f4957193c26a', 'SC Polymer Solar - PO-2024082900001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024082900001', '', '2024-08-29', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 108720.00, 116330.40, 100000.00, 107000.00, 8720.00, 8.02, 108720.00, 100000.00, 8720.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:54:56', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('7fc6a707-7a83-4295-a0fc-f32434aeecb0', 'บริการเช่าใช้ระบบ AI Platform พร้อม Hardware สำหรับงาน Showroom เดือนที่ 2', '2025-04-16', '2025-05-16', 'ชนะ (Win)', '', '', '2025-04-09', '3', 38000.00, 40660.00, 24500.00, 26215.00, 13500.00, 35.53, 38000.00, 24500.00, 13500.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-04-09 06:24:22', '3', '2025-04-09 06:24:22', NULL, 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('819456c1-3df2-41b4-874f-377b4d2ecca4', 'โครงการเพิ่มประสิทธิภาพในการบริหารจัดการด้านความปลอดภัยในพื้นที่อาคารกรีฑาในร่ม ศูนย์กีฬาแห่งชาติภาคตะวันออก', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 53831775.70, 57600000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:23:11', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('83a43bff-44ad-4a1b-a7fc-bb3ae2ca67d5', 'Blaster Camera - IIS2410-0011', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'IIS2410-0011', '', '2024-10-17', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 221320.00, 236812.40, 203600.00, 217852.00, 17720.00, 8.01, 221320.00, 203600.00, 17720.00, '48cf0983-375c-46de-ab41-72350901a376', '2024-12-06 10:17:08', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('858613c6-b0b0-46f1-a307-c1eb89c9e588', 'GFCA MA IBM Storwize 5010E_GFCA_Site_2Years_END30Apr2027', '2025-05-01', '2027-04-30', 'ชนะ (Win)', '', '', '2025-03-19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 380400.00, 407028.00, 266400.00, 285048.00, 114000.00, 29.97, 380400.00, 266400.00, 114000.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-03-19 09:53:39', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-19 09:53:39', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('8b9ac1ee-fee4-4bb4-be6d-aabe610f27aa', 'MA DLD NSW3', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 500000.00, 535000.00, 204160.00, 218451.20, 295840.00, 59.17, 500000.00, 204160.00, 295840.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:39:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'ระบบรักษาความปลอดภัยประตูทางเข้า-ออก (มหาวิทยาลัยวลัยลักษณ์)', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', '', '2025-01-16', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 468470.00, 501262.90, 311000.00, 332770.00, 157470.00, 33.61, 0.00, 0.00, 0.00, NULL, '2025-01-16 11:03:50', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:31:16', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('8f307551-8e39-40f6-a66d-ee1ea2c6d7e1', 'ค่าเช่าเครื่องเงินไชโย (Project ทันใจ) 1721 Set', '2024-01-01', '2024-12-31', 'ชนะ (Win)', '', '', '2024-01-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 3396964.49, 3634752.00, 2479784.07, 2653368.96, 917180.42, 27.00, 3396964.49, 2479784.07, 917180.42, '6b3ba15b-ee6d-41ab-a543-d345e9f62259', '2024-11-11 08:53:21', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-12-11 13:14:59', NULL, '1d285bc6-cc8c-47f7-900e-bf84c92f12ad', 7.00),
('8f6e2e97-d5af-4515-b4e4-60d60a6939e8', 'ค่าบริการเช่าใช้ระบบดูแลเฝ้าระวังการล้มในผู้สูงอายุ Fall Detection and emergency monitoring สามารถรับส่งเหตุฉุกเฉินได้ระหว่างหน่วยงานหลักและหน่วยงานฉุกเฉินย่อยได้', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-11-28', '3', 1239359.81, 1326115.00, 707289.72, 756800.00, 532070.09, 42.93, 123935.98, 70728.97, 53207.01, '9a8307fa-375b-47c3-b09d-2f7ca12f0c02', '2024-12-02 14:51:50', '3', '2024-12-11 13:14:59', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('9057cfe3-9c6d-424d-9044-ff147aa46aab', 'ค่าบริการเช่าระบบ Kin-yoo-dee Healthcare Platform หรือ แพลตฟอร์มสำหรับดูแลสุขภาพดูแลกลุ่มเสี่ยงกลุ่มป่วยด้วยโรคเบาหวานและความดันโลหิตสูง และผู้สูงอายุทางไกล', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-11-28', '3', 545424.30, 583604.00, 381121.50, 407800.00, 164302.80, 30.12, 54542.43, 38112.15, 16430.28, '9a8307fa-375b-47c3-b09d-2f7ca12f0c02', '2024-12-02 14:53:09', '3', '2024-12-11 13:14:59', NULL, '3224e7a4-44ee-40ad-a6ac-22305c2b01eb', 7.00),
('913fb068-16a4-4e89-926e-488a27430a6e', 'SC Polymer Solar - PO-2024080100001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024080100001', '', '2024-08-01', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 56650.00, 60615.50, 52034.00, 55676.38, 4616.00, 8.15, 56650.00, 52034.00, 4616.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 07:56:40', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('918903af-e6df-4694-82a3-1cea8dda06e0', 'Server Installation: Faculty of Dentistry Chulalongkorn University', '2025-01-06', '2025-01-07', 'ชนะ (Win)', '', '', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 7000.00, 7490.00, 0.93, 1.00, 6999.07, 99.99, 7000.00, 0.93, 6999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-06 09:40:09', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 09:40:09', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('96f5ad4d-3a2d-4f3d-a909-9c74eaf3df55', 'MA K8S Honda Leasing', '2025-01-01', '2025-12-31', 'ชนะ (Win)', '', '', '2024-10-28', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 95000.00, 101650.00, 1.00, 1.07, 94999.00, 100.00, 95000.00, 1.00, 94999.00, 'f004cbe4-f666-4de7-8e85-7f940b6d8393', '2024-10-31 22:32:22', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-01-16 07:21:07', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('98c33fac-85b6-42dd-a794-2767f03300eb', 'โครงการดูแลสุขภาพของคนในชุมชน ผ่านระบบดูแลผู้สูงอายุด้วยแพลตฟอร์มดิจิตอลและอุปกรณ์ตรวจจับการล้ม แบบอัตโนมัติ เทศบาลนครระยอง', '2025-05-01', '2026-04-30', 'ชนะ (Win)', '', 'เช่าใช้อุปกรณ์พร้อมระบบ จำนวน 30 เครื่อง ระยะเวลา 12 เดือน', '2025-01-01', '3', 320560.75, 343000.00, 266816.00, 285493.12, 53744.75, 16.77, 320560.75, 266816.00, 53744.75, '677f5f38-3f7f-4ca8-b9d6-e4b60f7f241a', '2025-04-09 07:05:39', '3', '2025-04-09 07:05:39', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('a2476611-d200-4882-93b6-a48caab4900e', 'OBEC Infrastructure 77M', '2024-08-01', '2024-10-01', 'ชนะ (Win)', '', '', '2024-07-17', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 1490000.00, 1594300.00, 0.93, 1.00, 1489999.07, 100.00, 1490000.00, 0.93, 1489999.07, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 21:15:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'df374787-e96c-4d3c-8089-3867edd96cf4', 7.00),
('a3fa105e-b258-474a-87e5-e39272e3f127', 'โครงการจัดทำพื้นที่สำหรับจัดเก็บเอกสารและอุปกรณ์ของสำนักยุทธศาสตร์และงบประมาณ', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 2411214.95, 2580000.00, 0.00, 0.00, 0.00, 0.00, 1205607.48, 0.00, 1205607.48, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:15:04', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('a458ea0c-327b-4f5a-8454-63352cefac85', 'SC Polymer Solar - PO-2024102400001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024102400001', '', '2024-10-24', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 152210.00, 162864.70, 140000.00, 149800.00, 12210.00, 8.02, 152210.00, 140000.00, 12210.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 10:21:05', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('a49f69a2-6a70-4a23-98fa-cb91496c5b7a', 'ซื้อโครงการติดตั้งระบบตรวจจับและวิเคราะห์ป้ายทะเบียนรถอัตโนมัติ บริเวณพื้นที่ หมู่ที่ 2 หมู่ที่ 3, 4, 5, 6, 7, 8(งาน LPR)', '2024-07-25', '2024-12-22', 'ชนะ (Win)', '24/2567', '', '2024-07-25', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 4578598.13, 4899100.00, 3782677.00, 4047464.39, 795921.13, 17.38, 4578598.13, 3782677.00, 795921.13, 'b26996d4-08c7-4365-96fe-ea74a40aced8', '2024-11-06 04:44:14', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('a4a2747c-5e78-4196-85d6-22603ccb03b4', 'Project imedisyncTH', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-11-27', '3', 2144300.00, 2294401.00, 1401869.16, 1500000.00, 742430.84, 34.62, 214430.00, 140186.92, 74243.08, '0a462754-178e-4f0c-a510-d9dd40db6490', '2024-12-02 13:57:44', '3', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('a5addc9d-b9be-41a0-b9ae-aae652e47826', 'BSP Hayashi Telempu Replacement Switch', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-02-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 25000.00, 26750.00, 0.93, 1.00, 24999.07, 100.00, 25000.00, 0.93, 24999.07, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-02-06 09:30:57', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-02-06 09:30:57', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('a5e4b1fb-d1da-4594-9ffd-d28817f252a2', 'โรงฆ่าสัตว์ กรมปศุสัตว์', '2025-03-04', '2025-07-04', 'ชนะ (Win)', '', 'Nutanix, H3C Switch, Fortigate, SQL Server, i-Net Clear Report Plus, WIndows Server 2025', '2025-01-29', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 3408000.00, 3646560.00, 2553552.68, 2732301.37, 854447.32, 25.07, 3408000.00, 2553552.68, 854447.32, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-03-13 09:38:01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-03-13 09:41:43', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('a636ae0f-66d7-4bbb-98a1-91749eb59211', 'SC Polymer Solar - PO-2024081900002', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024081900002', '', '2024-08-19', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 254320.00, 272122.40, 233648.80, 250004.22, 20671.20, 8.13, 254320.00, 233648.80, 20671.20, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:48:15', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('a8634a06-fac7-43c5-bae1-f1b24d7509aa', 'e-Movement DLD', '2024-12-23', '2025-03-31', 'ใบเสนอราคา (Quotation)', '', 'Nutanix HCI, Network, Microsoft License', '2024-10-15', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 6488400.00, 6942588.00, 4988707.65, 5337917.19, 1499692.35, 23.11, 648840.00, 498870.77, 149969.23, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:28:46', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('a8f71376-2433-4415-8538-bfffff67dbba', 'รับจ้างบำรุงรักษาเครื่องปรับสมุดเงินฝากอัตโนมัติ พร้อม Software ยี่ห้อ Hitachi รุ่น BH180 จำนวน 291 เครื่อง และระบบสนับสนุน (Server) จำนวน 2 เครื่อง', '2025-01-01', '2025-12-31', 'ชนะ (Win)', 'พณ.พ.03-240/2567', '', '2024-11-28', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', 3898785.05, 4171700.00, 1635514.02, 1750000.00, 2263271.03, 58.05, 3898785.05, 1635514.02, 2263271.03, 'cb8e3303-3fd7-438c-9c64-07e6c80e012f', '2025-01-06 06:27:17', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 06:35:36', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('ac62d191-fbb0-4592-b287-014ed16e422d', 'Toner for ML-3710ND  + Toner for ML-4020+ Ribbon Hitachi', '2024-01-01', '2024-12-31', 'ชนะ (Win)', '', '', '2024-01-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 18583317.76, 19884150.00, 14123321.50, 15111954.00, 4459996.26, 24.00, 18583317.76, 14123321.50, 4459996.26, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2024-11-11 08:43:18', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-12-11 13:14:59', NULL, '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'งานจ้างเหมาติดตั้งระบบป้องกันอัคคีภัยเพื่อความปลอดภัยของโรงเรียนในสังกัดกรุงเทพมหานคร (58 โรงเรียน)', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'Project-co: พี่ซีน\r\nDev Internal: พี่ขวัญ\r\nUX/UI: พี่แอมป์', '2025-01-16', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 1652000.00, 1767640.00, 0.93, 1.00, 1651999.07, 100.00, 165200.00, 0.09, 165199.91, '0968cd06-9d79-4933-8de8-399cb9ac5868', '2025-01-16 08:47:14', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 09:15:40', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '0eb7a552-9888-4541-a43d-a6fa5b143dbc', 7.00),
('ad862d94-87fb-4be9-b37a-5ac08b2b8b7f', 'โครงการปรับปรุงประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิด บริเวณชายหาดพัทยา', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 5878504.67, 6290000.00, 4000000.00, 4280000.00, 1878504.67, 31.96, 0.00, 0.00, 0.00, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:20:57', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('adc5c8b8-68bf-420f-b240-0a4263b2d7b6', 'SC Polymer Solar - PO-2024102800001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024102800001', '', '2024-10-28', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 14980.00, 16028.60, 13780.00, 14744.60, 1200.00, 8.01, 14980.00, 13780.00, 1200.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 10:22:42', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('ae8cd8e6-3101-4adf-a234-5d0fe550230b', 'โครงการปรับปรุงและเพิ่มประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิดในพื้นที่ชุมชนเมืองพัทยา PH1', '2024-10-28', '2025-04-26', 'ชนะ (Win)', '42/2568', '', '2024-10-01', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 12803738.32, 13700000.00, 10250000.00, 10967500.00, 2553738.32, 19.95, 12803738.32, 10250000.00, 2553738.32, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 07:58:00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('aed4f594-e9f4-4ec3-844e-c0e35af9ec6f', 'BSP NIDEC Shibaura Wireless Access Point Onsite Check Problem', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-03-18', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 34000.00, 36380.00, 6880.00, 7361.60, 27120.00, 79.76, 34000.00, 6880.00, 27120.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-03-18 09:39:46', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-18 09:39:46', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('b2fbde7d-175a-4822-a719-495b57d4b9c0', 'MA DLD e-Movement', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-09', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 450000.00, 481500.00, 205760.00, 220163.20, 244240.00, 54.28, 450000.00, 205760.00, 244240.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:37:51', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('b4d8b6c9-1eb5-46ab-a443-68e24357c990', 'SC Polymer Solar - PO-2024071100001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024071100001', '', '2024-07-11', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 123000.00, 131610.00, 113000.00, 120910.00, 10000.00, 8.13, 123000.00, 113000.00, 10000.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-04 02:02:01', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('b659bc53-f12a-4a5c-9ab9-905939c9fb2e', 'Network สกบ.', '2024-10-07', '2024-12-31', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 740000.00, 791800.00, 0.93, 1.00, 739999.07, 100.00, 740000.00, 0.93, 739999.07, 'cea804cd-55ab-4a3f-b9ff-a942547402a7', '2024-10-31 21:24:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('b70608c1-6f57-4abd-bce0-9260962b0bb9', 'โครงการระบบแพลตฟอร์มวิเคราะห์ข้อมูลและปัญญาประดิษฐ์ในการบริการดูแลการใช้ชีวิตและดูแล สุขภาพระยะยาวสำหรับผู้สูงอายุ ในพื้นที่เทศบาลตำบลทับมา', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '2024-12-02', '3', 747663.55, 800000.00, 560747.66, 600000.00, 186915.89, 25.00, 373831.78, 280373.83, 93457.95, '2d4610f7-471d-42c1-a193-d79ac4eb24e8', '2024-12-02 14:59:29', '3', '2024-12-11 13:14:59', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('b922d9a1-08be-4ce3-8402-e1b1113ae430', 'จัดซื้อกล้องวงจรปิดพร้อมติดตั้ง 31 จุด', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-09-17', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 1482925.00, 1586729.75, 510588.00, 546329.16, 972337.00, 65.57, 148292.50, 51058.80, 97233.70, 'c2968a16-8dea-4f07-ab94-c7d2197562fa', '2024-12-03 01:18:37', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00);
INSERT INTO `projects` (`project_id`, `project_name`, `start_date`, `end_date`, `status`, `contract_no`, `remark`, `sales_date`, `seller`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `product_id`, `vat`) VALUES
('ba49000b-3509-4377-8d0f-456286a45e5f', 'โครงการพัฒนาระบบบริหารงานบุคคลดิจิทัล (D-HR)', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 8177570.09, 8750000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-27 08:16:52', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('bacb3b44-beee-4cb6-9f37-dbbed4ecf8b0', 'SC Polymer Solar - PO-2024102500001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024102500001', '', '2024-10-25', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 29900.00, 31993.00, 27500.00, 29425.00, 2400.00, 8.03, 29900.00, 27500.00, 2400.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 10:24:47', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('bad1c47d-0180-44ef-89eb-b7e853877c6b', 'e-Payment DLD', '2024-10-01', '2025-01-28', 'ชนะ (Win)', '', 'Nutanix HCI, Network, Microsoft License', '2024-09-02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 9840000.00, 10528800.00, 8360994.85, 8946264.49, 1479005.15, 15.03, 9840000.00, 8360994.85, 1479005.15, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 21:46:55', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('bc89294a-414c-469f-ba0d-26aaa1ba1ae7', 'Magnetic Stripe', '2024-09-01', '2024-11-19', 'ชนะ (Win)', '', '', '2024-09-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 7401869.16, 7920000.00, 6282106.54, 6721854.00, 1119762.62, 15.13, 7401869.16, 6282106.54, 1119762.62, 'f5489b6a-fd5b-4896-b655-761768e44b8f', '2024-11-11 08:59:40', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-12-11 13:14:59', NULL, '3bf8bc62-f878-4fd9-9bee-2a6917190458', 7.00),
('bdb816d7-49d1-4fae-881f-f6ac087c1bdc', 'MA e-Library กนอ.', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', 'เช่าใช้ Cloud 1 ปี', '2024-09-02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 84000.00, 89880.00, 20954.88, 22421.72, 63045.12, 75.05, 84000.00, 20954.88, 63045.12, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:06:00', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('bded5dd9-9eff-4685-89cf-962c1953e0ea', 'Datacenter BK01 -บางพลี-', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'End user : Mr. CHAWAPAT PRASERTTONGSUK  chawapatp@wtpthailand.com, www.wtpartnership.com\r\nProject lead : ดิว Axis\r\nDistributor : Ying Bacom', '0000-00-00', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 6875335.00, 7356608.45, 5718540.00, 6118837.80, 1156795.00, 16.83, 687533.50, 571854.00, 115679.50, '350429f1-d84a-4cec-8c28-d1a2ce9c4763', '2025-03-17 03:26:27', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-17 03:26:27', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('c1eeffcd-ceca-46dc-9b09-dae4d6d00091', 'งานติดตั้งสายสัญญาณคอมพิวเตอร์ และย้ายห้องควบคุมระบบคอมพิวเตอร์', '2024-07-01', '2024-12-28', 'ชนะ (Win)', '105/2567', '', '2024-07-01', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 4635514.02, 4960000.00, 2921450.00, 3125951.50, 1714064.02, 36.98, 4635514.02, 2921450.00, 1714064.02, '1fb0fb81-4482-438a-ab66-5472c52bf9e4', '2024-11-04 03:42:21', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('c25a8b9b-64ad-4b8a-b79a-79e97922eb40', 'เช่าใช้ชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุภายในบ้านและภายนอกบ้าน อบจ.ชลบุรี', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-02-03', '3', 4613319.63, 4936252.00, 3686514.00, 3944569.98, 926805.63, 20.09, 461331.96, 368651.40, 92680.56, '02e18007-e4e7-4fb7-a2c2-c924ece0a966', '2025-04-09 06:03:50', '3', '2025-04-09 06:03:50', NULL, '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('c3cda5cd-242f-4a35-8364-1304577a7d28', 'อุปกรณ์ต่อพ่วง', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '0000-00-00', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 2803738.32, 3000000.00, 2056074.77, 2200000.00, 747663.55, 26.67, 280373.83, 205607.48, 74766.36, 'cbf32bae-0896-4e5b-ab8e-f4fdca7916f8', '2024-11-25 01:19:17', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('c76bdba6-c78d-4071-8371-bde13f3a3c67', 'SC Polymer Solar - PO-2024082000002', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024082000002', '', '2024-08-20', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 294170.00, 314761.90, 270514.12, 289450.11, 23655.88, 8.04, 294170.00, 270514.12, 23655.88, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 09:53:02', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('c9607961-6240-4066-965f-5a171dcee526', 'MA BAAC PromptPay ปีที่ 5', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', 'ปีสุดท้าย', '2024-09-10', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 116000.00, 124120.00, 1.00, 1.07, 115999.00, 100.00, 116000.00, 1.00, 115999.00, 'f313a7ba-64ae-4d61-af99-f493a98039b2', '2024-10-31 21:57:08', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('c98d7d90-9fa7-4093-9f5a-39938d19e142', 'โครงการพัฒนาศักยภาพด้านความปลอดภัยพื้นที่การจราจรบริเวณเทศบาลเมืองป่าตอง ระยะที่  1', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 4485981.31, 4800000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'ad0ee715-c3b7-4df0-b097-6d0bf1565fb0', '2024-11-27 08:13:09', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('cbd18a9f-2b3a-49f2-8710-5134fa1d8069', 'ระบบวิเคราะห์ภาพจากกล้อง Thermal ในนิคมอุตสาหกรรม', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'Project-co: พี่ซีน\r\nUX-UI: พี่แอมป์\r\nAI: VAM Stack\r\nDev Internal: พี่ขวัญ', '2025-01-15', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 1200000.00, 1284000.00, 280373.83, 300000.00, 919626.17, 76.64, 1200000.00, 300000.00, 900000.00, 'ae83116d-3c1a-41f7-a066-3e99373b2b44', '2025-01-16 07:24:48', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 08:48:25', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '0eb7a552-9888-4541-a43d-a6fa5b143dbc', 7.00),
('cce12004-7e3d-4a8e-aa44-6e2a07bb9a57', 'BSP NIDEC Wireless Access Point Configuration 9 Sets', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '3 Sets + 6 Sets', '2025-03-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 39000.00, 41730.00, 1.00, 1.07, 38999.00, 100.00, 39000.00, 1.00, 38999.00, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-03-06 04:42:45', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-06 04:42:45', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('cd66bfe3-0067-475b-b745-b35d2da71455', 'Thai-Otsuka DR Server Deployment', '2025-01-01', '2025-01-31', 'ชนะ (Win)', '', 'Recheck DR Server and Replication Policy\r\nHardware Preparation IP, Hostname, DNS, Raid Configuration\r\nDeployment vSphere esxi 6.0/6.5/6.7\r\nDeployment vSphere vCenter VCSA 6.0/6.5/6.7\r\nCreate new Virtual Machine Guest for Veeam Backup and Replication\r\nConfiguration Veeam Replicate for SAP and FS\r\nVerify Veeam Replicate job', '2025-01-06', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 20000.00, 21400.00, 0.93, 1.00, 19999.07, 100.00, 20000.00, 0.93, 19999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-06 09:43:11', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-06 09:43:54', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('cf4ae75b-1fe6-4e4f-b96d-d49fefb04ffd', 'โครงการปรับปรุงและเพิ่มประสิทธิภาพระบบเฝ้าระวังภัยด้วยกล้องโทรทัศน์วงจรปิดในพื้นที่ชุมชนเมืองพัทยา PH2', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '2024-11-27', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 11962616.82, 12800000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:18:43', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('cf907b6e-e0d5-4aa6-b2b6-006e2cc90a94', 'BSP NEC MA Service Onsite support 8x5 NBD (1Year) 2025', '2025-02-01', '2026-01-31', 'ชนะ (Win)', 'SE20241203-001', 'PO: PO2025005', '2025-01-15', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 98000.00, 104860.00, 0.93, 1.00, 97999.07, 100.00, 98000.00, 0.93, 97999.07, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-01-15 07:37:10', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-15 07:37:51', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('d0c02f40-fc72-4219-b7dc-b6c77f6a8d5a', 'โครงการปรับปรุงและเพิ่มประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิดภายในอาคารของหน่วยงานสังกัดเมืองพัทยา', '0000-00-00', '0000-00-00', 'รอการพิจารณา (On Hold)', '', '', '0000-00-00', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 13925233.64, 14900000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-27 08:19:42', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('d1765bcd-968f-4203-bc56-a62447106389', 'SC Polymer Solar - PO-2024071700001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024071700001', '', '2024-07-17', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 172335.00, 184398.45, 158290.50, 169370.84, 14044.50, 8.15, 172335.00, 158290.50, 14044.50, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-02 07:05:33', '5', '2024-12-11 13:14:59', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('d2a7935d-2f11-40f2-9c1c-89088ee9e180', 'GFCA GeoTrust DV SSL Wildcard *.gfca.com', '2025-01-13', '2026-01-13', 'ชนะ (Win)', 'SE20250113-001', '', '2025-01-13', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 13500.00, 14445.00, 12000.00, 12840.00, 1500.00, 11.11, 13500.00, 12000.00, 1500.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-01-13 05:03:48', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-13 05:04:38', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('d351ce78-b7c0-4d0f-8ed3-d47104931534', 'เครื่องลงเวลาด้วยใบหน้าระยะไกล Zkteco MB40 VL', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-09-24', '3', 92000.00, 98440.00, 57009.35, 61000.00, 34990.65, 38.03, 9200.00, 5700.94, 3499.06, 'cdd15d78-73d7-41d6-9fad-dfd0da61a1a9', '2024-12-02 13:40:51', '3', '2024-12-11 13:14:59', '3', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('d466792e-3411-4a51-83ca-287e100c3108', 'MA DLD LIMs', '2025-03-14', '2025-05-31', 'ชนะ (Win)', '', 'MA อุปกรณ์โครงการ LIMs DLD', '2025-03-03', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 150025.00, 160526.75, 71020.00, 75991.40, 79005.00, 52.66, 150025.00, 71020.00, 79005.00, '642afc1e-c8d5-42f3-a685-aa899e78be1e', '2025-03-13 09:46:30', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-03-13 09:48:16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('dbe7c117-97cb-4b00-a592-a9c8909b5b53', 'SC Polymer Solar - PO-2024071200001', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024071200001', '', '2024-07-12', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 232727.00, 249017.89, 213979.88, 228958.47, 18747.12, 8.06, 232727.00, 213979.88, 18747.12, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-04 02:35:10', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('dd898ff9-d819-4695-b0b7-6fb4dfa441ce', 'ระบบบริหารจัดการกล้องและ Multi Media ในสวนสัตว์ (Zoo Point)', '0000-00-00', '0000-00-00', 'นำเสนอโครงการ (Presentations)', '', 'Project-co: พี่ซีน\r\nDev Internal: พี่ขวัญ\r\nUX/UI: พี่แอมป์\r\nงานติดตั้งกล้อง: พี่แจ๊ค', '2025-01-16', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 21495327.10, 23000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '4f049ce6-9488-4664-865f-5d9729659ee2', '2025-01-16 09:12:21', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:28:20', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('de838089-b8cd-4964-a05d-c49c19422cb1', 'งานจ้างเหมาเดินสายระบบไฟฟ้าเข้าเครื่องสำรองไฟ', '2024-08-20', '2024-11-18', 'ชนะ (Win)', '203/2567', '', '2024-08-20', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 38258.36, 40936.44, 26762.00, 28635.34, 11496.36, 30.05, 38258.36, 26762.00, 11496.36, 'd4efc031-32d4-487f-87ff-69afe9f948e4', '2024-11-06 05:00:33', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('de8c069d-6370-49de-aafc-20b6cde1025b', 'Samsung LaserJet Toner SL-4020ND  (SU894A) - 16 box', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'IV671106003', '', '2024-11-06', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 38130.84, 40800.00, 29515.20, 31581.26, 8615.64, 22.59, 38130.84, 29515.20, 8615.64, 'df6e7ebd-77f2-49e4-bcdf-04c71608005f', '2024-12-09 01:47:45', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('deca5fd5-88c4-4a82-8d93-538f735cffe4', 'งานปรับปรุงและเพิ่มประสิทธิภาพระบบกล้องโทรทัศน์วงจรปิดในพื้นที่ชุมชนเมืองพัทยา', '2024-10-28', '2025-04-26', 'ชนะ (Win)', '42/2568', '', '2024-10-28', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 12803738.32, 13700000.00, 0.00, 0.00, 0.00, 0.00, 12803738.32, 0.00, 12803738.32, 'fda15ece-1a00-4583-b354-cb5f3c01bb23', '2024-11-04 04:07:32', 'e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', '2024-12-11 13:14:59', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('e261d4de-a628-45bd-b28e-7ae8a18a9b66', 'SC Polymer Solar - PO-2024102400002', '0000-00-00', '0000-00-00', 'ชนะ (Win)', 'PO-2024102400002', '', '2024-10-24', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 66960.00, 71647.20, 61600.00, 65912.00, 5360.00, 8.00, 66960.00, 61600.00, 5360.00, '65b9a9b5-5272-4b9d-a02f-1b4c85460069', '2024-12-06 10:19:39', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('e33fc204-3396-4c06-807d-2512218b8fc2', 'MA e-Courtroom ประจำปี 68', '2024-12-01', '2025-09-30', 'แพ้ (Loss)', '', 'แพ้เนื่องจากเจ้าที่เสนอมี SME', '2024-11-13', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 709345.79, 759000.00, 650000.00, 695500.00, 59345.79, 8.37, 0.00, 0.00, 0.00, NULL, '2024-11-11 08:32:52', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-11 13:14:59', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('e54e5686-6be2-44f0-b7a1-3689614b3244', 'ค่า Toner (Project ทันใจ)', '2024-01-01', '2024-11-11', 'ชนะ (Win)', '', '', '2024-01-01', '3140fdaf-5103-4423-bf87-11b7c1153416', 17971028.04, 19229000.00, 16028037.38, 17150000.00, 1942990.66, 10.81, 17971028.04, 16028037.38, 1942990.66, '6b3ba15b-ee6d-41ab-a543-d345e9f62259', '2024-11-11 08:51:04', '3140fdaf-5103-4423-bf87-11b7c1153416', '2024-12-11 13:14:59', NULL, '19747bf2-8f2d-47db-a2e8-4fca20843812', 7.00),
('e565cdde-1422-4d12-8127-a067e5b01fe5', 'Universal Rice Network Improvement (URC)', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2025-01-31', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 293000.00, 313510.00, 0.93, 1.00, 292999.07, 100.00, 29300.00, 0.09, 29299.91, '895e71fc-991e-4b42-9803-4bcafdb03023', '2025-01-31 06:23:03', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-31 06:23:03', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('e5ff0b66-f1db-4f84-befe-73d06829d4ec', 'MA OBEC Mail', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 228000.00, 243960.00, 0.00, 0.00, 227999.07, 100.00, 228000.00, 0.00, 228000.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:11:16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-16 09:24:50', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('e6184225-dba7-4cc7-90d5-af9584109353', 'โครงการติดตั้งกล้องโทรทัศน์วงจรปิด', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', '', '0000-00-00', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 3247312.75, 3474624.64, 2862525.00, 3062901.75, 384787.75, 11.85, 1623656.38, 1431262.50, 192393.88, 'fc372e65-cca3-4c7c-b580-c689ef2d0798', '2024-12-03 02:43:51', 'c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', '2024-12-11 13:14:59', NULL, '581f6ca7-8e1e-447a-9dae-680755c4fd29', 7.00),
('e718b8d8-ea0c-4e36-821c-14e1da8fa258', 'ระบบแลกบัตรเข้าออกอาคาร', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', 'รอคุยเรื่องผู้เสนอต้องเป็น SME หรือเปล่า', '0000-00-00', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 461682.24, 494000.00, 383177.57, 410000.00, 78504.67, 17.00, 46168.22, 38317.76, 7850.47, '360a7a11-6bcd-4301-8156-b4d11ebd6794', '2024-11-28 08:50:22', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '2024-12-16 01:57:31', '8ae1a02d-32c2-4469-ba98-818dfc76dcdb', '075afde8-650f-4d75-b73d-f41242854682', 7.00),
('e75996eb-c5ba-4dfe-b36e-5e58ba334bb6', 'Mindss Thai-Otsuka Onsite Recheck Server VMware pink screen', '0000-00-00', '0000-00-00', 'ชนะ (Win)', '', '', '2025-01-31', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 5000.00, 5350.00, 0.93, 1.00, 4999.07, 99.98, 5000.00, 0.93, 4999.07, 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', '2025-01-31 06:17:12', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-31 06:17:12', NULL, '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('e7c861a2-b027-4992-a280-c8dc6a180784', 'MA BAAC ICAS ปีที่ 4', '2024-03-01', '2025-02-28', 'ชนะ (Win)', '', '', '2024-11-01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 50000.00, 53500.00, 1.00, 1.07, 49999.00, 100.00, 50000.00, 1.00, 49999.00, 'f313a7ba-64ae-4d61-af99-f493a98039b2', '2024-10-31 22:00:44', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('e96e8f2a-5a2c-48fc-bae1-d19c30217990', 'BSP NEC vSphere and vCenter upgrade to 8.0 Installation Service (Non-Business Day)', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', 'SE20241203-002', 'NEC vSphere and vCenter upgrade to 8.0 Installation Service (Non-Business Day)', '2025-01-15', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 58000.00, 62060.00, 0.93, 1.00, 57999.07, 100.00, 5800.00, 0.09, 5799.91, 'c918919d-7d14-4f42-97a8-3357016c382a', '2025-01-15 07:40:07', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-01-15 07:40:31', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '162fd42b-855e-40ac-8696-0d0535fbe2b1', 7.00),
('ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'โครงการจ้างระบบแพลตฟอร์มวิเคราะห์ข้อมูล ปัญญาประดิษฐ์ในการบริการดูแลการใช้ชีวิตและดูแลสุขภาพระยะยาวสำหรับผู้สูงอายุและผู้ที่มีภาวะพึ่งพิง', '2024-09-27', '2025-09-29', 'ชนะ (Win)', '๖/๒๕๖๗', '', '2024-09-06', '3', 266822.43, 285500.00, 198336.00, 212219.52, 68486.43, 25.67, 266822.43, 198336.00, 68486.43, '5e2a838a-110f-48bc-9518-f01a7066955b', '2024-10-15 21:17:03', '3', '2024-12-11 13:14:59', '3', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('eab3d4f8-1ab7-4654-b1c3-d1ddce015b5b', 'MA DLD Datalake', '2024-11-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-10-01', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 816400.00, 873548.00, 278320.00, 297802.40, 538080.00, 65.91, 816400.00, 278320.00, 538080.00, 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', '2024-10-31 22:04:08', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-16 06:46:16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('ebb8ead8-1089-426c-9525-9a352b756974', 'GFCA Project Wireless Access Point Cisco Catalyst 9115AX MA 1Y5M_8x5NBD_AAI-สมุทรสงคราม', '2025-04-19', '2026-08-31', 'ชนะ (Win)', '', '', '2025-02-28', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 13000.00, 13910.00, 7760.00, 8303.20, 5240.00, 40.31, 13000.00, 7760.00, 5240.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-02-28 08:44:58', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-19 09:32:50', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('ecb183b4-4add-4215-8b4b-f7f60e544274', 'บริการเช่าใช้ระบบ AI Platform พร้อม Hardware สำหรับงาน Showroom', '2025-03-14', '2025-04-15', 'ชนะ (Win)', '', '', '2025-02-11', '3', 63200.00, 67624.00, 47100.00, 50397.00, 16100.00, 25.47, 63200.00, 47100.00, 16100.00, 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', '2025-04-09 06:16:11', '3', '2025-04-09 06:21:47', '3', 'c21eef19-0bab-4c25-89ff-c5dc6ad1f3b9', 7.00),
('ecf41d44-20a8-4185-bc52-deb21201033d', 'จัดซื้อครุภัณฑ์คอมพิวเตอร์ จำนวน 45 เครื่อง', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '0000-00-00', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 925200.00, 989964.00, 841121.50, 900000.00, 84078.50, 9.09, 92520.00, 84112.15, 8407.85, '81b62776-9408-4a36-af8e-45799f86883d', '2024-11-25 01:16:37', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', '2024-12-11 13:14:59', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('eed702ae-86ea-45b3-8688-33fb3bda90e0', 'Firewall ฌาปนกิจสงเคราะห์ สตช.', '2025-01-17', '2026-01-18', 'ชนะ (Win)', '', 'Fortigate 100F', '2025-01-06', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 180000.00, 192600.00, 120000.00, 128400.00, 60000.00, 33.33, 180000.00, 120000.00, 60000.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2025-03-13 09:52:52', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2025-03-13 09:52:52', NULL, 'de486d4d-c877-40a8-a113-d92b2dfcbda5', 7.00),
('f40d86bd-b4ed-4c79-a96a-7aafe7283719', 'งานพิพิธภัณฑ์ จ.ระนอง', '0000-00-00', '0000-00-00', 'ยื่นประมูล (Bidding)', '', 'End user : งานพิพิธภัณฑ์ จ.ระนอง\r\nเข้างานโดย บริษัท เบคอม อินเตอร์เน็ทเวอร์ค จำกัด, Anyapat  Wannakunlapat  (Ying)', '0000-00-00', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 376547.50, 402905.83, 289007.01, 309237.50, 87540.49, 23.25, 37654.75, 28900.70, 8754.05, '9f005cab-6ce1-4813-bafe-95be81d93b1d', '2025-03-17 03:10:54', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', '2025-03-17 03:10:54', NULL, '3431f4cb-f892-4e08-a9af-240a743ebc25', 7.00),
('f50eb76c-0230-4f71-b47f-c2e60d652ce1', 'MA OBEC DataCenter', '2024-10-01', '2025-09-30', 'ชนะ (Win)', '', '', '2024-09-16', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', 3080000.00, 3295600.00, 1.00, 1.07, 3079999.00, 100.00, 3080000.00, 1.00, 3079999.00, '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', '2024-10-31 22:09:02', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', '2024-12-11 13:14:59', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00),
('f655c0f1-25e0-4c51-bf01-4f99b4121ba7', 'ค่าเช่าชุดเฝ้าระวังเหตุฉุกเฉินการล้มในผู้สูงอายุภายในบ้านและภายนอกบ้าน ระยะเวลา 12 เดือน', '0000-00-00', '0000-00-00', 'ใบเสนอราคา (Quotation)', '', '', '2024-10-31', '5', 106800.00, 114276.00, 77570.09, 83000.00, 29229.91, 27.37, 10680.00, 7757.01, 2922.99, '690cfd6a-0270-4b22-8d1f-de1f91dda830', '2024-12-02 13:46:02', '3', '2024-12-11 13:14:59', '5', '4c85d842-54f3-4f06-87e6-553f81488234', 7.00),
('fa1a06ff-e3b5-4243-8508-9ef70aa510a3', 'จัดซื้อ Auto Update Passbook พร้อมติดตั้ง จำนวน 794 เครื่อง', '2024-07-27', '2029-07-27', 'ชนะ (Win)', 'POIT66-136', 'Hitachi BH-180AZ+PC+Frame', '2023-12-15', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', 21976635.51, 23515000.00, 16557530.84, 17716558.00, 5419104.67, 24.66, 21976635.51, 16557530.84, 5419104.67, '9392ce88-098b-49a8-8df4-c4882971735e', '2025-01-06 08:38:35', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', '2025-01-06 08:38:35', NULL, '1d4a6419-9576-4284-9fb6-de65e6f4a1bc', 7.00),
('faf652c9-8bd0-4b21-a935-30c904d8650a', 'GFCA MA IBM Server and Storage_END30Apr2027_GFCA_Site_BackupServer_2Years', '2025-05-01', '2027-04-30', 'ชนะ (Win)', '', '', '2025-03-19', '14d9e34c-b691-4ce8-a5ef-929ace71248a', 191000.00, 204370.00, 98400.00, 105288.00, 92600.00, 48.48, 191000.00, 98400.00, 92600.00, '054160f3-f50e-40a2-a45e-569777875172', '2025-03-19 09:51:32', '14d9e34c-b691-4ce8-a5ef-929ace71248a', '2025-03-19 09:51:32', NULL, 'ae10bae3-0b1c-419f-8b21-8c57c607d3de', 7.00);

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
  `unit` varchar(50) DEFAULT NULL,
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

INSERT INTO `project_costs` (`cost_id`, `project_id`, `type`, `part_no`, `description`, `quantity`, `unit`, `price_per_unit`, `cost_per_unit`, `supplier`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('13f9d3e5-aa9c-4fe7-b57f-f7e2eaa3f1d3', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'A', 'Hardware', 'ชุดเฝ้าระวัง', 30, NULL, 2850.00, 1347.00, 'Stock Point ', '2024-11-01 11:14:30', '3', '2024-11-01 11:14:30', NULL),
('1f412777-1029-4ff2-bc88-91e2c8984fe6', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'A', 'Hardware', 'อุปกรณ์ไมโครคอนโทรลเลอร์	', 169, 'ชุด', 5000.00, 3000.00, 'ต่างประเทศ', '2025-01-23 12:30:53', '3', '2025-01-23 12:31:53', '3'),
('34b4f516-8845-4dfa-8c44-fc3b5338a346', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 'B', 'Software', 'License โปรแกรมไมโครคอนโทรลเลอร์	', 169, 'ชุด', 1000.00, 2000.00, 'Dev', '2025-01-23 12:32:38', '3', '2025-01-23 12:32:38', NULL),
('4d850f5e-35ef-442b-be1b-0ebc59c0f8ef', '7c67ce7e-ee05-487f-a763-4627899516bb', 'A', 'Service', 'Care Center 24*7', 1, 'คน', 5999.00, 3000.00, 'Service Teams', '2025-01-08 01:26:16', '5', '2025-01-08 01:27:14', '5'),
('6d35285c-0bd3-4406-9ed2-e5674f8704c0', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Software', '-', 'บริหารจัดการส่วนกลาง', 1, 'License', 100000.00, 0.00, 'Innovation', '2025-01-16 11:16:47', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:16:47', NULL),
('74f0fb16-3d4f-4c40-ba67-2b0608ad70ef', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Software', '-', 'ระบบตรวจจับป้ายทะเบียน LPR', 2, 'License', 35000.00, 30000.00, 'อ.มงคล', '2025-01-16 11:15:04', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:15:04', NULL),
('7ec94428-a477-4a67-901a-099f9a739826', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'A', 'Hardware', 'Sim Internet แบบรายเดือน ระยะเวลา 12 เดือน', 31, NULL, 1366.20, 1188.00, 'AIS', '2024-11-01 11:16:14', '3', '2024-11-01 11:20:20', '3'),
('840e083f-1211-4d8e-9257-8674e29bd538', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Software', '-', 'ระบบตรวจจับใบหน้า IBOC', 2, 'License', 35000.00, 30000.00, 'IBOC', '2025-01-16 11:14:10', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:14:10', NULL),
('84863965-80f0-4ce8-aa08-79b3c876af8a', 'f655c0f1-25e0-4c51-bf01-4f99b4121ba7', 'Service', 'S01', 'Service', 12, NULL, 5000.00, 4000.00, 'Service PIT', '2024-12-03 02:55:33', '5', '2024-12-03 02:55:33', NULL),
('8ea6620f-e60a-4df6-96c1-7a617513dc4d', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Installation', '-', 'ค่าติดตั้งกล้อง', 4, 'Job', 2000.00, 0.00, 'Internal', '2025-01-16 11:18:07', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:18:07', NULL),
('a2357f90-57be-4482-a45a-a83c5176aba4', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Hardware', '-', 'อุปกรณ์ป้องกันไม้หล่น', 2, 'ชุด', 6160.00, 5500.00, 'HIP', '2025-01-16 11:10:23', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:10:23', NULL),
('c757c157-937a-423f-86c3-7b7f612e65e2', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Hardware', '-', 'ตู้ควบคุมทางเข้าออก', 2, 'ชุด', 64500.00, 55000.00, 'HIP', '2025-01-16 11:08:52', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:09:10', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a'),
('d4d45c0b-b3d4-444e-b7b9-679779e4405b', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Hardware', '-', 'ควบคุมระบบไม้กั้น', 1, 'ชุด', 11550.00, 10000.00, 'HIP', '2025-01-16 11:11:34', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:11:34', NULL),
('e975cd23-9e02-4087-8b67-461314a7fe84', '8c11b5f4-daf0-4a3b-829d-f856081e9c97', 'Hardware', '-', 'กล้อง', 4, 'ตัว', 16900.00, 15000.00, 'Hikvision', '2025-01-16 11:12:43', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-01-16 11:12:43', NULL);

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
('7c67ce7e-ee05-487f-a763-4627899516bb', 5999.00, 419.93, 6418.93, 3000.00, 210.00, 3210.00, 3208.93, 49.99, '2025-01-08 01:27:15'),
('8c11b5f4-daf0-4a3b-829d-f856081e9c97', 468470.00, 32792.90, 501262.90, 311000.00, 21770.00, 332770.00, 168492.90, 33.61, '2025-01-16 11:18:07'),
('ad3b9787-d382-4eb0-ac96-d9a3917d5db6', 1014000.00, 70980.00, 1084980.00, 845000.00, 59150.00, 904150.00, 180830.00, 16.67, '2025-01-23 12:32:38'),
('ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 127852.20, 8949.65, 136801.85, 77238.00, 5406.66, 82644.66, 54157.19, 39.59, '2024-11-01 11:20:20'),
('f50eb76c-0230-4f71-b47f-c2e60d652ce1', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2024-12-02 07:19:08'),
('f655c0f1-25e0-4c51-bf01-4f99b4121ba7', 60000.00, 4200.00, 64200.00, 48000.00, 3360.00, 51360.00, 12840.00, 20.00, '2024-12-03 02:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `project_customers`
--

CREATE TABLE `project_customers` (
  `id` char(36) NOT NULL COMMENT 'รหัสความสัมพันธ์ (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `customer_id` char(36) NOT NULL COMMENT 'รหัสลูกค้า (เชื่อมโยงกับตาราง customers)',
  `is_primary` tinyint(1) DEFAULT 0 COMMENT 'สถานะลูกค้าหลักของโครงการ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างความสัมพันธ์'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บความสัมพันธ์ระหว่างโครงการกับลูกค้า';

--
-- Dumping data for table `project_customers`
--

INSERT INTO `project_customers` (`id`, `project_id`, `customer_id`, `is_primary`, `created_at`) VALUES
('01b89e41-733c-495f-930f-cd37446e53e3', '7fc6a707-7a83-4295-a0fc-f32434aeecb0', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-04-09 06:24:22'),
('02505f69-21ad-465a-a564-adbe0626c624', '96f5ad4d-3a2d-4f3d-a909-9c74eaf3df55', 'f004cbe4-f666-4de7-8e85-7f940b6d8393', 1, '2025-01-16 07:21:07'),
('0355659d-3a83-48ba-b9e6-f1fb55e5c5f7', '64e25a53-27be-4e55-8dc8-8a6cdb3b8115', 'a7398772-5d5f-4f09-9eb6-6edf32fb9893', 1, '2025-04-09 05:25:35'),
('055e67d9-fc8e-48ab-a137-117d94a3875b', '4b86e1b4-e921-4ca4-809b-28fc4ef63b07', 'cc80c251-336b-4039-9850-5a042948e8f3', 1, '2025-04-09 05:37:09'),
('064232f7-4d8f-44b5-a69d-4d7ef4c8f965', 'a5addc9d-b9be-41a0-b9ae-aae652e47826', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-02-06 09:30:57'),
('0767e2b1-7426-4c7c-b5a5-87177e41bda8', 'bded5dd9-9eff-4685-89cf-962c1953e0ea', '350429f1-d84a-4cec-8c28-d1a2ce9c4763', 1, '2025-03-17 03:26:27'),
('0aa82182-a9b1-4194-90b0-fdc69f3f5713', 'e565cdde-1422-4d12-8127-a067e5b01fe5', '895e71fc-991e-4b42-9803-4bcafdb03023', 1, '2025-01-31 06:23:03'),
('0acea8d7-fb74-4231-8954-5fad65bec5a2', '5ee574f8-06dc-4c6f-8d61-7fb7c093d010', '2b5c101f-db79-4143-89f9-2b42fbea06bd', 1, '2025-01-28 06:12:18'),
('0c2f4f9d-919d-4376-82fa-12f3f6317217', 'e5ff0b66-f1db-4f84-befe-73d06829d4ec', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2024-12-16 09:24:50'),
('0d886bf1-ed83-4083-9318-bd6121c65031', '0df824e9-139b-4fe0-af22-e2c390df0cc6', '0f80acd4-d034-4175-b501-f879a9e203de', 1, '2025-01-30 07:53:14'),
('0fd45ce8-0bff-4127-97af-41b0b89bd3b4', 'ebb8ead8-1089-426c-9525-9a352b756974', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:32:50'),
('170070bf-ce95-4c38-82bf-6238f1d1eef2', '4821cea3-07a4-4495-a139-8e8d74e26254', 'cea804cd-55ab-4a3f-b9ff-a942547402a7', 1, '2025-01-16 08:49:14'),
('1bd9c25d-bfcb-4297-914d-1807db8e4134', '525b742d-5749-40d2-a148-0290161fd3c3', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-04-09 12:25:18'),
('2d5e271f-f682-4998-9d6b-14166b2ee127', 'cd66bfe3-0067-475b-b745-b35d2da71455', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-06 09:43:54'),
('2ff59fad-ccbe-4ed7-bb60-560bf6092280', '0b8f2e6e-5e6f-42c9-8ab5-aafa5ad065eb', '0968cd06-9d79-4933-8de8-399cb9ac5868', 1, '2025-01-17 11:44:14'),
('305356c2-810d-4416-829b-40234b36b9f3', '2cfe725d-8bfa-4d78-a196-a30881a8eb22', '5aa126c7-c78d-4234-b0f3-45153034626e', 1, '2025-04-09 05:45:25'),
('338b12eb-6c26-436f-9366-d40ced783100', 'eed702ae-86ea-45b3-8688-33fb3bda90e0', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2025-03-13 09:52:52'),
('3cc12fd1-2d33-4cb3-99db-695df6ad35d6', '52d95985-84b0-4d61-8748-b1a76856536f', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-01-20 04:17:13'),
('3cf236b1-a4de-4140-b371-3811b9766981', '72f91cb8-944d-44f5-babc-f4288568c964', '69512d42-1a96-42c4-b9a5-5e188b4ea0c8', 1, '2024-12-16 06:46:49'),
('3db4e67f-d57c-4faf-995c-05a9a5a3433e', '284033fa-5e82-48be-a26e-60f91dd0b65f', '0f80acd4-d034-4175-b501-f879a9e203de', 1, '2025-01-30 07:54:21'),
('41b922d3-3549-4a48-b819-34ee0cd8c058', 'a8f71376-2433-4415-8538-bfffff67dbba', '3ef73d28-72ff-4c90-b04a-693a33baf895', 1, '2025-01-06 06:35:36'),
('47c11a86-7698-4533-8572-4149d5f5ae55', 'faf652c9-8bd0-4b21-a935-30c904d8650a', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:51:32'),
('48b1aabc-4462-4cbc-95e2-18e59a858c70', '98c33fac-85b6-42dd-a794-2767f03300eb', '677f5f38-3f7f-4ca8-b9d6-e4b60f7f241a', 1, '2025-04-09 07:05:39'),
('4d0e3e38-637f-4f53-a6dc-12222c7e1bc0', '4b7ab0ca-b747-482a-a113-03a5891f9aab', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-03-06 04:39:00'),
('5906854a-0947-4cb6-ac06-e7085e9a78d2', 'a5e4b1fb-d1da-4594-9ffd-d28817f252a2', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-03-13 09:41:43'),
('5d5ee192-47a2-4fd5-b03f-b489a5cffbd7', 'e718b8d8-ea0c-4e36-821c-14e1da8fa258', '360a7a11-6bcd-4301-8156-b4d11ebd6794', 1, '2024-12-16 01:57:31'),
('5db76591-6aa3-475b-901e-769ef2d6c356', '74152581-8a94-443f-ad39-140e5f9dc509', 'df6e7ebd-77f2-49e4-bcdf-04c71608005f', 1, '2024-12-11 13:15:13'),
('607d09bb-fb5a-4ae8-beb0-a676ebd9b111', '1103540e-b227-4f59-8729-1b85ecd0d05d', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-06 09:48:27'),
('60ea54f9-e8c9-4979-a105-12c53ece36a5', '49100e5b-82a9-4a11-849b-17e45117adba', '88bc1a3c-f646-4e7a-863d-3424b0fbe1c1', 1, '2025-01-06 06:47:19'),
('632a0873-7548-4157-a200-a5da76b3e2f3', 'dd898ff9-d819-4695-b0b7-6fb4dfa441ce', '4f049ce6-9488-4664-865f-5d9729659ee2', 1, '2025-01-16 11:28:20'),
('63a02795-8dc4-48da-ba48-36ecc084c020', '11bf7c88-a563-4a01-8419-a32500a9194d', '88d465c6-3e16-4c58-a6da-10bce309af89', 1, '2025-03-17 03:51:49'),
('6f0d1420-aedf-43be-a151-3efecbbd45b6', '1cf8da79-24cc-4f87-ad15-bae140ab4e55', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:30:08'),
('6f292c95-5ea1-438f-a136-7cd77fbaebaf', 'eab3d4f8-1ab7-4654-b1c3-d1ddce015b5b', 'dd7d359f-6c63-4c11-80c5-d4dfa7407c92', 1, '2024-12-16 06:46:16'),
('7611c753-5ddb-45c3-97f3-0c64671e6994', '161e830e-355e-4364-acce-405857cf30b9', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-04-09 06:20:27'),
('77347889-af1c-4e14-b0bf-924d782a4b86', 'cce12004-7e3d-4a8e-aa44-6e2a07bb9a57', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-03-06 04:42:45'),
('7757c384-0cae-4336-8ae8-6c3d793b1a5b', '26ffb5a6-2d63-464a-a9db-3e976c2d3893', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:37:47'),
('7765be02-8dee-4831-9876-47f14bbc2836', 'd2a7935d-2f11-40f2-9c1c-89088ee9e180', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-01-13 05:04:38'),
('78d28885-9bdd-4608-a09e-baef274d44fe', '69b3d4b2-f00b-4d75-9379-f17aaf4c2e34', '6e23608d-46bb-4e74-8326-21365397565b', 1, '2025-04-09 12:22:07'),
('7c06dcdc-4764-4a66-81c7-501365608b3b', '7b578d51-8794-46f4-8a3a-f1da2429e855', '360a7a11-6bcd-4301-8156-b4d11ebd6794', 1, '2024-12-16 01:55:56'),
('82a0e59c-1813-4797-bf36-d06f12a497d6', 'fa1a06ff-e3b5-4243-8508-9ef70aa510a3', '9392ce88-098b-49a8-8df4-c4882971735e', 1, '2025-01-06 08:38:35'),
('84382aaa-2e4c-4869-a16c-d062b6493785', '858613c6-b0b0-46f1-a307-c1eb89c9e588', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:53:39'),
('88db516f-ea55-4d88-b57f-2424a4794e9c', '5486e1ad-8bc0-4884-a100-c626c0a2d731', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-02-28 08:42:34'),
('98ec547c-2ffe-43a0-a0a6-1b702a04aa99', 'aed4f594-e9f4-4ec3-844e-c0e35af9ec6f', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-03-18 09:39:46'),
('a2467f23-9afc-47e7-9056-f8180864e5af', '49100e5b-82a9-4a11-849b-17e45117adba', '45af1f14-b041-43b2-b4ff-d93692564a61', 1, '2025-01-06 06:47:19'),
('af483dec-4697-4763-85d2-ab51982c8769', '7c338957-c9a9-4134-b79b-3d131b19dec9', '0f80acd4-d034-4175-b501-f879a9e203de', 1, '2025-03-13 14:51:55'),
('b0ce7682-e6df-4aa9-ac2f-76b1fc07644e', 'ecb183b4-4add-4215-8b4b-f7f60e544274', 'cf5b1437-ce07-4f44-a672-ecd9cee08e41', 1, '2025-04-09 06:21:47'),
('b1669519-bd7b-433e-9f1f-f99cd0309a9c', 'cf907b6e-e0d5-4aa6-b2b6-006e2cc90a94', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-01-15 07:37:51'),
('b1969522-c018-4f7d-87d0-ca6c625711a3', 'd466792e-3411-4a51-83ca-287e100c3108', '642afc1e-c8d5-42f3-a685-aa899e78be1e', 1, '2025-03-13 09:48:16'),
('be5e5db5-adc5-42cd-8264-fe99d3a3c855', 'e96e8f2a-5a2c-48fc-bae1-d19c30217990', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-01-15 07:40:31'),
('c0363a99-5d2b-46d0-aa3d-2870777d3b9f', '26b7618c-cba9-47bd-a7f5-026e193dd543', 'fb683856-9635-4316-ad3a-2eb57d6eb10f', 1, '2025-04-09 05:57:36'),
('c68ebd0a-f695-4297-b889-afff82bed914', '15288aa6-9497-471c-812c-3251021c8f72', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-06 09:51:28'),
('c72444ef-f744-4db9-b889-2de102469c9a', 'e75996eb-c5ba-4dfe-b36e-5e58ba334bb6', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-31 06:17:12'),
('cc689ad6-0cef-4a14-b9fb-5299beb29d96', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', '0968cd06-9d79-4933-8de8-399cb9ac5868', 1, '2025-01-16 09:15:40'),
('d2275d8d-e3b9-478f-8f51-567eb8320f5a', '56235a39-8439-479b-9c55-9affabfa7b2a', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-02-17 03:42:42'),
('d79c32a5-c688-4c5b-b929-b7facb75c4b4', '6f882833-ac9e-4367-8468-ebb05dd81a8e', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-21 09:30:36'),
('de939b66-5b35-4df9-8f47-70eb8bc6cb9e', '918903af-e6df-4694-82a3-1cea8dda06e0', 'affeb10c-dc64-41d9-952e-d6f01c2d05d1', 1, '2025-01-06 09:40:09'),
('dfbcb554-8c1b-4d29-b524-ddc7f342879d', '16e09e12-f206-4f3b-a5f7-21cff663edfa', 'c918919d-7d14-4f42-97a8-3357016c382a', 1, '2025-02-17 03:41:37'),
('e0d5f1a5-9906-4983-b979-d8a7d554f705', 'a8f71376-2433-4415-8538-bfffff67dbba', 'cb8e3303-3fd7-438c-9c64-07e6c80e012f', 1, '2025-01-06 06:35:36'),
('f16c370c-6f70-41b8-819d-15af00fcbe1a', 'cbd18a9f-2b3a-49f2-8710-5134fa1d8069', 'ae83116d-3c1a-41f7-a066-3e99373b2b44', 1, '2025-01-16 08:48:25'),
('f35bea0b-8e69-4fa5-aca3-8c5ca4b4bb75', '4ce93953-2518-44e8-9380-e55008c39155', '054160f3-f50e-40a2-a45e-569777875172', 1, '2025-03-19 09:49:26'),
('f71254f1-1929-4882-ab55-930364366f7a', 'c25a8b9b-64ad-4b8a-b79a-79e97922eb40', '02e18007-e4e7-4fb7-a2c2-c924ece0a966', 1, '2025-04-09 06:03:51'),
('fb8c30f8-c85c-4f9e-aff1-ec9fb6e20d40', '78f216ec-1aad-43a2-898f-e2301ce04a05', '88d465c6-3e16-4c58-a6da-10bce309af89', 1, '2025-03-17 11:34:04'),
('fbf31815-232a-4d37-a721-0fb31af43d9e', 'f40d86bd-b4ed-4c79-a96a-7aafe7283719', '9f005cab-6ce1-4813-bafe-95be81d93b1d', 1, '2025-03-17 03:10:54');

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
('0412c148-9070-4004-bd70-e9930742eaad', 'ea072d02-f6d1-42b2-bdf9-9451bb5eff3f', 'SO', 'pdf', '../../uploads/project_documents/Innovation/ea072d02-f6d1-42b2-bdf9-9451bb5eff3f/670f3fffd9c53.pdf', 124502, '2024-10-15 21:24:31', '3'),
('08987325-ce6b-45dd-a168-a432909467cd', 'b70608c1-6f57-4abd-bce0-9260962b0bb9', 'ทดสอบ', 'pdf', '../../uploads/project_documents/Innovation_PIT/b70608c1-6f57-4abd-bce0-9260962b0bb9/6785224941598.pdf', 524162, '2025-01-13 14:25:13', '2'),
('e343fa3c-8bb4-439e-b1d4-46acdcfcb13c', 'cbd18a9f-2b3a-49f2-8710-5134fa1d8069', 'ใบเสนอราคา', 'pdf', '../../uploads/project_documents/Enterprise_PIT/cbd18a9f-2b3a-49f2-8710-5134fa1d8069/6788c14178180.pdf', 123553, '2025-01-16 08:20:17', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a');

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
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `member_id` char(36) NOT NULL COMMENT 'รหัสสมาชิกในโครงการ (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `user_id` char(36) NOT NULL COMMENT 'รหัสพนักงาน (เชื่อมโยงกับตาราง users)',
  `role_id` char(36) NOT NULL COMMENT 'รหัสบทบาทในโครงการ (เชื่อมโยงกับตาราง project_roles)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'สถานะการเป็นสมาชิก (1=ยังเป็นสมาชิก, 0=พ้นสภาพ)',
  `joined_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เข้าร่วมโครงการ',
  `left_date` timestamp NULL DEFAULT NULL COMMENT 'วันที่ออกจากโครงการ',
  `created_by` char(36) NOT NULL COMMENT 'ผู้เพิ่มสมาชิก',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เพิ่มข้อมูล',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้แก้ไขล่าสุด',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุเพิ่มเติม'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูลสมาชิกในโครงการ';

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`member_id`, `project_id`, `user_id`, `role_id`, `is_active`, `joined_date`, `left_date`, `created_by`, `created_at`, `updated_by`, `updated_at`, `remark`) VALUES
('af2694eb-d99b-11ef-8216-005056b8f6d0', '6f882833-ac9e-4367-8468-ebb05dd81a8e', 'f30e8b87-d047-4bca-9b34-d223170df87c', '91e08c3d-d8a9-11ef-8216-005056b8f6d0', 1, '2025-01-23 15:07:00', NULL, '2', '2025-01-23 15:07:00', NULL, NULL, NULL),
('fe3f3aed-d984-11ef-8216-005056b8f6d0', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', '3', '91e086ae-d8a9-11ef-8216-005056b8f6d0', 1, '2025-01-23 12:24:34', NULL, '2', '2025-01-23 12:24:34', NULL, NULL, NULL);

--
-- Triggers `project_members`
--
DELIMITER $$
CREATE TRIGGER `before_insert_project_members` BEFORE INSERT ON `project_members` FOR EACH ROW BEGIN
    IF NEW.member_id IS NULL THEN
        SET NEW.member_id = UUID();
    END IF;
END
$$
DELIMITER ;

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
-- Table structure for table `project_roles`
--

CREATE TABLE `project_roles` (
  `role_id` char(36) NOT NULL COMMENT 'รหัสบทบาท (UUID)',
  `role_name` varchar(100) NOT NULL COMMENT 'ชื่อบทบาท',
  `role_description` text DEFAULT NULL COMMENT 'คำอธิบายบทบาท',
  `created_by` char(36) NOT NULL COMMENT 'ผู้สร้างบทบาท',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `updated_by` char(36) DEFAULT NULL COMMENT 'ผู้แก้ไขล่าสุด',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูลบทบาทในโครงการ';

--
-- Dumping data for table `project_roles`
--

INSERT INTO `project_roles` (`role_id`, `role_name`, `role_description`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
('91dec191-d8a9-11ef-8216-005056b8f6d0', 'Project Manager', 'ผู้จัดการโครงการ - รับผิดชอบการบริหารจัดการโครงการโดยรวม', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e086ae-d8a9-11ef-8216-005056b8f6d0', 'Project Coordinator', 'ผู้ประสานงานโครงการ - ประสานงานระหว่างทีมและลูกค้า', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08a79-d8a9-11ef-8216-005056b8f6d0', 'Developer', 'นักพัฒนาระบบ - พัฒนาระบบตามความต้องการของโครงการ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08b6b-d8a9-11ef-8216-005056b8f6d0', 'System Analyst', 'นักวิเคราะห์ระบบ - วิเคราะห์และออกแบบระบบ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08c3d-d8a9-11ef-8216-005056b8f6d0', 'Tester', 'ผู้ทดสอบระบบ - ทดสอบระบบตามข้อกำหนด', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08d01-d8a9-11ef-8216-005056b8f6d0', 'System Engineer', 'วิศวกรระบบ - ดูแลและติดตั้งระบบ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08dbc-d8a9-11ef-8216-005056b8f6d0', 'Support', 'ผู้ดูแลระบบ - ให้การสนับสนุนและแก้ไขปัญหา', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08e89-d8a9-11ef-8216-005056b8f6d0', 'Business Analyst', 'นักวิเคราะห์ธุรกิจ - วิเคราะห์ความต้องการทางธุรกิจ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e08f50-d8a9-11ef-8216-005056b8f6d0', 'QA Engineer', 'วิศวกรประกันคุณภาพ - ควบคุมคุณภาพของระบบ', '1', '2025-01-22 10:13:51', NULL, NULL),
('91e09076-d8a9-11ef-8216-005056b8f6d0', 'Technical Lead', 'ผู้นำด้านเทคนิค - กำหนดแนวทางเทคนิคของโครงการ', '1', '2025-01-22 10:13:51', NULL, NULL),
('be76042b-d8a9-11ef-8216-005056b8f6d0', 'UX/UI Designer', NULL, '2', '2025-01-22 10:15:06', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_tasks`
--

CREATE TABLE `project_tasks` (
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน (UUID)',
  `project_id` char(36) NOT NULL COMMENT 'รหัสโครงการ (เชื่อมโยงกับตาราง projects)',
  `parent_task_id` char(36) DEFAULT NULL COMMENT 'รหัสงานแม่ (ถ้าเป็น Sub Task)',
  `task_name` varchar(255) NOT NULL COMMENT 'ชื่องาน',
  `description` text DEFAULT NULL COMMENT 'รายละเอียดงาน',
  `start_date` date DEFAULT NULL COMMENT 'วันที่เริ่มงาน',
  `end_date` date DEFAULT NULL COMMENT 'วันที่สิ้นสุดงาน',
  `status` enum('Pending','In Progress','Completed','Cancelled') DEFAULT 'Pending' COMMENT 'สถานะงาน',
  `progress` decimal(5,2) DEFAULT 0.00 COMMENT 'ความคืบหน้า (%)',
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium' COMMENT 'ระดับความสำคัญ',
  `created_by` char(36) NOT NULL COMMENT 'รหัสผู้สร้างงาน',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้แก้ไขล่าสุด',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด',
  `task_order` int(11) DEFAULT 0,
  `task_level` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูลงานในโครงการ';

--
-- Dumping data for table `project_tasks`
--

INSERT INTO `project_tasks` (`task_id`, `project_id`, `parent_task_id`, `task_name`, `description`, `start_date`, `end_date`, `status`, `progress`, `priority`, `created_by`, `created_at`, `updated_by`, `updated_at`, `task_order`, `task_level`) VALUES
('0db715cb-32d5-402d-bc84-fdea13cef6bf', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', NULL, '1. Microcontroller Design', 'Microcontroler Desing พี่ซีนประสานงานพี่ตุ้ม ดังนี้ \n1. ออกแบบกล่อง\n2. นำเข้าบอร์ด ER32 \n3. Screen Brand ติดกล่อง ', '2025-01-23', '2025-01-27', 'In Progress', 50.00, 'Medium', '3', '2025-01-23 12:29:25', '3', '2025-01-24 09:03:35', 1, 0),
('954d3afb-e95d-4812-b743-4e1064c2d22a', 'ad3b9787-d382-4eb0-ac96-d9a3917d5db6', '0db715cb-32d5-402d-bc84-fdea13cef6bf', '1.1. อ้ดเดทความคืบหน้าประสานงาน ดำเนินการนำเข้า ES32 และตัวกล่องบรรจุภัฑณ์', '- ประสานงานพี่ตุ๋ม ช่วยหาของภายในประเทศไทย เนื่องอุปกรณ์รองรับการใส่ Sim, WIFI จะถูกตรวจสอบ หรือใช่เวลาดำเนินการค่อนข้างนานเนื่องจากต้องผ่านการตรวจสอบผ่าน กสทช. \n- ประสานงานพี่ขวัญ หาตัว Board ไม่มี SIM , WIFI เพื่อให้ง่ายต่อการนำเข้า \n\nพี่ขวัญ จัดหาเป็นต้น : https://lilygo.cc/products/t-internet-poe?srsltid=AfmBOoq9ALNRGXzV-pSvz5DmnPz_yg9hCyBnNyIdOKIiOr8f2Gvy1vOX\nพี่ตุ้ม : ให้ทางจัดซื้อ จัดซื้อ และประสานงานผู้ผลิตตัวกล่องในการดำเนินการ และขอราคาแพ็กเก็จทั้งหมด', '2025-01-24', '2025-01-27', 'In Progress', 50.00, 'Medium', '3', '2025-01-24 09:02:43', '3', '2025-01-24 09:03:24', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `project_task_assignments`
--

CREATE TABLE `project_task_assignments` (
  `assignment_id` char(36) NOT NULL COMMENT 'รหัสการมอบหมายงาน (UUID)',
  `task_id` char(36) NOT NULL COMMENT 'รหัสงาน',
  `user_id` char(36) NOT NULL COMMENT 'รหัสผู้รับผิดชอบ',
  `assigned_by` char(36) NOT NULL COMMENT 'รหัสผู้มอบหมายงาน',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่มอบหมายงาน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บการมอบหมายงาน';

--
-- Dumping data for table `project_task_assignments`
--

INSERT INTO `project_task_assignments` (`assignment_id`, `task_id`, `user_id`, `assigned_by`, `assigned_at`) VALUES
('471516ed-ef74-4b84-ba7c-e0dda9c50861', '954d3afb-e95d-4812-b743-4e1064c2d22a', '3', '3', '2025-01-24 09:03:24'),
('ac88fd56-1c5c-4128-a5b3-e5222f0d18d8', '0db715cb-32d5-402d-bc84-fdea13cef6bf', '3', '3', '2025-01-24 09:03:35');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` char(36) NOT NULL,
  `supplier_name` varchar(255) NOT NULL COMMENT 'ชื่อผู้จำหน่าย/ซัพพลายเออร์',
  `company` varchar(255) DEFAULT NULL COMMENT 'ชื่อบริษัท/องค์กร',
  `contact_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อผู้ติดต่อ',
  `position` varchar(255) DEFAULT NULL COMMENT 'ตำแหน่ง',
  `address` text DEFAULT NULL COMMENT 'ที่อยู่',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `office_phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์สำนักงาน',
  `extension` varchar(10) DEFAULT NULL COMMENT 'เบอร์ต่อ',
  `email` varchar(255) DEFAULT NULL COMMENT 'อีเมล',
  `suppliers_image` varchar(255) DEFAULT NULL COMMENT 'รูปโลโก้บริษัท/องค์กร',
  `remark` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `created_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้สร้างข้อมูล',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่อัพเดทล่าสุด',
  `updated_by` char(36) DEFAULT NULL COMMENT 'รหัสผู้แก้ไขข้อมูลล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `company`, `contact_name`, `position`, `address`, `phone`, `office_phone`, `extension`, `email`, `suppliers_image`, `remark`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
('23722daa-6eec-4a29-aa60-89cdea4dcd8c', 'Point IT', 'Point IT Consulting Co.,Ltd.', NULL, 'Service', '19 ซอยสุภาพงษ์ 1 แยก 6 แขวงหนองบอน เขตประเวศ กรุงเทพมหานคร 10250', '087-687-1184', '02-348-4790', '1041', 'info@pointit.co.th', '', 'บริการงานไอทีครบวงจร', '2025-01-12 05:23:59', '2', '2025-01-12 05:25:33', '2'),
('65cd0179-03e2-480d-84f0-42eec670151d', 'Apirak bangpuk', 'Point IT Consulting Co.,Ltd.', NULL, 'Project Manager', 'เลขที่ 111/1 ธนพงษ์แมนชั่น ห้อง. 302 ซ. สันนิบาตเทศบาล แขวง จันทรเกษม', '0839595800', '0839595800', '', 'apirak.ba@gmail.com', '', '', '2025-04-12 07:53:41', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', '2025-04-12 16:04:15', '0');

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
('28534929-e527-4755-bd37-0acdd51b7b45', 'Sale Corporate _PIT', 'Sale Corporate _PIT', '5', '2024-11-06 01:46:07', '5', '2024-12-03 04:26:43', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6'),
('3', 'Service_PIT', 'Service Bank & Corporate_Chittichai', '2', '2024-09-26 03:35:50', '5', '2024-12-03 04:27:06', '97c68703-a8b7-4ceb-9344-65fe4404c4ab'),
('37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'Enterprise_PIT', 'Presales Service & Enterprise Solution', '5', '2024-11-01 01:05:11', '5', '2024-12-03 04:27:43', '34e67e45-92f6-4e20-a78b-a4ffe97b3775'),
('4', 'Point IT', 'Point IT Consulting Co. Ltd.', '2', '2024-09-26 03:35:50', '2', '2024-10-20 12:39:29', '5'),
('715e81f0-4985-4981-982c-45cafb9748dc', 'Versual Teams (MAZK)', 'ใช้เวลาว่าให้เกิดประโยชน์', '2', '2025-01-07 10:47:16', '2', '2025-01-07 10:48:27', 'b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a'),
('b9db21db-cfd7-4887-9ca7-5088a12f1bda', 'Sales Solution_PIT', 'Sales Solution', '5', '2024-11-04 02:58:23', '5', '2024-12-03 04:28:12', '6614b721-a8b4-46d2-9c80-0caab04772dc'),
('c8fcdec8-4a28-4b6b-be8b-8bb0579d74bc', 'Outsourcing Service_PIT', 'Outsourcing Team Pattaya', '5', '2024-11-25 01:37:30', '5', '2024-12-03 04:28:28', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f'),
('db32697a-0f69-41f7-9413-58ffe920ad7d', 'Sales Bank Corporate_PIT', 'Bank Corporate Sales', '5', '2024-11-04 02:29:50', '5', '2024-11-06 01:21:56', '6614b721-a8b4-46d2-9c80-0caab04772dc'),
('de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 'Smart City Solutions', '', '5', '2024-12-09 03:17:23', NULL, NULL, '5'),
('f4b11a86-0fca-45e5-8511-6a946c7f21d4', 'Sales Smart City_PIT', 'Oran Team', '5', '2024-11-04 01:39:32', '5', '2024-11-06 01:23:26', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f');

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
('1', 'Sale', 'Test Platform', 'Sale', 'Saletest@gmail.com', 'Seller', '4', 'Sale Test Platform', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:43:58', '2', ''),
('14d9e34c-b691-4ce8-a5ef-929ace71248a', 'Boongred', 'Theephukhieo', 'boongerd', 'boongerd@pointit.co.th', 'Sale Supervisor', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'System Engineer Manager', '0818741889', '$2y$10$nOlaLUtPDsBhJxyi37sYZukj7i8dJJ811mbTxeC749VKxZZuYO1vW', 'Point IT Consulting Co.,Ltd.', '2024-10-31 23:55:23', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', NULL),
('1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'Arunnee', 'Thiamthawisin', 'Arunnee', 'arunnee@pointit.co.th', 'Seller', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 'Account Executive Manager', '', '$2y$10$mzextohitcaMnwfRGgyUg.C1LyMEvRbxq2sy4dnN3WKlOVJZIMi4S', 'Point IT Consulting Co.,Ltd.', '2024-11-04 03:05:45', '5', NULL),
('1f540668-fa06-45ec-8881-b50c378cf648', 'Podchanan', 'Setthanan', 'Podchanan', 'Podchanan@pointit.co.th', 'Seller', '28534929-e527-4755-bd37-0acdd51b7b45', 'Account Executive', '', '$2y$10$4Wtf3LOLe3wXebNw4co/e.58NEKkRyjxqUE7vceMnEFSLqA.D7eym', 'Point IT Consulting Co.,Ltd.', '2024-12-03 04:23:35', '5', NULL),
('2', 'Systems', 'Admin', 'Admin', 'Systems_admin@gmail.com', 'Executive', '1', 'Systems Admin', '0839595800', '$2y$10$jcmTr.I9CthXOrWFC78XjuOjwPoZlbvF80M4RKow4RvnNbm1Ej8dO', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:43:58', '2', ''),
('270c74ec-9124-4eb5-9469-0253ba8530af', 'Awirut', 'Somsanguan', 'Awirut', 'Awirut@pointit.co.th', 'Sale Supervisor', '28534929-e527-4755-bd37-0acdd51b7b45', 'Smart Innovation Technology Consulting Manager', '', '$2y$10$zbqZ8JHuuGejCPqkozcYb.wzIfiTgY.peFop7RJInr9HIUPjzZFra', 'Point IT Consulting Co.,LTD.', '2024-11-06 02:20:29', '5', NULL),
('2f6d353b-53f1-4492-8878-bc93c18c5de9', 'Prakorb', 'Jongjarussang', 'Prakorb', 'prakorb@pointit.co.th', 'Executive', '4', 'MD', '', '$2y$10$Nl9zzwKG.i1pS2jiZhQ41OcybPwbB5qGl80aY12.pcV4v6/bVzxn6', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:31:48', '5', NULL),
('3', 'Miss Phattraorn', 'Amornophakun', 'Phattraorn', 'phattraorn.a@pointit.co.th', 'Seller', '1', 'Sales', '0619522111', '$2y$10$LZXVwCISxNHxb8lvs93mDe9RCLU76842RRbezYEqSmJbvuBCgvExe', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:43:58', '2', '670e42ef5b4a3.jpg'),
('30750fba-88ab-44ce-baf2-d0894357c67c', 'Bulakorn', 'Puapun', 'Bulakorn', 'bulakorn@gmail.com', 'Sale Supervisor', '1', 'AI Business Consulting Director', '', '$2y$10$h7OSSaVYQM5CLz9rmn37Z.4qQ2Hax0D17UerN.BTqrixUTv6U69Ta', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:14:26', '5', NULL),
('3140fdaf-5103-4423-bf87-11b7c1153416', 'Direk', 'Wongsngam', 'Direk', 'Direk@pointit.co.th', 'Seller', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 'Bank & Corporate SalesDirector', '', '$2y$10$M/bAx1lFykgf1LklAvbQKONKI4OQfpu7NofVfwA.r1GDy9xx94uGO', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:39:01', '5', NULL),
('34e67e45-92f6-4e20-a78b-a4ffe97b3775', 'Pisarn', 'Siribandit', 'Pisarn', 'pisarn@pointit.co.th', 'Sale Supervisor', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'Digital Transformation Consulting Director', '', '$2y$10$aEOtRUxIfKi52ib5Jj.Vpue/FP7eIWKeNRdM68DEr1GCH5OUa1uOy', 'Point IT Consulting Co.,Ltd.', '2024-10-31 18:08:53', '5', '67242a25ce524.png'),
('3d82b654-e49f-46f8-b698-fceaa5d4cdae', 'Natapornsuang', 'Chanasan', 'Natapornsuang', 'natapornsuang@pointit.co.th', 'Seller', 'b9db21db-cfd7-4887-9ca7-5088a12f1bda', 'Account Executive Manager', '', '$2y$10$cMPa/VsJaIs.WSQxCHvVT.Ct6hbifKjAkQScjAEQv6dbGQwR8zCOC', 'Point IT Consulting Co.,Ltd.', '2024-11-04 03:00:40', '5', NULL),
('3efcb87b-ce45-4a66-9d73-91259caba1d0', 'Teerayut', 'Kaengjai', 'Teerayut', 'Teerayut@pointit.co.th', 'Engineer', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'Head of Enterprise Engineer Service', '', '$2y$10$u5SlcRNFVTOxQ1aFabruaeLG49neZPwAQEWo6ToVm8ZwwZul8lqVS', 'Point IT Consulting Co.,Ltd.', '2024-11-06 02:29:31', '5', NULL),
('4', 'Support', 'Platform', 'Support', 'Support@gmail.com', 'Executive', '4', 'Application Support', '0839595811', '$2y$10$RAWOJU03Vy72u4zMVF/M/O9Af1HSbGOHAjlDKZHgrzbSZodZUcuky', 'Point IT Consulting Co.,Ltd.', '2024-09-15 09:55:43', '2', '6724613260590.png'),
('44ab4e8b-e3e6-431d-ad49-40d4601779b4', 'Nutjaree', 'Chaothonglang', 'Nutjaree', 'nutjaree@pointit.co.th', 'Sale Supervisor', '3', 'Assistant Service Manager', '', '$2y$10$OeTqb/woFTv/pt7uaBRx4ujA7jJYTuyGzSmx2y4jtijxn9oJcRuky', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:04:37', '97c68703-a8b7-4ceb-9344-65fe4404c4ab', NULL),
('5', 'Panit', 'Paophan', 'Panit', 'panit@poinitit.co.th', 'Executive', '4', 'Executive Director', '0814834619', '$2y$10$mwci/Fvi0nXZjgARpb1C2efi3WvHJnU9Blwy3umq0RBreRHEnpb.G', 'Point IT Consulting Co.,Ltd.', '2024-09-17 08:15:37', '2', NULL),
('5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'Kanitta', 'Ongsathan', 'Kanitta', 'kanitta@pointit.co.th', 'Sale Supervisor', '4', 'Senior Procurement', '0880223292', '$2y$10$6BcDhIY.7m7X2s7D6iAXkOTuden3sQucRuN.8mcV4WF44RMmFHHui', 'Point IT Consulting Co.,Ltd.', '2024-12-03 04:01:23', '5', NULL),
('6614b721-a8b4-46d2-9c80-0caab04772dc', 'Woradol', 'Daoduang', 'Woradol', 'Woradol@pointit.co.th', 'Executive', '4', 'Executive Director', '', '$2y$10$l454f/PTDFOabJbIz0BAkedEGdUGc000TRpac7ffYJrRzlIIwcUc2', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:34:51', '5', NULL),
('85c114ec-a416-41c0-9859-12b90dc5b488', 'Porapath', 'Yanthukij', 'Porapath', 'porapath@pointit.co.th', 'Seller', '4', 'Procurement', '0956422238', '$2y$10$U1hx.FejkNpt5/ltAvw.b.gxPyzq3fS5WpqMh4H.10negrF/7qVk6', 'Point IT Consulting Co.,Ltd.', '2024-12-03 04:02:44', '5', NULL),
('8ae1a02d-32c2-4469-ba98-818dfc76dcdb', 'Pawitcha', 'Katekhong', 'Pawitcha', 'Pawitcha@pointit.co.th', 'Seller', 'db32697a-0f69-41f7-9413-58ffe920ad7d', 'Bank &amp; Corporate Account Executive', '', '$2y$10$GlSuFx2QYYWyqTQLkDdIwuUN5Lt2Pn8wwC/N0mG5pRrZ/lUF4p0Z2', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:43:57', '5', NULL),
('8c1c0a55-2610-4081-8d12-b2a6971ffbe8', 'Yuthana', 'Jaturajitraporn', 'Yuthana', 'yuthana@pointit.co.th', 'Seller', '1', 'Senior Sales Backend Developer', '', '$2y$10$.ZJ0wDC827yYB5BqJmbrD.sbXB8sk1m4QPbEXHeVsXCrKofMhC0km', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:07:14', '5', NULL),
('8c782887-8fd3-4f99-ac27-63054a8a1942', 'Surapan', 'Pawanrum', 'Surapan', 'Surapan@pointit.co.th', 'Sale Supervisor', '1', 'Platform Development Manager', '', '$2y$10$wf6P22p7BIpJ2bIdRuyyyur2jxxyliqEi4T084m6Slq.4FZsQxCOa', 'Point IT Consulting Co.,Ltd.', '2024-12-02 06:48:59', '5', NULL),
('97c68703-a8b7-4ceb-9344-65fe4404c4ab', 'Chittichai', 'Duangnang', 'Chittichai', 'chittichai@pointit.co.th', 'Sale Supervisor', '3', 'Service Manager', '', '$2y$10$va/6nCSzdBqd/kCyMgYN7.gtksHhW2t14s3Qr1EClGsr10cSFJyza', 'Point IT Consulting Co.,Ltd.', '2024-11-04 02:00:10', '5', NULL),
('a5741799-938b-4d0a-a3dc-4ca1aa164708', 'Theerachart', 'Tiyapongpattana', 'Theerachart', 'theerachart@pointit.co.th', 'Engineer', '1', 'Innovation Business Consulting Manager', '', '$2y$10$FcspHzhkNMDUaSMshYrZdOGC/8OHya2fH8nwgcppvoFI0HT9w8W7O', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:28:08', '5', NULL),
('b27b56e5-6f28-4d30-8add-4bddafa38841', 'Decha', 'Suratkullwattana', 'Decha', 'khadectemp@outlook.com', 'Engineer', '1', 'Software Business Consultant', '', '$2y$10$YUnc5HvQZ1UQFx64cdsP2.0S3y38hdWzvqDur3v2Plj8gnE8w3iXa', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:11:18', '5', NULL),
('b3d20651-6a09-4fb7-b1f8-c72c9cd9e91a', 'Nanthika', 'Chongcharassang', 'nanthika', 'nanthika@pointit.co.th', 'Sale Supervisor', '37547921-5387-4be1-bde0-e9ba5c4e0fdf', 'Project Manager', '0631979263', '$2y$10$q4fQzTZYBAJk123iwNvRU.LsTxBU9lfdPhk5kVIqvMYVAxU/VGLyC', 'Point IT Consulting Co.,Ltd.', '2024-10-31 23:57:35', '34e67e45-92f6-4e20-a78b-a4ffe97b3775', NULL),
('ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', 'Gun', 'Oran', 'Oran.gun', 'oran.gun@gmail.com', 'Sale Supervisor', '2', 'MD', '', '$2y$10$hEznSrVA2uC81Y0sqsnqS.u3Xy36fnCmdum/TNzWD8huYy7ki.4HO', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:34:43', '5', '672824b3cb14d.png'),
('bd9e0c55-0c75-44b1-9475-c11dfc91fbf4', 'Yanisa', 'Khemthong', 'Yanisa_Pit', 'Yanisa@pointit.co.th', 'Seller', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 'Senior Account Executive, Smart City Solution', '', '$2y$10$Y.4Xgneo59aAcdNUcz9Zx.Aa71Fj2bLqIZJhVYt95a19ztua6wSxC', 'Point IT Consulting Co.,Ltd.', '2024-11-04 01:49:55', 'ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', NULL),
('c81eb76b-260d-4d5f-a8fe-d4f72ca6630a', 'Yanisa', 'Zoom', 'Yanisa_Zoom', 'yanisa8742@gmail.com', 'Seller', '2', 'Senior Account Executive, Smart City Solution', '', '$2y$10$Y.4Xgneo59aAcdNUcz9Zx.Aa71Fj2bLqIZJhVYt95a19ztua6wSxC', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:54:45', 'ba194fb5-b62a-40e3-99a3-c4b82f9bd84f', NULL),
('c89b96f1-f916-448d-9725-2e0957cdba49', 'Versual Teams', '(Mazk)', 'mazk', 'innovation@pointit.co.th', 'Sale Supervisor', '715e81f0-4985-4981-982c-45cafb9748dc', 'Project Management', '0619512111', '$2y$10$KLN.d4rgbQqAiGH8s2LYYeD.4XgakfQLDzGESSV/HUuLcz0oCaBVG', 'Point IT Consulting Co.,Ltd.', '2025-01-16 06:51:41', '2', '6788ac7dea3bd.jpg'),
('c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'Jakkrit', 'Pontpai', 'Jakkrit', 'jakkrit@pointit.co.th', 'Sale Supervisor', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 'Smart City Business Consulting Manager', '', '$2y$10$Vd8C2.69FvbUIvmAejUz4eZddOs.rEUiemJ.e94.7B15R2O0CQJ7S', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:18:56', '5', NULL),
('e23160ec-23a4-4724-9690-adb205162afb', 'Wilaiwan', 'Vutipram', 'Wilaiwan', 'wilaiwan@pointit.co.th', 'Seller', 'de3fc0f5-9ebf-4c47-88dd-da5a570653ae', 'Project Management , Smart city solutions', '', '$2y$10$GdkL6jMtVHIyWuKlOv7KUO2aXwQTB1cC4v2E7GItr3oFjesZRjE36', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:21:15', '5', NULL),
('e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 'Woraluck', 'Khunsuwanchai', 'Woraluck', 'Woraluck@pointit.co.th', 'Sale Supervisor', '28534929-e527-4755-bd37-0acdd51b7b45', 'Account Executive Manager', '', '$2y$10$Y.4Xgneo59aAcdNUcz9Zx.Aa71Fj2bLqIZJhVYt95a19ztua6wSxC', 'Point IT Consulting Co.,Ltd.', '2024-11-06 01:17:45', '5', NULL),
('e79e9929-6132-41ae-ab06-65b29fe70f6c', 'Panuwat', 'Sukcheep', 'Panuwat.S', 'panuwat@pointit.co.th', 'Engineer', 'c8fcdec8-4a28-4b6b-be8b-8bb0579d74bc', 'IT Outsourcing Service Manager', '', '$2y$10$Y.4Xgneo59aAcdNUcz9Zx.Aa71Fj2bLqIZJhVYt95a19ztua6wSxC', 'Point IT Consulting Co.,LTD.', '2024-11-25 01:33:50', '5', NULL),
('e8237f0d-f317-4b92-a1f2-61e97d8eaaa1', 'Daranee', 'Punyathiti', 'Daranee', 'daranee@pointit.co.th', 'Executive', '2', 'MD', '', '$2y$10$Y.4Xgneo59aAcdNUcz9Zx.Aa71Fj2bLqIZJhVYt95a19ztua6wSxC', 'บริษัท ซูม อินฟอร์เมชั่น ซิสเต็ม จํากัด', '2024-11-04 01:30:21', '5', '672823ad217eb.png'),
('ef458c7c-2dff-4dda-8c1b-8aa7c9520c3f', 'Oran.gun', 'Point IT', 'Oran.pit', 'Oran@pointit.co.th', 'Sale Supervisor', 'f4b11a86-0fca-45e5-8511-6a946c7f21d4', 'Smart City Consulting Director', '', '$2y$10$HryjgOgnKvm.xzNYpmiC4uQ6bPif4/A.oZH6HSUGoHr.rxPQim4pO', 'Point IT Consulting Co.,Ltd.', '2024-11-04 01:45:31', '5', NULL),
('f30e8b87-d047-4bca-9b34-d223170df87c', 'Jiratip', 'vittayanusak', 'Jiratip', 'j.vittayanusak@gmail.com', 'Engineer', '1', 'Software Tester', '0902215120', '$2y$10$FHjLgmFWhJC2vBmL6yJh9.9dKpyLGaJPa.8M.92nBUEiZtSpIcGdC', 'Point IT Consulting Co.,Ltd.', '2024-12-09 10:08:45', '2', NULL),
('f384c704-5291-4413-8f52-dc25e10b5d4f', 'Piti', 'Nithitanabhornkul', 'Piti', 'piti@pointit.co.th', 'Engineer', '1', 'Senior Backend Software Develper', '', '$2y$10$Y.4Xgneo59aAcdNUcz9Zx.Aa71Fj2bLqIZJhVYt95a19ztua6wSxC', 'Point IT Consulting Co.,Ltd.', '2024-12-09 03:02:34', '5', NULL),
('f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'Pongsan', 'chakranon', 'Pongsan', 'pongsan.chakranon@gmail.com', 'Engineer', '1', 'Ai Software Developer', '', '$2y$10$Y.4Xgneo59aAcdNUcz9Zx.Aa71Fj2bLqIZJhVYt95a19ztua6wSxC', 'Point IT Consulting Co.,Ltd.', '2024-12-09 02:57:19', '5', NULL),
('ff2acbbb-4ec0-4214-8a30-eb1fc6e02700', 'Poomsak', 'Janluan', 'Poomsak', 'poomsak1994@gmail.com', 'Engineer', '1', 'Software Development', '0862295093', '$2y$10$nLnc8X5SpG/Mx7IbAjyrLOxCmfbWO44oGsW6DUmeOf8VppdOu8A3i', 'Point IT Consulting Co.,Ltd.', '2024-12-09 09:27:12', '2', NULL);

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
(17, '5', '1b9c09d2-dc91-4b5e-a62b-8c42a41958ab', 'Seller', '2024-11-04 03:05:46'),
(18, '5', 'e40dedaf-3e9b-4694-8ee9-c173d5c44db6', 'Seller', '2024-11-06 01:17:45'),
(19, '5', '270c74ec-9124-4eb5-9469-0253ba8530af', 'Sale Supervisor', '2024-11-06 02:20:29'),
(20, '5', '3efcb87b-ce45-4a66-9d73-91259caba1d0', 'Engineer', '2024-11-06 02:29:31'),
(21, '5', 'e79e9929-6132-41ae-ab06-65b29fe70f6c', 'Engineer', '2024-11-25 01:33:50'),
(22, '5', '8c782887-8fd3-4f99-ac27-63054a8a1942', 'Sale Supervisor', '2024-12-02 06:48:59'),
(23, '5', '5eef69ba-15ee-4414-a2e4-be4f68b8839e', 'Sale Supervisor', '2024-12-03 04:01:23'),
(24, '5', '85c114ec-a416-41c0-9859-12b90dc5b488', 'Seller', '2024-12-03 04:02:44'),
(25, '5', '1f540668-fa06-45ec-8881-b50c378cf648', 'Seller', '2024-12-03 04:23:35'),
(26, '5', 'f4c662e1-82d1-4d5a-ba11-b8ddac4c21a0', 'Engineer', '2024-12-09 02:57:20'),
(27, '5', 'f384c704-5291-4413-8f52-dc25e10b5d4f', 'Engineer', '2024-12-09 03:02:34'),
(28, '5', '8c1c0a55-2610-4081-8d12-b2a6971ffbe8', 'Engineer', '2024-12-09 03:07:14'),
(29, '5', 'b27b56e5-6f28-4d30-8add-4bddafa38841', 'Engineer', '2024-12-09 03:11:18'),
(30, '5', '30750fba-88ab-44ce-baf2-d0894357c67c', 'Sale Supervisor', '2024-12-09 03:14:26'),
(31, '5', 'c9245a19-52fa-4b02-a98c-b962f2f51b3f', 'Sale Supervisor', '2024-12-09 03:18:56'),
(32, '5', 'e23160ec-23a4-4724-9690-adb205162afb', 'Seller', '2024-12-09 03:21:15'),
(33, '5', 'a5741799-938b-4d0a-a3dc-4ca1aa164708', 'Engineer', '2024-12-09 03:28:08'),
(34, '2', 'ff2acbbb-4ec0-4214-8a30-eb1fc6e02700', 'Engineer', '2024-12-09 09:27:13'),
(35, '2', 'f30e8b87-d047-4bca-9b34-d223170df87c', 'Engineer', '2024-12-09 10:08:46'),
(36, '2', 'c89b96f1-f916-448d-9725-2e0957cdba49', 'Sale Supervisor', '2025-01-16 06:51:42');

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
-- Indexes for table `document_links`
--
ALTER TABLE `document_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_document_links_project` (`project_id`),
  ADD KEY `fk_document_links_created_by` (`created_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_email` (`personal_email`),
  ADD UNIQUE KEY `company_email` (`company_email`),
  ADD UNIQUE KEY `company_email_2` (`company_email`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD UNIQUE KEY `idx_expense_number` (`expense_number`);

--
-- Indexes for table `expense_approval_limits`
--
ALTER TABLE `expense_approval_limits`
  ADD PRIMARY KEY (`limit_id`),
  ADD UNIQUE KEY `idx_role` (`role`);

--
-- Indexes for table `expense_approval_logs`
--
ALTER TABLE `expense_approval_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_expense_id` (`expense_id`),
  ADD KEY `idx_reviewer_id` (`reviewer_id`);

--
-- Indexes for table `expense_documents`
--
ALTER TABLE `expense_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `idx_expense_id` (`expense_id`),
  ADD KEY `idx_item_id` (`item_id`);

--
-- Indexes for table `expense_items`
--
ALTER TABLE `expense_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_expense_id` (`expense_id`),
  ADD KEY `idx_expense_type` (`expense_type`);

--
-- Indexes for table `expense_types`
--
ALTER TABLE `expense_types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `idx_type_name` (`type_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `products_ibfk_1` (`created_by`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `products_team_fk` (`team_id`);

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
-- Indexes for table `project_customers`
--
ALTER TABLE `project_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `customer_id` (`customer_id`);

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
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `unique_project_user` (`project_id`,`user_id`) COMMENT 'ป้องกันการเพิ่มซ้ำ',
  ADD KEY `fk_project_members_user` (`user_id`),
  ADD KEY `fk_project_members_role` (`role_id`);

--
-- Indexes for table `project_payments`
--
ALTER TABLE `project_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_project_payments_project` (`project_id`),
  ADD KEY `fk_project_payments_user` (`created_by`);

--
-- Indexes for table `project_roles`
--
ALTER TABLE `project_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `unique_role_name` (`role_name`) COMMENT 'ป้องกันชื่อบทบาทซ้ำ';

--
-- Indexes for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `parent_task_id` (`parent_task_id`);

--
-- Indexes for table `project_task_assignments`
--
ALTER TABLE `project_task_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_image` FOREIGN KEY (`image_id`) REFERENCES `category_image` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_links`
--
ALTER TABLE `document_links`
  ADD CONSTRAINT `fk_document_links_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_document_links_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_approval_logs`
--
ALTER TABLE `expense_approval_logs`
  ADD CONSTRAINT `fk_expense_approval_logs_expense` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_documents`
--
ALTER TABLE `expense_documents`
  ADD CONSTRAINT `fk_expense_documents_expense` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_expense_documents_item` FOREIGN KEY (`item_id`) REFERENCES `expense_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_items`
--
ALTER TABLE `expense_items`
  ADD CONSTRAINT `fk_expense_items_expense` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `products_team_fk` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`);

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
-- Constraints for table `project_customers`
--
ALTER TABLE `project_customers`
  ADD CONSTRAINT `project_customers_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_customers_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

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
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `fk_project_members_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_project_members_role` FOREIGN KEY (`role_id`) REFERENCES `project_roles` (`role_id`),
  ADD CONSTRAINT `fk_project_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `project_tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `project_tasks_ibfk_2` FOREIGN KEY (`parent_task_id`) REFERENCES `project_tasks` (`task_id`);

--
-- Constraints for table `project_task_assignments`
--
ALTER TABLE `project_task_assignments`
  ADD CONSTRAINT `project_task_assignments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`task_id`),
  ADD CONSTRAINT `project_task_assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `fk_team_leader` FOREIGN KEY (`team_leader`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
