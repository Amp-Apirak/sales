-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2024 at 06:49 PM
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
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(1, 'Sale 1', 'Bangpuk', 'Sale', 'ApirakSS@gmail.com', 'Seller', 2, 'IT', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:43:58', NULL),
(2, 'Apirak', 'Bangpuk', 'Admin', 'Apirak@gmail.com', 'Executive', 1, 'IT', '0839595800', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:43:58', NULL),
(3, 'Apirak', 'Bangpuk', 'Supervisor', 'apirak.ba@gmail.com', 'Sale Supervisor', 1, 'IT support', NULL, '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:43:58', NULL),
(4, 'Apirak', 'Bangpuk', 'Support', 'apirakAA@gmail.com', 'Engineer', 3, 'IT Service', '0839595811', '$2y$10$AFDgtICvjsQ6EkPk.cUizOTf1HE1bCnBJXsLtCjJy7WijtNWTQsji', 'PIT', '2024-09-15 16:55:43', 2),
(5, 'Panit', 'Poapun', 'Panit', 'Panit@gmail.com', 'Executive', 4, 'Executive Director', '0839595822', '$2y$10$eAar02e4iaTG6bhKs2XLfua7ck.2co.8dkla8VX0tVCC5cnQfc/E6', 'PIT', '2024-09-17 15:15:37', 2),
(12, 'Ying', 'Positakub', 'ying', 'Ying@gmail.com', 'Sale Supervisor', 3, 'Product Sale', '0839595888', '$2y$10$c7lOPwTFlqF/qsiFR/K1DuNPzfXae.PPsJ5O4NH2bIazwc8mWYsNq', 'PIT', '2024-09-17 15:26:14', 2);

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
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

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
