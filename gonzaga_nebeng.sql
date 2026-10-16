-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 06, 2026 at 08:20 AM
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
-- Database: `gonzaga_nebeng`
--

-- --------------------------------------------------------

--
-- Table structure for table `list_passenger`
--

CREATE TABLE `list_passenger` (
  `passenger_id` int(11) NOT NULL,
  `passenger_name` varchar(150) NOT NULL,
  `passenger_class` varchar(5) NOT NULL,
  `passenger_phone_number` varchar(20) NOT NULL,
  `passenger_note` text NOT NULL,
  `trip_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list_ride_type`
--

CREATE TABLE `list_ride_type` (
  `ride_type_id` int(11) NOT NULL,
  `ride_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list_trip`
--

CREATE TABLE `list_trip` (
  `trip_id` int(11) NOT NULL,
  `host_name` varchar(100) NOT NULL,
  `destination` text NOT NULL,
  `departure_time` time(4) NOT NULL,
  `meetpoint` text NOT NULL,
  `ride_type_id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `list_passenger`
--
ALTER TABLE `list_passenger`
  ADD PRIMARY KEY (`passenger_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `list_ride_type`
--
ALTER TABLE `list_ride_type`
  ADD PRIMARY KEY (`ride_type_id`);

--
-- Indexes for table `list_trip`
--
ALTER TABLE `list_trip`
  ADD PRIMARY KEY (`trip_id`),
  ADD KEY `ride_type_id` (`ride_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `list_passenger`
--
ALTER TABLE `list_passenger`
  MODIFY `passenger_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `list_ride_type`
--
ALTER TABLE `list_ride_type`
  MODIFY `ride_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `list_trip`
--
ALTER TABLE `list_trip`
  MODIFY `trip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `list_passenger`
--
ALTER TABLE `list_passenger`
  ADD CONSTRAINT `list_passenger_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `list_ride_type` (`ride_type_id`);

--
-- Constraints for table `list_trip`
--
ALTER TABLE `list_trip`
  ADD CONSTRAINT `list_trip_ibfk_1` FOREIGN KEY (`ride_type_id`) REFERENCES `list_ride_type` (`ride_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
