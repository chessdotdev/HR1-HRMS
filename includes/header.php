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
    <link rel="stylesheet" href="../public/assets/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
   html{
        scroll-behavior: smooth;
    }
    .hero-banner {
        position: relative;
        background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
        url('./assets/image/job-opening.jpg') center center/cover no-repeat; 
        min-height: 450px;
        background-attachment: scroll;
        background-size: 100%;

}
#jobs{
    scroll-margin-top: 80px; 
}
.job-card {
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.job-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
}

.hover-shadow:hover {
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.transition-all {
    transition: all 0.3s ease;
}

.badge {
    font-weight: 500;
}

.btn-primary {
    background: #0d6efd;
    border: none;
}

.btn-primary:hover {
    background: #0b5ed7;
}

.apply-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    background-color: #111827; /* dark neutral */
    color: #ffffff;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.apply-btn:hover {
    background-color: #1f2937;
}

.apply-btn:active {
    transform: scale(0.98);
}

.apply-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
}

.apply-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="../public/index.php">Hotel & Restaurant</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                   <li class="nav-item"><a class="nav-link" href="index.php">Jobs</a></li>
                <?php if(isset($_SESSION['applicant_username'])): ?>
                    <li class="nav-item"><a class="nav-link" href="#">Hello, <?php echo htmlspecialchars($_SESSION['applicant_username']); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                <?php else: ?>
                
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

