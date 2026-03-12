<?php
session_start();
require_once '../auth/Applicants_account.php';

if (isset($_SESSION['applicant_username'])) {
    header("Location: index.php");
    exit();
}

$applicant_acc = new Applicants_account();
$messageErr = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $messageErr = "All fields are required";
    }

    if (empty($messageErr)) {
        try {
            $user = $applicant_acc->loginApplicant($username, $password);
            if ($user) {
                $_SESSION['applicant_id']       = $user['id'];
                $_SESSION['applicant_username'] = $user['username'];
                $_SESSION['applicant_role']     = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                $messageErr = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $messageErr = 'Something went wrong. Please try again.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — HR Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        min-height: 100vh;
        background: #ffffff;
        font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        color: #09090b;
    }

    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        padding: 0.875rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e4e4e7;
        background: #fff;
        z-index: 100;
    }
    .navbar a{
        font-size:0.8rem;
        color:black;
        text-decoration:none;
        font-family:inherit;
        font-weight: 600;
        margin-right: 10px;
    }

    .login-card {
        width: 100%;
        max-width: 360px;
        border: 1px solid #e4e4e7;
        border-radius: 12px;
        padding: 2rem 1.75rem;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03);
    }

    .login-header {
        text-align: center;
        margin-bottom: 1.75rem;
    }

    .login-logo {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: #09090b;
        border-radius: 8px;
        margin-bottom: 1.25rem;
        font-size: 1.1rem;
    }

    .login-title {
        font-size: 1.35rem;
        font-weight: 600;
        color: #09090b;
        margin-bottom: 0.3rem;
        letter-spacing: -0.02em;
    }

    .login-subtitle {
        font-size: 0.82rem;
        color: #71717a;
        font-weight: 400;
    }

    .form-card {
        border: 1px solid #e4e4e7;
        border-radius: 10px;
        padding: 1.5rem;
        background: #ffffff;
    }

    .alert-error {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 0.65rem 0.85rem;
        border-radius: 6px;
        font-size: 0.8rem;
        margin-bottom: 1.25rem;
    }

    .alert-error::before {
        content: '⚠';
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .field-group {
        margin-bottom: 1rem;
    }

    .field-label {
        display: block;
        font-size: 0.82rem;
        font-weight: 500;
        color: #09090b;
        margin-bottom: 0.4rem;
    }

    .field-input {
        width: 100%;
        height: 36px;
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 6px;
        padding: 0 0.75rem;
        color: #09090b;
        font-family: inherit;
        font-size: 0.875rem;
        transition: border-color 0.15s, box-shadow 0.15s;
        outline: none;
    }

    .field-input::placeholder {
        color: #a1a1aa;
    }

    .field-input:focus {
        border-color: #09090b;
        box-shadow: 0 0 0 3px rgba(9, 9, 11, 0.06);
    }

    .btn-login {
        width: 100%;
        height: 36px;
        background: #09090b;
        border: none;
        border-radius: 6px;
        color: #fafafa;
        font-family: inherit;
        font-size: 0.82rem;
        font-weight: 500;
        letter-spacing: 0.01em;
        cursor: pointer;
        margin-top: 0.25rem;
        transition: background 0.15s, opacity 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    .btn-login:hover {
        background: #27272a;
    }

    .btn-login:active {
        opacity: 0.85;
    }

    .divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1.25rem 0;
        color: #a1a1aa;
        font-size: 0.75rem;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e4e4e7;
    }

    .register-link {
        margin-top: 1.25rem;
        text-align: center;
        font-size: 0.8rem;
        color: #71717a;
    }

    .register-link a {
        color: #09090b;
        font-weight: 500;
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-color: #d4d4d8;
        transition: text-decoration-color 0.15s;
    }

    .register-link a:hover {
        text-decoration-color: #09090b;
    }

    .footer-note {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.72rem;
        color: #a1a1aa;
    }
</style>

<body>
    <nav class="navbar">
        <span style="font-size:0.875rem; font-weight:600; color:#09090b;">
            Hotel &amp; Restaurant
        </span>
        <a class="navbar-link" href="index.php">
            View open positions →
        </a>
    </nav>


    <div class="login-card">
        <div class="login-header">
            <img src="./assets/image/hrms-logo-transparent.png" class="login-logo">
            <h1 class="login-title">Welcome</h1>
            <p class="login-subtitle">Sign in to your applicant account</p>
        </div>

        <div class="form-card">
            <?php if ($messageErr): ?>
                <div class="alert-error"><?= htmlspecialchars($messageErr) ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="field-group">
                    <label class="field-label" for="username">Username</label>
                    <input type="text" class="field-input" id="username" name="username"
                        placeholder="johndoe"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>

                <div class="field-group">
                    <label class="field-label" for="password">Password</label>
                    <input type="password" class="field-input" id="password" name="password"
                        placeholder="••••••••">
                </div>

                <button type="submit" class="btn-login">
                    Sign in →
                </button>
            </form>
        </div>

        <div class="register-link">
            Don't have an account? <a href="register.php">Create one</a>
        </div>

        <div class="footer-note">
            Hotel &amp; Restaurant 
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>