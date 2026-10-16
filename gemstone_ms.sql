-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 08:25 AM
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
-- Database: `gemstone_ms`
--

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_access_control`
--

CREATE TABLE `gemstone_access_control` (
  `admin_tier_id` int(11) NOT NULL,
  `admin_level` int(11) NOT NULL,
  `division_id` int(11) NOT NULL,
  `level_title` varchar(20) NOT NULL,
  `fulltime` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_access_control`
--

INSERT INTO `gemstone_access_control` (`admin_tier_id`, `admin_level`, `division_id`, `level_title`, `fulltime`) VALUES
(1, 26, 1, 'Super Admin', 1),
(2, 5, 2, 'Owner', 1),
(3, 3, 2, 'Supervisor', 1),
(4, 1, 2, 'Staff', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_access_rule`
--

CREATE TABLE `gemstone_access_rule` (
  `rule_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `authorized_date` datetime NOT NULL,
  `authorizing_admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_action`
--

CREATE TABLE `gemstone_action` (
  `action_id` int(11) NOT NULL,
  `action_code` varchar(10) NOT NULL,
  `action_name` varchar(50) NOT NULL,
  `action_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_action`
--

INSERT INTO `gemstone_action` (`action_id`, `action_code`, `action_name`, `action_description`) VALUES
(1, '00-001', 'Login', ''),
(2, '00-002', 'View Dashboard', ''),
(3, '00-003', 'Logout', ''),
(4, '00-004', 'Force Logout', ''),
(5, '00-005', 'View Admin List', ''),
(6, '00-005A', 'Add New Admin', ''),
(7, '00-005DS', 'View Admin Detail (Self)', ''),
(8, '00-005D', 'View Admin Detail', ''),
(9, '00-005DEAC', 'Deactivate Admin', ''),
(10, '00-005REAC', 'Reactivate Admin', ''),
(11, '00-005ES', 'Edit Admin Detail (Self)', ''),
(12, '00-005E', 'Edit Admin Detail', ''),
(13, '00-013', 'View Gemstone Settings', ''),
(14, '00-014', 'Edit Gemstone Settings', ''),
(15, '00-015', 'View Position List', ''),
(16, '00-015A', 'Add New Position', ''),
(17, '00-015D', 'View Position Detail', ''),
(18, '00-015E', 'Edit Position Detail', ''),
(19, '00-015GRN', 'Grant Access Rule', ''),
(20, '00-015RVK', 'Revoke Access Rule', ''),
(21, '00-021A', 'Add New Division', ''),
(22, '00-021D', 'View Division Detail', ''),
(23, '00-021E', 'Edit Division Detail', ''),
(24, '00-004A', 'Force Logout All User', ''),
(25, '00-024', 'View UAC Table', ''),
(26, '00-025', 'Download UAC Table', ''),
(27, '00-026', 'View DB Structure', ''),
(28, '00-005REPT', 'View Admin Activity Report', ''),
(29, '00-028', 'Send Push Notification to Admin', ''),
(30, '00-024I', 'Import UAC Table', ''),
(32, '00-021', 'View Division List', '');

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_activity_log`
--

CREATE TABLE `gemstone_activity_log` (
  `log_id` int(11) NOT NULL,
  `log_date` datetime NOT NULL,
  `actor` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `target_link` text DEFAULT NULL,
  `label` text DEFAULT NULL,
  `meta_data` text DEFAULT NULL,
  `legacy_log` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_activity_log`
--

INSERT INTO `gemstone_activity_log` (`log_id`, `log_date`, `actor`, `action_id`, `description`, `target_link`, `label`, `meta_data`, `legacy_log`) VALUES
(1, '2025-01-31 13:21:39', 1, 1, NULL, NULL, NULL, NULL, 0),
(2, '2025-01-31 13:21:41', 1, 2, NULL, NULL, NULL, NULL, 0),
(3, '2025-01-31 13:21:48', 1, 3, NULL, NULL, NULL, NULL, 0),
(4, '2025-02-07 14:32:12', 1, 1, NULL, NULL, NULL, NULL, 0),
(5, '2025-02-07 14:32:13', 1, 2, NULL, NULL, NULL, NULL, 0),
(6, '2025-02-07 14:32:16', 1, 5, NULL, NULL, NULL, NULL, 0),
(7, '2025-02-07 14:32:17', 1, 13, NULL, NULL, NULL, NULL, 0),
(8, '2025-02-07 14:32:18', 1, 5, NULL, NULL, NULL, NULL, 0),
(9, '2025-02-07 14:32:23', 1, 28, NULL, NULL, NULL, NULL, 0),
(10, '2025-02-07 14:32:26', 1, 2, NULL, NULL, NULL, NULL, 0),
(11, '2025-02-07 14:32:30', 1, 3, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_admin`
--

CREATE TABLE `gemstone_admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(20) NOT NULL,
  `admin_fullname` varchar(100) NOT NULL,
  `admin_phone` varchar(20) NOT NULL,
  `admin_email` varchar(200) NOT NULL,
  `last_active` datetime NOT NULL,
  `admin_tier_id` int(11) NOT NULL,
  `password` varchar(60) NOT NULL,
  `admin_status` int(11) NOT NULL DEFAULT 1,
  `photo_path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_admin`
--

INSERT INTO `gemstone_admin` (`admin_id`, `admin_name`, `admin_fullname`, `admin_phone`, `admin_email`, `last_active`, `admin_tier_id`, `password`, `admin_status`, `photo_path`) VALUES
(1, 'aquaterra', 'Super Admin', '', '', '2025-02-07 14:32:12', 1, '$2y$10$PE24x2AqZ4.4LTzxQkBzFeFUPaKdIhyFip6iI7wBEVox3cvqkykOC', 1, ''),
(2, 'owner', 'owner', '', '', '2021-04-17 16:00:08', 2, '$2y$10$uzHssWZen.JdrYXsNGLbcuP/D/4VTsI/KhNmkkFJha3bdyHsTNsUG', 1, ''),
(3, 'supervisor', 'supervisor', '', '', '2021-04-17 19:24:28', 3, '$2y$10$e6SCnWwMR1B8Sp/r/4XQMOyDSJKSFYQjXkql.Xi4x/YyCDbuzwIVe', 1, ''),
(4, 'staff', 'staff', '', '', '2021-04-17 15:55:40', 4, '$2y$10$Obob/b41WvCuuHqnZdkSwuZoRWkYDEZCOgqvNH9b65/j9mKMNG3JW', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_admin_status`
--

CREATE TABLE `gemstone_admin_status` (
  `admin_status_id` int(11) NOT NULL,
  `admin_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_admin_status`
--

INSERT INTO `gemstone_admin_status` (`admin_status_id`, `admin_status`) VALUES
(1, 'Active'),
(2, 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_config`
--

CREATE TABLE `gemstone_config` (
  `configuration_id` int(11) NOT NULL,
  `item_per_page` int(11) NOT NULL DEFAULT 15,
  `default_language` int(11) DEFAULT NULL,
  `email_notification` tinyint(4) NOT NULL DEFAULT 0,
  `default_email_recipient` varchar(320) NOT NULL,
  `working_hour_start` time NOT NULL DEFAULT '09:00:00',
  `working_hour_end` time NOT NULL DEFAULT '18:00:00',
  `work_mon` tinyint(4) NOT NULL DEFAULT 1,
  `work_tue` tinyint(4) NOT NULL DEFAULT 1,
  `work_wed` tinyint(4) NOT NULL DEFAULT 1,
  `work_thu` tinyint(4) NOT NULL DEFAULT 1,
  `work_fri` tinyint(4) NOT NULL DEFAULT 1,
  `work_sat` tinyint(4) NOT NULL DEFAULT 0,
  `work_sun` tinyint(4) NOT NULL DEFAULT 0,
  `is_maintenance` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_config`
--

INSERT INTO `gemstone_config` (`configuration_id`, `item_per_page`, `default_language`, `email_notification`, `default_email_recipient`, `working_hour_start`, `working_hour_end`, `work_mon`, `work_tue`, `work_wed`, `work_thu`, `work_fri`, `work_sat`, `work_sun`, `is_maintenance`) VALUES
(1, 15, 1, 0, '', '09:00:00', '20:00:00', 1, 1, 1, 1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_division`
--

CREATE TABLE `gemstone_division` (
  `division_id` int(11) NOT NULL,
  `division_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_division`
--

INSERT INTO `gemstone_division` (`division_id`, `division_name`) VALUES
(1, 'System Administrator'),
(2, 'Management');

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_firebase_token`
--

CREATE TABLE `gemstone_firebase_token` (
  `token` varchar(500) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `added_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_language`
--

CREATE TABLE `gemstone_language` (
  `lang_id` int(11) NOT NULL,
  `lang_code` varchar(10) NOT NULL,
  `language` varchar(50) NOT NULL,
  `currency` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_language`
--

INSERT INTO `gemstone_language` (`lang_id`, `lang_code`, `language`, `currency`) VALUES
(1, 'en', 'English', ''),
(2, 'id', 'Indonesia', '');

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_module`
--

CREATE TABLE `gemstone_module` (
  `module_id` int(11) NOT NULL,
  `module_code` varchar(50) NOT NULL,
  `module_name` varchar(50) NOT NULL,
  `module_version` int(11) NOT NULL,
  `module_description` text NOT NULL,
  `install_date` datetime NOT NULL,
  `update_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_reset_token`
--

CREATE TABLE `gemstone_reset_token` (
  `token` varchar(26) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(2) NOT NULL,
  `date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gemstone_session_token`
--

CREATE TABLE `gemstone_session_token` (
  `session_token` varchar(26) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `device_info` text NOT NULL,
  `access_ip` varchar(50) NOT NULL,
  `session_start` datetime NOT NULL,
  `last_active` datetime NOT NULL,
  `logged_out` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gemstone_session_token`
--

INSERT INTO `gemstone_session_token` (`session_token`, `admin_id`, `device_info`, `access_ip`, `session_start`, `last_active`, `logged_out`) VALUES
('4e503cba53113891b5212a9830', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36|Windows 10|1|||Chrome|132.0.0.0', '::1', '2025-02-07 14:32:12', '2025-02-07 14:32:26', 1),
('e120289cf0b391fbf26b7131f8', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36|Windows 10|1|||Chrome|132.0.0.0', '::1', '2025-01-31 13:21:39', '2025-01-31 13:21:41', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gemstone_access_control`
--
ALTER TABLE `gemstone_access_control`
  ADD PRIMARY KEY (`admin_tier_id`),
  ADD KEY `division_id` (`division_id`);

--
-- Indexes for table `gemstone_access_rule`
--
ALTER TABLE `gemstone_access_rule`
  ADD PRIMARY KEY (`rule_id`),
  ADD KEY `FK_gemstone_access_rule_gemstone_access_control` (`position_id`),
  ADD KEY `FK_gemstone_access_rule_gemstone_action` (`action_id`),
  ADD KEY `FK_gemstone_access_rule_gemstone_admin` (`authorizing_admin_id`);

--
-- Indexes for table `gemstone_action`
--
ALTER TABLE `gemstone_action`
  ADD PRIMARY KEY (`action_id`);

--
-- Indexes for table `gemstone_activity_log`
--
ALTER TABLE `gemstone_activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_const` (`actor`),
  ADD KEY `action_const` (`action_id`);

--
-- Indexes for table `gemstone_admin`
--
ALTER TABLE `gemstone_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `admin_status` (`admin_status`);

--
-- Indexes for table `gemstone_admin_status`
--
ALTER TABLE `gemstone_admin_status`
  ADD PRIMARY KEY (`admin_status_id`);

--
-- Indexes for table `gemstone_config`
--
ALTER TABLE `gemstone_config`
  ADD PRIMARY KEY (`configuration_id`);

--
-- Indexes for table `gemstone_division`
--
ALTER TABLE `gemstone_division`
  ADD PRIMARY KEY (`division_id`);

--
-- Indexes for table `gemstone_firebase_token`
--
ALTER TABLE `gemstone_firebase_token`
  ADD PRIMARY KEY (`token`),
  ADD KEY `FK_gemstone_firebase_token_gemstone_admin` (`admin_id`);

--
-- Indexes for table `gemstone_language`
--
ALTER TABLE `gemstone_language`
  ADD PRIMARY KEY (`lang_id`);

--
-- Indexes for table `gemstone_module`
--
ALTER TABLE `gemstone_module`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `module_code` (`module_code`);

--
-- Indexes for table `gemstone_reset_token`
--
ALTER TABLE `gemstone_reset_token`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `gemstone_session_token`
--
ALTER TABLE `gemstone_session_token`
  ADD PRIMARY KEY (`session_token`),
  ADD KEY `admin_session_token` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gemstone_access_control`
--
ALTER TABLE `gemstone_access_control`
  MODIFY `admin_tier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gemstone_access_rule`
--
ALTER TABLE `gemstone_access_rule`
  MODIFY `rule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gemstone_action`
--
ALTER TABLE `gemstone_action`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `gemstone_activity_log`
--
ALTER TABLE `gemstone_activity_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `gemstone_admin`
--
ALTER TABLE `gemstone_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gemstone_admin_status`
--
ALTER TABLE `gemstone_admin_status`
  MODIFY `admin_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gemstone_config`
--
ALTER TABLE `gemstone_config`
  MODIFY `configuration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gemstone_division`
--
ALTER TABLE `gemstone_division`
  MODIFY `division_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gemstone_language`
--
ALTER TABLE `gemstone_language`
  MODIFY `lang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gemstone_module`
--
ALTER TABLE `gemstone_module`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gemstone_access_rule`
--
ALTER TABLE `gemstone_access_rule`
  ADD CONSTRAINT `FK_gemstone_access_rule_gemstone_access_control` FOREIGN KEY (`position_id`) REFERENCES `gemstone_access_control` (`admin_tier_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_gemstone_access_rule_gemstone_action` FOREIGN KEY (`action_id`) REFERENCES `gemstone_action` (`action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_gemstone_access_rule_gemstone_admin` FOREIGN KEY (`authorizing_admin_id`) REFERENCES `gemstone_admin` (`admin_id`) ON UPDATE CASCADE;

--
-- Constraints for table `gemstone_activity_log`
--
ALTER TABLE `gemstone_activity_log`
  ADD CONSTRAINT `action_const` FOREIGN KEY (`action_id`) REFERENCES `gemstone_action` (`action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `admin_const` FOREIGN KEY (`actor`) REFERENCES `gemstone_admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gemstone_admin`
--
ALTER TABLE `gemstone_admin`
  ADD CONSTRAINT `FK_gemstone_admin_gemstone_admin_status` FOREIGN KEY (`admin_status`) REFERENCES `gemstone_admin_status` (`admin_status_id`) ON UPDATE CASCADE;

--
-- Constraints for table `gemstone_firebase_token`
--
ALTER TABLE `gemstone_firebase_token`
  ADD CONSTRAINT `FK_gemstone_firebase_token_gemstone_admin` FOREIGN KEY (`admin_id`) REFERENCES `gemstone_admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gemstone_session_token`
--
ALTER TABLE `gemstone_session_token`
  ADD CONSTRAINT `admin_session_token` FOREIGN KEY (`admin_id`) REFERENCES `gemstone_admin` (`admin_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
