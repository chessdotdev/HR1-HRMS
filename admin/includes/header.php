<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR System</title>
    <link href="../public/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
     <!-- icons -->
    <link href="https://cdn.lineicons.com/5.1/line/lineicons.css" rel="stylesheet"/>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <i><img src="./assets/images/web.png" alt="" width="25px"></i>
            </button>
            
        </div>

        <!-- Sidebar Navigation -->
        <ul class="sidebar-nav list-unstyled">

            <!-- Profile -->
            <li class="sidebar-item">
                <a href="dashboard.php" class="sidebar-link">
                    <i><img src="./assets/images/dashboard.png" alt="" width="25px"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- recruit Dropdown -->
            <li class="sidebar-item">
                <a href="#recruitMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <i><!-- svg icon --></i>
                    <span>Recruitment</span>
                </a>
                <ul id="recruitMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">
                    <li class="sidebar-item"><a href="job_openings.php" class="sidebar-link">Job Posting</a></li>
                    <li class="sidebar-item"><a href="applicants.php" class="sidebar-link">Applicants</a></li>
                    <li class="sidebar-item"><a href="#" class="sidebar-link">Interviews</a></li>
                    <li class="sidebar-item"><a href="#" class="sidebar-link">Hiring Status</a></li>
                </ul>
            </li>

            <!-- Multi Level Dropdown -->
            <li class="sidebar-item">
                <a href="#multiMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                    <span>Multi Level</span>
                    <i><!-- svg icon --></i>
                </a>
                <ul id="multiMenu" class="collapse list-unstyled" data-bs-parent="#sidebar">

                    <!-- Links Nested -->
                    <li class="sidebar-item">
                        <a href="#multiTwoMenu" class="sidebar-link has-dropdown collapsed" data-bs-toggle="collapse" aria-expanded="false">
                            Two Links
                        </a>
                        <ul id="multiTwoMenu" class="collapse list-unstyled" data-bs-parent="#multiMenu">
                            <li class="sidebar-item"><a href="#" class="sidebar-link">Link1</a></li>
                            <li class="sidebar-item"><a href="#" class="sidebar-link">Link2</a></li>
                            <li class="sidebar-item"><a href="#" class="sidebar-link">Link3</a></li>
                        </ul>
                    </li>

                </ul>
            </li>

            <!-- Notifications -->
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i><!-- svg icon --></i>
                    <span>Notif</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="sidebar-item">
                <a href="#" class="sidebar-link">
                    <i><!-- svg icon --></i>
                    <span>Settings</span>
                </a>
            </li>

        </ul>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer mt-auto">
            <a href="#" class="sidebar-link">
                <i><!-- svg icon --></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
