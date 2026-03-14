<?php
session_start();
include './includes/verify_admin.php';

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
                <a href="dashboard.php">Admin</a>
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

            <!-- Performance Dropdown -->
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

            <!-- Recognition Dropdown -->
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

            <!-- RBAC Module -->
            <li class="sidebar-item">
                <a href="#rbacMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-shield-lock"></i>
                    <span>RBAC</span>
                </a>
                <ul id="rbacMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="roles.php" class="sidebar-link">Roles</a></li>
                    <li class="sidebar-item"><a href="permissions.php" class="sidebar-link">Permissions</a></li>
                    <li class="sidebar-item"><a href="user_roles.php" class="sidebar-link">User Roles</a></li>
                </ul>
            </li>

            <!-- Notifications -->
            <li class="sidebar-item">
                <a href="notifications.php" class="sidebar-link">
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="sidebar-item">
                <a href="#settingsMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                <ul id="settingsMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="account_settings.php" class="sidebar-link">Account Settings</a></li>
                    <li class="sidebar-item"><a href="system_settings.php" class="sidebar-link">System Settings</a></li>
                </ul>
            </li>

        </ul>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer mt-auto">
            <a href="../logout.php" class="sidebar-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
