-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2024 at 03:33 PM
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
-- Database: `inventorydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `Id` int(255) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `warehouse` varchar(255) NOT NULL,
  `quantity` int(255) NOT NULL,
  `timestamp` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`Id`, `stockcode`, `description`, `warehouse`, `quantity`, `timestamp`) VALUES
(2, 'FLANGE/ST/40/THREAD', 'Threaded', 'CS', 15, '2024-02-26'),
(4, 'FLANGE/ST/40', 'Stab', 'CS', 15, '2024-02-26'),
(5, 'FLANGE/ST/50', 'Stab50', 'CS', 10, '2024-02-26');

-- --------------------------------------------------------

--
-- Table structure for table `productclass`
--

CREATE TABLE `productclass` (
  `Id` int(11) NOT NULL,
  `product_class` varchar(5) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `Id` int(11) NOT NULL,
  `stockcode` varchar(255) NOT NULL,
  `transaction_date` date NOT NULL DEFAULT current_timestamp(),
  `transaction_type` varchar(10) NOT NULL,
  `transaction_quantity` int(255) NOT NULL,
  `reference` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`Id`, `stockcode`, `transaction_date`, `transaction_type`, `transaction_quantity`, `reference`) VALUES
(1, 'FLANGE/ST/40', '2024-02-26', 'Issue', 5, 'ORDER'),
(2, 'FLANGE/ST/40/THREAD', '2024-02-26', 'Issue', 10, 'Bad Items');

-- --------------------------------------------------------

--
-- Table structure for table `transactiontype`
--

CREATE TABLE `transactiontype` (
  `Id` int(255) NOT NULL,
  `transaction_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactiontype`
--

INSERT INTO `transactiontype` (`Id`, `transaction_type`) VALUES
(2, 'Issue'),
(3, 'Expense');

-- --------------------------------------------------------

--
-- Table structure for table `users1`
--

CREATE TABLE `users1` (
  `Id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users1`
--

INSERT INTO `users1` (`Id`, `username`, `firstname`, `lastname`, `email`, `department`, `password`) VALUES
(1, 'DCL264', 'Erick', 'Chomba', 'erick@dancocapital.co.ke', 'IT', 'Dickson1');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse`
--

CREATE TABLE `warehouse` (
  `Id` int(11) NOT NULL,
  `warehouse` varchar(5) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `stockcode` (`stockcode`),
  ADD UNIQUE KEY `stockcode_2` (`stockcode`);

--
-- Indexes for table `productclass`
--
ALTER TABLE `productclass`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `transactiontype`
--
ALTER TABLE `transactiontype`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `users1`
--
ALTER TABLE `users1`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `productclass`
--
ALTER TABLE `productclass`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactiontype`
--
ALTER TABLE `transactiontype`
  MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users1`
--
ALTER TABLE `users1`
  MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
