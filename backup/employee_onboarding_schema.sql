-- Employee Onboarding Table Schema
-- Run this SQL to create the employee_onboarding table

CREATE TABLE IF NOT EXISTS `employee_onboarding` (
    `onboarding_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `applicant_id` INT NOT NULL,
    
    -- Personal Information Completion
    `personal_info_completed` TINYINT(1) DEFAULT 0,
    `personal_info_status` ENUM('Not Submitted', 'Pending Review', 'Approved', 'Rejected') DEFAULT 'Not Submitted',
    `emergency_contact` VARCHAR(255) DEFAULT NULL,
    `emergency_phone` VARCHAR(20) DEFAULT NULL,
    `emergency_relationship` VARCHAR(50) DEFAULT NULL,
    `tin_number` VARCHAR(50) DEFAULT NULL,
    `sss_number` VARCHAR(50) DEFAULT NULL,
    `pagibig_number` VARCHAR(50) DEFAULT NULL,
    `philhealth_number` VARCHAR(50) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `province` VARCHAR(100) DEFAULT NULL,
    `postal_code` VARCHAR(10) DEFAULT NULL,
    `bank_name` VARCHAR(100) DEFAULT NULL,
    `bank_account_number` VARCHAR(50) DEFAULT NULL,
    
    -- Document Submission
    `documents_submitted` TINYINT(1) DEFAULT 0,
    `documents_status` ENUM('Not Submitted', 'Pending Review', 'Approved', 'Rejected') DEFAULT 'Not Submitted',
    `government_id_path` VARCHAR(255) DEFAULT NULL,
    `diploma_tor_path` VARCHAR(255) DEFAULT NULL,
    `nbi_clearance_path` VARCHAR(255) DEFAULT NULL,
    `medical_certificate_path` VARCHAR(255) DEFAULT NULL,
    
    -- Orientation Schedule
    `orientation_completed` TINYINT(1) DEFAULT 0,
    `orientation_day1_date` DATE DEFAULT NULL,
    `orientation_day1_status` ENUM('Pending', 'Completed', 'Missed') DEFAULT 'Pending',
    `orientation_day2_date` DATE DEFAULT NULL,
    `orientation_day2_status` ENUM('Pending', 'Completed', 'Missed') DEFAULT 'Pending',
    `orientation_day3_date` DATE DEFAULT NULL,
    `orientation_day3_status` ENUM('Pending', 'Completed', 'Missed') DEFAULT 'Pending',
    
    -- Overall Status
    `onboarding_status` ENUM('Not Started', 'In Progress', 'Completed') DEFAULT 'Not Started',
    `onboarding_start_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `onboarding_completion_date` DATETIME DEFAULT NULL,
    
    -- Tracking
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employee_id`) ON DELETE CASCADE,
    FOREIGN KEY (`applicant_id`) REFERENCES `applicantss`(`apply_id`) ON DELETE CASCADE,
    
    -- Indexes
    INDEX `idx_employee_id` (`employee_id`),
    INDEX `idx_onboarding_status` (`onboarding_status`),
    INDEX `idx_orientation_dates` (`orientation_day1_date`, `orientation_day2_date`, `orientation_day3_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Update employees table (if needed)
-- Run this only if your employees table doesn't have these columns

ALTER TABLE `employees` 
MODIFY `employment_status` ENUM('New Hire', 'Active', 'Inactive', 'Terminated') DEFAULT 'New Hire';

-- Add missing columns if they don't exist
ALTER TABLE `employees` 
ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add unique constraint on username if not exists
ALTER TABLE `employees` 
ADD UNIQUE INDEX IF NOT EXISTS `idx_username` (`username`);

-- Add review status columns (run if table already exists)
ALTER TABLE `employee_onboarding`
ADD COLUMN IF NOT EXISTS `personal_info_status` ENUM('Not Submitted', 'Pending Review', 'Approved', 'Rejected') DEFAULT 'Not Submitted' AFTER `personal_info_completed`,
ADD COLUMN IF NOT EXISTS `documents_status` ENUM('Not Submitted', 'Pending Review', 'Approved', 'Rejected') DEFAULT 'Not Submitted' AFTER `documents_submitted`;
