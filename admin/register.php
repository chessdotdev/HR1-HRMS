<?php
session_start();
header("Location: login.php");
exit();
?>
$messageErr = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (empty(trim($username)) || empty(trim($email)) || empty(trim($password))) {
        $messageErr = "All fields are required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageErr = "Invalid email format.";
    }

    if (empty($messageErr)) {
        try {
            $create = $applicants_account->createAdmin($username, $email, $password);

            if ($create) {
                $success = true;
            } else {
                $messageErr = "Failed to create account. Please try again.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $messageErr = "This email or username is already registered.";
            } else {
                $messageErr = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register — HR Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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
    .register-card {
        width: 100%;
        max-width: 360px;
        border: 1px solid #e4e4e7;
        border-radius: 12px;
        padding: 2rem 1.75rem;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03);
    }

    .register-header {
        text-align: center;
        margin-bottom: 1.75rem;
    }

    .register-logo {
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

    .register-title {
        font-size: 1.35rem;
        font-weight: 600;
        color: #09090b;
        margin-bottom: 0.3rem;
        letter-spacing: -0.02em;
    }

    .register-subtitle {
        font-size: 0.82rem;
        color: #71717a;
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

    .alert-error::before { content: '⚠'; font-size: 0.85rem; flex-shrink: 0; }

    .alert-success {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
        padding: 0.65rem 0.85rem;
        border-radius: 6px;
        font-size: 0.8rem;
        margin-bottom: 1.25rem;
    }

    .alert-success::before { content: '✓'; font-size: 0.85rem; flex-shrink: 0; }

    .field-group { margin-bottom: 1rem; }

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

    .field-input::placeholder { color: #a1a1aa; }

    .field-input:focus {
        border-color: #09090b;
        box-shadow: 0 0 0 3px rgba(9,9,11,0.06);
    }

    .btn-register {
        width: 100%;
        height: 36px;
        background: #09090b;
        border: none;
        border-radius: 6px;
        color: #fafafa;
        font-family: inherit;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        margin-top: 0.25rem;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-register:hover { background: #27272a; }
    .btn-register:active { opacity: 0.85; }

    .login-link {
        margin-top: 1.25rem;
        text-align: center;
        font-size: 0.8rem;
        color: #71717a;
    }

    .login-link a {
        color: #09090b;
        font-weight: 500;
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-color: #d4d4d8;
        transition: text-decoration-color 0.15s;
    }

    .login-link a:hover { text-decoration-color: #09090b; }

    .footer-note {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.72rem;
        color: #a1a1aa;
    }
</style>
<body>

<div class="register-card">
    <div class="register-header">
        <div class="register-logo">🏨</div>
        <h1 class="register-title">Create admin account</h1>
        <p class="register-subtitle">Register to manage HR system</p>
    </div>

    <?php if ($success): ?>
        <div class="alert-success">Account created! <a href="login.php" style="color:#16a34a; font-weight:600;">Sign in now →</a></div>
    <?php endif; ?>

    <?php if ($messageErr): ?>
        <div class="alert-error"><?= htmlspecialchars($messageErr) ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" autocomplete="off">
        <div class="field-group">
            <label class="field-label" for="username">Username</label>
            <input type="text" class="field-input" id="username" name="username"
                placeholder="admin"
                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>

        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <input type="email" class="field-input" id="email" name="email"
                placeholder="admin@example.com"
                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>

        <div class="field-group">
            <label class="field-label" for="password">Password</label>
            <input type="password" class="field-input" id="password" name="password"
                placeholder="••••••••">
        </div>

        <button type="submit" class="btn-register">Create account →</button>
    </form>
    <?php endif; ?>

    <div class="login-link">
        Already have an account? <a href="login.php">Sign in</a>
    </div>

    <div class="footer-note">
        Hotel &amp; Restaurant HR System
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>