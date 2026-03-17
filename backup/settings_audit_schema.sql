-- System Settings (key-value store)
CREATE TABLE IF NOT EXISTS system_settings (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default values
INSERT IGNORE INTO system_settings (`key`, `value`) VALUES
('company_name',        'Luxor Grand Hotel'),
('company_email',       'hr@luxorgrand.com'),
('company_address',     '123 Grand Avenue, Makati City, Metro Manila'),
('probation_days',      '90'),
('email_signature',     'Best regards,\nHR Team\nLuxor Grand Hotel');

-- Audit Logs
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id      INT AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT NOT NULL,
    admin_name  VARCHAR(100) NOT NULL,
    action      VARCHAR(100) NOT NULL,
    module      VARCHAR(100) NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
