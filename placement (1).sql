-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2024 at 11:27 PM
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
  `min_college_entrance_result` float NOT NULL,
  `college_category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college_name`, `intake_capacity`, `min_college_entrance_result`, `college_category`) VALUES
(7, 'College of Engineering and Technology', 520, 0, 'Engineering and Technology'),
(8, 'College of Natural Science', 450, 0, 'Natural Science'),
(10, 'College of Health Science', 440, 0, 'Health Science');

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
(1, 20, 8),
(2, 18, 10),
(3, 19, 10),
(4, 17, 7);

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
(21, 17, 7, 1),
(22, 17, 8, 2),
(23, 17, 10, 3),
(24, 18, 7, 3),
(25, 18, 8, 2),
(26, 18, 10, 1),
(27, 19, 7, 2),
(28, 19, 8, 3),
(29, 19, 10, 1),
(30, 20, 7, 2),
(31, 20, 8, 1),
(32, 20, 10, 3);

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
(7, 'Software Engineering ', 7, 100, 0),
(8, 'Civil Engineering', 7, 150, 0),
(9, 'Information System', 7, 70, 0),
(10, 'Biology', 8, 100, 0),
(11, 'Chemisrtry', 8, 80, 0),
(12, 'Geology', 8, 60, 0),
(13, 'Mathematics', 8, 50, 0),
(14, 'Physics ', 8, 80, 0),
(15, 'Statstics', 8, 80, 0),
(16, 'Chemical Engineering', 7, 50, 0),
(17, 'Mechanical Engineering', 7, 80, 0),
(18, 'Medicine ', 10, 120, 0),
(19, 'Dental Medicine ', 10, 100, 0),
(20, 'Pharmacy', 10, 90, 0),
(21, 'Veterinary ', 10, 60, 0),
(22, 'Midwifery ', 10, 70, 0);

-- --------------------------------------------------------

--
-- Table structure for table `department_allocations`
--

CREATE TABLE `department_allocations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_allocations`
--

INSERT INTO `department_allocations` (`id`, `student_id`, `department_id`) VALUES
(1, 20, 10),
(2, 18, 18),
(3, 19, 18),
(4, 17, 7);

-- --------------------------------------------------------

--
-- Table structure for table `department_preferences`
--

CREATE TABLE `department_preferences` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `preference` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_preferences`
--

INSERT INTO `department_preferences` (`id`, `student_id`, `department_id`, `preference`) VALUES
(6, 20, 10, 1),
(7, 20, 11, 2),
(8, 20, 12, 3),
(9, 20, 13, 4),
(10, 20, 14, 5),
(11, 20, 15, 6),
(12, 18, 18, 1),
(13, 18, 19, 2),
(14, 18, 20, 4),
(15, 18, 21, 3),
(16, 18, 22, 5),
(17, 19, 18, 2),
(18, 19, 19, 1),
(19, 19, 20, 3),
(20, 19, 21, 4),
(21, 19, 22, 5),
(22, 17, 7, 1),
(23, 17, 8, 4),
(24, 17, 9, 2),
(25, 17, 16, 5),
(26, 17, 17, 3);

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
-- Table structure for table `student_additional_results`
--

CREATE TABLE `student_additional_results` (
  `student_id` int(11) NOT NULL,
  `second_sem_gpa` float DEFAULT NULL,
  `coc_exam_result` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_additional_results`
--

INSERT INTO `student_additional_results` (`student_id`, `second_sem_gpa`, `coc_exam_result`) VALUES
(17, 3.5, NULL),
(18, NULL, 80),
(19, NULL, 70),
(20, 3.5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_results`
--

CREATE TABLE `student_results` (
  `result_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `gpa` float DEFAULT NULL,
  `entrance_exam` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_results`
--

INSERT INTO `student_results` (`result_id`, `student_id`, `gpa`, `entrance_exam`) VALUES
(6, 17, 3.6, 485),
(7, 18, 3.8, 550),
(8, 19, 3.9, 590),
(9, 20, 3.9, 540);

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
(3, 'Melat Ermiyas', 'Melu', 'melat@gmail.com', '$2y$10$aJOXhDsr0Ooez1ip9PCUt.kwGOUX9ttB25SnrFvQTe/UnD6AwenEu', 'female', 1, '2024-05-25 12:26:34'),
(5, 'Reem Mohammed', 'Reem', 'reem@gmail.com', '$2y$10$hx60uRkxqW4YnLHlB/mBn.WAhfhgGz6F43vb0XcMIoWyThFDIHk76', 'female', 1, '2024-05-25 12:29:05'),
(6, 'Fikir Fikre', 'Fikir', 'fikir@gmail.com', '$2y$10$3OQ/RwgzG1kc1IsBPmKYuOSINK6vgvnFq0icRu84mW8Uhq00gVRu2', 'female', 2, '2024-05-25 12:41:51'),
(7, 'Muse Yemane', 'Muse', 'muse@gmail.com', '$2y$10$si8gUtT4XSE/8MawuIZWfOpiMT5lrMV7OFGyk950UI75dhSXClndW', 'male', 2, '2024-05-25 13:44:13'),
(17, 'Merry Ermiyas', 'Merry', 'merry@gmail.com', '$2y$10$Y3Mc/6vkeukO0b7HommW2uo92KMhyRA.WjLePHuiEqO9XpRf4O2rG', 'female', 3, '2024-05-28 07:56:19'),
(18, 'Aksumawit Yemane', 'Aksume', 'aksum@gmail.com', '$2y$10$lb0yOmA7HP3yjxdProWwPO5JyqE7fxgiLdCwulGY/G/8TDJ1uCYj.', 'female', 3, '2024-05-28 08:22:59'),
(19, 'Tekleab Yemane', 'Tekleab', 'tekleab@gmail.com', '$2y$10$eQb/Am3FyaFCS8cnSkgWzeJ5ciqwVGlmaaq7qw9RTSFE5h8Bvf.4y', 'male', 3, '2024-05-28 17:48:35'),
(20, 'Yimegnushal Tadesse', 'Yimegnu', 'Yimegnu@gmail.com', '$2y$10$RyDfll2La9hIxszuOsY0EOhrt2DbEulr9uvkHe/ik7pm22lBLBCEK', 'female', 3, '2024-05-28 18:52:45');

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
-- Indexes for table `department_allocations`
--
ALTER TABLE `department_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `department_preferences`
--
ALTER TABLE `department_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `student_additional_results`
--
ALTER TABLE `student_additional_results`
  ADD PRIMARY KEY (`student_id`);

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
  MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `college_allocations`
--
ALTER TABLE `college_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `college_preferences`
--
ALTER TABLE `college_preferences`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `department_allocations`
--
ALTER TABLE `department_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `department_preferences`
--
ALTER TABLE `department_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_results`
--
ALTER TABLE `student_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
-- Constraints for table `department_allocations`
--
ALTER TABLE `department_allocations`
  ADD CONSTRAINT `department_allocations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `department_allocations_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `department_preferences`
--
ALTER TABLE `department_preferences`
  ADD CONSTRAINT `department_preferences_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `department_preferences_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `student_additional_results`
--
ALTER TABLE `student_additional_results`
  ADD CONSTRAINT `student_additional_results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `student_results`
--
ALTER TABLE `student_results`
  ADD CONSTRAINT `student_results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
