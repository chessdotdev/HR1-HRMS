<?php
session_start();
require_once '../config/Database.php';

$db   = new Database();
$conn = $db->connect();

$employee_id = $_SESSION['employee_id'] ?? null;
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current  = $_POST['current_password'] ?? '';
    $new      = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $row = $conn->prepare("SELECT password FROM employees WHERE employee_id = :id");
    $row->execute([':id' => $employee_id]);
    $hash = $row->fetchColumn();

    if (!password_verify($current, $hash)) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $conn->prepare("UPDATE employees SET password = :p WHERE employee_id = :id")
             ->execute([':p' => password_hash($new, PASSWORD_DEFAULT), ':id' => $employee_id]);
        $success = 'Password changed successfully.';
    }
}

include 'includes/header.php';
?>

<div class="mb-3">
    <h2 class="mb-0">Settings</h2>
    <p class="text-muted mb-0">Manage your account security</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show py-2">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-lock me-2"></i>Change Password</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
