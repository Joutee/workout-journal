-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 01:19 PM
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
-- Database: `kovp07`
--

-- --------------------------------------------------------

--
-- Table structure for table `exercise_set`
--

CREATE TABLE `exercise_set` (
  `exercise_set_id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `repetitions` int(11) NOT NULL,
  `weight` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `exercise_set`
--

INSERT INTO `exercise_set` (`exercise_set_id`, `workout_id`, `exercise_id`, `repetitions`, `weight`) VALUES
(1, 1, 1, 10, 60),
(2, 1, 1, 8, 70),
(3, 1, 2, 12, 80),
(4, 2, 3, 8, 0),
(5, 2, 3, 6, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exercise_set`
--
ALTER TABLE `exercise_set`
  ADD PRIMARY KEY (`exercise_set_id`),
  ADD KEY `workout_id` (`workout_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exercise_set`
--
ALTER TABLE `exercise_set`
  MODIFY `exercise_set_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exercise_set`
--
ALTER TABLE `exercise_set`
  ADD CONSTRAINT `exercise_set_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`exercise_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exercise_set_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workout` (`workout_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
