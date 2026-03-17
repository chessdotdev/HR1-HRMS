-- ============================================================
-- PERFORMANCE MANAGEMENT (Probation Period)
-- ============================================================

-- One probation record per employee (created when hired)
CREATE TABLE IF NOT EXISTS probation_reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL UNIQUE,
    probation_start DATE NOT NULL,
    probation_end DATE NOT NULL,
    status ENUM('Ongoing','Passed','Failed','Extended') DEFAULT 'Ongoing',
    final_remarks TEXT,
    reviewed_by INT COMMENT 'admin_id',
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Goals set for the employee during probation
CREATE TABLE IF NOT EXISTS probation_goals (
    goal_id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    goal_title VARCHAR(255) NOT NULL,
    description TEXT,
    target_date DATE,
    status ENUM('Pending','In Progress','Achieved','Not Achieved') DEFAULT 'Pending',
    FOREIGN KEY (review_id) REFERENCES probation_reviews(review_id) ON DELETE CASCADE
);

-- Periodic feedback entries (can be added multiple times)
CREATE TABLE IF NOT EXISTS probation_feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    feedback_date DATE NOT NULL,
    strengths TEXT,
    improvements TEXT,
    given_by INT COMMENT 'admin_id',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES probation_reviews(review_id) ON DELETE CASCADE
);

-- Final rating per category
CREATE TABLE IF NOT EXISTS probation_ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    category VARCHAR(100) NOT NULL COMMENT 'e.g. Work Quality, Attendance, Teamwork',
    score TINYINT NOT NULL COMMENT '1-5 scale',
    FOREIGN KEY (review_id) REFERENCES probation_reviews(review_id) ON DELETE CASCADE
);

-- ============================================================
-- SOCIAL RECOGNITION
-- ============================================================

-- Recognition posts (admin or peer nominations)
CREATE TABLE IF NOT EXISTS recognition_posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    nominee_employee_id INT NOT NULL,
    nominated_by_employee_id INT NULL COMMENT 'NULL = admin awarded',
    nominated_by_admin_id INT NULL,
    award_type ENUM('Employee of the Month','Peer Appreciation','Above & Beyond','Best Attendance','Team Player','Innovation Award') NOT NULL,
    message TEXT NOT NULL,
    is_public TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reactions/likes on recognition posts
CREATE TABLE IF NOT EXISTS recognition_reactions (
    reaction_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    employee_id INT NOT NULL,
    UNIQUE KEY unique_reaction (post_id, employee_id),
    FOREIGN KEY (post_id) REFERENCES recognition_posts(post_id) ON DELETE CASCADE
);
