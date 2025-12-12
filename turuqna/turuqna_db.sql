-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 11, 2025 at 03:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `turuqna_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Your report #10 has been updated to: Resolved', 1, '2025-12-11 09:05:19'),
(2, 1, 'Your report #10 has been updated to: Resolved', 0, '2025-12-11 10:42:26'),
(3, 1, 'Your report #9 has been updated to: In Progress', 0, '2025-12-11 10:42:32'),
(4, 1, 'Your report #10 has been updated to: In Progress', 0, '2025-12-11 11:09:33');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `status` enum('Pending','In Progress','Resolved') DEFAULT 'Pending',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `user_id`, `description`, `latitude`, `longitude`, `status`, `image_path`, `created_at`) VALUES
(1, 1, 'road is stock for 1 hour no moving', 25.38649411, 49.56874609, 'Pending', 'uploads/1765383121_traffic congestion.jpeg', '2025-12-10 16:12:01'),
(2, 1, 'road is stock for 1 hour no moving', 25.38649411, 49.56874609, 'Pending', 'uploads/1765395894_traffic congestion.jpeg', '2025-12-10 19:44:54'),
(3, 1, 'cvcvbv', 24.83258021, 46.68886536, 'Pending', 'uploads/1765395992_cam.png', '2025-12-10 19:46:32'),
(4, 1, 'cvcvbv', 24.83258021, 46.68886536, 'Pending', 'uploads/1765396560_cam.png', '2025-12-10 19:56:00'),
(5, 1, 'cvcvbv', 24.83258021, 46.68886536, 'Pending', 'uploads/1765396719_cam.png', '2025-12-10 19:58:39'),
(6, 1, 'cvcvbv', 24.83258021, 46.68886536, 'Pending', 'uploads/1765396781_cam.png', '2025-12-10 19:59:41'),
(7, 1, 'cvcvbv', 24.83258021, 46.68886536, 'Pending', 'uploads/1765398982_cam.png', '2025-12-10 20:36:22'),
(8, 1, 'cvcvbv', 24.83258021, 46.68886536, 'Pending', 'uploads/1765399342_cam.png', '2025-12-10 20:42:22'),
(9, 1, 'dhgcdhcd', 24.83258113, 46.68886158, 'In Progress', 'uploads/1765401538_traffic congestion.jpeg', '2025-12-10 21:18:58'),
(10, 1, 'udf', 24.83436450, 46.73038960, 'In Progress', 'uploads/1765439626_logo[1].png', '2025-12-11 07:53:46'),
(14, 1, 'Severe accident causing delay', 26.21720000, 50.19710000, 'Pending', NULL, '2025-12-11 14:02:25'),
(15, 1, 'Road construction work', 26.22000000, 50.20000000, 'In Progress', NULL, '2025-12-11 14:02:25'),
(16, 1, 'Traffic flow returned to normal', 26.21500000, 50.19500000, 'Resolved', NULL, '2025-12-11 14:02:25'),
(17, 1, 'Heavy traffic jam on King Fahd Road near Kingdom Centre', 24.71360000, 46.67530000, 'Pending', NULL, '2025-12-11 14:25:43'),
(18, 1, 'Car accident blocking two lanes', 24.72360000, 46.68530000, 'Pending', NULL, '2025-12-11 14:25:43'),
(19, 1, 'Road construction on Northern Ring Road', 24.75500000, 46.65000000, 'In Progress', NULL, '2025-12-11 14:25:43'),
(20, 1, 'Minor slowdown near Olaya Street', 24.69500000, 46.68000000, 'In Progress', NULL, '2025-12-11 14:25:43'),
(21, 1, 'Traffic cleared on Eastern Ring Road', 24.78000000, 46.72000000, 'Resolved', NULL, '2025-12-11 14:25:43'),
(22, 1, 'Vehicle breakdown on Makkah Road', 24.66000000, 46.70000000, 'Pending', NULL, '2025-12-11 14:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_number` varchar(20) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Citizen','TrafficOfficer','Admin') DEFAULT 'Citizen',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `id_number`, `phone_number`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'reem almousa', 'reemalmouss@gmail.com', '1120137433', '0508067503', '$2y$10$WW.adDGIFaGkWPdezYFDZ.STJiAPdb.Wjv19AOh3KiJRdpNKbggPq', 'Citizen', '2025-12-10 15:09:09', '2025-12-10 22:47:10'),
(6, 'Officer Ahmed', 'officer@turuqna.com', '2000000001', '0500000001', '$2y$10$1pAxnCF7eOCbCpSDqENzjO9PPcoiGO89NFyNJCW4LCrYaNVctvW.2', 'TrafficOfficer', '2025-12-10 21:31:58', '2025-12-10 21:46:44'),
(7, 'Admin System', 'admin@turuqna.com', '3000000001', '0500000002', '$2y$10$1pAxnCF7eOCbCpSDqENzjO9PPcoiGO89NFyNJCW4LCrYaNVctvW.2', 'Admin', '2025-12-10 21:31:58', '2025-12-10 21:46:44'),
(8, 'ahmad', 'Ahmad@gmail.com', '123456', '050897655', '$2y$10$oyUurAvWRc4xarSu02T3Ye5ob4Wdbhspl2VTJArPllgEiLrDsYPSu', 'TrafficOfficer', '2025-12-10 22:04:11', '2025-12-10 22:04:11'),
(9, 'reem', '2220002025@iau.edu.sa', '11203894', '940895', '$2y$10$IL9MfziIjWCNR9J/spG9eOs3Al/QClRCCNIsw955mDB259EKDRvnW', 'Citizen', '2025-12-11 09:04:05', '2025-12-11 09:04:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
