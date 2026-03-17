<?php
session_start();
require_once '../modules/AuditLog.php';
require_once '../config/Database.php';
require_once 'includes/verify_admin.php';

$db    = new Database();
$conn  = $db->connect();
$audit = new AuditLog();

$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['company_name', 'company_email', 'company_address', 'probation_days', 'email_signature'];
    $stmt   = $conn->prepare("INSERT INTO system_settings (`key`, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `value`=:v");
    foreach ($fields as $field) {
        $val = trim($_POST[$field] ?? '');
        $stmt->execute([':k' => $field, ':v' => $val]);
    }
    $audit->log($admin_id, $admin_name, 'Update Settings', 'Settings', 'System settings updated');
    $success = 'Settings saved.';
}

$settings = [];
foreach ($conn->query("SELECT `key`, `value` FROM system_settings") as $row) {
    $settings[$row['key']] = $row['value'];
}
?>
<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="mb-3">
        <h2 class="mb-0">System Settings</h2>
        <p class="text-muted mb-0">Global configuration for the HR system</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-8">
            <form method="POST">
                <!-- Company Info -->
                <div class="card mb-4">
                    <div class="card-header bg-white"><i class="bi bi-building me-2"></i>Company Information</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Company HR Email</label>
                            <input type="email" name="company_email" class="form-control" value="<?= htmlspecialchars($settings['company_email'] ?? '') ?>" required>
                            <small class="text-muted">Used as sender in all system emails.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Company Address</label>
                            <input type="text" name="company_address" class="form-control" value="<?= htmlspecialchars($settings['company_address'] ?? '') ?>" placeholder="e.g. 123 Grand Avenue, Makati City">
                            <small class="text-muted">Shown in interview and hiring notification emails.</small>
                        </div>
                    </div>
                </div>

                <!-- Config -->
                <div class="card mb-4">
                    <div class="card-header bg-white"><i class="bi bi-gear me-2"></i>HR Configuration</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Default Probation Period (days)</label>
                            <input type="number" name="probation_days" class="form-control" min="1" max="365" value="<?= htmlspecialchars($settings['probation_days'] ?? '90') ?>" required>
                            <small class="text-muted">Used when creating a new probation review.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Signature</label>
                            <textarea name="email_signature" class="form-control" rows="4"><?= htmlspecialchars($settings['email_signature'] ?? '') ?></textarea>
                            <small class="text-muted">Appended to all outgoing HR emails.</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark px-4">Save Settings</button>
            </form>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white"><i class="bi bi-info-circle me-2"></i>About Settings</div>
                <div class="card-body">
                    <p class="text-muted small mb-2">These settings apply system-wide and affect emails, probation reviews, and branding.</p>
                    <hr>
                    <small class="text-muted d-block mb-1"><strong>Company Name</strong> — shown in emails and reports.</small>
                    <small class="text-muted d-block mb-1"><strong>HR Email</strong> — sender address for all notifications.</small>
                    <small class="text-muted d-block mb-1"><strong>Company Address</strong> — shown in interview and hiring emails.</small>
                    <small class="text-muted d-block mb-1"><strong>Probation Days</strong> — default duration when creating reviews.</small>
                    <small class="text-muted d-block"><strong>Email Signature</strong> — footer text in all outgoing emails.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
