    -- Update admin role column to enforce 3 roles
    ALTER TABLE `admin` MODIFY `role` ENUM('super_admin','hr_manager','recruiter') NOT NULL DEFAULT 'recruiter';

    -- Update any existing 'admin' role to 'super_admin'
    UPDATE `admin` SET `role` = 'super_admin' WHERE `role` = 'admin' OR `role` = 'super_admin';
