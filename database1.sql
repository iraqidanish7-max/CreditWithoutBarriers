-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2025 at 10:21 PM
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
-- Database: `database1`
--

-- --------------------------------------------------------

--
-- Table structure for table `customerdetails`
--

CREATE TABLE `customerdetails` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `aadhar` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `loanreq` varchar(50) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customerdetails`
--

INSERT INTO `customerdetails` (`id`, `name`, `aadhar`, `mobile`, `email`, `loanreq`, `status`, `created_at`) VALUES
(1, 'Mantasir', '376217663365', '9021751570', 'mantasir@gmail.com', '50000', 'approved', '2025-11-20 16:01:34'),
(2, 'Tom', '376217663365', '9021751570', 'tom@gmail.com', '200000', 'pending', '2025-11-20 16:21:15'),
(3, 'Harry', '376217663365', '9021751570', 'harry@gmail.com', '90000', 'pending', '2025-11-20 16:22:06'),
(4, 'Aabid', '376217663365', '9021751570', 'aabid@gmail.com', '100000', 'pending', '2025-11-20 16:49:34'),
(5, 'Yusuf', '376217663365', '9021751570', 'yusuf@gmail.com', '50000', 'pending', '2025-11-21 04:46:46'),
(6, 'Tom', '376217663365', '9021751570', 'tom@gmail.com', '50000', 'pending', '2025-11-21 10:04:58'),
(7, 'john', '376217663365', '9021751570', 'john@gmail.com', '90000', 'pending', '2025-11-21 10:23:55'),
(8, 'Sanket', '376217663365', '9021751570', 'sanket@gmail.com', '400000', 'rejected', '2025-11-21 11:26:28'),
(9, 'Danish Iraqi', '376217663365', '9021751570', 'iraqidanish7@gmail.com', '200000', 'pending', '2025-11-21 14:02:51'),
(10, 'Danish Iraqi', '376217663365', '9021751570', 'iraqidanish7@gmail.com', '100000', 'approved', '2025-11-21 14:51:15'),
(11, 'Tom', '376217663365', '9021751570', 'tom@gmail.com', '150000', 'approved', '2025-11-21 17:05:24'),
(12, 'marry', '376217663365', '9021751570', 'marry@gmail.com', '100000', 'approved', '2025-11-21 19:17:06');

-- --------------------------------------------------------

--
-- Table structure for table `loan_offers`
--

CREATE TABLE `loan_offers` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `lender_name` varchar(100) NOT NULL,
  `lender_email` varchar(100) NOT NULL,
  `offer_amount` decimal(10,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `tenure_months` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected','withdrawn') NOT NULL DEFAULT 'pending',
  `admin_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_offers`
--

INSERT INTO `loan_offers` (`id`, `request_id`, `lender_name`, `lender_email`, `offer_amount`, `interest_rate`, `tenure_months`, `status`, `admin_status`, `created_at`) VALUES
(1, 2, 'Brad', 'brad@gmail.com', 200000.00, 12.00, 36, 'rejected', 'pending', '2025-11-20 16:25:24'),
(2, 2, 'Aabid', 'aabid@gmail.com', 200000.00, 13.00, 36, 'accepted', 'pending', '2025-11-20 16:52:35'),
(3, 4, 'shaban', 'shaban@gmail.com', 90000.00, 8.00, 12, 'pending', 'approved', '2025-11-20 19:10:08'),
(4, 4, 'shaban', 'shaban@gmail.com', 90000.00, 8.00, 12, 'pending', 'pending', '2025-11-20 19:11:54'),
(5, 2, 'shaban', 'shaban@gmail.com', 180000.00, 13.00, 36, 'accepted', 'pending', '2025-11-20 19:19:46'),
(6, 7, 'Aamir', 'aamir@gmail.com', 90000.00, 12.00, 24, 'rejected', 'pending', '2025-11-21 10:25:11'),
(7, 7, 'Nawaz', 'nawaz@gmail.com', 100000.00, 9.00, 18, 'accepted', 'pending', '2025-11-21 10:28:42'),
(8, 8, 'Aamir', 'aamir@gmail.com', 400000.00, 10.00, 48, 'accepted', 'pending', '2025-11-21 11:31:01'),
(9, 9, 'Nawaz', 'nawaz@gmail.com', 200000.00, 7.00, 48, 'accepted', 'pending', '2025-11-21 14:06:12'),
(10, 6, 'Aamir', 'aamir@gmail.com', 50000.00, 11.00, 24, 'pending', 'rejected', '2025-11-21 14:48:01'),
(11, 10, 'Aamir', 'aamir@gmail.com', 100000.00, 9.00, 12, 'accepted', 'pending', '2025-11-21 14:53:48'),
(12, 5, 'Aamir', 'aamir@gmail.com', 50000.00, 9.00, 24, 'accepted', 'pending', '2025-11-21 16:22:28'),
(13, 3, 'Nawaz', 'nawaz@gmail.com', 90000.00, 8.00, 12, 'accepted', 'approved', '2025-11-21 16:40:26'),
(14, 11, 'Nawaz', 'nawaz@gmail.com', 150000.00, 9.00, 12, 'accepted', 'approved', '2025-11-21 17:50:40'),
(15, 12, 'Nawaz', 'nawaz@gmail.com', 100000.00, 6.00, 12, 'accepted', 'approved', '2025-11-21 19:18:53');

-- --------------------------------------------------------

--
-- Table structure for table `loan_requests`
--

CREATE TABLE `loan_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('open','offered','accepted','funded','rejected','closed') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `lender_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','accepted','rejected','withdrawn') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offer_otps`
--

CREATE TABLE `offer_otps` (
  `id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offer_otps`
--

INSERT INTO `offer_otps` (`id`, `offer_id`, `user_id`, `otp_code`, `expires_at`, `is_used`, `created_at`) VALUES
(1, 7, 24, '276661', '2025-11-21 14:45:27', 1, '2025-11-21 13:35:27'),
(2, 7, 24, '230775', '2025-11-21 14:53:25', 1, '2025-11-21 13:43:25'),
(3, 7, 24, '514434', '2025-11-21 14:55:05', 1, '2025-11-21 13:45:05'),
(4, 9, 24, '458469', '2025-11-21 15:21:45', 1, '2025-11-21 14:11:45'),
(5, 9, 24, '588131', '2025-11-21 15:43:49', 1, '2025-11-21 14:33:49'),
(6, 10, 2, '511365', '2025-11-21 15:59:52', 0, '2025-11-21 14:49:52'),
(7, 11, 24, '836644', '2025-11-21 16:04:52', 1, '2025-11-21 14:54:52'),
(8, 11, 24, '422411', '2025-11-21 16:09:12', 1, '2025-11-21 14:59:12'),
(9, 11, 24, '443486', '2025-11-21 16:09:57', 1, '2025-11-21 14:59:57'),
(10, 11, 24, '822725', '2025-11-21 17:28:25', 1, '2025-11-21 16:18:25'),
(11, 12, 21, '818587', '2025-11-21 17:33:13', 1, '2025-11-21 16:23:13'),
(12, 13, 3, '243122', '2025-11-21 17:51:05', 1, '2025-11-21 16:41:05'),
(13, 14, 2, '454439', '2025-11-21 19:15:20', 1, '2025-11-21 18:05:20'),
(14, 15, 5, '165547', '2025-11-21 20:31:37', 1, '2025-11-21 19:21:37'),
(15, 15, 5, '560410', '2025-11-21 20:31:50', 1, '2025-11-21 19:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `offer_id` int(11) DEFAULT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `tx_type` enum('disburse','repayment') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('borrower','lender','admin') NOT NULL DEFAULT 'borrower',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `created_at`, `role`, `is_active`) VALUES
(2, 'Tom', 'tom@gmail.com', '9021751570', '$2y$10$yDl6siKV9lEIPHe.mAf..uDrlsFJ/zJinfg7AijRSCoCQLvw.fnxu', '2025-11-19 13:21:22', 'borrower', 1),
(3, 'Harry', 'harry@gmail.com', '9021751570', '$2y$10$IZELhxKt9S03kDQoM2LetOcU/TspdfXUMaL20Ifb8VLK0OiuhQMre', '2025-11-19 13:37:17', 'borrower', 1),
(4, 'Jerry', 'jerry@gmail.com', '9021751570', '$2y$10$09rOoph37vfHLdzBu.DoyOQfPItkGWR7ihKIZXTRZ5Q052B5LDOBi', '2025-11-19 13:48:00', 'borrower', 1),
(5, 'marry', 'marry@gmail.com', '9021751570', '$2y$10$OE3VCBB19TQ74bXI5rslse4wr07U.ZNlereTuaCL/t5aySX.ORWUq', '2025-11-19 15:18:51', 'borrower', 1),
(6, 'john', 'john@gmail.com', '9021751570', '$2y$10$eS1g9WgVzpvEYCmaGNTd3eLNGXtSIp.xzfDDV1uPwfQ7lPkTqBb.G', '2025-11-19 15:28:00', 'borrower', 1),
(8, 'Alex', 'alex@gmail.com', '9876543210', '$2y$10$RytBf1y55H8AGKcBx8aeBu.kz1DKhkMACR7oxPFW2cE3ASATfyuWS', '2025-11-19 16:11:06', 'borrower', 1),
(9, 'David', 'david@gmail.com', '8765432910', '$2y$10$JWSCfhWS5zSGBCwSAh5Y9.ylUC3ccTZPYZ8tx9wqQ/Kt4NyI3e7O2', '2025-11-19 16:34:03', 'borrower', 1),
(10, 'nishat', 'nishat@gmail.com', '4634356542', '$2y$10$H3554MAsqvXxUDGvEuYjjuAuvYxf2vU14zBCdywR4kWWeW6IlF59K', '2025-11-20 03:18:12', 'borrower', 1),
(11, 'Rehan', 'rehan@gmail.com', '7620336684', '$2y$10$LpOqLz/zRSN4.lFJyJFas.vhep1aRNOTQlGfu5vlswIOc8tuy2bXy', '2025-11-20 03:36:17', 'borrower', 1),
(12, 'usman', 'usman@gmail.com', '9876543219', '$2y$10$Q3OrkVMkybwCkS2gySb62utklIvd.7cHnYQ0XdW2.O.5UvbXHYXqm', '2025-11-20 04:47:16', 'borrower', 1),
(13, 'Mantasir', 'mantasir@gmail.com', '9021751570', '$2y$10$IRQ2X5bMXhEeBpMnlA8oveW8EXjcFBu4y/X9gZU4Ltj8c3s.DTtyG', '2025-11-20 15:47:23', 'borrower', 1),
(14, 'Aabid', 'aabid@gmail.com', '9021751570', '$2y$10$R8aZzzgHrkx8DrVOhPIazOPEcOxPT8QruAxI8KgcTKQk5vCe75R0y', '2025-11-20 16:48:45', 'borrower', 1),
(15, 'Rizwan', 'rizwan@gmail.com', '9021751570', '$2y$10$2xC1cE5AgzKMiZALFD8I4.1CFZkpZSdCspPay83EQb3cNLEG7Ybpe', '2025-11-20 18:40:40', 'borrower', 1),
(16, 'Shaban', 'shaban@gmail.com', '9021751570', '$2y$10$nnmD9m4xYf6mLIag6bU7zePoWrEfDso2EleSGWpYuqLVTHokhN4jK', '2025-11-20 18:42:11', 'lender', 1),
(17, 'Nawaz', 'nawaz@gmail.com', '09021751570', '$2y$10$KSosW1wW5JWcU7gsCgjJOeNg.xAG8yHvfdamFV44rMSzesT4W//nm', '2025-11-20 18:53:02', 'lender', 1),
(18, 'Kashfi', 'kashfi@gmail.com', '9021751570', '$2y$10$rIbYjurHVZw0LLAosXFLR.GEGnatL/rjlTPMt0Ghjop4UOgB3jvHm', '2025-11-20 18:54:57', 'borrower', 1),
(20, 'Admin User', 'admin@example.com', '8888899991', '$2y$10$46LXOUWInNdix9kUCox/JOvq7zoLev06oHP1Wqi2jfH0tkPOYBlqa', '2025-11-20 19:58:14', 'admin', 1),
(21, 'Yusuf', 'yusuf@gmail.com', '9021751570', '$2y$10$fURjDlzCRWCGnK/om1KNr.5yEG2IZksW0vxyIY6Hts22Ujr3Y1Ux6', '2025-11-21 04:45:47', 'borrower', 1),
(22, 'Aamir', 'aamir@gmail.com', '9021751570', '$2y$10$HOdoXthdtC2SYmE3PrHxZ.BxZ5IRCGtR215kG.rwXnWR0kfonrSAS', '2025-11-21 04:49:25', 'lender', 1),
(23, 'Sanket', 'sanket@gmail.com', '9021751570', '$2y$10$ielGGH7CdxCUG8inxWVyaeYQyyWTfPknVJ3UJaiQbyf9tbSyTF4Fe', '2025-11-21 11:25:18', 'borrower', 1),
(24, 'Danish Iraqi', 'iraqidanish7@gmail.com', '9021751570', '$2y$10$K0bw.9zeC.xcNobTk5N0IupvbYKEmlkZLRIrU1GjZWoA/Fk11MGIy', '2025-11-21 13:21:57', 'borrower', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customerdetails`
--
ALTER TABLE `customerdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_offers`
--
ALTER TABLE `loan_offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_loan_offers_request` (`request_id`);

--
-- Indexes for table `loan_requests`
--
ALTER TABLE `loan_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`),
  ADD KEY `lender_id` (`lender_id`);

--
-- Indexes for table `offer_otps`
--
ALTER TABLE `offer_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_offer_otps_offer` (`offer_id`),
  ADD KEY `fk_offer_otps_user` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offer_id` (`offer_id`),
  ADD KEY `loan_id` (`loan_id`);

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
-- AUTO_INCREMENT for table `customerdetails`
--
ALTER TABLE `customerdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `loan_offers`
--
ALTER TABLE `loan_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `loan_requests`
--
ALTER TABLE `loan_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offer_otps`
--
ALTER TABLE `offer_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loan_offers`
--
ALTER TABLE `loan_offers`
  ADD CONSTRAINT `fk_loan_offers_request` FOREIGN KEY (`request_id`) REFERENCES `customerdetails` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_requests`
--
ALTER TABLE `loan_requests`
  ADD CONSTRAINT `loan_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`lender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offer_otps`
--
ALTER TABLE `offer_otps`
  ADD CONSTRAINT `fk_offer_otps_offer` FOREIGN KEY (`offer_id`) REFERENCES `loan_offers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_offer_otps_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`loan_id`) REFERENCES `loan_requests` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
