-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2025 at 09:39 PM
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
-- Table structure for table `exercise`
--

CREATE TABLE `exercise` (
  `exercise_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `exercise`
--

INSERT INTO `exercise` (`exercise_id`, `user_id`, `name`, `description`) VALUES
(24, 9, 'Dřep', 'Základní cvik na stehna'),
(26, 9, 'Mrtvý tah', 'Základní cvik na záda'),
(27, 9, 'Bicepsový zdvih', 'Cvik na biceps'),
(28, 9, 'Tricepsový tlak', 'Cvik na triceps'),
(29, 9, 'Tlaky na ramena', 'Cvik na ramena'),
(30, 9, 'Výpony lýtek', 'Cvik na lýtka'),
(31, 9, 'Předloktí zdvih', 'Cvik na předloktí'),
(32, 9, 'Krčení ramen', 'Cvik na krk'),
(33, 9, 'Zkracovačky', 'Cvik na břicho'),
(44, 11, 'Dřep', 'Základní cvik na stehna'),
(45, 11, 'Bench press', 'Základní cvik na prsa'),
(46, 11, 'Mrtvý tah', 'Základní cvik na záda'),
(47, 11, 'Bicepsový zdvih', 'Cvik na biceps'),
(48, 11, 'Tricepsový tlak', 'Cvik na triceps'),
(49, 11, 'Tlaky na ramena', 'Cvik na ramena'),
(50, 11, 'Výpony lýtek', 'Cvik na lýtka'),
(51, 11, 'Předloktí zdvih', 'Cvik na předloktí'),
(52, 11, 'Krčení ramen', 'Cvik na krk'),
(53, 11, 'Zkracovačky', 'Cvik na břicho');

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
(53, 18);

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
(54, 39, 29, 4, 8),
(55, 40, 30, 3, 50),
(56, 41, 24, 50, 0),
(57, 41, 24, 50, 0),
(58, 41, 24, 40, 0),
(65, 46, 27, 5, 8);

-- --------------------------------------------------------

--
-- Table structure for table `muscle_group`
--

CREATE TABLE `muscle_group` (
  `muscle_group_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `muscle_group`
--

INSERT INTO `muscle_group` (`muscle_group_id`, `name`) VALUES
(1, 'Prsa'),
(2, 'Záda'),
(4, 'Biceps'),
(5, 'Triceps'),
(6, 'Ramena'),
(14, 'Stehna'),
(15, 'Lýtka'),
(16, 'Předloktí'),
(17, 'Krk'),
(18, 'Břicho');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `facebook_id` varchar(50) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `surname`, `email`, `password`, `facebook_id`, `admin`) VALUES
(9, 'Petr', 'Kovanda', 'kovp07@vse.cz', '$2y$10$ggEIwfA4A1FB1jS1qO.BFefQmXH8kzLIU.D/yXJhdu5pFL1yAuYqq', '', 1),
(11, 'Jarmil', 'Vrtulnik', 'kotojoj993@rowplant.com', '$2y$10$1gmBHkeOA2WffIc5A/NI7eGeWqXxmNCQxX.rpzXWfjhB2BXtQcj/q', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `workout`
--

CREATE TABLE `workout` (
  `workout_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `workout`
--

INSERT INTO `workout` (`workout_id`, `user_id`, `name`, `date`, `note`) VALUES
(39, 9, 'Úterní trénink', '2025-06-03 12:00:00', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Fusce tellus. Fusce dui leo, imperdiet in, aliquam sit amet, feugiat eu, orci. Mauris metus. Aliquam id dolor.'),
(40, 9, 'Čtvrteční trénink', '2025-06-05 12:03:00', 'Sit amet, consectetuer adipiscing elit. Fusce tellus. Fusce dui leo, imperdiet in, aliquam sit amet, feugiat eu, orci. Mauris metus. Aliquam id dolor.'),
(41, 9, 'Páteční trénink', '2025-06-06 12:18:00', 'sdfsd ddddddddddddf  sdfsdfsd'),
(42, 9, 'Sobotní trénink', '2025-06-07 15:01:00', ''),
(46, 9, 'Sobotní trénink', '2025-06-07 19:08:00', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exercise`
--
ALTER TABLE `exercise`
  ADD PRIMARY KEY (`exercise_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exercise_muscle_group`
--
ALTER TABLE `exercise_muscle_group`
  ADD KEY `exercise_id` (`exercise_id`),
  ADD KEY `muscle_group_id` (`muscle_group_id`);

--
-- Indexes for table `exercise_set`
--
ALTER TABLE `exercise_set`
  ADD PRIMARY KEY (`exercise_set_id`),
  ADD KEY `workout_id` (`workout_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indexes for table `muscle_group`
--
ALTER TABLE `muscle_group`
  ADD PRIMARY KEY (`muscle_group_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `workout`
--
ALTER TABLE `workout`
  ADD PRIMARY KEY (`workout_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exercise`
--
ALTER TABLE `exercise`
  MODIFY `exercise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `exercise_set`
--
ALTER TABLE `exercise_set`
  MODIFY `exercise_set_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `muscle_group`
--
ALTER TABLE `muscle_group`
  MODIFY `muscle_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `workout`
--
ALTER TABLE `workout`
  MODIFY `workout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exercise`
--
ALTER TABLE `exercise`
  ADD CONSTRAINT `exercise_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exercise_muscle_group`
--
ALTER TABLE `exercise_muscle_group`
  ADD CONSTRAINT `exercise_muscle_group_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`exercise_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exercise_muscle_group_ibfk_2` FOREIGN KEY (`muscle_group_id`) REFERENCES `muscle_group` (`muscle_group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exercise_set`
--
ALTER TABLE `exercise_set`
  ADD CONSTRAINT `exercise_set_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `exercise` (`exercise_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exercise_set_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workout` (`workout_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `workout`
--
ALTER TABLE `workout`
  ADD CONSTRAINT `workout_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
