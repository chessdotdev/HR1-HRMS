<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { include './includes/verify_admin.php'; }
if (!class_exists('RBAC')) require_once '../modules/RBAC.php';
$_role = $_SESSION['admin_role'] ?? 'recruiter';
?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR System</title>
    <!-- <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
   
</head>

<body>

<div class="wrapper">
    <aside id="sidebar">
        <!-- Sidebar Header -->
        <div class="d-flex align-items-center mb-3" style="position: relative;">
           <div class="sidebar-logo">
                <a href="dashboard.php">TechnoVista</a>
            </div>
            <button class="toggle-btn btn btn-light me-2" type="button">
                <i class="bi bi-list" id="toggle-icon"></i>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <ul class="sidebar-nav list-unstyled">

            <!-- Dashboard -->
            <li class="sidebar-item">
                <a href="dashboard.php" class="sidebar-link">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Recruitment Dropdown -->
            <li class="sidebar-item">
                <a href="#recruitMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-people"></i>
                    <span>Recruitment</span>
                </a>
                <ul id="recruitMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="job_openings.php" class="sidebar-link">Job Postings</a></li>
                    <li class="sidebar-item"><a href="applicants.php" class="sidebar-link">Applicants</a></li>
                    <li class="sidebar-item"><a href="interviews.php" class="sidebar-link">Interviews</a></li>
                    <li class="sidebar-item"><a href="applicant_status.php" class="sidebar-link">Hiring Status</a></li>
                </ul>
            </li>

            <!-- Onboarding Dropdown -->
            <li class="sidebar-item">
                <a href="#onboardingMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-person-check"></i>
                    <span>Onboarding</span>
                </a>
                <ul id="onboardingMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="new_hires.php" class="sidebar-link">New Hires</a></li>
                    <li class="sidebar-item"><a href="onboarding_tasks.php" class="sidebar-link">Tasks</a></li>
                    <li class="sidebar-item"><a href="orientation_schedule.php" class="sidebar-link">Orientation Schedule</a></li>
                </ul>
            </li>

            <!-- Employees Dropdown -->
            <?php if (RBAC::canAccess($_role, 'employee_list')): ?>
            <li class="sidebar-item">
                <a href="#employeesMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-person-badge"></i>
                    <span>Employees</span>
                </a>
                <ul id="employeesMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="employee_list.php" class="sidebar-link">Employee List</a></li>
                    <li class="sidebar-item"><a href="departments.php" class="sidebar-link">Departments</a></li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- Performance Dropdown -->
            <?php if (RBAC::canAccess($_role, 'evaluation_forms')): ?>
            <li class="sidebar-item">
                <a href="#performanceMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-graph-up"></i>
                    <span>Performance</span>
                </a>
                <ul id="performanceMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="evaluation_forms.php" class="sidebar-link">Evaluation Forms</a></li>
                    <li class="sidebar-item"><a href="evaluation_results.php" class="sidebar-link">Evaluation Results</a></li>
                </ul>
            </li>
            <?php endif; ?>

            <!-- Recognition Dropdown -->
            <?php if (RBAC::canAccess($_role, 'points_rewards')): ?>
            <li class="sidebar-item">
                <a href="#recognitionMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-trophy"></i>
                    <span>Recognition</span>
                </a>
                <ul id="recognitionMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="points_rewards.php" class="sidebar-link">Points & Rewards</a></li>
                    <li class="sidebar-item"><a href="leaderboard.php" class="sidebar-link">Leaderboard</a></li>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (RBAC::canAccess($_role, 'roles')): ?>
            <!-- Roles & Access -->
            <li class="sidebar-item">
                <a href="roles.php" class="sidebar-link">
                    <i class="bi bi-shield-lock"></i>
                    <span>Roles & Access</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Settings -->
            <li class="sidebar-item">
                <a href="#settingsMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                <ul id="settingsMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="account_settings.php" class="sidebar-link">Account Settings</a></li>
                    <?php if (RBAC::canAccess($_role, 'system_settings')): ?>
                    <li class="sidebar-item"><a href="system_settings.php" class="sidebar-link">System Settings</a></li>
                    <?php endif; ?>
                    <?php if (RBAC::canAccess($_role, 'audit_logs')): ?>
                    <li class="sidebar-item"><a href="audit_logs.php" class="sidebar-link">Audit Logs</a></li>
                    <?php endif; ?>
                </ul>
            </li>

        </ul>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer mt-auto">
            <div class="px-2 pb-2" style="font-size:0.72rem;color:#a1a1aa;">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?>
                <span class="badge bg-<?= RBAC::getRoleBadgeColor($_role) ?> ms-1" style="font-size:0.65rem;">
                    <?= RBAC::getRoleLabel($_role) ?>
                </span>
            </div>
            <a href="../logout.php" class="sidebar-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
