<?php
session_start();
require_once '../modules/RBAC.php';
require_once '../modules/AuditLog.php';
require_once '../auth/Admin.php';
require_once 'includes/verify_admin.php';

$db   = new Database();
$conn = $db->connect();
$audit = new AuditLog();
$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = $_POST['role'] ?? '';

        if (!$username || !$email || !$password || !$role) {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } elseif (!array_key_exists($role, RBAC::roles())) {
            $error = 'Invalid role selected.';
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO admin (username, email, password, role) VALUES (:u, :e, :p, :r)");
                $stmt->execute([':u' => $username, ':e' => $email, ':p' => password_hash($password, PASSWORD_DEFAULT), ':r' => $role]);
                $audit->log($admin_id, $admin_name, 'Create Admin', 'Roles', "Created admin account '{$username}' with role {$role}");
                $success = "Account for \"{$username}\" created.";
            } catch (PDOException $e) {
                $error = $e->getCode() == 23000 ? 'Username or email already exists.' : 'Failed to create account.';
            }
        }

    } elseif ($action === 'update_role') {
        $target_id = (int)($_POST['target_id'] ?? 0);
        $new_role  = $_POST['role'] ?? '';

        if ($target_id === (int)$_SESSION['admin_id']) {
            $error = 'You cannot change your own role.';
        } elseif (!array_key_exists($new_role, RBAC::roles())) {
            $error = 'Invalid role.';
        } else {
            $conn->prepare("UPDATE admin SET role=:r WHERE id=:id")->execute([':r' => $new_role, ':id' => $target_id]);
            $audit->log($admin_id, $admin_name, 'Update Role', 'Roles', "Changed role of admin ID {$target_id} to {$new_role}");
            $success = 'Role updated.';
        }

    } elseif ($action === 'delete') {
        $target_id = (int)($_POST['target_id'] ?? 0);
        if ($target_id === (int)$_SESSION['admin_id']) {
            $error = 'You cannot delete your own account.';
        } else {
            $targetRow = $conn->prepare("SELECT username FROM admin WHERE id=:id");
            $targetRow->execute([':id' => $target_id]);
            $targetUsername = $targetRow->fetchColumn() ?: "ID {$target_id}";
            $conn->prepare("DELETE FROM admin WHERE id=:id")->execute([':id' => $target_id]);
            $audit->log($admin_id, $admin_name, 'Delete Admin', 'Roles', "Deleted admin account '{$targetUsername}'");
            $success = 'Account removed.';
        }
    }
}

$admins = $conn->query("SELECT id, username, email, role FROM admin ORDER BY FIELD(role,'super_admin','hr_manager','recruiter'), username")->fetchAll(PDO::FETCH_ASSOC);
$roles  = RBAC::roles();
?>
<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Roles & Access</h2>
            <p class="text-muted mb-0">Manage admin accounts and their roles</p>
        </div>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-person-plus me-1"></i> Add Admin
        </button>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <!-- Role Legend -->
    <!-- <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <span class="badge bg-dark mb-2">Super Admin</span>
                <p class="text-muted small mb-0">Full access to all modules including Roles & Access and Settings.</p>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <span class="badge bg-primary mb-2">HR Manager</span>
                <p class="text-muted small mb-0">All modules except Roles & Access and Settings.</p>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <span class="badge bg-secondary mb-2">Recruiter</span>
                <p class="text-muted small mb-0">Recruitment and Onboarding modules only.</p>
            </div></div>
        </div>
    </div> -->

    <!-- Admin Accounts Table -->
    <div class="card">
        <div class="card-header bg-white"><i class="bi bi-people me-2"></i>Admin Accounts (<?= count($admins) ?>)</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Change Role</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $a): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($a['username']) ?></strong>
                            <?php if ($a['id'] == $_SESSION['admin_id']): ?>
                                <span class="badge bg-light text-dark border ms-1" style="font-size:0.65rem;">You</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?= htmlspecialchars($a['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= RBAC::getRoleBadgeColor($a['role']) ?>">
                                <?= RBAC::getRoleLabel($a['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($a['id'] != $_SESSION['admin_id']): ?>
                            <form method="POST" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="action" value="update_role">
                                <input type="hidden" name="target_id" value="<?= $a['id'] ?>">
                                <select name="role" class="form-select form-select-sm" style="width:auto;">
                                    <?php foreach ($roles as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= $a['role'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-dark">Save</button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($a['id'] != $_SESSION['admin_id']): ?>
                            <form method="POST" onsubmit="return confirm('Delete this account?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="target_id" value="<?= $a['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Admin Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add Admin Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="">Select role...</option>
                            <?php foreach ($roles as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark"><i class="bi bi-person-plus me-1"></i>Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
