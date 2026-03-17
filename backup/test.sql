-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 05:29 PM
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
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','hr_manager','recruiter') NOT NULL DEFAULT 'recruiter'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$rONDzGjpGPQhUMG6wXwyEeNtfNdeKsjDfsc2926HKjB0AvZkW/yWq', 'super_admin'),
(2, 'akali04', 'akali04123@gmail.com', '$2y$10$vHDat8r6wOBVTIGfRg8iP.7dx8FmwdMfiuQ2Vo8FPuZpuSnQddqSO', 'recruiter'),
(3, 'admin1', 'admin321231@gmail.com', '$2y$10$Hc6PbwG/nLaNuiREYhD2IeSitZFLW2KMrYgIx00X0V5ViGVhcC78S', 'hr_manager'),
(4, 'admin2', 'admin2@gmail.com', '$2y$10$rb0IhyEDoaFz5dHSUru8LOxmewdI1pUYvtIwLvf2gLdlX8RjSQ/kS', 'recruiter');

-- --------------------------------------------------------

--
-- Table structure for table `applicantss`
--

CREATE TABLE `applicantss` (
  `apply_id` int(11) NOT NULL,
  `applicant_id` int(10) UNSIGNED DEFAULT NULL,
  `job_id` int(255) UNSIGNED NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `suffix` enum('none','jr','sr','ii','iii','iv') NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(200) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `gender` enum('male','female','','') NOT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `skills` text NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Interview','Hired','Rejected') DEFAULT 'Pending',
  `resume_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `applicantss`
--

INSERT INTO `applicantss` (`apply_id`, `applicant_id`, `job_id`, `job_title`, `firstname`, `lastname`, `middle_name`, `suffix`, `birthdate`, `age`, `phone`, `gender`, `civil_status`, `city`, `province`, `nationality`, `email`, `skills`, `applied_at`, `status`, `resume_path`) VALUES
(25, 56, 19, 'Accounting Officer', 'Robert', 'Pradilla', 'Toledo', 'jr', '2005-02-03', 21, '09263422473', 'male', 'Single', 'Quezon City', 'Metro Manila', 'Filipino', 'robertpradilla03@gmail.com', 'blah blah blah blah blah', '2026-03-17 15:52:47', 'Hired', 'resume_56_1773762767.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `applicants_account`
--

CREATE TABLE `applicants_account` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'Applicant'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `applicants_account`
--

INSERT INTO `applicants_account` (`id`, `username`, `email`, `password`, `role`) VALUES
(56, 'akali03', 'robertpradilla03@gmail.com', '$2y$10$IuJ6noxnvEMbsBHlcUvKDeY9e2qNAqK37OZRBRxOgqx0/lQPu4wou', 'Applicant');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `admin_id`, `admin_name`, `action`, `module`, `description`, `created_at`) VALUES
(6, 1, 'admin', 'Update Job Status', 'Recruitment', 'Job ID 14 set to open', '2026-03-17 15:35:33'),
(7, 1, 'admin', 'Schedule Interview', 'Recruitment', 'Scheduled Onsite interview for applicant ID 25 on 2026-03-20 10:00', '2026-03-17 15:53:38'),
(8, 1, 'admin', 'Schedule Interview', 'Recruitment', 'Scheduled Onsite interview for applicant ID 25 on 2026-03-20 10:00', '2026-03-17 15:55:31'),
(9, 1, 'admin', 'Interview Result', 'Recruitment', 'Interview ID 35 marked as Passed', '2026-03-17 16:15:08'),
(10, 1, 'admin', 'Schedule Orientation', 'Onboarding', 'Scheduled orientation dates for employee ID 14', '2026-03-17 16:18:52'),
(11, 1, 'admin', 'Update Orientation Status', 'Onboarding', 'Employee ID 14 Day 1 set to Completed', '2026-03-17 16:18:59'),
(12, 1, 'admin', 'Update Orientation Status', 'Onboarding', 'Employee ID 14 Day 2 set to Pending', '2026-03-17 16:19:00'),
(13, 1, 'admin', 'Update Orientation Status', 'Onboarding', 'Employee ID 14 Day 2 set to Completed', '2026-03-17 16:19:03'),
(14, 1, 'admin', 'Update Orientation Status', 'Onboarding', 'Employee ID 14 Day 3 set to Completed', '2026-03-17 16:19:06'),
(15, 1, 'admin', 'Approve Personal Info', 'Onboarding', 'Approved personal info for employee ID 14', '2026-03-17 16:19:17'),
(16, 1, 'admin', 'Approve Documents', 'Onboarding', 'Approved documents for employee ID 14', '2026-03-17 16:19:19'),
(17, 1, 'admin', 'Create Review', 'Performance', 'Created probation review for employee ID 14 (2026-03-17 to 2026-06-15)', '2026-03-17 16:20:01'),
(18, 1, 'admin', 'Add Goal', 'Performance', 'Added goal \'Complete Training\' to review ID 6', '2026-03-17 16:20:50'),
(19, 1, 'admin', 'Save Ratings', 'Performance', 'Saved ratings for review ID 6', '2026-03-17 16:21:16'),
(20, 1, 'admin', 'Add Feedback', 'Performance', 'Added feedback to review ID 6', '2026-03-17 16:21:45'),
(21, 1, 'admin', 'Update Goal', 'Performance', 'Goal ID 5 set to Achieved (review 6)', '2026-03-17 16:21:49'),
(22, 1, 'admin', 'Finalize Review', 'Performance', 'Review ID 6 finalized as Passed', '2026-03-17 16:21:54'),
(23, 1, 'admin', 'Post Recognition', 'Recognition', 'Awarded \'Employee of the Month\' to employee ID 14', '2026-03-17 16:22:24');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Front Office', 'Guest services, check-in/check-out, reservations', 'Active', '2026-03-14 07:38:24', '2026-03-14 07:38:24'),
(2, 'Kitchen', 'Food preparation, cooking, menu planning', 'Active', '2026-03-14 07:38:24', '2026-03-14 07:38:24'),
(3, 'Food & Beverage', 'Restaurant service, bar, room service', 'Active', '2026-03-14 07:38:24', '2026-03-14 07:38:24'),
(4, 'Housekeeping', 'Room cleaning, laundry, maintenance of cleanliness', 'Active', '2026-03-14 07:38:24', '2026-03-14 07:38:24'),
(5, 'Maintenance', 'Facility repairs, equipment maintenance', 'Active', '2026-03-14 07:38:24', '2026-03-14 07:38:24'),
(6, 'Sales & Marketing', 'Promotions, client relations, marketing campaigns', 'Active', '2026-03-14 07:38:24', '2026-03-14 07:38:24'),
(7, 'Accounting', 'Finance, payroll, budgeting', 'Active', '2026-03-14 07:38:24', '2026-03-14 07:38:24');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `employment_status` enum('New Hire','Active','Inactive','Terminated') DEFAULT 'New Hire',
  `onboarding_status` enum('Not Started','In Progress','Completed') DEFAULT 'Not Started',
  `hired_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `applicant_id`, `username`, `password`, `employment_status`, `onboarding_status`, `hired_at`) VALUES
(14, 25, 'pradilla202625', '$2y$10$wB0MyS/sZHnQ0ppL6mB9deMPfeyMHv7VMH6P3epnAqjzosnX8wo3q', 'Active', 'Completed', '2026-03-17 09:15:04');

-- --------------------------------------------------------

--
-- Table structure for table `employee_onboarding`
--

CREATE TABLE `employee_onboarding` (
  `onboarding_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `personal_info_completed` tinyint(1) DEFAULT 0,
  `personal_info_status` enum('Not Submitted','Pending Review','Approved','Rejected') DEFAULT 'Not Submitted',
  `emergency_contact` varchar(255) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `emergency_relationship` varchar(50) DEFAULT NULL,
  `tin_number` varchar(50) DEFAULT NULL,
  `sss_number` varchar(50) DEFAULT NULL,
  `pagibig_number` varchar(50) DEFAULT NULL,
  `philhealth_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `documents_submitted` tinyint(1) DEFAULT 0,
  `documents_status` enum('Not Submitted','Pending Review','Approved','Rejected') DEFAULT 'Not Submitted',
  `government_id_path` varchar(255) DEFAULT NULL,
  `diploma_tor_path` varchar(255) DEFAULT NULL,
  `nbi_clearance_path` varchar(255) DEFAULT NULL,
  `medical_certificate_path` varchar(255) DEFAULT NULL,
  `orientation_completed` tinyint(1) DEFAULT 0,
  `orientation_day1_date` date DEFAULT NULL,
  `orientation_day1_status` enum('Pending','Completed','Missed') DEFAULT 'Pending',
  `orientation_day2_date` date DEFAULT NULL,
  `orientation_day2_status` enum('Pending','Completed','Missed') DEFAULT 'Pending',
  `orientation_day3_date` date DEFAULT NULL,
  `orientation_day3_status` enum('Pending','Completed','Missed') DEFAULT 'Pending',
  `onboarding_status` enum('Not Started','In Progress','Completed') DEFAULT 'Not Started',
  `onboarding_start_date` datetime DEFAULT current_timestamp(),
  `onboarding_completion_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employee_onboarding`
--

INSERT INTO `employee_onboarding` (`onboarding_id`, `employee_id`, `applicant_id`, `personal_info_completed`, `personal_info_status`, `emergency_contact`, `emergency_phone`, `emergency_relationship`, `tin_number`, `sss_number`, `pagibig_number`, `philhealth_number`, `address`, `city`, `province`, `postal_code`, `bank_name`, `bank_account_number`, `documents_submitted`, `documents_status`, `government_id_path`, `diploma_tor_path`, `nbi_clearance_path`, `medical_certificate_path`, `orientation_completed`, `orientation_day1_date`, `orientation_day1_status`, `orientation_day2_date`, `orientation_day2_status`, `orientation_day3_date`, `orientation_day3_status`, `onboarding_status`, `onboarding_start_date`, `onboarding_completion_date`, `created_at`, `updated_at`) VALUES
(12, 14, 25, 1, 'Approved', 'Kevin Nash Fontanilla', '09646781342', 'Friend', '012391203012', '1238912831', '12312321312', '19283912831', '10 Payatas Qc malapit sa basurahan', 'Quezon City', 'Metro Manila', '1131', 'Metrobank', '313512313', 1, 'Approved', 'gov_id_14_1773764304.pdf', 'diploma_14_1773764304.jpg', 'nbi_14_1773764304.pdf', 'medical_14_1773764304.jpg', 1, '2026-03-23', 'Completed', '2026-03-24', 'Completed', '2026-03-25', 'Completed', 'Completed', '2026-03-17 09:16:35', '2026-03-17 09:19:19', '2026-03-17 16:16:35', '2026-03-17 16:19:19');

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `interview_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `type` enum('Online','Onsite') NOT NULL,
  `result` enum('Pending','Passed','Failed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`interview_id`, `applicant_id`, `date`, `time`, `type`, `result`, `created_at`) VALUES
(35, 25, '2026-03-20', '10:00:00', 'Onsite', 'Passed', '2026-03-17 15:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `job_openings`
--

CREATE TABLE `job_openings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `department` varchar(100) NOT NULL,
  `responsibilities` text NOT NULL,
  `qualifications` text NOT NULL,
  `benefits` text NOT NULL,
  `location` text NOT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `job_openings`
--

INSERT INTO `job_openings` (`id`, `title`, `department`, `responsibilities`, `qualifications`, `benefits`, `location`, `status`, `created_at`) VALUES
(14, 'Front Desk Executive', 'Front Office', 'Greet and welcome guests with a friendly attitude\nManage check-ins and check-outs efficiently\nHandle reservations and guest inquiries\nCoordinate with housekeeping and other departments\nMaintain accurate records of guest information', 'High school diploma or equivalent\nPrevious hospitality or customer service experience preferred\nExcellent communication and interpersonal skills\nAbility to work flexible shifts, including weekends and holidays', 'Competitive salary\nStaff meals provided\nHealth insurance\nAccommodation options (if required)\nTraining and career development programs', 'Luxor Grand Hotel, Downtown City', 'open', '2026-03-12 11:15:39'),
(19, 'Accounting Officer', 'Accounting', 'Prepare and process payroll accurately and on time\nRecord and reconcile daily financial transactions\nAssist in budget preparation and financial reporting\nMonitor accounts payable and receivable\nEnsure compliance with tax regulations and hotel financial policies', 'Degree in Accounting, Finance, or a related field\nCPA license is an advantage\nAt least 2 years accounting experience preferably in hospitality\nProficient in accounting software and MS Excel\nHigh attention to detail and strong analytical skills', 'Competitive salary\nStaff meals provided\nHealth insurance\nHMO coverage\nTraining and career development programs', 'Luxor Grand Hotel, Downtown City\r\nResponsibilities:', 'open', '2026-03-14 14:37:25');

-- --------------------------------------------------------

--
-- Table structure for table `probation_feedback`
--

CREATE TABLE `probation_feedback` (
  `feedback_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `feedback_date` date NOT NULL,
  `strengths` text DEFAULT NULL,
  `improvements` text DEFAULT NULL,
  `given_by` int(11) DEFAULT NULL COMMENT 'admin_id',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `probation_feedback`
--

INSERT INTO `probation_feedback` (`feedback_id`, `review_id`, `feedback_date`, `strengths`, `improvements`, `given_by`, `created_at`) VALUES
(6, 6, '2026-03-17', 'Teamwork', 'communication skills', 1, '2026-03-17 16:21:45');

-- --------------------------------------------------------

--
-- Table structure for table `probation_goals`
--

CREATE TABLE `probation_goals` (
  `goal_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `goal_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `status` enum('Pending','In Progress','Achieved','Not Achieved') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `probation_goals`
--

INSERT INTO `probation_goals` (`goal_id`, `review_id`, `goal_title`, `description`, `target_date`, `status`) VALUES
(5, 6, 'Complete Training', 'galingan mo', '2026-03-30', 'Achieved');

-- --------------------------------------------------------

--
-- Table structure for table `probation_ratings`
--

CREATE TABLE `probation_ratings` (
  `rating_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL COMMENT 'e.g. Work Quality, Attendance, Teamwork',
  `score` tinyint(4) NOT NULL COMMENT '1-5 scale'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `probation_ratings`
--

INSERT INTO `probation_ratings` (`rating_id`, `review_id`, `category`, `score`) VALUES
(21, 6, 'Work Quality', 5),
(22, 6, 'Attendance & Punctuality', 5),
(23, 6, 'Teamwork & Collaboration', 5),
(24, 6, 'Communication', 1),
(25, 6, 'Initiative & Attitude', 5);

-- --------------------------------------------------------

--
-- Table structure for table `probation_reviews`
--

CREATE TABLE `probation_reviews` (
  `review_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `probation_start` date NOT NULL,
  `probation_end` date NOT NULL,
  `status` enum('Ongoing','Passed','Failed','Extended') DEFAULT 'Ongoing',
  `final_remarks` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL COMMENT 'admin_id',
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `probation_reviews`
--

INSERT INTO `probation_reviews` (`review_id`, `employee_id`, `probation_start`, `probation_end`, `status`, `final_remarks`, `reviewed_by`, `reviewed_at`, `created_at`) VALUES
(6, 14, '2026-03-17', '2026-06-15', 'Passed', '', 1, '2026-03-17 16:21:54', '2026-03-17 16:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `recognition_posts`
--

CREATE TABLE `recognition_posts` (
  `post_id` int(11) NOT NULL,
  `nominee_employee_id` int(11) NOT NULL,
  `nominated_by_employee_id` int(11) DEFAULT NULL COMMENT 'NULL = admin awarded',
  `nominated_by_admin_id` int(11) DEFAULT NULL,
  `award_type` enum('Employee of the Month','Peer Appreciation','Above & Beyond','Best Attendance','Team Player','Innovation Award') NOT NULL,
  `message` text NOT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `recognition_posts`
--

INSERT INTO `recognition_posts` (`post_id`, `nominee_employee_id`, `nominated_by_employee_id`, `nominated_by_admin_id`, `award_type`, `message`, `is_public`, `created_at`) VALUES
(10, 14, NULL, 1, 'Employee of the Month', 'Super galing mo bossing', 1, '2026-03-17 16:22:24');

-- --------------------------------------------------------

--
-- Table structure for table `recognition_reactions`
--

CREATE TABLE `recognition_reactions` (
  `reaction_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`key`, `value`, `updated_at`) VALUES
('company_address', '123 Grand Avenue, Makati City, Metro Manila', '2026-03-17 15:42:41'),
('company_email', 'pradillarogin@gmail.com', '2026-03-17 13:25:53'),
('company_name', 'TechnoVista', '2026-03-17 13:33:26'),
('email_signature', 'Best regards,\r\nHR Team\r\nTechnoVista', '2026-03-17 13:33:26'),
('probation_days', '90', '2026-03-17 13:16:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Applicant') DEFAULT 'Applicant',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'awaw', '$2y$10$vQIo5LY99Xb9HcVuFCQKI.O56z0dwzeq/PtznKR43zyZ4NmY6ZqOi', 'Applicant', '2026-02-07 12:56:55'),
(3, 'asdasdas', '$2y$10$6Eqeh8H3Rxwb8k.cvWJ2culLQ5ePA39b.IFuKXSWxHlBEpbVtkQy6', 'Applicant', '2026-02-07 12:57:12'),
(5, 'test', '$2y$10$EGBGEq.XVitHlKW6Cpd0cO39fEkRJLvII4Eoqkd/nm3oOchmxSRfC', 'Applicant', '2026-02-08 14:06:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applicantss`
--
ALTER TABLE `applicantss`
  ADD PRIMARY KEY (`apply_id`);

--
-- Indexes for table `applicants_account`
--
ALTER TABLE `applicants_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_name` (`name`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `employee_onboarding`
--
ALTER TABLE `employee_onboarding`
  ADD PRIMARY KEY (`onboarding_id`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_onboarding_status` (`onboarding_status`),
  ADD KEY `idx_orientation_dates` (`orientation_day1_date`,`orientation_day2_date`,`orientation_day3_date`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`interview_id`);

--
-- Indexes for table `job_openings`
--
ALTER TABLE `job_openings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `probation_feedback`
--
ALTER TABLE `probation_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indexes for table `probation_goals`
--
ALTER TABLE `probation_goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indexes for table `probation_ratings`
--
ALTER TABLE `probation_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indexes for table `probation_reviews`
--
ALTER TABLE `probation_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- Indexes for table `recognition_posts`
--
ALTER TABLE `recognition_posts`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `recognition_reactions`
--
ALTER TABLE `recognition_reactions`
  ADD PRIMARY KEY (`reaction_id`),
  ADD UNIQUE KEY `unique_reaction` (`post_id`,`employee_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `applicantss`
--
ALTER TABLE `applicantss`
  MODIFY `apply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `applicants_account`
--
ALTER TABLE `applicants_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `employee_onboarding`
--
ALTER TABLE `employee_onboarding`
  MODIFY `onboarding_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `interview_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `job_openings`
--
ALTER TABLE `job_openings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `probation_feedback`
--
ALTER TABLE `probation_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `probation_goals`
--
ALTER TABLE `probation_goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `probation_ratings`
--
ALTER TABLE `probation_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `probation_reviews`
--
ALTER TABLE `probation_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `recognition_posts`
--
ALTER TABLE `recognition_posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `recognition_reactions`
--
ALTER TABLE `recognition_reactions`
  MODIFY `reaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_onboarding`
--
ALTER TABLE `employee_onboarding`
  ADD CONSTRAINT `employee_onboarding_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_onboarding_ibfk_2` FOREIGN KEY (`applicant_id`) REFERENCES `applicantss` (`apply_id`) ON DELETE CASCADE;

--
-- Constraints for table `probation_feedback`
--
ALTER TABLE `probation_feedback`
  ADD CONSTRAINT `probation_feedback_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `probation_reviews` (`review_id`) ON DELETE CASCADE;

--
-- Constraints for table `probation_goals`
--
ALTER TABLE `probation_goals`
  ADD CONSTRAINT `probation_goals_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `probation_reviews` (`review_id`) ON DELETE CASCADE;

--
-- Constraints for table `probation_ratings`
--
ALTER TABLE `probation_ratings`
  ADD CONSTRAINT `probation_ratings_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `probation_reviews` (`review_id`) ON DELETE CASCADE;

--
-- Constraints for table `recognition_reactions`
--
ALTER TABLE `recognition_reactions`
  ADD CONSTRAINT `recognition_reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `recognition_posts` (`post_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
