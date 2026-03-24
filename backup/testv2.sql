-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 02:18 PM
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
(4, 'admin2', 'admin2@gmail.com', '$2y$10$rb0IhyEDoaFz5dHSUru8LOxmewdI1pUYvtIwLvf2gLdlX8RjSQ/kS', 'recruiter'),
(5, 'Mitzi Guarin', 'mitzi@gmail.com', '$2y$10$ipKeM4Uu8qMXJy6mWjSjjOb41QPYs4qzukUGSVe7nRb0/MtUj3UUm', 'recruiter');

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
(35, 56, 19, 'Accounting Officer', 'Robert', 'Pradilla', 'Toledo', 'jr', '2005-02-03', 21, '09123654789', 'male', 'Single', 'Quezon City', 'Metro Manila', 'Filipino', 'robertpradilla03@gmail.com', 'BLah blah blah blah', '2026-03-24 12:03:02', 'Hired', 'resume_56_1774353782.pdf');

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
(56, 'akali03', 'robertpradilla03@gmail.com', '$2y$10$IuJ6noxnvEMbsBHlcUvKDeY9e2qNAqK37OZRBRxOgqx0/lQPu4wou', 'Applicant'),
(57, 'test67', 'test67@gmail.com', '$2y$10$GJ/GqC5KEsJKquga61.Y1O4/TJZSkRqKMO7PCnxFPWjFIsg1GqYc.', 'Applicant'),
(58, 'akali03', 'akali03@gmail.com', '$2y$10$3lAtqNRJ2zkaRareAncI9OMOE4WwIAIwYSw2SYyjHQk0kX8XxqE.m', 'Applicant');

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
(28, 1, 'admin', 'Schedule Interview', 'Recruitment', 'Scheduled Onsite interview for applicant ID 27 on 2026-03-29 10:00', '2026-03-24 11:39:10'),
(29, 1, 'admin', 'Schedule Interview', 'Recruitment', 'Scheduled Onsite interview for applicant ID 35 on 2026-03-29 10:00', '2026-03-24 12:03:30'),
(30, 1, 'admin', 'Schedule Interview', 'Recruitment', 'Scheduled Onsite interview for applicant ID 35 on 2026-03-29 10:00', '2026-03-24 12:11:02'),
(31, 1, 'admin', 'Create Admin', 'Roles', 'Created admin account \'Mitzi Guarin\' with role recruiter', '2026-03-24 12:20:01'),
(32, 5, 'Mitzi Guarin', 'Schedule Interview', 'Recruitment', 'Scheduled Onsite interview for applicant ID 35 on 2026-03-20 10:00', '2026-03-24 12:22:05'),
(33, 5, 'Mitzi Guarin', 'Schedule Interview', 'Recruitment', 'Scheduled Onsite interview for applicant ID 35 on 2026-03-29 10:00', '2026-03-24 12:29:12'),
(34, 5, 'Mitzi Guarin', 'Interview Result', 'Recruitment', 'Interview ID 42 marked as Passed (Exam: 7/41)', '2026-03-24 12:30:06'),
(35, 1, 'admin', 'Approve Personal Info', 'Onboarding', 'Approved personal info for employee ID 15', '2026-03-24 12:50:38'),
(36, 1, 'admin', 'Schedule Orientation', 'Onboarding', 'Scheduled orientation dates for employee ID 15', '2026-03-24 12:51:07'),
(37, 1, 'admin', 'Update Orientation Status', 'Onboarding', 'Employee ID 15 Day 1 set to Completed', '2026-03-24 12:51:09'),
(38, 1, 'admin', 'Update Orientation Status', 'Onboarding', 'Employee ID 15 Day 2 set to Completed', '2026-03-24 12:51:11'),
(39, 1, 'admin', 'Update Orientation Status', 'Onboarding', 'Employee ID 15 Day 3 set to Completed', '2026-03-24 12:51:13'),
(40, 1, 'admin', 'Approve Documents', 'Onboarding', 'Approved documents for employee ID 15', '2026-03-24 12:51:43');

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
  `department` varchar(100) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `employment_status` enum('New Hire','Active','Inactive','Terminated') DEFAULT 'New Hire',
  `onboarding_status` enum('Not Started','In Progress','Completed') DEFAULT 'Not Started',
  `hired_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `applicant_id`, `department`, `username`, `password`, `employment_status`, `onboarding_status`, `hired_at`) VALUES
(15, 35, NULL, 'pradilla202635', '$2y$10$jN5pRZKj1udyqqxiaffc8uYmKs4bzcmosXWwfp1XQKsNjRBXhcykK', 'Active', 'Completed', '2026-03-24 05:30:02');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tin_photo_path` varchar(255) DEFAULT NULL,
  `sss_photo_path` varchar(255) DEFAULT NULL,
  `pagibig_photo_path` varchar(255) DEFAULT NULL,
  `philhealth_photo_path` varchar(255) DEFAULT NULL,
  `bank_photo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employee_onboarding`
--

INSERT INTO `employee_onboarding` (`onboarding_id`, `employee_id`, `applicant_id`, `personal_info_completed`, `personal_info_status`, `emergency_contact`, `emergency_phone`, `emergency_relationship`, `tin_number`, `sss_number`, `pagibig_number`, `philhealth_number`, `address`, `city`, `province`, `postal_code`, `bank_name`, `bank_account_number`, `documents_submitted`, `documents_status`, `government_id_path`, `diploma_tor_path`, `nbi_clearance_path`, `medical_certificate_path`, `orientation_completed`, `orientation_day1_date`, `orientation_day1_status`, `orientation_day2_date`, `orientation_day2_status`, `orientation_day3_date`, `orientation_day3_status`, `onboarding_status`, `onboarding_start_date`, `onboarding_completion_date`, `created_at`, `updated_at`, `tin_photo_path`, `sss_photo_path`, `pagibig_photo_path`, `philhealth_photo_path`, `bank_photo_path`) VALUES
(13, 15, 35, 1, 'Approved', 'Kevin Nash Fontanilla', '09123131231', 'Friend', '1231231231', '2123154564', '54564132156', '123213--21321-213', '13 katuparan ext kalayaan A', 'Quezon City', 'Metro Manila', '1126', 'BDO', '12315325412', 1, 'Approved', 'gov_id_15_1774356700.jpg', 'diploma_15_1774356700.jpg', 'nbi_15_1774356700.jpg', 'medical_15_1774356700.png', 1, '2026-04-02', 'Completed', '2026-04-03', 'Completed', '2026-04-04', 'Completed', 'Completed', '2026-03-24 05:30:38', '2026-03-24 05:51:43', '2026-03-24 12:30:38', '2026-03-24 12:51:43', 'uploads/personal/tin_photo_15_1774356222.jpg', 'uploads/personal/sss_photo_15_1774356222.jpg', 'uploads/personal/pagibig_photo_15_1774356222.png', 'uploads/personal/philhealth_photo_15_1774356222.png', 'uploads/personal/bank_photo_15_1774356222.png');

-- --------------------------------------------------------

--
-- Table structure for table `exam_answers`
--

CREATE TABLE `exam_answers` (
  `answer_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text DEFAULT NULL COMMENT 'for text questions',
  `answer_choice` enum('a','b','c','d') DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points_earned` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `question_id` int(11) NOT NULL,
  `job_title` varchar(255) DEFAULT NULL COMMENT 'NULL = applies to all jobs',
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','text') DEFAULT 'multiple_choice',
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_answer` enum('a','b','c','d') DEFAULT NULL,
  `points` int(11) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL COMMENT 'admin_id',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`question_id`, `job_title`, `question_text`, `question_type`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `points`, `created_by`, `created_at`) VALUES
(36, 'Accounting Officer', 'What is an asset?', 'multiple_choice', 'Something the company owes', 'Company expenses', 'Something the company owns', 'Company income', 'b', 1, 1, '2026-03-24 13:13:21'),
(37, 'Accounting Officer', 'Which of the following is a liability?', 'multiple_choice', 'Cash', 'Equipment', 'Accounts Payable', 'Revenue', 'c', 1, 1, '2026-03-24 13:13:21'),
(38, 'Accounting Officer', 'What is the normal balance of assets?', 'multiple_choice', 'Credit', 'Debit', 'Both', 'None', 'b', 1, 1, '2026-03-24 13:13:21'),
(39, 'Accounting Officer', 'Which financial statement shows financial position?', 'multiple_choice', 'Income Statement', 'Cash Flow Statement', 'Balance Sheet', 'Journal', 'c', 1, 1, '2026-03-24 13:13:21'),
(40, 'Accounting Officer', 'What is revenue?', 'multiple_choice', 'Money spent', 'Money earned', 'Money borrowed', 'Money saved', 'b', 1, 1, '2026-03-24 13:13:21'),
(41, 'Accounting Officer', 'What is the purpose of a trial balance?', 'multiple_choice', 'To record transactions', 'To check accuracy of accounts', 'To prepare invoices', 'To track inventory', 'b', 1, 1, '2026-03-24 13:13:21'),
(42, 'Accounting Officer', 'Which account is debited when cash is received?', 'multiple_choice', 'Cash', 'Revenue', 'Expense', 'Liability', 'a', 1, 1, '2026-03-24 13:13:21'),
(43, 'Accounting Officer', 'What is an expense?', 'multiple_choice', 'Income earned', 'Cost incurred', 'Asset owned', 'Liability owed', 'b', 1, 1, '2026-03-24 13:13:21'),
(44, 'Accounting Officer', 'Which of the following is an example of equity?', 'multiple_choice', 'Loan', 'Capital', 'Accounts Payable', 'Utilities', 'a', 1, 1, '2026-03-24 13:13:21'),
(45, 'Accounting Officer', 'What does GAAP stand for?', 'multiple_choice', 'General Accounting Application Process', 'Generally Accepted Accounting Principles', 'Government Accounting Approved Policy', 'General Account Analysis Plan', 'b', 1, 1, '2026-03-24 13:13:21'),
(46, 'Accounting Officer', 'Explain the difference between assets, liabilities, and equity.', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:13:21'),
(47, 'Accounting Officer', 'Describe the accounting cycle and its main steps.', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:13:21'),
(48, 'Accounting Officer', 'Explain the importance of accuracy in financial records.', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:13:21'),
(49, 'Accounting Officer', 'How do you ensure confidentiality of financial information?', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:13:21'),
(50, 'Accounting Officer', 'Describe how you would prepare a financial report.', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:13:21'),
(51, 'Front Desk Executive', 'What is the first thing you should do when a guest arrives', 'multiple_choice', 'Ask for payment', 'Greet them politely', 'Direct them to the restaurant', 'Check their luggage', 'b', 1, 1, '2026-03-24 13:18:25'),
(52, 'Front Desk Executive', 'A guest complains about a noisy room. You should:', 'multiple_choice', 'Ignore them', 'Apologize and offer a solution', 'Tell them it’s not your problem', 'Ask them to leave', 'b', 1, 1, '2026-03-24 13:18:25'),
(53, 'Front Desk Executive', 'What information is usually required at check-in?', 'multiple_choice', 'Guest name and ID', 'Favorite food', 'Job history', 'Travel insurance only', 'a', 1, 1, '2026-03-24 13:18:25'),
(54, 'Front Desk Executive', 'Which of the following is important for a front desk executive?', 'multiple_choice', 'Good communication', 'Cooking skills', 'Fast typing only', 'Cleaning tables', 'a', 1, 1, '2026-03-24 13:18:25'),
(55, 'Front Desk Executive', 'How should phone calls be answered?', 'multiple_choice', 'Politely, with hotel name', 'By ignoring the caller', 'Only after the third ring', 'With personal greeting', 'a', 1, 1, '2026-03-24 13:18:25'),
(56, 'Front Desk Executive', 'What is the primary role of the front desk?', 'multiple_choice', 'Greeting guests and managing reservations', 'Serving food', 'Cleaning rooms', 'Driving guests', 'a', 1, 1, '2026-03-24 13:18:25'),
(57, 'Front Desk Executive', 'If a guest requests late checkout, you should:', 'multiple_choice', 'Refuse immediately', 'Check availability and politely respond', 'Ignore the request', 'Call security', 'b', 1, 1, '2026-03-24 13:18:25'),
(58, 'Front Desk Executive', 'Which information is confidential?', 'multiple_choice', 'Guest payment details', 'Hotel address', 'Check-out time', 'Lobby location', 'a', 1, 1, '2026-03-24 13:18:25'),
(59, 'Front Desk Executive', 'What should you do if a guest leaves a negative review?', 'multiple_choice', 'Respond politely and try to resolve the issue', 'Delete it', 'Ignore it', 'Argue publicly', 'a', 1, 1, '2026-03-24 13:18:25'),
(60, 'Front Desk Executive', 'Which software might a front desk executive use?', 'multiple_choice', 'Property Management System (PMS)', 'Accounting software only', 'Cooking app', 'Word processor only', 'a', 1, 1, '2026-03-24 13:18:25'),
(61, 'Front Desk Executive', 'How would you greet a guest arriving at the hotel?', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:18:25'),
(62, 'Front Desk Executive', 'What steps would you follow to check in a guest?', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:18:25'),
(63, 'Front Desk Executive', 'How would you handle a guest complaint about a room?', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:18:25'),
(64, 'Front Desk Executive', 'Describe how you manage multiple guests at the front desk during a busy hour.', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:18:25'),
(65, 'Front Desk Executive', 'What would you do if a guest lost their room key?', 'text', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-24 13:18:25');

-- --------------------------------------------------------

--
-- Table structure for table `exam_sessions`
--

CREATE TABLE `exam_sessions` (
  `session_id` int(11) NOT NULL,
  `interview_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `total_score` int(11) DEFAULT 0,
  `total_points` int(11) DEFAULT 0,
  `status` enum('Not Started','In Progress','Submitted') DEFAULT 'Not Started',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_sessions`
--

INSERT INTO `exam_sessions` (`session_id`, `interview_id`, `applicant_id`, `started_at`, `submitted_at`, `total_score`, `total_points`, `status`, `created_at`) VALUES
(8, 42, 35, '2026-03-24 12:29:25', '2026-03-24 12:29:49', 7, 41, 'Submitted', '2026-03-24 12:29:25');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `interviewer_id` int(11) DEFAULT NULL COMMENT 'admin_id of who scheduled the interview',
  `interviewer_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`interview_id`, `applicant_id`, `date`, `time`, `type`, `result`, `created_at`, `interviewer_id`, `interviewer_name`) VALUES
(42, 35, '2026-03-29', '10:00:00', 'Onsite', 'Passed', '2026-03-24 12:29:07', 5, 'Mitzi Guarin');

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
-- Indexes for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `exam_sessions`
--
ALTER TABLE `exam_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD UNIQUE KEY `interview_id` (`interview_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `applicantss`
--
ALTER TABLE `applicantss`
  MODIFY `apply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `applicants_account`
--
ALTER TABLE `applicants_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `employee_onboarding`
--
ALTER TABLE `employee_onboarding`
  MODIFY `onboarding_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `exam_answers`
--
ALTER TABLE `exam_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `exam_sessions`
--
ALTER TABLE `exam_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `interview_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
-- Constraints for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD CONSTRAINT `exam_answers_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `exam_sessions` (`session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_sessions`
--
ALTER TABLE `exam_sessions`
  ADD CONSTRAINT `exam_sessions_ibfk_1` FOREIGN KEY (`interview_id`) REFERENCES `interviews` (`interview_id`) ON DELETE CASCADE;

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
