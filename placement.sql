-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2024 at 08:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `placement`
--

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(11) NOT NULL,
  `college_name` varchar(100) NOT NULL,
  `intake_capacity` int(11) NOT NULL,
  `min_college_entrance_result` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college_name`, `intake_capacity`, `min_college_entrance_result`) VALUES
(1, 'College of Natural Science', 230, 55),
(2, 'College of Health Science', 80, 68),
(3, 'College of Engineering and Technology', 250, 40);

-- --------------------------------------------------------

--
-- Table structure for table `college_allocations`
--

CREATE TABLE `college_allocations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `college_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_allocations`
--

INSERT INTO `college_allocations` (`id`, `student_id`, `college_id`) VALUES
(1, 9, 2),
(2, 1, 1),
(3, 8, 2),
(4, 11, 3),
(5, 10, 3);

-- --------------------------------------------------------

--
-- Table structure for table `college_preferences`
--

CREATE TABLE `college_preferences` (
  `preference_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `college_id` int(11) DEFAULT NULL,
  `preference` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_preferences`
--

INSERT INTO `college_preferences` (`preference_id`, `student_id`, `college_id`, `preference`) VALUES
(9, 1, 1, 1),
(10, 1, 2, 2),
(11, 9, 1, 2),
(12, 9, 2, 1),
(13, 8, 1, 2),
(14, 8, 2, 1),
(15, 10, 1, 3),
(16, 10, 2, 1),
(17, 10, 3, 2),
(18, 11, 1, 3),
(19, 11, 2, 2),
(20, 11, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `college_id` int(11) NOT NULL,
  `intake_capacity` int(11) NOT NULL,
  `min_department_entrance_result` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `college_id`, `intake_capacity`, `min_department_entrance_result`) VALUES
(2, 'Biology', 1, 120, 66),
(3, 'Chemisrtry', 1, 50, 3.6),
(4, 'Pharmacy', 2, 80, 3.8),
(5, 'Mathematics', 1, 60, 3.5),
(6, 'Software Engineering ', 3, 250, 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'registrar'),
(3, 'student');

-- --------------------------------------------------------

--
-- Table structure for table `student_results`
--

CREATE TABLE `student_results` (
  `result_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `entrance_exam` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_results`
--

INSERT INTO `student_results` (`result_id`, `student_id`, `gpa`, `entrance_exam`) VALUES
(1, 1, 4.00, 650),
(2, 8, 4.00, 680),
(3, 9, 4.00, 690),
(4, 10, 3.00, 500),
(5, 11, 3.00, 550);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 3,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `email`, `password`, `gender`, `role_id`, `registration_date`) VALUES
(1, 'Aksumawit Yemane', 'Aksume', 'mebrhit765@gmail.com', '$2y$10$KsBN4ryUzP90r5guKhChie0Wq9z3Wo5645fj05Xukn6x44V94ZwQq', 'female', 3, '2024-05-25 11:54:55'),
(3, 'Melat Ermiyas', 'Melu', 'melat@gmail.com', '$2y$10$aJOXhDsr0Ooez1ip9PCUt.kwGOUX9ttB25SnrFvQTe/UnD6AwenEu', 'female', 1, '2024-05-25 12:26:34'),
(5, 'Reem Mohammed', 'Reem', 'reem@gmail.com', '$2y$10$hx60uRkxqW4YnLHlB/mBn.WAhfhgGz6F43vb0XcMIoWyThFDIHk76', 'female', 1, '2024-05-25 12:29:05'),
(6, 'Fikir Fikre', 'Fikir', 'fikir@gmail.com', '$2y$10$3OQ/RwgzG1kc1IsBPmKYuOSINK6vgvnFq0icRu84mW8Uhq00gVRu2', 'female', 2, '2024-05-25 12:41:51'),
(7, 'Muse Yemane', 'Muse', 'muse@gmail.com', '$2y$10$si8gUtT4XSE/8MawuIZWfOpiMT5lrMV7OFGyk950UI75dhSXClndW', 'male', 2, '2024-05-25 13:44:13'),
(8, 'Tekleab Yemane', 'Tekleab', 'baby@gmail.com', '$2y$10$GuzJHQg9K5vM2TTP5gdg2u8Ue6uj2Fcei5h1ZR7Z6nuNZmtiDnSLO', 'male', 3, '2024-05-25 13:45:25'),
(9, 'Merry Ermiyas', 'Merry', 'merry@gmail.com', '$2y$10$cqteTbtY9SQBFGjeDAGOcesedmwPnObSIaKeviXfqvmTJ3TzSVzQC', 'female', 3, '2024-05-25 18:59:34'),
(10, 'Dawit Yemane', 'Dawit', 'dawit@gmail.com', '$2y$10$BL1Vhg/cAG7u7Sw/5a30h.FWoFjMZfe9mGeCpRKp.TZfFPZaYKkr2', 'male', 3, '2024-05-26 10:44:48'),
(11, 'Yimegnushal Tadesse', 'Yimegnu', 'Yimegnu@gmail.com', '$2y$10$yh/qqBBuG5vmQkj5r9QnouEmaMPLcz9zxnnk2cVol7Cc.F4jwS3TK', 'female', 3, '2024-05-26 18:39:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `college_allocations`
--
ALTER TABLE `college_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `college_preferences`
--
ALTER TABLE `college_preferences`
  ADD PRIMARY KEY (`preference_id`),
  ADD UNIQUE KEY `unique_preference` (`student_id`,`college_id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `student_results`
--
ALTER TABLE `student_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `college_allocations`
--
ALTER TABLE `college_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `college_preferences`
--
ALTER TABLE `college_preferences`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_results`
--
ALTER TABLE `student_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `college_allocations`
--
ALTER TABLE `college_allocations`
  ADD CONSTRAINT `college_allocations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `college_allocations_ibfk_2` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`);

--
-- Constraints for table `college_preferences`
--
ALTER TABLE `college_preferences`
  ADD CONSTRAINT `college_preferences_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `college_preferences_ibfk_2` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`);

--
-- Constraints for table `student_results`
--
ALTER TABLE `student_results`
  ADD CONSTRAINT `student_results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
