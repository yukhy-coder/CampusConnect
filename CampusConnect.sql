-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 10, 2025 at 04:18 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `CampusConnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `timestamp`, `logout_time`) VALUES
(58, 8, 'Login', '2025-12-05 00:20:35', '2025-12-05 00:20:49'),
(59, 14, 'Login', '2025-12-05 00:20:57', '2025-12-05 00:21:09'),
(60, 8, 'Login', '2025-12-05 00:21:14', '2025-12-05 00:21:36'),
(61, 14, 'Login', '2025-12-05 00:21:45', '2025-12-05 00:44:10'),
(62, 8, 'Login', '2025-12-05 00:44:16', NULL),
(63, 8, 'Login', '2025-12-05 16:13:30', '2025-12-05 16:13:54'),
(64, 8, 'Login', '2025-12-05 16:35:11', '2025-12-05 17:49:00'),
(65, 8, 'Login', '2025-12-05 18:04:33', NULL),
(66, 8, 'Login', '2025-12-09 23:09:18', '2025-12-09 23:09:47'),
(67, 8, 'Login', '2025-12-09 23:22:50', '2025-12-09 23:23:11'),
(68, 16, 'Login', '2025-12-09 23:23:15', '2025-12-09 23:30:23'),
(69, 8, 'Login', '2025-12-09 23:30:29', '2025-12-09 23:34:40'),
(70, 8, 'Login', '2025-12-09 23:36:09', '2025-12-09 23:37:20'),
(71, 8, 'Login', '2025-12-10 09:54:27', '2025-12-10 09:58:32'),
(72, 14, 'Login', '2025-12-10 09:58:40', NULL),
(73, 8, 'Login', '2025-12-10 10:54:01', '2025-12-10 11:11:59'),
(74, 8, 'Login', '2025-12-10 11:12:10', '2025-12-10 11:12:32'),
(75, 8, 'Login', '2025-12-10 11:13:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `anonymousmessages`
--

CREATE TABLE `anonymousmessages` (
  `message_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `attachment_link` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `is_favorite` tinyint(1) DEFAULT 0,
  `is_archived` tinyint(1) DEFAULT 0,
  `is_reviewed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anonymousmessages`
--

INSERT INTO `anonymousmessages` (`message_id`, `message_text`, `tags`, `attachment_link`, `ip_address`, `created_at`, `is_read`, `is_favorite`, `is_archived`, `is_reviewed`) VALUES
(25, 'read', '', NULL, '::1', '2025-12-03 22:59:24', 0, 0, 0, 1),
(26, 'favorite', '', NULL, '::1', '2025-12-03 22:59:36', 1, 0, 0, 0),
(27, 'archived', '', NULL, '::1', '2025-12-03 22:59:52', 0, 1, 0, 0),
(28, 'reviewed', '', NULL, '::1', '2025-12-03 23:00:05', 0, 1, 0, 0),
(29, 'hiiii', '', NULL, '::1', '2025-12-04 11:07:26', 0, 0, 0, 0),
(30, 'hiii', '', NULL, '::1', '2025-12-04 11:30:51', 0, 0, 0, 0),
(31, 'asda', '', NULL, '::1', '2025-12-04 11:31:12', 0, 0, 0, 0),
(32, 'asdas', '', NULL, '::1', '2025-12-04 11:31:15', 1, 0, 0, 0),
(33, 'd', '', NULL, '::1', '2025-12-04 11:35:49', 0, 1, 0, 0),
(34, 's', '', NULL, '::1', '2025-12-04 11:35:52', 0, 0, 0, 1),
(35, 'ds', '', NULL, '::1', '2025-12-04 11:35:54', 0, 0, 1, 0),
(36, 'dad', '', NULL, '::1', '2025-12-04 11:35:57', 0, 1, 0, 0),
(37, 'dssad', '', NULL, '::1', '2025-12-04 11:36:00', 1, 0, 0, 0),
(38, 'asdas', '', NULL, '::1', '2025-12-04 11:40:42', 0, 1, 0, 0),
(39, 'gwapo ko', '', '1764826318_0_IMG_8233.JPG,1764826318_1_IMG_8234.JPG,1764826318_2_IMG_8251.JPG,1764826318_3_IMG_8253.JPG', '::1', '2025-12-04 13:31:58', 0, 0, 0, 0),
(40, 'adsas #Complaint ', 'Complaint', NULL, '::1', '2025-12-04 16:58:28', 0, 0, 0, 0),
(41, 'asdaasa #Concern ', 'Concern', NULL, '::1', '2025-12-04 16:58:33', 0, 0, 0, 0),
(42, 'dadsw #Query ', 'Query', NULL, '::1', '2025-12-04 16:58:38', 1, 0, 0, 0),
(43, 'kuan #Complaint ', 'Complaint', NULL, '::1', '2025-12-04 18:31:08', 0, 0, 0, 0),
(44, 'bayottt', '', NULL, '::1', '2025-12-04 19:16:54', 0, 0, 0, 0),
(45, 'bayott', '', NULL, '::1', '2025-12-04 19:17:02', 0, 0, 0, 0),
(46, 'okay kayow', '', NULL, '::1', '2025-12-04 19:47:32', 0, 0, 0, 0),
(47, 'kwee #Concern #Complaint #Query ', 'Concern,Complaint,Query', NULL, '::1', '2025-12-04 19:47:56', 0, 0, 0, 0),
(48, 'Goodmorning', '', NULL, '::1', '2025-12-05 00:27:52', 0, 0, 0, 0),
(49, 'adas', '', NULL, '::1', '2025-12-09 23:42:53', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_date`, `title`, `description`, `created_at`, `user_id`) VALUES
(18, '2025-12-11', 'Pasko sa CTU', '', '2025-12-10 09:58:26', 8),
(19, '2025-12-25', 'Pasko', '', '2025-12-10 09:58:53', 14);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` enum('message','event') NOT NULL,
  `text` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `text`, `is_read`, `created_at`, `user_id`) VALUES
(2, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:07:26', NULL),
(5, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:30:51', NULL),
(6, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:31:12', NULL),
(7, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:31:15', NULL),
(8, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:35:49', NULL),
(9, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:35:52', NULL),
(10, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:35:54', NULL),
(11, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:35:57', NULL),
(12, 'message', 'You have a new unread post. Tap to navigate!', 0, '2025-12-04 11:36:00', NULL),
(19, 'event', 'Villegas Khyan Earl G. added event: Pasko sa CTU', 0, '2025-12-10 09:58:26', 8),
(20, 'event', 'Machica, John Lester M. added event: Pasko', 0, '2025-12-10 09:58:53', 14);

-- --------------------------------------------------------

--
-- Table structure for table `passwordreset`
--

CREATE TABLE `passwordreset` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passwordreset`
--

INSERT INTO `passwordreset` (`id`, `user_email`, `otp_code`, `expires_at`, `created_at`) VALUES
(12, 'villegaskhyan@gmail.com', '5803', '2025-12-05 10:52:37', '2025-12-05 17:52:37'),
(13, 'villegaskhyan@gmail.com', '9684', '2025-12-09 16:38:09', '2025-12-09 23:38:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `birthday` date DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `role` enum('admin','officer') NOT NULL DEFAULT 'officer',
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `email`, `birthday`, `profile_pic`, `password`, `position`, `role`, `is_approved`, `created_at`, `approved_at`, `last_login`) VALUES
(8, 'Villegas Khyan Earl G.', 'villegaskhyanearl@gmail.com', '2005-04-20', '1764847134_pfp_Gemini_Generated_Image_tqt360tqt360tqt3.png', '$2y$10$RmqhANJStVb.kKPT5Ws6Xech5L4aFcBitJ2pxQ1ZuVxueP28Ht022', 'DOCUMENTATION OFFICER', 'admin', 1, '2025-12-04 05:58:10', '2025-12-04 20:20:03', '2025-12-10 11:13:06'),
(14, 'Machica, John Lester M.', 'laxusmachica@gmail.com', NULL, NULL, '$2y$10$75S3.pnRqqwsJJcEXZGRuOJB37NwU581dz7D63KwcDC2x1abgOZ2i', 'LOGISTICS COMMITTEE', 'admin', 1, '2025-12-04 22:24:10', '2025-12-04 22:25:17', '2025-12-10 09:58:40'),
(16, 'Villegas Khyan Earl G.', 'villegaskhyan@gmail.com', NULL, NULL, '$2y$10$ZsKnEyT0WvBOTXuv72AMbuJhP31cD2I50mVv0CjMZ/8Het4Rm8.BK', 'DOCUMENTATION OFFICER', 'officer', 1, '2025-12-05 16:14:13', '2025-12-09 23:23:04', '2025-12-09 23:23:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_activity_logs` (`user_id`);

--
-- Indexes for table `anonymousmessages`
--
ALTER TABLE `anonymousmessages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_events_users` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notif_users` (`user_id`);

--
-- Indexes for table `passwordreset`
--
ALTER TABLE `passwordreset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `anonymousmessages`
--
ALTER TABLE `anonymousmessages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `passwordreset`
--
ALTER TABLE `passwordreset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `passwordreset`
--
ALTER TABLE `passwordreset`
  ADD CONSTRAINT `passwordreset_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
