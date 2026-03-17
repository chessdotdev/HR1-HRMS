<?php
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../modules/Employee.php';
$employeeObj = new Employee();
$employee = $employeeObj->getEmployeeById($_SESSION['employee_id']);
$onboarding = $employeeObj->getOnboardingData($_SESSION['employee_id']);
$progress = $employeeObj->getOnboardingProgress($_SESSION['employee_id']);

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fafafa;
            color: #09090b;
        }

        .wrapper { display: flex; min-height: 100vh; }

        #sidebar {
            width: 260px;
            background: #ffffff;
            border-right: 1px solid #e4e4e7;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
        }

        #sidebar.collapsed { width: 70px; }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #e4e4e7;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #09090b;
            white-space: nowrap;
        }

        .logo-icon { font-size: 1.4rem; }

        #sidebar.collapsed .logo-text { display: none; }

        .toggle-btn {
            background: transparent;
            border: none;
            color: #71717a;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.25rem;
            transition: color 0.15s;
            flex-shrink: 0;
        }

        .toggle-btn:hover { color: #09090b; }

        .sidebar-user {
            padding: 1rem;
            border-bottom: 1px solid #e4e4e7;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: #09090b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .user-info { overflow: hidden; }

        .user-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #09090b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-status {
            font-size: 0.72rem;
            color: #71717a;
        }

        #sidebar.collapsed .user-info { display: none; }

        .sidebar-progress {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e4e4e7;
        }

        .sidebar-progress-label {
            font-size: 0.72rem;
            color: #71717a;
            margin-bottom: 0.4rem;
            display: flex;
            justify-content: space-between;
        }

        .progress {
            height: 5px;
            border-radius: 4px;
            background: #f4f4f5;
        }

        .progress-bar { background: #09090b; border-radius: 4px; }

        #sidebar.collapsed .sidebar-progress { display: none; }

        .sidebar-nav {
            flex-grow: 1;
            padding: 0.75rem 0.5rem;
            list-style: none;
        }

        .sidebar-section-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.5rem 0.75rem 0.25rem;
        }

        #sidebar.collapsed .sidebar-section-label { display: none; }

        .sidebar-item { margin-bottom: 0.15rem; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            color: #71717a;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .sidebar-link i { font-size: 1rem; min-width: 20px; display: flex; justify-content: center; }

        .sidebar-link:hover { background: #f4f4f5; color: #09090b; }

        .sidebar-link.active { background: #f4f4f5; color: #09090b; }

        #sidebar.collapsed .sidebar-link span { display: none; }

        .sidebar-link .check {
            margin-left: auto;
            font-size: 0.75rem;
            color: #16a34a;
        }

        #sidebar.collapsed .sidebar-link .check { display: none; }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #e4e4e7;
        }

        .sidebar-footer .sidebar-link { color: #71717a; }

        .sidebar-footer .sidebar-link:hover { color: #dc2626; background: #fef2f2; }

        .main {
            flex-grow: 1;
            background: #fafafa;
            padding: 2rem;
            overflow-y: auto;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #09090b;
            margin-bottom: 0.25rem;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            margin-bottom: 1.25rem;
        }

        .card-header {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid #e4e4e7;
            background: #fafafa;
            font-weight: 600;
            font-size: 0.875rem;
            color: #09090b;
            border-radius: 8px 8px 0 0;
        }

        .card-body { padding: 1.25rem; }

        .btn {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .btn-primary { background: #09090b; border: 1px solid #09090b; color: #fafafa; }
        .btn-primary:hover { background: #27272a; border-color: #27272a; color: #fff; }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }

        .form-label { font-size: 0.8rem; font-weight: 500; color: #09090b; margin-bottom: 0.4rem; }

        .form-control, .form-select {
            font-size: 0.875rem;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #09090b;
            box-shadow: 0 0 0 3px rgba(9,9,11,0.06);
        }

        .form-control:disabled, .form-control[readonly] {
            background: #fafafa;
            color: #52525b;
        }

        .badge { font-size: 0.72rem; font-weight: 500; padding: 0.3rem 0.6rem; border-radius: 4px; }

        .table { font-size: 0.875rem; }
        .table th { font-weight: 600; font-size: 0.8rem; color: #09090b; }
        .table td { color: #52525b; vertical-align: middle; }

        .alert { font-size: 0.875rem; border-radius: 6px; }

        .info-row {
            display: flex;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f4f4f5;
            font-size: 0.875rem;
        }

        .info-row:last-child { border-bottom: none; }

        .info-label { color: #71717a; width: 45%; font-size: 0.8rem; }
        .info-value { color: #09090b; font-weight: 500; }
    </style>
</head>

<body>
<div class="wrapper">
    <aside id="sidebar">

        <div class="sidebar-header">
            <div class="sidebar-logo">
                <span class="logo-text">Employee Portal</span>
            </div>
            <button class="toggle-btn" id="toggleBtn" type="button">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">
                <?= strtoupper(substr($employee['firstname'], 0, 1) . substr($employee['lastname'], 0, 1)) ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']) ?></div>
                <div class="user-status">
                    <span class="badge bg-<?= $employee['employment_status'] === 'Active' ? 'success' : 'warning text-dark' ?>" style="font-size: 0.65rem;">
                        <?= $employee['employment_status'] ?>
                    </span>
                </div>
            </div>
        </div>

        <?php if ($employee['employment_status'] === 'New Hire'): ?>
        <div class="sidebar-progress">
            <div class="sidebar-progress-label">
                <span>Onboarding</span>
                <span><?= $progress ?>%</span>
            </div>
            <div class="progress">
                <div class="progress-bar" style="width: <?= $progress ?>%"></div>
            </div>
        </div>
        <?php endif; ?>

        <ul class="sidebar-nav">

            <?php if ($employee['employment_status'] === 'New Hire'): ?>
                <li><div class="sidebar-section-label">Onboarding</div></li>

                <li class="sidebar-item">
                    <a href="onboarding.php" class="sidebar-link <?= $current_page === 'onboarding.php' ? 'active' : '' ?>">
                        <i class="bi bi-grid"></i>
                        <span>Overview</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="personal_info.php" class="sidebar-link <?= $current_page === 'personal_info.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Personal Information</span>
                        <?php if ($onboarding && $onboarding['personal_info_completed']): ?>
                            <i class="bi bi-check-circle-fill check"></i>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="documents.php" class="sidebar-link <?= $current_page === 'documents.php' ? 'active' : '' ?>">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        <span>Documents</span>
                        <?php if ($onboarding && $onboarding['documents_submitted']): ?>
                            <i class="bi bi-check-circle-fill check"></i>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="orientation.php" class="sidebar-link <?= $current_page === 'orientation.php' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-event"></i>
                        <span>Orientation</span>
                        <?php if ($onboarding && $onboarding['orientation_completed']): ?>
                            <i class="bi bi-check-circle-fill check"></i>
                        <?php endif; ?>
                    </a>
                </li>

            <?php else: ?>
                <li><div class="sidebar-section-label">Main</div></li>

                <li class="sidebar-item">
                    <a href="dashboard.php" class="sidebar-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li><div class="sidebar-section-label">My Work</div></li>

                <li class="sidebar-item">
                    <a href="my_performance.php" class="sidebar-link <?= $current_page === 'my_performance.php' ? 'active' : '' ?>">
                        <i class="bi bi-graph-up"></i>
                        <span>Performance</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="recognition.php" class="sidebar-link <?= in_array($current_page, ['recognition.php','recognition_history.php']) ? 'active' : '' ?>">
                        <i class="bi bi-trophy"></i>
                        <span>Recognition</span>
                    </a>
                </li>
                <li class="sidebar-item" style="padding-left:0.75rem;">
                    <a href="recognition.php" class="sidebar-link <?= $current_page === 'recognition.php' ? 'active' : '' ?>" style="font-size:0.82rem;padding:0.4rem 0.75rem;">
                        <i class="bi bi-grid-3x3-gap" style="font-size:0.85rem;"></i>
                        <span>All Recognitions</span>
                    </a>
                </li>
                <li class="sidebar-item" style="padding-left:0.75rem;">
                    <a href="recognition_history.php" class="sidebar-link <?= $current_page === 'recognition_history.php' ? 'active' : '' ?>" style="font-size:0.82rem;padding:0.4rem 0.75rem;">
                        <i class="bi bi-clock-history" style="font-size:0.85rem;"></i>
                        <span>My History</span>
                    </a>
                </li>
            <?php endif; ?>

            <li><div class="sidebar-section-label">Account</div></li>

            <li class="sidebar-item">
                <a href="profile.php" class="sidebar-link <?= $current_page === 'profile.php' ? 'active' : '' ?>">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a href="settings.php" class="sidebar-link <?= $current_page === 'settings.php' ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>

        </ul>

        <div class="sidebar-footer">
            <a href="../logout.php" class="sidebar-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>

    </aside>

    <div class="main">
