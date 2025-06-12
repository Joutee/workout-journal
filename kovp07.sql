-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 12, 2025 at 08:39 PM
-- Server version: 10.5.23-MariaDB-0+deb11u1
-- PHP Version: 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kovp07`
--

-- --------------------------------------------------------

--
-- Table structure for table `exercise_muscle_group`
--

CREATE TABLE `exercise_muscle_group` (
  `exercise_id` int(11) NOT NULL,
  `muscle_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `exercise_muscle_group`
--

INSERT INTO `exercise_muscle_group` (`exercise_id`, `muscle_group_id`) VALUES
(24, 14),
(24, 15),
(26, 2),
(26, 14),
(27, 4),
(28, 5),
(29, 6),
(30, 15),
(31, 16),
(32, 17),
(33, 18),
(44, 14),
(44, 15),
(45, 1),
(45, 6),
(46, 2),
(46, 14),
(47, 4),
(48, 5),
(49, 6),
(50, 15),
(51, 16),
(52, 17),
(53, 18),
(54, 14),
(54, 15),
(55, 1),
(55, 6),
(56, 2),
(56, 14),
(57, 4),
(58, 5),
(59, 6),
(60, 15),
(61, 16),
(62, 17),
(63, 18),
(64, 14),
(64, 15),
(65, 1),
(65, 6),
(66, 2),
(66, 14),
(67, 4),
(68, 5),
(69, 6),
(70, 15),
(71, 16),
(72, 17),
(73, 18),
(78, 4),
(79, 1),
(82, 14),
(82, 15),
(83, 1),
(83, 6),
(84, 2),
(84, 14),
(85, 4),
(86, 5),
(87, 6),
(88, 15),
(89, 16),
(90, 17),
(91, 18),
(92, 14),
(92, 15),
(93, 1),
(93, 6),
(94, 2),
(94, 14),
(95, 4),
(96, 5),
(97, 6),
(98, 15),
(99, 16),
(100, 17),
(101, 18);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exercise_muscle_group`
--
ALTER TABLE `exercise_muscle_group`
  ADD KEY `exercise_id` (`exercise_id`),
  ADD KEY `muscle_group_id` (`muscle_group_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exercise_muscle_group`
--
ALTER TABLE `exercise_muscle_group`
  ADD CONSTRAINT `exercise_muscle_group_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`exercise_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exercise_muscle_group_ibfk_2` FOREIGN KEY (`muscle_group_id`) REFERENCES `muscle_group` (`muscle_group_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
