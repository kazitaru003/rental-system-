-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 02:11 PM
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
-- Database: `rental-system-`
--

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `rental_no` int(11) NOT NULL,
  `renter_name` varchar(100) NOT NULL,
  `renter_contact` int(11) NOT NULL,
  `licence_plate_number` varchar(6) NOT NULL,
  `days_rented` int(11) NOT NULL,
  `rental_start` date NOT NULL,
  `rental_end` date NOT NULL,
  `rental_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`rental_no`, `renter_name`, `renter_contact`, `licence_plate_number`, `days_rented`, `rental_start`, `rental_end`, `rental_status`) VALUES
(2, 'asdfRenter5', 2147483647, 'GHI789', 7, '2026-05-03', '2026-05-10', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `licence_plate_number` varchar(6) NOT NULL,
  `daily_rate` int(11) NOT NULL,
  `vehicle_make` varchar(50) NOT NULL,
  `vehicle_brand` varchar(50) NOT NULL,
  `vehicle_type` varchar(20) NOT NULL,
  `vehicle_year` int(4) NOT NULL,
  `vehicle_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`licence_plate_number`, `daily_rate`, `vehicle_make`, `vehicle_brand`, `vehicle_type`, `vehicle_year`, `vehicle_status`) VALUES
('ABC123', 1000, 'Corolla', 'Toyota', 'Car', 2002, 'Available'),
('DEF456', 1500, 'Taurus', 'Ford', 'Truck', 2026, 'Maintenance'),
('GHI789', 1250, 'Montero Sport', 'Mitsubishi', 'Car', 2018, 'Rented');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`rental_no`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`licence_plate_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `rental_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
