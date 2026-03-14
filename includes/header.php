<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel & Restaurant</title>
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
    nav .nav-link {
    color: #09090b !important;
    font-size: 0.875rem;
    font-weight: 500;
    }

    nav .nav-link:hover {
        color: #52525b !important;
    }

    nav .navbar-brand {
        color: #09090b !important;
        font-weight: 600;
    }
    .apply-btn{
        background-color: black;
        border-color: #0d6efd;
        color: white;
        padding: 10px 14px;
        border-radius: 8px;
        border: none;
    }
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="../public/index.php">Hotel & Restaurant</a>
        <button class="navbar-toggler" type="button" 
            data-bs-toggle="collapse" 
            data-bs-target="#navbarNav"
            aria-controls="navbarNav" 
            aria-expanded="false" 
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
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
