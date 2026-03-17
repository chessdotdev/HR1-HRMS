<?php

class RBAC {

    // Role labels for display
    public static function roles(): array {
        return [
            'super_admin' => 'Super Admin',
            'hr_manager'  => 'HR Manager',
            'recruiter'   => 'Recruiter',
        ];
    }

    // Pages each role can access (filename without .php)
    private static function permissions(): array {
        $recruitment = ['dashboard','job_openings','applicants','view_applicant','applicant_status','interviews','schedule_interview','update_interview_result','update_status','hiring_status'];
        $onboarding  = ['new_hires','view_new_hire','onboarding_tasks','orientation_schedule'];
        $employees   = ['employee_list','view_employee','departments'];
        $performance = ['evaluation_forms','evaluation_results'];
        $recognition = ['points_rewards','leaderboard'];
        $settings    = ['roles','account_settings','system_settings','audit_logs'];

        return [
            'super_admin' => array_merge($recruitment, $onboarding, $employees, $performance, $recognition, $settings),
            'hr_manager'  => array_merge($recruitment, $onboarding, $employees, $performance, $recognition, ['account_settings']),
            'recruiter'   => array_merge($recruitment, $onboarding, ['account_settings']),
        ];
    }

    public static function canAccess(string $role, string $page): bool {
        $map = self::permissions();
        return in_array($page, $map[$role] ?? []);
    }

    public static function getRoleLabel(string $role): string {
        return self::roles()[$role] ?? $role;
    }

    public static function getRoleBadgeColor(string $role): string {
        return match($role) {
            'super_admin' => 'dark',
            'hr_manager'  => 'primary',
            'recruiter'   => 'secondary',
            default       => 'secondary'
        };
    }
}
?>
