-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2024 at 06:03 PM
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
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
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
(1, 'John Doe', 'TechCorp', '123 Main St, City A', '555-1234', 'john.doe@techcorp.com', 'ลูกค้าประจำ', 1, '2024-09-20 15:03:08'),
(2, 'Jane Smith', 'Innovate Inc', '456 Elm St, City B', '555-5678', 'jane.smith@innovate.com', 'ลูกค้าใหม่', 2, '2024-09-20 15:03:08'),
(3, 'Michael Brown', 'Design Solutions', '789 Oak St, City C', '555-7890', 'michael.brown@design.com', '', 1, '2024-09-20 15:03:08'),
(4, 'Emily Davis', 'BuildIt', '101 Pine St, City D', '555-2345', 'emily.davis@buildit.com', 'ลูกค้าโครงการใหญ่', 3, '2024-09-20 15:03:08'),
(5, 'William Johnson', 'ConstructCo', '202 Maple St, City E', '555-6789', 'william.johnson@constructco.com', '', 2, '2024-09-20 15:03:08'),
(6, 'Olivia Wilson', 'WebCreatives', '303 Birch St, City F', '555-3456', 'olivia.wilson@webcreatives.com', 'ลูกค้าโครงการเล็ก', 1, '2024-09-20 15:03:08'),
(7, 'James Taylor', 'MarketingPro', '404 Cedar St, City G', '555-9012', 'james.taylor@marketingpro.com', '', 3, '2024-09-20 15:03:08'),
(8, 'Sophia Anderson', 'Smart Solutions', '505 Aspen St, City H', '555-7890', 'sophia.anderson@smartsolutions.com', 'ลูกค้าประจำ', 2, '2024-09-20 15:03:08'),
(9, 'Liam Martinez', 'SecurityTech', '606 Spruce St, City I', '555-4567', 'liam.martinez@securitytech.com', '', 1, '2024-09-20 15:03:08'),
(10, 'Emma White', 'AutoMechanic', '707 Pinecone St, City J', '555-6789', 'emma.white@automechanic.com', 'ลูกค้าใหม่', 3, '2024-09-20 15:03:08'),
(11, 'Noah Harris', 'GreenEnergy', '808 Redwood St, City K', '555-2345', 'noah.harris@greenenergy.com', '', 2, '2024-09-20 15:03:08'),
(12, 'Ava Lewis', 'HealthPlus', '909 Sequoia St, City L', '555-5678', 'ava.lewis@healthplus.com', 'ลูกค้าประจำ', 1, '2024-09-20 15:03:08'),
(13, 'Benjamin Walker', 'BuildFuture', '1000 Willow St, City M', '555-7890', 'benjamin.walker@buildfuture.com', '', 2, '2024-09-20 15:03:08'),
(14, 'Mia Hall', 'StartUpLab', '1100 Cypress St, City N', '555-9012', 'mia.hall@startuplab.com', 'ลูกค้าใหม่', 3, '2024-09-20 15:03:08'),
(15, 'Lucas Clark', 'FinTech Solutions', '1200 Dogwood St, City O', '555-3456', 'lucas.clark@fintechsolutions.com', '', 1, '2024-09-20 15:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `sales_price` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `contract_no` varchar(50) DEFAULT NULL,
  `product` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `sales_date` date DEFAULT NULL,
  `seller` int(11) DEFAULT NULL,
  `sale_no_vat` decimal(10,2) DEFAULT NULL,
  `sale_vat` decimal(10,2) DEFAULT NULL,
  `cost_no_vat` decimal(10,2) DEFAULT NULL,
  `cost_vat` decimal(10,2) DEFAULT NULL,
  `gross_profit` decimal(10,2) DEFAULT NULL,
  `potential` decimal(5,2) DEFAULT NULL,
  `es_sale_no_vat` decimal(10,2) DEFAULT NULL,
  `es_cost_no_vat` decimal(10,2) DEFAULT NULL,
  `es_gp_no_vat` decimal(10,2) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `project_name`, `sales_price`, `start_date`, `end_date`, `status`, `contract_no`, `product`, `remark`, `sales_date`, `seller`, `sale_no_vat`, `sale_vat`, `cost_no_vat`, `cost_vat`, `gross_profit`, `potential`, `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat`, `customer_id`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'Project Alpha', 100000.00, '2023-01-10', '2023-02-10', 'In Progress', 'CN001', 'Product A', 'Remark for project Alpha', '2023-01-10', 1, 95000.00, 5000.00, 85000.00, 10000.00, 10000.00, 80.50, 92000.00, 83000.00, 9000.00, 1, '2024-09-22 05:54:27', 1, '2024-09-22 05:54:27', 2),
(2, 'Project Beta', 150000.00, '2023-02-15', '2023-03-20', 'Completed', 'CN002', 'Product B', 'Remark for project Beta', '2023-02-20', 2, 145000.00, 5000.00, 125000.00, 20000.00, 20000.00, 85.30, 140000.00, 130000.00, 10000.00, 2, '2024-09-22 05:54:27', 2, '2024-09-22 05:54:27', 1),
(3, 'Project Gamma', 200000.00, '2023-03-05', '2023-04-10', 'On Hold', 'CN003', 'Product C', 'Remark for project Gamma', '2023-03-10', 3, 190000.00, 10000.00, 160000.00, 30000.00, 30000.00, 80.00, 185000.00, 175000.00, 10000.00, 3, '2024-09-22 05:54:27', 3, '2024-09-22 05:54:27', 2),
(4, 'Project Delta', 250000.00, '2023-04-01', '2023-05-01', 'Cancelled', 'CN004', 'Product D', 'Remark for project Delta', '2023-04-05', 4, 240000.00, 10000.00, 210000.00, 30000.00, 30000.00, 79.00, 235000.00, 225000.00, 10000.00, 4, '2024-09-22 05:54:27', 4, '2024-09-22 05:54:27', 3),
(5, 'Project Epsilon', 300000.00, '2023-05-15', '2023-06-15', 'In Progress', 'CN005', 'Product E', 'Remark for project Epsilon', '2023-05-20', 5, 285000.00, 15000.00, 240000.00, 45000.00, 45000.00, 82.00, 290000.00, 270000.00, 20000.00, 5, '2024-09-22 05:54:27', 5, '2024-09-22 05:54:27', 2),
(6, 'Project Zeta', 350000.00, '2023-06-10', '2023-07-10', 'Completed', 'CN006', 'Product F', 'Remark for project Zeta', '2023-06-15', 1, 330000.00, 20000.00, 290000.00, 40000.00, 40000.00, 81.00, 340000.00, 320000.00, 20000.00, 1, '2024-09-22 05:54:27', 1, '2024-09-22 05:54:27', 3),
(7, 'Project Eta', 400000.00, '2023-07-01', '2023-08-01', 'On Hold', 'CN007', 'Product G', 'Remark for project Eta', '2023-07-05', 2, 380000.00, 20000.00, 320000.00, 60000.00, 60000.00, 80.50, 390000.00, 370000.00, 20000.00, 2, '2024-09-22 05:54:27', 2, '2024-09-22 05:54:27', 1),
(8, 'Project Theta', 450000.00, '2023-08-05', '2023-09-05', 'Cancelled', 'CN008', 'Product H', 'Remark for project Theta', '2023-08-10', 3, 430000.00, 20000.00, 370000.00, 60000.00, 60000.00, 82.00, 440000.00, 420000.00, 20000.00, 3, '2024-09-22 05:54:27', 3, '2024-09-22 05:54:27', 4),
(9, 'Project Iota', 500000.00, '2023-09-01', '2023-10-01', 'In Progress', 'CN009', 'Product I', 'Remark for project Iota', '2023-09-05', 4, 480000.00, 20000.00, 410000.00, 70000.00, 70000.00, 81.50, 490000.00, 460000.00, 30000.00, 4, '2024-09-22 05:54:27', 4, '2024-09-22 05:54:27', 1),
(10, 'Project Kappa', 550000.00, '2023-10-10', '2023-11-10', 'Completed', 'CN010', 'Product J', 'Remark for project Kappa', '2023-10-15', 5, 520000.00, 30000.00, 450000.00, 70000.00, 70000.00, 81.00, 530000.00, 500000.00, 30000.00, 5, '2024-09-22 05:54:27', 5, '2024-09-22 05:54:27', 2),
(11, 'Project Lambda', 600000.00, '2023-11-01', '2023-12-01', 'On Hold', 'CN011', 'Product K', 'Remark for project Lambda', '2023-11-05', 1, 580000.00, 20000.00, 510000.00, 70000.00, 70000.00, 80.50, 590000.00, 560000.00, 30000.00, 1, '2024-09-22 05:54:27', 1, '2024-09-22 05:54:27', 3),
(12, 'Project Mu', 650000.00, '2023-12-10', '2024-01-10', 'In Progress', 'CN012', 'Product L', 'Remark for project Mu', '2023-12-15', 2, 630000.00, 20000.00, 540000.00, 90000.00, 90000.00, 82.00, 640000.00, 610000.00, 30000.00, 2, '2024-09-22 05:54:27', 2, '2024-09-22 05:54:27', 4),
(13, 'Project Nu', 700000.00, '2024-01-01', '2024-02-01', 'Completed', 'CN013', 'Product M', 'Remark for project Nu', '2024-01-05', 3, 680000.00, 20000.00, 580000.00, 100000.00, 100000.00, 82.50, 690000.00, 660000.00, 30000.00, 3, '2024-09-22 05:54:27', 3, '2024-09-22 05:54:27', 1),
(14, 'Project Xi', 750000.00, '2024-02-15', '2024-03-15', 'Cancelled', 'CN014', 'Product N', 'Remark for project Xi', '2024-02-20', 4, 720000.00, 30000.00, 620000.00, 100000.00, 100000.00, 81.00, 730000.00, 700000.00, 30000.00, 4, '2024-09-22 05:54:27', 4, '2024-09-22 05:54:27', 2),
(15, 'Project Omicron', 800000.00, '2024-03-05', '2024-04-05', 'In Progress', 'CN015', 'Product O', 'Remark for project Omicron', '2024-03-10', 5, 770000.00, 30000.00, 670000.00, 100000.00, 100000.00, 81.50, 780000.00, 750000.00, 30000.00, 5, '2024-09-22 05:54:27', 5, '2024-09-22 05:54:27', 3);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `team_leader` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_id`, `team_name`, `team_leader`) VALUES
(1, 'Innovation', NULL),
(2, 'Sales A', NULL),
(3, 'Service', 2),
(4, 'Point IT', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('Executive','Sale Supervisor','Seller','Engineer') NOT NULL,
  `team_id` int(11) NOT NULL,
  `position` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `email`, `role`, `team_id`, `position`, `phone`, `password`, `company`, `created_at`, `created_by`) VALUES
(1, 'Supachai', 'Bangpuk', 'Sale', 'ApirakSS@gmail.com', 'Seller', 2, 'IT', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:43:58', NULL),
(2, 'Apirak', 'Bangpuk', 'Admin', 'Apirak@gmail.com', 'Executive', 1, 'IT', '0839595800', '$2y$10$jcmTr.I9CthXOrWFC78XjuOjwPoZlbvF80M4RKow4RvnNbm1Ej8dO', 'PIT', '2024-09-15 16:43:58', NULL),
(3, 'Apirak', 'Bangpuk', 'Supervisor', 'apirak.ba@gmail.com', 'Sale Supervisor', 1, 'IT support', NULL, '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:43:58', NULL),
(4, 'Apirakt', 'Bangpuk', 'Support', 'apirakAA@gmail.com', 'Engineer', 3, 'IT Service', '0839595811', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:55:43', 2),
(5, 'Panit', 'Poapun', 'Panit', 'Panit@gmail.com', 'Executive', 4, 'Executive Director', '0839595822', '$2y$10$eAar02e4iaTG6bhKs2XLfua7ck.2co.8dkla8VX0tVCC5cnQfc/E6', 'PIT', '2024-09-17 15:15:37', 2),
(12, 'Rungnapa', 'Positakub', 'ying', 'Ying@gmail.com', 'Sale Supervisor', 3, 'Product Sale', '0839595888', '$2y$10$c7lOPwTFlqF/qsiFR/K1DuNPzfXae.PPsJ5O4NH2bIazwc8mWYsNq', 'PIT', '2024-09-17 15:26:14', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `seller` (`seller`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `team_leader` (`team_leader`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `team_id` (`team_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`seller`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`team_leader`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
