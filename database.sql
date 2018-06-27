-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 27, 2018 at 11:01 AM
-- Server version: 10.1.23-MariaDB-9+deb9u1
-- PHP Version: 7.0.27-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `EmailRelationshipClassifier`
--
CREATE DATABASE IF NOT EXISTS `EmailRelationshipClassifier` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `EmailRelationshipClassifier`;

-- --------------------------------------------------------

--
-- Table structure for table `Classifications`
--

CREATE TABLE `Classifications` (
  `ID` int(11) NOT NULL,
  `Classification` varchar(128) NOT NULL,
  `Weight` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Dumping data for table `Classifications`
--

INSERT INTO `Classifications` (`ID`, `Classification`, `Weight`) VALUES
(1, 'Colleague', 1),
(2, 'Employee', 1),
(3, 'Manager', 1),
(4, 'Employer', 1),
(5, 'Spouse', 1),
(6, 'Husband', 0.9),
(7, 'Wife', 0.9),
(8, 'Parent', 1),
(9, 'Father', 0.9),
(10, 'Mother', 0.9),
(11, 'Child', 1),
(12, 'Son', 0.9),
(13, 'Daughter', 0.9),
(14, 'Sibling', 1),
(15, 'Brother', 0.9),
(16, 'Sister', 0.9),
(17, 'Grandparent', 1),
(18, 'Grandfather', 0.9),
(19, 'Grandmother', 0.9),
(20, 'Grandchild', 1),
(21, 'Grandson', 0.9),
(22, 'Granddaughter', 0.9),
(23, 'Uncle', 1),
(24, 'Aunt', 1),
(25, 'Cousin', 1),
(26, 'Nephew', 1),
(27, 'Niece', 1),
(28, 'Friend', 1);

--
-- Table structure for table `Words`
--

CREATE TABLE `Words` (
  `ID` int(11) NOT NULL,
  `Word` text NOT NULL,
  `Colleague-Sender` int(11) NOT NULL,
  `Employee-Sender` int(11) NOT NULL,
  `Manager-Sender` int(11) NOT NULL,
  `Employer-Sender` int(11) NOT NULL,
  `Spouse-Sender` int(11) NOT NULL,
  `Husband-Sender` int(11) NOT NULL,
  `Wife-Sender` int(11) NOT NULL,
  `Parent-Sender` int(11) NOT NULL,
  `Father-Sender` int(11) NOT NULL,
  `Mother-Sender` int(11) NOT NULL,
  `Child-Sender` int(11) NOT NULL,
  `Son-Sender` int(11) NOT NULL,
  `Daughter-Sender` int(11) NOT NULL,
  `Sibling-Sender` int(11) NOT NULL,
  `Brother-Sender` int(11) NOT NULL,
  `Sister-Sender` int(11) NOT NULL,
  `Grandparent-Sender` int(11) NOT NULL,
  `Grandfather-Sender` int(11) NOT NULL,
  `Grandmother-Sender` int(11) NOT NULL,
  `Grandchild-Sender` int(11) NOT NULL,
  `Grandson-Sender` int(11) NOT NULL,
  `Granddaughter-Sender` int(11) NOT NULL,
  `Uncle-Sender` int(11) NOT NULL,
  `Aunt-Sender` int(11) NOT NULL,
  `Cousin-Sender` int(11) NOT NULL,
  `Nephew-Sender` int(11) NOT NULL,
  `Niece-Sender` int(11) NOT NULL,
  `Friend-Sender` int(11) NOT NULL,
  `Colleague-Recipient` int(11) NOT NULL,
  `Employee-Recipient` int(11) NOT NULL,
  `Manager-Recipient` int(11) NOT NULL,
  `Employer-Recipient` int(11) NOT NULL,
  `Spouse-Recipient` int(11) NOT NULL,
  `Husband-Recipient` int(11) NOT NULL,
  `Wife-Recipient` int(11) NOT NULL,
  `Parent-Recipient` int(11) NOT NULL,
  `Father-Recipient` int(11) NOT NULL,
  `Mother-Recipient` int(11) NOT NULL,
  `Child-Recipient` int(11) NOT NULL,
  `Son-Recipient` int(11) NOT NULL,
  `Daughter-Recipient` int(11) NOT NULL,
  `Sibling-Recipient` int(11) NOT NULL,
  `Brother-Recipient` int(11) NOT NULL,
  `Sister-Recipient` int(11) NOT NULL,
  `Grandparent-Recipient` int(11) NOT NULL,
  `Grandfather-Recipient` int(11) NOT NULL,
  `Grandmother-Recipient` int(11) NOT NULL,
  `Grandchild-Recipient` int(11) NOT NULL,
  `Grandson-Recipient` int(11) NOT NULL,
  `Granddaughter-Recipient` int(11) NOT NULL,
  `Uncle-Recipient` int(11) NOT NULL,
  `Aunt-Recipient` int(11) NOT NULL,
  `Cousin-Recipient` int(11) NOT NULL,
  `Nephew-Recipient` int(11) NOT NULL,
  `Niece-Recipient` int(11) NOT NULL,
  `Friend-Recipient` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Classifications`
--
ALTER TABLE `Classifications`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Words`
--
ALTER TABLE `Words`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Classifications`
--
ALTER TABLE `Classifications`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `Words`
--
ALTER TABLE `Words`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
