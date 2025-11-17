-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 02:45 PM
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
-- Database: `apartment_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `apartments`
--

CREATE TABLE `apartments` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `status` enum('vacant','occupied') DEFAULT 'vacant',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apartments`
--

INSERT INTO `apartments` (`id`, `owner_id`, `title`, `description`, `price`, `picture`, `status`, `created_at`) VALUES
(13, 3, 'room2', 'for rent', 1700.00, 'uploads/1762879357_rent2.jpg', 'vacant', '2025-11-11 16:42:37'),
(14, 3, 'room3', 'for rent', 3000.00, 'uploads/1762880406_rent3.jpg', 'vacant', '2025-11-11 17:00:06'),
(15, 3, 'Room1', 'for sale', 1500.00, 'uploads/1763105170_rent1.jpeg', 'occupied', '2025-11-14 07:26:10'),
(17, 3, 'room5', 'for rent', 15000.00, 'uploads/1763250676_rent5.jpg', 'vacant', '2025-11-15 23:51:16');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('due_reminder','owner_rental') NOT NULL,
  `message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `tenant_name` varchar(255) DEFAULT NULL,
  `apartment_id` int(11) NOT NULL,
  `paymongo_link_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('paid','failed') DEFAULT 'paid',
  `paymongo_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_extension` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `tenant_id`, `tenant_name`, `apartment_id`, `paymongo_link_id`, `amount`, `status`, `paymongo_reference`, `created_at`, `updated_at`, `is_extension`) VALUES
(21, 8, 'reinkyle', 13, 'link_11kWCvindRW2guXGLbhtzyhk', 1700.00, 'paid', NULL, '2025-11-17 10:34:28', '2025-11-17 10:34:28', 0),
(22, 8, 'reinkyle', 13, 'link_2CMK4TV9hf8vma7JMwDV2B5A', 1700.00, 'paid', NULL, '2025-11-17 10:35:14', '2025-11-17 10:35:14', 0),
(23, 10, 'kaykay', 15, 'link_XbDgYgvdpgYvcW7YKTodh8kb', 1500.00, 'paid', NULL, '2025-11-17 11:01:18', '2025-11-17 11:01:18', 0),
(24, 10, 'kaykay', 15, 'link_hraw8ZrYfJsajcTztUkDGx8V', 1500.00, 'paid', NULL, '2025-11-17 11:01:56', '2025-11-17 11:01:56', 0),
(25, 10, 'kaykay', 15, 'link_bYHYkKgT3RPpsmJxkzZhmFfL', 1500.00, 'paid', NULL, '2025-11-17 11:25:09', '2025-11-17 11:25:09', 0),
(26, 10, 'kaykay', 14, 'link_vVxngmN83ccEMgBRgNdzqVuz', 3000.00, 'paid', NULL, '2025-11-17 11:25:34', '2025-11-17 11:25:34', 0),
(27, 10, 'kaykay', 14, 'link_WV9CucdBvJ2yawTZpiwgj7kD', 3000.00, 'paid', NULL, '2025-11-17 11:26:13', '2025-11-17 11:26:13', 0),
(28, 10, 'kaykay', 14, 'link_akKkakCVArTYXUvVUaUf3TWX', 3000.00, 'paid', NULL, '2025-11-17 11:30:37', '2025-11-17 11:30:37', 0),
(29, 10, 'kaykay', 14, 'link_EZBG46jLE3UjXAjAYBRtLkEG', 3000.00, 'paid', NULL, '2025-11-17 11:31:13', '2025-11-17 11:31:13', 0),
(30, 10, 'kaykay', 15, 'link_frVcxATu23BMVJrUGD5jL5uG', 1500.00, 'paid', NULL, '2025-11-17 11:31:30', '2025-11-17 11:31:30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `apartment_id` int(11) NOT NULL,
  `status` enum('rented','paid') DEFAULT 'rented',
  `payment_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `apartment_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('active','paid','overdue') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `apartment_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('owner','tenant') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(3, 'rein', '$2y$10$tpIvucm8caMjxfe4ruyp.ualSX9tkd.4RISxg8PC42nwfW.2cKjji', 'admin@gmail.com', 'owner', '2025-11-11 08:18:51'),
(8, 'reinkyle', '$2y$10$INzywYzV8jLxeXJpzz0cpe.Mq/MTvqKFuzm7OLEwyTfkmcdzCGx32', 'preinkyle@gmail.com', 'tenant', '2025-11-11 13:36:15'),
(10, 'kaykay', '$2y$10$/CUyDEMn93.jTSJKcpcHouYTWzSH/pO1yO0XDB/H1bMvn2mMABlPC', 'tenant@gmail.com', 'tenant', '2025-11-11 16:58:57'),
(12, 'kyle', '$2y$10$sD5.uhHP3fDY20Z51mS.hO1zMoJbwtJbl9nG.7qu2CHkLVdnZgtqq', 'reinkyle082504@gmail.com', 'tenant', '2025-11-16 14:18:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apartments`
--
ALTER TABLE `apartments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `apartment_id` (`apartment_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `apartment_id` (`apartment_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `apartment_id` (`apartment_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_owner` (`owner_id`),
  ADD KEY `fk_tenant` (`tenant_id`),
  ADD KEY `fk_apartment` (`apartment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apartments`
--
ALTER TABLE `apartments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apartments`
--
ALTER TABLE `apartments`
  ADD CONSTRAINT `apartments_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `fk_apartment` FOREIGN KEY (`apartment_id`) REFERENCES `apartments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
