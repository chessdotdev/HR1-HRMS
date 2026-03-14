-- Departments Table Schema
-- Run this SQL to create the departments table

CREATE TABLE IF NOT EXISTS `departments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `idx_name` (`name`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample departments for Hotel & Restaurant
INSERT INTO `departments` (`name`, `description`) VALUES
('Front Office', 'Guest services, check-in/check-out, reservations'),
('Kitchen', 'Food preparation, cooking, menu planning'),
('Food & Beverage', 'Restaurant service, bar, room service'),
('Housekeeping', 'Room cleaning, laundry, maintenance of cleanliness'),
('Maintenance', 'Facility repairs, equipment maintenance'),
('Sales & Marketing', 'Promotions, client relations, marketing campaigns'),
('Human Resources', 'Recruitment, employee relations, training'),
('Accounting', 'Finance, payroll, budgeting');
