<?php
session_start();
require_once '../modules/Employee.php';

if (isset($_SESSION['employee_id'])) {
    header("Location: dashboard.php");
    exit();
}

$employee = new Employee();
$messageErr = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $messageErr = "All fields are required";
    }

    if (empty($messageErr)) {
        try {
            $user = $employee->loginEmployee($username, $password);
            if ($user) {
                $_SESSION['employee_id'] = $user['employee_id'];
                $_SESSION['employee_username'] = $user['username'];
                $_SESSION['employment_status'] = $user['employment_status'];
                $_SESSION['onboarding_status'] = $user['onboarding_status'];
                
                // Redirect based on employment status
                if ($user['employment_status'] === 'New Hire') {
                    header("Location: onboarding.php");
                } else {
                    header("Location: dashboard.php");
                }
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
    <title>Employee Login — HR Portal</title>
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

    .footer-note {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.72rem;
        color: #a1a1aa;
    }
</style>

<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">👤</div>
            <h1 class="login-title">Employee Portal</h1>
            <p class="login-subtitle">Sign in to your employee account</p>
        </div>

        <div class="form-card">
            <?php if ($messageErr): ?>
                <div class="alert-error"><?= htmlspecialchars($messageErr) ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="field-group">
                    <label class="field-label" for="username">Username</label>
                    <input type="text" class="field-input" id="username" name="username"
                        placeholder="Enter your username"
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

        <div class="footer-note">
            Hotel &amp; Restaurant Employee Portal
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
