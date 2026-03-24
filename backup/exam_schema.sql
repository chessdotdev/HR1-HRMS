-- ============================================================
-- INTERVIEW EXAM SYSTEM
-- Run this in your MySQL database
-- ============================================================

-- Add interviewer_id to interviews table
ALTER TABLE `interviews`
ADD COLUMN IF NOT EXISTS `interviewer_id` INT DEFAULT NULL COMMENT 'admin_id of who scheduled the interview',
ADD COLUMN IF NOT EXISTS `interviewer_name` VARCHAR(255) DEFAULT NULL;

-- Question bank (per job title or general)
CREATE TABLE IF NOT EXISTS `exam_questions` (
    `question_id`    INT AUTO_INCREMENT PRIMARY KEY,
    `job_title`      VARCHAR(255) DEFAULT NULL COMMENT 'NULL = applies to all jobs',
    `question_text`  TEXT NOT NULL,
    `question_type`  ENUM('multiple_choice','text') DEFAULT 'multiple_choice',
    `option_a`       VARCHAR(255) DEFAULT NULL,
    `option_b`       VARCHAR(255) DEFAULT NULL,
    `option_c`       VARCHAR(255) DEFAULT NULL,
    `option_d`       VARCHAR(255) DEFAULT NULL,
    `correct_answer` ENUM('a','b','c','d') DEFAULT NULL,
    `points`         INT DEFAULT 1,
    `created_by`     INT COMMENT 'admin_id',
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- One exam session per interview
CREATE TABLE IF NOT EXISTS `exam_sessions` (
    `session_id`   INT AUTO_INCREMENT PRIMARY KEY,
    `interview_id` INT NOT NULL UNIQUE,
    `applicant_id` INT NOT NULL,
    `started_at`   TIMESTAMP NULL,
    `submitted_at` TIMESTAMP NULL,
    `total_score`  INT DEFAULT 0,
    `total_points` INT DEFAULT 0,
    `status`       ENUM('Not Started','In Progress','Submitted') DEFAULT 'Not Started',
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`interview_id`) REFERENCES `interviews`(`interview_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Applicant answers
CREATE TABLE IF NOT EXISTS `exam_answers` (
    `answer_id`     INT AUTO_INCREMENT PRIMARY KEY,
    `session_id`    INT NOT NULL,
    `question_id`   INT NOT NULL,
    `answer_text`   TEXT COMMENT 'for text questions',
    `answer_choice` ENUM('a','b','c','d') DEFAULT NULL,
    `is_correct`    TINYINT(1) DEFAULT NULL,
    `points_earned` INT DEFAULT 0,
    FOREIGN KEY (`session_id`)  REFERENCES `exam_sessions`(`session_id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `exam_questions`(`question_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
