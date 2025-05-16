-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 16, 2025 at 09:34 PM
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
-- Table structure for table `excercise`
--

CREATE TABLE `excercise` (
  `exercise_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `excercise`
--

INSERT INTO `excercise` (`exercise_id`, `user_id`, `name`, `description`) VALUES
(1, 1, 'Bench press', 'Základní cvik na rozvoj prsních svalů, tricepsů a ramen.'),
(2, 1, 'Dřep', 'Komplexní cvik na posílení dolní poloviny těla.'),
(3, 2, 'Shyby', 'Cvik na rozvoj zádových svalů a bicepsů.');

-- --------------------------------------------------------

--
-- Table structure for table `excercise_muscle_group`
--

CREATE TABLE `excercise_muscle_group` (
  `exercise_id` int(11) NOT NULL,
  `muscle_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `excercise_muscle_group`
--

INSERT INTO `excercise_muscle_group` (`exercise_id`, `muscle_group_id`) VALUES
(1, 1),
(1, 5),
(1, 6),
(2, 3),
(3, 2),
(3, 4);

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
(3, 'Nohy'),
(4, 'Biceps'),
(5, 'Triceps'),
(6, 'Ramena');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `surname`, `email`, `password`) VALUES
(1, 'Jan', 'Novak', 'jan.novak@email.cz', 'heslo123'),
(2, 'Petr', 'Svoboda', 'petr.svoboda@email.cz', 'tajneheslo');

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
(1, 1, 'Trénink prsa a nohy', '2025-05-10 15:00:00', 'Trénink prsa a nohy'),
(2, 2, 'Záda a biceps', '2025-05-12 16:30:00', 'Záda a biceps');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `excercise`
--
ALTER TABLE `excercise`
  ADD PRIMARY KEY (`exercise_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `excercise_muscle_group`
--
ALTER TABLE `excercise_muscle_group`
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
-- AUTO_INCREMENT for table `excercise`
--
ALTER TABLE `excercise`
  MODIFY `exercise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exercise_set`
--
ALTER TABLE `exercise_set`
  MODIFY `exercise_set_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `muscle_group`
--
ALTER TABLE `muscle_group`
  MODIFY `muscle_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `workout`
--
ALTER TABLE `workout`
  MODIFY `workout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `excercise`
--
ALTER TABLE `excercise`
  ADD CONSTRAINT `excercise_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `excercise_muscle_group`
--
ALTER TABLE `excercise_muscle_group`
  ADD CONSTRAINT `excercise_muscle_group_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `excercise` (`exercise_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `excercise_muscle_group_ibfk_2` FOREIGN KEY (`muscle_group_id`) REFERENCES `muscle_group` (`muscle_group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `exercise_set`
--
ALTER TABLE `exercise_set`
  ADD CONSTRAINT `exercise_set_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `excercise` (`exercise_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exercise_set_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workout` (`workout_id`) ON UPDATE CASCADE;

--
-- Constraints for table `workout`
--
ALTER TABLE `workout`
  ADD CONSTRAINT `workout_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
