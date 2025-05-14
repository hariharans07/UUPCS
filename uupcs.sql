-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 08:08 AM
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
-- Database: `uupcs`
--

-- --------------------------------------------------------

--
-- Table structure for table `collaborations`
--

CREATE TABLE `collaborations` (
  `id` int(11) NOT NULL,
  `scheme1_id` int(11) DEFAULT NULL,
  `scheme2_id` int(11) DEFAULT NULL,
  `initiator_ceo_id` int(11) DEFAULT NULL,
  `receiver_ceo_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `total_quantity` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `name`, `total_quantity`, `department`, `created_at`, `updated_at`) VALUES
(1, 'Cement Bags', 350, 'water', '2025-04-16 03:39:58', '2025-04-16 09:27:59'),
(2, 'Cement Bags', 400, 'road', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(3, 'Steel Rods', 200, 'water', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(4, 'Steel Rods', 250, 'road', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(5, 'Bricks', 5000, 'road', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(6, 'Bricks', 4500, 'water', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(7, 'Sand', 1500, 'road', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(8, 'Sand', 1800, 'water', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(9, 'Paint Buckets', 100, 'road', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(10, 'Paint Buckets', 80, 'water', '2025-04-16 03:39:58', '2025-04-16 03:39:58'),
(11, 'shovels', 50, 'water', '2025-04-16 04:03:32', '2025-04-16 04:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `resource_requests`
--

CREATE TABLE `resource_requests` (
  `id` int(11) NOT NULL,
  `engineer_id` int(11) DEFAULT NULL,
  `scheme_id` int(11) DEFAULT NULL,
  `collaboration_id` int(11) DEFAULT NULL,
  `taskid` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `requested_quantity` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schemes`
--

CREATE TABLE `schemes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `department` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `assigned_engineer_id` int(11) DEFAULT NULL,
  `startdate` date NOT NULL,
  `deadline` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `status` enum('ongoing','completed','collaborated') DEFAULT NULL,
  `created_by_ceo_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `scheme_id` int(11) DEFAULT NULL,
  `collaboration_id` int(11) DEFAULT NULL,
  `engineer_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `role` enum('ceo','engineer') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `dob`, `mobile`, `photo`, `details`, `department`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Ravi Kumar', 'ravi.ceo@example.com', 'ceopass123', '1980-05-10', '9876543210', 'ravi.jpg', 'CEO of Water Department', 'water', 'ceo', '2025-04-07 04:26:59', '2025-04-07 04:26:59'),
(2, 'Anita Sharma', 'anita.ceo@example.com', 'ceopass456', '1978-09-22', '9876501234', 'anita.jpg', 'CEO of Road Department', 'road', 'ceo', '2025-04-07 04:26:59', '2025-04-07 04:26:59'),
(3, 'Vikram Patel', 'vikram.eng@example.com', '1294', '1990-01-16', '9123456787', 'vikram.jpg', 'Water department engineer', 'water', 'engineer', '2025-04-07 04:26:59', '2025-04-29 04:06:37'),
(4, 'Neha Singh', 'neha.eng@example.com', 'engpass456', '1992-08-30', '9234567890', 'neha.jpg', 'Road department structural engineer', 'road', 'engineer', '2025-04-07 04:26:59', '2025-04-07 04:26:59'),
(5, 'Ajay Verma', 'ajay.eng@example.com', 'engpass789', '1988-12-05', '9345678901', 'ajay.jpg', 'Water project site engineer', 'water', 'engineer', '2025-04-07 04:26:59', '2025-04-07 04:26:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `collaborations`
--
ALTER TABLE `collaborations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scheme1_id` (`scheme1_id`),
  ADD KEY `scheme2_id` (`scheme2_id`),
  ADD KEY `initiator_ceo_id` (`initiator_ceo_id`),
  ADD KEY `receiver_ceo_id` (`receiver_ceo_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resource_requests`
--
ALTER TABLE `resource_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `engineer_id` (`engineer_id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `resource_requests_ibfk_3` (`collaboration_id`);

--
-- Indexes for table `schemes`
--
ALTER TABLE `schemes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_engineer_id` (`assigned_engineer_id`),
  ADD KEY `created_by_ceo_id` (`created_by_ceo_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `engineer_id` (`engineer_id`),
  ADD KEY `tasks_ibfk_3` (`collaboration_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collaborations`
--
ALTER TABLE `collaborations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `resource_requests`
--
ALTER TABLE `resource_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schemes`
--
ALTER TABLE `schemes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `collaborations`
--
ALTER TABLE `collaborations`
  ADD CONSTRAINT `collaborations_ibfk_1` FOREIGN KEY (`scheme1_id`) REFERENCES `schemes` (`id`),
  ADD CONSTRAINT `collaborations_ibfk_2` FOREIGN KEY (`scheme2_id`) REFERENCES `schemes` (`id`),
  ADD CONSTRAINT `collaborations_ibfk_3` FOREIGN KEY (`initiator_ceo_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `collaborations_ibfk_4` FOREIGN KEY (`receiver_ceo_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `resource_requests`
--
ALTER TABLE `resource_requests`
  ADD CONSTRAINT `resource_requests_ibfk_1` FOREIGN KEY (`engineer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `resource_requests_ibfk_2` FOREIGN KEY (`scheme_id`) REFERENCES `schemes` (`id`),
  ADD CONSTRAINT `resource_requests_ibfk_3` FOREIGN KEY (`collaboration_id`) REFERENCES `collaborations` (`id`);

--
-- Constraints for table `schemes`
--
ALTER TABLE `schemes`
  ADD CONSTRAINT `schemes_ibfk_1` FOREIGN KEY (`assigned_engineer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `schemes_ibfk_2` FOREIGN KEY (`created_by_ceo_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`scheme_id`) REFERENCES `schemes` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`engineer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`collaboration_id`) REFERENCES `collaborations` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
