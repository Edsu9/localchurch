-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 02:32 PM
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
-- Database: `church_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `donation_date` date NOT NULL,
  `donation_type` enum('tithe','offering','project','other') NOT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `purpose` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `category` varchar(255) NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `tax_deductible` tinyint(1) NOT NULL DEFAULT 0,
  `campaign` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `receipt_file` varchar(255) DEFAULT NULL,
  `is_recurring` tinyint(1) DEFAULT 0,
  `receipt_number` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `member_id`, `amount`, `donation_date`, `donation_type`, `notes`, `recorded_by`, `created_at`, `purpose`, `payment_method`, `category`, `reference_number`, `tax_deductible`, `campaign`, `updated_at`, `receipt_file`, `is_recurring`, `receipt_number`, `date`) VALUES
(11, 36, 1500.00, '2025-03-04', 'tithe', 'May God bless u :)', NULL, '2025-03-15 15:15:36', NULL, 'Cash', 'Offering', '', 0, 'Building Fund', '2025-03-15 15:15:36', NULL, 0, '', '2025-03-24 13:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_name` varchar(255) NOT NULL,
  `end_time` time DEFAULT NULL,
  `event_type` varchar(255) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `max_attendees` int(11) DEFAULT NULL,
  `registration_required` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `event_image` varchar(255) NOT NULL,
  `start_time` time DEFAULT NULL,
  `organizer` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `public_display` tinyint(1) NOT NULL DEFAULT 1,
  `is_public` tinyint(1) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `event_time`, `location`, `created_by`, `created_at`, `event_name`, `end_time`, `event_type`, `organizer_id`, `contact_person`, `contact_email`, `contact_phone`, `max_attendees`, `registration_required`, `status`, `event_image`, `start_time`, `organizer`, `updated_at`, `public_display`, `is_public`, `featured`) VALUES
(39, '', 'Join us', '2025-04-17', '00:00:00', 'Somal-ot Casiguran Sorgoson', NULL, '2025-04-04 06:04:06', 'Youth Event', '20:00:00', 'Youth Event', 0, 'Myla Matuba', 'myla@gmail.com', '09123456789', 35, 0, 'Scheduled', 'event_67efcab6a92ca.png', '17:00:00', 'Chan Lagata', '2025-04-04 06:04:06', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `ministry` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT 'default.jpg',
  `membership_status` enum('Active','Inactive','Visitor','New Member') DEFAULT 'Active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL,
  `date_joined` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `email`, `phone`, `address`, `date_of_birth`, `date_added`, `last_updated`, `last_name`, `first_name`, `city`, `state`, `zip`, `gender`, `join_date`, `ministry`, `notes`, `profile_photo`, `membership_status`, `updated_at`, `created_at`, `status`, `date_joined`) VALUES
(36, '', 'mark.reyes@gmail.com', '09171234567', '455 Rodriguez St.', '1998-04-11', '2025-03-15 10:14:48', '2025-03-17 07:44:34', 'Habal', 'Edward', 'Metro Manila', 'Metro Manila', '1104', 'Male', '2022-03-15', 'Youth Ministry', 'Glory to God', '1742033688_1562543227960.jpg', 'New Member', '2025-03-17 07:44:34', '2025-03-15 10:14:48', NULL, NULL),
(37, '', 'myla@gmail.com', '09123456789', 'Cawit, Casiguran, Sorsogon', '1997-11-11', '2025-03-16 05:37:00', '2025-03-16 05:37:00', 'Matuba', 'Myla', 'Sorsogon City', 'Sorsogon', '4702', 'Female', '2022-03-16', 'ACTS', 'Praise Lord', '1742103420_998f41fc4c63e69c06b99a6e03629815.jpg', 'Inactive', '2025-03-16 05:37:00', '2025-03-16 05:37:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ministries`
--

CREATE TABLE `ministries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `leader` varchar(100) DEFAULT NULL,
  `meeting_time` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expires_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires`, `user_id`, `expires_at`, `created_at`) VALUES
(1, 'admin@gmail.com', 'd74d524f760b497f7baf88e215b912a31798eda33b9b5ef41c01f16de782cdd306a7cad7376e5419814526b9c9e5195a092c', 1741435678, 0, '2025-03-16 12:07:56', '2025-03-16 12:08:49'),
(2, 'admin@gmail.com', 'f4f3761a755eb2a533b7b24f8b27960e2e817a9f08f7b5883e06762a44811be03172850a80bec429cde754a403dd2b01795a', 1741436081, 0, '2025-03-16 12:07:56', '2025-03-16 12:08:49'),
(3, 'admin@gmail.com', '183ba5df813363b92200d2e00eabd702b61d4943a0b705677a28c3bc50f728b7042c78eaa55938ea865a708560069a15165a', 1741436204, 0, '2025-03-16 12:07:56', '2025-03-16 12:08:49'),
(4, 'admin@gmail.com', '02b81807f51eac2895444d0804e68eccbdc606cc690fc35dc93f9dc2aca33e16cc8994314d2dcd02d5313692cca77b268ae2', 1741436261, 0, '2025-03-16 12:07:56', '2025-03-16 12:08:49'),
(5, 'edsilgrefalda09@gmail.com', '3d7a6485a87827887d3f8979731aae55a61ffceb865600fc6160e9da0065abec263e2c5fa58fce92a7ec934f989f625361f0', 1741589987, 0, '2025-03-16 12:07:56', '2025-03-16 12:08:49'),
(6, '', '45bbb740c2240ea72f247e5f0238873f65f644afb8d4ba9a2737be03f6b50910', 0, 1, '2025-03-16 13:08:51', '2025-03-16 12:08:51'),
(7, '', '2559ea61620aa680dd7a7ce82ac4edff2edd46d27afd7e0f84df255b2ea5bf92', 0, 1, '2025-03-16 13:09:51', '2025-03-16 12:09:51'),
(9, '', '63ca555d225ec93191cdddcf9e3b40ec69eee7e1bbe9dfac3adb7f16d0104d76', 0, 1, '2025-03-17 14:53:58', '2025-03-17 13:53:58'),
(10, '', '3cc9dce61c82df159bd8e106a2a899c5b0773402341128016b3f724623ebb2b7', 0, 1, '2025-03-17 14:57:35', '2025-03-17 13:57:35'),
(11, '', 'f4231ed277be4fab9ce94bd44d911dffc46508c96bc65bc2371ad1d29f4ebd97', 0, 1, '2025-03-17 14:58:58', '2025-03-17 13:58:58'),
(12, '', '552097107c51147078c17c9d435124940676786ca6fce2e5f9db6a9013297416', 0, 1, '2025-03-17 14:59:15', '2025-03-17 13:59:15'),
(13, '', '1a245048fa9fbbbee93996480b575607c8865869395d2ab883691766c9ac1776', 0, 1, '2025-03-17 14:59:47', '2025-03-17 13:59:47'),
(14, '', '6d3ec0e122b06d1ea1f56b4bad4ed06451722e72bd3cc133cb62df69be0d2c0d', 0, 1, '2025-03-17 15:00:04', '2025-03-17 14:00:04'),
(15, '', 'bfafb9b34b616b085ba785b1c206aafe0365cf8147851512ba1e6d6ce6dd9e21', 0, 1, '2025-03-17 15:00:10', '2025-03-17 14:00:10'),
(16, '', '5db606820f3122fc9df0caabdd6b85808657e240d1e4aae0ab010f88a106bc0e', 0, 1, '2025-03-17 15:00:31', '2025-03-17 14:00:31'),
(17, '', '2417a4dbc9008843478753ec5594fad2ddbacaadb0471e19da5f673d182861ce', 0, 1, '2025-03-17 15:01:07', '2025-03-17 14:01:07'),
(18, '', 'b6de73d69f914edefdf435a2f7bdb28fcc2476cd3ca3ab1666b924d29e867cb8', 0, 1, '2025-03-17 15:01:30', '2025-03-17 14:01:30'),
(19, '', 'a1633aa7efb68ab4c75cd65f8c93254a1f8ed5423599e13e55ea21fb45c9aecd', 0, 1, '2025-03-17 15:01:53', '2025-03-17 14:01:53'),
(20, '', '87465cd50bd7927d315235bf257c400b3cbf20c027bce0fd8116bafd52083b08', 0, 1, '2025-03-17 15:02:03', '2025-03-17 14:02:03'),
(21, '', '99a3971530fa78b723e75987454af389b9508cb671491e295f83ef27712a9e3a', 0, 1, '2025-03-17 15:02:30', '2025-03-17 14:02:30'),
(22, '', 'e50b5e5656bf7d987449dee4decc6c0bfb00b22b1504346f255887764c804910', 0, 1, '2025-03-17 15:02:40', '2025-03-17 14:02:40'),
(23, '', '6d57259af7c6caa133566ae574c2170fe6ccb147ad5272b3e263c912f8b6adba', 0, 1, '2025-03-22 14:21:23', '2025-03-22 13:21:23'),
(24, '', 'f42da4ff542b9b2cb1e7542fae5d1f4a4b6acfcb8034186955cc7ecdc3cbbe07', 0, 1, '2025-03-25 17:28:06', '2025-03-25 16:28:06'),
(25, '', 'a8a5beedf1381127ca5f4032c5bae8d4c7fdd78553f0424e518590ec9788fd28', 0, 1, '2025-03-25 17:46:18', '2025-03-25 16:46:18');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_title` varchar(255) NOT NULL,
  `service_day` varchar(20) NOT NULL,
  `service_time` varchar(50) NOT NULL,
  `service_location` varchar(255) DEFAULT NULL,
  `service_leader` varchar(255) DEFAULT NULL,
  `service_description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  `day_of_week` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_title`, `service_day`, `service_time`, `service_location`, `service_leader`, `service_description`, `is_active`, `created_at`, `updated_at`, `status`, `day_of_week`) VALUES
(2, 'Sunday Service', 'Sunday', '9:00 am - 11:00 am', 'Somal-ot Casiguran Sorsogon', 'John Doe', 'Join us', 1, '2025-03-31 05:24:26', '2025-04-01 02:45:57', NULL, NULL),
(3, 'Regional Prayer Meeting', 'Sunday', '11:00 am - 2:00 pm', 'Somal-ot Casiguran Sorsogon', 'Rochelle Grefalda', 'Join Us', 1, '2025-04-04 12:05:35', '2025-04-04 12:05:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `tagline` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `pastor_name` varchar(255) NOT NULL,
  `pastor_title` varchar(255) NOT NULL,
  `church_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `tagline`, `logo`, `pastor_name`, `pastor_title`, `church_name`) VALUES
(1, '', NULL, '', '', ''),
(2, '', NULL, '', '', ''),
(3, '', NULL, '', '', ''),
(4, '', NULL, '', '', ''),
(5, 'Transforming Lives Through Christ', 'jia.png', 'Pastor John Doe', 'Senior Pastor', 'JIA Somal-ot Church');

-- --------------------------------------------------------

--
-- Table structure for table `social_media`
--

CREATE TABLE `social_media` (
  `id` int(11) NOT NULL,
  `platform` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
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
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `full_name` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `full_name`, `reset_token`, `reset_expires`, `updated_at`) VALUES
(1, 'admin', '$2y$10$b.f7nIq3gi7V6N0/F2Wht.fJ3Mw535BpLZYcVDr02J/JmD1VjOSb2', 'edsiljeremias@gmail.com', 'admin', 'edsuderpo', 'e91d3ca61af98d78cd97f05e168d9760184cd7ad83bbd21221ded1072eb7f16c', '2025-03-16 05:47:04', '2025-04-11 06:09:13'),
(3, 'Edsill', '$2y$10$KtxMaaUfswkwKah5lTmU/OCTGJcHc5eZYegzF8VDkUY.f.gi.5hCW', 'localchurch@gmail.com', 'admin', 'System Administrator', 'c1ad2437c669f10e3782be20eaac4d02ac13bcf1ad1fe70f30140c5606676dd9', '2025-03-10 09:53:53', '2025-03-13 10:15:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ministries`
--
ALTER TABLE `ministries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_media`
--
ALTER TABLE `social_media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `platform` (`platform`);

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
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `ministries`
--
ALTER TABLE `ministries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `social_media`
--
ALTER TABLE `social_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
