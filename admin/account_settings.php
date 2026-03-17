<?php
session_start();
require_once '../modules/AuditLog.php';
require_once '../config/Database.php';
require_once 'includes/verify_admin.php';

$db      = new Database();
$conn    = $db->connect();
$audit   = new AuditLog();

$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');

        if (!$username || !$email) {
            $error = 'Username and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } else {
            try {
                $conn->prepare("UPDATE admin SET username=:u, email=:e WHERE id=:id")
                     ->execute([':u' => $username, ':e' => $email, ':id' => $admin_id]);
                $_SESSION['admin_username'] = $username;
                $audit->log($admin_id, $admin_name, 'Update Profile', 'Settings', "Changed username/email");
                $success = 'Profile updated.';
            } catch (PDOException $e) {
                $error = $e->getCode() == 23000 ? 'Username or email already taken.' : 'Update failed.';
            }
        }

    } elseif ($action === 'change_password') {
        $current  = $_POST['current_password'] ?? '';
        $new      = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $row = $conn->prepare("SELECT password FROM admin WHERE id=:id");
        $row->execute([':id' => $admin_id]);
        $hash = $row->fetchColumn();

        if (!password_verify($current, $hash)) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($new) < 6) {
            $error = 'New password must be at least 6 characters.';
        } elseif ($new !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $conn->prepare("UPDATE admin SET password=:p WHERE id=:id")
                 ->execute([':p' => password_hash($new, PASSWORD_DEFAULT), ':id' => $admin_id]);
            $audit->log($admin_id, $admin_name, 'Change Password', 'Settings', 'Password changed');
            $success = 'Password changed successfully.';
        }
    }
}

$admin = $conn->prepare("SELECT username, email, role FROM admin WHERE id=:id");
$admin->execute([':id' => $admin_id]);
$admin = $admin->fetch(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="mb-3">
        <h2 class="mb-0">Account Settings</h2>
        <p class="text-muted mb-0">Manage your admin account</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Profile Info -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><i class="bi bi-person me-2"></i>Profile Information</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars(ucwords(str_replace('_', ' ', $admin['role']))) ?>" disabled>
                        </div>
                        <button type="submit" class="btn btn-dark">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white"><i class="bi bi-lock me-2"></i>Change Password</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
