-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2026 at 10:23 PM
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
-- Database: `sample`
--

-- --------------------------------------------------------

--
-- Table structure for table `eer_surveys`
--

CREATE TABLE `eer_surveys` (
  `eer_survey_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_anonymous` tinyint(1) DEFAULT 0,
  `survey_type` varchar(100) DEFAULT 'engagement',
  `created_by_employee` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eer_surveys`
--

INSERT INTO `eer_surveys` (`eer_survey_id`, `title`, `created_by`, `is_anonymous`, `survey_type`, `created_by_employee`) VALUES
(1, 'Satisfaction', 1, 0, 'engagement', NULL),
(2, 'Remote Work', 2, 0, 'engagement', NULL),
(3, 'remote work', 9, 0, 'engagement', NULL),
(4, 'rumor\'s', 9, 0, 'engagement', NULL),
(5, 'qwerty', 9, 0, 'engagement', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eer_surveys`
--
ALTER TABLE `eer_surveys`
  ADD PRIMARY KEY (`eer_survey_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_survey_employee` (`created_by_employee`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eer_surveys`
--
ALTER TABLE `eer_surveys`
  MODIFY `eer_survey_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eer_surveys`
--
ALTER TABLE `eer_surveys`
  ADD CONSTRAINT `eer_surveys_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_survey_employee` FOREIGN KEY (`created_by_employee`) REFERENCES `employees` (`employee_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
